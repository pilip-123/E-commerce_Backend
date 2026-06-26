<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'customers' => User::where('role', 'customer')->count(),
            'categories' => Category::count(),
            'products' => Product::count(),
            'orders' => Order::count(),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'revenue' => Order::sum('total_amount'),
        ];

        $trends = $this->calculateTrends();

        $topSellingProducts = OrderItem::selectRaw('product_id, SUM(quantity) as total_sold')
            ->with('product.category', 'product.reviews')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get()
            ->map(fn ($item) => $item->product)
            ->filter()
            ->map(fn ($product) => $this->attachRating($product));

        if ($topSellingProducts->count() < 5) {
            $topSellingProducts = Product::with('category', 'reviews')
                ->latest()
                ->take(5)
                ->get()
                ->map(fn ($product) => $this->attachRating($product));
        }

        $recentOrders = Order::with('user')->latest()->take(5)->get();

        $totalSold = OrderItem::sum('quantity');
        $totalCost = Product::sum(\DB::raw('price * stock'));

        $monthlyOrders = Order::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count, SUM(total_amount) as total")
            ->where('created_at', '>=', Carbon::now()->subMonths(6)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $chartMonths = collect();
        $chartOrders = collect();
        $chartRevenue = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('Y-m');
            $label = Carbon::now()->subMonths($i)->format('M');
            $chartMonths->push($label);
            $data = $monthlyOrders->get($month);
            $chartOrders->push($data ? $data->count : 0);
            $chartRevenue->push($data ? (float) $data->total : 0);
        }

        return view('admin.dashboard', [
            'stats' => $stats,
            'trends' => $trends,
            'recentOrders' => $recentOrders,
            'topSellingProducts' => $topSellingProducts,
            'chartMonths' => $chartMonths,
            'chartOrders' => $chartOrders,
            'chartRevenue' => $chartRevenue,
            'pendingCount' => $stats['pendingOrders'],
            'totalSold' => $totalSold,
            'totalCost' => $totalCost,
        ]);
    }

    public function getData(Request $request): JsonResponse
    {
        $period = $request->input('period', 'month');

        $now = Carbon::now();

        $dateFrom = match ($period) {
            'day' => $now->copy()->startOfDay(),
            'week' => $now->copy()->startOfWeek(),
            'annual' => $now->copy()->startOfYear(),
            default => $now->copy()->startOfMonth(),
        };

        $dateTo = $now;

        $orders = Order::whereBetween('created_at', [$dateFrom, $dateTo]);
        $revenue = (float) $orders->sum('total_amount');
        $orderCount = $orders->count();
        $pendingCount = (clone $orders)->where('status', 'pending')->count();

        $users = User::whereBetween('created_at', [$dateFrom, $dateTo])->count();

        $prevFrom = match ($period) {
            'day' => $dateFrom->copy()->subDay()->startOfDay(),
            'week' => $dateFrom->copy()->subWeek()->startOfWeek(),
            'annual' => $dateFrom->copy()->subYear()->startOfYear(),
            default => $dateFrom->copy()->subMonth()->startOfMonth(),
        };
        $prevTo = $dateFrom->copy()->subSecond();

        $prevOrders = Order::whereBetween('created_at', [$prevFrom, $prevTo])->count();
        $prevRevenue = (float) Order::whereBetween('created_at', [$prevFrom, $prevTo])->sum('total_amount');
        $prevUsers = User::whereBetween('created_at', [$prevFrom, $prevTo])->count();

        $orderTrend = $prevOrders > 0 ? round((($orderCount - $prevOrders) / $prevOrders) * 100, 2) : 0;
        $revenueTrend = $prevRevenue > 0 ? round((($revenue - $prevRevenue) / $prevRevenue) * 100, 2) : 0;
        $userTrend = $prevUsers > 0 ? round((($users - $prevUsers) / $prevUsers) * 100, 2) : 0;

        $label = match ($period) {
            'day' => 'today',
            'week' => 'this week',
            'annual' => 'this year',
            default => 'this month',
        };

        return response()->json([
            'stats' => [
                'orders' => $orderCount,
                'revenue' => $revenue,
                'pendingOrders' => $pendingCount,
                'users' => $users,
            ],
            'trends' => [
                'orders' => $orderTrend,
                'revenue' => $revenueTrend,
                'users' => $userTrend,
            ],
            'label' => $label,
        ]);
    }

    private function attachRating($product)
    {
        $avg = $product->reviews->avg('rating');
        $product->avg_rating = $avg ? round($avg, 1) : null;
        $product->reviews_count = $product->reviews->count();
        return $product;
    }

    private function calculateTrends(): array
    {
        $now = Carbon::now();

        $prevMonthOrders = Order::whereBetween('created_at', [
            $now->copy()->subMonths(2)->startOfMonth(),
            $now->copy()->subMonth()->endOfMonth(),
        ])->count();

        $currentMonthOrders = Order::whereBetween('created_at', [
            $now->copy()->subMonth()->startOfMonth(),
            $now->endOfMonth(),
        ])->count();

        $orderTrend = $prevMonthOrders > 0
            ? round((($currentMonthOrders - $prevMonthOrders) / $prevMonthOrders) * 100, 2)
            : 0;

        $prevRevenue = Order::whereBetween('created_at', [
            $now->copy()->subMonths(2)->startOfMonth(),
            $now->copy()->subMonth()->endOfMonth(),
        ])->sum('total_amount');

        $currentRevenue = Order::whereBetween('created_at', [
            $now->copy()->subMonth()->startOfMonth(),
            $now->endOfMonth(),
        ])->sum('total_amount');

        $revenueTrend = $prevRevenue > 0
            ? round((($currentRevenue - $prevRevenue) / $prevRevenue) * 100, 2)
            : 0;

        $prevUsers = User::whereBetween('created_at', [
            $now->copy()->subMonths(2)->startOfMonth(),
            $now->copy()->subMonth()->endOfMonth(),
        ])->count();

        $currentUsers = User::whereBetween('created_at', [
            $now->copy()->subMonth()->startOfMonth(),
            $now->endOfMonth(),
        ])->count();

        $userTrend = $prevUsers > 0
            ? round((($currentUsers - $prevUsers) / $prevUsers) * 100, 2)
            : 0;

        return [
            'orders' => $orderTrend,
            'revenue' => $revenueTrend,
            'users' => $userTrend,
        ];
    }
}
