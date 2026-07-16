<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Services\ProductAlertService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:inventory.view');
    }

    public function index(Request $request): View
    {
        $query = Product::with('category');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('stock_status')) {
            match ($request->input('stock_status')) {
                'low' => $query->where('stock', '<=', 3)->where('stock', '>', 0),
                'out' => $query->where('stock', '<=', 0),
                'in' => $query->where('stock', '>', 3),
                default => null,
            };
        }

        if ($request->boolean('low')) {
            $query->where('stock', '<=', 3);
        }

        $products = $query->paginate(10);
        $lowStockCount = Product::where('stock', '<=', 3)->where('stock', '>', 0)->count();
        $outOfStockCount = Product::where('stock', '<=', 0)->count();
        $totalProducts = Product::count();
        $totalValue = Product::select(DB::raw('SUM(stock * COALESCE(unit_cost, price)) as total'))->value('total') ?? 0;

        return view('admin.inventory.index', compact(
            'products', 'lowStockCount', 'outOfStockCount', 'totalProducts', 'totalValue'
        ))->with('categories', \App\Models\Category::orderBy('name')->get());
    }

    public function stockInForm(): View
    {
        $products = Product::orderBy('name')->get();
        return view('admin.inventory.stock-in', compact('products'));
    }

    public function stockIn(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'nullable|numeric|min:0',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $product = Product::findOrFail($data['product_id']);

        DB::transaction(function () use ($product, $data) {
            $stockBefore = $product->stock;
            $product->increment('stock', $data['quantity']);

            if (!empty($data['unit_cost'])) {
                $product->update(['unit_cost' => $data['unit_cost']]);
            }

            InventoryTransaction::create([
                'product_id' => $product->id,
                'type' => 'stock_in',
                'quantity' => $data['quantity'],
                'stock_before' => $stockBefore,
                'stock_after' => $product->fresh()->stock,
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'unit_cost' => $data['unit_cost'] ?? null,
                'user_id' => auth()->id(),
            ]);
        });

        return redirect()->route('admin.inventory.index')
            ->with('status', "<strong>{$product->name}</strong> — <span class=\"text-success\">+{$data['quantity']} units</span> added. New stock: <strong>{$product->fresh()->stock}</strong>.");
    }

    public function stockOutForm(): View
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')->get();
        return view('admin.inventory.stock-out', compact('products'));
    }

    public function stockOut(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $product = Product::findOrFail($data['product_id']);

        if ($product->stock < $data['quantity']) {
            return back()->withErrors(['quantity' => 'Not enough stock. Available: ' . $product->stock]);
        }

        DB::transaction(function () use ($product, $data) {
            $stockBefore = $product->stock;
            $product->decrement('stock', $data['quantity']);

            InventoryTransaction::create([
                'product_id' => $product->id,
                'type' => 'stock_out',
                'quantity' => -$data['quantity'],
                'stock_before' => $stockBefore,
                'stock_after' => $product->fresh()->stock,
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'user_id' => auth()->id(),
            ]);
        });

        app(ProductAlertService::class)->checkLowStock($product->fresh());
        app(ProductAlertService::class)->checkOutOfStock($product->fresh());

        return redirect()->route('admin.inventory.index')
            ->with('status', "<strong>{$product->name}</strong> — <span class=\"text-danger\">-{$data['quantity']} units</span> removed. New stock: <strong>{$product->fresh()->stock}</strong>.");
    }

    public function transferForm(): View
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')->get();
        return view('admin.inventory.transfer', compact('products'));
    }

    public function transfer(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'from_product_id' => 'required|exists:products,id|different:to_product_id',
            'to_product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        $fromProduct = Product::findOrFail($data['from_product_id']);

        if ($fromProduct->stock < $data['quantity']) {
            return back()->withErrors(['quantity' => 'Not enough stock. Available: ' . $fromProduct->stock]);
        }

        $toProduct = Product::findOrFail($data['to_product_id']);

        DB::transaction(function () use ($fromProduct, $toProduct, $data) {
            $fromBefore = $fromProduct->stock;
            $toBefore = $toProduct->stock;

            $fromProduct->decrement('stock', $data['quantity']);
            $toProduct->increment('stock', $data['quantity']);

            InventoryTransaction::create([
                'product_id' => $fromProduct->id,
                'type' => 'transfer_out',
                'quantity' => -$data['quantity'],
                'stock_before' => $fromBefore,
                'stock_after' => $fromProduct->fresh()->stock,
                'reference' => 'Transfer to ' . $toProduct->name,
                'notes' => $data['notes'] ?? null,
                'user_id' => auth()->id(),
            ]);

            InventoryTransaction::create([
                'product_id' => $toProduct->id,
                'type' => 'transfer_in',
                'quantity' => $data['quantity'],
                'stock_before' => $toBefore,
                'stock_after' => $toProduct->fresh()->stock,
                'reference' => 'Transfer from ' . $fromProduct->name,
                'notes' => $data['notes'] ?? null,
                'user_id' => auth()->id(),
            ]);
        });

        app(ProductAlertService::class)->checkLowStock($fromProduct->fresh());
        app(ProductAlertService::class)->checkOutOfStock($fromProduct->fresh());

        return redirect()->route('admin.inventory.index')
            ->with('status', "Transferred <strong>{$data['quantity']} units</strong> from <strong>{$fromProduct->name}</strong> to <strong>{$toProduct->name}</strong>.");
    }

    public function adjustmentForm(): View
    {
        $products = Product::orderBy('name')->get();
        return view('admin.inventory.adjustment', compact('products'));
    }

    public function adjustment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'new_quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:1000',
        ]);

        $product = Product::findOrFail($data['product_id']);
        $oldStock = $product->stock;

        DB::transaction(function () use ($product, $data, $oldStock) {
            $difference = $data['new_quantity'] - $oldStock;

            $product->update(['stock' => $data['new_quantity']]);

            InventoryTransaction::create([
                'product_id' => $product->id,
                'type' => 'adjustment',
                'quantity' => $difference,
                'stock_before' => $oldStock,
                'stock_after' => $data['new_quantity'],
                'notes' => $data['reason'],
                'user_id' => auth()->id(),
            ]);
        });

        app(ProductAlertService::class)->checkLowStock($product->fresh());
        app(ProductAlertService::class)->checkOutOfStock($product->fresh());

        return redirect()->route('admin.inventory.index')
            ->with('status', "<strong>{$product->name}</strong> adjusted from <strong>{$oldStock}</strong> to <strong>{$data['new_quantity']}</strong> units.");
    }

    public function stockCountForm(): View
    {
        $products = Product::with('category')->orderBy('name')->get();
        return view('admin.inventory.stock-count', compact('products'));
    }

    public function stockCount(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'counts' => 'required|array',
            'counts.*.product_id' => 'required|exists:products,id',
            'counts.*.actual_quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();
        $reference = 'SC-' . now()->format('YmdHis');

        DB::transaction(function () use ($data, $user, $reference) {
            foreach ($data['counts'] as $count) {
                $product = Product::find($count['product_id']);
                if (!$product) continue;

                $stockBefore = $product->stock;
                $actualQty = (int) $count['actual_quantity'];
                $difference = $actualQty - $stockBefore;

                if ($difference !== 0) {
                    $product->update(['stock' => $actualQty]);
                }

                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'type' => 'stock_count',
                    'quantity' => $difference,
                    'stock_before' => $stockBefore,
                    'stock_after' => $actualQty,
                    'reference' => $reference,
                    'notes' => $data['notes'] ?? null,
                    'user_id' => $user->id,
                ]);

                app(ProductAlertService::class)->checkLowStock($product->fresh());
                app(ProductAlertService::class)->checkOutOfStock($product->fresh());
            }
        });

        return redirect()->route('admin.inventory.index')
            ->with('status', "Stock count completed. All discrepancies have been corrected. <span class=\"text-muted small\">Ref: {$reference}</span>");
    }

    public function history(Request $request): View
    {
        $query = InventoryTransaction::with('product', 'user')->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(10);
        $products = Product::orderBy('name')->get();

        return view('admin.inventory.history', compact('transactions', 'products'));
    }

    public function clearHistory(): RedirectResponse
    {
        $count = InventoryTransaction::count();
        InventoryTransaction::truncate();

        return redirect()->route('admin.inventory.history')
            ->with('status', "All <strong>{$count} transaction(s)</strong> have been cleared from the history log.");
    }

    public function valuation(): View
    {
        $products = Product::with('category')
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        $totalValue = $products->sum(fn ($p) => $p->stock * (float) ($p->unit_cost ?? $p->price));

        return view('admin.inventory.valuation', compact('products', 'totalValue'));
    }

}
