@extends('layouts.admin')

@section('title', 'Transfer Inventory')

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0">Transfer Inventory</h5>
                <small class="text-muted">Move stock between products</small>
            </div>
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary btn-sm ms-auto">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.inventory.transfer.store') }}">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">From Product <span class="text-danger">*</span></label>
                        <select name="from_product_id" class="form-select @error('from_product_id') is-invalid @enderror" required>
                            <option value="">— Select Source —</option>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}" data-stock="{{ $p->stock }}" @selected(old('from_product_id') == $p->id)>
                                    {{ $p->name }} (Stock: {{ $p->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('from_product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">To Product <span class="text-danger">*</span></label>
                        <select name="to_product_id" class="form-select @error('to_product_id') is-invalid @enderror" required>
                            <option value="">— Select Destination —</option>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}" @selected(old('to_product_id') == $p->id)>
                                    {{ $p->name }} (Stock: {{ $p->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('to_product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                               value="{{ old('quantity') }}" min="1" required>
                        @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2" placeholder="Optional notes">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-info px-4 text-white">
                            <i class="bi bi-arrow-left-right me-1"></i>Transfer Stock
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.querySelector('select[name="from_product_id"]')?.addEventListener('change', function() {
    let opt = this.options[this.selectedIndex];
    if (opt && opt.dataset.stock) {
        let qty = document.querySelector('input[name="quantity"]');
        if (qty) qty.max = opt.dataset.stock;
    }
});
</script>
@endsection
