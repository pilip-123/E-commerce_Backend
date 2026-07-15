<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DiscountCode;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Review;
use App\Models\User;
use App\Services\ExportService;
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
        $products = Product::with('category')->latest()->get();

        $rows = $products->map(fn (Product $p) => [
            $p->id,
            $p->name,
            $p->category->name ?? '',
            '$' . number_format($p->price, 2),
            $p->stock,
            $p->status ? 'Active' : 'Inactive',
            $p->created_at->format('Y-m-d'),
        ]);

        return app(ExportService::class)->export('Products', [
            'ID', 'Name', 'Category', 'Price', 'Stock', 'Status', 'Created',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function categories(Request $request): BinaryFileResponse
    {
        $categories = Category::withCount('products')->latest()->get();

        $rows = $categories->map(fn (Category $c) => [
            $c->id,
            $c->name,
            $c->description ?? '',
            $c->products_count,
            $c->created_at->format('Y-m-d'),
        ]);

        return app(ExportService::class)->export('Categories', [
            'ID', 'Name', 'Description', 'Products', 'Created',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function users(Request $request): BinaryFileResponse
    {
        $users = User::latest()->get();

        $rows = $users->map(fn (User $u) => [
            $u->id,
            $u->name,
            $u->email,
            $u->role,
            $u->phone ?? '',
            $u->created_at->format('Y-m-d'),
        ]);

        return app(ExportService::class)->export('Users', [
            'ID', 'Name', 'Email', 'Role', 'Phone', 'Joined',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function reviews(Request $request): BinaryFileResponse
    {
        $reviews = Review::with(['user', 'product'])->latest()->get();

        $rows = $reviews->map(fn (Review $r) => [
            $r->id,
            $r->user->name ?? 'Deleted User',
            $r->product->name ?? 'Deleted Product',
            $r->rating . '/5',
            $r->comment ?? '',
            $r->created_at->format('Y-m-d'),
        ]);

        return app(ExportService::class)->export('Reviews', [
            'ID', 'User', 'Product', 'Rating', 'Comment', 'Date',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function promotions(Request $request): BinaryFileResponse
    {
        $promotions = Promotion::withCount('products')->latest()->get();

        $rows = $promotions->map(fn (Promotion $p) => [
            $p->id,
            $p->name,
            $p->discount_type === 'percentage'
                ? $p->discount_value . '%'
                : '$' . number_format($p->discount_value, 2),
            $p->start_date->format('Y-m-d'),
            $p->end_date->format('Y-m-d'),
            $p->products_count,
            $p->status ? 'Active' : 'Inactive',
        ]);

        return app(ExportService::class)->export('Promotions', [
            'ID', 'Name', 'Discount', 'Start', 'End', 'Products', 'Status',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function customers(Request $request): BinaryFileResponse
    {
        $users = User::withCount('orders')
            ->withSum('orders', 'total_amount')
            ->latest()
            ->get();

        $rows = $users->map(fn (User $u) => [
            $u->id,
            $u->name,
            $u->email,
            $u->phone ?? '',
            $u->role,
            $u->orders_count,
            '$' . number_format($u->orders_sum_total_amount ?? 0, 2),
            $u->created_at->format('Y-m-d'),
        ]);

        return app(ExportService::class)->export('Customers', [
            'ID', 'Name', 'Email', 'Phone', 'Role', 'Orders', 'Total Spent', 'Joined',
        ], $rows->toArray(), $this->getFormat($request));
    }

    public function inventoryHistory(Request $request): BinaryFileResponse
    {
        $transactions = InventoryTransaction::with('product', 'user')->latest()->get();

        $rows = $transactions->map(fn (InventoryTransaction $t) => [
            $t->created_at->format('Y-m-d H:i'),
            ucfirst(str_replace('_', ' ', $t->type)),
            $t->product->name ?? 'Deleted',
            ($t->quantity > 0 ? '+' : '') . $t->quantity,
            $t->stock_before ?? '',
            $t->stock_after ?? '',
            $t->reference ?? '',
            $t->user->name ?? 'System',
            $t->notes ?? '',
        ]);

        return app(ExportService::class)->export('Inventory_History', [
            'Date', 'Type', 'Product', 'Quantity', 'Stock Before', 'Stock After', 'Reference', 'By', 'Notes',
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
            $d->used_count,
            $d->max_uses,
            $d->sent_count ?? 0,
            $d->created_at->format('Y-m-d'),
        ]);

        return app(ExportService::class)->export('VIP_Codes', [
            'Code', 'Discount', 'Times Used', 'Max Uses', 'Sent To', 'Created',
        ], $rows->toArray(), $this->getFormat($request));
    }
}
