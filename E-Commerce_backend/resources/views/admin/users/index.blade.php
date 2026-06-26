@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4">
            <div>
                <h5 class="fw-bold mb-0">Users</h5>
                <small class="text-muted">{{ $users->total() }} total</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 small fw-bold text-uppercase">ID</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Name</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Email</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Role</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">Actions</th>
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
                                    @if ($user->role === 'admin')
                                        <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">Admin</span>
                                    @else
                                        <span class="badge bg-info-subtle text-info-emphasis px-3 py-2">Customer</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                       class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        data-url="{{ route('admin.users.destroy', $user->id) }}"
                                        title="Delete user">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-5 text-center text-muted">No users found.</td>
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