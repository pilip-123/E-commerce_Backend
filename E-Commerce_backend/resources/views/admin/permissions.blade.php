@extends('layouts.admin')

@section('title', __('Permissions'))

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">{{ __('Permissions Management') }}</h4>
            <p class="text-muted small mb-0">{{ __('Configure role-based permissions for all users.') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.permissions.update') }}" id="permissionsForm">
        @csrf
        @method('PUT')

        <div class="row g-4">
            @foreach ($permissionGroups as $groupName => $perms)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-header bg-white d-flex align-items-center gap-2 py-3 rounded-top-4" style="border-bottom: 2px solid var(--admin-primary-light);">
                            <span class="fw-bold" style="font-size: 0.85rem; color: var(--admin-text);">{{ $groupName }}</span>
                        </div>
                        <div class="card-body">
                            @foreach ($perms as $perm)
                                <div style="border-bottom: 1px solid var(--admin-border); padding: 6px 0;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span style="font-size: 0.75rem; font-weight: 500; color: var(--admin-text);">{{ $perm->display_name }}</span>
                                        <div class="d-flex gap-2">
                                            @foreach ($roles as $role)
                                                @php
                                                    $checked = in_array($perm->id, $rolePermissionIds[$role] ?? []);
                                                    $id = "p_{$role}_{$perm->id}";
                                                @endphp
                                                <div class="form-check d-flex align-items-center gap-1" style="margin: 0;">
                                                    <input type="checkbox" class="form-check-input role-perm"
                                                        name="perms[{{ $role }}][]"
                                                        value="{{ $perm->id }}"
                                                        id="{{ $id }}"
                                                        style="width: 13px; height: 13px; margin: 0; cursor: pointer;"
                                                        {{ $checked ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="{{ $id }}"
                                                        style="font-size: 0.65rem; cursor: pointer; text-transform: capitalize; color: var(--admin-text-muted);">
                                                        {{ $role }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-end mt-4 mb-5">
            <button type="submit" class="btn text-white fw-semibold px-5 py-2 rounded-3" id="saveBtn"
                style="background: var(--admin-primary);">
                <i class="bi bi-check-lg me-1"></i> {{ __('Save Permissions') }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('permissionsForm').addEventListener('submit', function () {
        const btn = document.getElementById('saveBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
    });
});
</script>
@endpush
