<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected ?string $botToken;
    protected ?string $chatId;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
    }

    public function sendMessage(string $message): bool
    {
        if (empty($this->botToken) || empty($this->chatId)) {
            Log::warning('Telegram credentials not configured.');
            return false;
        }

        $response = Http::timeout(5)->post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
            'chat_id' => $this->chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);

        if ($response->failed()) {
            Log::error('Telegram notification failed: ' . $response->body());
            return false;
        }

        return true;
    }

    public function sendOrderNotification(\App\Models\Order $order): bool
    {
        $items = $order->items->map(fn ($item) =>
            "• {$item->product->name} x{$item->quantity} — \$" . number_format($item->price * $item->quantity, 2)
        )->implode("\n");

        $message = "<b>🛒 New Order #{$order->id}</b>\n"
            . "─────────────────────\n"
            . "<b>Customer:</b> {$order->user->name}\n"
            . "<b>Email:</b> {$order->user->email}\n"
            . "<b>Phone:</b> {$order->phone}\n"
            . "<b>Address:</b> {$order->shipping_address}\n"
            . "─────────────────────\n"
            . "<b>Items:</b>\n{$items}\n"
            . "─────────────────────\n"
            . "<b>Total:</b> \$" . number_format($order->total_amount, 2);

        return $this->sendMessage($message);
    }

    public function sendSupplierNotification(\App\Models\Supplier $supplier, ?string $createdBy = null): bool
    {
        $status = $supplier->status ? 'Active' : 'Inactive';

        $message = "<b>🏭 New Supplier Created</b>\n"
            . "─────────────────────\n"
            . "<b>Name:</b> {$supplier->name}\n"
            . ($supplier->company ? "<b>Company:</b> {$supplier->company}\n" : '')
            . ($supplier->contact_person ? "<b>Contact Person:</b> {$supplier->contact_person}\n" : '')
            . ($supplier->phone ? "<b>Phone:</b> {$supplier->phone}\n" : '')
            . ($supplier->email ? "<b>Email:</b> {$supplier->email}\n" : '')
            . ($supplier->address ? "<b>Address:</b> {$supplier->address}\n" : '')
            . "<b>Status:</b> {$status}\n"
            . "─────────────────────\n"
            . "<b>Created by:</b> " . ($createdBy ?? 'N/A');

        return $this->sendMessage($message);
    }

    public function sendPurchaseOrderNotification(\App\Models\PurchaseOrder $purchaseOrder, ?string $createdBy = null): bool
    {
        $items = $purchaseOrder->items->map(fn ($item) =>
            "• " . ($item->product->name ?? 'Deleted Product') . " x{$item->quantity} — \$" . number_format((float) $item->total, 2)
        )->implode("\n");

        $message = "<b>📦 New Purchase Order</b>\n"
            . "─────────────────────\n"
            . "<b>PO Number:</b> {$purchaseOrder->po_number}\n"
            . "<b>Supplier:</b> " . ($purchaseOrder->supplier->name ?? 'N/A') . "\n"
            . "<b>Order Date:</b> " . ($purchaseOrder->order_date?->format('Y-m-d') ?? 'N/A') . "\n"
            . "<b>Status:</b> " . ucwords(str_replace('_', ' ', $purchaseOrder->status)) . "\n"
            . "<b>Payment:</b> " . ucfirst($purchaseOrder->payment_status) . "\n"
            . "─────────────────────\n"
            . "<b>Items:</b>\n{$items}\n"
            . "─────────────────────\n"
            . "<b>Total:</b> \$" . number_format((float) $purchaseOrder->grand_total, 2) . "\n"
            . "<b>Created by:</b> " . ($createdBy ?? 'N/A');

        return $this->sendMessage($message);
    }

    public function sendPurchaseReturnNotification(\App\Models\PurchaseReturn $purchaseReturn, ?string $createdBy = null): bool
    {
        $items = $purchaseReturn->items->map(fn ($item) =>
            "• " . ($item->product->name ?? 'Deleted Product') . " x{$item->quantity} — \$" . number_format((float) $item->total, 2)
        )->implode("\n");

        $message = "<b>↩️ New Purchase Return</b>\n"
            . "─────────────────────\n"
            . "<b>Return Number:</b> {$purchaseReturn->return_number}\n"
            . "<b>Purchase Order:</b> " . ($purchaseReturn->purchaseOrder->po_number ?? 'N/A') . "\n"
            . "<b>Supplier:</b> " . ($purchaseReturn->supplier->name ?? 'N/A') . "\n"
            . "<b>Return Date:</b> " . ($purchaseReturn->return_date?->format('Y-m-d') ?? 'N/A') . "\n"
            . "<b>Status:</b> " . ucwords(str_replace('_', ' ', $purchaseReturn->status)) . "\n"
            . ($purchaseReturn->reason ? "<b>Reason:</b> {$purchaseReturn->reason}\n" : '')
            . "─────────────────────\n"
            . "<b>Items:</b>\n{$items}\n"
            . "─────────────────────\n"
            . "<b>Total:</b> \$" . number_format((float) $purchaseReturn->total_amount, 2) . "\n"
            . "<b>Created by:</b> " . ($createdBy ?? 'N/A');

        return $this->sendMessage($message);
    }

    public function sendLowStockAlert(\App\Models\Product $product): bool
    {
        $message = "<b>⚠️ Low Stock Alert</b>\n"
            . "─────────────────────\n"
            . "<b>Product:</b> {$product->name}\n"
            . "<b>Stock:</b> {$product->stock} remaining\n"
            . "<b>Price:</b> \$" . number_format($product->price, 2) . "\n"
            . "<b>Category:</b> " . ($product->category->name ?? 'N/A') . "\n"
            . "─────────────────────\n"
            . "Stock has dropped to {$product->stock} — below the minimum of 3.";

        return $this->sendMessage($message);
    }

    public function sendOutOfStockAlert(\App\Models\Product $product): bool
    {
        $message = "<b>🚫 Out of Stock Alert</b>\n"
            . "─────────────────────\n"
            . "<b>Product:</b> {$product->name}\n"
            . "<b>Price:</b> \$" . number_format($product->price, 2) . "\n"
            . "<b>Category:</b> " . ($product->category->name ?? 'N/A') . "\n"
            . "─────────────────────\n"
            . "This product is now out of stock!";

        return $this->sendMessage($message);
    }
}
