@extends('layouts.admin')

@section('title', __('Edit Promotion'))

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4" style="max-width: 720px;">
        <div class="card-header bg-white py-3 rounded-4">
            <h5 class="fw-bold mb-0 fs-6"><i class="bi bi-pencil-square me-2 text-success"></i>{{ __('Edit Promotion') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.promotions.update', $promotion->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small mb-1">{{ __('Promotion Name') }}</label>
                        <input type="text" name="name" value="{{ old('name', $promotion->name) }}"
                               class="form-control form-control-sm @error('name') is-invalid @enderror" required
                               placeholder="e.g. Summer Sale">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-sm-4">
                        <label class="form-label fw-semibold small mb-1">{{ __('Discount Type') }}</label>
                        <select name="discount_type" class="form-select form-select-sm @error('discount_type') is-invalid @enderror" required>
                            <option value="percentage" {{ old('discount_type', $promotion->discount_type) === 'percentage' ? 'selected' : '' }}>{{ __('Percentage') }} (%)</option>
                            <option value="fixed" {{ old('discount_type', $promotion->discount_type) === 'fixed' ? 'selected' : '' }}>{{ __('Fixed') }} ($)</option>
                        </select>
                        @error('discount_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-sm-4">
                        <label class="form-label fw-semibold small mb-1">{{ __('Discount Value') }}</label>
                        <input type="number" step="0.01" min="0" name="discount_value" value="{{ old('discount_value', $promotion->discount_value) }}"
                               class="form-control form-control-sm @error('discount_value') is-invalid @enderror" required
                               placeholder="0.00">
                        @error('discount_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-sm-4">
                        <label class="form-label fw-semibold small mb-1">{{ __('Status') }}</label>
                        <div class="d-flex gap-3 pt-1">
                            <div class="form-check">
                                <input type="radio" name="status" value="1" class="form-check-input" id="statActive" {{ old('status', $promotion->status) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="statActive">{{ __('Active') }}</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="status" value="0" class="form-check-input" id="statInactive" {{ !old('status', $promotion->status) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="statInactive">{{ __('Inactive') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small mb-1">{{ __('Start Date') }}</label>
                        <input type="datetime-local" name="start_date" value="{{ old('start_date', \Carbon\Carbon::parse($promotion->start_date)->format('Y-m-d\TH:i')) }}"
                               class="form-control form-control-sm @error('start_date') is-invalid @enderror" required>
                        @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small mb-1">{{ __('End Date') }}</label>
                        <input type="datetime-local" name="end_date" value="{{ old('end_date', \Carbon\Carbon::parse($promotion->end_date)->format('Y-m-d\TH:i')) }}"
                               class="form-control form-control-sm @error('end_date') is-invalid @enderror" required>
                        @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small mb-1">{{ __('Description') }}</label>
                        <textarea name="description" rows="3"
                                  class="form-control form-control-sm @error('description') is-invalid @enderror"
                                  placeholder="{{ __('Promotion description') }}">{{ old('description', $promotion->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small mb-1">{{ __('Products') }}</label>
                        <div class="border rounded-3 p-3" style="max-height: 200px; overflow-y: auto;">
                            @forelse ($products as $product)
                                <div class="form-check">
                                    <input type="checkbox" name="products[]" value="{{ $product->id }}"
                                           class="form-check-input" id="product_{{ $product->id }}"
                                           {{ in_array($product->id, old('products', $promotion->products->pluck('id')->toArray())) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="product_{{ $product->id }}">
                                        {{ $product->name }} - ${{ number_format($product->price, 2) }}
                                    </label>
                                </div>
                            @empty
                                <p class="text-muted small mb-0">{{ __('No products available.') }}</p>
                            @endforelse
                        </div>
                        @error('products') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="my-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success btn-sm px-4">
                        <i class="bi bi-check-lg me-1"></i>{{ __('Update') }}
                    </button>
                    <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary btn-sm px-4">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
