@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
    <div class="container-fluid p-0">
        <div class="card border-0 shadow-sm rounded-4">
            <div
                class="card-header bg-white py-3 rounded-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="fw-bold mb-0">All Users</h5>
                    <small class="text-muted">{{ $users->total() }} total</small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 small fw-bold text-uppercase">#</th>
                                <th class="px-4 py-3 small fw-bold text-uppercase">User</th>
                                <th class="px-4 py-3 small fw-bold text-uppercase">Contact</th>
                                <th class="px-4 py-3 small fw-bold text-uppercase">Role</th>
                                <th class="px-4 py-3 small fw-bold text-uppercase text-center">Orders</th>
                                <th class="px-4 py-3 small fw-bold text-uppercase text-end">Total Spent</th>
                                <th class="px-4 py-3 small fw-bold text-uppercase text-end">Joined</th>
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
                                                <img src="{{ asset('storage/' . $user->image_url) }}"
                                                    alt="{{ $user->name }}" class="rounded-circle flex-shrink-0"
                                                    style="width: 38px; height: 38px; object-fit: cover;">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-success text-white fw-bold flex-shrink-0"
                                                    style="width: 38px; height: 38px; font-size: 14px;">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <p class="fw-semibold mb-0 small">{{ $user->name }}</p>
                                                @if ($user->phone)
                                                    <p class="text-muted mb-0" style="font-size: 11px;">{{ $user->phone }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="mb-0 small">{{ $user->email }}</p>
                                        @if ($user->address)
                                            <p class="text-muted mb-0" style="font-size: 11px;">
                                                {{ Str::limit($user->address, 40) }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($user->role === 'admin')
                                            <span
                                                class="badge bg-success-subtle text-success-emphasis px-3 py-2">Admin</span>
                                        @else
                                            <span class="badge bg-info-subtle text-info-emphasis px-3 py-2">Customer</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="badge bg-info-subtle text-info-emphasis px-3 py-2">{{ $user->orders_count }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-end fw-bold text-success">
                                        ${{ number_format($user->orders_sum_total_amount ?? 0, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-end text-muted small">
                                        {{ $user->created_at?->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="{{ route('admin.users.show', $user->id) }}"
                                                class="btn btn-sm btn-outline-info" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if (auth()->id() !== $user->id)
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-url="{{ route('admin.users.destroy', $user->id) }}"
                                                    title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-5 text-center text-muted">No users found.</td>
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