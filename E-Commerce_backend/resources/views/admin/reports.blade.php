@extends('layouts.admin')

@section('title', __('Reports'))

@section('content')
    @include('admin.partials.charts')
    <div class="container-fluid p-0">

        {{-- ───── PAGE HEADER ───── --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                {{-- <h1 class="h3 fw-bold mb-0">{{ __('Reports & Analytics') }}</h1> --}}
            </div>
        </div>

        {{-- ───── SUMMARY STATS ───── --}}
        @php
            $pct = fn ($cur, $prev) => $prev > 0 ? ($cur - $prev) / $prev * 100 : null;
            $topCustomer = collect($topCustomers)->first();
            $topSeller = collect($bestSellers)->first();
            $customerShare = $topCustomer && $totalRevenue > 0 ? $topCustomer['total_spent'] / $totalRevenue * 100 : 0;
            $unitsSold = collect($bestSellers)->sum('units_sold');
        @endphp
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <x-admin.kpi color="green" icon="bi-currency-dollar" :label="__('Total Revenue')" :value="'$' . number_format($totalRevenue, 2)"
                    :trend="$pct($totalRevenue, $previousRevenue)" :trend-title="__('vs the 3 months before')" :spark="$weeklyRevenue"
                    :caption="__('Last 3 months') . ' · ' . __('weekly trend')" />
            </div>
            <div class="col-6 col-xl-3">
                <x-admin.kpi color="blue" icon="bi-receipt" :label="__('Total Orders')" :value="number_format($totalSales)"
                    :trend="$pct($totalSales, $previousSales)" :trend-title="__('vs the 3 months before')" :spark="$weeklyOrders"
                    :caption="__('Avg') . ' $' . number_format($totalSales ? $totalRevenue / $totalSales : 0, 2) . ' / ' . __('order')" />
            </div>
            <div class="col-6 col-xl-3">
                <x-admin.kpi color="amber" icon="bi-people" :label="__('Top Customers')" :value="number_format(count($topCustomers))"
                    :chip="$topCustomer ? number_format($customerShare) . '%' : null" chip-icon="bi-star" :chip-title="__('Top customer share of revenue')"
                    :caption="$topCustomer ? __('Top') . ': ' . \Illuminate\Support\Str::limit($topCustomer['name'], 16) . ' · $' . number_format($topCustomer['total_spent'], 2) : __('No customers yet')"
                    :meter="$topCustomer ? $customerShare : null" />
            </div>
            <div class="col-6 col-xl-3">
                <x-admin.kpi color="cyan" icon="bi-box-seam" :label="__('Best Sellers')" :value="number_format(count($bestSellers))"
                    :chip="$unitsSold ? number_format($unitsSold) . ' ' . __('sold') : null" chip-icon="bi-bag-check"
                    :caption="$topSeller ? __('Top') . ': ' . \Illuminate\Support\Str::limit($topSeller['name'], 16) . ' · ' . $topSeller['units_sold'] . ' ' . __('sold') : __('No sales yet')"
                    :meter="$topSeller && $unitsSold ? $topSeller['units_sold'] / $unitsSold * 100 : null" />
            </div>
        </div>

        {{-- ───── TABS ───── --}}
        <div class="d-flex flex-wrap gap-1 mb-4" id="reportTabs" role="tablist">
            <button class="rpt-tab active" data-target="daily-pane" role="tab"
                style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:2px solid var(--admin-border);border-radius:10px;background:var(--admin-surface);color:var(--admin-text-muted);font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;">
                <i class="bi bi-calendar"></i> {{ __('Daily Sales') }}
            </button>
            <button class="rpt-tab" data-target="monthly-pane" role="tab"
                style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:2px solid var(--admin-border);border-radius:10px;background:var(--admin-surface);color:var(--admin-text-muted);font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;">
                <i class="bi bi-bar-chart"></i> {{ __('Monthly Sales') }}
            </button>
            <button class="rpt-tab" data-target="revenue-pane" role="tab"
                style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:2px solid var(--admin-border);border-radius:10px;background:var(--admin-surface);color:var(--admin-text-muted);font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;">
                <i class="bi bi-cash-stack"></i> {{ __('Revenue') }}
            </button>
            <button class="rpt-tab" data-target="customers-pane" role="tab"
                style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:2px solid var(--admin-border);border-radius:10px;background:var(--admin-surface);color:var(--admin-text-muted);font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;">
                <i class="bi bi-people"></i> {{ __('Top Customers') }}
            </button>
            <button class="rpt-tab" data-target="sellers-pane" role="tab"
                style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:2px solid var(--admin-border);border-radius:10px;background:var(--admin-surface);color:var(--admin-text-muted);font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;">
                <i class="bi bi-graph-up"></i> {{ __('Best Sellers') }}
            </button>
            <button class="rpt-tab" data-target="promotions-pane" role="tab"
                style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:2px solid var(--admin-border);border-radius:10px;background:var(--admin-surface);color:var(--admin-text-muted);font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;">
                <i class="bi bi-percent"></i> {{ __('Promotions') }}
            </button>
            <button class="rpt-tab" data-target="purchases-pane" role="tab"
                style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:2px solid var(--admin-border);border-radius:10px;background:var(--admin-surface);color:var(--admin-text-muted);font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;">
                <i class="bi bi-truck"></i> {{ __('Purchasing') }}
            </button>
            @if ($savingReport)
                <button class="rpt-tab" data-target="saving-pane" role="tab"
                    style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:2px solid var(--admin-border);border-radius:10px;background:var(--admin-surface);color:var(--admin-text-muted);font-weight:600;font-size:.85rem;cursor:pointer;transition:all .2s;">
                    <i class="bi bi-piggy-bank"></i> {{ __('Saving Tracker') }}
                </button>
            @endif
        </div>

        <div class="tab-content">

            {{-- ════════════ DAILY SALES ════════════ --}}
            <div class="tab-pane show active" id="daily-pane" role="tabpanel">
                <div class="d-flex flex-wrap align-items-center gap-2 bg-white border rounded-3 p-3 mb-4 shadow-sm">
                    <label class="fw-semibold small text-uppercase mb-0">{{ __('Date') }}:</label>
                    <input type="date" class="form-control form-control-sm" style="max-width: 180px;" id="dailyDate"
                        value="{{ $today->format('Y-m-d') }}">
                    <button class="btn btn-sm btn-success" onclick="loadDailySales()">{{ __('Apply') }}</button>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 mini-kpi tone-info">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 mini-kpi-icon flex-shrink-0"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-cart text-info fs-5"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">{{ __('Total Sales') }}</p>
                                    <h5 class="fw-bold mb-0" id="dailyTotalSales">{{ number_format($dailyTotalSales) }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 mini-kpi tone-success">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 mini-kpi-icon flex-shrink-0"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-currency-dollar text-success fs-5"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">{{ __('Total Revenue') }}</p>
                                    <h5 class="fw-bold mb-0" id="dailyTotalRevenue">
                                        ${{ number_format($dailyTotalRevenue, 2) }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 mini-kpi tone-primary">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 mini-kpi-icon flex-shrink-0"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-calculator text-primary fs-5"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">{{ __('Average Order') }}</p>
                                    <h5 class="fw-bold mb-0" id="dailyAvgOrder">$0.00</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div
                        class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h5 class="fw-bold mb-0"><i
                                class="bi bi-clock-history text-success me-2"></i>{{ __('Hourly Sales Distribution') }}
                        </h5>
                        <span class="badge bg-success-subtle text-success rounded-pill small fw-bold"
                            id="dailyChartMeta">{{ $today->format('M d, Y') }}</span>
                    </div>
                    <div class="card-body py-4 px-4" style="background:var(--badge-success-bg);">
                        <div id="dailyHourlyChart" class="w-100" style="height:220px;position:relative;"></div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 rounded-4">
                        <h5 class="fw-bold mb-0"><i
                                class="bi bi-pie-chart text-success me-2"></i>{{ __('Status Breakdown') }}</h5>
                    </div>
                    <div class="card-body py-4 px-4">
                        <div class="row g-3" id="dailyStatus"></div>
                    </div>
                </div>
            </div>

            {{-- ════════════ MONTHLY SALES ════════════ --}}
            <div class="tab-pane" id="monthly-pane" role="tabpanel" style="display:none;">
                <div class="d-flex flex-wrap align-items-center gap-2 bg-white border rounded-3 p-3 mb-4 shadow-sm">
                    <label class="fw-semibold small text-uppercase mb-0">{{ __('Month') }}:</label>
                    <input type="month" class="form-control form-control-sm" style="max-width: 200px;"
                        id="monthlyMonth" value="{{ $currentMonth->format('Y-m') }}">
                    <button class="btn btn-sm btn-success" onclick="loadMonthlySales()">{{ __('Apply') }}</button>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 mini-kpi tone-info">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 mini-kpi-icon flex-shrink-0"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-cart text-info fs-5"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">{{ __('Total Sales') }} <span
                                            id="monthlySalesGrowth" class="small fw-bold text-success"
                                            style="display:none;"></span></p>
                                    <h5 class="fw-bold mb-0" id="monthlyTotalSales">
                                        {{ number_format($monthlyTotalSales) }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 mini-kpi tone-success">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 mini-kpi-icon flex-shrink-0"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-currency-dollar text-success fs-5"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">{{ __('Total Revenue') }} <span
                                            id="monthlyRevenueGrowth" class="small fw-bold text-success"
                                            style="display:none;"></span></p>
                                    <h5 class="fw-bold mb-0" id="monthlyTotalRevenue">
                                        ${{ number_format($monthlyTotalRevenue, 2) }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 mini-kpi tone-primary">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 mini-kpi-icon flex-shrink-0"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-calculator text-primary fs-5"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">{{ __('Average Order') }}</p>
                                    <h5 class="fw-bold mb-0" id="monthlyAvgOrder">$0.00</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div
                        class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h5 class="fw-bold mb-0"><i class="bi bi-graph-up text-success me-2"></i>{{ __('Daily Trends') }}
                        </h5>
                        <span class="badge bg-success-subtle text-success rounded-pill small fw-bold"
                            id="monthlyChartMeta">{{ $currentMonth->format('F Y') }}</span>
                    </div>
                    <div class="card-body py-4 px-4" style="background:var(--badge-success-bg);">
                        <div id="monthlyDailyChart" class="w-100" style="height:200px;position:relative;"></div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 rounded-4">
                        <h5 class="fw-bold mb-0"><i
                                class="bi bi-pie-chart text-success me-2"></i>{{ __('Status Breakdown') }}</h5>
                    </div>
                    <div class="card-body py-4 px-4">
                        <div class="row g-3" id="monthlyStatus"></div>
                    </div>
                </div>
            </div>

            {{-- ════════════ REVENUE ════════════ --}}
            <div class="tab-pane" id="revenue-pane" role="tabpanel">
                <div class="d-flex flex-wrap align-items-center gap-2 bg-white border rounded-3 p-3 mb-4 shadow-sm">
                    <label class="fw-semibold small text-uppercase mb-0">{{ __('From') }}:</label>
                    <input type="date" class="form-control form-control-sm" style="max-width: 160px;" id="revFrom"
                        value="{{ $threeMonthsAgo->format('Y-m-d') }}">
                    <label class="fw-semibold small text-uppercase mb-0 ms-2">{{ __('To') }}:</label>
                    <input type="date" class="form-control form-control-sm" style="max-width: 160px;" id="revTo"
                        value="{{ $now->format('Y-m-d') }}">
                    <button class="btn btn-sm btn-success" onclick="loadRevenue()">{{ __('Apply') }}</button>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100 mini-kpi tone-success">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 mini-kpi-icon flex-shrink-0"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-currency-dollar text-success fs-5"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">{{ __('Total Revenue') }}</p>
                                    <h5 class="fw-bold mb-0" id="revTotalRevenue">${{ number_format($totalRevenue, 2) }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100 mini-kpi tone-info">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 mini-kpi-icon flex-shrink-0"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-cart text-info fs-5"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">{{ __('Total Sales') }}</p>
                                    <h5 class="fw-bold mb-0" id="revTotalSales">{{ number_format($totalSales) }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100 mini-kpi tone-primary">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 mini-kpi-icon flex-shrink-0"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-check-circle text-primary fs-5"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">{{ __('Completed') }}</p>
                                    <h5 class="fw-bold mb-0" id="revCompleted">$0.00</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100 mini-kpi tone-warning">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 mini-kpi-icon flex-shrink-0"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-calculator text-warning fs-5"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">{{ __('Avg Order') }}</p>
                                    <h5 class="fw-bold mb-0" id="revAvgOrder">$0.00</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white border-0 pt-4 px-4 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <h5 class="fw-bold mb-1"><i class="bi bi-graph-up-arrow text-success me-2"></i>{{ __('Revenue by Month') }}</h5>
                                </div>
                                    <span class="badge bg-success-subtle text-success rounded-pill small fw-bold" id="revMonthlyChartMeta">
                                        {{ $threeMonthsAgo->format('M d, Y') }} - {{ $now->format('M d, Y') }}
                                </span>
                            </div>
                            <div class="card-body py-4 px-4 d-flex align-items-center" style="background:var(--badge-success-bg);"">
                                <div id="revMonthlyChart" class="w-100" style="height:250px;position:relative;box-sizing:border-box;padding:14px 16px 10px;border:1px solid rgba(5,150,105,.12);border-radius:16px;background:linear-gradient(180deg,rgba(255,255,255,.96),rgba(236,253,245,.72));box-shadow:inset 0 1px 3px rgba(15,23,42,.04);"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div
                                class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <h5 class="fw-bold mb-0"><i
                                        class="bi bi-pie-chart text-success me-2"></i>{{ __('Revenue by Category') }}</h5>
                            </div>
                            <div class="card-body py-4 px-4 d-flex align-items-center" style="background:var(--badge-success-bg);">
                                <div id="revCategoryBars" class="w-100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════════════ TOP CUSTOMERS ════════════ --}}
            <div class="tab-pane" id="customers-pane" role="tabpanel">
                <div class="d-flex flex-wrap align-items-center gap-2 bg-white border rounded-3 p-3 mb-4 shadow-sm">
                    <label class="fw-semibold small text-uppercase mb-0">{{ __('From') }}:</label>
                    <input type="date" class="form-control form-control-sm" style="max-width: 160px;" id="custFrom"
                        value="{{ $threeMonthsAgo->format('Y-m-d') }}">
                    <label class="fw-semibold small text-uppercase mb-0 ms-2">{{ __('To') }}:</label>
                    <input type="date" class="form-control form-control-sm" style="max-width: 160px;" id="custTo"
                        value="{{ $now->format('Y-m-d') }}">
                    <label class="fw-semibold small text-uppercase mb-0 ms-2">{{ __('Limit') }}:</label>
                    <input type="number" class="form-control form-control-sm" style="max-width: 70px;" id="custLimit"
                        value="10" min="1" max="50">
                    <button class="btn btn-sm btn-success" onclick="loadTopCustomers()">{{ __('Apply') }}</button>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 mini-kpi tone-success">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 mini-kpi-icon flex-shrink-0"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-currency-dollar text-success fs-5"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">{{ __('Customer Revenue') }}</p>
                                    <h5 class="fw-bold mb-0" id="custTotalRevenue">${{ number_format($totalRevenue, 2) }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 mini-kpi tone-info">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 mini-kpi-icon flex-shrink-0"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-people text-info fs-5"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">{{ __('Active Customers') }}</p>
                                    <h5 class="fw-bold mb-0" id="custCount">{{ count($topCustomers) }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 rounded-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-trophy text-success me-2"></i>{{ __('Top Customers') }}
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-3 small fw-bold text-uppercase">#</th>
                                        <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Name') }}</th>
                                        <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Email') }}</th>
                                        <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Orders') }}</th>
                                        <th class="px-4 py-3 small fw-bold text-uppercase text-end">
                                            {{ __('Total Spent') }}</th>
                                        <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Avg Order') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="custTable">
                                    @forelse ($topCustomers as $i => $customer)
                                        <tr>
                                            <td class="px-4 py-3 fw-bold text-muted">{{ $i + 1 }}</td>
                                            <td class="px-4 py-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span
                                                        class="d-inline-flex align-items-center justify-content-center rounded-circle text-white fw-bold flex-shrink-0"
                                                        style="width: 32px; height: 32px; font-size: 13px; background: #059669;">
                                                        {{ strtoupper(substr($customer['name'] ?? '?', 0, 1)) }}
                                                    </span>
                                                    <span class="fw-semibold">{{ $customer['name'] }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-muted">{{ $customer['email'] }}</td>
                                            <td class="px-4 py-3"><span
                                                    class="badge bg-light text-dark rounded-pill">{{ $customer['order_count'] }}</span>
                                            </td>
                                            <td class="px-4 py-3 fw-bold text-end">
                                                ${{ number_format($customer['total_spent'], 2) }}</td>
                                            <td class="px-4 py-3 fw-bold text-end">
                                                ${{ number_format($customer['average_order'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                {{ __('No customer data found.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════════════ BEST SELLERS ════════════ --}}
            <div class="tab-pane" id="sellers-pane" role="tabpanel">
                <div class="d-flex flex-wrap align-items-center gap-2 bg-white border rounded-3 p-3 mb-4 shadow-sm">
                    <label class="fw-semibold small text-uppercase mb-0">{{ __('From') }}:</label>
                    <input type="date" class="form-control form-control-sm" style="max-width: 160px;" id="sellFrom"
                        value="{{ $threeMonthsAgo->format('Y-m-d') }}">
                    <label class="fw-semibold small text-uppercase mb-0 ms-2">{{ __('To') }}:</label>
                    <input type="date" class="form-control form-control-sm" style="max-width: 160px;" id="sellTo"
                        value="{{ $now->format('Y-m-d') }}">
                    <label class="fw-semibold small text-uppercase mb-0 ms-2">{{ __('Limit') }}:</label>
                    <input type="number" class="form-control form-control-sm" style="max-width: 70px;" id="sellLimit"
                        value="10" min="1" max="50">
                    <button class="btn btn-sm btn-success" onclick="loadBestSellers()">{{ __('Apply') }}</button>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 mini-kpi tone-info">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 mini-kpi-icon flex-shrink-0"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-box-seam text-info fs-5"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">{{ __('Units Sold') }}</p>
                                    <h5 class="fw-bold mb-0" id="sellTotalUnits">0</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 mini-kpi tone-primary">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 mini-kpi-icon flex-shrink-0"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-grid text-primary fs-5"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">{{ __('Products Sold') }}</p>
                                    <h5 class="fw-bold mb-0" id="sellCount">{{ count($bestSellers) }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div
                        class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h5 class="fw-bold mb-0"><i
                                class="bi bi-graph-up text-success me-2"></i>{{ __('Best Sellers Chart') }}</h5>
                    </div>
                    <div class="card-body py-4 px-4" style="background:var(--badge-success-bg);">
                        <div id="sellersBarChart" class="w-100" style="height:220px;position:relative;"></div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 rounded-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-table text-success me-2"></i>{{ __('Product Details') }}
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-3 small fw-bold text-uppercase">#</th>
                                        <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Product') }}</th>
                                        <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Category') }}</th>
                                        <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Price') }}
                                        </th>
                                        <th class="px-4 py-3 small fw-bold text-uppercase text-center">
                                            {{ __('Units Sold') }}</th>
                                        <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Revenue') }}
                                        </th>
                                        <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Stock') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="sellersTable">
                                    @forelse ($bestSellers as $i => $product)
                                        <tr>
                                            <td class="px-4 py-3 fw-bold text-muted">{{ $i + 1 }}</td>
                                            <td class="px-4 py-3 fw-semibold">{{ $product['name'] }}</td>
                                            <td class="px-4 py-3 text-muted">{{ $product['category'] ?? '-' }}</td>
                                            <td class="px-4 py-3 fw-bold text-end">
                                                ${{ number_format($product['price'], 2) }}</td>
                                            <td class="px-4 py-3 text-center"><span
                                                    class="badge bg-light text-dark rounded-pill">{{ $product['units_sold'] }}</span>
                                            </td>
                                            <td class="px-4 py-3 fw-bold text-end">
                                                ${{ number_format($product['total_revenue'], 2) }}</td>
                                            <td class="px-4 py-3 text-center">{{ $product['stock'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                {{ __('No product data found.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════════════ PROMOTIONS ════════════ --}}
            <div class="tab-pane" id="promotions-pane" role="tabpanel">
                <div class="d-flex flex-wrap align-items-center gap-2 bg-white border rounded-3 p-3 mb-4 shadow-sm">
                    <label class="fw-semibold small text-uppercase mb-0">{{ __('Period') }}:</label>
                    <select class="form-select form-select-sm" style="max-width: 140px;" id="promoPeriod">
                        <option value="daily">{{ __('Daily') }}</option>
                        <option value="weekly">{{ __('Weekly') }}</option>
                        <option value="monthly">{{ __('Monthly') }}</option>
                        <option value="yearly">{{ __('Yearly') }}</option>
                    </select>
                    <button class="btn btn-sm btn-success" onclick="loadPromotions()">{{ __('Apply') }}</button>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card border-0 rounded-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width: 48px; height: 48px; background: rgba(255,255,255,.2);">
                                    <i class="bi bi-percent fs-5"></i>
                                </div>
                                <div>
                                    <p class="small text-uppercase fw-bold opacity-75 mb-0" style="font-size: 11px;">{{ __('This Week') }}</p>
                                    <h4 class="fw-bold mb-0" id="promoWeek">{{ $promoThisWeek }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card border-0 rounded-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width: 48px; height: 48px; background: rgba(255,255,255,.2);">
                                    <i class="bi bi-calendar-month fs-5"></i>
                                </div>
                                <div>
                                    <p class="small text-uppercase fw-bold opacity-75 mb-0" style="font-size: 11px;">{{ __('This Month') }}</p>
                                    <h4 class="fw-bold mb-0" id="promoMonth">{{ $promoThisMonth }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card border-0 rounded-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width: 48px; height: 48px; background: rgba(255,255,255,.2);">
                                    <i class="bi bi-calendar fs-5"></i>
                                </div>
                                <div>
                                    <p class="small text-uppercase fw-bold opacity-75 mb-0" style="font-size: 11px;">{{ __('This Year') }}</p>
                                    <h4 class="fw-bold mb-0" id="promoYear">{{ $promoThisYear }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card border-0 rounded-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width: 48px; height: 48px; background: rgba(255,255,255,.2);">
                                    <i class="bi bi-check-circle fs-5"></i>
                                </div>
                                <div>
                                    <p class="small text-uppercase fw-bold opacity-75 mb-0" style="font-size: 11px;">{{ __('Active Now') }}</p>
                                    <h4 class="fw-bold mb-0" id="promoActive">{{ $activePromotions }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white py-3 rounded-4 d-flex align-items-center gap-3">
                                <h6 class="fw-bold mb-0"><i class="bi bi-graph-up me-2"></i>{{ __('Daily Sales') }}</h6>
                                <div class="d-flex gap-2 ms-auto">
                                    <span class="small text-muted d-flex align-items-center gap-1"><span style="width:10px;height:10px;border-radius:2px;display:inline-block;background:#059669;"></span> {{ __('Revenue') }}</span>
                                    <span class="small text-muted d-flex align-items-center gap-1"><span style="width:10px;height:10px;border-radius:2px;display:inline-block;background:#f59e0b;"></span> {{ __('Orders') }}</span>
                                </div>
                            </div>
                            <div class="card-body py-4 px-4">
                                <div id="salesChart" class="w-100" style="height:240px;position:relative;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white py-3 rounded-4">
                                <h6 class="fw-bold mb-0"><i class="bi bi-pie-chart me-2"></i>{{ __('Promotions By Type') }}</h6>
                            </div>
                            <div class="card-body py-4 px-4">
                                <div id="promoTypeChart" class="w-100" style="height:240px;position:relative;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 rounded-4 text-white position-relative overflow-hidden h-100" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); min-height: 280px;">
                            <div class="card-body d-flex flex-column h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <p class="small text-uppercase fw-bold opacity-75 mb-0" style="font-size: 11px; letter-spacing: .08em;">{{ __('Promotions Created') }}</p>
                                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 38px; height: 38px; background: rgba(255,255,255,.2);">
                                        <i class="bi bi-percent fs-6"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 position-relative" style="min-height: 0;">
                                    <div id="promoChart" class="w-100 h-100" style="position:absolute;top:0;left:0;"></div>
                                </div>
                            </div>
                            <div class="position-absolute bottom-0 start-0 end-0" style="height: 36px; opacity: .3; pointer-events:none;">
                                <svg viewBox="0 0 200 36" class="w-100 h-100" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0,28 C20,26 40,20 60,18 C80,16 120,24 140,14 C160,8 180,10 200,6" stroke="white" stroke-width="2" fill="none"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 rounded-4 text-white position-relative overflow-hidden h-100" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); min-height: 280px;">
                            <div class="card-body d-flex flex-column h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <p class="small text-uppercase fw-bold opacity-75 mb-0" style="font-size: 11px; letter-spacing: .08em;">{{ __('Discount Codes') }}</p>
                                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 38px; height: 38px; background: rgba(255,255,255,.2);">
                                        <i class="bi bi-ticket fs-6"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 position-relative" style="min-height: 0;">
                                    <div id="codesChart" class="w-100 h-100" style="position:absolute;top:0;left:0;"></div>
                                </div>
                            </div>
                            <div class="position-absolute bottom-0 start-0 end-0" style="height: 36px; opacity: .3; pointer-events:none;">
                                <svg viewBox="0 0 200 36" class="w-100 h-100" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0,28 C20,24 50,20 80,16 C110,12 140,22 170,14 C185,10 195,8 200,6" stroke="white" stroke-width="2" fill="none"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white py-3 rounded-4">
                                <h6 class="fw-bold mb-0"><i class="bi bi-info-circle me-2"></i>{{ __('Summary') }}</h6>
                            </div>
                            <div class="card-body py-3 px-4">
                                <div class="row g-3" id="promoSummary">
                                    <div class="col-3">
                                        <div class="card border-0 rounded-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                                <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width: 42px; height: 42px; background: rgba(255,255,255,.2);">
                                                    <i class="bi bi-megaphone fs-6"></i>
                                                </div>
                                                <div>
                                                    <p class="small text-uppercase fw-bold opacity-75 mb-0" style="font-size: 10px;">{{ __('Total Promotions') }}</p>
                                                    <h5 class="fw-bold mb-0" id="promoTotalAll">{{ $totalPromotions }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="card border-0 rounded-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
                                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                                <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width: 42px; height: 42px; background: rgba(255,255,255,.2);">
                                                    <i class="bi bi-toggle-on fs-6"></i>
                                                </div>
                                                <div>
                                                    <p class="small text-uppercase fw-bold opacity-75 mb-0" style="font-size: 10px;">{{ __('Active') }}</p>
                                                    <h5 class="fw-bold mb-0" id="promoTotalActive">{{ $activePromotions }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="card border-0 rounded-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                                <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width: 42px; height: 42px; background: rgba(255,255,255,.2);">
                                                    <i class="bi bi-ticket fs-6"></i>
                                                </div>
                                                <div>
                                                    <p class="small text-uppercase fw-bold opacity-75 mb-0" style="font-size: 10px;">{{ __('Discount Codes') }}</p>
                                                    <h5 class="fw-bold mb-0" id="promoTotalCodes">{{ $totalDiscountCodes }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="card border-0 rounded-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                                <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width: 42px; height: 42px; background: rgba(255,255,255,.2);">
                                                    <i class="bi bi-graph-up-arrow fs-6"></i>
                                                </div>
                                                <div>
                                                    <p class="small text-uppercase fw-bold opacity-75 mb-0" style="font-size: 10px;">{{ __('Total Uses') }}</p>
                                                    <h5 class="fw-bold mb-0" id="promoTotalUses">{{ $totalCodeUses }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════════════ PURCHASING ════════════ --}}
            {{-- ════════════ SAVING TRACKER ════════════ --}}
            @if ($savingReport)
            @php
                $sr = $savingReport;
                $isKhr = $sr['currency'] === 'KHR';
                $money = fn ($usd) => $isKhr ? '៛' . number_format($usd * $sr['rate']) : ($usd < 0 ? '−$' : '$') . number_format(abs($usd), 2);
                $pct = fn ($cur, $prev) => $prev > 0 ? ($cur - $prev) / $prev * 100 : null;
                $catColors = ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316', '#64748b'];
                $mixTotal = max(0.01, $sr['currencyMix']['USD']['usd'] + $sr['currencyMix']['KHR']['usd']);
            @endphp
            <div class="tab-pane" id="saving-pane" role="tabpanel" style="display:none;">
                <div class="saving-filter mb-4">
                    <form method="GET" class="d-flex flex-wrap align-items-end gap-2">
                        <input type="hidden" name="tab" value="saving">
                        <div>
                            <label class="form-label small fw-semibold mb-1">{{ __('From') }}</label>
                            <input type="date" name="saving_from" class="form-control form-control-sm" value="{{ $sr['from']->format('Y-m-d') }}">
                        </div>
                        <div>
                            <label class="form-label small fw-semibold mb-1">{{ __('To') }}</label>
                            <input type="date" name="saving_to" class="form-control form-control-sm" value="{{ $sr['to']->format('Y-m-d') }}">
                        </div>
                        <div class="saving-cur" role="radiogroup" aria-label="{{ __('Show amounts in') }}">
                            <input type="radio" id="sr-usd" name="saving_currency" value="USD" @checked(! $isKhr) onchange="this.form.submit()"><label for="sr-usd"><b>$</b> USD</label>
                            <input type="radio" id="sr-khr" name="saving_currency" value="KHR" @checked($isKhr) onchange="this.form.submit()"><label for="sr-khr"><b>៛</b> KHR</label>
                        </div>
                        <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-funnel me-1"></i>{{ __('Apply') }}</button>
                        <a href="{{ route('admin.reports', ['tab' => 'saving']) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Reset') }}"><i class="bi bi-arrow-counterclockwise"></i></a>
                        <a href="{{ route('admin.saving.index') }}" class="btn btn-sm btn-outline-success ms-auto"><i class="bi bi-box-arrow-up-right me-1"></i>{{ __('Open tracker') }}</a>
                    </form>
                    <small class="text-muted d-block mt-2">
                        {{ $sr['from']->format('M j, Y') }} – {{ $sr['to']->format('M j, Y') }} · {{ $sr['days'] }} {{ __('days') }} · {{ __('Your personal tracker') }} · 1 USD = {{ number_format($sr['rate']) }} KHR
                    </small>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6 col-xl-3">
                        <x-admin.kpi color="green" icon="bi-piggy-bank" :label="__('Total saved')" :value="$money($sr['totals']['saved'])"
                            :trend="$pct($sr['totals']['saved'], $sr['previous']['saved'])" :trend-title="__('vs the previous :days days', ['days' => $sr['days']])"
                            :spark="$sr['series']['saved']" :caption="__('Avg') . ' ' . $money($sr['totals']['avgSavedPerDay']) . ' / ' . __('day')" />
                    </div>
                    <div class="col-6 col-xl-3">
                        <x-admin.kpi color="amber" icon="bi-credit-card" :label="__('Total spent')" :value="$money($sr['totals']['spent'])"
                            :trend="$pct($sr['totals']['spent'], $sr['previous']['spent'])" :higher-is-good="false" :trend-title="__('vs the previous :days days', ['days' => $sr['days']])"
                            :spark="$sr['series']['spent']" :caption="$sr['totals']['entries'] . ' ' . __('entries in this period')" />
                    </div>
                    <div class="col-6 col-xl-3">
                        <x-admin.kpi color="violet" icon="bi-wallet2" :label="__('Net balance')" :value="$money($sr['totals']['net'])"
                            :value-class="$sr['totals']['net'] < 0 ? 'text-danger' : ''"
                            :chip="$sr['totals']['net'] >= 0 ? __('Surplus') : __('Deficit')" :chip-tone="$sr['totals']['net'] >= 0 ? 'good' : 'bad'"
                            :chip-icon="$sr['totals']['net'] >= 0 ? 'bi-check2-circle' : 'bi-exclamation-circle'"
                            :spark="$sr['series']['balance']" :caption="__('Saved minus spent')" />
                    </div>
                    <div class="col-6 col-xl-3">
                        <x-admin.kpi color="blue" icon="bi-percent" :label="__('Savings rate')" :value="$sr['totals']['savingsRate'] . '%'"
                            :chip="$sr['totals']['savingsRate'] >= 50 ? __('Great') : ($sr['totals']['savingsRate'] >= 20 ? __('Good') : __('Low'))"
                            :chip-tone="$sr['totals']['savingsRate'] >= 50 ? 'good' : ($sr['totals']['savingsRate'] >= 20 ? 'warn' : 'bad')"
                            :meter="$sr['totals']['savingsRate']" :caption="__('Saved on') . ' ' . $sr['totals']['activeDays'] . ' ' . \Illuminate\Support\Str::plural(__('day'), $sr['totals']['activeDays'])" />
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                    <div>
                                        <h5 class="card-title fw-bold mb-0" style="font-size:15px;">{{ __('Saved vs spent') }}</h5>
                                        <small class="text-muted">{{ $sr['series']['daily'] ? __('Per day') : __('Per month') }} · {{ __('the purple line is the net') }}</small>
                                    </div>
                                    <div class="ac-legend">
                                        <span><i style="background:#10b981"></i>{{ __('Saved') }}</span>
                                        <span><i style="background:#f59e0b"></i>{{ __('Spent') }}</span>
                                        <span><i style="background:#7c3aed"></i>{{ __('Net') }}</span>
                                    </div>
                                </div>
                                <div id="savingFlowChart" style="height: 280px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-0" style="font-size:15px;">{{ __('Running balance') }}</h5>
                                <small class="text-muted">{{ __('How your net savings grew over the period') }}</small>
                                <div id="savingBalanceChart" style="height: 200px;" class="mt-2"></div>
                                <div class="saving-mini mt-3">
                                    <div><small>{{ __('Best saving day') }}</small><strong>{{ $sr['bestDay'] ? \Illuminate\Support\Carbon::parse($sr['bestDay']['date'])->format('M j') : '—' }}</strong><span>{{ $sr['bestDay'] ? $money($sr['bestDay']['usd']) : '' }}</span></div>
                                    <div><small>{{ __('Active saving days') }}</small><strong>{{ $sr['totals']['activeDays'] }} / {{ $sr['days'] }}</strong><span>{{ $sr['days'] ? round($sr['totals']['activeDays'] / $sr['days'] * 100) : 0 }}%</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    @foreach ([['title' => __('Where you saved'), 'rows' => $sr['savedByCategory'], 'empty' => __('No savings in this period.')], ['title' => __('Where you spent'), 'rows' => $sr['spentByCategory'], 'empty' => __('No spending in this period.')]] as $block)
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold mb-3" style="font-size:15px;">{{ $block['title'] }}</h5>
                                    @forelse ($block['rows'] as $i => $row)
                                        <div class="saving-cat">
                                            <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                                                <span class="fw-semibold small text-truncate"><i class="saving-dot" style="background:{{ $catColors[$i % 8] }}"></i>{{ $row['name'] }} <span class="text-muted">· {{ $row['count'] }}</span></span>
                                                <span class="fw-bold small flex-shrink-0">{{ $money($row['usd']) }}</span>
                                            </div>
                                            <div class="saving-track"><span style="width:{{ $row['share'] }}%;background:{{ $catColors[$i % 8] }}"></span></div>
                                        </div>
                                    @empty
                                        <p class="text-muted text-center small py-4 mb-0">{{ $block['empty'] }}</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-3" style="font-size:15px;">{{ __('Goals') }}</h5>
                                @foreach ([['label' => __('This month') . ' · ' . $sr['goals']['month']['label'], 'g' => $sr['goals']['month']], ['label' => __('This year') . ' · ' . $sr['goals']['year']['label'], 'g' => $sr['goals']['year']]] as $goal)
                                    <div class="saving-goal">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span class="fw-semibold">{{ $goal['label'] }}</span>
                                            <strong>{{ $goal['g']['pct'] !== null ? $goal['g']['pct'] . '%' : __('No goal') }}</strong>
                                        </div>
                                        <div class="saving-track saving-track--lg"><span style="width:{{ $goal['g']['pct'] ?? 0 }}%;"></span></div>
                                        <small class="text-muted">{{ $money($goal['g']['saved']) }} @if ($goal['g']['target']) {{ __('of') }} {{ $money($goal['g']['target']) }} @endif</small>
                                    </div>
                                @endforeach
                                <h6 class="fw-bold mt-3 mb-2" style="font-size:13px;">{{ __('Currency mix') }}</h6>
                                <div class="saving-mix" role="img" aria-label="{{ __('Entries by currency') }}">
                                    <span style="width:{{ $sr['currencyMix']['USD']['usd'] / $mixTotal * 100 }}%;background:#3b82f6"></span>
                                    <span style="width:{{ $sr['currencyMix']['KHR']['usd'] / $mixTotal * 100 }}%;background:#f59e0b"></span>
                                </div>
                                <div class="d-flex justify-content-between small text-muted mt-1">
                                    <span><i class="saving-dot" style="background:#3b82f6"></i>$ · {{ $sr['currencyMix']['USD']['count'] }} {{ __('entries') }}</span>
                                    <span><i class="saving-dot" style="background:#f59e0b"></i>៛ · {{ $sr['currencyMix']['KHR']['count'] }} {{ __('entries') }} ({{ '៛' . number_format($sr['currencyMix']['KHR']['riel']) }})</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-3" style="font-size:15px;">{{ __('Buying plans') }}</h5>
                                @forelse ($sr['plans'] as $plan)
                                    <div class="saving-plan">
                                        <div class="d-flex justify-content-between align-items-start gap-2">
                                            <div class="min-w-0">
                                                <strong class="d-block text-truncate">{{ $plan['name'] }}</strong>
                                                <small class="text-muted">{{ $plan['type'] }} · @if ($plan['daysLeft'] === null) {{ __('No deadline') }} @elseif ($plan['daysLeft'] < 0) <span class="text-danger">{{ __('Overdue') }}</span> @else {{ $plan['daysLeft'] }} {{ __('days left') }} @endif</small>
                                            </div>
                                            <span class="saving-pill {{ $plan['pct'] >= 100 ? 'is-done' : '' }}">{{ $plan['pct'] }}%</span>
                                        </div>
                                        <div class="saving-track saving-track--lg mt-2"><span style="width:{{ $plan['pct'] }}%;background:linear-gradient(90deg,#f472b6,#db2777)"></span></div>
                                        <small class="text-muted">{{ $money($plan['saved']) }} {{ __('of') }} {{ $money($plan['price']) }}</small>
                                    </div>
                                @empty
                                    <p class="text-muted text-center small py-4 mb-0">{{ __('No buying plans yet.') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-3" style="font-size:15px;">{{ __('Recent entries') }}</h5>
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0 saving-table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Category') }}</th>
                                                <th class="text-end">{{ __('Entered') }}</th>
                                                <th class="text-end">{{ __('Amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($sr['recent'] as $e)
                                                <tr>
                                                    <td class="text-nowrap small">{{ \Illuminate\Support\Carbon::parse($e->date)->format('M j, Y') }}</td>
                                                    <td>
                                                        <span class="fw-semibold small">{{ $e->category }}</span>
                                                        @if ($e->note)<small class="d-block text-muted text-truncate" style="max-width:220px;">{{ $e->note }}</small>@endif
                                                    </td>
                                                    <td class="text-end text-muted small text-nowrap">{{ $e->currency === 'KHR' ? '៛' . number_format($e->amount) : '$' . number_format($e->amount, 2) }}</td>
                                                    <td class="text-end text-nowrap"><span class="saving-amt {{ $e->kind }}">{{ $e->kind === 'saved' ? '+' : '−' }}{{ ltrim($money($e->usd), '−') }}</span></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="4" class="text-center text-muted small py-4">{{ __('No entries in this period.') }}</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="tab-pane" id="purchases-pane" role="tabpanel" style="display:none;">
                <div class="d-flex flex-wrap align-items-center gap-2 bg-white border rounded-3 p-3 mb-4 shadow-sm">
                    <form method="GET" class="row g-2 align-items-end w-100">
                        <input type="hidden" name="tab" value="purchases">
                        <div class="col-auto">
                            <input type="date" name="purchase_from" class="form-control form-control-sm" style="max-width: 160px;" value="{{ $purchaseFrom->format('Y-m-d') }}">
                        </div>
                        <div class="col-auto">
                            <input type="date" name="purchase_to" class="form-control form-control-sm" style="max-width: 160px;" value="{{ $purchaseTo->format('Y-m-d') }}">
                        </div>
                        <div class="col-auto">
                            <select name="purchase_supplier" class="form-select form-select-sm" style="max-width: 180px;">
                                <option value="">{{ __('All Suppliers') }}</option>
                                @foreach ($purchaseSuppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('purchase_supplier') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <select name="purchase_status" class="form-select form-select-sm" style="max-width: 170px;">
                                <option value="">{{ __('All Statuses') }}</option>
                                @foreach (['draft', 'pending', 'approved', 'ordered', 'partially_received', 'received', 'cancelled'] as $status)
                                    <option value="{{ $status }}" {{ request('purchase_status') === $status ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-funnel me-1"></i>{{ __('Apply') }}</button>
                            <a href="{{ route('admin.reports', ['tab' => 'purchases']) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                        </div>
                    </form>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100 mini-kpi tone-info">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 mini-kpi-icon flex-shrink-0" style="width: 48px; height: 48px;">
                                    <i class="bi bi-receipt text-info fs-5"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">{{ __('Purchase Orders') }}</p>
                                    <h5 class="fw-bold mb-0">{{ number_format($purchaseStats['totalOrders']) }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100 mini-kpi tone-success">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 mini-kpi-icon flex-shrink-0" style="width: 48px; height: 48px;">
                                    <i class="bi bi-cash-stack text-success fs-5"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">{{ __('Total Purchase Amount') }}</p>
                                    <h5 class="fw-bold mb-0">${{ number_format($purchaseStats['totalAmount'], 2) }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100 mini-kpi tone-warning">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 mini-kpi-icon flex-shrink-0" style="width: 48px; height: 48px;">
                                    <i class="bi bi-hourglass-split text-warning fs-5"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">{{ __('Pending Payments') }}</p>
                                    <h5 class="fw-bold mb-0">${{ number_format($purchaseStats['pendingPayments'], 2) }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100 mini-kpi tone-danger">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 mini-kpi-icon flex-shrink-0" style="width: 48px; height: 48px;">
                                    <i class="bi bi-arrow-return-left text-danger fs-5"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">{{ __('Purchase Returns') }}</p>
                                    <h5 class="fw-bold mb-0">{{ number_format($purchaseReturnStats['count']) }} <span class="text-muted" style="font-size: 12px;">(${{ number_format($purchaseReturnStats['totalAmount'], 2) }})</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart text-success me-2"></i>{{ __('Purchases Over Time') }}</h5>
                        @include('admin.partials.export-dropdown', ['exportRoute' => route('admin.export.purchase-spending')])
                    </div>
                    <div class="card-body py-4 px-4">
                        <div class="ac-legend mb-2"><span><i style="background:#059669"></i>{{ __('Purchases') }} <b>${{ number_format($purchaseChartTotals->sum(), 2) }}</b></span><span>{{ __('Avg') }} <b>${{ number_format($purchaseChartTotals->avg() ?? 0, 2) }}</b> / {{ __('month') }}</span></div>
                        <div id="reportPurchaseChart" style="height: 230px;"></div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <h5 class="fw-bold mb-0"><i class="bi bi-receipt text-success me-2"></i>{{ __('Purchase Orders') }}</h5>
                                @include('admin.partials.export-dropdown', ['exportRoute' => route('admin.export.purchase-report')])
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('PO') }}</th>
                                                <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Supplier') }}</th>
                                                <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Date') }}</th>
                                                <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Total') }}</th>
                                                <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($purchaseOrders as $po)
                                                <tr>
                                                    <td class="px-4 py-3"><a href="{{ route('admin.purchases.show', $po) }}" class="font-monospace fw-bold small text-decoration-none">{{ $po->po_number }}</a></td>
                                                    <td class="px-4 py-3 fw-semibold small">{{ $po->supplier->name ?? __('Deleted') }}</td>
                                                    <td class="px-4 py-3 text-muted small">{{ $po->order_date?->format('M d, Y') }}</td>
                                                    <td class="px-4 py-3 text-end fw-bold">${{ number_format($po->grand_total, 2) }}</td>
                                                    <td class="px-4 py-3 text-center">
                                                        @php
                                                            $badge = match ($po->status) {
                                                                'pending' => ['var(--badge-warning-bg)', 'var(--badge-warning-text)'],
                                                                'approved', 'ordered' => ['var(--badge-info-bg)', 'var(--badge-info-text)'],
                                                                'partially_received' => ['var(--badge-warning-bg)', 'var(--badge-warning-text)'],
                                                                'received' => ['var(--badge-success-bg)', 'var(--badge-success-text)'],
                                                                'cancelled' => ['var(--badge-red-bg)', 'var(--badge-red-text)'],
                                                                default => ['var(--badge-gray-bg)', 'var(--badge-gray-text)'],
                                                            };
                                                        @endphp
                                                        <span class="badge rounded-pill text-uppercase" style="font-size: 9px; background: {{ $badge[0] }}; color: {{ $badge[1] }};">{{ str_replace('_', ' ', $po->status) }}</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">{{ __('No purchase orders found.') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <h5 class="fw-bold mb-0"><i class="bi bi-arrow-return-left text-danger me-2"></i>{{ __('Purchase Returns') }}</h5>
                                @include('admin.partials.export-dropdown', ['exportRoute' => route('admin.export.purchase-returns')])
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Return') }}</th>
                                                <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Supplier') }}</th>
                                                <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Total') }}</th>
                                                <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($purchaseReturns as $return)
                                                <tr>
                                                    <td class="px-4 py-3"><a href="{{ route('admin.purchase-returns.show', $return) }}" class="font-monospace fw-bold small text-decoration-none">{{ $return->return_number }}</a></td>
                                                    <td class="px-4 py-3 fw-semibold small">{{ $return->supplier->name ?? __('Deleted') }}</td>
                                                    <td class="px-4 py-3 text-end fw-bold">${{ number_format($return->total_amount, 2) }}</td>
                                                    <td class="px-4 py-3 text-center">
                                                        @php
                                                            $badge = match ($return->status) {
                                                                'pending' => ['var(--badge-warning-bg)', 'var(--badge-warning-text)'],
                                                                'approved' => ['var(--badge-info-bg)', 'var(--badge-info-text)'],
                                                                'completed' => ['var(--badge-success-bg)', 'var(--badge-success-text)'],
                                                                'cancelled' => ['var(--badge-red-bg)', 'var(--badge-red-text)'],
                                                                default => ['var(--badge-gray-bg)', 'var(--badge-gray-text)'],
                                                            };
                                                        @endphp
                                                        <span class="badge rounded-pill text-uppercase" style="font-size: 9px; background: {{ $badge[0] }}; color: {{ $badge[1] }};">{{ $return->status }}</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-4">{{ __('No purchase returns found.') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h5 class="fw-bold mb-0"><i class="bi bi-truck text-success me-2"></i>{{ __('Supplier Purchase Summary') }}</h5>
                        @include('admin.partials.export-dropdown', ['exportRoute' => route('admin.export.supplier-purchase-report')])
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-3 small fw-bold text-uppercase">#</th>
                                        <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Supplier') }}</th>
                                        <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Orders') }}</th>
                                        <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Total Spent') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($supplierSpending as $i => $supplier)
                                        <tr>
                                            <td class="px-4 py-3 fw-bold text-muted">{{ $i + 1 }}</td>
                                            <td class="px-4 py-3 fw-semibold small">{{ $supplier->name }}</td>
                                            <td class="px-4 py-3 text-center"><span class="badge bg-light text-dark rounded-pill">{{ $supplier->po_count }}</span></td>
                                            <td class="px-4 py-3 text-end fw-bold">${{ number_format($supplier->po_total ?? 0, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">{{ __('No supplier data found.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection


@push('styles')
<style>
    .saving-filter { padding: 14px 16px; border-radius: 16px; background: var(--admin-surface); border: 1px solid var(--admin-border); }
    .saving-cur { position: relative; display: inline-grid; grid-template-columns: 1fr 1fr; padding: 3px; border-radius: 999px; background: var(--card-muted-bg); border: 1px solid var(--admin-border); }
    .saving-cur input { position: absolute; opacity: 0; pointer-events: none; }
    .saving-cur label { display: inline-flex; align-items: center; justify-content: center; gap: 4px; min-width: 62px; padding: 5px 10px; border-radius: 999px; font-size: .75rem; font-weight: 700; color: var(--admin-text-muted); cursor: pointer; transition: all .2s ease; }
    .saving-cur label b { font-size: .95rem; line-height: 1; }
    .saving-cur input:checked + label { color: #fff; background: linear-gradient(135deg, #10b981, #047857); box-shadow: 0 4px 10px -4px rgba(4, 120, 87, .6); }
    .saving-cur input:focus-visible + label { outline: 2px solid #10b981; outline-offset: 2px; }
    .saving-mini { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .saving-mini > div { display: grid; padding: 10px 12px; border-radius: 12px; background: var(--card-muted-bg); border: 1px solid var(--admin-border); }
    .saving-mini small { color: var(--admin-text-muted); font-size: .7rem; font-weight: 700; }
    .saving-mini strong { font-size: 1.05rem; }
    .saving-mini span { color: #059669; font-size: .78rem; font-weight: 700; }
    .saving-cat, .saving-goal, .saving-plan { margin-bottom: 14px; }
    .saving-plan { padding-bottom: 12px; border-bottom: 1px dashed var(--admin-border); }
    .saving-plan:last-child { border-bottom: 0; padding-bottom: 0; }
    .saving-dot { display: inline-block; width: 8px; height: 8px; margin-right: 6px; border-radius: 3px; }
    .saving-track { height: 7px; border-radius: 99px; background: var(--card-muted-bg); overflow: hidden; }
    .saving-track--lg { height: 9px; }
    .saving-track > span { display: block; height: 100%; border-radius: 99px; background: linear-gradient(90deg, #34d399, #059669); transition: width .6s ease; }
    .saving-mix { display: flex; gap: 2px; height: 9px; border-radius: 99px; overflow: hidden; background: var(--card-muted-bg); }
    .saving-pill { flex: none; padding: 3px 9px; border-radius: 999px; background: #fce7f3; color: #be185d; font-size: .75rem; font-weight: 800; }
    .saving-pill.is-done { background: #d1fae5; color: #047857; }
    .saving-table th { font-size: .7rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; color: var(--admin-text-muted); }
    .saving-amt { display: inline-block; padding: 4px 9px; border-radius: 9px; font-weight: 800; font-size: .85rem; }
    .saving-amt.saved { background: #d1fae5; color: #047857; }
    .saving-amt.spent { background: #fef3c7; color: #b45309; }
</style>
@endpush

@push('scripts')
    <script>
        @if ($savingReport)
        document.addEventListener('DOMContentLoaded', function () {
            var srKhr = @json($savingReport['currency'] === 'KHR'), srRate = {{ (int) $savingReport['rate'] }};
            var srConv = function (arr) { return arr.map(function (v) { return srKhr ? Math.round(v * srRate) : v; }); };
            // USD uses the chart library's money format ($ on axis + labels); riel gets its own ៛ formatter.
            var srFmt = srKhr ? function (v) { return '៛' + Math.round(v).toLocaleString(); } : 'money';
            var s = @json($savingReport['series']);
            AppCharts.bars('savingFlowChart', {
                labels: s.labels,
                emptyText: @json(__('No saving or spending in this period')),
                series: [
                    { name: @json(__('Saved')), values: srConv(s.saved), color: '#10b981', format: srFmt },
                    { name: @json(__('Spent')), values: srConv(s.spent), color: '#f59e0b', format: srFmt }
                ],
                line: { name: @json(__('Net')), values: srConv(s.net), color: '#7c3aed', format: srFmt },
                valueLabels: s.labels.length <= 14
            });
            AppCharts.line('savingBalanceChart', {
                labels: s.labels,
                emptyText: @json(__('No data yet')),
                average: false,
                series: [{ name: @json(__('Balance')), values: srConv(s.balance), color: '#7c3aed', format: srFmt }]
            });
        });
        @endif
        document.addEventListener('DOMContentLoaded', function () {
            AppCharts.bars('reportPurchaseChart', {
                labels: @json($purchaseChartMonths),
                emptyText: @json(__('No purchases in this period')),
                series: [{ name: @json(__('Purchases')), values: @json($purchaseChartTotals), color: '#059669', format: 'money' }]
            });
        });
        (function() {
            var R = {
                dailySales: @json(route('admin.reports.daily-sales')),
                monthlySales: @json(route('admin.reports.monthly-sales')),
                revenue: @json(route('admin.reports.revenue')),
                topCustomers: @json(route('admin.reports.top-customers')),
                bestSellers: @json(route('admin.reports.best-sellers')),
                promotions: @json(route('admin.reports.promotions')),
            };

            var sc = {
                Pending: '#f59e0b',
                Processing: '#3b82f6',
                Shipped: '#6366f1',
                Delivered: '#22c55e',
                Cancelled: '#ef4444'
            };
            var cc = ['#059669', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316', '#6366f1', '#d946ef', '#0ea5e9', '#84cc16', '#e11d48', '#06b6d4', '#a855f7', '#fb923c'];

            function fm(v) {
                return '$' + Number(v || 0).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function mx(a, k) {
                return a && a.length ? Math.max.apply(null, a.map(function(i) {
                    return i[k] || 0;
                })) || 1 : 1;
            }

            function fj(u, p) {
                var q = Object.keys(p).filter(function(k) {
                    return p[k] !== '' && p[k] != null;
                }).map(function(k) {
                    return encodeURIComponent(k) + '=' + encodeURIComponent(p[k]);
                }).join('&');
                return fetch(u + (q ? '?' + q : '')).then(function(r) {
                    return r.json();
                });
            }

            // Line chart for the Daily / Monthly / Revenue tabs (drawn by AppCharts).
            function lc(id, items, vk, lk, color) {
                var el = document.getElementById(id);
                if (!el) return;
                items = items || [];
                var labels = items.map(function(it) {
                    if (it[lk] == null) return '';
                    var label = String(it[lk]);
                    if (/^\d{4}-\d{2}$/.test(label)) return new Date(label + '-01T12:00:00').toLocaleDateString(undefined, { month: 'short', year: 'numeric' });
                    if (/^\d{4}-\d{2}-\d{2}$/.test(label)) return new Date(label + 'T12:00:00').toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
                    if (/^\d{1,2}$/.test(label)) return label.padStart(2, '0') + ':00';
                    return label;
                });
                var money = vk === 'total' || vk === 'revenue';
                AppCharts.line(el, {
                    title: id,
                    labels: labels,
                    emptyText: @json(__('No sales in this period')),
                    series: [{
                        name: money ? @json(__('Revenue')) : @json(__('Orders')),
                        values: items.map(function(it) { return it[vk] || 0; }),
                        color: color === 'c' ? '#06b6d4' : '#10b981',
                        format: money ? 'money' : 'number'
                    }]
                });
            }

            // Bar chart for the Best Sellers tab.
            function bc(id, items, vk, lk) {
                var el = document.getElementById(id);
                if (!el) return;
                items = items || [];
                AppCharts.bars(el, {
                    title: id,
                    labels: items.map(function(it) { return it[lk] != null ? String(it[lk]) : ''; }),
                    rotateLabels: items.length > 6,
                    emptyText: @json(__('No products sold in this period')),
                    series: [{ name: @json(__('Units sold')), values: items.map(function(it) { return it[vk] || 0; }), color: '#059669', format: 'number' }]
                });
            }

            function sg(id, items) {
                var el = document.getElementById(id);
                if (!el) return;
                if (!items || !items.length) {
                    el.innerHTML = '<div class="col-12 text-muted text-center py-3">No data</div>';
                    return;
                }
                el.innerHTML = items.map(function(s) {
                    var c = sc[s.status] || '#9ca3af';
                    return '<div class="col-sm-6 col-lg"><div class="d-flex align-items-center gap-3 py-3 px-3 border rounded-3 bg-white shadow-sm" style="border-left:3px solid ' +
                        c + '!important;"><span class="fw-semibold small flex-grow-1">' + (s.status || '') +
                        '</span><span class="badge bg-light text-dark rounded-pill px-3">' + (s.count || 0) +
                        '</span><span class="fw-bold small" style="color:' + c + ';">' + fm(s.revenue) +
                        '</span></div></div>';
                }).join('');
            }

            function ct(items) {
                var el = document.getElementById('custTable');
                if (!el) return;
                if (!items || !items.length) {
                    el.innerHTML =
                        '<tr><td colspan="6" class="text-center text-muted py-4">No customer data found.</td></tr>';
                    return;
                }
                el.innerHTML = items.map(function(c, i) {
                    var inl = (c.name || '?')[0].toUpperCase();
                    return '<tr><td class="px-4 py-3 fw-bold text-muted">' + (i + 1) +
                        '</td><td class="px-4 py-3"><div class="d-flex align-items-center gap-2"><span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white fw-bold flex-shrink-0" style="width:32px;height:32px;font-size:13px;background:#059669;">' +
                        inl + '</span><span class="fw-semibold">' + (c.name || '') +
                        '</span></div></td><td class="px-4 py-3 text-muted">' + (c.email || '-') +
                        '</td><td class="px-4 py-3"><span class="badge bg-light text-dark rounded-pill">' + (c
                            .order_count || 0) + '</span></td><td class="px-4 py-3 fw-bold text-end">' + fm(c
                            .total_spent) + '</td><td class="px-4 py-3 fw-bold text-end">' + fm(c
                        .average_order) + '</td></tr>';
                }).join('');
            }

            function st(items) {
                var el = document.getElementById('sellersTable');
                if (!el) return;
                if (!items || !items.length) {
                    el.innerHTML =
                        '<tr><td colspan="7" class="text-center text-muted py-4">No product data found.</td></tr>';
                    return;
                }
                el.innerHTML = items.map(function(p, i) {
                    return '<tr><td class="px-4 py-3 fw-bold text-muted">' + (i + 1) +
                        '</td><td class="px-4 py-3 fw-semibold">' + (p.name || '') +
                        '</td><td class="px-4 py-3 text-muted">' + (p.category || '-') +
                        '</td><td class="px-4 py-3 fw-bold text-end">' + fm(p.price) +
                        '</td><td class="px-4 py-3 text-center"><span class="badge bg-light text-dark rounded-pill">' +
                        (p.units_sold || 0) + '</span></td><td class="px-4 py-3 fw-bold text-end">' + fm(p
                            .total_revenue) + '</td><td class="px-4 py-3 text-center">' + (p.stock || 0) +
                        '</td></tr>';
                }).join('');
            }

            function cb(items) {
                var el = document.getElementById('revCategoryBars');
                if (!el) return;
                if (!items || !items.length) {
                    el.innerHTML = '<p class="text-muted text-center py-4 mb-0">No category data found.</p>';
                    return;
                }
                var total = items.reduce(function(s, c) {
                    return s + (c.total || 0);
                }, 0);
                var cx = 130,
                    cy = 130,
                    or = 100,
                    ir = 58;

                function pt(a, r) {
                    return (cx + r * Math.cos(a - 0.5 * Math.PI)).toFixed(1) + ',' + (cy + r * Math.sin(a - 0.5 * Math
                        .PI)).toFixed(1);
                }

                function arcPath(s, e) {
                    var o1 = pt(s, or),
                        o2 = pt(e, or),
                        i1 = pt(e, ir),
                        i2 = pt(s, ir);
                    var la = (e - s > Math.PI) ? 1 : 0;
                    return 'M' + o1 + ' A' + or + ',' + or + ' 0 ' + la + ',1 ' + o2 + ' L' + i1 + ' A' + ir + ',' +
                        ir + ' 0 ' + la + ',0 ' + i2 + ' Z';
                }
                var slices = [],
                    angle = 0,
                    tt = document.createElement('div');
                tt.id = 'dnttip';
                tt.style.cssText =
                    'position:absolute;top:-10px;left:50%;transform:translateX(-50%);background:#f59e0b;color:#fff;padding:8px 18px;border-radius:10px;font-size:.85rem;font-weight:700;pointer-events:none;opacity:0;transition:opacity .2s;z-index:99;white-space:nowrap;box-shadow:0 6px 20px rgba(0,0,0,.25);';
                items.forEach(function(c, i) {
                    var pct = c.total / total,
                        sliceAngle = pct * 2 * Math.PI;
                    var path = arcPath(angle, angle + sliceAngle),
                        co = cc[i % cc.length];
                    slices.push({
                        path: path,
                        color: co,
                        category: c.category,
                        value: c.total,
                        pct: pct,
                        idx: i
                    });
                    angle += sliceAngle;
                });
                var svg = '<svg viewBox="0 0 260 260" width="100%" style="max-width:250px;height:auto;">';
                slices.forEach(function(s) {
                    svg += '<path d="' + s.path + '" fill="' + s.color +
                        '" stroke="#f59e0b" stroke-width="3" style="cursor:pointer;transition:transform .15s,opacity .15s;" onmouseover="var t=document.getElementById(\'dnttip\');t.style.opacity=\'1\';t.textContent=\'' +
                        s.category + ': ' + fm(s.value) + ' (' + (s.pct * 100).toFixed(1) +
                        '%)\';this.style.opacity=\'.85\';this.setAttribute(\'transform\',\'translate(-4,-4)\');" onmouseout="var t=document.getElementById(\'dnttip\');t.style.opacity=\'0\';this.style.opacity=\'1\';this.setAttribute(\'transform\',\'translate(0,0)\');"></path>';
                });
                svg += '<circle cx="' + cx + '" cy="' + cy + '" r="' + ir +
                    '" fill="#fff" stroke="#f59e0b" stroke-width="2"/></svg>';
                var html =
                    '<div style="position:relative;display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:24px;padding:4px 0;">' +
                    '<div style="position:relative;flex-shrink:0;">' + svg + tt.outerHTML +
                    '</div><div style="display:grid;gap:10px;">';
                slices.forEach(function(s) {
                    var pct = (s.pct * 100).toFixed(1);
                    html +=
                        '<div style="display:flex;align-items:center;gap:10px;min-width:180px;" onmouseover="this.querySelector(\'span\').style.opacity=\'1\'" onmouseout="this.querySelector(\'span\').style.opacity=\'.7\'"><span style="width:16px;height:16px;border-radius:4px;background:' +
                        s.color +
                        ';flex-shrink:0;opacity:.7;transition:opacity .15s;"></span><span style="font-size:.85rem;font-weight:600;color:var(--bs-body-color);flex:1;">' +
                        (s.category || '') +
                        '</span><span style="font-size:.85rem;font-weight:700;color:var(--bs-body-color);">' +
                        fm(s.value) +
                        '</span><span style="font-size:.75rem;font-weight:600;color:var(--bs-secondary-color);">(' +
                        pct + '%)</span></div>';
                });
                html += '</div></div>';
                el.innerHTML = html;
            }

            window.loadDailySales = function() {
                var d = document.getElementById('dailyDate').value || '';
                fj(R.dailySales, {
                    date: d
                }).then(function(r) {
                    document.getElementById('dailyTotalSales').textContent = (r.total_sales || 0)
                        .toLocaleString();
                    document.getElementById('dailyTotalRevenue').textContent = fm(r.total_revenue);
                    document.getElementById('dailyAvgOrder').textContent = fm(r.average_order);
                    var meta = document.getElementById('dailyChartMeta');
                    if (meta && r.date) meta.textContent = new Date(r.date + 'T12:00:00')
                        .toLocaleDateString('en-US', {
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric'
                        });
                    lc('dailyHourlyChart', r.hourly_distribution || [], 'count', 'hour', 'c');
                    sg('dailyStatus', r.status_breakdown || []);
                }).catch(function() {});
            };
            window.loadMonthlySales = function() {
                var m = document.getElementById('monthlyMonth').value || '';
                fj(R.monthlySales, {
                    month: m
                }).then(function(r) {
                    document.getElementById('monthlyTotalSales').textContent = (r.total_sales || 0)
                        .toLocaleString();
                    document.getElementById('monthlyTotalRevenue').textContent = fm(r.total_revenue);
                    document.getElementById('monthlyAvgOrder').textContent = fm(r.average_order);
                    var meta = document.getElementById('monthlyChartMeta');
                    if (meta && r.month) meta.textContent = new Date(r.month + '-01T12:00:00')
                        .toLocaleDateString('en-US', {
                            month: 'long',
                            year: 'numeric'
                        });
                    var sv = document.getElementById('monthlySalesGrowth'),
                        rv = document.getElementById('monthlyRevenueGrowth');
                    var s = r.sales_growth || 0,
                        v = r.revenue_growth || 0;
                    if (sv) {
                        sv.style.display = 'inline';
                        sv.className = 'small fw-bold ' + (s >= 0 ? 'text-success' : 'text-danger');
                        sv.innerHTML = (s >= 0 ? '&#8593; ' : '&#8595; ') + Math.abs(s).toFixed(2) + '%';
                    }
                    if (rv) {
                        rv.style.display = 'inline';
                        rv.className = 'small fw-bold ' + (v >= 0 ? 'text-success' : 'text-danger');
                        rv.innerHTML = (v >= 0 ? '&#8593; ' : '&#8595; ') + Math.abs(v).toFixed(2) + '%';
                    }
                    lc('monthlyDailyChart', r.daily_distribution || [], 'total', 'date', 'g');
                    sg('monthlyStatus', r.status_breakdown || []);
                }).catch(function() {});
            };
            window.loadRevenue = function() {
                var f = document.getElementById('revFrom').value || '',
                    t = document.getElementById('revTo').value || '';
                fj(R.revenue, {
                    from: f,
                    to: t
                }).then(function(r) {
                    document.getElementById('revTotalRevenue').textContent = fm(r.total_revenue);
                    document.getElementById('revTotalSales').textContent = (r.total_sales || 0)
                        .toLocaleString();
                    document.getElementById('revCompleted').textContent = fm(r.completed_revenue);
                    document.getElementById('revAvgOrder').textContent = fm(r.average_order);
                    lc('revMonthlyChart', r.monthly_breakdown || [], 'total', 'month', 'g');
                    cb(r.revenue_by_category || []);
                }).catch(function() {});
            };
            window.loadTopCustomers = function() {
                var f = document.getElementById('custFrom').value || '',
                    t = document.getElementById('custTo').value || '',
                    l = document.getElementById('custLimit').value || 10;
                fj(R.topCustomers, {
                    from: f,
                    to: t,
                    limit: l
                }).then(function(r) {
                    document.getElementById('custTotalRevenue').textContent = fm(r.total_customer_revenue);
                    document.getElementById('custCount').textContent = (r.top_customers || []).length;
                    ct(r.top_customers || []);
                }).catch(function() {});
            };
            window.loadBestSellers = function() {
                var f = document.getElementById('sellFrom').value || '',
                    t = document.getElementById('sellTo').value || '',
                    l = document.getElementById('sellLimit').value || 10;
                fj(R.bestSellers, {
                    from: f,
                    to: t,
                    limit: l
                }).then(function(r) {
                    document.getElementById('sellTotalUnits').textContent = (r.total_units_sold || 0)
                        .toLocaleString();
                    document.getElementById('sellCount').textContent = (r.best_sellers || []).length;
                    bc('sellersBarChart', r.best_sellers || [], 'units_sold', 'name', 'p', 1);
                    st(r.best_sellers || []);
                }).catch(function() {});
            };

            function promoLineChart(id, items, lk) {
                var el = document.getElementById(id);
                if (!el) return;
                if (!items || !items.length) {
                    el.innerHTML = '<p class="text-muted text-center py-4 mb-0" style="margin-top:40px;">No data</p>';
                    return;
                }
                var maxVal = items.reduce(function(m, d) { return Math.max(m, d.count || 0); }, 0) || 1;
                var w = 300, h = 120;
                var pts = items.map(function(d, i) {
                    var x = (i / Math.max(items.length - 1, 1)) * w;
                    var y = h - ((d.count || 0) / maxVal) * (h - 4) - 2;
                    return { x: x, y: y, label: d[lk] || '', value: d.count || 0, index: i };
                });
                var d = pts.map(function(p, i) {
                    if (i === 0) return 'M' + p.x.toFixed(1) + ',' + p.y.toFixed(1);
                    var cp1x = pts[i - 1].x + (p.x - pts[i - 1].x) / 2;
                    return 'S' + cp1x.toFixed(1) + ',' + pts[i - 1].y.toFixed(1) + ',' + p.x.toFixed(1) + ',' + p.y.toFixed(1);
                }).join(' ');
                var areaD = d + ' L' + pts[pts.length - 1].x.toFixed(1) + ',' + h + ' L' + pts[0].x.toFixed(1) + ',' + h + ' Z';
                var dotsHtml = pts.map(function(p) {
                    return '<circle cx="' + p.x + '" cy="' + p.y + '" r="3" fill="rgba(255,255,255,0.01)" stroke="none" style="cursor:pointer;" data-idx="' + p.index + '"/>';
                }).join('');
                var gradId = 'sp_' + id;
                var svg = '<svg viewBox="0 0 ' + w + ' ' + h + '" preserveAspectRatio="none" style="width:100%;height:100%;display:block;position:absolute;top:0;left:0;" xmlns="http://www.w3.org/2000/svg">' +
                    '<defs><linearGradient id="' + gradId + '" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#fff" stop-opacity="0.3"/><stop offset="100%" stop-color="#fff" stop-opacity="0.02"/></linearGradient></defs>' +
                    '<path d="' + areaD + '" fill="url(#' + gradId + ')"/>' +
                    '<path d="' + d + '" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>' +
                    '<g>' + dotsHtml + '</g>' +
                    '</svg>';
                el.innerHTML = '<div style="position:relative;width:100%;height:100%;">' + svg + '</div>';
                var dots = el.querySelectorAll('circle');
                var tip = document.createElement('div');
                tip.style.cssText = 'display:none;position:absolute;background:#1e293b;color:#fff;padding:4px 10px;border-radius:5px;font-size:11px;font-weight:600;pointer-events:none;white-space:nowrap;z-index:10;transform:translate(-50%,-130%);box-shadow:0 3px 10px rgba(0,0,0,.25);';
                el.querySelector('div').appendChild(tip);
                dots.forEach(function(dot) {
                    dot.addEventListener('mouseenter', function(e) {
                        var idx = parseInt(this.getAttribute('data-idx'));
                        if (isNaN(idx) || !pts[idx]) return;
                        var p = pts[idx];
                        tip.textContent = p.label + ': ' + p.value.toLocaleString();
                        tip.style.display = 'block';
                        var rect = el.getBoundingClientRect();
                        tip.style.left = (e.clientX - rect.left) + 'px';
                        tip.style.top = (p.y / h * rect.height) + 'px';
                    });
                    dot.addEventListener('mouseleave', function() {
                        tip.style.display = 'none';
                    });
                });
            }

            // Promotions tab: revenue (left axis) vs orders (right axis, dashed).
            function salesLineChart(id, items, lk) {
                var el = document.getElementById(id);
                if (!el) return;
                items = items || [];
                AppCharts.line(el, {
                    title: id,
                    labels: items.map(function(d) { return d[lk] || ''; }),
                    emptyText: @json(__('No sales in this period')),
                    series: [
                        { name: @json(__('Revenue')), values: items.map(function(d) { return d.revenue || 0; }), color: '#059669', format: 'money' },
                        { name: @json(__('Orders')), values: items.map(function(d) { return d.orders || 0; }), color: '#f59e0b', format: 'number', axis: 'right', dashed: true }
                    ]
                });
            }

            window.loadPromotions = function() {
                var p = document.getElementById('promoPeriod').value;
                fj(R.promotions, { period: p }).then(function(r) {
                    var pData = r.promotions || [];
                    var cData = r.discount_codes || [];
                    var sData = r.sales || [];
                    var lk = r.label_key || 'date';
                    var s = r.summary || {};
                    salesLineChart('salesChart', sData, lk);
                    promoLineChart('promoChart', pData, lk);
                    promoLineChart('codesChart', cData, lk);
                    var tb = r.type_breakdown || [];
                    var tEl = document.getElementById('promoTypeChart');
                    if (tEl) {
                        if (!tb.length) {
                            tEl.innerHTML = '<p class="text-muted text-center py-4 mb-0">No data</p>';
                        } else {
                            var total = tb.reduce(function(acc, t) { return acc + (t.count || 0); }, 0);
                            var colors = ['#059669', '#d97706'];
                            var labels = { percentage: 'Percentage', fixed: 'Fixed' };
                            var cx = 130, cy = 130, r2 = 100, ir = 50;
                            var angle = -Math.PI / 2;
                            var svg = '<svg viewBox="0 0 260 260" width="100%" style="max-width:180px;height:auto;display:block;margin:0 auto;">';
                            tb.forEach(function(t, i) {
                                var sliceAngle = (t.count / total) * Math.PI * 2;
                                var x1 = cx + r2 * Math.cos(angle);
                                var y1 = cy + r2 * Math.sin(angle);
                                angle += sliceAngle;
                                var x2 = cx + r2 * Math.cos(angle);
                                var y2 = cy + r2 * Math.sin(angle);
                                var large = sliceAngle > Math.PI ? 1 : 0;
                                var d = 'M' + cx + ',' + cy + ' L' + x1 + ',' + y1 + ' A' + r2 + ',' + r2 + ' 0 ' + large + ' 1 ' + x2 + ',' + y2 + ' Z';
                                svg += '<path d="' + d + '" fill="' + colors[i % colors.length] + '" stroke="#d97706" stroke-width="3"/>';
                            });
                            svg += '<circle cx="' + cx + '" cy="' + cy + '" r="' + ir + '" fill="#059669" stroke="#e2e" stroke-width="2"/>';
                            svg += '</svg>';
                            var legend = '<div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-top:8px;">';
                            tb.forEach(function(t, i) {
                                var pct = total > 0 ? ((t.count / total) * 100).toFixed(1) : 0;
                                legend += '<div style="display:flex;align-items:center;gap:6px;"><span style="width:12px;height:12px;border-radius:3px;background:' + colors[i % colors.length] + ';"></span><span style="font-size:.8rem;color:#64748b;">' + (labels[t.type] || t.type) + ' (' + pct + '%)</span></div>';
                            });
                            legend += '</div>';
                            tEl.innerHTML = '<div style="text-align:center;">' + svg + legend + '</div>';
                        }
                    }
                    document.getElementById('promoTotalAll').textContent = s.total_promotions || 0;
                    document.getElementById('promoTotalActive').textContent = s.active_promotions || 0;
                    document.getElementById('promoTotalCodes').textContent = s.total_codes || 0;
                    document.getElementById('promoTotalUses').textContent = s.total_code_uses || 0;
                }).catch(function() {});
            };

            document.addEventListener('DOMContentLoaded', function() {
                // Tab switching with auto-load
                var tabs = document.querySelectorAll('.rpt-tab');
                var panes = {};
                document.querySelectorAll('.tab-pane').forEach(function(p) {
                    panes[p.id] = p;
                });
                var loaders = {
                    dailyPane: function() {
                        window.loadDailySales();
                    },
                    monthlyPane: function() {
                        window.loadMonthlySales();
                    },
                    revenuePane: function() {
                        window.loadRevenue();
                    },
                    customersPane: function() {
                        window.loadTopCustomers();
                    },
                    sellersPane: function() {
                        window.loadBestSellers();
                    },
                    promotionsPane: function() {
                        window.loadPromotions();
                    }
                };
                tabs.forEach(function(t) {
                    t.addEventListener('click', function() {
                        var id = this.getAttribute('data-target');
                        var target = panes[id];
                        if (!target) return;
                        tabs.forEach(function(x) {
                            x.style.borderColor = 'var(--admin-border)';
                            x.style.background = 'var(--admin-surface)';
                            x.style.color = 'var(--admin-text-muted)';
                        });
                        this.style.borderColor = 'var(--admin-primary)';
                        this.style.background = 'var(--admin-primary-light)';
                        this.style.color = 'var(--admin-primary-dark)';
                        Object.keys(panes).forEach(function(k) {
                            panes[k].style.display = 'none';
                            panes[k].classList.remove('show', 'active');
                        });
                        target.style.display = 'block';
                        target.classList.add('show', 'active');
                        var loader = loaders[id.replace(/-(\w)/g, function(_, c) { return c.toUpperCase(); })]; // 'sellers-pane' → 'sellersPane'
                        if (loader) setTimeout(loader, 50);
                    });
                });
                // Set initial active tab (honour ?tab=... query param)
                var requestedTab = new URLSearchParams(window.location.search).get('tab');
                var initialTab = tabs[0];
                if (requestedTab) {
                    tabs.forEach(function(t) {
                        if (t.getAttribute('data-target') === requestedTab + '-pane') initialTab = t;
                    });
                }
                if (tabs.length) {
                    initialTab.click();
                }
                window.loadDailySales();
                window.loadMonthlySales();
                window.loadRevenue();
            });
        })();
    </script>
@endpush
