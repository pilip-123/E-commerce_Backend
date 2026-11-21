@extends('layouts.admin')

@section('title', __('Inventory Valuation'))

@section('content')
<div class="container-fluid p-0">
    @include('admin.partials.kpi-styles')
    @php
        $productCount = $products->count();
        $units = $products->sum('stock');
        $valueOf = fn ($p) => $p->stock * (float) ($p->unit_cost ?? $p->price);
        $topProduct = $products->sortByDesc($valueOf)->first();
        $topShare = $topProduct && $totalValue > 0 ? $valueOf($topProduct) / $totalValue * 100 : 0;
        $categoryValues = $products->groupBy(fn ($p) => $p->category->name ?? __('Uncategorized'))->map(fn ($g) => $g->sum($valueOf))->sortDesc();
    @endphp
    {{-- Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="app-kpi kpi-green">
                <div class="app-kpi-head"><span class="app-kpi-icon"><i class="bi bi-cash-stack"></i></span><span class="app-kpi-label">{{ __('Total Inventory Value') }}</span><span class="app-kpi-chip" title="{{ __('Categories') }}"><i class="bi bi-tags"></i>{{ $categoryValues->count() }}</span></div>
                <div class="app-kpi-value">${{ number_format($totalValue, 2) }}</div>
                <div class="app-kpi-foot"><div class="app-kpi-caption">
                    @php $palette = ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#94a3b8']; $top = $categoryValues->take(4); $rest = $categoryValues->slice(4)->sum(); @endphp
                    <div class="app-kpi-meter">
                        @foreach ($top as $name => $value)<span style="width:{{ $totalValue > 0 ? $value / $totalValue * 100 : 0 }}%;background:{{ $palette[$loop->index] }}" title="{{ $name }}: ${{ number_format($value, 2) }}"></span>@endforeach
                        @if ($rest > 0)<span style="width:{{ $rest / $totalValue * 100 }}%;background:{{ $palette[4] }}" title="{{ __('Other') }}: ${{ number_format($rest, 2) }}"></span>@endif
                    </div>
                    <div class="app-kpi-legend">@foreach ($top as $name => $value)<span><i style="background:{{ $palette[$loop->index] }}"></i>{{ \Illuminate\Support\Str::limit($name, 14) }}</span>@endforeach @if ($rest > 0)<span><i style="background:{{ $palette[4] }}"></i>{{ __('Other') }}</span>@endif</div>
                </div></div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="app-kpi kpi-sky">
                <div class="app-kpi-head"><span class="app-kpi-icon"><i class="bi bi-box-seam"></i></span><span class="app-kpi-label">{{ __('Products in Stock') }}</span><span class="app-kpi-chip" title="{{ __('Units in stock') }}"><i class="bi bi-boxes"></i>{{ number_format($units) }}</span></div>
                <div class="app-kpi-value">{{ number_format($productCount) }}</div>
                <div class="app-kpi-foot"><div class="app-kpi-caption">{{ number_format($units) }} {{ __('units on hand') }} · {{ __('avg') }} {{ $productCount ? number_format($units / $productCount, 1) : 0 }} {{ __('per product') }}</div></div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="app-kpi kpi-amber">
                <div class="app-kpi-head"><span class="app-kpi-icon"><i class="bi bi-calculator"></i></span><span class="app-kpi-label">{{ __('Average Value/Product') }}</span>@if ($topProduct)<span class="app-kpi-chip warn" title="{{ __('Share of total value held by the most valuable product') }}"><i class="bi bi-trophy"></i>{{ round($topShare) }}%</span>@endif</div>
                <div class="app-kpi-value">${{ $productCount > 0 ? number_format($totalValue / $productCount, 2) : '0.00' }}</div>
                <div class="app-kpi-foot"><div class="app-kpi-caption">@if ($topProduct){{ __('Most valuable') }}: <strong>{{ \Illuminate\Support\Str::limit($topProduct->name, 24) }}</strong> (${{ number_format($valueOf($topProduct), 2) }})<div class="app-kpi-meter"><span style="width:{{ $topShare }}%"></span></div>@else{{ __('No products in stock') }}@endif</div></div>
            </div>
        </div>
    </div>

    {{-- Valuation Table --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4">
            <h5 class="fw-bold mb-0">{{ __('Inventory Valuation Detail') }}</h5>
            <small class="text-muted">{{ $products->count() }} {{ __('products with stock') }}</small>
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
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Selling Price') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Total Cost') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Total Value (Price)') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Potential Profit') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            @php
                                $unitCost = (float)($product->unit_cost ?? $product->price);
                                $totalCost = $product->stock * $unitCost;
                                $totalValueAtPrice = $product->stock * (float)$product->price;
                                $profit = $totalValueAtPrice - $totalCost;
                            @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                                 class="rounded-3 border flex-shrink-0" style="width: 36px; height: 36px; object-fit: cover;">
                                        @else
                                            <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-light border text-muted flex-shrink-0"
                                                  style="width: 36px; height: 36px; font-size: 9px;">{{ __('N/A') }}</span>
                                        @endif
                                        <span class="fw-semibold">{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">
                                        {{ $product->category->name ?? __('N/A') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-end fw-bold">{{ $product->stock }}</td>
                                <td class="px-4 py-3 text-end">
                                    ${{ number_format($unitCost, 2) }}
                                    @if (!$product->unit_cost)
                                        <span class="text-muted small">{{ __('(price)') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">${{ number_format($product->price, 2) }}</td>
                                <td class="px-4 py-3 text-end fw-semibold">${{ number_format($totalCost, 2) }}</td>
                                <td class="px-4 py-3 text-end fw-semibold">${{ number_format($totalValueAtPrice, 2) }}</td>
                                <td class="px-4 py-3 text-end fw-bold {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                                    ${{ number_format($profit, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-5 text-center text-muted">{{ __('No products with stock found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @php
                        $totalStock = $products->sum('stock');
                        $totalCostSum = $products->sum(fn($p) => $p->stock * (float)($p->unit_cost ?? $p->price));
                        $totalValueSum = $products->sum(fn($p) => $p->stock * (float)$p->price);
                        $avgUnitCost = $totalStock > 0 ? $totalCostSum / $totalStock : 0;
                        $avgSellingPrice = $totalStock > 0 ? $totalValueSum / $totalStock : 0;
                    @endphp
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2" class="px-4 py-3">{{ __('Total') }}</td>
                            <td class="px-4 py-3 text-end">{{ $totalStock }}</td>
                            <td class="px-4 py-3 text-end">${{ number_format($avgUnitCost, 2) }}</td>
                            <td class="px-4 py-3 text-end">${{ number_format($avgSellingPrice, 2) }}</td>
                            <td class="px-4 py-3 text-end">${{ number_format($totalCostSum, 2) }}</td>
                            <td class="px-4 py-3 text-end">${{ number_format($totalValueSum, 2) }}</td>
                            <td class="px-4 py-3 text-end">${{ number_format($totalValueSum - $totalCostSum, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
