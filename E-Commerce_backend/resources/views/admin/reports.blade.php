@extends('layouts.admin')

@section('title', __('Reports'))

@section('content')
<div class="container-fluid p-0">

    {{-- ───── PAGE HEADER ───── --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">{{ __('Reports & Analytics') }}</h1>
        </div>
    </div>

    {{-- ───── SUMMARY STATS ───── --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-4 py-4 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle flex-shrink-0" style="width: 56px; height: 56px;">
                        <i class="bi bi-currency-dollar text-success fs-3"></i>
                    </div>
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.04em;">{{ __('Total Revenue') }}</p>
                        <h4 class="fw-bold mb-0">${{ number_format($totalRevenue, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-4 py-4 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle flex-shrink-0" style="width: 56px; height: 56px;">
                        <i class="bi bi-receipt text-primary fs-3"></i>
                    </div>
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.04em;">{{ __('Total Orders') }}</p>
                        <h4 class="fw-bold mb-0">{{ number_format($totalSales) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-4 py-4 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-warning-subtle flex-shrink-0" style="width: 56px; height: 56px;">
                        <i class="bi bi-people text-warning fs-3"></i>
                    </div>
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.04em;">{{ __('Top Customers') }}</p>
                        <h4 class="fw-bold mb-0">{{ count($topCustomers) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-4 py-4 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0" style="width: 56px; height: 56px;">
                        <i class="bi bi-box-seam text-info fs-3"></i>
                    </div>
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.04em;">{{ __('Best Sellers') }}</p>
                        <h4 class="fw-bold mb-0">{{ count($bestSellers) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ───── TABS ───── --}}
    <div class="d-flex flex-wrap gap-1 mb-4" id="reportTabs" role="tablist">
        <button class="rpt-tab active" data-target="daily-pane" role="tab" style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:2px solid #e2e8f0;border-radius:10px;background:#fff;color:#64748b;font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;">
            <i class="bi bi-calendar"></i> {{ __('Daily Sales') }}
        </button>
        <button class="rpt-tab" data-target="monthly-pane" role="tab" style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:2px solid #e2e8f0;border-radius:10px;background:#fff;color:#64748b;font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;">
            <i class="bi bi-bar-chart"></i> {{ __('Monthly Sales') }}
        </button>
        <button class="rpt-tab" data-target="revenue-pane" role="tab" style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:2px solid #e2e8f0;border-radius:10px;background:#fff;color:#64748b;font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;">
            <i class="bi bi-cash-stack"></i> {{ __('Revenue') }}
        </button>
        <button class="rpt-tab" data-target="customers-pane" role="tab" style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:2px solid #e2e8f0;border-radius:10px;background:#fff;color:#64748b;font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;">
            <i class="bi bi-people"></i> {{ __('Top Customers') }}
        </button>
        <button class="rpt-tab" data-target="sellers-pane" role="tab" style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:2px solid #e2e8f0;border-radius:10px;background:#fff;color:#64748b;font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;">
            <i class="bi bi-graph-up"></i> {{ __('Best Sellers') }}
        </button>
    </div>

    <div class="tab-content">

        {{-- ════════════ DAILY SALES ════════════ --}}
        <div class="tab-pane show active" id="daily-pane" role="tabpanel">
            <div class="d-flex flex-wrap align-items-center gap-2 bg-white border rounded-3 p-3 mb-4 shadow-sm">
                <label class="fw-semibold small text-uppercase mb-0">{{ __('Date') }}:</label>
                <input type="date" class="form-control form-control-sm" style="max-width: 180px;" id="dailyDate" value="{{ $today->format('Y-m-d') }}">
                <button class="btn btn-sm btn-success" onclick="loadDailySales()">{{ __('Apply') }}</button>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                                <i class="bi bi-cart text-info fs-5"></i>
                            </div>
                            <div>
                                <p class="text-muted small mb-0">{{ __('Total Sales') }}</p>
                                <h5 class="fw-bold mb-0" id="dailyTotalSales">{{ number_format($dailyTotalSales) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                                <i class="bi bi-currency-dollar text-success fs-5"></i>
                            </div>
                            <div>
                                <p class="text-muted small mb-0">{{ __('Total Revenue') }}</p>
                                <h5 class="fw-bold mb-0" id="dailyTotalRevenue">${{ number_format($dailyTotalRevenue, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle flex-shrink-0" style="width: 48px; height: 48px;">
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
                <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-success me-2"></i>{{ __('Hourly Sales Distribution') }}</h5>
                    <span class="badge bg-success-subtle text-success rounded-pill small fw-bold" id="dailyChartMeta">{{ $today->format('M d, Y') }}</span>
                </div>
                <div class="card-body py-4 px-4" style="background:#ecfdf5;">
                    <div id="dailyHourlyChart" class="w-100" style="height:220px;position:relative;"></div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 rounded-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-pie-chart text-success me-2"></i>{{ __('Status Breakdown') }}</h5>
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
                <input type="month" class="form-control form-control-sm" style="max-width: 200px;" id="monthlyMonth" value="{{ $currentMonth->format('Y-m') }}">
                <button class="btn btn-sm btn-success" onclick="loadMonthlySales()">{{ __('Apply') }}</button>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                                <i class="bi bi-cart text-info fs-5"></i>
                            </div>
                            <div>
                                <p class="text-muted small mb-0">{{ __('Total Sales') }} <span id="monthlySalesGrowth" class="small fw-bold text-success" style="display:none;"></span></p>
                                <h5 class="fw-bold mb-0" id="monthlyTotalSales">{{ number_format($monthlyTotalSales) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                                <i class="bi bi-currency-dollar text-success fs-5"></i>
                            </div>
                            <div>
                                <p class="text-muted small mb-0">{{ __('Total Revenue') }} <span id="monthlyRevenueGrowth" class="small fw-bold text-success" style="display:none;"></span></p>
                                <h5 class="fw-bold mb-0" id="monthlyTotalRevenue">${{ number_format($monthlyTotalRevenue, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle flex-shrink-0" style="width: 48px; height: 48px;">
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
                <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h5 class="fw-bold mb-0"><i class="bi bi-graph-up text-success me-2"></i>{{ __('Daily Trends') }}</h5>
                    <span class="badge bg-success-subtle text-success rounded-pill small fw-bold" id="monthlyChartMeta">{{ $currentMonth->format('F Y') }}</span>
                </div>
                <div class="card-body py-4 px-4" style="background:#ecfdf5;">
                    <div id="monthlyDailyChart" class="w-100" style="height:200px;position:relative;"></div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 rounded-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-pie-chart text-success me-2"></i>{{ __('Status Breakdown') }}</h5>
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
                <input type="date" class="form-control form-control-sm" style="max-width: 160px;" id="revFrom" value="{{ $threeMonthsAgo->format('Y-m-d') }}">
                <label class="fw-semibold small text-uppercase mb-0 ms-2">{{ __('To') }}:</label>
                <input type="date" class="form-control form-control-sm" style="max-width: 160px;" id="revTo" value="{{ $now->format('Y-m-d') }}">
                <button class="btn btn-sm btn-success" onclick="loadRevenue()">{{ __('Apply') }}</button>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                                <i class="bi bi-currency-dollar text-success fs-5"></i>
                            </div>
                            <div>
                                <p class="text-muted small mb-0">{{ __('Total Revenue') }}</p>
                                <h5 class="fw-bold mb-0" id="revTotalRevenue">${{ number_format($totalRevenue, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0" style="width: 48px; height: 48px;">
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
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle flex-shrink-0" style="width: 48px; height: 48px;">
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
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-warning-subtle flex-shrink-0" style="width: 48px; height: 48px;">
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
                        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart text-success me-2"></i>{{ __('Revenue by Month') }}</h5>
                        </div>
                        <div class="card-body py-4 px-4" style="background:#ecfdf5;">
                            <div id="revMonthlyChart" class="w-100" style="height:220px;position:relative;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <h5 class="fw-bold mb-0"><i class="bi bi-pie-chart text-success me-2"></i>{{ __('Revenue by Category') }}</h5>
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
                <input type="date" class="form-control form-control-sm" style="max-width: 160px;" id="custFrom" value="{{ $threeMonthsAgo->format('Y-m-d') }}">
                <label class="fw-semibold small text-uppercase mb-0 ms-2">{{ __('To') }}:</label>
                <input type="date" class="form-control form-control-sm" style="max-width: 160px;" id="custTo" value="{{ $now->format('Y-m-d') }}">
                <label class="fw-semibold small text-uppercase mb-0 ms-2">{{ __('Limit') }}:</label>
                <input type="number" class="form-control form-control-sm" style="max-width: 70px;" id="custLimit" value="10" min="1" max="50">
                <button class="btn btn-sm btn-success" onclick="loadTopCustomers()">{{ __('Apply') }}</button>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                                <i class="bi bi-currency-dollar text-success fs-5"></i>
                            </div>
                            <div>
                                <p class="text-muted small mb-0">{{ __('Customer Revenue') }}</p>
                                <h5 class="fw-bold mb-0" id="custTotalRevenue">${{ number_format($totalRevenue, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0" style="width: 48px; height: 48px;">
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
                    <h5 class="fw-bold mb-0"><i class="bi bi-trophy text-success me-2"></i>{{ __('Top Customers') }}</h5>
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
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Total Spent') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Avg Order') }}</th>
                                </tr>
                            </thead>
                            <tbody id="custTable">
                                @forelse ($topCustomers as $i => $customer)
                                    <tr>
                                        <td class="px-4 py-3 fw-bold text-muted">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white fw-bold flex-shrink-0"
                                                    style="width: 32px; height: 32px; font-size: 13px; background: #059669;">
                                                    {{ strtoupper(substr($customer['name'] ?? '?', 0, 1)) }}
                                                </span>
                                                <span class="fw-semibold">{{ $customer['name'] }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-muted">{{ $customer['email'] }}</td>
                                        <td class="px-4 py-3"><span class="badge bg-light text-dark rounded-pill">{{ $customer['order_count'] }}</span></td>
                                        <td class="px-4 py-3 fw-bold text-end">${{ number_format($customer['total_spent'], 2) }}</td>
                                        <td class="px-4 py-3 fw-bold text-end">${{ number_format($customer['average_order'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">{{ __('No customer data found.') }}</td></tr>
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
                <input type="date" class="form-control form-control-sm" style="max-width: 160px;" id="sellFrom" value="{{ $threeMonthsAgo->format('Y-m-d') }}">
                <label class="fw-semibold small text-uppercase mb-0 ms-2">{{ __('To') }}:</label>
                <input type="date" class="form-control form-control-sm" style="max-width: 160px;" id="sellTo" value="{{ $now->format('Y-m-d') }}">
                <label class="fw-semibold small text-uppercase mb-0 ms-2">{{ __('Limit') }}:</label>
                <input type="number" class="form-control form-control-sm" style="max-width: 70px;" id="sellLimit" value="10" min="1" max="50">
                <button class="btn btn-sm btn-success" onclick="loadBestSellers()">{{ __('Apply') }}</button>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0" style="width: 48px; height: 48px;">
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
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle flex-shrink-0" style="width: 48px; height: 48px;">
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
                <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h5 class="fw-bold mb-0"><i class="bi bi-graph-up text-success me-2"></i>{{ __('Best Sellers Chart') }}</h5>
                </div>
                <div class="card-body py-4 px-4" style="background:#ecfdf5;">
                    <div id="sellersBarChart" class="w-100" style="height:220px;position:relative;"></div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 rounded-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-table text-success me-2"></i>{{ __('Product Details') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3 small fw-bold text-uppercase">#</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Product') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Category') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Price') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Units Sold') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Revenue') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Stock') }}</th>
                                </tr>
                            </thead>
                            <tbody id="sellersTable">
                                @forelse ($bestSellers as $i => $product)
                                    <tr>
                                        <td class="px-4 py-3 fw-bold text-muted">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3 fw-semibold">{{ $product['name'] }}</td>
                                        <td class="px-4 py-3 text-muted">{{ $product['category'] ?? '-' }}</td>
                                        <td class="px-4 py-3 fw-bold text-end">${{ number_format($product['price'], 2) }}</td>
                                        <td class="px-4 py-3 text-center"><span class="badge bg-light text-dark rounded-pill">{{ $product['units_sold'] }}</span></td>
                                        <td class="px-4 py-3 fw-bold text-end">${{ number_format($product['total_revenue'], 2) }}</td>
                                        <td class="px-4 py-3 text-center">{{ $product['stock'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">{{ __('No product data found.') }}</td></tr>
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

@push('scripts')
<script>
(function(){
    var R = {
        dailySales: @json(route('admin.reports.daily-sales')),
        monthlySales: @json(route('admin.reports.monthly-sales')),
        revenue: @json(route('admin.reports.revenue')),
        topCustomers: @json(route('admin.reports.top-customers')),
        bestSellers: @json(route('admin.reports.best-sellers')),
    };

    var sc = {Pending:'#f59e0b',Processing:'#3b82f6',Shipped:'#6366f1',Delivered:'#22c55e',Cancelled:'#ef4444'};
    var cc = ['#059669','#3b82f6','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6','#f97316'];

    function fm(v){return '$'+Number(v||0).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});}
    function mx(a,k){return a&&a.length?Math.max.apply(null,a.map(function(i){return i[k]||0;}))||1:1;}
    function fj(u,p){
        var q=Object.keys(p).filter(function(k){return p[k]!==''&&p[k]!=null;}).map(function(k){return encodeURIComponent(k)+'='+encodeURIComponent(p[k]);}).join('&');
        return fetch(u+(q?'?'+q:'')).then(function(r){return r.json();});
    }

    function bc(id,items,vk,lk,cl,ev){
        var el=document.getElementById(id);if(!el)return;
        var m=mx(items,vk),e=ev||3;
        var html='<div style="position:absolute;left:0;right:0;top:0;bottom:28px;pointer-events:none;">';
        for(var g=1;g<=4;g++){var y=(g/5)*100;html+='<div style="position:absolute;left:0;right:0;top:'+(100-y)+'%;border-bottom:1px dashed #d1fae5;"></div>';}
        html+='</div><div style="display:flex;align-items:flex-end;gap:3px;height:100%;position:relative;z-index:1;">';
        items.map(function(it,i){
            var h=it[vk]/m*100;
            var grad=cl==='g'?'linear-gradient(180deg,#34d399,#059669)':cl==='p'?'linear-gradient(180deg,#059669,#047857)':'linear-gradient(180deg,#10b981,#059669)';
            var shadow=cl==='g'?'0 2px 8px rgba(5,150,105,.25)':cl==='p'?'0 2px 8px rgba(4,120,87,.3)':'0 2px 8px rgba(16,185,129,.25)';
            var lb=it[lk]!=null?String(it[lk]).substring(0,8):'';
            html+='<div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;height:100%;position:relative;">';
            html+='<div title="'+lb+': '+it[vk]+'" style="width:70%;max-width:36px;height:'+h+'%;border-radius:5px 5px 2px 2px;background:'+grad+';box-shadow:'+shadow+';min-height:2px;transition:all .25s ease;cursor:pointer;" onmouseover="this.style.opacity=\'.8\'" onmouseout="this.style.opacity=\'1\'"></div>';
            if(i%e===0)html+='<span style="position:absolute;bottom:-24px;left:50%;transform:translateX(-50%);font-size:.6rem;font-weight:600;color:var(--bs-secondary-color);white-space:nowrap;">'+lb+'</span>';
            html+='</div>';
        });
        html+='</div>';
        el.innerHTML=html;
    }

    function sg(id,items){
        var el=document.getElementById(id);if(!el)return;
        if(!items||!items.length){el.innerHTML='<div class="col-12 text-muted text-center py-3">No data</div>';return;}
        el.innerHTML=items.map(function(s){
            var c=sc[s.status]||'#9ca3af';
            return '<div class="col-sm-6 col-lg"><div class="d-flex align-items-center gap-3 py-3 px-3 border rounded-3 bg-white shadow-sm" style="border-left:3px solid '+c+'!important;"><span class="fw-semibold small flex-grow-1">'+(s.status||'')+'</span><span class="badge bg-light text-dark rounded-pill px-3">'+(s.count||0)+'</span><span class="fw-bold small" style="color:'+c+';">'+fm(s.revenue)+'</span></div></div>';
        }).join('');
    }

    function ct(items){
        var el=document.getElementById('custTable');if(!el)return;
        if(!items||!items.length){el.innerHTML='<tr><td colspan="6" class="text-center text-muted py-4">No customer data found.</td></tr>';return;}
        el.innerHTML=items.map(function(c,i){
            var inl=(c.name||'?')[0].toUpperCase();
            return '<tr><td class="px-4 py-3 fw-bold text-muted">'+(i+1)+'</td><td class="px-4 py-3"><div class="d-flex align-items-center gap-2"><span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white fw-bold flex-shrink-0" style="width:32px;height:32px;font-size:13px;background:#059669;">'+inl+'</span><span class="fw-semibold">'+(c.name||'')+'</span></div></td><td class="px-4 py-3 text-muted">'+(c.email||'-')+'</td><td class="px-4 py-3"><span class="badge bg-light text-dark rounded-pill">'+(c.order_count||0)+'</span></td><td class="px-4 py-3 fw-bold text-end">'+fm(c.total_spent)+'</td><td class="px-4 py-3 fw-bold text-end">'+fm(c.average_order)+'</td></tr>';
        }).join('');
    }

    function st(items){
        var el=document.getElementById('sellersTable');if(!el)return;
        if(!items||!items.length){el.innerHTML='<tr><td colspan="7" class="text-center text-muted py-4">No product data found.</td></tr>';return;}
        el.innerHTML=items.map(function(p,i){
            return '<tr><td class="px-4 py-3 fw-bold text-muted">'+(i+1)+'</td><td class="px-4 py-3 fw-semibold">'+(p.name||'')+'</td><td class="px-4 py-3 text-muted">'+(p.category||'-')+'</td><td class="px-4 py-3 fw-bold text-end">'+fm(p.price)+'</td><td class="px-4 py-3 text-center"><span class="badge bg-light text-dark rounded-pill">'+(p.units_sold||0)+'</span></td><td class="px-4 py-3 fw-bold text-end">'+fm(p.total_revenue)+'</td><td class="px-4 py-3 text-center">'+(p.stock||0)+'</td></tr>';
        }).join('');
    }

    function cb(items){
        var el=document.getElementById('revCategoryBars');if(!el)return;
        if(!items||!items.length){el.innerHTML='<p class="text-muted text-center py-4 mb-0">No category data found.</p>';return;}
        var total=items.reduce(function(s,c){return s+(c.total||0);},0);
        var cx=130,cy=130,or=100,ir=58;
        function pt(a,r){return (cx+r*Math.cos(a-0.5*Math.PI)).toFixed(1)+','+(cy+r*Math.sin(a-0.5*Math.PI)).toFixed(1);}
        function arcPath(s,e){
            var o1=pt(s,or),o2=pt(e,or),i1=pt(e,ir),i2=pt(s,ir);
            var la=(e-s>Math.PI)?1:0;
            return 'M'+o1+' A'+or+','+or+' 0 '+la+',1 '+o2+' L'+i1+' A'+ir+','+ir+' 0 '+la+',0 '+i2+' Z';
        }
        var slices=[],angle=0,tt=document.createElement('div');
        tt.id='dnttip';tt.style.cssText='position:absolute;top:-10px;left:50%;transform:translateX(-50%);background:#1e293b;color:#fff;padding:8px 18px;border-radius:10px;font-size:.85rem;font-weight:700;pointer-events:none;opacity:0;transition:opacity .2s;z-index:99;white-space:nowrap;box-shadow:0 6px 20px rgba(0,0,0,.25);';
        items.forEach(function(c,i){
            var pct=c.total/total,sliceAngle=pct*2*Math.PI;
            var path=arcPath(angle,angle+sliceAngle),co=cc[i%cc.length];
            slices.push({path:path,color:co,category:c.category,value:c.total,pct:pct,idx:i});
            angle+=sliceAngle;
        });
        var svg='<svg viewBox="0 0 260 260" width="100%" style="max-width:250px;height:auto;">';
        slices.forEach(function(s){
            svg+='<path d="'+s.path+'" fill="'+s.color+'" stroke="#fff" stroke-width="3" style="cursor:pointer;transition:transform .15s,opacity .15s;" onmouseover="var t=document.getElementById(\'dnttip\');t.style.opacity=\'1\';t.textContent=\''+s.category+': '+fm(s.value)+' ('+(s.pct*100).toFixed(1)+'%)\';this.style.opacity=\'.85\';this.setAttribute(\'transform\',\'translate(-4,-4)\');" onmouseout="var t=document.getElementById(\'dnttip\');t.style.opacity=\'0\';this.style.opacity=\'1\';this.setAttribute(\'transform\',\'translate(0,0)\');"></path>';
        });
        svg+='<circle cx="'+cx+'" cy="'+cy+'" r="'+ir+'" fill="#fff" stroke="#e2e8f0" stroke-width="2"/></svg>';
        var html='<div style="position:relative;display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:24px;padding:4px 0;">'+'<div style="position:relative;flex-shrink:0;">'+svg+tt.outerHTML+'</div><div style="display:grid;gap:10px;">';
        slices.forEach(function(s){
            var pct=(s.pct*100).toFixed(1);
            html+='<div style="display:flex;align-items:center;gap:10px;min-width:180px;" onmouseover="this.querySelector(\'span\').style.opacity=\'1\'" onmouseout="this.querySelector(\'span\').style.opacity=\'.7\'"><span style="width:16px;height:16px;border-radius:4px;background:'+s.color+';flex-shrink:0;opacity:.7;transition:opacity .15s;"></span><span style="font-size:.85rem;font-weight:600;color:var(--bs-body-color);flex:1;">'+(s.category||'')+'</span><span style="font-size:.85rem;font-weight:700;color:var(--bs-body-color);">'+fm(s.value)+'</span><span style="font-size:.75rem;font-weight:600;color:var(--bs-secondary-color);">('+pct+'%)</span></div>';
        });
        html+='</div></div>';
        el.innerHTML=html;
    }

    window.loadDailySales=function(){
        var d=document.getElementById('dailyDate').value||'';
        fj(R.dailySales,{date:d}).then(function(r){
            document.getElementById('dailyTotalSales').textContent=(r.total_sales||0).toLocaleString();
            document.getElementById('dailyTotalRevenue').textContent=fm(r.total_revenue);
            document.getElementById('dailyAvgOrder').textContent=fm(r.average_order);
            var meta=document.getElementById('dailyChartMeta');
            if(meta&&r.date)meta.textContent=new Date(r.date+'T12:00:00').toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});
            bc('dailyHourlyChart',r.hourly_distribution||[],'count','hour','c',3);
            sg('dailyStatus',r.status_breakdown||[]);
        }).catch(function(){});
    };
    window.loadMonthlySales=function(){
        var m=document.getElementById('monthlyMonth').value||'';
        fj(R.monthlySales,{month:m}).then(function(r){
            document.getElementById('monthlyTotalSales').textContent=(r.total_sales||0).toLocaleString();
            document.getElementById('monthlyTotalRevenue').textContent=fm(r.total_revenue);
            document.getElementById('monthlyAvgOrder').textContent=fm(r.average_order);
            var meta=document.getElementById('monthlyChartMeta');
            if(meta&&r.month)meta.textContent=new Date(r.month+'-01T12:00:00').toLocaleDateString('en-US',{month:'long',year:'numeric'});
            var sv=document.getElementById('monthlySalesGrowth'),rv=document.getElementById('monthlyRevenueGrowth');
            var s=r.sales_growth||0,v=r.revenue_growth||0;
            if(sv){sv.style.display='inline';sv.className='small fw-bold '+(s>=0?'text-success':'text-danger');sv.innerHTML=(s>=0?'&#8593; ':'&#8595; ')+Math.abs(s).toFixed(2)+'%';}
            if(rv){rv.style.display='inline';rv.className='small fw-bold '+(v>=0?'text-success':'text-danger');rv.innerHTML=(v>=0?'&#8593; ':'&#8595; ')+Math.abs(v).toFixed(2)+'%';}
            bc('monthlyDailyChart',r.daily_distribution||[],'total','date','g',4);
            sg('monthlyStatus',r.status_breakdown||[]);
        }).catch(function(){});
    };
    window.loadRevenue=function(){
        var f=document.getElementById('revFrom').value||'',t=document.getElementById('revTo').value||'';
        fj(R.revenue,{from:f,to:t}).then(function(r){
            document.getElementById('revTotalRevenue').textContent=fm(r.total_revenue);
            document.getElementById('revTotalSales').textContent=(r.total_sales||0).toLocaleString();
            document.getElementById('revCompleted').textContent=fm(r.completed_revenue);
            document.getElementById('revAvgOrder').textContent=fm(r.average_order);
            bc('revMonthlyChart',r.monthly_breakdown||[],'total','month','g',1);
            cb(r.revenue_by_category||[]);
        }).catch(function(){});
    };
    window.loadTopCustomers=function(){
        var f=document.getElementById('custFrom').value||'',t=document.getElementById('custTo').value||'',l=document.getElementById('custLimit').value||10;
        fj(R.topCustomers,{from:f,to:t,limit:l}).then(function(r){
            document.getElementById('custTotalRevenue').textContent=fm(r.total_customer_revenue);
            document.getElementById('custCount').textContent=(r.top_customers||[]).length;
            ct(r.top_customers||[]);
        }).catch(function(){});
    };
    window.loadBestSellers=function(){
        var f=document.getElementById('sellFrom').value||'',t=document.getElementById('sellTo').value||'',l=document.getElementById('sellLimit').value||10;
        fj(R.bestSellers,{from:f,to:t,limit:l}).then(function(r){
            document.getElementById('sellTotalUnits').textContent=(r.total_units_sold||0).toLocaleString();
            document.getElementById('sellCount').textContent=(r.best_sellers||[]).length;
            bc('sellersBarChart',r.best_sellers||[],'units_sold','name','p',1);
            st(r.best_sellers||[]);
        }).catch(function(){});
    };

    document.addEventListener('DOMContentLoaded',function(){
        // Tab switching with auto-load
        var tabs=document.querySelectorAll('.rpt-tab');
        var panes={};
        document.querySelectorAll('.tab-pane').forEach(function(p){panes[p.id]=p;});
        var loaders={dailyPane:function(){window.loadDailySales();},monthlyPane:function(){window.loadMonthlySales();},revenuePane:function(){window.loadRevenue();},customersPane:function(){window.loadTopCustomers();},sellersPane:function(){window.loadBestSellers();}};
        tabs.forEach(function(t){
            t.addEventListener('click',function(){
                var id=this.getAttribute('data-target');
                var target=panes[id];
                if(!target)return;
                tabs.forEach(function(x){
                    x.style.borderColor='#e2e8f0';x.style.background='#fff';x.style.color='#64748b';
                });
                this.style.borderColor='#059669';this.style.background='#ecfdf5';this.style.color='#047857';
                Object.keys(panes).forEach(function(k){panes[k].style.display='none';panes[k].classList.remove('show','active');});
                target.style.display='block';target.classList.add('show','active');
                var loader=loaders[id.replace(/-/g,'')];
                if(loader)setTimeout(loader,50);
            });
        });
        // Set initial active tab
        if(tabs.length){tabs[0].click();}
        window.loadDailySales();
        window.loadMonthlySales();
        window.loadRevenue();
    });
})();
</script>
@endpush
