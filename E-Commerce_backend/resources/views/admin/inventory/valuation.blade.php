@extends('layouts.admin')

@section('title', 'Inventory Valuation')

@section('content')
<div class="container-fluid p-0">
    {{-- Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-cash-stack text-success fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Total Inventory Value</p>
                        <h5 class="fw-bold mb-0">${{ number_format($totalValue, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-box-seam text-info fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Products in Stock</p>
                        <h5 class="fw-bold mb-0">{{ $products->count() }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-warning-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-calculator text-warning fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Average Value/Product</p>
                        <h5 class="fw-bold mb-0">
                            ${{ $products->count() > 0 ? number_format($totalValue / $products->count(), 2) : '0.00' }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Valuation Table --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4">
            <h5 class="fw-bold mb-0">Inventory Valuation Detail</h5>
            <small class="text-muted">{{ $products->count() }} products with stock</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Product</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Category</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">Stock</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">Unit Cost</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">Selling Price</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">Total Cost</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">Total Value (Price)</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">Potential Profit</th>
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
                                                  style="width: 36px; height: 36px; font-size: 9px;">N/A</span>
                                        @endif
                                        <span class="fw-semibold">{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">
                                        {{ $product->category->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-end fw-bold">{{ $product->stock }}</td>
                                <td class="px-4 py-3 text-end">
                                    ${{ number_format($unitCost, 2) }}
                                    @if (!$product->unit_cost)
                                        <span class="text-muted small">(price)</span>
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
                                <td colspan="8" class="px-4 py-5 text-center text-muted">No products with stock found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2" class="px-4 py-3">Total</td>
                            <td class="px-4 py-3 text-end">{{ $products->sum('stock') }}</td>
                            <td class="px-4 py-3 text-end">—</td>
                            <td class="px-4 py-3 text-end">—</td>
                            <td class="px-4 py-3 text-end">${{ number_format($products->sum(fn($p) => $p->stock * (float)($p->unit_cost ?? $p->price)), 2) }}</td>
                            <td class="px-4 py-3 text-end">${{ number_format($products->sum(fn($p) => $p->stock * (float)$p->price), 2) }}</td>
                            <td class="px-4 py-3 text-end">${{ number_format($products->sum(fn($p) => $p->stock * (float)$p->price - $p->stock * (float)($p->unit_cost ?? $p->price)), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
