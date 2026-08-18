@extends('layouts.admin')

@section('title', __('Suppliers'))

@section('content')
<div class="container-fluid p-0">

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-truck text-success fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Total Suppliers') }}</p>
                        <h5 class="fw-bold mb-0">{{ $suppliers->total() }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-receipt text-info fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Purchase Orders') }}</p>
                        <h5 class="fw-bold mb-0">{{ $totalOrders }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-warning-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-cash-coin text-warning fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Total Purchases') }}</p>
                        <h5 class="fw-bold mb-0">${{ number_format($totalSpend, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h5 class="fw-bold mb-0">{{ __('All Suppliers') }}</h5>
                <small class="text-muted">{{ $suppliers->total() }} {{ __('total') }}</small>
            </div>
            <div class="d-flex gap-2">
                @include('admin.partials.export-dropdown', ['exportRoute' => route('admin.export.suppliers')])
                @if (auth()->user()->hasPermission('suppliers.create'))
                    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-plus-circle me-1"></i>{{ __('Add Supplier') }}
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body border-bottom px-3 py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-auto flex-grow-1" style="min-width: 200px;">
                    <input type="search" name="search" class="form-control form-control-sm" placeholder="{{ __('Search by name, company, contact, email...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>
                <div class="col-auto d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-funnel me-1"></i>{{ __('Filter') }}</button>
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 small fw-bold text-uppercase">#</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Supplier') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Contact') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Company') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Orders') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Total Spent') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Status') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $supplier)
                            <tr>
                                <td class="px-4 py-3 fw-semibold text-muted small">#{{ $supplier->id }}</td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($supplier->image)
                                            <img src="{{ asset('storage/' . $supplier->image) }}" alt="{{ $supplier->name }}" class="rounded-circle object-fit-cover flex-shrink-0" style="width: 36px; height: 36px;">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold flex-shrink-0" style="width: 36px; height: 36px; font-size: 13px; background: var(--admin-primary);">
                                                {{ strtoupper(substr($supplier->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('admin.suppliers.show', $supplier) }}" class="fw-semibold small text-decoration-none">{{ $supplier->name }}</a>
                                            <div class="text-muted" style="font-size: 11px;">{{ $supplier->email ?: '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="fw-semibold small d-block">{{ $supplier->contact_person ?: '—' }}</span>
                                    <span class="text-muted" style="font-size: 11px;">{{ $supplier->phone ?: '' }}</span>
                                </td>
                                <td class="px-4 py-3 text-muted small">{{ $supplier->company ?: '—' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="badge bg-success-subtle text-success-emphasis rounded-pill">{{ $supplier->purchase_orders_count }}</span>
                                </td>
                                <td class="px-4 py-3 text-end fw-bold">${{ number_format($supplier->purchase_orders_sum_grand_total ?? 0, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if ($supplier->status)
                                        <span class="badge rounded-pill" style="background: var(--badge-success-bg); color: var(--badge-success-text);">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge rounded-pill" style="background: var(--badge-gray-bg); color: var(--badge-gray-text);">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('View') }}"><i class="bi bi-eye"></i></a>
                                    @if (auth()->user()->hasPermission('suppliers.edit'))
                                        <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-success" title="{{ __('Edit') }}"><i class="bi bi-pencil"></i></a>
                                    @endif
                                    @if (auth()->user()->hasPermission('suppliers.delete') && $supplier->purchase_orders_count === 0)
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-url="{{ route('admin.suppliers.destroy', $supplier) }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-5 text-center text-muted">
                                    <i class="bi bi-truck fs-2 d-block mb-2 text-muted"></i>
                                    {{ __('No suppliers found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($suppliers->hasPages())
            <div class="card-footer bg-white py-3 rounded-4 border-0">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
