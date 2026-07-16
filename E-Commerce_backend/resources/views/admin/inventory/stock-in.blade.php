@extends('layouts.admin')

@section('title', __('Stock In'))

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0">{{ __('Stock In') }}</h5>
                <small class="text-muted">{{ __('Add inventory to products') }}</small>
            </div>
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary btn-sm ms-auto">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.inventory.stock-in.store') }}">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('Product') }} <span class="text-danger">*</span></label>
                        <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                            <option value="">— {{ __('Select Product') }} —</option>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}" @selected(old('product_id') == $p->id)>
                                    {{ $p->name }} ({{ __('Stock:') }} {{ $p->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">{{ __('Quantity') }} <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                               value="{{ old('quantity') }}" min="1" required>
                        @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">{{ __('Unit Cost') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="unit_cost" class="form-control @error('unit_cost') is-invalid @enderror"
                                   value="{{ old('unit_cost') }}" min="0" step="0.01">
                        </div>
                        @error('unit_cost') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('Reference') }}</label>
                        <input type="text" name="reference" class="form-control @error('reference') is-invalid @enderror"
                               value="{{ old('reference') }}">
                        @error('reference') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('Notes') }}</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-plus-circle me-1"></i>{{ __('Add Stock') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
