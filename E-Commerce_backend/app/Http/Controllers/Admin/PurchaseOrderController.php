<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class PurchaseOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:purchases.view', ['only' => ['index', 'show', 'receiveForm']]);
        $this->middleware('permission:purchases.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:purchases.update', ['only' => ['edit', 'update', 'updatePayment']]);
        $this->middleware('permission:purchases.approve', ['only' => ['approve', 'markOrdered']]);
        $this->middleware('permission:purchases.receive', ['only' => ['receive']]);
        $this->middleware('permission:purchases.cancel', ['only' => ['cancel']]);
        $this->middleware('permission:purchases.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $query = PurchaseOrder::with(['supplier' => fn ($q) => $q->withSum(['purchaseOrders as active_total' => fn ($sq) => $sq->where('status', '!=', PurchaseOrder::STATUS_CANCELLED)], 'grand_total')]);

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->integer('supplier_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->input('date_to'));
        }

        $sortable = ['po_number', 'order_date', 'grand_total', 'status', 'payment_status', 'created_at'];
        $sort = in_array($request->input('sort'), $sortable, true) ? $request->input('sort') : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $purchaseOrders = $query->withSum('items as received_quantity', 'received_quantity')->withCount('returns')
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        $poStats = [
            'total' => PurchaseOrder::count(),
            'pending' => PurchaseOrder::where('status', PurchaseOrder::STATUS_PENDING)->count(),
            'received' => PurchaseOrder::where('status', PurchaseOrder::STATUS_RECEIVED)->count(),
            'cancelled' => PurchaseOrder::where('status', PurchaseOrder::STATUS_CANCELLED)->count(),
        ];

        return view('admin.purchases.index', [
            'purchaseOrders' => $purchaseOrders,
            'suppliers' => Supplier::orderBy('name')->get(),
            'statuses' => $this->statuses(),
            'poStats' => $poStats,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function create(): View
    {
        return view('admin.purchases.create', [
            'purchaseOrder' => new PurchaseOrder(),
            'suppliers' => Supplier::active()->orderBy('name')->get(),
            'products' => Product::with('category')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePurchaseOrder($request);

        $status = PurchaseOrder::STATUS_DRAFT;
        if (auth()->user()->hasPermission('purchases.approve')
            && in_array($data['status'], [PurchaseOrder::STATUS_DRAFT, PurchaseOrder::STATUS_PENDING, PurchaseOrder::STATUS_APPROVED, PurchaseOrder::STATUS_ORDERED], true)) {
            $status = $data['status'];
        }

        $purchaseOrder = DB::transaction(function () use ($data, $status) {
            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $this->nextNumber(),
                'supplier_id' => $data['supplier_id'],
                'order_date' => $data['order_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'status' => $status,
                'payment_status' => PurchaseOrder::PAYMENT_UNPAID,
                'subtotal' => $data['subtotal'],
                'discount' => $data['discount'],
                'tax' => $data['tax'],
                'grand_total' => $data['grand_total'],
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $this->syncItems($purchaseOrder, $data['items']);

            return $purchaseOrder;
        });

        return redirect()->route('admin.purchases.show', $purchaseOrder)
            ->with('status', "Purchase order <strong>{$purchaseOrder->po_number}</strong> created as draft.");
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load(['supplier', 'creator', 'items.product.category', 'returns']);

        return view('admin.purchases.show', [
            'purchaseOrder' => $purchaseOrder,
        ]);
    }

    public function edit(PurchaseOrder $purchaseOrder): View|RedirectResponse
    {
        if (! $purchaseOrder->isEditable()) {
            return redirect()->route('admin.purchases.show', $purchaseOrder)
                ->with('error', 'Only draft or pending purchase orders can be edited.');
        }

        $purchaseOrder->load('items.product');

        return view('admin.purchases.edit', [
            'purchaseOrder' => $purchaseOrder,
            'suppliers' => Supplier::orderBy('name')->get(),
            'products' => Product::with('category')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        if (! $purchaseOrder->isEditable()) {
            return redirect()->route('admin.purchases.show', $purchaseOrder)
                ->with('error', 'Only draft or pending purchase orders can be edited.');
        }

        $data = $this->validatePurchaseOrder($request);

        DB::transaction(function () use ($purchaseOrder, $data) {
            $purchaseOrder->update([
                'supplier_id' => $data['supplier_id'],
                'order_date' => $data['order_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'subtotal' => $data['subtotal'],
                'discount' => $data['discount'],
                'tax' => $data['tax'],
                'grand_total' => $data['grand_total'],
                'notes' => $data['notes'] ?? null,
            ]);

            $this->syncItems($purchaseOrder, $data['items']);
        });

        return redirect()->route('admin.purchases.show', $purchaseOrder)
            ->with('status', "Purchase order <strong>{$purchaseOrder->po_number}</strong> has been updated.");
    }

    public function approve(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        if ($purchaseOrder->status !== PurchaseOrder::STATUS_PENDING) {
            return back()->with('error', 'Only pending purchase orders can be approved.');
        }

        $purchaseOrder->update(['status' => PurchaseOrder::STATUS_APPROVED]);

        return back()->with('status', "Purchase order <strong>{$purchaseOrder->po_number}</strong> has been approved.");
    }

    public function markOrdered(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        if ($purchaseOrder->status !== PurchaseOrder::STATUS_APPROVED) {
            return back()->with('error', 'Only approved purchase orders can be marked as ordered.');
        }

        $purchaseOrder->update(['status' => PurchaseOrder::STATUS_ORDERED]);

        return back()->with('status', "Purchase order <strong>{$purchaseOrder->po_number}</strong> marked as ordered.");
    }

    public function receiveForm(PurchaseOrder $purchaseOrder): View|RedirectResponse
    {
        if (! $purchaseOrder->isReceivable()) {
            return redirect()->route('admin.purchases.show', $purchaseOrder)
                ->with('error', 'This purchase order cannot be received in its current status.');
        }

        $purchaseOrder->load('supplier', 'items.product');

        return view('admin.purchases.receive', compact('purchaseOrder'));
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $request->validate([
            'quantities' => ['required', 'array'],
            'quantities.*' => ['nullable', 'integer', 'min:0'],
        ]);

        try {
            $result = app(PurchaseService::class)->receive($purchaseOrder, $request->input('quantities', []));
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.purchases.show', $purchaseOrder)
            ->with('status', "Received <strong>{$result['total_received']} units</strong> for <strong>{$purchaseOrder->po_number}</strong>. Status: <span class=\"text-success\">" . ucwords(str_replace('_', ' ', $result['status'])) . '</span>.');
    }

    public function destroy(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $purchaseOrder->loadSum('items as received_quantity', 'received_quantity')->loadCount('returns');

        if ((int) $purchaseOrder->received_quantity > 0) {
            return back()->with('error', 'Cannot delete a purchase order that has received items. Cancel it instead.');
        }

        if ($purchaseOrder->returns_count > 0) {
            return back()->with('error', 'Cannot delete a purchase order that has purchase returns.');
        }

        $purchaseOrder->delete();

        return redirect()->route('admin.purchases.index')
            ->with('status', "Purchase order <strong>{$purchaseOrder->po_number}</strong> has been deleted.");
    }

    public function cancel(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        if (! in_array($purchaseOrder->status, [
            PurchaseOrder::STATUS_DRAFT,
            PurchaseOrder::STATUS_PENDING,
            PurchaseOrder::STATUS_APPROVED,
        ])) {
            return back()->with('error', 'This purchase order cannot be cancelled in its current status.');
        }

        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $purchaseOrder->update([
            'status' => PurchaseOrder::STATUS_CANCELLED,
            'notes' => $data['notes'] ? trim($purchaseOrder->notes . "\nCancelled: " . $data['notes']) : $purchaseOrder->notes,
        ]);

        return redirect()->route('admin.purchases.show', $purchaseOrder)
            ->with('status', "Purchase order <strong>{$purchaseOrder->po_number}</strong> has been cancelled.");
    }

    public function updatePayment(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $data = $request->validate([
            'payment_status' => ['required', 'in:unpaid,partial,paid'],
        ]);

        $purchaseOrder->update(['payment_status' => $data['payment_status']]);

        return back()->with('status', "Payment status updated to <strong>" . ucfirst($data['payment_status']) . '</strong>.');
    }

    /**
     * @return array{items: array<int, array{product_id: int, quantity: int, unit_cost: float, discount: float, tax: float, total: float}>, subtotal: float, discount: float, tax: float, grand_total: float}
     */
    private function validatePurchaseOrder(Request $request): array
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'order_date' => ['required', 'date'],
            'expected_delivery_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:draft,pending,approved,ordered'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax' => ['nullable', 'numeric', 'min:0'],
        ], [
            'items.*.product_id.distinct' => 'Each product can only be added once per purchase order.',
        ]);

        $items = [];
        $subtotal = 0;

        foreach ($validated['items'] as $item) {
            $lineTotal = round(
                ((float) $item['quantity'] * (float) $item['unit_cost'])
                - (float) ($item['discount'] ?? 0)
                + (float) ($item['tax'] ?? 0),
                2
            );

            $items[] = [
                'product_id' => (int) $item['product_id'],
                'quantity' => (int) $item['quantity'],
                'unit_cost' => (float) $item['unit_cost'],
                'discount' => (float) ($item['discount'] ?? 0),
                'tax' => (float) ($item['tax'] ?? 0),
                'total' => $lineTotal,
            ];

            $subtotal += $lineTotal;
        }

        $discount = (float) ($validated['discount'] ?? 0);
        $tax = (float) ($validated['tax'] ?? 0);
        $grandTotal = round($subtotal - $discount + $tax, 2);

        return [
            'supplier_id' => $validated['supplier_id'],
            'order_date' => $validated['order_date'],
            'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
            'status' => $validated['status'] ?? PurchaseOrder::STATUS_DRAFT,
            'notes' => $validated['notes'] ?? null,
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'grand_total' => $grandTotal,
        ];
    }

    private function syncItems(PurchaseOrder $purchaseOrder, array $items): void
    {
        $keepIds = [];

        foreach ($items as $item) {
            $attributes = [
                'purchase_order_id' => $purchaseOrder->id,
                'product_id' => $item['product_id'],
            ];

            $existing = PurchaseOrderItem::where($attributes)->first();

            if ($existing) {
                $existing->update([
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'discount' => $item['discount'],
                    'tax' => $item['tax'],
                    'total' => $item['total'],
                ]);
                $keepIds[] = $existing->id;
            } else {
                $created = $purchaseOrder->items()->create($item);
                $keepIds[] = $created->id;
            }
        }

        $purchaseOrder->items()
            ->whereNotIn('id', $keepIds)
            ->where('received_quantity', 0)
            ->delete();
    }

    private function nextNumber(): string
    {
        $next = (PurchaseOrder::max('id') ?? 0) + 1;

        return 'PO-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    private function statuses(): array
    {
        return [
            PurchaseOrder::STATUS_DRAFT => 'Draft',
            PurchaseOrder::STATUS_PENDING => 'Pending',
            PurchaseOrder::STATUS_APPROVED => 'Approved',
            PurchaseOrder::STATUS_ORDERED => 'Ordered',
            PurchaseOrder::STATUS_PARTIALLY_RECEIVED => 'Partially Received',
            PurchaseOrder::STATUS_RECEIVED => 'Received',
            PurchaseOrder::STATUS_CANCELLED => 'Cancelled',
        ];
    }
}
