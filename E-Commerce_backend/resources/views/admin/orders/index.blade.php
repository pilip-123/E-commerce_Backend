@extends('layouts.admin')

@section('title', __('Orders'))

@section('content')
<div class="container-fluid p-0">

    {{-- Header with stats --}}

    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle flex-shrink-0"
                         style="width: 48px; height: 48px;">
                        <i class="bi bi-receipt text-success fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Total Orders') }}</p>
                        <h5 class="fw-bold mb-0">{{ $totalOrders }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-warning-subtle flex-shrink-0"
                         style="width: 48px; height: 48px;">
                        <i class="bi bi-clock text-warning fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Pending') }}</p>
                        <h5 class="fw-bold mb-0">{{ $pendingCount }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0"
                         style="width: 48px; height: 48px;">
                        <i class="bi bi-currency-dollar text-info fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Revenue') }}</p>
                        <h5 class="fw-bold mb-0">${{ number_format($revenueTotal, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Orders table --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h5 class="fw-bold mb-0">{{ __('All Orders') }}</h5>
                <small class="text-muted">{{ $orders->total() }} {{ __('total') }}</small>
            </div>
            <div class="d-flex gap-2">
                @include('admin.partials.export-dropdown', ['exportRoute' => route('admin.export.orders')])
            </div>
        </div>
        <div class="card-body border-bottom px-3 py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-auto">
                    <input type="search" name="search" class="form-control form-control-sm" placeholder="{{ __('Search orders...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                        <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>{{ __('Shipped') }}</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    </select>
                </div>
                <div class="col-auto">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" placeholder="{{ __('From') }}">
                </div>
                <div class="col-auto">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" placeholder="{{ __('To') }}">
                </div>
                <div class="col-auto d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-funnel me-1"></i>{{ __('Filter') }}</button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Order') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Customer') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Items') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Total') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Status') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase">{{ __('Date') }}</th>
                            <th class="px-4 py-3 small fw-bold text-uppercase text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            @php
                                $badgeClass = match($order->status) {
                                    'pending' => 'bg-warning-subtle text-warning-emphasis',
                                    'processing' => 'bg-info-subtle text-info-emphasis',
                                    'shipped' => 'bg-primary-subtle text-primary-emphasis',
                                    'delivered' => 'bg-success-subtle text-success-emphasis',
                                    'cancelled' => 'bg-danger-subtle text-danger-emphasis',
                                    default => 'bg-secondary-subtle text-secondary-emphasis',
                                };
                                $badgeIcon = match($order->status) {
                                    'pending' => 'bi-clock',
                                    'processing' => 'bi-gear',
                                    'shipped' => 'bi-truck',
                                    'delivered' => 'bi-check-circle',
                                    'cancelled' => 'bi-x-circle',
                                    default => 'bi-question-circle',
                                };
                                $itemCount = $order->items?->count() ?? 0;
                            @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-dark text-white fw-bold flex-shrink-0"
                                             style="width: 40px; height: 40px; font-size: 13px;">
                                            #{{ $order->id }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($order->user?->image_url)
                                            <img src="{{ asset('storage/' . $order->user->image_url) }}" alt="{{ $order->user->name }}"
                                                 class="rounded-circle flex-shrink-0"
                                                 style="width: 34px; height: 34px; object-fit: cover;">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-success text-white fw-bold flex-shrink-0"
                                                 style="width: 34px; height: 34px; font-size: 12px;">
                                                {{ strtoupper(substr($order->user->name ?? '?', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="fw-semibold mb-0 small">{{ $order->user->name ?? __('N/A') }}</p>
                                            @if ($order->phone)
                                                <span class="text-muted" style="font-size: 11px;">{{ $order->phone }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="fw-semibold">{{ $itemCount }}</span>
                                    <span class="text-muted small">{{ __('item') }}{{ $itemCount !== 1 ? 's' : '' }}</span>
                                    @if ($itemCount > 0)
                                        <div class="mt-1 d-flex flex-wrap gap-1">
                                            @foreach ($order->items->take(2) as $item)
                                                <span class="badge bg-light text-dark fw-normal" style="font-size: 10px;">
                                                    {{ Str::limit($item->product->name ?? __('Product'), 18) }}
                                                </span>
                                            @endforeach
                                            @if ($itemCount > 2)
                                                <span class="badge bg-light text-muted fw-normal" style="font-size: 10px;">
                                                    +{{ $itemCount - 2 }} {{ __('more') }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 fw-bold text-success">${{ number_format($order->total_amount, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span class="badge {{ $badgeClass }} px-3 py-2 d-inline-flex align-items-center gap-1 rounded-pill">
                                        <i class="bi {{ $badgeIcon }}"></i>
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-muted small">
                                    <div>{{ $order->created_at?->format('M d, Y') }}</div>
                                    <div style="font-size: 10px;">{{ $order->created_at?->format('h:i A') }}</div>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        @if ($order->status !== 'cancelled' && $order->status !== 'delivered')
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-success dropdown-toggle" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                                    <li><span class="dropdown-item-text small text-muted fw-semibold">{{ __('Update status') }}</span></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                                                        @if ($status !== $order->status)
                                                            <li>
                                                                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                                                                    @csrf @method('PUT')
                                                                    <input type="hidden" name="status" value="{{ $status }}">
                                                                    <button type="submit" class="dropdown-item small d-flex align-items-center gap-2">
                                                                        <i class="bi {{ match($status) {
                                                                            'pending' => 'bi-clock',
                                                                            'processing' => 'bi-gear',
                                                                            'shipped' => 'bi-truck',
                                                                            'delivered' => 'bi-check-circle',
                                                                            'cancelled' => 'bi-x-circle',
                                                                        } }}"></i>
                                                                        {{ ucfirst($status) }}
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                data-url="{{ route('admin.orders.destroy', $order->id) }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-5 text-center text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                                    {{ __('No orders found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($orders->hasPages())
            <div class="card-footer bg-white py-3 rounded-4 border-0">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
