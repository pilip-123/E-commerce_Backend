@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4">
            <h5 class="fw-bold mb-0">Create Category</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Category Name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror" required
                               placeholder="e.g. Electronics">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Description <span class="text-muted fw-normal">(optional)</span></label>
                        <textarea name="description" rows="4"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Brief description of the category">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check-lg me-1"></i>Create Category
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
