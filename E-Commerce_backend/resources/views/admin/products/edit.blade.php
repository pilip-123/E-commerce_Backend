@extends('layouts.admin')

@section('title', __('Edit Product'))

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4" style="max-width: 720px;">
        <div class="card-header bg-white py-3 rounded-4">
            <h5 class="fw-bold mb-0 fs-6"><i class="bi bi-pencil-square me-2 text-success"></i>{{ __('Edit Product') }}</h5>
        </div>
        <div class="card-body">
            <form id="editProductForm" action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div id="formAlert" class="alert d-none mb-3 py-2 small"></div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small mb-1">{{ __('Product Name') }}</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}"
                               class="form-control form-control-sm @error('name') is-invalid @enderror" required
                               placeholder="e.g. Wireless Headphones">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small mb-1">{{ __('Category') }}</label>
                        <select name="category_id" class="form-select form-select-sm @error('category_id') is-invalid @enderror" required>
                            <option value="">{{ __('Select...') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-sm-3">
                        <label class="form-label fw-semibold small mb-1">{{ __('Price') }} ($)</label>
                        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}"
                               class="form-control form-control-sm @error('price') is-invalid @enderror" required
                               placeholder="0.00">
                        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-sm-3">
                        <label class="form-label fw-semibold small mb-1">{{ __('Stock') }}</label>
                        <input type="number" min="0" name="stock" value="{{ old('stock', $product->stock) }}"
                               class="form-control form-control-sm @error('stock') is-invalid @enderror" required
                               placeholder="0">
                        @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small mb-1">{{ __('Description') }}</label>
                        <textarea name="description" rows="3"
                                  class="form-control form-control-sm @error('description') is-invalid @enderror"
                                  placeholder="{{ __('Full product description') }}">{{ old('description', $product->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small mb-1">{{ __('Image') }}</label>
                        <div class="drop-zone {{ $product->image ? 'has-image' : '' }}" id="dropZone">
                            <input type="file" name="image" id="imageInput" accept="image/*" hidden>
                            <div id="dropContent" class="{{ $product->image ? 'd-none' : '' }}">
                                <i class="bi bi-cloud-arrow-up text-success" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2 small fw-semibold">{{ __('Drop image here or click to browse') }}</p>
                                <p class="text-muted mb-0" style="font-size: 11px;">{{ __('PNG, JPG up to 4MB') }}</p>
                            </div>
                            <div id="dropPreview" class="{{ $product->image ? '' : 'd-none' }}">
                                @if ($product->image)
                                    <img id="previewImg" src="{{ asset('storage/' . $product->image) }}" alt="Preview">
                                @else
                                    <img id="previewImg" src="" alt="Preview">
                                @endif
                                <p class="mb-0 mt-2 small text-muted" id="fileName">{{ $product->image ? basename($product->image) : '' }}</p>
                                <p class="text-muted mb-0" style="font-size: 10px;">{{ __('Click or drop to replace') }}</p>
                            </div>
                        </div>
                        <div class="form-text small">{{ __('Leave empty to keep current image.') }}</div>
                        @error('image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-sm-4">
                        <label class="form-label fw-semibold small mb-1">{{ __('Status') }}</label>
                        <div class="d-flex gap-3 pt-1">
                            <div class="form-check">
                                <input type="radio" name="status" value="1" class="form-check-input" id="statActive" {{ old('status', $product->status) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="statActive">{{ __('Active') }}</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="status" value="0" class="form-check-input" id="statInactive" {{ !old('status', $product->status) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="statInactive">{{ __('Inactive') }}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success btn-sm px-4">
                        <i class="bi bi-check-lg me-1"></i>{{ __('Update') }}
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm px-4">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var form = this;
    var btn = form.querySelector('button[type="submit"]');
    var alert = document.getElementById('formAlert');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';
    alert.classList.add('d-none');
    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(function(r) {
        return r.json().then(function(data) {
            if (r.ok) {
                alert.className = 'alert alert-success mb-3 py-2 small';
                alert.innerHTML = data.message || 'Product updated successfully.';
                alert.classList.remove('d-none');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Update';
            } else {
                var msg = data.message || 'Validation error.';
                if (data.errors) {
                    msg = Object.values(data.errors).flat().join('<br>');
                }
                alert.className = 'alert alert-danger mb-3 py-2 small';
                alert.innerHTML = msg;
                alert.classList.remove('d-none');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Update';
            }
        });
    }).catch(function() {
        alert.className = 'alert alert-danger mb-3 py-2 small';
        alert.innerHTML = 'Network error. Please try again.';
        alert.classList.remove('d-none');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Update';
    });
});
</script>
@endpush
