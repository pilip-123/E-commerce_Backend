@extends('layouts.admin')

@section('title', __('Reports'))

@section('content')
    <div class="container-fluid p-0">

        {{-- ───── PAGE HEADER ───── --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                {{-- <h1 class="h3 fw-bold mb-0">{{ __('Reports & Analytics') }}</h1> --}}
            </div>
        </div>

        {{-- ───── SUMMARY STATS ───── --}}
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body d-flex align-items-center gap-4 py-4 px-4">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle flex-shrink-0"
                            style="width: 56px; height: 56px;">
                            <i class="bi bi-currency-dollar text-success fs-3"></i>
                        </div>
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.04em;">
                                {{ __('Total Revenue') }}</p>
                            <h4 class="fw-bold mb-0">${{ number_format($totalRevenue, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body d-flex align-items-center gap-4 py-4 px-4">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle flex-shrink-0"
                            style="width: 56px; height: 56px;">
                            <i class="bi bi-receipt text-primary fs-3"></i>
                        </div>
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.04em;">
                                {{ __('Total Orders') }}</p>
                            <h4 class="fw-bold mb-0">{{ number_format($totalSales) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body d-flex align-items-center gap-4 py-4 px-4">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-warning-subtle flex-shrink-0"
                            style="width: 56px; height: 56px;">
                            <i class="bi bi-people text-warning fs-3"></i>
                        </div>
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.04em;">
                                {{ __('Top Customers') }}</p>
                            <h4 class="fw-bold mb-0">{{ count($topCustomers) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body d-flex align-items-center gap-4 py-4 px-4">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0"
                            style="width: 56px; height: 56px;">
                            <i class="bi bi-box-seam text-info fs-3"></i>
                        </div>
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.04em;">
                                {{ __('Best Sellers') }}</p>
                            <h4 class="fw-bold mb-0">{{ count($bestSellers) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ───── TABS ───── --}}
        <div class="d-flex flex-wrap gap-1 mb-4" id="reportTabs" role="tablist">
            <button class="rpt-tab active" data-target="daily-pane" role="tab"
                style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:2px solid #e2e8f0;border-radius:10px;background:#fff;color:#64748b;font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;">
                <i class="bi bi-calendar"></i> {{ __('Daily Sales') }}
            </button>
            <button class="rpt-tab" data-target="monthly-pane" role="tab"
                style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:2px solid #e2e8f0;border-radius:10px;background:#fff;color:#64748b;font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;">
                <i class="bi bi-bar-chart"></i> {{ __('Monthly Sales') }}
            </button>
            <button class="rpt-tab" data-target="revenue-pane" role="tab"
                style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:2px solid #e2e8f0;border-radius:10px;background:#fff;color:#64748b;font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;">
                <i class="bi bi-cash-stack"></i> {{ __('Revenue') }}
            </button>
            <button class="rpt-tab" data-target="customers-pane" role="tab"
                style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:2px solid #e2e8f0;border-radius:10px;background:#fff;color:#64748b;font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;">
                <i class="bi bi-people"></i> {{ __('Top Customers') }}
            </button>
            <button class="rpt-tab" data-target="sellers-pane" role="tab"
                style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:2px solid #e2e8f0;border-radius:10px;background:#fff;color:#64748b;font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;">
                <i class="bi bi-graph-up"></i> {{ __('Best Sellers') }}
            </button>
            <button class="rpt-tab" data-target="promotions-pane" role="tab"
                style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:2px solid #e2e8f0;border-radius:10px;background:#fff;color:#64748b;font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;">
                <i class="bi bi-percent"></i> {{ __('Promotions') }}
            </button>
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
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0"
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
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle flex-shrink-0"
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
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle flex-shrink-0"
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
                    <div class="card-body py-4 px-4" style="background:#ecfdf5;">
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
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0"
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
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle flex-shrink-0"
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
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle flex-shrink-0"
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
                    <div class="card-body py-4 px-4" style="background:#ecfdf5;">
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
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle flex-shrink-0"
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
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0"
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
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle flex-shrink-0"
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
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 bg-warning-subtle flex-shrink-0"
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
                            <div
                                class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <h5 class="fw-bold mb-0"><i
                                        class="bi bi-bar-chart text-success me-2"></i>{{ __('Revenue by Month') }}</h5>
                            </div>
                            <div class="card-body py-4 px-4" style="background:#ecfdf5;">
                                <div id="revMonthlyChart" class="w-100" style="height:220px;position:relative;"></div>
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
                            <div class="card-body py-4 px-4 d-flex align-items-center" style="background:#ecfdf5;">
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
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle flex-shrink-0"
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
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0"
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
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0"
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
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle flex-shrink-0"
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
                    <div class="card-body py-4 px-4" style="background:#ecfdf5;">
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

        </div>
    </div>
@endsection

@push('scripts')
    <script>
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

            function lineChartPath(values, width, height) {
                if (!values || values.length < 2) return {
                    area: '',
                    line: '',
                    points: []
                };
                var maxVal = Math.max.apply(null, values) || 1;
                var stepX = width / (values.length - 1);
                var points = values.map(function(v, i) {
                    var x = i * stepX;
                    var y = height - (v / maxVal) * (height * 0.82) - 8;
                    return {
                        x: x,
                        y: Math.max(y, 2),
                        value: v,
                        index: i
                    };
                });

                var d = 'M' + points[0].x + ',' + points[0].y;
                for (var i = 1; i < points.length; i++) {
                    var prev = points[i - 1],
                        cur = points[i];
                    var cx1 = prev.x + (cur.x - prev.x) / 2,
                        cy1 = prev.y;
                    var cx2 = cur.x - (cur.x - prev.x) / 2,
                        cy2 = cur.y;
                    d += ' C' + cx1 + ',' + cy1 + ' ' + cx2 + ',' + cy2 + ' ' + cur.x + ',' + cur.y;
                }
                var area = d + ' L' + points[points.length - 1].x + ',' + height + ' L' + points[0].x + ',' + height +
                    ' Z';
                return {
                    area: area,
                    line: d,
                    points: points
                };
            }

            function lc(id, items, vk, lk, color) {
                var el = document.getElementById(id);
                if (!el || !items || !items.length) return;
                var values = items.map(function(it) {
                    return it[vk] || 0;
                });
                var labels = items.map(function(it) {
                    return it[lk] != null ? String(it[lk]).substring(0, 8) : '';
                });
                var w = 600,
                    h = 200;
                var pathData = lineChartPath(values, w, h);
                var pts = pathData.points;

                var strokeColor = color === 'g' ? '#059669' : color === 'p' ? '#047857' : '#10b981';
                var gradId = 'lcGrad_' + id;

                var svg = '<svg viewBox="0 0 ' + w + ' ' + h +
                    '" preserveAspectRatio="none" style="width:100%;height:100%;display:block;position:absolute;top:0;left:0;" xmlns="http://www.w3.org/2000/svg">' +
                    '<defs><linearGradient id="' + gradId + '" x1="0" y1="0" x2="0" y2="1">' +
                    '<stop offset="0%" stop-color="' + strokeColor + '" stop-opacity="0.45"/>' +
                    '<stop offset="100%" stop-color="' + strokeColor + '" stop-opacity="0.04"/>' +
                    '</linearGradient></defs>' +
                    '<path d="' + pathData.area + '" fill="url(#' + gradId + ')"/>' +
                    '<path d="' + pathData.line + '" fill="none" stroke="' + strokeColor + '" stroke-width="2.5"/>' +
                    '<g id="dots_' + id + '">' +
                    pts.map(function(p) {
                        return '<circle cx="' + p.x + '" cy="' + p.y + '" r="4" fill="#fff" stroke="' +
                            strokeColor + '" stroke-width="2.5" class="lc-dot-' + id +
                            '" style="cursor:crosshair"/>';
                    }).join('') +
                    '</g>' +
                    '<rect width="' + w + '" height="' + h +
                    '" fill="transparent" style="cursor:crosshair;pointer-events:all;" class="lc-overlay-' + id +
                    '"/>' +
                    '</svg>';

                var tooltipId = 'lcTooltip_' + id;
                var labelsHtml = '<div class="d-flex justify-content-between small text-muted mt-1 px-1">' +
                    items.map(function(it, i) {
                        var show = (items.length <= 8 || i % Math.ceil(items.length / 6) === 0 || i === items
                            .length - 1);
                        return show ? '<span>' + labels[i] + '</span>' : '';
                    }).join('') +
                    '</div>';

                el.innerHTML = '<div style="position:relative;height:100%;">' +
                    '<div id="' + tooltipId +
                    '" style="display:none;position:absolute;background:#1f2937;color:#fff;padding:6px 12px;border-radius:8px;font-size:12px;font-weight:600;pointer-events:none;white-space:nowrap;z-index:10;transform:translate(-50%,-110%);box-shadow:0 4px 12px rgba(0,0,0,.2);"></div>' +
                    svg +
                    '</div>' +
                    labelsHtml;

                // Hover logic
                var container = el.querySelector('div[style*="position:relative"]') || el;
                var overlay = el.querySelector('.lc-overlay-' + id);
                var tooltip = document.getElementById(tooltipId);
                if (overlay && tooltip) {
                    overlay.addEventListener('mousemove', function(e) {
                        var rect = container.getBoundingClientRect();
                        var scaleX = w / rect.width;
                        var mx = (e.clientX - rect.left) * scaleX;
                        var closest = pts[0],
                            minDist = Math.abs(mx - pts[0].x);
                        for (var i = 1; i < pts.length; i++) {
                            var dist = Math.abs(mx - pts[i].x);
                            if (dist < minDist) {
                                minDist = dist;
                                closest = pts[i];
                            }
                        }
                        var label = labels[closest.index] || '';
                        tooltip.textContent = label + ': ' + (closest.value || 0).toLocaleString();

                        var elRect = el.getBoundingClientRect();
                        var tx = e.clientX - elRect.left;
                        var ty = (closest.y / h) * rect.height;
                        tooltip.style.display = 'block';
                        tooltip.style.left = tx + 'px';
                        tooltip.style.top = ty + 'px';

                        document.querySelectorAll('.lc-dot-' + id).forEach(function(dot) {
                            var cx = parseFloat(dot.getAttribute('cx'));
                            if (Math.abs(cx - closest.x) < 1) {
                                dot.setAttribute('r', '6');
                                dot.setAttribute('fill', strokeColor);
                            } else {
                                dot.setAttribute('r', '4');
                                dot.setAttribute('fill', '#fff');
                            }
                        });
                    });
                    overlay.addEventListener('mouseleave', function() {
                        tooltip.style.display = 'none';
                        document.querySelectorAll('.lc-dot-' + id).forEach(function(dot) {
                            dot.setAttribute('r', '4');
                            dot.setAttribute('fill', '#fff');
                        });
                    });
                }
            }

            function bc(id, items, vk, lk, cl, ev) {
                var el = document.getElementById(id);
                if (!el) return;
                var m = mx(items, vk),
                    e = ev || 3;
                var html = '<div style="position:absolute;left:0;right:0;top:0;bottom:32px;pointer-events:none;">';
                for (var g = 1; g <= 4; g++) {
                    var y = (g / 5) * 100;
                    html += '<div style="position:absolute;left:0;right:0;top:' + (100 - y) +
                        '%;border-bottom:1px dashed #d1fae5;"></div>';
                }
                html +=
                    '</div><div style="display:flex;align-items:flex-end;gap:4px;height:100%;position:relative;z-index:1;padding:0 4px;">';
                items.map(function(it, i) {
                    var h = it[vk] / m * 100;
                    var grad = cl === 'g' ? 'linear-gradient(180deg,#34d399,#059669)' : cl === 'p' ?
                        'linear-gradient(180deg,#059669,#047857)' : 'linear-gradient(180deg,#10b981,#059669)';
                    var shadow = cl === 'p' ? '0 2px 8px rgba(4,120,87,.3)' : '0 2px 8px rgba(16,185,129,.25)';
                    var lb = it[lk] != null ? String(it[lk]).substring(0, 14) : '';
                    var val = it[vk];
                    html +=
                        '<div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;height:100%;position:relative;">';
                    html += '<div title="' + lb + ': ' + val + '" style="width:70%;max-width:40px;height:' +
                        h + '%;border-radius:6px 6px 3px 3px;background:' + grad + ';box-shadow:' + shadow +
                        ';min-height:2px;transition:all .25s ease;cursor:pointer;position:relative;" onmouseover="this.style.opacity=\'.8\'" onmouseout="this.style.opacity=\'1\'">';
                    if (h > 15) html += '<span style="position:absolute;top:-18px;left:50%;transform:translateX(-50%);font-size:.65rem;font-weight:700;color:#059669;white-space:nowrap;">' + val + '</span>';
                    html += '</div>';
                    html +=
                        '<span style="position:absolute;bottom:-28px;left:50%;transform:translateX(-50%);font-size:.6rem;font-weight:600;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:64px;text-align:center;">' +
                        lb + '</span>';
                    html += '</div>';
                });
                html += '</div>';
                el.innerHTML = html;
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
                    'position:absolute;top:-10px;left:50%;transform:translateX(-50%);background:#1e293b;color:#fff;padding:8px 18px;border-radius:10px;font-size:.85rem;font-weight:700;pointer-events:none;opacity:0;transition:opacity .2s;z-index:99;white-space:nowrap;box-shadow:0 6px 20px rgba(0,0,0,.25);';
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
                        '" stroke="#fff" stroke-width="3" style="cursor:pointer;transition:transform .15s,opacity .15s;" onmouseover="var t=document.getElementById(\'dnttip\');t.style.opacity=\'1\';t.textContent=\'' +
                        s.category + ': ' + fm(s.value) + ' (' + (s.pct * 100).toFixed(1) +
                        '%)\';this.style.opacity=\'.85\';this.setAttribute(\'transform\',\'translate(-4,-4)\');" onmouseout="var t=document.getElementById(\'dnttip\');t.style.opacity=\'0\';this.style.opacity=\'1\';this.setAttribute(\'transform\',\'translate(0,0)\');"></path>';
                });
                svg += '<circle cx="' + cx + '" cy="' + cy + '" r="' + ir +
                    '" fill="#fff" stroke="#e2e8f0" stroke-width="2"/></svg>';
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

            function salesLineChart(id, items, lk) {
                var el = document.getElementById(id);
                if (!el) return;
                if (!items || !items.length) {
                    el.innerHTML = '<p class="text-muted text-center py-4 mb-0">No data</p>';
                    return;
                }
                var maxRev = items.reduce(function(m, d) { return Math.max(m, d.revenue || 0); }, 0) || 1;
                var maxOrd = items.reduce(function(m, d) { return Math.max(m, d.orders || 0); }, 0) || 1;
                var w = 700, h = 240, pad = { t: 20, r: 20, b: 40, l: 55 };
                var cw = w - pad.l - pad.r, ch = h - pad.t - pad.b;
                var stepX = cw / Math.max(items.length - 1, 1);
                var revPts = items.map(function(d, i) {
                    var x = pad.l + i * stepX;
                    var y = pad.t + ch - ((d.revenue || 0) / maxRev) * ch;
                    return { x: x, y: y, label: d[lk] || '', value: d.revenue || 0, index: i };
                });
                var ordPts = items.map(function(d, i) {
                    var x = pad.l + i * stepX;
                    var y = pad.t + ch - ((d.orders || 0) / maxOrd) * ch;
                    return { x: x, y: y, label: d[lk] || '', value: d.orders || 0, index: i };
                });
                function linePath(pts) {
                    return pts.map(function(p, i) { return (i === 0 ? 'M' : 'L') + p.x.toFixed(1) + ',' + p.y.toFixed(1); }).join(' ');
                }
                function areaPath(pts) {
                    var bottom = pad.t + ch;
                    return linePath(pts) + ' L' + pts[pts.length - 1].x.toFixed(1) + ',' + bottom + ' L' + pts[0].x.toFixed(1) + ',' + bottom + ' Z';
                }
                var grid = '';
                for (var g = 0; g <= 4; g++) {
                    var gy = pad.t + ch - (g / 4) * ch;
                    var gv = (g / 4) * maxRev;
                    grid += '<line x1="' + pad.l + '" y1="' + gy + '" x2="' + (w - pad.r) + '" y2="' + gy + '" stroke="#e5e7eb" stroke-width="1"/>';
                    grid += '<text x="' + (pad.l - 8) + '" y="' + (gy + 4) + '" text-anchor="end" fill="#9ca3af" font-size="11">$' + fm(gv) + '</text>';
                }
                var labelsHtml = '';
                var labelInterval = Math.max(1, Math.floor(items.length / 10));
                revPts.forEach(function(p, i) {
                    if (i % labelInterval === 0 || i === revPts.length - 1) {
                        labelsHtml += '<text x="' + p.x + '" y="' + (h - 8) + '" text-anchor="middle" fill="#9ca3af" font-size="10">' + p.label + '</text>';
                    }
                });
                var revDots = revPts.map(function(p) {
                    return '<circle cx="' + p.x + '" cy="' + p.y + '" r="4" fill="#fff" stroke="#059669" stroke-width="2.5" style="cursor:pointer;" data-idx="' + p.index + '" data-series="rev"/>';
                }).join('');
                var ordDots = ordPts.map(function(p) {
                    return '<circle cx="' + p.x + '" cy="' + p.y + '" r="4" fill="#fff" stroke="#f59e0b" stroke-width="2.5" style="cursor:pointer;" data-idx="' + p.index + '" data-series="ord"/>';
                }).join('');
                var svg = '<svg viewBox="0 0 ' + w + ' ' + h + '" style="width:100%;height:100%;display:block;position:absolute;top:0;left:0;">' +
                    '<defs><linearGradient id="revGrad" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#059669" stop-opacity="0.35"/><stop offset="100%" stop-color="#059669" stop-opacity="0.03"/></linearGradient><linearGradient id="ordGrad" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#f59e0b" stop-opacity="0.25"/><stop offset="100%" stop-color="#f59e0b" stop-opacity="0.02"/></linearGradient></defs>' +
                    grid +
                    '<path d="' + areaPath(revPts) + '" fill="url(#revGrad)"/>' +
                    '<path d="' + linePath(revPts) + '" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>' +
                    '<path d="' + areaPath(ordPts) + '" fill="url(#ordGrad)"/>' +
                    '<path d="' + linePath(ordPts) + '" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="6,3"/>' +
                    '<g>' + revDots + ordDots + '</g>' +
                    '<g>' + labelsHtml + '</g>' +
                    '</svg>';
                el.innerHTML = '<div style="position:relative;height:100%;width:100%;">' + svg + '</div>';
                var dots = el.querySelectorAll('circle');
                var tip = document.createElement('div');
                tip.id = id + '_tip';
                tip.style.cssText = 'display:none;position:absolute;background:#1f2937;color:#fff;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;pointer-events:none;white-space:nowrap;z-index:10;transform:translate(-50%,-120%);box-shadow:0 4px 14px rgba(0,0,0,.25);';
                el.querySelector('div').appendChild(tip);
                dots.forEach(function(dot) {
                    dot.addEventListener('mouseenter', function(e) {
                        var idx = parseInt(this.getAttribute('data-idx'));
                        if (isNaN(idx) || !revPts[idx]) return;
                        var series = this.getAttribute('data-series');
                        var p = series === 'rev' ? revPts[idx] : ordPts[idx];
                        var prefix = series === 'rev' ? 'Revenue: $' : 'Orders: ';
                        tip.textContent = p.label + ' — ' + prefix + (p.value || 0).toLocaleString();
                        tip.style.display = 'block';
                        var rect = el.getBoundingClientRect();
                        tip.style.left = (p.x / w * rect.width) + 'px';
                        tip.style.top = (p.y / h * rect.height) + 'px';
                        this.setAttribute('r', '6');
                        this.setAttribute('fill', series === 'rev' ? '#059669' : '#f59e0b');
                    });
                    dot.addEventListener('mouseleave', function() {
                        tip.style.display = 'none';
                        this.setAttribute('r', '4');
                        this.setAttribute('fill', '#fff');
                    });
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
                                svg += '<path d="' + d + '" fill="' + colors[i % colors.length] + '" stroke="#fff" stroke-width="3"/>';
                            });
                            svg += '<circle cx="' + cx + '" cy="' + cy + '" r="' + ir + '" fill="#fff" stroke="#e2e8f0" stroke-width="2"/>';
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
                            x.style.borderColor = '#e2e8f0';
                            x.style.background = '#fff';
                            x.style.color = '#64748b';
                        });
                        this.style.borderColor = '#059669';
                        this.style.background = '#ecfdf5';
                        this.style.color = '#047857';
                        Object.keys(panes).forEach(function(k) {
                            panes[k].style.display = 'none';
                            panes[k].classList.remove('show', 'active');
                        });
                        target.style.display = 'block';
                        target.classList.add('show', 'active');
                        var loader = loaders[id.replace(/-/g, '')];
                        if (loader) setTimeout(loader, 50);
                    });
                });
                // Set initial active tab
                if (tabs.length) {
                    tabs[0].click();
                }
                window.loadDailySales();
                window.loadMonthlySales();
                window.loadRevenue();
            });
        })();
    </script>
@endpush
