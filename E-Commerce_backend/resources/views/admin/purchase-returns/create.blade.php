@extends('layouts.admin')

@section('title', __('Create Purchase Return'))

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2 text-success"></i>{{ __('Create Purchase Return') }}</h5>
                <small class="text-muted">{{ __('Return products to a supplier — inventory is only reduced when the return is completed') }}</small>
            </div>
            <a href="{{ route('admin.purchase-returns.index') }}" class="btn btn-outline-secondary btn-sm ms-auto">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
        <div class="card-body p-4">
            @if ($purchaseOrders->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 d-block mb-3 text-muted"></i>
                    <h5 class="fw-bold">{{ __('No received purchase orders') }}</h5>
                    <p class="text-muted small">{{ __('Receive a purchase order before you can create a return for it.') }}</p>
                    <a href="{{ route('admin.purchases.index') }}" class="btn btn-success btn-sm"><i class="bi bi-box-seam me-1"></i>{{ __('Go to Purchase Orders') }}</a>
                </div>
            @else
                <form action="{{ route('admin.purchase-returns.store') }}" method="POST" id="returnForm">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1">{{ __('Purchase Order') }} <span class="text-danger">*</span></label>
                            <select name="purchase_order_id" id="poSelect" class="form-select @error('purchase_order_id') is-invalid @enderror" required>
                                <option value="">— {{ __('Select Purchase Order') }} —</option>
                                @foreach ($purchaseOrders as $po)
                                    <option value="{{ $po->id }}" @selected(old('purchase_order_id') == $po->id)>
                                        {{ $po->po_number }} — {{ $po->supplier->name ?? __('Deleted') }} ({{ $po->order_date?->format('M d, Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('purchase_order_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small mb-1">{{ __('Return Date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="return_date" class="form-control @error('return_date') is-invalid @enderror" value="{{ old('return_date', now()->format('Y-m-d')) }}" required>
                            @error('return_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small mb-1">{{ __('Total Amount') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" id="returnTotal" class="form-control" value="$0.00" readonly>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-2"><i class="bi bi-box-seam me-2 text-success"></i>{{ __('Return Items') }}</h6>
                    <div class="table-responsive border rounded-3 mb-3">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-3 py-2 small fw-bold text-uppercase" style="min-width: 200px;">{{ __('Product') }}</th>
                                    <th class="px-3 py-2 small fw-bold text-uppercase text-center" style="width: 90px;">{{ __('Unit Cost') }}</th>
                                    <th class="px-3 py-2 small fw-bold text-uppercase text-center" style="width: 110px;">{{ __('Available') }}</th>
                                    <th class="px-3 py-2 small fw-bold text-uppercase text-center" style="width: 110px;">{{ __('Quantity') }}</th>
                                    <th class="px-3 py-2 small fw-bold text-uppercase" style="min-width: 140px;">{{ __('Reason') }}</th>
                                    <th class="px-3 py-2 small fw-bold text-uppercase text-end" style="width: 120px;">{{ __('Total') }}</th>
                                    <th class="px-3 py-2" style="width: 40px;"></th>
                                </tr>
                            </thead>
                            <tbody id="returnItemsBody">
                                <tr id="noItemsRow">
                                    <td colspan="7" class="px-4 py-4 text-center text-muted small">
                                        {{ __('Select a purchase order to add return items.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @error('items') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                    <div class="mb-4">
                        <label class="form-label fw-semibold small mb-1">{{ __('Reason') }}</label>
                        <textarea name="reason" rows="2" class="form-control @error('reason') is-invalid @enderror" placeholder="{{ __('e.g. Damaged goods, wrong items, quality issues') }}">{{ old('reason') }}</textarea>
                        @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small mb-1">{{ __('Notes') }}</label>
                        <textarea name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-check-lg me-1"></i>{{ __('Create Return') }}
                        </button>
                        <a href="{{ route('admin.purchase-returns.index') }}" class="btn btn-outline-secondary px-4">{{ __('Cancel') }}</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ordersJson = @json($ordersJson);

    var body = document.getElementById('returnItemsBody');

    function calcTotals() {
        var total = 0;
        document.querySelectorAll('#returnItemsBody .return-row').forEach(function(row) {
            var cost = parseFloat(row.querySelector('.return-cost').textContent.replace(/[^0-9.\-]/g, '')) || 0;
            var qty = parseFloat(row.querySelector('.return-qty').value) || 0;
            var line = cost * qty;
            row.querySelector('.return-line-total').textContent = '$' + line.toFixed(2);
            total += line;
        });
        document.getElementById('returnTotal').value = '$' + total.toFixed(2);
    }

    function buildRows(po) {
        body.innerHTML = '';
        if (!po || !po.items.length) {
            body.innerHTML = '<tr id="noItemsRow"><td colspan="7" class="px-4 py-4 text-center text-muted small">{{ __('No items available to return for this purchase order.') }}</td></tr>';
            return;
        }
        po.items.forEach(function(item, i) {
            var tr = document.createElement('tr');
            tr.className = 'return-row';
            tr.innerHTML =
                '<td class="px-3 py-2"><span class="fw-semibold small">' + item.name + '</span>' +
                '<input type="hidden" name="items[' + i + '][purchase_order_item_id]" value="' + item.id + '"></td>' +
                '<td class="px-3 py-2 text-center"><span class="return-cost">$' + item.unit_cost.toFixed(2) + '</span></td>' +
                '<td class="px-3 py-2 text-center"><span class="badge rounded-pill" style="background: var(--badge-info-bg); color: var(--badge-info-text);">' + item.available + '</span></td>' +
                '<td class="px-3 py-2 text-center"><input type="number" name="items[' + i + '][quantity]" class="form-control form-control-sm return-qty text-center" value="' + item.available + '" min="1" max="' + item.available + '" required></td>' +
                '<td class="px-3 py-2"><input type="text" name="items[' + i + '][reason]" class="form-control form-control-sm" placeholder="{{ __('Reason') }}"></td>' +
                '<td class="px-3 py-2 text-end fw-bold return-line-total">$0.00</td>' +
                '<td class="px-3 py-2 text-center"><button type="button" class="btn btn-sm btn-outline-danger return-remove" tabindex="-1"><i class="bi bi-x-lg"></i></button></td>';
            body.appendChild(tr);

            tr.querySelector('.return-qty').addEventListener('input', function() {
                var max = parseInt(this.max, 10);
                if (this.value > max) this.value = max;
                calcTotals();
            });
            tr.querySelector('.return-remove').addEventListener('click', function() {
                tr.remove();
                calcTotals();
                if (!document.querySelectorAll('#returnItemsBody .return-row').length) {
                    body.innerHTML = '<tr id="noItemsRow"><td colspan="7" class="px-4 py-4 text-center text-muted small">{{ __('No items selected.') }}</td></tr>';
                }
            });
        });
        calcTotals();
    }

    document.getElementById('poSelect').addEventListener('change', function() {
        var po = ordersJson.find(function(o) { return String(o.id) === this.value; }, this);
        buildRows(po || null);
    });

    if (document.getElementById('poSelect').value) {
        document.getElementById('poSelect').dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection
