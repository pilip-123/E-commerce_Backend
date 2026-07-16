@extends('layouts.admin')

@section('title', __('Users'))

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0">{{ __('Users') }}</h5>
                <small class="text-muted">{{ $users->total() }} {{ __('total') }}</small>
            </div>
            @include('admin.partials.export-dropdown', ['exportRoute' => route('admin.export.users')])
        </div>
        <div class="card-body border-bottom px-3 py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-auto">
                    <input type="search" name="search" class="form-control form-control-sm" placeholder="{{ __('Search by name or email...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <select name="role" class="form-select form-select-sm">
                        <option value="">{{ __('All Roles') }}</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                        <option value="manager" {{ request('role') === 'manager' ? 'selected' : '' }}>{{ __('Manager') }}</option>
                        <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>{{ __('Staff') }}</option>
                        <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>{{ __('Customer') }}</option>
                    </select>
                </div>
                <div class="col-auto d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-funnel me-1"></i>{{ __('Filter') }}</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 small fw-bold text-uppercase">ID</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Name') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Email') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Role') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td class="px-4 py-3 fw-semibold text-muted small">#{{ $user->id }}</td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($user->image_url)
                                            <img src="{{ asset('storage/' . $user->image_url) }}" alt="{{ $user->name }}"
                                                 class="rounded-circle flex-shrink-0"
                                                 style="width: 36px; height: 36px; object-fit: cover;">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-success text-white fw-bold flex-shrink-0"
                                                 style="width: 36px; height: 36px; font-size: 13px;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <span class="fw-semibold">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-muted">{{ $user->email }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $roleColors = [
                                            'admin' => ['bg' => '#dcfce7', 'text' => '#15803d'],
                                            'manager' => ['bg' => '#dbeafe', 'text' => '#1d4ed8'],
                                            'staff' => ['bg' => '#fef3c7', 'text' => '#b45309'],
                                            'customer' => ['bg' => '#f1f5f9', 'text' => '#475569'],
                                        ];
                                        $color = $roleColors[$user->role] ?? ['bg' => '#f1f5f9', 'text' => '#475569'];
                                    @endphp
                                    <span class="badge rounded-pill px-3 py-2 fw-semibold"
                                        style="background: {{ $color['bg'] }}; color: {{ $color['text'] }};">{{ ucfirst($user->role) }}</span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                       class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        data-url="{{ route('admin.users.destroy', $user->id) }}"
                                        title="{{ __('Delete user') }}">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-5 text-center text-muted">{{ __('No users found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($users->hasPages())
            <div class="card-footer bg-white py-3 rounded-4 border-0">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection