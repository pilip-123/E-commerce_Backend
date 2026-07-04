@extends('layouts.admin')

@section('title', 'Inventory Management')

@section('content')
<div class="container-fluid p-0">
    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-box-seam text-success fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Total Products</p>
                        <h5 class="fw-bold mb-0">{{ $totalProducts }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-warning-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-exclamation-triangle text-warning fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Low Stock</p>
                        <h5 class="fw-bold mb-0">{{ $lowStockCount }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-danger-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-x-circle text-danger fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Out of Stock</p>
                        <h5 class="fw-bold mb-0">{{ $outOfStockCount }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-currency-dollar text-info fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Inventory Value</p>
                        <h5 class="fw-bold mb-0">${{ number_format($totalValue, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('admin.inventory.stock-in') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center py-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-success-subtle mb-3" style="width: 56px; height: 56px;">
                            <i class="bi bi-plus-circle text-success fs-3"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Stock In</h6>
                        <p class="text-muted small mb-0">Add inventory to products</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('admin.inventory.stock-out') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center py-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-danger-subtle mb-3" style="width: 56px; height: 56px;">
                            <i class="bi bi-dash-circle text-danger fs-3"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Stock Out</h6>
                        <p class="text-muted small mb-0">Remove inventory from products</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('admin.inventory.transfer') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center py-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-info-subtle mb-3" style="width: 56px; height: 56px;">
                            <i class="bi bi-arrow-left-right text-info fs-3"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Transfer</h6>
                        <p class="text-muted small mb-0">Move stock between products</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('admin.inventory.adjustment') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center py-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-warning-subtle mb-3" style="width: 56px; height: 56px;">
                            <i class="bi bi-sliders text-warning fs-3"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Adjustment</h6>
                        <p class="text-muted small mb-0">Correct stock quantities</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('admin.inventory.stock-count') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center py-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle mb-3" style="width: 56px; height: 56px;">
                            <i class="bi bi-clipboard-data text-primary fs-3"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Stock Count</h6>
                        <p class="text-muted small mb-0">Physical inventory count</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('admin.inventory.history') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center py-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-secondary-subtle mb-3" style="width: 56px; height: 56px;">
                            <i class="bi bi-clock-history text-secondary fs-3"></i>
                        </div>
                        <h6 class="fw-bold mb-1">History</h6>
                        <p class="text-muted small mb-0">View all inventory transactions</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('admin.inventory.valuation') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center py-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-success-subtle mb-3" style="width: 56px; height: 56px;">
                            <i class="bi bi-cash-stack text-success fs-3"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Valuation</h6>
                        <p class="text-muted small mb-0">Inventory worth &amp; cost analysis</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('admin.inventory.index') }}?low=1" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center py-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-danger-subtle mb-3" style="width: 56px; height: 56px;">
                            <i class="bi bi-bell text-danger fs-3"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Low Stock Warning</h6>
                        <p class="text-muted small mb-0">Products needing attention</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Product Stock Table --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0">Current Stock Levels</h5>
                <small class="text-muted">{{ $products->total() }} products</small>
            </div>
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
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">Total Value</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-center">Status</th>
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
                                                  style="width: 40px; height: 40px; font-size: 10px;">N/A</span>
                                        @endif
                                        <span class="fw-semibold">{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">
                                        {{ $product->category->name ?? 'Uncategorized' }}
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
                                        <span class="badge bg-danger-subtle text-danger-emphasis px-3 py-2">Out of Stock</span>
                                    @elseif ($product->stock <= 3)
                                        <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-2">Low Stock</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">In Stock</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-5 text-center text-muted">No products found.</td>
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
