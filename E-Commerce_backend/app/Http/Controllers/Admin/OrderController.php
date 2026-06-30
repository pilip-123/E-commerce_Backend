<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sales.view', ['only' => ['index']]);
        $this->middleware('permission:sales.edit', ['only' => ['edit', 'update', 'destroy']]);
    }

    public function index(Request $request): View
    {
        $query = Order::with(['user', 'items.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $filteredQuery = clone $query;

        return view('admin.orders.index', [
            'orders' => $query->latest()->paginate(10),
            'totalOrders' => Order::count(),
            'pendingCount' => Order::where('status', 'pending')->count(),
            'revenueTotal' => Order::sum('total_amount'),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,processing,shipped,delivered,cancelled'],
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders.index')->with('status', 'Order status updated successfully.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('admin.orders.index')->with('status', 'Order deleted successfully.');
    }
}
