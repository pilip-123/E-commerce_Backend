@extends('layouts.admin')

@section('title', __('Add Supplier'))

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4" style="max-width: 760px;">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2 text-success"></i>{{ __('Add Supplier') }}</h5>
                <small class="text-muted">{{ __('Create a new supplier record') }}</small>
            </div>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary btn-sm ms-auto">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.suppliers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold small mb-1">{{ __('Supplier Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small mb-1">{{ __('Company') }}</label>
                        <input type="text" name="company" value="{{ old('company') }}" class="form-control @error('company') is-invalid @enderror">
                        @error('company') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small mb-1">{{ __('Logo') }}</label>
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
                        @error('image') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small mb-1">{{ __('Contact Person') }}</label>
                        <input type="text" name="contact_person" value="{{ old('contact_person') }}" class="form-control @error('contact_person') is-invalid @enderror">
                        @error('contact_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small mb-1">{{ __('Phone') }}</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small mb-1">{{ __('Email') }}</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small mb-1">{{ __('Status') }}</label>
                        <div class="d-flex gap-3 pt-1">
                            <div class="form-check">
                                <input type="radio" name="status" value="1" class="form-check-input" id="statActive" {{ old('status', 1) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="statActive">{{ __('Active') }}</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="status" value="0" class="form-check-input" id="statInactive" {{ !old('status', 1) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="statInactive">{{ __('Inactive') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small mb-1">{{ __('Address') }}</label>
                        <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small mb-1">{{ __('Notes') }}</label>
                        <textarea name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <hr class="my-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check-lg me-1"></i>{{ __('Save Supplier') }}
                    </button>
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary px-4">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
