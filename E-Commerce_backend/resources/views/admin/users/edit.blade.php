@extends('layouts.admin')

@section('title', __('Edit User'))

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4" style="max-width: 720px;">
        <div class="card-header bg-white py-3 rounded-4">
            <h5 class="fw-bold mb-0 fs-6"><i class="bi bi-person-gear me-2 text-success"></i>{{ __('Edit User') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small mb-1">{{ __('Avatar') }}</label>
                        <div class="drop-zone {{ $user->image_url ? 'has-image' : '' }}" id="avatarZone" style="padding: 1rem;">
                            <input type="file" name="image" id="imageInput" accept="image/*" hidden>
                            <div id="dropContent" class="{{ $user->image_url ? 'd-none' : '' }}">
                                <i class="bi bi-person-circle text-success" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-1 small fw-semibold">{{ __('Drop image or click') }}</p>
                            </div>
                            <div id="dropPreview" class="{{ $user->image_url ? '' : 'd-none' }}">
                                <img id="previewImg"
                                     src="{{ $user->image_url ? asset('storage/' . $user->image_url) : '' }}"
                                     alt="Avatar" class="rounded-circle border"
                                     style="width: 72px; height: 72px; object-fit: cover;">
                                <p class="mb-0 mt-1 small text-muted" id="fileName">{{ $user->image_url ? __('Click to change') : '' }}</p>
                            </div>
                        </div>
                        @error('image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small mb-1">{{ __('Full Name') }}</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                               class="form-control form-control-sm @error('name') is-invalid @enderror" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small mb-1">{{ __('Email') }}</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                               class="form-control form-control-sm @error('email') is-invalid @enderror" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-sm-3">
                        <label class="form-label fw-semibold small mb-1">{{ __('Role') }}</label>
                        <div class="pt-1">
                            @if ($user->role === 'admin')
                                <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">{{ __('Admin') }}</span>
                            @else
                                <span class="badge bg-info-subtle text-info-emphasis px-3 py-2">{{ __('Customer') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <label class="form-label fw-semibold small mb-1">{{ __('Phone') }}</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="form-control form-control-sm @error('phone') is-invalid @enderror" placeholder="{{ __('Optional') }}">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small mb-1">{{ __('Password') }} <span class="text-muted fw-normal">({{ __('leave blank to keep') }})</span></label>
                        <div class="position-relative">
                            <input type="password" name="password" id="edit-user-password" class="form-control form-control-sm @error('password') is-invalid @enderror" placeholder="{{ __('New password') }}" style="padding-right: 36px;">
                            <button type="button" onclick="togglePwd('edit-user-password', this)" tabindex="-1"
                                class="position-absolute top-50 end-0 translate-middle-y btn border-0 d-flex align-items-center text-muted" style="padding: 4px 10px;">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small mb-1">{{ __('Address') }}</label>
                        <textarea name="address" rows="2" class="form-control form-control-sm @error('address') is-invalid @enderror"
                                  placeholder="{{ __('Optional') }}">{{ old('address', $user->address) }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="my-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success btn-sm px-4">
                        <i class="bi bi-check-lg me-1"></i>{{ __('Update') }}
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm px-4">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
