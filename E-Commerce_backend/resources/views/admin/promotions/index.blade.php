@extends('layouts.admin')

@section('title', 'Promotions')

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0">Promotions</h5>
                <small class="text-muted">{{ $promotions->total() }} total</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.export.promotions') }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-download me-1"></i>Export
                </a>
                <a href="{{ route('admin.promotions.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>New Promotion
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 small fw-bold text-uppercase">ID</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Name</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Discount</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Period</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Products</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Status</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($promotions as $promotion)
                            <tr>
                                <td class="px-4 py-3 fw-semibold text-muted small">#{{ $promotion->id }}</td>
                                <td class="px-4 py-3 fw-semibold">{{ $promotion->name }}</td>
                                <td class="px-4 py-3">
                                    @if ($promotion->discount_type === 'percentage')
                                        <span class="badge bg-info-subtle text-info-emphasis px-3 py-2">{{ $promotion->discount_value }}% off</span>
                                    @else
                                        <span class="badge bg-info-subtle text-info-emphasis px-3 py-2">${{ number_format($promotion->discount_value, 2) }} off</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 small">
                                    <div>{{ \Carbon\Carbon::parse($promotion->start_date)->format('M d, Y') }}</div>
                                    <div class="text-muted">→ {{ \Carbon\Carbon::parse($promotion->end_date)->format('M d, Y') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis px-3 py-2">{{ $promotion->products_count }} product(s)</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($promotion->start_date <= now() && $promotion->end_date >= now())
                                        <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">Active</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger-emphasis px-3 py-2">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('admin.promotions.show', $promotion->id) }}"
                                           class="btn btn-sm btn-outline-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.promotions.edit', $promotion->id) }}"
                                           class="btn btn-sm btn-outline-success" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                data-url="{{ route('admin.promotions.destroy', $promotion->id) }}" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-5 text-center text-muted">No promotions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($promotions->hasPages())
            <div class="card-footer bg-white py-3 rounded-4 border-0">
                {{ $promotions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
