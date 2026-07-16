@extends('layouts.admin')

@section('title', __('Products'))

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0">{{ __('Products') }}</h5>
                <small class="text-muted">{{ $products->total() }} {{ __('total') }}</small>
            </div>
            <div class="d-flex gap-2">
                @include('admin.partials.export-dropdown', ['exportRoute' => route('admin.export.products')])
                <a href="{{ route('admin.products.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>{{ __('New Product') }}
                </a>
            </div>
        </div>
        <div class="card-body border-bottom px-3 py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-auto">
                    <input type="search" name="search" class="form-control form-control-sm" placeholder="{{ __('Search by name or slug...') }}" value="{{ request('search') }}">
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
                    <select name="status" class="form-select form-select-sm">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>
                <div class="col-auto d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-funnel me-1"></i>{{ __('Filter') }}</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('ID') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Image') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Name') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Category') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Price') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Stock') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Actions') }}</th>
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
                                              style="width: 50px; height: 50px; font-size: 11px;">{{ __('N/A') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 fw-semibold">{{ $product->name }}</td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">
                                        {{ $product->category->name ?? __('Uncategorized') }}
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
                                        <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">{{ $product->stock }} {{ __('in stock') }}</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger-emphasis px-3 py-2">{{ __('Out of stock') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('admin.products.show', $product->id) }}"
                                           class="btn btn-sm btn-outline-info" title="{{ __('View') }}">
                                             <i class="bi bi-eye"></i>
                                         </a>
                                         <a href="{{ route('admin.products.edit', $product->id) }}"
                                            class="btn btn-sm btn-outline-success" title="{{ __('Edit') }}">
                                             <i class="bi bi-pencil"></i>
                                         </a>
                                         <button type="button" class="btn btn-sm btn-outline-danger"
                                                 data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                 data-url="{{ route('admin.products.destroy', $product->id) }}" title="{{ __('Delete') }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-5 text-center text-muted">{{ __('No products found.') }}</td>
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
