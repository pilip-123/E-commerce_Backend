@extends('layouts.admin')

@section('title', __('Purchase Order') . ' ' . $purchaseOrder->po_number)

@section('content')
<div class="container-fluid p-0">

    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 font-monospace d-flex align-items-center gap-2 flex-wrap">
                {{ $purchaseOrder->po_number }}
                @php
                    $statusBadge = match ($purchaseOrder->status) {
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
                <span class="badge rounded-pill text-uppercase" style="font-size: 10px; letter-spacing: .05em; background: {{ $statusBadge[0] }}; color: {{ $statusBadge[1] }};">{{ str_replace('_', ' ', $purchaseOrder->status) }}</span>
            </h4>
            <p class="text-muted small mb-0">
                @if ($purchaseOrder->supplier)
                    <a href="{{ route('admin.suppliers.show', $purchaseOrder->supplier) }}" class="text-decoration-none fw-semibold">{{ $purchaseOrder->supplier->name }}</a>
                @else
                    {{ __('Deleted') }}
                @endif
                · {{ $purchaseOrder->order_date?->format('M d, Y') }}
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if (auth()->user()->hasPermission('purchases.update') && $purchaseOrder->isEditable())
                <a href="{{ route('admin.purchases.edit', $purchaseOrder) }}" class="btn btn-sm btn-success">
                    <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
                </a>
            @endif
            @if (auth()->user()->hasPermission('purchases.approve') && $purchaseOrder->status === 'pending')
                <form action="{{ route('admin.purchases.approve', $purchaseOrder) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-check2-circle me-1"></i>{{ __('Approve') }}
                    </button>
                </form>
            @endif
            @if (auth()->user()->hasPermission('purchases.approve') && $purchaseOrder->status === 'approved')
                <form action="{{ route('admin.purchases.ordered', $purchaseOrder) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-truck me-1"></i>{{ __('Mark Ordered') }}
                    </button>
                </form>
            @endif
            @if (auth()->user()->hasPermission('purchases.receive') && $purchaseOrder->isReceivable())
                <a href="{{ route('admin.purchases.receive', $purchaseOrder) }}" class="btn btn-sm btn-success">
                    <i class="bi bi-box-seam me-1"></i>{{ __('Receive Products') }}
                </a>
            @endif
            @if (auth()->user()->hasPermission('purchases.cancel') && in_array($purchaseOrder->status, ['draft', 'pending', 'approved']))
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i class="bi bi-x-circle me-1"></i>{{ __('Cancel Order') }}
                </button>
            @endif
            @if (auth()->user()->hasPermission('purchases.delete') && $purchaseOrder->items->sum('received_quantity') === 0 && $purchaseOrder->returns->isEmpty())
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-url="{{ route('admin.purchases.destroy', $purchaseOrder) }}">
                    <i class="bi bi-trash me-1"></i>{{ __('Delete') }}
                </button>
            @endif
            <a href="{{ route('admin.purchases.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Items --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 rounded-4">
                    <h5 class="fw-bold mb-0 fs-6"><i class="bi bi-box-seam me-2 text-success"></i>{{ __('Order Items') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Product') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Category') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Ordered') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Received') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Unit Cost') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchaseOrder->items as $item)
                                    @php
                                        $pct = $item->quantity > 0 ? (int) round(($item->received_quantity / $item->quantity) * 100) : 0;
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3">
                                            <span class="fw-semibold small">{{ $item->product->name ?? __('Deleted') }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-muted small">{{ $item->product->category->name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-center fw-bold">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                <span class="fw-semibold small">{{ $item->received_quantity }}/{{ $item->quantity }}</span>
                                            </div>
                                            <div class="progress mx-auto" style="height: 5px; width: 80px; margin-top: 4px; background: var(--admin-border);">
                                                <div class="progress-bar" style="width: {{ $pct }}%; background: var(--admin-primary);"></div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-end">${{ number_format($item->unit_cost, 2) }}</td>
                                        <td class="px-4 py-3 text-end fw-bold">${{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-4 text-center text-muted">{{ __('No items on this purchase order.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td colspan="5" class="px-4 py-3 text-end fw-bold text-uppercase small">{{ __('Subtotal') }}</td>
                                    <td class="px-4 py-3 text-end fw-bold">${{ number_format($purchaseOrder->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-end text-muted small">{{ __('Discount') }}</td>
                                    <td class="px-4 py-3 text-end text-danger fw-semibold">-${{ number_format($purchaseOrder->discount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-end text-muted small">{{ __('Tax') }}</td>
                                    <td class="px-4 py-3 text-end fw-semibold">+${{ number_format($purchaseOrder->tax, 2) }}</td>
                                </tr>
                                <tr class="table-light">
                                    <td colspan="5" class="px-4 py-3 text-end fw-bold text-uppercase">{{ __('Grand Total') }}</td>
                                    <td class="px-4 py-3 text-end fw-bold fs-6" style="color: var(--admin-primary);">${{ number_format($purchaseOrder->grand_total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Related returns --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 rounded-4">
                    <h5 class="fw-bold mb-0 fs-6"><i class="bi bi-arrow-return-left me-2 text-danger"></i>{{ __('Purchase Returns') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Return Number') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Date') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Total') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Status') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchaseOrder->returns as $return)
                                    <tr>
                                        <td class="px-4 py-3 font-monospace fw-bold small">{{ $return->return_number }}</td>
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
                                            <a href="{{ route('admin.purchase-returns.show', $return) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-4 text-center text-muted">{{ __('No returns for this purchase order.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Summary --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 rounded-4">
                    <h5 class="fw-bold mb-0 fs-6"><i class="bi bi-info-circle me-2 text-success"></i>{{ __('Order Summary') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">{{ __('Status') }}</span>
                        @php
                            $badge = match ($purchaseOrder->status) {
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
                        <span class="badge rounded-pill text-uppercase" style="background: {{ $badge[0] }}; color: {{ $badge[1] }};">{{ str_replace('_', ' ', $purchaseOrder->status) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small">{{ __('Payment Status') }}</span>
                        @if (auth()->user()->hasPermission('purchases.update'))
                            <form action="{{ route('admin.purchases.payment', $purchaseOrder) }}" method="POST" class="d-flex gap-1">
                                @csrf
                                @method('PUT')
                                <select name="payment_status" class="form-select form-select-sm" style="width: 110px;" onchange="this.form.submit()">
                                    @foreach (['unpaid', 'partial', 'paid'] as $ps)
                                        <option value="{{ $ps }}" {{ $purchaseOrder->payment_status === $ps ? 'selected' : '' }}>{{ ucfirst($ps) }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @else
                            @php
                                $payBadge = match ($purchaseOrder->payment_status) {
                                    'paid' => ['var(--badge-success-bg)', 'var(--badge-success-text)'],
                                    'partial' => ['var(--badge-warning-bg)', 'var(--badge-warning-text)'],
                                    default => ['var(--badge-gray-bg)', 'var(--badge-gray-text)'],
                                };
                            @endphp
                            <span class="badge rounded-pill text-uppercase" style="background: {{ $payBadge[0] }}; color: {{ $payBadge[1] }};">{{ $purchaseOrder->payment_status }}</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">{{ __('Supplier') }}</span>
                        @if ($purchaseOrder->supplier)
                            <a href="{{ route('admin.suppliers.show', $purchaseOrder->supplier) }}" class="fw-semibold small text-decoration-none">{{ $purchaseOrder->supplier->name }}</a>
                        @else
                            <span class="fw-semibold small">{{ __('Deleted') }}</span>
                        @endif
                    </div>
                    @php
                        $totalOrderedQty = $purchaseOrder->items->sum('quantity');
                        $totalReceivedQty = $purchaseOrder->items->sum('received_quantity');
                        $totalReturnedQty = $purchaseOrder->items->sum('returned_quantity');
                        $receivePct = $totalOrderedQty > 0 ? (int) round(($totalReceivedQty / $totalOrderedQty) * 100) : 0;
                    @endphp
                    <div class="py-2 border-bottom">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">{{ __('Received') }}</span>
                            <span class="fw-semibold small">{{ $totalReceivedQty }} / {{ $totalOrderedQty }} units ({{ $receivePct }}%)</span>
                        </div>
                        <div class="progress" style="height: 6px; background: var(--admin-border);">
                            <div class="progress-bar" style="width: {{ $receivePct }}%; background: var(--admin-primary);"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">{{ __('Returned') }}</span>
                        <span class="fw-semibold small">{{ $totalReturnedQty }} units</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">{{ __('Order Date') }}</span>
                        <span class="fw-semibold small">{{ $purchaseOrder->order_date?->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">{{ __('Expected Delivery') }}</span>
                        <span class="fw-semibold small">{{ $purchaseOrder->expected_delivery_date?->format('M d, Y') ?: '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">{{ __('Created By') }}</span>
                        <span class="fw-semibold small">{{ $purchaseOrder->creator->name ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted small">{{ __('Created At') }}</span>
                        <span class="fw-semibold small">{{ $purchaseOrder->created_at?->format('M d, Y H:i') }}</span>
                    </div>
                    @if ($purchaseOrder->notes)
                        <hr>
                        <small class="text-muted d-block mb-1 fw-bold">{{ __('Notes') }}</small>
                        <p class="mb-0 small" style="white-space: pre-line;">{{ $purchaseOrder->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Cancel Modal --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('admin.purchases.cancel', $purchaseOrder) }}" method="POST">
                @csrf
                <div class="modal-body text-center py-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-x-circle text-danger fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-1">{{ __('Cancel Purchase Order') }}</h5>
                    <p class="text-muted small mb-0">{{ __('Are you sure you want to cancel this purchase order? This cannot be undone and will not affect inventory.') }}</p>
                    <div class="mt-3 text-start">
                        <label class="form-label fw-semibold small mb-1">{{ __('Reason') }}</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="{{ __('Optional reason') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="bi bi-x-circle me-1"></i>{{ __('Cancel Order') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
