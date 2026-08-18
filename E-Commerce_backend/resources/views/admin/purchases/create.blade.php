@extends('layouts.admin')

@section('title', __('Create Purchase Order'))

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center gap-2 border-0">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2 text-success"></i>{{ __('Create Purchase Order') }}</h5>
                <small class="text-muted">{{ __('Create a purchase order — inventory is only updated when products are received') }}</small>
            </div>
            <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline-secondary btn-sm ms-auto">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.purchases.store') }}" method="POST" id="poForm">
                @csrf

                {{-- ── Section: Order Information ── --}}
                <div class="border rounded-3 p-3 p-md-4 mb-4">
                    <h6 class="fw-bold mb-3 pb-2 border-bottom">
                        <i class="bi bi-shop me-2 text-success"></i>{{ __('Order Information') }}
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1">{{ __('Supplier') }} <span class="text-danger">*</span></label>
                            <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                                <option value="">— {{ __('Select Supplier') }} —</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small mb-1">{{ __('Order Date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="order_date" class="form-control @error('order_date') is-invalid @enderror" value="{{ old('order_date', now()->format('Y-m-d')) }}" required>
                            @error('order_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small mb-1">{{ __('Expected Delivery Date') }}</label>
                            <input type="date" name="expected_delivery_date" class="form-control @error('expected_delivery_date') is-invalid @enderror" value="{{ old('expected_delivery_date') }}">
                            @error('expected_delivery_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @if (auth()->user()->hasPermission('purchases.approve'))
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small mb-1">{{ __('Status') }}</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="draft" @selected(old('status', 'draft') === 'draft')>{{ __('Draft') }}</option>
                                    <option value="pending" @selected(old('status') === 'pending')>{{ __('Pending') }}</option>
                                    <option value="approved" @selected(old('status') === 'approved')>{{ __('Approved') }}</option>
                                    <option value="ordered" @selected(old('status') === 'ordered')>{{ __('Ordered') }}</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ── Section: Products ── --}}
                <div class="border rounded-3 p-3 p-md-4 mb-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-box-seam me-2 text-success"></i>{{ __('Products') }}
                            <span class="badge rounded-pill text-bg-success ms-1" id="itemCount">1</span>
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-success" id="addItemBtn">
                            <i class="bi bi-plus-circle me-1"></i>{{ __('Add Product') }}
                        </button>
                    </div>
                    <div class="table-responsive border rounded-3 mb-2">
                        <table class="table align-middle mb-0" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-3 py-2 small fw-bold text-uppercase" style="min-width: 220px;">{{ __('Product') }}</th>
                                    <th class="px-3 py-2 small fw-bold text-uppercase text-center" style="width: 90px;">{{ __('Quantity') }}</th>
                                    <th class="px-3 py-2 small fw-bold text-uppercase" style="width: 130px;">{{ __('Unit Cost') }}</th>
                                    <th class="px-3 py-2 small fw-bold text-uppercase" style="width: 120px;">{{ __('Discount') }}</th>
                                    <th class="px-3 py-2 small fw-bold text-uppercase" style="width: 110px;">{{ __('Tax') }}</th>
                                    <th class="px-3 py-2 small fw-bold text-uppercase text-end" style="width: 130px;">{{ __('Total') }}</th>
                                    <th class="px-3 py-2" style="width: 40px;"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                @php $oldItems = old('items', [['product_id' => '', 'quantity' => 1, 'unit_cost' => '', 'discount' => 0, 'tax' => 0]]); @endphp
                                @foreach ($oldItems as $i => $item)
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
                                            <input type="number" name="items[{{ $i }}][quantity]" class="form-control form-control-sm item-qty text-center" value="{{ $item['quantity'] ?? 1 }}" min="1" required>
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
                </div>

                {{-- ── Section: Totals & Notes ── --}}
                <div class="row g-3 mb-4">
                    <div class="col-lg-7">
                        <div class="border rounded-3 p-3 p-md-4 h-100">
                            <h6 class="fw-bold mb-3 pb-2 border-bottom">
                                <i class="bi bi-receipt me-2 text-success"></i>{{ __('Discounts, Tax & Notes') }}
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small mb-1">{{ __('Order Discount') }}</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="discount" id="orderDiscount" class="form-control" value="{{ old('discount', 0) }}" min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small mb-1">{{ __('Order Tax') }}</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="tax" id="orderTax" class="form-control" value="{{ old('tax', 0) }}" min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small mb-1">{{ __('Notes') }}</label>
                                    <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="{{ __('Optional notes about this order...') }}">{{ old('notes') }}</textarea>
                                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);">
                            <div class="card-body p-4 d-flex flex-column">
                                <h6 class="fw-bold mb-3 pb-2 border-bottom">
                                    <i class="bi bi-calculator me-2 text-success"></i>{{ __('Order Summary') }}
                                </h6>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small">{{ __('Subtotal') }}</span>
                                    <span class="fw-semibold" id="sumSubtotal">$0.00</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small">{{ __('Discount') }}</span>
                                    <span class="fw-semibold text-danger" id="sumDiscount">-$0.00</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted small">{{ __('Tax') }}</span>
                                    <span class="fw-semibold text-success" id="sumTax">+$0.00</span>
                                </div>
                                <div class="border-top pt-3 mt-auto d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">{{ __('Grand Total') }}</span>
                                    <span class="fw-bold fs-4" style="color: var(--admin-primary);" id="sumGrandTotal">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer actions --}}
                <div class="d-flex flex-wrap justify-content-end gap-2 border-top pt-4">
                    <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline-secondary px-4">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check-lg me-1"></i>{{ __('Create Purchase Order') }}
                    </button>
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

        var countEl = document.getElementById('itemCount');
        if (countEl) countEl.textContent = document.querySelectorAll('#itemsBody .item-row').length;
    }

    function bindRow(row) {
        row.querySelector('.item-product').addEventListener('change', function() {
            var val = this.value;
            var dup = false;
            document.querySelectorAll('#itemsBody .item-row').forEach(function(r) {
                if (r !== row && r.querySelector('.item-product').value === val && val !== '') dup = true;
            });
            var hint = row.querySelector('.item-dup-hint');
            if (dup) {
                if (!hint) {
                    hint = document.createElement('div');
                    hint.className = 'text-danger small mt-1 item-dup-hint';
                    hint.textContent = '{{ __('This product is already in the list.') }}';
                    row.querySelector('td:first-child').appendChild(hint);
                }
            } else if (hint) {
                hint.remove();
            }
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
            '<td class="px-3 py-2"><input type="number" name="items[' + index + '][quantity]" class="form-control form-control-sm item-qty text-center" value="1" min="1" required></td>' +
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
