@extends('layouts.admin')

@section('title', __('Inventory Adjustment'))

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0">{{ __('Inventory Adjustment') }}</h5>
                <small class="text-muted">{{ __('Correct stock quantities') }}</small>
            </div>
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary btn-sm ms-auto">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.inventory.adjustment.store') }}">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('Product') }} <span class="text-danger">*</span></label>
                        <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                            <option value="">— {{ __('Select Product') }} —</option>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}" data-stock="{{ $p->stock }}" @selected(old('product_id') == $p->id)>
                                    {{ $p->name }} (Current: {{ $p->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">{{ __('Current Stock') }}</label>
                        <input type="text" id="currentStock" class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">{{ __('New Quantity') }} <span class="text-danger">*</span></label>
                        <input type="number" name="new_quantity" class="form-control @error('new_quantity') is-invalid @enderror"
                               value="{{ old('new_quantity') }}" min="0" required>
                        @error('new_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">{{ __('Reason for Adjustment') }} <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="3" required>{{ old('reason') }}</textarea>
                        @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-warning px-4">
                            <i class="bi bi-sliders me-1"></i>{{ __('Apply Adjustment') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.querySelector('select[name="product_id"]')?.addEventListener('change', function() {
    let opt = this.options[this.selectedIndex];
    let el = document.getElementById('currentStock');
    if (opt && opt.dataset.stock && el) el.value = opt.dataset.stock;
});
</script>
@endsection
