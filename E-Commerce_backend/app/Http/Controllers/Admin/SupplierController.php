<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:suppliers.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:suppliers.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:suppliers.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:suppliers.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $query = Supplier::withCount('purchaseOrders')->withSum('purchaseOrders', 'grand_total');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $status = filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($status !== null) {
                $query->where('status', $status);
            }
        }

        $suppliers = $query->latest()->paginate(10)->withQueryString();

        return view('admin.suppliers.index', [
            'suppliers' => $suppliers,
            'activeCount' => Supplier::where('status', true)->count(),
            'totalOrders' => PurchaseOrder::count(),
            'totalSpend' => PurchaseOrder::where('status', '!=', PurchaseOrder::STATUS_CANCELLED)->sum('grand_total'),
        ]);
    }

    public function create(): View
    {
        return view('admin.suppliers.create', ['supplier' => new Supplier()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateSupplier($request);

        $supplier = Supplier::create($data);

        return redirect()->route('admin.suppliers.show', $supplier)
            ->with('status', "<strong>{$supplier->name}</strong> has been added successfully.");
    }

    public function show(Supplier $supplier): View
    {
        $supplier->load([
            'purchaseOrders.items',
            'purchaseOrders.creator',
            'purchaseReturns.items.product',
        ]);

        $orders = $supplier->purchaseOrders()
            ->with('items')
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'totalOrders' => $supplier->purchaseOrders()->count(),
            'totalAmount' => $supplier->purchaseOrders()
                ->where('status', '!=', PurchaseOrder::STATUS_CANCELLED)
                ->sum('grand_total'),
            'pendingPayments' => $supplier->purchaseOrders()
                ->whereIn('status', [
                    PurchaseOrder::STATUS_APPROVED,
                    PurchaseOrder::STATUS_ORDERED,
                    PurchaseOrder::STATUS_PARTIALLY_RECEIVED,
                    PurchaseOrder::STATUS_RECEIVED,
                ])
                ->where('payment_status', '!=', PurchaseOrder::PAYMENT_PAID)
                ->sum('grand_total'),
        ];

        $returnedProducts = $supplier->purchaseReturns()
            ->where('status', PurchaseReturn::STATUS_COMPLETED)
            ->with('items.product')
            ->get()
            ->flatMap(fn ($return) => $return->items)
            ->groupBy('product_id')
            ->map(function ($items) {
                $first = $items->first();

                return [
                    'product' => $first->product,
                    'quantity' => $items->sum('quantity'),
                    'amount' => $items->sum('total'),
                ];
            })
            ->values();

        return view('admin.suppliers.show', [
            'supplier' => $supplier,
            'orders' => $orders,
            'stats' => $stats,
            'returnedProducts' => $returnedProducts,
        ]);
    }

    public function edit(Supplier $supplier): View
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $data = $this->validateSupplier($request);

        $supplier->update($data);

        return redirect()->route('admin.suppliers.show', $supplier)
            ->with('status', "<strong>{$supplier->name}</strong> has been updated successfully.");
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->purchaseOrders()->exists()) {
            return back()->withErrors(['supplier' => 'Cannot delete a supplier that has purchase orders. You can deactivate it instead.']);
        }

        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('status', "<strong>{$supplier->name}</strong> has been deleted.");
    }

    private function validateSupplier(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'company' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['status'] = $request->boolean('status');

        return $validated;
    }
}
