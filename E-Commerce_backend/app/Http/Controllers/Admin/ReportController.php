<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $currentMonth = Carbon::now()->startOfMonth();
        $threeMonthsAgo = Carbon::now()->subMonths(3)->startOfDay();
        $now = Carbon::now()->endOfDay();

        $dailyTotalSales = Order::whereDate('created_at', $today)->count();
        $dailyTotalRevenue = (float) Order::whereDate('created_at', $today)->sum('total_amount');

        $monthlyTotalSales = Order::whereBetween('created_at', [
            $currentMonth->copy()->startOfMonth(),
            $currentMonth->copy()->endOfMonth(),
        ])->count();
        $monthlyTotalRevenue = (float) Order::whereBetween('created_at', [
            $currentMonth->copy()->startOfMonth(),
            $currentMonth->copy()->endOfMonth(),
        ])->sum('total_amount');

        $totalRevenue = (float) Order::whereBetween('created_at', [$threeMonthsAgo, $now])->sum('total_amount');
        $totalSales = Order::whereBetween('created_at', [$threeMonthsAgo, $now])->count();

        $topCustomers = Order::whereBetween('orders.created_at', [$threeMonthsAgo, $now])
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->selectRaw('users.id, users.name, users.email, COUNT(*) as order_count, SUM(orders.total_amount) as total_spent')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->take(10)
            ->get()
            ->map(fn ($customer) => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'order_count' => (int) $customer->order_count,
                'total_spent' => (float) $customer->total_spent,
                'average_order' => $customer->order_count > 0
                    ? round((float) $customer->total_spent / (int) $customer->order_count, 2)
                    : 0,
            ]);

        $bestSellers = OrderItem::whereHas('order', function ($q) use ($threeMonthsAgo, $now) {
                $q->whereBetween('created_at', [$threeMonthsAgo, $now]);
            })
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw('products.id, products.name, products.price, products.image, products.stock, categories.name as category_name, SUM(order_items.quantity) as units_sold, SUM(order_items.price * order_items.quantity) as total_revenue')
            ->groupBy('products.id', 'products.name', 'products.price', 'products.image', 'products.stock', 'categories.name')
            ->orderByDesc('units_sold')
            ->take(10)
            ->get()
            ->map(fn ($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'image' => $product->image,
                'stock' => (int) $product->stock,
                'category' => $product->category_name,
                'units_sold' => (int) $product->units_sold,
                'total_revenue' => (float) $product->total_revenue,
            ]);

        return view('admin.reports', compact(
            'today', 'currentMonth', 'threeMonthsAgo', 'now',
            'dailyTotalSales', 'dailyTotalRevenue',
            'monthlyTotalSales', 'monthlyTotalRevenue',
            'totalRevenue', 'totalSales',
            'topCustomers', 'bestSellers',
        ));
    }

    public function dailySales(Request $request): JsonResponse
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();

        $orders = Order::whereDate('created_at', $date)->get();

        $totalSales = $orders->count();
        $totalRevenue = (float) $orders->sum('total_amount');
        $averageOrder = $totalSales > 0 ? round($totalRevenue / $totalSales, 2) : 0;

        $hourly = Order::whereDate('created_at', $date)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $hourlyDistribution = [];
        for ($h = 0; $h < 24; $h++) {
            $entry = $hourly->firstWhere('hour', $h);
            $hourlyDistribution[] = [
                'hour' => sprintf('%02d:00', $h),
                'count' => $entry ? (int) $entry->count : 0,
                'total' => $entry ? (float) $entry->total : 0,
            ];
        }

        $statusBreakdown = [];
        foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status) {
            $count = $orders->where('status', $status)->count();
            $statusRevenue = (float) $orders->where('status', $status)->sum('total_amount');
            if ($count > 0) {
                $statusBreakdown[] = [
                    'status' => ucfirst($status),
                    'count' => $count,
                    'revenue' => $statusRevenue,
                ];
            }
        }

        return response()->json([
            'date' => $date->format('Y-m-d'),
            'total_sales' => $totalSales,
            'total_revenue' => $totalRevenue,
            'average_order' => $averageOrder,
            'hourly_distribution' => $hourlyDistribution,
            'status_breakdown' => $statusBreakdown,
        ]);
    }

    public function monthlySales(Request $request): JsonResponse
    {
        $month = $request->input('month')
            ? Carbon::parse($request->input('month') . '-01')
            : Carbon::now()->startOfMonth();

        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        $orders = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->get();

        $totalSales = $orders->count();
        $totalRevenue = (float) $orders->sum('total_amount');
        $averageOrder = $totalSales > 0 ? round($totalRevenue / $totalSales, 2) : 0;

        $daily = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('DATE(created_at) as day, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $daysInMonth = $endOfMonth->day;
        $dailyDistribution = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateStr = $startOfMonth->copy()->day($d)->format('Y-m-d');
            $entry = $daily->get($dateStr);
            $dailyDistribution[] = [
                'date' => $dateStr,
                'count' => $entry ? (int) $entry->count : 0,
                'total' => $entry ? (float) $entry->total : 0,
            ];
        }

        $statusBreakdown = [];
        foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status) {
            $count = $orders->where('status', $status)->count();
            $statusRevenue = (float) $orders->where('status', $status)->sum('total_amount');
            if ($count > 0) {
                $statusBreakdown[] = [
                    'status' => ucfirst($status),
                    'count' => $count,
                    'revenue' => $statusRevenue,
                ];
            }
        }

        $previousMonth = $startOfMonth->copy()->subMonth();
        $prevStart = $previousMonth->copy()->startOfMonth();
        $prevEnd = $previousMonth->copy()->endOfMonth();
        $previousRevenue = (float) Order::whereBetween('created_at', [$prevStart, $prevEnd])->sum('total_amount');
        $previousSales = Order::whereBetween('created_at', [$prevStart, $prevEnd])->count();

        $revenueGrowth = $previousRevenue > 0
            ? round((($totalRevenue - $previousRevenue) / $previousRevenue) * 100, 2)
            : 0;
        $salesGrowth = $previousSales > 0
            ? round((($totalSales - $previousSales) / $previousSales) * 100, 2)
            : 0;

        return response()->json([
            'month' => $month->format('Y-m'),
            'total_sales' => $totalSales,
            'total_revenue' => $totalRevenue,
            'average_order' => $averageOrder,
            'daily_distribution' => $dailyDistribution,
            'status_breakdown' => $statusBreakdown,
            'previous_month' => $previousMonth->format('Y-m'),
            'revenue_growth' => $revenueGrowth,
            'sales_growth' => $salesGrowth,
        ]);
    }

    public function revenue(Request $request): JsonResponse
    {
        $from = $request->input('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : Carbon::now()->subMonths(3)->startOfDay();
        $to = $request->input('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $orders = Order::whereBetween('created_at', [$from, $to])->get();

        $totalRevenue = (float) $orders->sum('total_amount');
        $totalSales = $orders->count();
        $averageOrder = $totalSales > 0 ? round($totalRevenue / $totalSales, 2) : 0;

        $completedRevenue = (float) $orders->where('status', 'delivered')->sum('total_amount');
        $pendingRevenue = (float) $orders->whereIn('status', ['pending', 'processing', 'shipped'])->sum('total_amount');
        $cancelledRevenue = (float) $orders->where('status', 'cancelled')->sum('total_amount');

        $monthly = Order::whereBetween('created_at', [$from, $to])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count, SUM(total_amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn ($row) => [
                'month' => $row->month,
                'count' => (int) $row->count,
                'total' => (float) $row->total,
            ]);

        $revenueByCategory = OrderItem::whereHas('order', function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to]);
            })
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw('categories.name as category, SUM(order_items.price * order_items.quantity) as total')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'category' => $row->category,
                'total' => (float) $row->total,
            ]);

        return response()->json([
            'period' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'total_revenue' => $totalRevenue,
            'total_sales' => $totalSales,
            'average_order' => $averageOrder,
            'completed_revenue' => $completedRevenue,
            'pending_revenue' => $pendingRevenue,
            'cancelled_revenue' => $cancelledRevenue,
            'monthly_breakdown' => $monthly,
            'revenue_by_category' => $revenueByCategory,
        ]);
    }

    public function topCustomers(Request $request): JsonResponse
    {
        $limit = min((int) $request->input('limit', 10), 50);
        $from = $request->input('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : Carbon::now()->subMonths(3)->startOfDay();
        $to = $request->input('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $topCustomers = Order::whereBetween('orders.created_at', [$from, $to])
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->selectRaw('users.id, users.name, users.email, users.phone, COUNT(*) as order_count, SUM(orders.total_amount) as total_spent')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.phone')
            ->orderByDesc('total_spent')
            ->take($limit)
            ->get()
            ->map(fn ($customer) => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'order_count' => (int) $customer->order_count,
                'total_spent' => (float) $customer->total_spent,
                'average_order' => $customer->order_count > 0
                    ? round((float) $customer->total_spent / (int) $customer->order_count, 2)
                    : 0,
            ]);

        $totalCustomerRevenue = (float) Order::whereBetween('created_at', [$from, $to])->sum('total_amount');

        return response()->json([
            'period' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'total_customer_revenue' => $totalCustomerRevenue,
            'top_customers' => $topCustomers,
        ]);
    }

    public function bestSellers(Request $request): JsonResponse
    {
        $limit = min((int) $request->input('limit', 10), 50);
        $from = $request->input('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : Carbon::now()->subMonths(3)->startOfDay();
        $to = $request->input('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $bestSellers = OrderItem::whereHas('order', function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to]);
            })
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw('products.id, products.name, products.price, products.image, products.stock, categories.name as category_name, SUM(order_items.quantity) as units_sold, SUM(order_items.price * order_items.quantity) as total_revenue')
            ->groupBy('products.id', 'products.name', 'products.price', 'products.image', 'products.stock', 'categories.name')
            ->orderByDesc('units_sold')
            ->take($limit)
            ->get()
            ->map(fn ($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'image' => $product->image,
                'stock' => (int) $product->stock,
                'category' => $product->category_name,
                'units_sold' => (int) $product->units_sold,
                'total_revenue' => (float) $product->total_revenue,
            ]);

        $totalUnitsSold = $bestSellers->sum('units_sold');

        return response()->json([
            'period' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'total_units_sold' => $totalUnitsSold,
            'best_sellers' => $bestSellers,
        ]);
    }
}
