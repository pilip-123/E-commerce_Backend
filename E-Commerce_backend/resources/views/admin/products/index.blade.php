@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0">Products</h5>
                <small class="text-muted">{{ $products->total() }} total</small>
            </div>
            <div class="d-flex gap-2">
                @include('admin.partials.export-dropdown', ['exportRoute' => route('admin.export.products')])
                <a href="{{ route('admin.products.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>New Product
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 small fw-bold text-uppercase">ID</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Image</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Name</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Category</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Price</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Stock</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td class="px-4 py-3 fw-semibold text-muted small">#{{ $product->id }}</td>
                                <td class="px-4 py-3">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                             class="rounded-3 border" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-light border text-muted"
                                              style="width: 50px; height: 50px; font-size: 11px;">N/A</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 fw-semibold">{{ $product->name }}</td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">
                                        {{ $product->category->name ?? 'Uncategorized' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 fw-bold">
                                    @php $discountPrice = $product->getDiscountPrice(); @endphp
                                    @if ($discountPrice)
                                        <s class="text-muted small">${{ number_format($product->price, 2) }}</s>
                                        <span class="text-danger">${{ number_format($discountPrice, 2) }}</span>
                                    @else
                                        <span class="text-success">${{ number_format($product->price, 2) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($product->stock > 0)
                                        <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">{{ $product->stock }} in stock</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger-emphasis px-3 py-2">Out of stock</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('admin.products.show', $product->id) }}"
                                           class="btn btn-sm btn-outline-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product->id) }}"
                                           class="btn btn-sm btn-outline-success" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                data-url="{{ route('admin.products.destroy', $product->id) }}" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-5 text-center text-muted">No products found.</td>
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
