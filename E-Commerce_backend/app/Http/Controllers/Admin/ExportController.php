<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DiscountCode;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseReturn;
use App\Models\Review;
use App\Models\Supplier;
use App\Models\User;
use App\Services\ExportService;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    protected function getFormat(Request $request): string
    {
        return in_array($request->query('format'), ['csv', 'html', 'doc', 'pdf']) ? $request->query('format') : 'xlsx';
    }

    public function products(Request $request): BinaryFileResponse
    {
        $query = Product::with('category');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $status = filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($status !== null) {
                $query->where('status', $status);
            }
        }

        $products = $query->latest()->get();

        $rows = $products->map(fn (Product $p) => [
            $p->id,
            $p->name,
            $p->slug,
            $p->category->name ?? 'Uncategorized',
            '$' . number_format($p->price, 2),
            (new \NumberFormatter('en', \NumberFormatter::DECIMAL))->format($p->stock ?? 0),
            $p->status ? 'Active' : 'Inactive',
            $p->created_at->format('M d, Y'),
        ]);

        return app(ExportService::class)->export('Products', [
            'ID', 'Name', 'Slug', 'Category', 'Price', 'Stock', 'Status', 'Created',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function categories(Request $request): BinaryFileResponse
    {
        $query = Category::withCount('products');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->latest()->get();

        $rows = $categories->map(fn (Category $c) => [
            $c->id,
            $c->name,
            $c->slug,
            $c->description ? \Str::limit($c->description, 100) : '',
            (new \NumberFormatter('en', \NumberFormatter::DECIMAL))->format($c->products_count ?? 0),
            $c->created_at->format('M d, Y'),
        ]);

        return app(ExportService::class)->export('Categories', [
            'ID', 'Name', 'Slug', 'Description', 'Products', 'Created',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function users(Request $request): BinaryFileResponse
    {
        $query = User::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $users = $query->latest()->get();

        $rows = $users->map(fn (User $u) => [
            $u->id,
            $u->name,
            $u->email,
            ucfirst($u->role ?? 'customer'),
            $u->phone ?? '',
            $u->address ?? '',
            $u->created_at->format('M d, Y'),
        ]);

        return app(ExportService::class)->export('Users', [
            'ID', 'Name', 'Email', 'Role', 'Phone', 'Address', 'Joined',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function reviews(Request $request): BinaryFileResponse
    {
        $query = Review::with(['user', 'product']);

        if ($request->filled('rating')) {
            $query->where('rating', $request->integer('rating'));
        }

        if ($request->filled('product')) {
            $query->whereHas('product', fn ($q) => $q->where('name', 'like', '%'.$request->input('product').'%'));
        }

        $reviews = $query->latest()->get();

        $rows = $reviews->map(fn (Review $r) => [
            $r->id,
            $r->product->name ?? 'Deleted Product',
            $r->user->name ?? 'Deleted User',
            $r->rating . ' / 5',
            str_repeat('★', $r->rating) . str_repeat('☆', 5 - $r->rating),
            $r->comment ?? '',
            $r->created_at->format('M d, Y'),
        ]);

        return app(ExportService::class)->export('Reviews', [
            'ID', 'Product', 'User', 'Rating', 'Stars', 'Comment', 'Date',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function promotions(Request $request): BinaryFileResponse
    {
        $query = Promotion::withCount('products');

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('type')) {
            $query->where('discount_type', $request->input('type'));
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $now = now();
            if ($request->boolean('status')) {
                $query->where('start_date', '<=', $now)->where('end_date', '>=', $now);
            } else {
                $query->where(function ($q) use ($now) {
                    $q->where('start_date', '>', $now)->orWhere('end_date', '<', $now);
                });
            }
        }

        $promotions = $query->latest()->get();

        $rows = $promotions->map(fn (Promotion $p) => [
            $p->id,
            $p->name,
            $p->discount_type === 'percentage'
                ? $p->discount_value . '%'
                : '$' . number_format($p->discount_value, 2),
            $p->discount_type,
            $p->start_date->format('M d, Y'),
            $p->end_date->format('M d, Y'),
            (new \NumberFormatter('en', \NumberFormatter::DECIMAL))->format($p->products_count ?? 0),
            ($p->start_date <= now() && $p->end_date >= now()) ? 'Active' : 'Inactive',
        ]);

        return app(ExportService::class)->export('Promotions', [
            'ID', 'Name', 'Discount', 'Type', 'Start', 'End', 'Products', 'Status',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function customers(Request $request): BinaryFileResponse
    {
        $query = User::withCount('orders')
            ->withSum('orders', 'total_amount');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $users = $query->latest()->get();

        $rows = $users->map(fn (User $u) => [
            $u->id,
            $u->name,
            $u->email,
            $u->phone ?? '',
            ucfirst($u->role ?? 'customer'),
            (new \NumberFormatter('en', \NumberFormatter::DECIMAL))->format($u->orders_count ?? 0),
            '$' . number_format($u->orders_sum_total_amount ?? 0, 2),
            $u->created_at->format('M d, Y'),
        ]);

        return app(ExportService::class)->export('Customers', [
            'ID', 'Name', 'Email', 'Phone', 'Role', 'Orders', 'Total Spent', 'Joined',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function orders(Request $request): BinaryFileResponse
    {
        $query = Order::with(['user', 'items.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('id', is_numeric($search) ? (int) $search : 0)
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->get();

        $rows = $orders->map(fn (Order $o) => [
            $o->id,
            $o->user->name ?? 'N/A',
            $o->user->email ?? 'N/A',
            $o->phone ?? '',
            (new \NumberFormatter('en', \NumberFormatter::DECIMAL))->format($o->items->count()),
            '$' . number_format($o->total_amount, 2),
            ucfirst($o->status),
            $o->created_at->format('M d, Y'),
        ]);

        return app(ExportService::class)->export('Orders', [
            'Order ID', 'Customer', 'Email', 'Phone', 'Items', 'Total', 'Status', 'Date',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function inventoryHistory(Request $request): BinaryFileResponse
    {
        $query = InventoryTransaction::with('product', 'user');

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

        $transactions = $query->latest()->get();

        $rows = $transactions->map(fn (InventoryTransaction $t) => [
            $t->created_at->format('M d, Y H:i'),
            ucfirst(str_replace('_', ' ', $t->type)),
            $t->product->name ?? 'Deleted',
            ($t->quantity > 0 ? '+' : '') . (new \NumberFormatter('en', \NumberFormatter::DECIMAL))->format($t->quantity),
            $t->stock_before !== null ? (new \NumberFormatter('en', \NumberFormatter::DECIMAL))->format($t->stock_before) : '',
            $t->stock_after !== null ? (new \NumberFormatter('en', \NumberFormatter::DECIMAL))->format($t->stock_after) : '',
            $t->reference ?? '',
            $t->user->name ?? 'System',
            $t->notes ?? '',
        ]);

        return app(ExportService::class)->export('Inventory_History', [
            'Date', 'Type', 'Product', 'Quantity', 'Stock Before', 'Stock After', 'Reference', 'By', 'Notes',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function suppliers(Request $request): BinaryFileResponse
    {
        $query = Supplier::withCount('purchaseOrders');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $status = filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($status !== null) {
                $query->where('status', $status);
            }
        }

        $suppliers = $query->latest()->get();

        $rows = $suppliers->map(fn (Supplier $s) => [
            $s->id,
            $s->name,
            $s->company ?? '',
            $s->contact_person ?? '',
            $s->phone ?? '',
            $s->email ?? '',
            $s->address ?? '',
            $s->status ? 'Active' : 'Inactive',
            (new \NumberFormatter('en', \NumberFormatter::DECIMAL))->format($s->purchase_orders_count ?? 0),
            $s->created_at->format('M d, Y'),
        ]);

        return app(ExportService::class)->export('Suppliers', [
            'ID', 'Name', 'Company', 'Contact Person', 'Phone', 'Email', 'Address', 'Status', 'Orders', 'Created',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function purchaseOrders(Request $request): BinaryFileResponse
    {
        $query = PurchaseOrder::with('supplier', 'items');

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->integer('supplier_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        $orders = $query->latest()->get();

        $rows = $orders->map(fn (PurchaseOrder $po) => [
            $po->po_number,
            $po->supplier->name ?? 'Deleted',
            $po->order_date->format('M d, Y'),
            $po->expected_delivery_date?->format('M d, Y') ?? '',
            (new \NumberFormatter('en', \NumberFormatter::DECIMAL))->format($po->items->sum('quantity')),
            '$' . number_format($po->subtotal, 2),
            '$' . number_format($po->discount, 2),
            '$' . number_format($po->tax, 2),
            '$' . number_format($po->grand_total, 2),
            ucwords(str_replace('_', ' ', $po->status)),
            ucfirst($po->payment_status),
            $po->notes ?? '',
        ]);

        return app(ExportService::class)->export('Purchase_Orders', [
            'PO Number', 'Supplier', 'Order Date', 'Expected Delivery', 'Items', 'Subtotal', 'Discount', 'Tax', 'Grand Total', 'Status', 'Payment', 'Notes',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function purchaseReturns(Request $request): BinaryFileResponse
    {
        $query = PurchaseReturn::with('supplier', 'purchaseOrder', 'items');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->integer('supplier_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('return_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('return_date', '<=', $request->date_to);
        }

        $returns = $query->latest()->get();

        $rows = $returns->map(fn (PurchaseReturn $r) => [
            $r->return_number,
            $r->purchaseOrder->po_number ?? '',
            $r->supplier->name ?? 'Deleted',
            $r->return_date->format('M d, Y'),
            (new \NumberFormatter('en', \NumberFormatter::DECIMAL))->format($r->items->sum('quantity')),
            '$' . number_format($r->total_amount, 2),
            ucfirst($r->status),
            $r->reason ?? '',
            $r->notes ?? '',
        ]);

        return app(ExportService::class)->export('Purchase_Returns', [
            'Return Number', 'Purchase Order', 'Supplier', 'Return Date', 'Items', 'Total Amount', 'Status', 'Reason', 'Notes',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function purchaseReport(Request $request): BinaryFileResponse
    {
        $query = PurchaseOrderItem::with('product', 'purchaseOrder');

        if ($request->filled('purchase_from')) {
            $query->whereHas('purchaseOrder', fn ($q) => $q->whereDate('order_date', '>=', $request->input('purchase_from')));
        }

        if ($request->filled('purchase_to')) {
            $query->whereHas('purchaseOrder', fn ($q) => $q->whereDate('order_date', '<=', $request->input('purchase_to')));
        }

        if ($request->filled('purchase_supplier')) {
            $query->whereHas('purchaseOrder', fn ($q) => $q->where('supplier_id', $request->integer('purchase_supplier')));
        }

        if ($request->filled('purchase_status')) {
            $query->whereHas('purchaseOrder', fn ($q) => $q->where('status', $request->input('purchase_status')));
        }

        $items = $query->latest()->get();

        $rows = $items->map(fn (PurchaseOrderItem $item) => [
            $item->purchaseOrder->po_number ?? '',
            $item->product->name ?? 'Deleted',
            $item->product->sku ?? '',
            (new \NumberFormatter('en', \NumberFormatter::DECIMAL))->format($item->quantity),
            (new \NumberFormatter('en', \NumberFormatter::DECIMAL))->format($item->received_quantity),
            '$' . number_format($item->unit_cost, 2),
            '$' . number_format($item->discount, 2),
            '$' . number_format($item->tax, 2),
            '$' . number_format($item->total, 2),
        ]);

        return app(ExportService::class)->export('Purchase_Report', [
            'PO Number', 'Product', 'SKU', 'Quantity', 'Received', 'Unit Cost', 'Discount', 'Tax', 'Total',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function supplierPurchaseReport(Request $request): BinaryFileResponse
    {
        $query = Supplier::withCount(['purchaseOrders as po_count' => function ($q) {
            $q->where('status', '!=', PurchaseOrder::STATUS_CANCELLED);
        }])
            ->withSum(['purchaseOrders as po_total' => function ($q) {
                $q->where('status', '!=', PurchaseOrder::STATUS_CANCELLED);
            }], 'grand_total');

        if ($request->filled('purchase_from')) {
            $query->whereHas('purchaseOrders', fn ($q) => $q->whereDate('order_date', '>=', $request->input('purchase_from')));
        }

        if ($request->filled('purchase_to')) {
            $query->whereHas('purchaseOrders', fn ($q) => $q->whereDate('order_date', '<=', $request->input('purchase_to')));
        }

        $suppliers = $query->latest()->get();

        $rows = $suppliers->map(fn (Supplier $s) => [
            $s->id,
            $s->name,
            $s->company ?? '',
            $s->phone ?? '',
            $s->email ?? '',
            (new \NumberFormatter('en', \NumberFormatter::DECIMAL))->format($s->po_count ?? 0),
            '$' . number_format($s->po_total ?? 0, 2),
        ]);

        return app(ExportService::class)->export('Supplier_Purchase_Report', [
            'ID', 'Supplier', 'Company', 'Phone', 'Email', 'Orders', 'Total Purchases',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function purchaseSpending(Request $request): BinaryFileResponse
    {
        $monthly = PurchaseOrder::where('status', '!=', PurchaseOrder::STATUS_CANCELLED)
            ->selectRaw("DATE_FORMAT(order_date, '%Y-%m') as month, COUNT(*) as count, SUM(grand_total) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $rows = $monthly->map(fn ($row) => [
            Carbon::parse($row->month . '-01')->format('F Y'),
            (new \NumberFormatter('en', \NumberFormatter::DECIMAL))->format($row->count),
            '$' . number_format($row->total, 2),
        ]);

        return app(ExportService::class)->export('Purchase_Spending', [
            'Month', 'Purchase Orders', 'Total Spent',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function vipCodes(Request $request): BinaryFileResponse
    {
        $codes = DiscountCode::latest()->get();

        $rows = $codes->map(fn (DiscountCode $d) => [
            $d->code,
            $d->discount_type === 'percentage'
                ? $d->discount_value . '%'
                : '$' . number_format($d->discount_value, 2),
            $d->discount_type,
            (new \NumberFormatter('en', \NumberFormatter::DECIMAL))->format($d->used_count ?? 0),
            $d->max_uses ? (new \NumberFormatter('en', \NumberFormatter::DECIMAL))->format($d->max_uses) : 'Unlimited',
            (new \NumberFormatter('en', \NumberFormatter::DECIMAL))->format($d->sent_count ?? 0),
            $d->created_at->format('M d, Y'),
        ]);

        return app(ExportService::class)->export('VIP_Codes', [
            'Code', 'Discount Value', 'Type', 'Times Used', 'Max Uses', 'Sent To', 'Created',
        ], $rows->toArray(), $this->getFormat($request));
    }
}
