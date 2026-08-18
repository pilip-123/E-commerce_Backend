@extends('layouts.admin')

@section('title', __('Purchase Return') . ' ' . $purchaseReturn->return_number)

@section('content')
<div class="container-fluid p-0">

    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 font-monospace">{{ $purchaseReturn->return_number }}</h4>
            <p class="text-muted small mb-0">
                {{ $purchaseReturn->supplier->name ?? __('Deleted') }} · {{ $purchaseReturn->return_date?->format('M d, Y') }}
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if (auth()->user()->hasPermission('purchase_returns.approve') && $purchaseReturn->status === 'pending')
                <form action="{{ route('admin.purchase-returns.approve', $purchaseReturn) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-check2-circle me-1"></i>{{ __('Approve') }}
                    </button>
                </form>
            @endif
            @if (auth()->user()->hasPermission('purchase_returns.complete') && $purchaseReturn->status === 'approved')
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#completeModal">
                    <i class="bi bi-check-lg me-1"></i>{{ __('Complete Return') }}
                </button>
            @endif
            @if (auth()->user()->hasPermission('purchase_returns.cancel') && in_array($purchaseReturn->status, ['pending', 'approved']))
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelReturnModal">
                    <i class="bi bi-x-circle me-1"></i>{{ __('Cancel Return') }}
                </button>
            @endif
            @if (auth()->user()->hasPermission('purchase_returns.delete') && in_array($purchaseReturn->status, ['pending', 'cancelled']))
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-url="{{ route('admin.purchase-returns.destroy', $purchaseReturn) }}">
                    <i class="bi bi-trash me-1"></i>{{ __('Delete') }}
                </button>
            @endif
            <a href="{{ route('admin.purchase-returns.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Items --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 rounded-4">
                    <h5 class="fw-bold mb-0 fs-6"><i class="bi bi-arrow-return-left me-2 text-danger"></i>{{ __('Return Items') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Product') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('SKU') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Quantity') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Unit Cost') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Reason') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseReturn->items as $item)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <span class="fw-semibold small">{{ $item->product->name ?? __('Deleted') }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-muted small font-monospace">{{ $item->product->sku ?? '—' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="badge rounded-pill" style="background: var(--badge-red-bg); color: var(--badge-red-text);">-{{ $item->quantity }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-end">${{ number_format($item->unit_cost, 2) }}</td>
                                        <td class="px-4 py-3 text-muted small">{{ $item->reason ?: '—' }}</td>
                                        <td class="px-4 py-3 text-end fw-bold">${{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td colspan="5" class="px-4 py-3 text-end fw-bold text-uppercase small">{{ __('Total Amount') }}</td>
                                    <td class="px-4 py-3 text-end fw-bold fs-6" style="color: var(--admin-primary);">${{ number_format($purchaseReturn->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Summary --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 rounded-4">
                    <h5 class="fw-bold mb-0 fs-6"><i class="bi bi-info-circle me-2 text-success"></i>{{ __('Return Summary') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">{{ __('Status') }}</span>
                        @php
                            $badge = match ($purchaseReturn->status) {
                                'pending' => ['var(--badge-warning-bg)', 'var(--badge-warning-text)'],
                                'approved' => ['var(--badge-info-bg)', 'var(--badge-info-text)'],
                                'completed' => ['var(--badge-success-bg)', 'var(--badge-success-text)'],
                                'cancelled' => ['var(--badge-red-bg)', 'var(--badge-red-text)'],
                                default => ['var(--badge-gray-bg)', 'var(--badge-gray-text)'],
                            };
                        @endphp
                        <span class="badge rounded-pill text-uppercase" style="background: {{ $badge[0] }}; color: {{ $badge[1] }};">{{ $purchaseReturn->status }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">{{ __('Purchase Order') }}</span>
                        <a href="{{ route('admin.purchases.show', $purchaseReturn->purchaseOrder) }}" class="fw-semibold small font-monospace text-decoration-none">{{ $purchaseReturn->purchaseOrder->po_number ?? '—' }}</a>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">{{ __('Supplier') }}</span>
                        <a href="{{ route('admin.suppliers.show', $purchaseReturn->supplier) }}" class="fw-semibold small text-decoration-none">{{ $purchaseReturn->supplier->name ?? __('Deleted') }}</a>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">{{ __('Return Date') }}</span>
                        <span class="fw-semibold small">{{ $purchaseReturn->return_date?->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">{{ __('Created By') }}</span>
                        <span class="fw-semibold small">{{ $purchaseReturn->creator->name ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted small">{{ __('Created At') }}</span>
                        <span class="fw-semibold small">{{ $purchaseReturn->created_at?->format('M d, Y H:i') }}</span>
                    </div>
                    @if ($purchaseReturn->reason)
                        <hr>
                        <small class="text-muted d-block mb-1 fw-bold">{{ __('Reason') }}</small>
                        <p class="mb-0 small">{{ $purchaseReturn->reason }}</p>
                    @endif
                    @if ($purchaseReturn->notes)
                        <hr>
                        <small class="text-muted d-block mb-1 fw-bold">{{ __('Notes') }}</small>
                        <p class="mb-0 small" style="white-space: pre-line;">{{ $purchaseReturn->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Complete Modal --}}
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('admin.purchase-returns.complete', $purchaseReturn) }}" method="POST">
                @csrf
                <div class="modal-body text-center py-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10 mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-exclamation-triangle text-warning fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-1">{{ __('Complete Purchase Return') }}</h5>
                    <p class="text-muted small mb-0">{{ __('Completing this return will immediately remove the returned products from inventory and record stock movements. This cannot be undone.') }}</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-warning px-4">
                        <i class="bi bi-check-lg me-1"></i>{{ __('Complete Return') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Cancel Modal --}}
<div class="modal fade" id="cancelReturnModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('admin.purchase-returns.cancel', $purchaseReturn) }}" method="POST">
                @csrf
                <div class="modal-body text-center py-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-x-circle text-danger fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-1">{{ __('Cancel Purchase Return') }}</h5>
                    <p class="text-muted small mb-0">{{ __('Are you sure you want to cancel this purchase return? This will not affect inventory.') }}</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="bi bi-x-circle me-1"></i>{{ __('Cancel Return') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
