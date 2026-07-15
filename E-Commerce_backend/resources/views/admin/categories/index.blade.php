@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0">Categories</h5>
                <small class="text-muted">{{ $categories->total() }} total</small>
            </div>
            <div class="d-flex gap-2">
                @include('admin.partials.export-dropdown', ['exportRoute' => route('admin.export.categories')])
                <a href="{{ route('admin.categories.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>New Category
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
                            <th class="px-4 py-3 small fw-bold text-uppercase">Description</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">Products</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td class="px-4 py-3 fw-semibold text-muted small">#{{ $category->id }}</td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-success-subtle text-success-emphasis px-3 py-2 fs-6">
                                        {{ $category->name }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-muted small">{{ Str::limit($category->description, 60) ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-info-subtle text-info-emphasis px-3 py-2">{{ $category->products_count }}</span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}"
                                       class="btn btn-sm btn-outline-success me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-url="{{ route('admin.categories.destroy', $category->id) }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-5 text-center text-muted">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($categories->hasPages())
            <div class="card-footer bg-white py-3 rounded-4 border-0">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
