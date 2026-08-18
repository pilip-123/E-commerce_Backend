@extends('layouts.admin')

@section('title', __('Supplier Details'))

@section('content')
<div class="container-fluid p-0">

    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            @if ($supplier->image)
                <img src="{{ asset('storage/' . $supplier->image) }}" alt="{{ $supplier->name }}" class="rounded-circle object-fit-cover flex-shrink-0" style="width: 56px; height: 56px;">
            @else
                <div class="d-flex align-items-center justify-content-center rounded-3 text-white fw-bold flex-shrink-0" style="width: 56px; height: 56px; font-size: 22px; background: var(--admin-primary);">
                    {{ strtoupper(substr($supplier->name, 0, 1)) }}
                </div>
            @endif
            <div>
                <h4 class="fw-bold mb-1">{{ $supplier->name }}</h4>
                <p class="text-muted small mb-0">{{ $supplier->company ?: __('Supplier') }} · {{ $supplier->email ?: '—' }}</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if (auth()->user()->hasPermission('suppliers.edit'))
                <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-sm btn-success">
                    <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
                </a>
            @endif
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0" style="width: 44px; height: 44px;">
                        <i class="bi bi-receipt text-info fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Total Purchase Orders') }}</p>
                        <h5 class="fw-bold mb-0">{{ $stats['totalOrders'] }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle flex-shrink-0" style="width: 44px; height: 44px;">
                        <i class="bi bi-cash-stack text-success fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Total Purchase Amount') }}</p>
                        <h5 class="fw-bold mb-0">${{ number_format($stats['totalAmount'], 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-warning-subtle flex-shrink-0" style="width: 44px; height: 44px;">
                        <i class="bi bi-hourglass-split text-warning fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Pending Payments') }}</p>
                        <h5 class="fw-bold mb-0">${{ number_format($stats['pendingPayments'], 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-danger-subtle flex-shrink-0" style="width: 44px; height: 44px;">
                        <i class="bi bi-arrow-return-left text-danger fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Returned Products') }}</p>
                        <h5 class="fw-bold mb-0">{{ $returnedProducts->count() }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Info --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 rounded-4">
                    <h5 class="fw-bold mb-0 fs-6"><i class="bi bi-info-circle me-2 text-success"></i>{{ __('Supplier Information') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                        <li class="d-flex align-items-start gap-3">
                            <i class="bi bi-person text-success fs-5"></i>
                            <div>
                                <small class="text-muted d-block">{{ __('Contact Person') }}</small>
                                <span class="fw-semibold">{{ $supplier->contact_person ?: '—' }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <i class="bi bi-telephone text-success fs-5"></i>
                            <div>
                                <small class="text-muted d-block">{{ __('Phone') }}</small>
                                <span class="fw-semibold">{{ $supplier->phone ?: '—' }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <i class="bi bi-envelope text-success fs-5"></i>
                            <div>
                                <small class="text-muted d-block">{{ __('Email') }}</small>
                                <span class="fw-semibold">{{ $supplier->email ?: '—' }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <i class="bi bi-building text-success fs-5"></i>
                            <div>
                                <small class="text-muted d-block">{{ __('Company') }}</small>
                                <span class="fw-semibold">{{ $supplier->company ?: '—' }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <i class="bi bi-geo-alt text-success fs-5"></i>
                            <div>
                                <small class="text-muted d-block">{{ __('Address') }}</small>
                                <span class="fw-semibold">{{ $supplier->address ?: '—' }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <i class="bi bi-calendar-check text-success fs-5"></i>
                            <div>
                                <small class="text-muted d-block">{{ __('Created At') }}</small>
                                <span class="fw-semibold">{{ $supplier->created_at?->format('M d, Y') }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <i class="bi bi-calendar-event text-success fs-5"></i>
                            <div>
                                <small class="text-muted d-block">{{ __('Updated At') }}</small>
                                <span class="fw-semibold">{{ $supplier->updated_at?->format('M d, Y') }}</span>
                            </div>
                        </li>
                    </ul>
                    @if ($supplier->notes)
                        <hr>
                        <small class="text-muted d-block mb-1 fw-bold">{{ __('Notes') }}</small>
                        <p class="mb-0 small">{{ $supplier->notes }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            {{-- Purchase History --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 rounded-4 d-flex align-items-center justify-content-between gap-2">
                    <h5 class="fw-bold mb-0 fs-6"><i class="bi bi-receipt me-2 text-success"></i>{{ __('Purchase History') }}</h5>
                    <a href="{{ route('admin.purchases.index', ['supplier_id' => $supplier->id]) }}" class="btn btn-sm btn-outline-success">{{ __('View All') }}</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('PO Number') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Date') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Items') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Total') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Status') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                    <tr>
                                        <td class="px-4 py-3 font-monospace fw-bold small">{{ $order->po_number }}</td>
                                        <td class="px-4 py-3 text-muted small">{{ $order->order_date?->format('M d, Y') }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="badge bg-success-subtle text-success-emphasis rounded-pill">{{ $order->items->sum('quantity') }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-end fw-bold">${{ number_format($order->grand_total, 2) }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @php
                                                $badge = match ($order->status) {
                                                    'draft' => ['var(--badge-gray-bg)', 'var(--badge-gray-text)'],
                                                    'pending' => ['var(--badge-warning-bg)', 'var(--badge-warning-text)'],
                                                    'approved', 'ordered' => ['var(--badge-info-bg)', 'var(--badge-info-text)'],
                                                    'partially_received' => ['var(--badge-warning-bg)', 'var(--badge-warning-text)'],
                                                    'received' => ['var(--badge-success-bg)', 'var(--badge-success-text)'],
                                                    'cancelled' => ['var(--badge-red-bg)', 'var(--badge-red-text)'],
                                                    default => ['var(--badge-gray-bg)', 'var(--badge-gray-text)'],
                                                };
                                            @endphp
                                            <span class="badge rounded-pill text-uppercase" style="font-size: 10px; background: {{ $badge[0] }}; color: {{ $badge[1] }};">{{ str_replace('_', ' ', $order->status) }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-end">
                                            <a href="{{ route('admin.purchases.show', $order) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-4 text-center text-muted">{{ __('No purchase orders yet.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Returned Products --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 rounded-4">
                    <h5 class="fw-bold mb-0 fs-6"><i class="bi bi-arrow-return-left me-2 text-danger"></i>{{ __('Returned Products') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Product') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-center">{{ __('Quantity') }}</th>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($returnedProducts as $item)
                                    <tr>
                                        <td class="px-4 py-3 fw-semibold small">{{ $item['product']->name ?? __('Deleted') }}</td>
                                        <td class="px-4 py-3 text-center"><span class="badge rounded-pill" style="background: var(--badge-red-bg); color: var(--badge-red-text);">-{{ $item['quantity'] }}</span></td>
                                        <td class="px-4 py-3 text-end fw-bold">${{ number_format($item['amount'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-center text-muted">{{ __('No products have been returned to this supplier.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
