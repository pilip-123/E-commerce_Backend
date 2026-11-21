@extends('layouts.admin')

@section('title', __('Inventory Management'))

@section('content')
<div class="container-fluid p-0">
    @include('admin.partials.kpi-styles')
    @php
        $inStockCount = max(0, $totalProducts - $lowStockCount - $outOfStockCount);
        $share = fn ($n) => $totalProducts > 0 ? round($n / $totalProducts * 100, 1) : 0;
        $healthy = $share($inStockCount);
    @endphp
    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="app-kpi kpi-green">
                <div class="app-kpi-head"><span class="app-kpi-icon"><i class="bi bi-box-seam"></i></span><span class="app-kpi-label">{{ __('Total Products') }}</span><span class="app-kpi-chip {{ $healthy >= 80 ? 'good' : ($healthy >= 50 ? 'warn' : 'bad') }}" title="{{ __('Products with healthy stock') }}"><i class="bi bi-heart-pulse"></i>{{ round($healthy) }}%</span></div>
                <div class="app-kpi-value">{{ number_format($totalProducts) }}</div>
                <div class="app-kpi-foot"><div class="app-kpi-caption">
                    <div class="app-kpi-meter" role="img" aria-label="{{ __('In stock') }} {{ $inStockCount }}, {{ __('Low stock') }} {{ $lowStockCount }}, {{ __('Out of stock') }} {{ $outOfStockCount }}">
                        @if ($inStockCount)<span style="width:{{ $share($inStockCount) }}%;background:#10b981"></span>@endif
                        @if ($lowStockCount)<span style="width:{{ $share($lowStockCount) }}%;background:#f59e0b"></span>@endif
                        @if ($outOfStockCount)<span style="width:{{ $share($outOfStockCount) }}%;background:#ef4444"></span>@endif
                    </div>
                    <div class="app-kpi-legend"><span><i style="background:#10b981"></i>{{ $inStockCount }} {{ __('in stock') }}</span><span><i style="background:#f59e0b"></i>{{ $lowStockCount }} {{ __('low') }}</span><span><i style="background:#ef4444"></i>{{ $outOfStockCount }} {{ __('out') }}</span></div>
                </div></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.inventory.index', ['stock_status' => 'low']) }}" class="app-kpi kpi-amber text-decoration-none">
                <div class="app-kpi-head"><span class="app-kpi-icon"><i class="bi bi-exclamation-triangle"></i></span><span class="app-kpi-label">{{ __('Low Stock') }}</span><span class="app-kpi-chip {{ $lowStockCount ? 'warn' : 'good' }}">{{ round($share($lowStockCount)) }}%</span></div>
                <div class="app-kpi-value">{{ number_format($lowStockCount) }}</div>
                <div class="app-kpi-foot"><div class="app-kpi-caption">{{ $lowStockCount ? __('10 units or fewer — reorder soon') : __('No products running low') }}<div class="app-kpi-meter"><span style="width:{{ $share($lowStockCount) }}%"></span></div></div><i class="bi bi-arrow-right text-muted" aria-hidden="true"></i></div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.inventory.index', ['stock_status' => 'out']) }}" class="app-kpi kpi-red text-decoration-none">
                <div class="app-kpi-head"><span class="app-kpi-icon"><i class="bi bi-x-octagon"></i></span><span class="app-kpi-label">{{ __('Out of Stock') }}</span><span class="app-kpi-chip {{ $outOfStockCount ? 'bad' : 'good' }}">{{ $outOfStockCount ? round($share($outOfStockCount)) . '%' : __('All stocked') }}</span></div>
                <div class="app-kpi-value">{{ number_format($outOfStockCount) }}</div>
                <div class="app-kpi-foot"><div class="app-kpi-caption">{{ $outOfStockCount ? __('Customers cannot buy these now') : __('Every product is available') }}<div class="app-kpi-meter"><span style="width:{{ $share($outOfStockCount) }}%"></span></div></div><i class="bi bi-arrow-right text-muted" aria-hidden="true"></i></div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.inventory.valuation') }}" class="app-kpi kpi-blue text-decoration-none">
                <div class="app-kpi-head"><span class="app-kpi-icon"><i class="bi bi-currency-dollar"></i></span><span class="app-kpi-label">{{ __('Inventory Value') }}</span><span class="app-kpi-chip" title="{{ __('Units in stock') }}"><i class="bi bi-boxes"></i>{{ number_format($totalUnits) }}</span></div>
                <div class="app-kpi-value">${{ number_format($totalValue, 2) }}</div>
                <div class="app-kpi-foot"><div class="app-kpi-caption">{{ __('Avg') }} ${{ number_format($totalUnits ? $totalValue / $totalUnits : 0, 2) }} {{ __('per unit') }} · {{ number_format($totalUnits) }} {{ __('units') }}</div><i class="bi bi-arrow-right text-muted" aria-hidden="true"></i></div>
            </a>
        </div>
    </div>

    {{-- Action Cards --}}
    <div class="d-flex align-items-end justify-content-between mb-2">
        <div><h2 class="h6 fw-bold mb-0">{{ __('Inventory tools') }}</h2><small class="text-muted">{{ __('Move, count and review your stock') }}</small></div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.inventory.stock-in') }}" class="feature-tile" style="--tile:#10b981">
                <span class="feature-tile-top"><span class="feature-tile-icon"><i class="bi bi-plus-circle"></i></span><i class="bi bi-arrow-right feature-tile-arrow" aria-hidden="true"></i></span>
                <span class="feature-tile-title">{{ __('Stock In') }}</span>
                <span class="feature-tile-sub">{{ __('Add inventory to products') }}</span>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.inventory.stock-out') }}" class="feature-tile" style="--tile:#ef4444">
                <span class="feature-tile-top"><span class="feature-tile-icon"><i class="bi bi-dash-circle"></i></span><i class="bi bi-arrow-right feature-tile-arrow" aria-hidden="true"></i></span>
                <span class="feature-tile-title">{{ __('Stock Out') }}</span>
                <span class="feature-tile-sub">{{ __('Remove inventory from products') }}</span>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.inventory.transfer') }}" class="feature-tile" style="--tile:#0ea5e9">
                <span class="feature-tile-top"><span class="feature-tile-icon"><i class="bi bi-arrow-left-right"></i></span><i class="bi bi-arrow-right feature-tile-arrow" aria-hidden="true"></i></span>
                <span class="feature-tile-title">{{ __('Transfer') }}</span>
                <span class="feature-tile-sub">{{ __('Move stock between products') }}</span>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.inventory.adjustment') }}" class="feature-tile" style="--tile:#f59e0b">
                <span class="feature-tile-top"><span class="feature-tile-icon"><i class="bi bi-sliders"></i></span><i class="bi bi-arrow-right feature-tile-arrow" aria-hidden="true"></i></span>
                <span class="feature-tile-title">{{ __('Adjustment') }}</span>
                <span class="feature-tile-sub">{{ __('Correct stock quantities') }}</span>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.inventory.stock-count') }}" class="feature-tile" style="--tile:#6366f1">
                <span class="feature-tile-top"><span class="feature-tile-icon"><i class="bi bi-clipboard-data"></i></span><i class="bi bi-arrow-right feature-tile-arrow" aria-hidden="true"></i></span>
                <span class="feature-tile-title">{{ __('Stock Count') }}</span>
                <span class="feature-tile-sub">{{ __('Physical inventory count') }}</span>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.inventory.history') }}" class="feature-tile" style="--tile:#64748b">
                <span class="feature-tile-top"><span class="feature-tile-icon"><i class="bi bi-clock-history"></i></span><i class="bi bi-arrow-right feature-tile-arrow" aria-hidden="true"></i></span>
                <span class="feature-tile-title">{{ __('History') }}</span>
                <span class="feature-tile-sub">{{ __('View all inventory transactions') }}</span>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.inventory.valuation') }}" class="feature-tile" style="--tile:#14b8a6">
                <span class="feature-tile-top"><span class="feature-tile-icon"><i class="bi bi-cash-stack"></i></span><i class="bi bi-arrow-right feature-tile-arrow" aria-hidden="true"></i></span>
                <span class="feature-tile-title">{{ __('Valuation') }}</span>
                <span class="feature-tile-sub">{{ __('Inventory worth & cost analysis') }}</span><span class="feature-tile-badge">${{ number_format($totalValue, 0) }}</span>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.inventory.index') }}?low=1" class="feature-tile" style="--tile:#e11d48">
                <span class="feature-tile-top"><span class="feature-tile-icon"><i class="bi bi-bell"></i></span><i class="bi bi-arrow-right feature-tile-arrow" aria-hidden="true"></i></span>
                <span class="feature-tile-title">{{ __('Low Stock Warning') }}</span>
                <span class="feature-tile-sub">{{ __('Products needing attention') }}</span><span class="feature-tile-badge">@if($lowStockCount + $outOfStockCount){{ $lowStockCount + $outOfStockCount }} {{ __('need attention') }}@else{{ __('All good') }}@endif</span>
            </a>
        </div>
    </div>

    {{-- Product Stock Table --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0">{{ __('Current Stock Levels') }}</h5>
                <small class="text-muted">{{ $products->total() }} {{ __('products') }}</small>
            </div>
        </div>
        <div class="card-body border-bottom px-3 py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-auto">
                    <input type="search" name="search" class="form-control form-control-sm" value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <select name="category_id" class="form-select form-select-sm">
                        <option value="">{{ __('All Categories') }}</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <select name="stock_status" class="form-select form-select-sm">
                        <option value="">{{ __('All Stock') }}</option>
                        <option value="in" {{ request('stock_status') === 'in' ? 'selected' : '' }}>{{ __('In Stock') }}</option>
                        <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>{{ __('Low Stock') }}</option>
                        <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>{{ __('Out of Stock') }}</option>
                    </select>
                </div>
                <div class="col-auto d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-funnel me-1"></i>{{ __('Filter') }}</button>
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Product') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Category') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Stock') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Unit Cost') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Total Value') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                                 class="rounded-3 border flex-shrink-0" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-light border text-muted flex-shrink-0"
                                                  style="width: 40px; height: 40px; font-size: 10px;">{{ __('N/A') }}</span>
                                        @endif
                                        <span class="fw-semibold">{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">
                                        {{ $product->category->name ?? __('Uncategorized') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-end fw-bold">{{ $product->stock }}</td>
                                <td class="px-4 py-3 text-end text-muted">
                                    @if ($product->unit_cost)
                                        ${{ number_format($product->unit_cost, 2) }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end fw-semibold">
                                    ${{ number_format($product->stock * (float)($product->unit_cost ?? $product->price), 2) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($product->stock <= 0)
                                        <span class="badge bg-danger-subtle text-danger-emphasis px-3 py-2">{{ __('Out of Stock') }}</span>
                                    @elseif ($product->stock <= 10)
                                        <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-2">{{ __('Low Stock') }}</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">{{ __('In Stock') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-5 text-center text-muted">{{ __('No products found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($products->hasPages())
            <div class="card-footer bg-white py-3 rounded-4 border-0">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
