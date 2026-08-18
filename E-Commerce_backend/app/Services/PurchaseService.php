<?php

namespace App\Services;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PurchaseService
{
    /**
     * Receive quantities for a purchase order.
     *
     * - Only increases inventory by the received quantities (partial receiving supported).
     * - Records a stock movement for every product received.
     * - Transitions the order to "received" or "partially_received".
     *
     * @param  array<int, int>  $quantities  keyed by purchase_order_item id => quantity
     */
    public function receive(PurchaseOrder $purchaseOrder, array $quantities): array
    {
        if (! $purchaseOrder->isReceivable()) {
            throw new RuntimeException('This purchase order cannot be received in its current status.');
        }

        $purchaseOrder->load('items.product');

        $updates = [];
        foreach ($purchaseOrder->items as $item) {
            $qty = (int) ($quantities[$item->id] ?? 0);

            if ($qty < 0) {
                throw new RuntimeException("Received quantity for {$item->product->name} cannot be negative.");
            }

            if ($qty > $item->remaining_to_receive) {
                throw new RuntimeException(
                    "Cannot receive more than the ordered quantity for {$item->product->name}."
                    . " Remaining: {$item->remaining_to_receive}."
                );
            }

            if ($qty > 0) {
                $updates[$item->id] = ['item' => $item, 'qty' => $qty];
            }
        }

        if (empty($updates)) {
            throw new RuntimeException('Enter a quantity to receive for at least one product.');
        }

        DB::transaction(function () use ($purchaseOrder, $updates) {
            foreach ($updates as $data) {
                /** @var PurchaseOrderItem $item */
                $item = $data['item'];
                $qty = $data['qty'];

                /** @var Product $product */
                $product = $item->product;

                $stockBefore = (int) $product->stock;
                $product->increment('stock', $qty);

                $item->update([
                    'received_quantity' => $item->received_quantity + $qty,
                ]);

                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'type' => 'purchase',
                    'quantity' => $qty,
                    'stock_before' => $stockBefore,
                    'stock_after' => $product->fresh()->stock,
                    'reference' => $purchaseOrder->po_number,
                    'notes' => 'Received from ' . ($purchaseOrder->supplier->name ?? 'supplier'),
                    'unit_cost' => $item->unit_cost,
                    'user_id' => auth()->id(),
                ]);

                app(ProductAlertService::class)->checkLowStock($product->fresh());
                app(ProductAlertService::class)->checkOutOfStock($product->fresh());
            }

            $purchaseOrder->refresh();
            $purchaseOrder->load('items');

            $purchaseOrder->update([
                'status' => $purchaseOrder->isFullyReceived()
                    ? PurchaseOrder::STATUS_RECEIVED
                    : PurchaseOrder::STATUS_PARTIALLY_RECEIVED,
            ]);
        });

        $totalReceived = collect($updates)->sum('qty');

        return [
            'total_received' => $totalReceived,
            'status' => $purchaseOrder->fresh()->status,
        ];
    }

    /**
     * Complete an approved purchase return and decrease inventory.
     *
     * @return array{total_returned: int, status: string}
     */
    public function completeReturn(PurchaseReturn $purchaseReturn): array
    {
        if ($purchaseReturn->status !== PurchaseReturn::STATUS_APPROVED) {
            throw new RuntimeException('Only approved purchase returns can be completed.');
        }

        $purchaseReturn->load('items.product', 'items.purchaseOrderItem');

        $totalReturned = 0;

        DB::transaction(function () use ($purchaseReturn, &$totalReturned) {
            foreach ($purchaseReturn->items as $item) {
                /** @var PurchaseReturnItem $item */
                $product = $item->product;
                $qty = (int) $item->quantity;

                if ($qty <= 0) {
                    continue;
                }

                if ((int) $product->stock < $qty) {
                    throw new RuntimeException(
                        "Not enough stock to return {$product->name}. Available: {$product->stock}."
                    );
                }

                $stockBefore = (int) $product->stock;
                $product->decrement('stock', $qty);

                if ($item->purchaseOrderItem) {
                    $item->purchaseOrderItem->increment('returned_quantity', $qty);
                }

                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'type' => 'purchase_return',
                    'quantity' => -$qty,
                    'stock_before' => $stockBefore,
                    'stock_after' => $product->fresh()->stock,
                    'reference' => $purchaseReturn->return_number,
                    'notes' => 'Returned to ' . ($purchaseReturn->supplier->name ?? 'supplier'),
                    'unit_cost' => $item->unit_cost,
                    'user_id' => auth()->id(),
                ]);

                $totalReturned += $qty;

                app(ProductAlertService::class)->checkLowStock($product->fresh());
                app(ProductAlertService::class)->checkOutOfStock($product->fresh());
            }

            $purchaseReturn->update([
                'status' => PurchaseReturn::STATUS_COMPLETED,
                'total_returned' => $totalReturned,
                'credited_at' => now(),
            ]);
        });

        return [
            'total_returned' => $totalReturned,
            'status' => PurchaseReturn::STATUS_COMPLETED,
        ];
    }

    /**
     * Quantity of a PO item that is still available to return.
     * Accounts for pending, approved and completed return items.
     */
    public function availableToReturn(PurchaseOrderItem $item): int
    {
        $reserved = PurchaseReturnItem::where('purchase_order_item_id', $item->id)
            ->whereHas('purchaseReturn', function ($q) {
                $q->whereIn('status', [
                    PurchaseReturn::STATUS_PENDING,
                    PurchaseReturn::STATUS_APPROVED,
                    PurchaseReturn::STATUS_COMPLETED,
                ]);
            })
            ->sum('quantity');

        return max(0, $item->received_quantity - (int) $reserved);
    }

    /**
     * Validate return quantities against what was received (rule: no over-returning).
     *
     * @param  array<int, array{quantity: int}>  $items  keyed by purchase_order_item id
     * @return array<int, PurchaseOrderItem> map of po item id => item (validated)
     */
    public function validateReturnItems(array $items): array
    {
        $validated = [];

        foreach ($items as $poItemId => $data) {
            $poItem = PurchaseOrderItem::with('product')->find((int) $poItemId);

            if (! $poItem) {
                throw new RuntimeException('Invalid purchase order item selected.');
            }

            $qty = (int) ($data['quantity'] ?? 0);

            if ($qty <= 0) {
                continue;
            }

            $available = $this->availableToReturn($poItem);

            if ($qty > $available) {
                throw new RuntimeException(
                    "Cannot return more than received for {$poItem->product->name}."
                    . " Available to return: {$available}."
                );
            }

            $validated[$poItem->id] = $poItem;
        }

        if (empty($validated)) {
            throw new RuntimeException('Add at least one item with a quantity to return.');
        }

        return $validated;
    }
}
