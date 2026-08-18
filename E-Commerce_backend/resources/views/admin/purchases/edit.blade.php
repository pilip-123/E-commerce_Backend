@extends('layouts.admin')

@section('title', __('Edit Purchase Order'))

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2 text-success"></i>{{ __('Edit Purchase Order') }} <span class="font-monospace text-muted">{{ $purchaseOrder->po_number }}</span></h5>
                <small class="text-muted">{{ __('Editing is limited to draft or pending purchase orders') }}</small>
            </div>
            <a href="{{ route('admin.purchases.show', $purchaseOrder) }}" class="btn btn-outline-secondary btn-sm ms-auto">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.purchases.update', $purchaseOrder) }}" method="POST" id="poForm">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small mb-1">{{ __('Supplier') }} <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                            <option value="">— {{ __('Select Supplier') }} —</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected(old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id)>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small mb-1">{{ __('Order Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="order_date" class="form-control @error('order_date') is-invalid @enderror" value="{{ old('order_date', $purchaseOrder->order_date?->format('Y-m-d')) }}" required>
                        @error('order_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small mb-1">{{ __('Expected Delivery Date') }}</label>
                        <input type="date" name="expected_delivery_date" class="form-control @error('expected_delivery_date') is-invalid @enderror" value="{{ old('expected_delivery_date', $purchaseOrder->expected_delivery_date?->format('Y-m-d')) }}">
                        @error('expected_delivery_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Items --}}
                <h6 class="fw-bold mb-2"><i class="bi bi-box-seam me-2 text-success"></i>{{ __('Products') }}</h6>
                <div class="table-responsive border rounded-3 mb-3">
                    <table class="table align-middle mb-0" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3 py-2 small fw-bold text-uppercase" style="min-width: 220px;">{{ __('Product') }}</th>
                                <th class="px-3 py-2 small fw-bold text-uppercase" style="width: 90px;">{{ __('Quantity') }}</th>
                                <th class="px-3 py-2 small fw-bold text-uppercase" style="width: 130px;">{{ __('Unit Cost') }}</th>
                                <th class="px-3 py-2 small fw-bold text-uppercase" style="width: 120px;">{{ __('Discount') }}</th>
                                <th class="px-3 py-2 small fw-bold text-uppercase" style="width: 110px;">{{ __('Tax') }}</th>
                                <th class="px-3 py-2 small fw-bold text-uppercase text-end" style="width: 130px;">{{ __('Total') }}</th>
                                <th class="px-3 py-2" style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            @php
                                $items = old('items', $purchaseOrder->items->map(fn ($it) => [
                                    'product_id' => $it->product_id,
                                    'quantity' => $it->quantity,
                                    'unit_cost' => $it->unit_cost,
                                    'discount' => $it->discount,
                                    'tax' => $it->tax,
                                ])->all());
                            @endphp
                            @foreach ($items as $i => $item)
                                <tr class="item-row">
                                    <td class="px-3 py-2">
                                        <select name="items[{{ $i }}][product_id]" class="form-select form-select-sm item-product" required>
                                            <option value="">— {{ __('Select Product') }} —</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}" data-cost="{{ $product->unit_cost ?? $product->price }}" @selected(($item['product_id'] ?? '') == $product->id)>
                                                    {{ $product->name }} ({{ $product->category->name ?? __('No category') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" name="items[{{ $i }}][quantity]" class="form-control form-control-sm item-qty" value="{{ $item['quantity'] ?? 1 }}" min="1" required>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="items[{{ $i }}][unit_cost]" class="form-control item-cost" value="{{ $item['unit_cost'] ?? '' }}" min="0" step="0.01" required>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" name="items[{{ $i }}][discount]" class="form-control form-control-sm item-discount" value="{{ $item['discount'] ?? 0 }}" min="0" step="0.01">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" name="items[{{ $i }}][tax]" class="form-control form-control-sm item-tax" value="{{ $item['tax'] ?? 0 }}" min="0" step="0.01">
                                    </td>
                                    <td class="px-3 py-2 text-end fw-bold item-total">$0.00</td>
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger item-remove" tabindex="-1"><i class="bi bi-x-lg"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @error('items') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                <button type="button" class="btn btn-sm btn-outline-success mb-4" id="addItemBtn">
                    <i class="bi bi-plus-circle me-1"></i>{{ __('Add Product') }}
                </button>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small mb-1">{{ __('Order Discount') }}</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">$</span>
                            <input type="number" name="discount" id="orderDiscount" class="form-control" value="{{ old('discount', $purchaseOrder->discount) }}" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small mb-1">{{ __('Order Tax') }}</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">$</span>
                            <input type="number" name="tax" id="orderTax" class="form-control" value="{{ old('tax', $purchaseOrder->tax) }}" min="0" step="0.01">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mb-4">
                    <div class="bg-light border rounded-3 px-4 py-3" style="min-width: 300px;">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">{{ __('Subtotal') }}</span>
                            <span class="fw-bold" id="sumSubtotal">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">{{ __('Discount') }}</span>
                            <span class="fw-bold" id="sumDiscount">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-2">
                            <span class="text-muted">{{ __('Tax') }}</span>
                            <span class="fw-bold" id="sumTax">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold border-top pt-2">
                            <span>{{ __('Grand Total') }}</span>
                            <span id="sumGrandTotal">$0.00</span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold small mb-1">{{ __('Notes') }}</label>
                    <textarea name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check-lg me-1"></i>{{ __('Update Purchase Order') }}
                    </button>
                    <a href="{{ route('admin.purchases.show', $purchaseOrder) }}" class="btn btn-outline-secondary px-4">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var productsJson = @json($products->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'cost' => $p->unit_cost ?? $p->price]));

    function productOptions(selected) {
        var html = '<option value="">— Select Product —</option>';
        productsJson.forEach(function(p) {
            html += '<option value="' + p.id + '" data-cost="' + p.cost + '"' + (String(selected) === String(p.id) ? ' selected' : '') + '>' + p.name + '</option>';
        });
        return html;
    }

    function calcLine(row) {
        var qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        var cost = parseFloat(row.querySelector('.item-cost').value) || 0;
        var disc = parseFloat(row.querySelector('.item-discount').value) || 0;
        var tax = parseFloat(row.querySelector('.item-tax').value) || 0;
        var total = qty * cost - disc + tax;
        row.querySelector('.item-total').textContent = '$' + total.toFixed(2);
        recalcTotals();
    }

    function recalcTotals() {
        var subtotal = 0;
        document.querySelectorAll('#itemsBody .item-row').forEach(function(row) {
            var qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            var cost = parseFloat(row.querySelector('.item-cost').value) || 0;
            var disc = parseFloat(row.querySelector('.item-discount').value) || 0;
            var tax = parseFloat(row.querySelector('.item-tax').value) || 0;
            subtotal += qty * cost - disc + tax;
        });
        var discount = parseFloat(document.getElementById('orderDiscount').value) || 0;
        var tax = parseFloat(document.getElementById('orderTax').value) || 0;
        document.getElementById('sumSubtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('sumDiscount').textContent = '-$' + discount.toFixed(2);
        document.getElementById('sumTax').textContent = '+$' + tax.toFixed(2);
        document.getElementById('sumGrandTotal').textContent = '$' + (subtotal - discount + tax).toFixed(2);
    }

    function bindRow(row) {
        row.querySelector('.item-product').addEventListener('change', function() {
            var opt = this.options[this.selectedIndex];
            if (opt && opt.dataset.cost) {
                row.querySelector('.item-cost').value = opt.dataset.cost;
            }
            calcLine(row);
        });
        ['item-qty', 'item-cost', 'item-discount', 'item-tax'].forEach(function(cls) {
            row.querySelector('.' + cls).addEventListener('input', function() { calcLine(row); });
        });
        row.querySelector('.item-remove').addEventListener('click', function() {
            if (document.querySelectorAll('#itemsBody .item-row').length > 1) {
                row.remove();
                recalcTotals();
            }
        });
        calcLine(row);
    }

    document.querySelectorAll('#itemsBody .item-row').forEach(bindRow);

    document.getElementById('addItemBtn').addEventListener('click', function() {
        var tbody = document.getElementById('itemsBody');
        var index = tbody.querySelectorAll('.item-row').length;
        var tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML =
            '<td class="px-3 py-2"><select name="items[' + index + '][product_id]" class="form-select form-select-sm item-product" required>' + productOptions('') + '</select></td>' +
            '<td class="px-3 py-2"><input type="number" name="items[' + index + '][quantity]" class="form-control form-control-sm item-qty" value="1" min="1" required></td>' +
            '<td class="px-3 py-2"><div class="input-group input-group-sm"><span class="input-group-text">$</span><input type="number" name="items[' + index + '][unit_cost]" class="form-control item-cost" value="" min="0" step="0.01" required></div></td>' +
            '<td class="px-3 py-2"><input type="number" name="items[' + index + '][discount]" class="form-control form-control-sm item-discount" value="0" min="0" step="0.01"></td>' +
            '<td class="px-3 py-2"><input type="number" name="items[' + index + '][tax]" class="form-control form-control-sm item-tax" value="0" min="0" step="0.01"></td>' +
            '<td class="px-3 py-2 text-end fw-bold item-total">$0.00</td>' +
            '<td class="px-3 py-2 text-center"><button type="button" class="btn btn-sm btn-outline-danger item-remove" tabindex="-1"><i class="bi bi-x-lg"></i></button></td>';
        tbody.appendChild(tr);
        bindRow(tr);
    });

    document.getElementById('orderDiscount').addEventListener('input', recalcTotals);
    document.getElementById('orderTax').addEventListener('input', recalcTotals);

    recalcTotals();
});
</script>
@endpush
@endsection
