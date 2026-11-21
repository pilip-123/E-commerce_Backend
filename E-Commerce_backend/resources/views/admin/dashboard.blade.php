@extends('layouts.admin')

@section('title', __('Admin Dashboard'))

@push('styles')
<style>
    html[data-theme="light"] .dashboard-date {
        color: #dc3545;
    }

    html[data-theme="dark"] .dashboard-date {
        color: #ecfdf5;
    }

    /* ── Live summary chart ── */
    .live-pill { display:inline-flex; align-items:center; gap:6px; padding:3px 10px; border-radius:99px; font-size:11.5px; font-weight:650; color:var(--badge-green-text); background:var(--badge-green-bg); }
    .live-pill .live-dot { width:7px; height:7px; border-radius:50%; background:#22c55e; box-shadow:0 0 0 0 rgba(34,197,94,.6); animation:live-ping 1.8s ease-out infinite; }
    .live-pill.is-paused, .live-pill.is-offline { color:var(--badge-gray-text); background:var(--badge-gray-bg); }
    .live-pill.is-paused .live-dot { background:#94a3b8; animation:none; }
    .live-pill.is-offline .live-dot { background:#ef4444; animation:none; }
    @keyframes live-ping { 0% { box-shadow:0 0 0 0 rgba(34,197,94,.55); } 80%,100% { box-shadow:0 0 0 7px rgba(34,197,94,0); } }
    .live-range .btn { font-size:12px; font-weight:650; color:var(--admin-text-muted); border-color:var(--admin-border); }
    .live-range .btn.active { color:#fff; background:#4f46e5; border-color:#4f46e5; }
    .live-legend { font-size:12.5px; color:var(--admin-text-muted); }
    .live-legend b { color:var(--admin-text); font-variant-numeric:tabular-nums; }
    .live-legend .swatch { display:inline-block; width:10px; height:10px; margin-right:6px; border-radius:3px; vertical-align:-1px; }
    .live-new { padding:3px 10px; border-radius:99px; font-size:11.5px; font-weight:700; color:#fff; background:linear-gradient(90deg,#6366f1,#8b5cf6); box-shadow:0 6px 16px rgba(99,102,241,.35); animation:live-pop .35s cubic-bezier(.2,.9,.3,1.4); }
    @keyframes live-pop { from { transform:scale(.6); opacity:0; } to { transform:scale(1); opacity:1; } }
    .live-chart { position:relative; min-height:240px; }
    .live-chart svg { display:block; width:100%; height:240px; overflow:visible; }
    .live-chart .live-line { stroke-dasharray:3000; stroke-dashoffset:3000; animation:live-draw 1.2s ease-out forwards; }
    .live-chart .live-area { opacity:0; animation:live-fade .8s ease-out .3s forwards; }
    .live-chart .live-bar { transform-box:fill-box; transform-origin:50% 100%; animation:live-grow .6s cubic-bezier(.2,.8,.2,1) both; animation-delay:calc(var(--i,0) * 15ms); }
    .live-chart .live-now { animation:live-now 1.8s ease-out infinite; transform-box:fill-box; transform-origin:center; }
    .live-chart .live-cross, .live-chart .live-hover-dot { opacity:0; transition:opacity .12s ease; pointer-events:none; }
    .live-chart.is-hovering .live-cross, .live-chart.is-hovering .live-hover-dot { opacity:1; }
    @keyframes live-draw { to { stroke-dashoffset:0; } }
    @keyframes live-fade { to { opacity:1; } }
    @keyframes live-grow { from { transform:scaleY(0); } to { transform:scaleY(1); } }
    @keyframes live-now { 0% { r:5; opacity:.7; } 100% { r:16; opacity:0; } }
    .live-tooltip { position:absolute; display:none; z-index:10; min-width:170px; padding:10px 12px; border-radius:12px; pointer-events:none; color:#fff; font-size:12px; background:rgba(17,24,39,.94); box-shadow:0 12px 28px rgba(0,0,0,.25); }
    .live-tooltip .tt-title { margin-bottom:6px; font-weight:750; }
    .live-tooltip .tt-row { display:flex; justify-content:space-between; gap:16px; line-height:1.7; }
    .live-tooltip .tt-row i { display:inline-block; width:8px; height:8px; margin-right:6px; border-radius:2px; }
    .live-tooltip .tt-foot { margin-top:6px; padding-top:6px; border-top:1px solid rgba(255,255,255,.15); color:#cbd5e1; }
    .live-empty { position:absolute; inset:0; display:grid; place-items:center; color:var(--admin-text-muted); font-size:13px; }
    @media (prefers-reduced-motion:reduce) { .live-chart .live-line, .live-chart .live-area, .live-chart .live-bar { animation:none; stroke-dashoffset:0; opacity:1; } .live-chart .live-now, .live-pill .live-dot { animation:none; } }
</style>
@endpush

@section('content')
    @include('admin.partials.kpi-styles')
    <div class="container-fluid p-0">


        {{-- TOP ACTION BAR --}}
        <div
            class="d-flex flex-wrap align-items-center justify-content-between bg-white border rounded-3 p-3 mb-4 shadow-sm">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('admin.products.create') }}"
                    class="btn btn-sm d-flex align-items-center gap-2 fw-semibold text-white border-0"
                    style="background: #4f46e5;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    {{ __('Add Product') }}
                </a>
                <form action="{{ route('admin.products.index') }}" method="GET" class="d-inline">
                    <div class="input-group input-group-sm" style="max-width: 320px;">
                        <span class="input-group-text bg-light border-0 text-muted">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.35-4.35" />
                            </svg>
                        </span>
                        <input type="text" name="search" class="form-control border-0 bg-light"
                            placeholder="{{ __('Search products...') }}" style="font-size: 13px;" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-sm fw-semibold text-white border-0"
                            style="background: #4f46e5;">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            <div class="d-flex align-items-center gap-2">
                <div class="d-flex align-items-center gap-2 ms-2">
                    <span class="dashboard-date font-bold">{{ now()->format('F , j , Y') }}</span>
                </div>
                <div class="dropdown">
                    <button class="btn btn-light btn-sm position-relative border" title="{{ __('Notifications') }}"
                        data-bs-toggle="dropdown" aria-expanded="false" id="notifBell">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                        </svg>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size: 9px; display: none;" id="notifBadge">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-0"
                        style="width: 360px; max-height: 420px; overflow-y: auto;" id="notifDropdown">
                        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                            <strong style="font-size: 14px;">{{ __('Notifications') }}</strong>
                            <button class="btn btn-sm btn-link text-decoration-none p-0" id="markAllRead"
                                style="font-size: 12px;">{{ __('Mark all as read') }}</button>
                        </div>
                        <div id="notifList">
                            <div class="text-center text-muted py-4" style="font-size: 13px;">{{ __('Loading...') }}</div>
                        </div>
                    </div>
                </div>
                {{-- <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle border d-flex align-items-center gap-1"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span>&#127482;&#127480;</span> EN
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                        <li><a class="dropdown-item active" href="#"><span class="me-2">&#127482;&#127480;</span>
                                English</a></li>
                        <li><a class="dropdown-item" href="#"><span class="me-2">&#127479;&#127482;</span>
                                Arabic</a></li>
                        <li><a class="dropdown-item" href="#"><span class="me-2">&#127467;&#127479;</span>
                                French</a></li>
                        <li><a class="dropdown-item" href="#"><span class="me-2">&#127466;&#127472;</span>
                                Spanish</a></li>
                    </ul>
                </div> --}}
                <div class="d-flex align-items-center gap-2 ms-2">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=4f46e5&color=fff&size=40"
                        alt="avatar" class="rounded-circle" width="32" height="32">
                    <span class="fw-semibold small d-none d-md-inline">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </div>

        {{-- PAGE TITLE --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                {{-- <p class="text-muted small mb-0">Dashboard &rsaquo; Ecommerce Dashboard</p> --}}
                <h1 class="h3 fw-bold mb-0">{{ __('Ecommerce Dashboard') }}</h1>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary period-btn" data-period="day">{{ __('Day') }}</button>
                    <button type="button" class="btn btn-outline-secondary period-btn" data-period="week">{{ __('Week') }}</button>
                    <button type="button" class="btn period-btn active" data-period="month"
                        style="background: #4f46e5; color: #fff; border-color: #4f46e5;">{{ __('Month') }}</button>
                    <button type="button" class="btn btn-outline-secondary period-btn" data-period="annual">{{ __('Annual') }}</button>
                </div>
                <div class="d-flex align-items-center gap-1 border rounded-2 px-2 py-1">
                    <button type="button" class="btn btn-sm border-0 p-0 text-muted lh-1 date-prev">&lsaquo;</button>
                    <span class="small fw-semibold px-1" id="currentDate">{{ now()->format('F Y') }}</span>
                    <button type="button" class="btn btn-sm border-0 p-0 text-muted lh-1 date-next">&rsaquo;</button>
                </div>
            </div>
        </div>

        {{-- STAT CARDS --}}
        @php
            $pendingShare = $stats['orders'] > 0 ? $stats['pendingOrders'] / $stats['orders'] * 100 : 0;
            $poPendingShare = $purchaseStats['purchaseOrders'] > 0 ? $purchaseStats['pendingPurchaseOrders'] / $purchaseStats['purchaseOrders'] * 100 : 0;
            $paymentShare = $purchaseStats['totalPurchaseAmount'] > 0 ? min(100, $purchaseStats['pendingPayments'] / $purchaseStats['totalPurchaseAmount'] * 100) : 0;
        @endphp
        <div class="row g-3 mb-3">
            <div class="col-6 col-xl-3">
                <div class="app-kpi kpi-cyan">
                    <div class="app-kpi-head"><span class="app-kpi-icon"><i class="bi bi-bag-check"></i></span><span class="app-kpi-label">{{ __('New Orders') }}</span><span class="app-kpi-chip" id="trendOrders">—</span></div>
                    <div class="app-kpi-value" id="statOrders">{{ number_format($stats['orders']) }}</div>
                    <div class="app-kpi-foot"><div class="app-kpi-caption" id="ordersCaption">{{ __('All time') }}</div><svg class="app-kpi-spark" id="ordersSpark" aria-hidden="true"><title>{{ __('Orders, last 6 months') }}</title></svg></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="app-kpi kpi-green">
                    <div class="app-kpi-head"><span class="app-kpi-icon"><i class="bi bi-cash-coin"></i></span><span class="app-kpi-label">{{ __('Total Income') }}</span><span class="app-kpi-chip" id="trendRevenue">—</span></div>
                    <div class="app-kpi-value" id="statRevenue">${{ number_format($stats['revenue'], 2) }}</div>
                    <div class="app-kpi-foot"><div class="app-kpi-caption" id="revenueCaption">{{ __('Avg') }} ${{ number_format($stats['orders'] ? $stats['revenue'] / $stats['orders'] : 0, 2) }} / {{ __('order') }}</div><svg class="app-kpi-spark" id="revenueSpark" aria-hidden="true"><title>{{ __('Income, last 6 months') }}</title></svg></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="app-kpi kpi-blue">
                    <div class="app-kpi-head"><span class="app-kpi-icon"><i class="bi bi-hourglass-split"></i></span><span class="app-kpi-label">{{ __('Pending Orders') }}</span><span class="app-kpi-chip" id="pendingChip">{{ round($pendingShare) }}%</span></div>
                    <div class="app-kpi-value" id="statPending">{{ number_format($stats['pendingOrders']) }}</div>
                    <div class="app-kpi-foot"><div class="app-kpi-caption"><span id="pendingCaption">{{ __('of orders waiting to be processed') }}</span><div class="app-kpi-meter"><span id="pendingMeter" style="width:{{ round($pendingShare, 1) }}%"></span></div></div></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="app-kpi kpi-amber">
                    <div class="app-kpi-head"><span class="app-kpi-icon"><i class="bi bi-person-plus"></i></span><span class="app-kpi-label">{{ __('New Users') }}</span><span class="app-kpi-chip" id="trendUsers">—</span></div>
                    <div class="app-kpi-value" id="statUsers">{{ number_format($stats['users']) }}</div>
                    <div class="app-kpi-foot"><div class="app-kpi-caption" id="usersCaption">{{ __('All time') }}</div></div>
                </div>
            </div>
        </div>

        {{-- PURCHASING STATS --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="app-kpi kpi-sky">
                    <div class="app-kpi-head"><span class="app-kpi-icon"><i class="bi bi-truck"></i></span><span class="app-kpi-label">{{ __('Total Suppliers') }}</span><span class="app-kpi-chip" id="supplierChip">—</span></div>
                    <div class="app-kpi-value" id="purchaseStatSuppliers">{{ number_format($purchaseStats['totalSuppliers']) }}</div>
                    <div class="app-kpi-foot"><div class="app-kpi-caption" id="supplierCaption">{{ __('Supplier accounts') }}</div></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="app-kpi kpi-violet">
                    <div class="app-kpi-head"><span class="app-kpi-icon"><i class="bi bi-file-earmark-text"></i></span><span class="app-kpi-label">{{ __('Purchase Orders') }}</span><span class="app-kpi-chip" id="poChip">—</span></div>
                    <div class="app-kpi-value" id="purchaseStatOrders">{{ number_format($purchaseStats['purchaseOrders']) }}</div>
                    <div class="app-kpi-foot"><div class="app-kpi-caption" id="poCaption">{{ __('Worth') }} ${{ number_format($purchaseStats['totalPurchaseAmount'], 2) }}</div><svg class="app-kpi-spark" id="purchaseSpark" aria-hidden="true"><title>{{ __('Purchases, last 6 months') }}</title></svg></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="app-kpi kpi-amber">
                    <div class="app-kpi-head"><span class="app-kpi-icon"><i class="bi bi-clock-history"></i></span><span class="app-kpi-label">{{ __('Pending Purchase Orders') }}</span><span class="app-kpi-chip" id="poPendingChip">{{ round($poPendingShare) }}%</span></div>
                    <div class="app-kpi-value" id="purchaseStatPending">{{ number_format($purchaseStats['pendingPurchaseOrders']) }}</div>
                    <div class="app-kpi-foot"><div class="app-kpi-caption"><span id="poPendingCaption">{{ __('of purchase orders awaiting approval') }}</span><div class="app-kpi-meter"><span id="poPendingMeter" style="width:{{ round($poPendingShare, 1) }}%"></span></div></div></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="app-kpi kpi-red">
                    <div class="app-kpi-head"><span class="app-kpi-icon"><i class="bi bi-credit-card-2-front"></i></span><span class="app-kpi-label">{{ __('Pending Payments') }}</span><span class="app-kpi-chip" id="paymentChip">{{ round($paymentShare) }}%</span></div>
                    <div class="app-kpi-value" id="purchaseStatPayments">${{ number_format($purchaseStats['pendingPayments'], 2) }}</div>
                    <div class="app-kpi-foot"><div class="app-kpi-caption"><span id="paymentCaption">{{ __('of') }} ${{ number_format($purchaseStats['totalPurchaseAmount'], 2) }} {{ __('still unpaid') }}</span><div class="app-kpi-meter"><span id="paymentMeter" style="width:{{ round($paymentShare, 1) }}%"></span></div></div></div>
                </div>
            </div>
        </div>

        {{-- BOTTOM GRID --}}
        <div class="row g-3">
            <div class="col-lg-8 d-flex flex-column">
                {{-- Summary Chart --}}
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="card-title fw-bold mb-0" style="font-size: 15px;">{{ __('Summary') }}</h5>
                                <span class="live-pill" id="liveStatus" role="status" aria-live="polite"><span class="live-dot"></span><span id="liveText">{{ __('Connecting…') }}</span></span>
                            </div>
                            <div class="btn-group btn-group-sm live-range" role="group" aria-label="{{ __('Chart range') }}">
                                <button type="button" class="btn btn-outline-secondary" data-range="24h">24h</button>
                                <button type="button" class="btn btn-outline-secondary" data-range="7d">7d</button>
                                <button type="button" class="btn btn-outline-secondary active" data-range="30d">30d</button>
                                <button type="button" class="btn btn-outline-secondary" data-range="12m">12m</button>
                            </div>
                        </div>
                        <div class="row g-0 border-bottom pb-3 mb-3">
                            <div class="col border-end">
                                <p class="fw-bold mb-0" style="font-size: 15px;" id="summaryProducts">{{ $stats['products'] }}</p>
                                <p class="small text-muted mb-0">{{ __('Total Products') }}</p>
                            </div>
                            <div class="col border-end">
                                <p class="fw-bold mb-0" style="font-size: 15px;" id="summarySold">{{ number_format($totalSold) }}</p>
                                <p class="small text-muted mb-0">{{ __('Units Sold') }}</p>
                            </div>
                            <div class="col border-end">
                                <p class="fw-bold mb-0" style="font-size: 15px;" id="summaryCost">${{ number_format($totalCost) }}</p>
                                <p class="small text-muted mb-0">{{ __('Inventory Cost') }}</p>
                            </div>
                            <div class="col">
                                <p class="fw-bold mb-0" style="font-size: 15px;" id="summaryRevenue">
                                    ${{ number_format($stats['revenue'], 2) }}</p>
                                <p class="small text-muted mb-0">{{ __('Total Revenue') }}</p>
                            </div>
                        </div>
                        <div class="live-legend d-flex flex-wrap align-items-center gap-3 mb-2">
                            <span><i class="swatch" style="background:#6366f1"></i>{{ __('Revenue') }} <b id="liveRevenueTotal">—</b> <span class="app-kpi-chip" id="liveRevenueChip">—</span></span>
                            <span><i class="swatch" style="background:#22d3ee"></i>{{ __('Orders') }} <b id="liveOrdersTotal">—</b> <span class="app-kpi-chip" id="liveOrdersChip">—</span></span>
                            <span class="ms-auto live-new d-none" id="liveNew"></span>
                        </div>
                        <div id="chartContainer" class="live-chart">
                            <svg id="liveChart" role="img" aria-label="{{ __('Revenue and orders over time') }}"></svg>
                            <div class="live-empty" id="liveEmpty">{{ __('Loading chart…') }}</div>
                            <div id="chartTooltip" class="live-tooltip"></div>
                        </div>
                    </div>
                </div>

                {{-- Recent Orders --}}
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3" style="font-size: 15px;">{{ __('Recent Orders') }}</h5>
                        <div class="d-flex flex-column">
                            @forelse ($recentOrders as $order)
                                <div class="d-flex align-items-center gap-3 py-3 border-bottom">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold flex-shrink-0"
                                        style="width: 38px; height: 38px; font-size: 15px; background: #4f46e5;">
                                        {{ strtoupper(substr($order->user->name ?? 'C', 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <p class="fw-bold mb-0 small text-truncate">{{ $order->user->name ?? __('Customer') }}
                                        </p>
                                        <p class="text-muted mb-0" style="font-size: 11px;">
                                            {{ $order->created_at?->format('M d, Y') }}</p>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <p class="fw-bold mb-0" style="font-size: 14px;">
                                            ${{ number_format($order->total_amount, 2) }}</p>
                                        @php
                                            $badgeStyle = match ($order->status) {
                                                'pending' => 'background: var(--badge-warning-bg); color: var(--badge-warning-text);',
                                                'completed', 'delivered' => 'background: var(--badge-success-bg); color: var(--badge-success-text);',
                                                'cancelled' => 'background: var(--badge-red-bg); color: var(--badge-red-text);',
                                                default => 'background: var(--badge-info-bg); color: var(--badge-info-text);',
                                            };
                                        @endphp
                                        <span class="badge rounded-pill text-uppercase"
                                            style="font-size: 10px; letter-spacing: .05em; {{ $badgeStyle }}">{{ $order->status }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center py-4 mb-0">{{ __('No orders yet.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>            </div>

            <div class="col-lg-4">
                {{-- Top Selling Products --}}
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3" style="font-size: 15px;">{{ __('Top Selling Products') }}</h5>
                        <div class="d-flex flex-column">
                            @forelse ($topSellingProducts as $product)
                                <div class="d-flex align-items-center gap-3 py-3 border-bottom">
                                    <div class="d-flex align-items-center justify-content-center rounded-3 overflow-hidden flex-shrink-0 bg-light"
                                        style="width: 52px; height: 52px;">
                                        @if ($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}"
                                                alt="{{ $product->name }}" class="w-100 h-100"
                                                style="object-fit: cover;">
                                        @else
                                            <svg width="20" height="20" fill="none" stroke="#94a3b8"
                                                stroke-width="1.5" viewBox="0 0 24 24">
                                                <rect x="3" y="3" width="18" height="18" rx="2" />
                                                <path d="m3 9 4-4 4 4 4-4 4 4" />
                                                <path d="M3 15h18" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <p class="fw-bold mb-0 small text-truncate">{{ $product->name }}</p>
                                        <p class="text-muted mb-0" style="font-size: 11px;">
                                            {{ $product->category->name ?? __('No category') }}</p>
                                        @php
                                            $r = $product->avg_rating ?? 0;
                                            $full = floor($r);
                                            $half = $r - $full >= 0.25;
                                        @endphp
                                        <div style="color: #f59e0b; font-size: 12px; letter-spacing: 1px;">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $full)
                                                    &#9733;
                                                @elseif ($i === $full + 1 && $half)
                                                &#9733;@else&#9734;
                                                @endif
                                            @endfor
                                            @if ($r)
                                                <span
                                                    style="color: #9ca3af; font-size: 10px; margin-left: 2px;">{{ $r }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <p class="fw-bold mb-0" style="font-size: 14px;">
                                            ${{ number_format($product->price, 2) }}</p>
                                        <p class="mb-0" style="font-size: 11px; color: #1d63d4; font-weight: 600;">{{ __('Sales') }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center py-4 mb-0">{{ __('No products yet.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Purchases Chart --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3" style="font-size: 15px;">{{ __('Purchases Over Time') }}</h5>
                        @php
                            $maxPurchase = max($purchaseChartTotals->max() ?? 0, 1);
                            $barWidth = $purchaseChartTotals->count() ? 100 / $purchaseChartTotals->count() : 0;
                        @endphp
                        <div class="d-flex align-items-end gap-2" style="height: 160px;" id="purchaseChartBars">
                            @foreach ($purchaseChartTotals as $i => $total)
                                <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-end h-100" style="min-width: 0;">
                                    <div style="width: 100%; max-width: 42px; height: {{ $total > 0 ? max(($total / $maxPurchase) * 100, 2) : 2 }}%; border-radius: 6px 6px 0 0; background: linear-gradient(180deg, #34d399, #059669); transition: height .3s ease;"></div>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-between small text-muted mt-2 px-1" id="purchaseChartLabels">
                            @foreach ($purchaseChartMonths as $month)
                                <span style="font-size: 10px;">{{ $month }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                {{-- Recent Purchases --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h5 class="card-title fw-bold mb-0" style="font-size: 15px;">{{ __('Recent Purchases') }}</h5>
                            <a href="{{ route('admin.purchases.index') }}" class="btn btn-sm btn-outline-success">{{ __('View All') }}</a>
                        </div>
                        <div class="d-flex flex-column">
                            @forelse ($recentPurchases as $po)
                                <div class="d-flex align-items-center gap-3 py-3 border-bottom">
                                    <div class="d-flex align-items-center justify-content-center rounded-3 text-white fw-bold flex-shrink-0"
                                        style="width: 38px; height: 38px; font-size: 11px; background: #4f46e5;">
                                        {{ strtoupper(substr($po->supplier->name ?? 'S', 0, 2)) }}
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <p class="fw-bold mb-0 small text-truncate font-monospace">{{ $po->po_number }} <span class="text-muted" style="font-weight: 500;">· {{ $po->supplier->name ?? __('Deleted') }}</span></p>
                                        <p class="text-muted mb-0" style="font-size: 11px;">{{ $po->order_date?->format('M d, Y') }}</p>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <p class="fw-bold mb-0" style="font-size: 14px;">${{ number_format($po->grand_total, 2) }}</p>
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
                                        <span class="badge rounded-pill text-uppercase" style="font-size: 9px; letter-spacing: .05em; background: {{ $badge[0] }}; color: {{ $badge[1] }};">{{ str_replace('_', ' ', $po->status) }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center py-4 mb-0">{{ __('No purchases yet.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ─── Period Filtering ──────────────────────────────────────────
            var activePeriod = 'month';

            document.querySelectorAll('.period-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.period-btn').forEach(function(b) {
                        b.classList.remove('active');
                        b.style.background = '';
                        b.style.color = '';
                        b.style.borderColor = '';
                    });
                    this.classList.add('active');
                    this.style.background = '#4f46e5';
                    this.style.color = '#fff';
                    this.style.borderColor = '#4f46e5';

                    activePeriod = this.getAttribute('data-period');
                    fetchDashboardData(activePeriod);
                });
            });

            function renderPurchaseChart(labels, totals) {
                var barsEl = document.getElementById('purchaseChartBars');
                var labelsEl = document.getElementById('purchaseChartLabels');
                if (!barsEl || !labelsEl) return;
                var maxVal = Math.max.apply(null, totals) || 1;
                var bars = '';
                for (var i = 0; i < totals.length; i++) {
                    var h = totals[i] > 0 ? Math.max((totals[i] / maxVal) * 100, 2) : 2;
                    bars += '<div class="flex-grow-1 d-flex flex-column align-items-center justify-content-end h-100" style="min-width: 0;">' +
                        '<div style="width: 100%; max-width: 42px; height: ' + h + '%; border-radius: 6px 6px 0 0; background: linear-gradient(180deg, #34d399, #059669); transition: height .3s ease;"></div>' +
                        '</div>';
                }
                barsEl.innerHTML = bars;
                labelsEl.innerHTML = labels.map(function(l) {
                    return '<span style="font-size: 10px;">' + l + '</span>';
                }).join('');
            }

            function setText(id, text) { var el = document.getElementById(id); if (el) el.textContent = text; }
            // Percentage chip + meter; the chip turns `tone` (warn/bad) once the share is noticeable.
            function setShare(chipId, meterId, percent, tone) {
                var chip = document.getElementById(chipId), meter = document.getElementById(meterId), p = Math.max(0, Math.min(100, percent || 0));
                if (chip) { chip.textContent = Math.round(p) + '%'; chip.className = 'app-kpi-chip' + (p >= 25 ? ' ' + tone : ''); }
                if (meter) meter.style.width = p + '%';
            }
            function setSupplierChip(suppliers, purchaseOrders) {
                var chip = document.getElementById('supplierChip'); if (!chip) return;
                chip.innerHTML = suppliers ? '<i class="bi bi-file-earmark-text"></i>' + (purchaseOrders / suppliers).toFixed(1) + ' ' + @json(__('PO each')) : '—';
                chip.title = @json(__('Purchase orders per supplier'));
            }
            appKpiSpark(document.getElementById('ordersSpark'), @json($chartOrders));
            appKpiSpark(document.getElementById('revenueSpark'), @json($chartRevenue));
            appKpiSpark(document.getElementById('purchaseSpark'), @json($purchaseChartTotals));
            setShare('pendingChip', 'pendingMeter', {{ round($pendingShare, 2) }}, 'warn');
            setShare('poPendingChip', 'poPendingMeter', {{ round($poPendingShare, 2) }}, 'warn');
            setShare('paymentChip', 'paymentMeter', {{ round($paymentShare, 2) }}, 'bad');
            setSupplierChip({{ (int) $purchaseStats['totalSuppliers'] }}, {{ (int) $purchaseStats['purchaseOrders'] }});
            appKpiTrend(document.getElementById('trendOrders'), {{ (float) $trends['orders'] }}, true, @json(__('30-day trend')));
            appKpiTrend(document.getElementById('trendRevenue'), {{ (float) $trends['revenue'] }}, true, @json(__('30-day trend')));
            appKpiTrend(document.getElementById('trendUsers'), {{ (float) $trends['users'] }}, true, @json(__('30-day trend')));

            function fetchDashboardData(period) {
                fetch('{{ route('admin.dashboard.data') }}?period=' + period)
                    .then(function(r) {
                        return r.json();
                    })
                    .then(function(data) {
                        document.getElementById('statOrders').textContent = data.stats.orders.toLocaleString();
                        document.getElementById('statRevenue').textContent = '$' + parseFloat(data.stats
                            .revenue).toFixed(2);
                        document.getElementById('statPending').textContent = data.stats.pendingOrders
                            .toLocaleString();
                        document.getElementById('statUsers').textContent = data.stats.users.toLocaleString();

                        // KPI chips & captions (compare with the previous period)
                        var vsText = {day: @json(__('vs yesterday')), week: @json(__('vs last week')), month: @json(__('vs last month')), annual: @json(__('vs last year'))}[period] || '';
                        var periodText = data.label ? data.label.charAt(0).toUpperCase() + data.label.slice(1) : '';
                        appKpiTrend(document.getElementById('trendOrders'), data.trends.orders, true, vsText);
                        appKpiTrend(document.getElementById('trendRevenue'), data.trends.revenue, true, vsText);
                        appKpiTrend(document.getElementById('trendUsers'), data.trends.users, true, vsText);
                        setText('ordersCaption', periodText + ' · ' + vsText);
                        setText('usersCaption', periodText + ' · ' + vsText);
                        var orders = Number(data.stats.orders) || 0, revenue = parseFloat(data.stats.revenue) || 0;
                        setText('revenueCaption', @json(__('Avg')) + ' $' + (orders ? revenue / orders : 0).toFixed(2) + ' / ' + @json(__('order')));
                        setShare('pendingChip', 'pendingMeter', orders ? data.stats.pendingOrders / orders * 100 : 0, 'warn');

                        if (data.summary) {
                            var el = document.getElementById('summaryProducts');
                            if (el) el.textContent = data.summary.totalProducts.toLocaleString();
                            el = document.getElementById('summarySold');
                            if (el) el.textContent = data.summary.totalSold.toLocaleString();
                            el = document.getElementById('summaryCost');
                            if (el) el.textContent = '$' + parseFloat(data.summary.totalCost).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            el = document.getElementById('summaryRevenue');
                            if (el) el.textContent = '$' + parseFloat(data.summary.totalRevenue).toFixed(2);
                        }

                        // Purchasing stats
                        var ps = data.purchaseStats || {};
                        document.getElementById('purchaseStatSuppliers').textContent = (ps.totalSuppliers || 0).toLocaleString();
                        document.getElementById('purchaseStatOrders').textContent = (ps.purchaseOrders || 0).toLocaleString();
                        document.getElementById('purchaseStatPending').textContent = (ps.pendingPurchaseOrders || 0).toLocaleString();
                        document.getElementById('purchaseStatPayments').textContent = '$' + parseFloat(ps.pendingPayments || 0).toFixed(2);
                        var poCount = ps.purchaseOrders || 0, purchaseTotal = parseFloat(ps.totalPurchaseAmount || 0);
                        setText('poCaption', @json(__('Worth')) + ' $' + purchaseTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' · ' + (data.label || ''));
                        setSupplierChip(ps.totalSuppliers || 0, poCount);
                        setShare('poPendingChip', 'poPendingMeter', poCount ? (ps.pendingPurchaseOrders || 0) / poCount * 100 : 0, 'warn');
                        setShare('paymentChip', 'paymentMeter', purchaseTotal ? Math.min(100, (ps.pendingPayments || 0) / purchaseTotal * 100) : 0, 'bad');
                        setText('paymentCaption', @json(__('of')) + ' $' + purchaseTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + @json(__('still unpaid')));

                        if (data.purchaseChart) {
                            renderPurchaseChart(data.purchaseChart.labels || [], data.purchaseChart.totals || []);
                        }
                    });
            }


            // ─── Live summary chart ───────────────────────────────────────
            // Polls the server every 15 s (paused while the tab is hidden) and
            // redraws revenue (line) over orders (bars) for the chosen range.
            var LIVE_URL = @json(route('admin.dashboard.live'));
            var LIVE_EVERY = 15000;
            var T = {
                live: @json(__('Live')), updated: @json(__('updated')), ago: @json(__('ago')), justNow: @json(__('just now')),
                paused: @json(__('Paused')), offline: @json(__('Reconnecting…')), revenue: @json(__('Revenue')), orders: @json(__('Orders')),
                avg: @json(__('Avg / order')), peak: @json(__('Peak')), now: @json(__('Now')), newOrder: @json(__('new order')), newOrders: @json(__('new orders')),
                empty: @json(__('No orders in this range yet')), fresh: @json(__('New')), vsPrev: @json(__('vs previous period'))
            };
            var live = { range: '30d', data: null, timer: null, updatedAt: 0, failed: false, latest: null, newTimer: null };

            function fmtMoney(v) { return '$' + Number(v || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
            function shortMoney(v) { var a = Math.abs(v); return '$' + (a >= 1e6 ? +(v / 1e6).toFixed(1) + 'M' : a >= 1e3 ? +(v / 1e3).toFixed(1) + 'k' : +v.toFixed(a < 10 ? 1 : 0)); }
            function niceCeil(v) { if (!(v > 0)) return 1; var e = Math.pow(10, Math.floor(Math.log10(v))), f = v / e; return (f <= 1 ? 1 : f <= 2 ? 2 : f <= 2.5 ? 2.5 : f <= 5 ? 5 : 10) * e; }
            function escapeHtml(t) { return String(t).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
            function smoothLine(pts) {
                return pts.map(function (p, i) {
                    if (!i) return 'M' + p[0] + ',' + p[1];
                    var q = pts[i - 1], mx = (q[0] + p[0]) / 2;
                    return 'C' + mx + ',' + q[1] + ' ' + mx + ',' + p[1] + ' ' + p[0] + ',' + p[1];
                }).join('');
            }
            function setChangeChip(id, current, previous) {
                var el = document.getElementById(id); if (!el) return;
                if (!previous) { el.className = 'app-kpi-chip' + (current ? ' good' : ''); el.textContent = current ? T.fresh : '—'; el.title = T.vsPrev; return; }
                appKpiTrend(el, (current - previous) / previous * 100, true, T.vsPrev);
            }

            function drawLiveChart(animate) {
                var d = live.data, svg = document.getElementById('liveChart'); if (!d || !svg) return;
                var wrap = svg.parentElement, W = Math.max(wrap.clientWidth, 320), H = 240, L = 52, R = 34, top = 26, B = 26;
                var pw = W - L - R, ph = H - top - B, n = d.labels.length, step = pw / n;
                var maxRev = niceCeil(Math.max.apply(null, d.revenue.concat(0)) * 1.15);
                var maxOrd = Math.max(4, Math.ceil(Math.max.apply(null, d.orders.concat(0)) * 1.25 / 4) * 4);
                var x = function (i) { return L + step * i + step / 2; };
                var yRev = function (v) { return top + ph - v / maxRev * ph; };
                var yOrd = function (v) { return top + ph - v / maxOrd * ph; };
                var barW = Math.max(3, Math.min(18, step * .5));
                var html = '<defs><linearGradient id="liveArea" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#6366f1" stop-opacity=".35"/><stop offset="100%" stop-color="#6366f1" stop-opacity="0"/></linearGradient>'
                    + '<linearGradient id="liveBar" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#67e8f9"/><stop offset="100%" stop-color="#06b6d4"/></linearGradient>'
                    + '<linearGradient id="liveStroke" x1="0" y1="0" x2="1" y2="0"><stop offset="0%" stop-color="#818cf8"/><stop offset="100%" stop-color="#7c3aed"/></linearGradient>'
                    + '<filter id="liveGlow" x="-20%" y="-50%" width="140%" height="200%"><feDropShadow dx="0" dy="4" stdDeviation="4" flood-color="#6366f1" flood-opacity=".35"/></filter></defs>';
                for (var t = 0; t <= 4; t++) {
                    var gy = top + ph - ph * t / 4;
                    html += '<line x1="' + L + '" y1="' + gy + '" x2="' + (W - R) + '" y2="' + gy + '" stroke="var(--admin-border)" ' + (t ? 'stroke-dasharray="2 5"' : '') + '/>'
                        + '<text x="' + (L - 8) + '" y="' + (gy + 3.5) + '" text-anchor="end" font-size="10" fill="var(--admin-text-muted)">' + shortMoney(maxRev * t / 4) + '</text>'
                        + '<text x="' + (W - R + 8) + '" y="' + (gy + 3.5) + '" font-size="10" fill="#0891b2">' + (maxOrd * t / 4) + '</text>';
                }
                // orders as bars (right axis)
                d.orders.forEach(function (v, i) {
                    if (!v) return;
                    var h = ph - (yOrd(v) - top), bx = x(i) - barW / 2, r = Math.min(4, barW / 2, h);
                    html += '<path class="' + (animate ? 'live-bar' : '') + '" style="--i:' + i + '" d="M' + bx + ',' + (top + ph) + 'V' + (yOrd(v) + r) + 'Q' + bx + ',' + yOrd(v) + ' ' + (bx + r) + ',' + yOrd(v) + 'H' + (bx + barW - r) + 'Q' + (bx + barW) + ',' + yOrd(v) + ' ' + (bx + barW) + ',' + (yOrd(v) + r) + 'V' + (top + ph) + 'Z" fill="url(#liveBar)" opacity="' + (i === n - 1 ? .9 : .45) + '"/>';
                });
                // revenue as smooth area + line (left axis)
                var pts = d.revenue.map(function (v, i) { return [+x(i).toFixed(1), +yRev(v).toFixed(1)]; });
                var line = smoothLine(pts);
                html += '<path class="' + (animate ? 'live-area' : '') + '" d="' + line + 'L' + pts[n - 1][0] + ',' + (top + ph) + 'L' + pts[0][0] + ',' + (top + ph) + 'Z" fill="url(#liveArea)"/>'
                    + '<path class="' + (animate ? 'live-line' : '') + '" d="' + line + '" fill="none" stroke="url(#liveStroke)" stroke-width="2.6" stroke-linecap="round" filter="url(#liveGlow)"/>';
                // peak marker
                var peak = d.revenue.indexOf(Math.max.apply(null, d.revenue));
                if (d.revenue[peak] > 0) {
                    var px = pts[peak][0], py = pts[peak][1], label = T.peak + ' ' + shortMoney(d.revenue[peak]), lw = label.length * 6 + 16;
                    var lx = Math.min(Math.max(px - lw / 2, L), W - R - lw);
                    html += '<circle cx="' + px + '" cy="' + py + '" r="4.5" fill="#7c3aed" stroke="var(--admin-surface)" stroke-width="2"/>'
                        + '<rect x="' + lx + '" y="' + Math.max(0, py - 28) + '" width="' + lw + '" height="18" rx="9" fill="#7c3aed"/>'
                        + '<text x="' + (lx + lw / 2) + '" y="' + (Math.max(0, py - 28) + 12.5) + '" text-anchor="middle" font-size="10" font-weight="700" fill="#fff">' + escapeHtml(label) + '</text>';
                }
                // "now" pulse on the latest bucket
                var last = pts[n - 1];
                html += '<circle class="live-now" cx="' + last[0] + '" cy="' + last[1] + '" r="5" fill="#6366f1"/><circle cx="' + last[0] + '" cy="' + last[1] + '" r="4" fill="var(--admin-surface)" stroke="#6366f1" stroke-width="2.5"/>';
                // x labels (thinned)
                var every = Math.ceil(n / Math.max(4, Math.floor(pw / 70)));
                d.labels.forEach(function (lab, i) {
                    if (i !== n - 1 && (i % every || n - 1 - i < every * .7)) return; // keep room for the "Now" label
                    html += '<text x="' + x(i) + '" y="' + (H - 6) + '" text-anchor="middle" font-size="10" ' + (i === n - 1 ? 'font-weight="700" fill="#6366f1">' + escapeHtml(T.now) : 'fill="var(--admin-text-muted)">' + escapeHtml(lab)) + '</text>';
                });
                html += '<line class="live-cross" id="liveCross" x1="0" x2="0" y1="' + top + '" y2="' + (top + ph) + '" stroke="#6366f1" stroke-width="1" stroke-dasharray="3 3"/>'
                    + '<circle class="live-hover-dot" id="liveHoverDot" r="5.5" fill="#6366f1" stroke="var(--admin-surface)" stroke-width="2.5"/>';
                svg.setAttribute('viewBox', '0 0 ' + W + ' ' + H);
                svg.innerHTML = html;
                svg._live = { L: L, step: step, n: n, pts: pts };
                document.getElementById('liveEmpty').textContent = T.empty;
                document.getElementById('liveEmpty').hidden = d.totals.orders > 0;
            }

            (function bindLiveHover() {
                var svg = document.getElementById('liveChart'), wrap = document.getElementById('chartContainer'), tip = document.getElementById('chartTooltip');
                if (!svg) return;
                function hide() { wrap.classList.remove('is-hovering'); tip.style.display = 'none'; }
                svg.addEventListener('pointermove', function (e) {
                    var c = svg._live, d = live.data; if (!c || !d) return;
                    var rect = svg.getBoundingClientRect(), mx = e.clientX - rect.left;
                    var i = Math.max(0, Math.min(c.n - 1, Math.floor((mx - c.L) / c.step))), p = c.pts[i];
                    var cross = document.getElementById('liveCross'), dot = document.getElementById('liveHoverDot');
                    cross.setAttribute('x1', p[0]); cross.setAttribute('x2', p[0]); dot.setAttribute('cx', p[0]); dot.setAttribute('cy', p[1]);
                    wrap.classList.add('is-hovering');
                    var orders = d.orders[i], revenue = d.revenue[i];
                    tip.innerHTML = '<div class="tt-title">' + escapeHtml(d.labels[i]) + '</div>'
                        + '<div class="tt-row"><span><i style="background:#6366f1"></i>' + T.revenue + '</span><b>' + fmtMoney(revenue) + '</b></div>'
                        + '<div class="tt-row"><span><i style="background:#22d3ee"></i>' + T.orders + '</span><b>' + orders + '</b></div>'
                        + '<div class="tt-foot tt-row"><span>' + T.avg + '</span><b>' + fmtMoney(orders ? revenue / orders : 0) + '</b></div>';
                    tip.style.display = 'block';
                    var wr = wrap.getBoundingClientRect(), tw = tip.offsetWidth, left = p[0] + 14;
                    if (left + tw > wr.width) left = p[0] - tw - 14;
                    tip.style.left = Math.max(0, left) + 'px';
                    tip.style.top = Math.max(0, p[1] - tip.offsetHeight / 2) + 'px';
                });
                svg.addEventListener('pointerleave', hide);
            })();

            function setLiveStatus() {
                var pill = document.getElementById('liveStatus'), text = document.getElementById('liveText');
                pill.classList.toggle('is-paused', document.hidden);
                pill.classList.toggle('is-offline', live.failed && !document.hidden);
                if (document.hidden) { text.textContent = T.paused; return; }
                if (live.failed) { text.textContent = T.offline; return; }
                if (!live.updatedAt) return;
                var s = Math.round((Date.now() - live.updatedAt) / 1000);
                text.textContent = T.live + ' · ' + T.updated + ' ' + (s < 5 ? T.justNow : s + 's ' + T.ago);
            }
            setInterval(setLiveStatus, 1000);

            function showNewOrders(count) {
                var el = document.getElementById('liveNew'); if (!el || count <= 0) return;
                el.textContent = '+' + count + ' ' + (count === 1 ? T.newOrder : T.newOrders);
                el.classList.remove('d-none'); el.style.animation = 'none'; void el.offsetWidth; el.style.animation = '';
                clearTimeout(live.newTimer); live.newTimer = setTimeout(function () { el.classList.add('d-none'); }, 8000);
            }

            function loadLive(animate) {
                clearTimeout(live.timer);
                var range = live.range;
                fetch(LIVE_URL + '?range=' + range, { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                    .then(function (d) {
                        if (range !== live.range) return; // user switched range mid-request
                        var previous = live.data;
                        if (previous && previous.range === d.range && live.latest && d.latestOrderAt && d.latestOrderAt !== live.latest) {
                            showNewOrders(d.totals.orders - previous.totals.orders);
                            animate = false;
                        }
                        live.data = d; live.latest = d.latestOrderAt; live.failed = false; live.updatedAt = Date.now();
                        setText('liveRevenueTotal', fmtMoney(d.totals.revenue));
                        setText('liveOrdersTotal', d.totals.orders.toLocaleString());
                        setChangeChip('liveRevenueChip', d.totals.revenue, d.previous.revenue);
                        setChangeChip('liveOrdersChip', d.totals.orders, d.previous.orders);
                        drawLiveChart(animate);
                    })
                    .catch(function (err) { live.failed = true; console.error('Live chart update failed', err); })
                    .then(function () { setLiveStatus(); if (!document.hidden) live.timer = setTimeout(function () { loadLive(false); }, LIVE_EVERY); });
            }

            document.querySelectorAll('.live-range [data-range]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    if (btn.dataset.range === live.range) return;
                    document.querySelectorAll('.live-range [data-range]').forEach(function (b) { b.classList.toggle('active', b === btn); });
                    live.range = btn.dataset.range; live.data = null; live.latest = null;
                    loadLive(true);
                });
            });
            document.addEventListener('visibilitychange', function () {
                if (document.hidden) { clearTimeout(live.timer); setLiveStatus(); } else { loadLive(false); }
            });
            var liveResize;
            window.addEventListener('resize', function () { clearTimeout(liveResize); liveResize = setTimeout(function () { drawLiveChart(false); }, 150); });
            loadLive(true);

            // ─── Date navigation ───────────────────────────────────────────
            var months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            var currentDate = new Date();
            var displayDate = new Date(currentDate);

            function updateDateDisplay() {
                var el = document.getElementById('currentDate');
                if (el) {
                    el.textContent = months[displayDate.getMonth()] + ' ' + displayDate.getFullYear();
                }
            }

            var prevBtn = document.querySelector('.date-prev');
            var nextBtn = document.querySelector('.date-next');
            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    displayDate.setMonth(displayDate.getMonth() - 1);
                    updateDateDisplay();
                });
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    displayDate.setMonth(displayDate.getMonth() + 1);
                    updateDateDisplay();
                });
            }

        });

    </script>
@endpush
