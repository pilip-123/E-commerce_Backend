@extends('layouts.admin')

@section('title', __('Admin Dashboard'))

@section('content')
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
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 rounded-4 text-white position-relative overflow-hidden h-100"
                    style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); min-height: 160px;">
                    <div class="card-body d-flex flex-column pb-0">
                        <p class="small text-uppercase fw-bold opacity-75 mb-0"
                            style="font-size: 11px; letter-spacing: .08em;">{{ __('New Orders') }}</p>
                        <p class="fw-bold mb-0" style="font-size: 38px; line-height: 1.15; margin-top: 4px;"
                            id="statOrders">
                            {{ number_format($stats['orders']) }}</p>
                        <p class="small opacity-75" style="font-size: 11px; margin-top: 3px;" id="trendOrders">
                            {{ $trends['orders'] >= 0 ? '+' : '' }}{{ number_format($trends['orders'], 2) }}% (30 days)
                        </p>
                    </div>
                    <div class="position-absolute bottom-0 start-0 end-0" style="height: 42px; opacity: .45;">
                        <svg viewBox="0 0 200 40" class="w-100 h-100" preserveAspectRatio="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M0,30 C30,28 50,20 80,18 C110,16 140,24 170,16 C185,12 195,8 200,6" stroke="white"
                                stroke-width="2.5" fill="none" />
                        </svg>
                    </div>
                    <div class="position-absolute top-0 end-0 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 44px; height: 44px; margin: 18px; background: rgba(255,255,255,.25);">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                            <line x1="3" y1="6" x2="21" y2="6" />
                            <path d="M16 10a4 4 0 0 1-8 0" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 rounded-4 text-white position-relative overflow-hidden h-100"
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); min-height: 160px;">
                    <div class="card-body d-flex flex-column pb-0">
                        <p class="small text-uppercase fw-bold opacity-75 mb-0"
                            style="font-size: 11px; letter-spacing: .08em;">{{ __('Total Income') }}</p>
                        <p class="fw-bold mb-0" style="font-size: 38px; line-height: 1.15; margin-top: 4px;"
                            id="statRevenue">
                            ${{ number_format($stats['revenue'], 2) }}</p>
                        <p class="small opacity-75" style="font-size: 11px; margin-top: 3px;" id="trendRevenue">
                            {{ $trends['revenue'] >= 0 ? __('Increased') : __('Decreased') }} by
                            {{ number_format(abs($trends['revenue']), 2) }}%
                        </p>
                    </div>
                    <div class="position-absolute bottom-0 start-0 end-0" style="height: 42px; opacity: .45;">
                        <svg viewBox="0 0 200 40" class="w-100 h-100" preserveAspectRatio="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <rect x="10" y="20" width="14" height="20" fill="white" rx="2" />
                            <rect x="32" y="14" width="14" height="26" fill="white" rx="2" />
                            <rect x="54" y="10" width="14" height="30" fill="white" rx="2" />
                            <rect x="76" y="16" width="14" height="24" fill="white" rx="2" />
                            <rect x="98" y="8" width="14" height="32" fill="white" rx="2" />
                            <rect x="120" y="12" width="14" height="28" fill="white" rx="2" />
                            <rect x="142" y="6" width="14" height="34" fill="white" rx="2" />
                            <rect x="164" y="2" width="14" height="38" fill="white" rx="2" />
                        </svg>
                    </div>
                    <div class="position-absolute top-0 end-0 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 44px; height: 44px; margin: 18px; background: rgba(255,255,255,.25);">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 6v6l4 2" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 rounded-4 text-white position-relative overflow-hidden h-100"
                    style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); min-height: 160px;">
                    <div class="card-body d-flex flex-column pb-0">
                        <p class="small text-uppercase fw-bold opacity-75 mb-0"
                            style="font-size: 11px; letter-spacing: .08em;">{{ __('Pending Orders') }}</p>
                        <p class="fw-bold mb-0" style="font-size: 38px; line-height: 1.15; margin-top: 4px;"
                            id="statPending">
                            {{ number_format($stats['pendingOrders']) }}</p>
                        <p class="small opacity-75" style="font-size: 11px; margin-top: 3px;" id="trendLabel">
                            {{ $trends['orders'] >= 0 ? '+' : '' }}{{ number_format($trends['orders'], 2) }}% (30 days)
                        </p>
                    </div>
                    <div class="position-absolute bottom-0 start-0 end-0" style="height: 42px; opacity: .45;">
                        <svg viewBox="0 0 200 40" class="w-100 h-100" preserveAspectRatio="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M0,20 C40,20 60,18 100,18 C140,18 160,22 200,18" stroke="white" stroke-width="2"
                                fill="none" />
                        </svg>
                    </div>
                    <div class="position-absolute top-0 end-0 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 44px; height: 44px; margin: 18px; background: rgba(255,255,255,.25);">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <rect x="5" y="2" width="14" height="20" rx="2" />
                            <line x1="9" y1="7" x2="15" y2="7" />
                            <line x1="9" y1="11" x2="15" y2="11" />
                            <line x1="9" y1="15" x2="12" y2="15" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 rounded-4 text-white position-relative overflow-hidden h-100"
                    style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); min-height: 160px;">
                    <div class="card-body d-flex flex-column pb-0">
                        <p class="small text-uppercase fw-bold opacity-75 mb-0"
                            style="font-size: 11px; letter-spacing: .08em;">{{ __('New Users') }}</p>
                        <p class="fw-bold mb-0" style="font-size: 38px; line-height: 1.15; margin-top: 4px;"
                            id="statUsers">
                            {{ number_format($stats['users']) }}</p>
                        <p class="small opacity-75" style="font-size: 11px; margin-top: 3px;" id="trendUsers">
                            {{ $trends['users'] >= 0 ? '+' : '' }}{{ number_format($trends['users'], 2) }}% (30 days)
                        </p>
                    </div>
                    <div class="position-absolute bottom-0 start-0 end-0" style="height: 42px; opacity: .45;">
                        <svg viewBox="0 0 200 40" class="w-100 h-100" preserveAspectRatio="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <rect x="8" y="28" width="10" height="12" fill="white" rx="2" />
                            <rect x="24" y="18" width="10" height="22" fill="white" rx="2" />
                            <rect x="40" y="10" width="10" height="30" fill="white" rx="2" />
                            <rect x="56" y="20" width="10" height="20" fill="white" rx="2" />
                            <rect x="72" y="6" width="10" height="34" fill="white" rx="2" />
                            <rect x="88" y="14" width="10" height="26" fill="white" rx="2" />
                            <rect x="104" y="4" width="10" height="36" fill="white" rx="2" />
                            <rect x="120" y="12" width="10" height="28" fill="white" rx="2" />
                            <rect x="136" y="8" width="10" height="32" fill="white" rx="2" />
                            <rect x="152" y="2" width="10" height="38" fill="white" rx="2" />
                        </svg>
                    </div>
                    <div class="position-absolute top-0 end-0 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 44px; height: 44px; margin: 18px; background: rgba(255,255,255,.25);">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- BOTTOM GRID --}}
        <div class="row g-3">
            <div class="col-lg-8 d-flex flex-column">
                {{-- Summary Chart --}}
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3" style="font-size: 15px;">{{ __('Summary') }}</h5>
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
                        <div id="chartContainer" style="position: relative;">
                            <svg viewBox="0 0 600 130" preserveAspectRatio="none" class="w-100"
                                style="height: 130px; display: block;" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="areaGrad" x1="0" y1="0" x2="0"
                                        y2="1">
                                        <stop offset="0%" stop-color="#f87171" stop-opacity="0.55" />
                                        <stop offset="100%" stop-color="#f87171" stop-opacity="0.04" />
                                    </linearGradient>
                                </defs>
                                <path id="chartArea"
                                    d="M0,120 C60,115 90,100 120,95 C160,88 190,105 240,100 C290,95 320,85 360,70 C400,55 440,75 490,55 C530,38 570,20 600,10 L600,130 L0,130 Z"
                                    fill="url(#areaGrad)" />
                                <path id="chartLine"
                                    d="M0,120 C60,115 90,100 120,95 C160,88 190,105 240,100 C290,95 320,85 360,70 C400,55 440,75 490,55 C530,38 570,20 600,10"
                                    fill="none" stroke="#f87171" stroke-width="2.5" />
                                <g id="chartDots"></g>
                                <rect id="chartOverlay" width="600" height="130" fill="transparent" style="cursor: crosshair;" />
                            </svg>
                            <div id="chartTooltip"
                                style="display:none; position:absolute; background:#1f2937; color:#fff; padding:6px 12px; border-radius:8px; font-size:12px; font-weight:600; pointer-events:none; white-space:nowrap; z-index:10; transform:translate(-50%,-110%); box-shadow:0 4px 12px rgba(0,0,0,.2);">
                            </div>
                            <div class="d-flex justify-content-between small text-muted mt-1 px-1" id="chartLabels">
                                @foreach ($chartMonths as $month)
                                    <span>{{ $month }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Orders --}}
                <div class="card border-0 shadow-sm rounded-4">
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
                                                'pending' => 'background: #fef3c7; color: #d97706;',
                                                'completed', 'delivered' => 'background: #d1fae5; color: #059669;',
                                                'cancelled' => 'background: #fee2e2; color: #dc2626;',
                                                default => 'background: #dbeafe; color: #2563eb;',
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
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Top Selling Products --}}
                <div class="card border-0 shadow-sm rounded-4 h-100">
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

            var chartPoints = [];
            var chartMonths = [];
            var chartValues = [];

            function buildChartPath(values, width, height) {
                if (!values || values.length < 2) return { area: '', line: '', points: [] };
                var maxVal = Math.max.apply(null, values) || 1;
                var stepX = width / (values.length - 1);
                var points = values.map(function(v, i) {
                    var x = i * stepX;
                    var y = height - (v / maxVal) * (height * 0.85) - 8;
                    return { x: x, y: Math.max(y, 2), value: v, index: i };
                });
                var d = 'M' + points[0].x + ',' + points[0].y;
                for (var i = 1; i < points.length; i++) {
                    var prev = points[i - 1];
                    var cur = points[i];
                    var cx1 = prev.x + (cur.x - prev.x) / 2;
                    var cy1 = prev.y;
                    var cx2 = cur.x - (cur.x - prev.x) / 2;
                    var cy2 = cur.y;
                    d += ' C' + cx1 + ',' + cy1 + ' ' + cx2 + ',' + cy2 + ' ' + cur.x + ',' + cur.y;
                }
                var area = d + ' L' + points[points.length - 1].x + ',' + height + ' L' + points[0].x + ',' + height + ' Z';
                return { area: area, line: d, points: points };
            }

            function renderChartDots(points) {
                var dotsEl = document.getElementById('chartDots');
                if (!dotsEl) return;
                dotsEl.innerHTML = '';
                points.forEach(function(p) {
                    var circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    circle.setAttribute('cx', p.x);
                    circle.setAttribute('cy', p.y);
                    circle.setAttribute('r', '4');
                    circle.setAttribute('fill', '#fff');
                    circle.setAttribute('stroke', '#f87171');
                    circle.setAttribute('stroke-width', '2.5');
                    circle.setAttribute('class', 'chart-dot');
                    circle.style.cursor = 'crosshair';
                    dotsEl.appendChild(circle);
                });
            }

            function setupChartHover(points, months, values) {
                var container = document.getElementById('chartContainer');
                var overlay = document.getElementById('chartOverlay');
                var tooltip = document.getElementById('chartTooltip');
                if (!overlay || !tooltip || !container) return;

                overlay.addEventListener('mousemove', function(e) {
                    var rect = container.getBoundingClientRect();
                    var svg = overlay.closest('svg');
                    var viewBoxW = 600;
                    var svgRect = svg.getBoundingClientRect();
                    var scaleX = svgRect.width / viewBoxW;
                    var mx = (e.clientX - svgRect.left) / scaleX;

                    var closest = points[0];
                    var minDist = Math.abs(mx - points[0].x);
                    for (var i = 1; i < points.length; i++) {
                        var dist = Math.abs(mx - points[i].x);
                        if (dist < minDist) { minDist = dist; closest = points[i]; }
                    }

                    var label = months[closest.index] || '';
                    var val = values[closest.index];
                    tooltip.textContent = label + ': ' + (val !== undefined ? val.toLocaleString() : '0') + ' orders';
                    tooltip.style.display = 'block';

                    var tooltipX = e.clientX - rect.left;
                    var tooltipY = (closest.y / 130) * svgRect.height;
                    tooltip.style.left = tooltipX + 'px';
                    tooltip.style.top = tooltipY + 'px';

                    document.querySelectorAll('.chart-dot').forEach(function(dot) {
                        var cx = parseFloat(dot.getAttribute('cx'));
                        if (Math.abs(cx - closest.x) < 1) {
                            dot.setAttribute('r', '6');
                            dot.setAttribute('fill', '#f87171');
                        } else {
                            dot.setAttribute('r', '4');
                            dot.setAttribute('fill', '#fff');
                        }
                    });
                });

                overlay.addEventListener('mouseleave', function() {
                    tooltip.style.display = 'none';
                    document.querySelectorAll('.chart-dot').forEach(function(dot) {
                        dot.setAttribute('r', '4');
                        dot.setAttribute('fill', '#fff');
                    });
                });
            }

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

                        document.getElementById('trendOrders').textContent = (data.trends.orders >= 0 ? '+' :
                            '') + data.trends.orders.toFixed(2) + '% (' + data.label + ')';
                        document.getElementById('trendRevenue').textContent = (data.trends.revenue >= 0 ?
                                '{{ __("Increased") }}' : '{{ __("Decreased") }}') + ' by ' + Math.abs(data.trends.revenue).toFixed(2) +
                            '%';
                        document.getElementById('trendUsers').textContent = (data.trends.users >= 0 ? '+' :
                            '') + data.trends.users.toFixed(2) + '% (' + data.label + ')';

                        var trendLabel = document.getElementById('trendLabel');
                        if (trendLabel) {
                            trendLabel.textContent = (data.trends.orders >= 0 ? '+' : '') + data.trends.orders
                                .toFixed(2) + '% (' + data.label + ')';
                        }

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

                        if (data.chart && data.chart.orders) {
                            chartMonths = data.chart.months || [];
                            chartValues = data.chart.orders || [];

                            var paths = buildChartPath(chartValues, 600, 130);
                            chartPoints = paths.points || [];

                            var areaEl = document.getElementById('chartArea');
                            var lineEl = document.getElementById('chartLine');
                            if (areaEl) areaEl.setAttribute('d', paths.area);
                            if (lineEl) lineEl.setAttribute('d', paths.line);

                            renderChartDots(chartPoints);
                            setupChartHover(chartPoints, chartMonths, chartValues);

                            var labelsEl = document.getElementById('chartLabels');
                            if (labelsEl && chartMonths.length) {
                                labelsEl.innerHTML = chartMonths.map(function(m) {
                                    return '<span>' + m + '</span>';
                                }).join('');
                            }
                        }
                    });
            }

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
