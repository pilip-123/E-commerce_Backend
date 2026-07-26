@extends('layouts.admin')

@section('title', 'Create Product')

@section('content')
    <div class="container-fluid p-0">
        <div class="card border-0 shadow-sm rounded-4" style="max-width: 720px;">
            <div class="card-header bg-white py-3 rounded-4">
                <h5 class="fw-bold mb-0 fs-6"><i class="bi bi-plus-circle me-2 text-success"></i>{{ __('Create Product') }}
                </h5>
            </div>
            <div class="card-body">
                <form id="createProductForm" action="{{ route('admin.products.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div id="formAlert" class="alert d-none mb-3 py-2 small"></div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small mb-2">{{ __('Product Name') }}</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control form-control-sm @error('name') is-invalid @enderror" required
                                placeholder="Product Name" style="height: 48px;">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small mb-1">{{ __('Category') }}</label>
                            <select name="category_id"
                                class="form-select form-select-sm @error('category_id') is-invalid @enderror" required>
                                <option value="">Select...</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-3">
                            <label class="form-label fw-semibold small mb-1">{{ __('Price') }} ($)</label>
                            <input type="number" step="0.01" min="0" name="price" value="{{ old('price') }}"
                                class="form-control form-control-sm @error('price') is-invalid @enderror" required
                                placeholder="0.00">
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-3">
                            <label class="form-label fw-semibold small mb-1">{{ __('Stock') }}</label>
                            <input type="number" min="0" name="stock" value="{{ old('stock', 0) }}"
                                class="form-control form-control-sm @error('stock') is-invalid @enderror" required
                                placeholder="0">
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ═══════════════════ Product Description Section ═══════════════════ --}}
                        <div class="col-12">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-card-text text-success fs-5"></i>
                                <h6 class="fw-semibold mb-0" style="font-size: 15px;">
                                    {{ __('Product Description') }}
                                </h6>
                            </div>
                            <textarea name="description" id="productDescription" rows="6"
                                class="form-control @error('description') is-invalid @enderror" placeholder="Write your product description..."
                                style="min-height: 150px;">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ═══════════════════ Product Images Section ═══════════════════ --}}
                        <div class="col-12">
                            <div class="section-card">
                                <div class="section-card-header">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-image text-success fs-5"></i>
                                        <h6 class="fw-semibold mb-0" style="font-size: 15px;">
                                            {{ __('Product Images') }}
                                        </h6>
                                    </div>
                                </div>
                                <div class="section-card-body">
                                    <div class="drop-zone" id="dropZone">
                                        <input type="file" name="image" id="imageInput" accept="image/*" hidden>
                                        <div id="dropContent">
                                            <i class="bi bi-cloud-arrow-up text-success" style="font-size: 2.4rem;"></i>
                                            <p class="mb-0 mt-3 fw-semibold" style="font-size: 14px;">
                                                {{ __('Drop image here or click to browse') }}
                                            </p>
                                            <p class="text-muted mb-0 mt-1" style="font-size: 12px;">
                                                {{ __('PNG, JPG up to 4MB') }}
                                            </p>
                                        </div>
                                        <div id="dropPreview" class="d-none">
                                            <img id="previewImg" src="" alt="Preview">
                                            <p class="mb-0 mt-2 text-muted" id="fileName" style="font-size: 13px;"></p>
                                            <p class="text-muted mb-0 mt-1" style="font-size: 11px;">
                                                {{ __('Click or drop to replace') }}
                                            </p>
                                        </div>
                                    </div>
                                    @error('image')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <label class="form-label fw-semibold small mb-1">{{ __('Status') }}</label>
                            <div class="d-flex gap-3 pt-1">
                                <div class="form-check">
                                    <input type="radio" name="status" value="1" class="form-check-input"
                                        id="statActive" {{ old('status', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="statActive">{{ __('Active') }}</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="status" value="0" class="form-check-input"
                                        id="statInactive" {{ !old('status', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="statInactive">{{ __('Inactive') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success btn-sm px-4">
                            <i class="bi bi-check-lg me-1"></i>{{ __('Create') }}
                        </button>
                        <a href="{{ route('admin.products.index') }}"
                            class="btn btn-outline-secondary btn-sm px-4">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('createProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var form = this;
            var btn = form.querySelector('button[type="submit"]');
            var alert = document.getElementById('formAlert');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Creating...';
            alert.classList.add('d-none');
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(function(r) {
                return r.json().then(function(data) {
                    if (r.ok) {
                        window.location.href = '{{ route('admin.products.index') }}';
                    } else {
                        var msg = data.message || 'Validation error.';
                        if (data.errors) {
                            msg = Object.values(data.errors).flat().join('<br>');
                        }
                        alert.className = 'alert alert-danger mb-3 py-2 small';
                        alert.innerHTML = msg;
                        alert.classList.remove('d-none');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Create';
                    }
                });
            }).catch(function() {
                alert.className = 'alert alert-danger mb-3 py-2 small';
                alert.innerHTML = 'Network error. Please try again.';
                alert.classList.remove('d-none');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Create';
            });
        });
    </script>
@endpush
