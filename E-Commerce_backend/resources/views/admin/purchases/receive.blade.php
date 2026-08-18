@extends('layouts.admin')

@section('title', __('Receive') . ' ' . $purchaseOrder->po_number)

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-box-seam me-2 text-success"></i>{{ __('Receive Products') }} <span class="font-monospace text-muted">{{ $purchaseOrder->po_number }}</span></h5>
                <small class="text-muted">{{ $purchaseOrder->supplier->name ?? __('Deleted') }} — {{ __('Receiving will increase inventory immediately') }}</small>
            </div>
            <a href="{{ route('admin.purchases.show', $purchaseOrder) }}" class="btn btn-outline-secondary btn-sm ms-auto">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger rounded-3 py-2 px-3">
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.purchases.receive.store', $purchaseOrder) }}" method="POST">
                @csrf
                <div class="table-responsive border rounded-3 mb-3">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Product') }}</th>
                                <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Ordered') }}</th>
                                <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Already Received') }}</th>
                                <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Remaining') }}</th>
                                <th class="px-4 py-3 small fw-bold text-uppercase text-center" style="width: 160px;">{{ __('Receiving Now') }}</th>
                                <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Current Stock') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchaseOrder->items as $item)
                                @php $remaining = $item->remaining_to_receive; @endphp
                                <tr>
                                    <td class="px-4 py-3">
                                        <span class="fw-semibold small">{{ $item->product->name ?? __('Deleted') }}</span>
                                        <div class="text-muted" style="font-size: 11px;">${{ number_format($item->unit_cost, 2) }} / unit</div>
                                    </td>
                                    <td class="px-4 py-3 text-center fw-bold">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge rounded-pill" style="background: var(--badge-success-bg); color: var(--badge-success-text);">{{ $item->received_quantity }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($remaining > 0)
                                            <span class="badge rounded-pill" style="background: var(--badge-warning-bg); color: var(--badge-warning-text);">{{ $remaining }}</span>
                                        @else
                                            <span class="badge rounded-pill" style="background: var(--badge-gray-bg); color: var(--badge-gray-text);">0</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($remaining > 0)
                                            <input type="number" name="quantities[{{ $item->id }}]" class="form-control form-control-sm receive-qty text-center"
                                                value="{{ old('quantities.' . $item->id, $remaining) }}" min="0" max="{{ $remaining }}">
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-end fw-bold">{{ $item->product->stock ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="receiveAllBtn">
                        <i class="bi bi-box-arrow-in-down me-1"></i>{{ __('Receive All Remaining') }}
                    </button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check-lg me-1"></i>{{ __('Receive Products') }}
                    </button>
                    <a href="{{ route('admin.purchases.show', $purchaseOrder) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('receiveAllBtn').addEventListener('click', function() {
        document.querySelectorAll('.receive-qty').forEach(function(input) {
            input.value = input.max;
        });
    });
});
</script>
@endpush
@endsection
