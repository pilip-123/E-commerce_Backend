<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Supplier;
use App\Services\PurchaseService;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class PurchaseReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:purchase_returns.view', ['only' => ['index', 'show', 'create']]);
        $this->middleware('permission:purchase_returns.create', ['only' => ['store']]);
        $this->middleware('permission:purchase_returns.approve', ['only' => ['approve']]);
        $this->middleware('permission:purchase_returns.complete', ['only' => ['complete']]);
        $this->middleware('permission:purchase_returns.cancel', ['only' => ['cancel']]);
        $this->middleware('permission:purchase_returns.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $query = PurchaseReturn::with('supplier', 'purchaseOrder');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('return_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                          ->orWhere('company', 'like', "%{$search}%");
                  })
                  ->orWhereHas('purchaseOrder', function ($pq) use ($search) {
                      $pq->where('po_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->integer('supplier_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('return_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('return_date', '<=', $request->input('date_to'));
        }

        $purchaseReturns = $query->latest()->paginate(10)->withQueryString();

        // ── Return analytics ──
        $returnStats = [
            'total' => PurchaseReturn::count(),
            'pending' => PurchaseReturn::where('status', PurchaseReturn::STATUS_PENDING)->count(),
            'completed' => PurchaseReturn::where('status', PurchaseReturn::STATUS_COMPLETED)->count(),
            'totalAmount' => (float) PurchaseReturn::where('status', '!=', PurchaseReturn::STATUS_CANCELLED)->sum('total_amount'),
            'totalUnits' => (int) PurchaseReturnItem::whereHas('purchaseReturn', fn ($q) => $q->where('status', '!=', PurchaseReturn::STATUS_CANCELLED))->sum('quantity'),
        ];

        $monthlyReturns = PurchaseReturn::selectRaw("DATE_FORMAT(return_date, '%Y-%m') as month, COUNT(*) as count, SUM(total_amount) as total")
            ->where('return_date', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->where('status', '!=', PurchaseReturn::STATUS_CANCELLED)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $returnChartMonths = collect();
        $returnChartTotals = collect();
        for ($i = 5; $i >= 0; $i--) {
            $key = Carbon::now()->subMonths($i)->format('Y-m');
            $returnChartMonths->push(Carbon::now()->subMonths($i)->format('M'));
            $data = $monthlyReturns->get($key);
            $returnChartTotals->push($data ? (float) $data->total : 0);
        }

        $returnsBySupplier = PurchaseReturn::with('supplier')
            ->selectRaw('supplier_id, COUNT(*) as count, SUM(total_amount) as amount')
            ->where('status', '!=', PurchaseReturn::STATUS_CANCELLED)
            ->groupBy('supplier_id')
            ->orderByDesc('amount')
            ->take(5)
            ->get();
        $maxSupplierReturn = max((float) $returnsBySupplier->max('amount'), 1);

        $returnsByProduct = PurchaseReturnItem::with('product')
            ->selectRaw('product_id, SUM(quantity) as quantity, SUM(total) as amount')
            ->whereHas('purchaseReturn', fn ($q) => $q->where('status', '!=', PurchaseReturn::STATUS_CANCELLED))
            ->groupBy('product_id')
            ->orderByDesc('quantity')
            ->take(5)
            ->get();
        $maxProductReturn = max((int) $returnsByProduct->max('quantity'), 1);

        return view('admin.purchase-returns.index', [
            'purchaseReturns' => $purchaseReturns,
            'suppliers' => Supplier::orderBy('name')->get(),
            'returnStats' => $returnStats,
            'returnChartMonths' => $returnChartMonths,
            'returnChartTotals' => $returnChartTotals,
            'returnsBySupplier' => $returnsBySupplier,
            'maxSupplierReturn' => $maxSupplierReturn,
            'returnsByProduct' => $returnsByProduct,
            'maxProductReturn' => $maxProductReturn,
        ]);
    }

    public function create(): View
    {
        $service = app(PurchaseService::class);

        $purchaseOrders = PurchaseOrder::with(['supplier', 'items' => function ($q) {
            $q->with('product')->where('received_quantity', '>', 0);
        }])
            ->whereIn('status', [PurchaseOrder::STATUS_RECEIVED, PurchaseOrder::STATUS_PARTIALLY_RECEIVED])
            ->latest()
            ->get()
            ->filter(fn ($po) => $po->items->isNotEmpty())
            ->map(function ($po) use ($service) {
                $po->items->each(function ($item) use ($service) {
                    $item->available_to_return = $service->availableToReturn($item);
                });

                $po->items = $po->items->filter(fn ($item) => $item->available_to_return > 0)->values();

                return $po;
            })
            ->filter(fn ($po) => $po->items->isNotEmpty())
            ->values();

        $ordersJson = $purchaseOrders->map(fn ($po) => [
            'id' => $po->id,
            'items' => $po->items->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->product->name ?? __('Deleted'),
                'unit_cost' => (float) $item->unit_cost,
                'available' => (int) $item->available_to_return,
            ])->values()->toArray(),
        ])->values()->toArray();

        return view('admin.purchase-returns.create', [
            'purchaseOrders' => $purchaseOrders,
            'ordersJson' => $ordersJson,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'purchase_order_id' => ['required', 'exists:purchase_orders,id'],
            'return_date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.purchase_order_item_id' => ['required', 'exists:purchase_order_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.reason' => ['nullable', 'string', 'max:500'],
        ]);

        $purchaseOrder = PurchaseOrder::with('supplier')->findOrFail($data['purchase_order_id']);

        if (! in_array($purchaseOrder->status, [PurchaseOrder::STATUS_RECEIVED, PurchaseOrder::STATUS_PARTIALLY_RECEIVED])) {
            return back()->with('error', 'Returns can only be created for received purchase orders.');
        }

        $itemsByPoItem = [];
        foreach ($data['items'] as $item) {
            $poItemId = (int) $item['purchase_order_item_id'];
            $itemsByPoItem[$poItemId]['quantity'] = ($itemsByPoItem[$poItemId]['quantity'] ?? 0) + (int) $item['quantity'];
            if (! empty($item['reason'])) {
                $itemsByPoItem[$poItemId]['reason'] = $item['reason'];
            }
        }

        $poItemIds = array_keys($itemsByPoItem);

        if (PurchaseOrderItem::where('purchase_order_id', $purchaseOrder->id)->whereIn('id', $poItemIds)->count() !== count($poItemIds)) {
            return back()->with('error', 'One or more selected items do not belong to this purchase order.')->withInput();
        }

        try {
            $validatedItems = app(PurchaseService::class)->validateReturnItems($itemsByPoItem);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        $total = 0;

        $purchaseReturn = DB::transaction(function () use ($data, $purchaseOrder, $validatedItems, $itemsByPoItem, &$total) {
            $return = PurchaseReturn::create([
                'return_number' => $this->nextNumber(),
                'purchase_order_id' => $purchaseOrder->id,
                'supplier_id' => $purchaseOrder->supplier_id,
                'return_date' => $data['return_date'],
                'reason' => $data['reason'] ?? null,
                'status' => PurchaseReturn::STATUS_PENDING,
                'total_amount' => 0,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($validatedItems as $poItemId => $poItem) {
                $qty = $itemsByPoItem[$poItemId]['quantity'];
                $lineTotal = round((float) $poItem->unit_cost * $qty, 2);
                $total += $lineTotal;

                PurchaseReturnItem::create([
                    'purchase_return_id' => $return->id,
                    'purchase_order_item_id' => $poItemId,
                    'product_id' => $poItem->product_id,
                    'quantity' => $qty,
                    'unit_cost' => $poItem->unit_cost,
                    'reason' => $itemsByPoItem[$poItemId]['reason'] ?? null,
                    'total' => $lineTotal,
                ]);
            }

            $return->update(['total_amount' => $total]);

            return $return;
        });

        try {
            app(TelegramService::class)->sendPurchaseReturnNotification($purchaseReturn, auth()->user()->name);
        } catch (\Throwable $e) {
            // Telegram failure should not block return creation
        }

        return redirect()->route('admin.purchase-returns.show', $purchaseReturn)
            ->with('status', "Purchase return <strong>{$purchaseReturn->return_number}</strong> created (pending).");
    }

    public function show(PurchaseReturn $purchaseReturn): View
    {
        $purchaseReturn->load(['supplier', 'purchaseOrder', 'creator', 'items.product', 'items.purchaseOrderItem']);

        return view('admin.purchase-returns.show', compact('purchaseReturn'));
    }

    public function approve(PurchaseReturn $purchaseReturn): RedirectResponse
    {
        if ($purchaseReturn->status !== PurchaseReturn::STATUS_PENDING) {
            return back()->with('error', 'Only pending purchase returns can be approved.');
        }

        $purchaseReturn->update(['status' => PurchaseReturn::STATUS_APPROVED]);

        return back()->with('status', "Purchase return <strong>{$purchaseReturn->return_number}</strong> has been approved.");
    }

    public function complete(PurchaseReturn $purchaseReturn): RedirectResponse
    {
        try {
            $result = app(PurchaseService::class)->completeReturn($purchaseReturn);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.purchase-returns.show', $purchaseReturn)
            ->with('status', "Purchase return <strong>{$purchaseReturn->return_number}</strong> completed. <strong>{$result['total_returned']}</strong> units returned to supplier and removed from inventory.");
    }

    public function destroy(PurchaseReturn $purchaseReturn): RedirectResponse
    {
        if (! in_array($purchaseReturn->status, [PurchaseReturn::STATUS_PENDING, PurchaseReturn::STATUS_CANCELLED])) {
            return back()->with('error', 'Only pending or cancelled purchase returns can be deleted.');
        }

        $purchaseReturn->delete();

        return redirect()->route('admin.purchase-returns.index')
            ->with('status', "Purchase return <strong>{$purchaseReturn->return_number}</strong> has been deleted.");
    }

    public function cancel(PurchaseReturn $purchaseReturn): RedirectResponse
    {
        if (! in_array($purchaseReturn->status, [PurchaseReturn::STATUS_PENDING, PurchaseReturn::STATUS_APPROVED])) {
            return back()->with('error', 'Only pending or approved purchase returns can be cancelled.');
        }

        $purchaseReturn->update(['status' => PurchaseReturn::STATUS_CANCELLED]);

        return back()->with('status', "Purchase return <strong>{$purchaseReturn->return_number}</strong> has been cancelled.");
    }

    private function nextNumber(): string
    {
        $next = (PurchaseReturn::max('id') ?? 0) + 1;

        return 'PR-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
