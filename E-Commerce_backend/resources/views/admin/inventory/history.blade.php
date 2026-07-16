@extends('layouts.admin')

@section('title', __('Inventory History'))

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3 rounded-4">
            <h5 class="fw-bold mb-0">{{ __('Filter Transactions') }}</h5>
        </div>
        <div class="card-body p-3">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="type" class="form-select form-select-sm">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="stock_in" @selected(request('type') == 'stock_in')>{{ __('Stock In') }}</option>
                        <option value="stock_out" @selected(request('type') == 'stock_out')>{{ __('Stock Out') }}</option>
                        <option value="transfer_out" @selected(request('type') == 'transfer_out')>{{ __('Transfer Out') }}</option>
                        <option value="transfer_in" @selected(request('type') == 'transfer_in')>{{ __('Transfer In') }}</option>
                        <option value="adjustment" @selected(request('type') == 'adjustment')>{{ __('Adjustment') }}</option>
                        <option value="stock_count" @selected(request('type') == 'stock_count')>{{ __('Stock Count') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="product_id" class="form-select form-select-sm">
                        <option value="">{{ __('All Products') }}</option>
                        @foreach ($products as $p)
                            <option value="{{ $p->id }}" @selected(request('product_id') == $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-success w-100"><i class="bi bi-funnel me-1"></i>{{ __('Filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0">{{ __('Transaction History') }}</h5>
                <small class="text-muted">{{ $transactions->total() }} {{ __('records') }}</small>
            </div>
            <div class="d-flex gap-2">
                @include('admin.partials.export-dropdown', ['exportRoute' => route('admin.export.inventory-history')])
                @if (auth()->user()->isAdmin())
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#clearHistoryModal">
                        <i class="bi bi-trash me-1"></i>{{ __('Clear History') }}
                    </button>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Date') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Type') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Product') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Qty') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Before') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('After') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Reference') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('By') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Notes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $t)
                            <tr>
                                <td class="px-4 py-3 text-muted small">{{ $t->created_at->format('M d, Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $typeBadges = [
                                            'stock_in' => ['bg-success-subtle text-success-emphasis', 'Stock In'],
                                            'stock_out' => ['bg-danger-subtle text-danger-emphasis', 'Stock Out'],
                                            'transfer_out' => ['bg-info-subtle text-info-emphasis', 'Transfer Out'],
                                            'transfer_in' => ['bg-info-subtle text-info-emphasis', 'Transfer In'],
                                            'adjustment' => ['bg-warning-subtle text-warning-emphasis', 'Adjustment'],
                                            'stock_count' => ['bg-primary-subtle text-primary-emphasis', 'Stock Count'],
                                        ];
                                        $badge = $typeBadges[$t->type] ?? ['bg-secondary-subtle text-secondary-emphasis', ucfirst(str_replace('_', ' ', $t->type))];
                                    @endphp
                                    <span class="badge {{ $badge[0] }} px-3 py-2">{{ $badge[1] }}</span>
                                </td>
                                <td class="px-4 py-3 fw-semibold small">{{ $t->product->name ?? __('Deleted') }}</td>
                                <td class="px-4 py-3 text-end fw-bold {{ $t->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $t->quantity > 0 ? '+' . $t->quantity : $t->quantity }}
                                </td>
                                <td class="px-4 py-3 text-end text-muted">{{ $t->stock_before ?? '—' }}</td>
                                <td class="px-4 py-3 text-end fw-semibold">{{ $t->stock_after ?? '—' }}</td>
                                <td class="px-4 py-3 text-muted small">{{ $t->reference ?? '—' }}</td>
                                <td class="px-4 py-3 text-muted small">{{ $t->user->name ?? __('System') }}</td>
                                <td class="px-4 py-3 text-muted small" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $t->notes ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-5 text-center text-muted">{{ __('No transactions found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($transactions->hasPages())
            <div class="card-footer bg-white py-3 rounded-4 border-0">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Clear History Confirmation Modal --}}
<div class="modal fade" id="clearHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger-subtle" style="width: 60px; height: 60px;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                    </span>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Clear All History?') }}</h5>
                <p class="text-muted mb-1">{{ __('This will permanently delete all') }} {{ $transactions->total() }} {{ __('inventory transaction records.') }}</p>
                <p class="text-muted small mb-4">{{ __('This action cannot be undone.') }}</p>
                <form method="POST" action="{{ route('admin.inventory.history.clear') }}">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-secondary px-4 me-2" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger px-4">{{ __('Yes, Clear All') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
