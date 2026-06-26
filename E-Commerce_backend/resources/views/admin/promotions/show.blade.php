@extends('layouts.admin')

@section('title', $promotion->name)

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0">{{ $promotion->name }}</h5>
                <small class="text-muted">Created {{ $promotion->created_at->format('M d, Y') }}</small>
            </div>
            <a href="{{ route('admin.promotions.edit', $promotion->id) }}" class="btn btn-success btn-sm">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr>
                            <th class="text-muted small fw-bold">Discount</th>
                            <td>
                                @if ($promotion->discount_type === 'percentage')
                                    {{ $promotion->discount_value }}% off
                                @else
                                    ${{ number_format($promotion->discount_value, 2) }} off
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted small fw-bold">Period</th>
                            <td>{{ \Carbon\Carbon::parse($promotion->start_date)->format('M d, Y H:i') }} → {{ \Carbon\Carbon::parse($promotion->end_date)->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted small fw-bold">Status</th>
                            <td>
                                @if ($promotion->start_date <= now() && $promotion->end_date >= now())
                                    <span class="badge bg-success-subtle text-success-emphasis">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger-emphasis">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @if ($promotion->description)
                            <tr>
                                <th class="text-muted small fw-bold">Description</th>
                                <td>{{ $promotion->description }}</td>
                            </tr>
                        @endif
                    </table>
                </div>

                <div class="col-md-6">
                    <h6 class="fw-bold small text-uppercase text-muted mb-3">Discounted Products</h6>
                    @if ($promotion->products->count())
                        <div class="list-group">
                            @foreach ($promotion->products as $product)
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                    <span>{{ $product->name }}</span>
                                    <span class="small text-muted">${{ number_format($product->price, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small">No products assigned to this promotion.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
