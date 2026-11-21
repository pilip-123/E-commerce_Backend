<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
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

        // ── Purchasing stats (net of completed returns) ──
        $totalCompletedReturns = (float) PurchaseReturn::where('status', PurchaseReturn::STATUS_COMPLETED)->sum('total_amount');
        $grossPurchaseTotal = PurchaseOrder::where('status', '!=', PurchaseOrder::STATUS_CANCELLED)->sum('grand_total');
        $grossPendingPayments = PurchaseOrder::whereIn('status', [
            PurchaseOrder::STATUS_APPROVED,
            PurchaseOrder::STATUS_ORDERED,
            PurchaseOrder::STATUS_PARTIALLY_RECEIVED,
            PurchaseOrder::STATUS_RECEIVED,
        ])->where('payment_status', '!=', PurchaseOrder::PAYMENT_PAID)->sum('grand_total');

        $purchaseStats = [
            'totalSuppliers' => Supplier::count(),
            'purchaseOrders' => PurchaseOrder::count(),
            'pendingPurchaseOrders' => PurchaseOrder::where('status', PurchaseOrder::STATUS_PENDING)->count(),
            'totalPurchaseAmount' => max(0, $grossPurchaseTotal - $totalCompletedReturns),
            'pendingPayments' => max(0, $grossPendingPayments - $totalCompletedReturns),
            'purchaseReturns' => PurchaseReturn::count(),
        ];

        $recentPurchases = PurchaseOrder::with('supplier')->latest()->take(3)->get();

        $monthlyPurchases = PurchaseOrder::selectRaw("DATE_FORMAT(order_date, '%Y-%m') as month, COUNT(*) as count, SUM(grand_total) as total")
            ->where('order_date', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->where('status', '!=', PurchaseOrder::STATUS_CANCELLED)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $purchaseChartMonths = collect();
        $purchaseChartTotals = collect();
        $purchaseChartCounts = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('Y-m');
            $purchaseChartMonths->push(Carbon::now()->subMonths($i)->format('M'));
            $data = $monthlyPurchases->get($month);
            $purchaseChartTotals->push($data ? (float) $data->total : 0);
            $purchaseChartCounts->push($data ? (int) $data->count : 0);
        }

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
            'purchaseStats' => $purchaseStats,
            'recentPurchases' => $recentPurchases,
            'purchaseChartMonths' => $purchaseChartMonths,
            'purchaseChartTotals' => $purchaseChartTotals,
            'purchaseChartCounts' => $purchaseChartCounts,
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

        $totalSoldPeriod = OrderItem::whereHas('order', fn($q) => $q->whereBetween('created_at', [$dateFrom, $dateTo]))
            ->sum('quantity');
        $totalCostPeriod = Product::sum(\DB::raw('price * stock'));
        $totalProducts = Product::count();

        $monthlyOrders = Order::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count, SUM(total_amount) as total")
            ->where('created_at', '>=', $dateFrom)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $chartMonths = [];
        $chartOrders = [];
        $chartRevenue = [];
        $cursor = $dateFrom->copy();
        while ($cursor <= $dateTo) {
            $key = $cursor->format('Y-m');
            $chartMonths[] = $cursor->format('M');
            $data = $monthlyOrders->get($key);
            $chartOrders[] = $data ? (int) $data->count : 0;
            $chartRevenue[] = $data ? (float) $data->total : 0;
            $cursor->addMonth();
        }

        // ── Purchasing stats (period-scoped, net of completed returns) ──
        $purchaseQuery = PurchaseOrder::whereBetween('order_date', [$dateFrom, $dateTo]);
        $grossPurchaseTotal = (float) (clone $purchaseQuery)->where('status', '!=', PurchaseOrder::STATUS_CANCELLED)->sum('grand_total');
        $grossPendingPayments = (float) (clone $purchaseQuery)
            ->whereIn('status', [
                PurchaseOrder::STATUS_APPROVED,
                PurchaseOrder::STATUS_ORDERED,
                PurchaseOrder::STATUS_PARTIALLY_RECEIVED,
                PurchaseOrder::STATUS_RECEIVED,
            ])
            ->where('payment_status', '!=', PurchaseOrder::PAYMENT_PAID)
            ->sum('grand_total');
        $completedReturns = (float) PurchaseReturn::where('status', PurchaseReturn::STATUS_COMPLETED)
            ->whereBetween('return_date', [$dateFrom, $dateTo])
            ->sum('total_amount');

        $purchaseStats = [
            'totalSuppliers' => Supplier::count(),
            'purchaseOrders' => (clone $purchaseQuery)->count(),
            'pendingPurchaseOrders' => (clone $purchaseQuery)->where('status', PurchaseOrder::STATUS_PENDING)->count(),
            'totalPurchaseAmount' => max(0, $grossPurchaseTotal - $completedReturns),
            'pendingPayments' => max(0, $grossPendingPayments - $completedReturns),
        ];

        // ── Purchases chart (period-bucketed) ──
        $chartFrom = match ($period) {
            'day' => $now->copy()->subDays(6)->startOfDay(),
            default => $dateFrom,
        };
        $granularity = $period === 'annual' ? 'month' : 'day';
        $dateFormat = $granularity === 'month' ? '%Y-%m' : '%Y-%m-%d';

        $periodPurchases = PurchaseOrder::selectRaw("DATE_FORMAT(order_date, '{$dateFormat}') as bucket, SUM(grand_total) as total")
            ->whereBetween('order_date', [$chartFrom, $dateTo])
            ->where('status', '!=', PurchaseOrder::STATUS_CANCELLED)
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get()
            ->keyBy('bucket');

        $purchaseChartLabels = [];
        $purchaseChartTotals = [];
        $cursor = $chartFrom->copy();
        while ($cursor <= $dateTo) {
            $key = $cursor->format($granularity === 'month' ? 'Y-m' : 'Y-m-d');
            $purchaseChartLabels[] = $cursor->format($granularity === 'month' ? 'M' : 'M d');
            $data = $periodPurchases->get($key);
            $purchaseChartTotals[] = $data ? (float) $data->total : 0;
            $granularity === 'month' ? $cursor->addMonth() : $cursor->addDay();
        }

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
                'products' => $totalProducts,
            ],
            'summary' => [
                'totalProducts' => $totalProducts,
                'totalSold' => $totalSoldPeriod,
                'totalCost' => $totalCostPeriod,
                'totalRevenue' => $revenue,
            ],
            'trends' => [
                'orders' => $orderTrend,
                'revenue' => $revenueTrend,
                'users' => $userTrend,
            ],
            'chart' => [
                'months' => $chartMonths,
                'orders' => $chartOrders,
                'revenue' => $chartRevenue,
            ],
            'purchaseStats' => $purchaseStats,
            'purchaseChart' => [
                'labels' => $purchaseChartLabels,
                'totals' => $purchaseChartTotals,
            ],
            'label' => $label,
        ]);
    }

    /**
     * Orders + revenue series for the live Summary chart.
     * Polled by the dashboard every few seconds; buckets are built in PHP so it
     * works on any database (MySQL in the app, SQLite in tests).
     */
    public function liveChart(Request $request): JsonResponse
    {
        $range = in_array($request->input('range'), ['24h', '7d', '30d', '12m'], true) ? $request->input('range') : '30d';
        $now = Carbon::now();

        [$from, $unit, $count, $keyFormat, $labelFormat] = match ($range) {
            '24h' => [$now->copy()->subHours(23)->startOfHour(), 'hour', 24, 'Y-m-d H', 'H:00'],
            '7d' => [$now->copy()->subDays(6)->startOfDay(), 'day', 7, 'Y-m-d', 'D d'],
            '12m' => [$now->copy()->subMonths(11)->startOfMonth(), 'month', 12, 'Y-m', 'M Y'],
            default => [$now->copy()->subDays(29)->startOfDay(), 'day', 30, 'Y-m-d', 'M d'],
        };

        $buckets = [];
        $cursor = $from->copy();
        for ($i = 0; $i < $count; $i++) {
            $buckets[$cursor->format($keyFormat)] = ['label' => $cursor->translatedFormat($labelFormat), 'orders' => 0, 'revenue' => 0.0];
            $cursor->addUnit($unit);
        }

        Order::where('created_at', '>=', $from)
            ->get(['created_at', 'total_amount'])
            ->each(function ($order) use (&$buckets, $keyFormat) {
                $key = $order->created_at->format($keyFormat);
                if (isset($buckets[$key])) {
                    $buckets[$key]['orders']++;
                    $buckets[$key]['revenue'] += (float) $order->total_amount;
                }
            });

        // Same-length window just before this one, for the "vs previous" chips.
        $previous = Order::whereBetween('created_at', [$from->copy()->subUnit($unit, $count), $from->copy()->subSecond()]);
        $series = array_values($buckets);

        return response()->json([
            'range' => $range,
            'labels' => array_column($series, 'label'),
            'orders' => array_column($series, 'orders'),
            'revenue' => array_map(fn ($b) => round($b['revenue'], 2), $series),
            'totals' => [
                'orders' => array_sum(array_column($series, 'orders')),
                'revenue' => round(array_sum(array_column($series, 'revenue')), 2),
            ],
            'previous' => [
                'orders' => (clone $previous)->count(),
                'revenue' => round((float) (clone $previous)->sum('total_amount'), 2),
            ],
            'latestOrderAt' => optional(Order::latest()->value('created_at'))->toIso8601String(),
            'updatedAt' => $now->toIso8601String(),
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
