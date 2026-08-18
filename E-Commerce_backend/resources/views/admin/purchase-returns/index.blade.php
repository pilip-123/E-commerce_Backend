@extends('layouts.admin')

@section('title', __('Purchase Returns'))

@section('content')
<div class="container-fluid p-0">

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-arrow-return-left text-info fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Total Returns') }}</p>
                        <h5 class="fw-bold mb-0">{{ number_format($returnStats['total']) }}</h5>
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
                        <h5 class="fw-bold mb-0">{{ number_format($returnStats['pending']) }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-check2-circle text-success fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Completed') }}</p>
                        <h5 class="fw-bold mb-0">{{ number_format($returnStats['completed']) }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-danger-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-cash-stack text-danger fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Returned Value') }}</p>
                        <h5 class="fw-bold mb-0">${{ number_format($returnStats['totalAmount'], 2) }}</h5>
                        <p class="text-muted mb-0" style="font-size: 11px;">{{ number_format($returnStats['totalUnits']) }} {{ __('units') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Analytics --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3" style="font-size: 15px;">{{ __('Monthly Returns') }}</h5>
                    @php $maxReturn = max($returnChartTotals->max() ?? 0, 1); @endphp
                    <div class="d-flex align-items-end gap-2" style="height: 140px;">
                        @foreach ($returnChartTotals as $i => $total)
                            <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-end h-100" style="min-width: 0;">
                                <div style="width: 100%; max-width: 34px; height: {{ $total > 0 ? max(($total / $maxReturn) * 100, 2) : 2 }}%; border-radius: 6px 6px 0 0; background: linear-gradient(180deg, #f87171, #dc2626);"></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-between small text-muted mt-2 px-1">
                        @foreach ($returnChartMonths as $month)
                            <span style="font-size: 10px;">{{ $month }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3" style="font-size: 15px;">{{ __('Returns by Supplier') }}</h5>
                    @forelse ($returnsBySupplier as $row)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                                <span class="fw-semibold small text-truncate">{{ $row->supplier->name ?? __('Deleted') }}</span>
                                <span class="fw-bold small flex-shrink-0">${{ number_format($row->amount, 2) }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" style="width: {{ ($row->amount / $maxSupplierReturn) * 100 }}%; background: linear-gradient(90deg, #f59e0b, #d97706);"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4 mb-0 small">{{ __('No returns yet.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3" style="font-size: 15px;">{{ __('Returns by Product') }}</h5>
                    @forelse ($returnsByProduct as $row)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                                <span class="fw-semibold small text-truncate">{{ $row->product->name ?? __('Deleted') }}</span>
                                <span class="fw-bold small flex-shrink-0">-{{ number_format($row->quantity) }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" style="width: {{ ($row->quantity / $maxProductReturn) * 100 }}%; background: linear-gradient(90deg, #ef4444, #dc2626);"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4 mb-0 small">{{ __('No returns yet.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h5 class="fw-bold mb-0">{{ __('All Purchase Returns') }}</h5>
                <small class="text-muted">{{ $purchaseReturns->total() }} {{ __('total') }}</small>
            </div>
            <div class="d-flex gap-2">
                @include('admin.partials.export-dropdown', ['exportRoute' => route('admin.export.purchase-returns')])
                @if (auth()->user()->hasPermission('purchase_returns.view'))
                    <a href="{{ route('admin.purchase-returns.create') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-plus-circle me-1"></i>{{ __('Create Return') }}
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body border-bottom px-3 py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-auto flex-grow-1" style="min-width: 200px;">
                    <input type="search" name="search" class="form-control form-control-sm" placeholder="{{ __('Search return number, PO or supplier...') }}" value="{{ request('search') }}">
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
                        @foreach (['pending', 'approved', 'completed', 'cancelled'] as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
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
                    <a href="{{ route('admin.purchase-returns.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Return Number') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Purchase Order') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Supplier') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Return Date') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Total') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Status') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purchaseReturns as $return)
                            <tr>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.purchase-returns.show', $return) }}" class="font-monospace fw-bold small text-decoration-none">{{ $return->return_number }}</a>
                                </td>
                                <td class="px-4 py-3 font-monospace small text-muted">{{ $return->purchaseOrder->po_number ?? '—' }}</td>
                                <td class="px-4 py-3 fw-semibold small">{{ $return->supplier->name ?? __('Deleted') }}</td>
                                <td class="px-4 py-3 text-muted small">{{ $return->return_date?->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-end fw-bold">${{ number_format($return->total_amount, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $badge = match ($return->status) {
                                            'pending' => ['var(--badge-warning-bg)', 'var(--badge-warning-text)'],
                                            'approved' => ['var(--badge-info-bg)', 'var(--badge-info-text)'],
                                            'completed' => ['var(--badge-success-bg)', 'var(--badge-success-text)'],
                                            'cancelled' => ['var(--badge-red-bg)', 'var(--badge-red-text)'],
                                            default => ['var(--badge-gray-bg)', 'var(--badge-gray-text)'],
                                        };
                                    @endphp
                                    <span class="badge rounded-pill text-uppercase" style="font-size: 10px; background: {{ $badge[0] }}; color: {{ $badge[1] }};">{{ $return->status }}</span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('admin.purchase-returns.show', $return) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('View') }}"><i class="bi bi-eye"></i></a>
                                    @if (auth()->user()->hasPermission('purchase_returns.delete') && in_array($return->status, ['pending', 'cancelled']))
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-url="{{ route('admin.purchase-returns.destroy', $return) }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-5 text-center text-muted">
                                    <i class="bi bi-arrow-return-left fs-2 d-block mb-2 text-muted"></i>
                                    {{ __('No purchase returns found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($purchaseReturns->hasPages())
            <div class="card-footer bg-white py-3 rounded-4 border-0">
                {{ $purchaseReturns->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
