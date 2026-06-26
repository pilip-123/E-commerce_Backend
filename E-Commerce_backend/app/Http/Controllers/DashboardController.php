<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $recentProducts = Product::query()
            ->with('category')
            ->latest()
            ->take(6)
            ->get();

        $recentOrders = $user->isAdmin()
            ? Order::query()->with('user')->latest()->take(5)->get()
            : Order::query()
                ->where('user_id', $user->id)
                ->with('items.product')
                ->latest()
                ->take(5)
                ->get();

        return view('dashboard', [
            'recentProducts' => $recentProducts,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function orders(): View
    {
        $user = auth()->user();

        $orders = Order::query()
            ->where('user_id', $user->id)
            ->with(['items.product'])
            ->latest()
            ->paginate(10);

        return view('orders.index', [
            'orders' => $orders,
        ]);
    }
}

