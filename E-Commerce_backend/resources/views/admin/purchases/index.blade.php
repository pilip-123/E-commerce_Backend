@extends('layouts.admin')

@section('title', __('Purchase Orders'))

@section('content')
<div class="container-fluid p-0">

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-receipt text-info fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Total Purchase Orders') }}</p>
                        <h5 class="fw-bold mb-0">{{ $poStats['total'] }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-warning-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-clock-history text-warning fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Pending') }}</p>
                        <h5 class="fw-bold mb-0">{{ $poStats['pending'] }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-box-seam text-success fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Received') }}</p>
                        <h5 class="fw-bold mb-0">{{ $poStats['received'] }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-danger-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-x-circle text-danger fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Cancelled') }}</p>
                        <h5 class="fw-bold mb-0">{{ $poStats['cancelled'] }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h5 class="fw-bold mb-0">{{ __('All Purchase Orders') }}</h5>
                <small class="text-muted">{{ $purchaseOrders->total() }} {{ __('total') }}</small>
            </div>
            <div class="d-flex gap-2">
                @include('admin.partials.export-dropdown', ['exportRoute' => route('admin.export.purchase-orders')])
                @if (auth()->user()->hasPermission('purchases.create'))
                    <a href="{{ route('admin.purchases.create') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-plus-circle me-1"></i>{{ __('Create Purchase Order') }}
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body border-bottom px-3 py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-auto flex-grow-1" style="min-width: 200px;">
                    <input type="search" name="search" class="form-control form-control-sm" placeholder="{{ __('Search PO number or supplier...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <select name="supplier_id" class="form-select form-select-sm">
                        <option value="">{{ __('All Suppliers') }}</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">{{ __('All Statuses') }}</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <select name="payment_status" class="form-select form-select-sm">
                        <option value="">{{ __('All Payments') }}</option>
                        @foreach (['unpaid' => 'Unpaid', 'partial' => 'Partial', 'paid' => 'Paid'] as $value => $label)
                            <option value="{{ $value }}" {{ request('payment_status') === $value ? 'selected' : '' }}>{{ __($label) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" title="{{ __('From') }}">
                </div>
                <div class="col-auto">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" title="{{ __('To') }}">
                </div>
                <div class="col-auto d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-funnel me-1"></i>{{ __('Filter') }}</button>
                    <a href="{{ route('admin.purchases.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                @php
                    $sortUrl = function ($column) {
                        $params = request()->query();
                        $params['sort'] = $column;
                        $params['direction'] = request('sort') === $column && request('direction') === 'asc' ? 'desc' : 'asc';
                        return route('admin.purchases.index', $params);
                    };
                    $sortIcon = function ($column) {
                        if (request('sort') !== $column) return '';
                        return request('direction') === 'asc' ? ' &uarr;' : ' &darr;';
                    };
                @endphp
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 small fw-bold text-uppercase"><a href="{{ $sortUrl('po_number') }}" class="text-decoration-none text-reset">{{ __('PO Number') }}{!! $sortIcon('po_number') !!}</a></th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Supplier') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase"><a href="{{ $sortUrl('order_date') }}" class="text-decoration-none text-reset">{{ __('Order Date') }}{!! $sortIcon('order_date') !!}</a></th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end"><a href="{{ $sortUrl('grand_total') }}" class="text-decoration-none text-reset">{{ __('Grand Total') }}{!! $sortIcon('grand_total') !!}</a></th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Supplier Total') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-center"><a href="{{ $sortUrl('status') }}" class="text-decoration-none text-reset">{{ __('Status') }}{!! $sortIcon('status') !!}</a></th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-center"><a href="{{ $sortUrl('payment_status') }}" class="text-decoration-none text-reset">{{ __('Payment') }}{!! $sortIcon('payment_status') !!}</a></th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purchaseOrders as $po)
                            <tr>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.purchases.show', $po) }}" class="font-monospace fw-bold small text-decoration-none">{{ $po->po_number }}</a>
                                </td>
                                <td class="px-4 py-3 fw-semibold small">{{ $po->supplier->name ?? __('Deleted') }}</td>
                                <td class="px-4 py-3 text-muted small">{{ $po->order_date?->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-end fw-bold">${{ number_format($po->grand_total, 2) }}</td>
                                <td class="px-4 py-3 text-end text-muted small">${{ number_format($po->supplier?->active_total ?? 0, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $badge = match ($po->status) {
                                            'draft' => ['var(--badge-gray-bg)', 'var(--badge-gray-text)'],
                                            'pending' => ['var(--badge-warning-bg)', 'var(--badge-warning-text)'],
                                            'approved' => ['var(--badge-info-bg)', 'var(--badge-info-text)'],
                                            'ordered' => ['var(--badge-info-bg)', 'var(--badge-info-text)'],
                                            'partially_received' => ['var(--badge-warning-bg)', 'var(--badge-warning-text)'],
                                            'received' => ['var(--badge-success-bg)', 'var(--badge-success-text)'],
                                            'cancelled' => ['var(--badge-red-bg)', 'var(--badge-red-text)'],
                                            default => ['var(--badge-gray-bg)', 'var(--badge-gray-text)'],
                                        };
                                    @endphp
                                    <span class="badge rounded-pill text-uppercase" style="font-size: 10px; background: {{ $badge[0] }}; color: {{ $badge[1] }};">{{ str_replace('_', ' ', $po->status) }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $pay = match ($po->payment_status) {
                                            'paid' => ['var(--badge-success-bg)', 'var(--badge-success-text)'],
                                            'partial' => ['var(--badge-warning-bg)', 'var(--badge-warning-text)'],
                                            default => ['var(--badge-gray-bg)', 'var(--badge-gray-text)'],
                                        };
                                    @endphp
                                    <span class="badge rounded-pill text-uppercase" style="font-size: 10px; background: {{ $pay[0] }}; color: {{ $pay[1] }};">{{ $po->payment_status }}</span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('admin.purchases.show', $po) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('View') }}"><i class="bi bi-eye"></i></a>
                                    @if (auth()->user()->hasPermission('purchases.update') && $po->isEditable())
                                        <a href="{{ route('admin.purchases.edit', $po) }}" class="btn btn-sm btn-outline-success" title="{{ __('Edit') }}"><i class="bi bi-pencil"></i></a>
                                    @endif
                                    @if (auth()->user()->hasPermission('purchases.receive') && $po->isReceivable())
                                        <a href="{{ route('admin.purchases.receive', $po) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Receive') }}"><i class="bi bi-box-seam"></i></a>
                                    @endif
                                    @if (auth()->user()->hasPermission('purchases.delete') && (int) $po->received_quantity === 0 && $po->returns_count === 0)
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-url="{{ route('admin.purchases.destroy', $po) }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-5 text-center text-muted">
                                    <i class="bi bi-receipt fs-2 d-block mb-2 text-muted"></i>
                                    {{ __('No purchase orders found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($purchaseOrders->hasPages())
            <div class="card-footer bg-white py-3 rounded-4 border-0">
                {{ $purchaseOrders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
