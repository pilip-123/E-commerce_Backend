@extends('layouts.admin')

@section('title', __('Purchasing Report'))

@section('content')
<div class="container-fluid p-0">

    {{-- Header + period filter --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">{{ __('Purchasing Report') }}</h4>
            <p class="text-muted small mb-0">{{ __('Purchase orders, returns and supplier spending for the selected period') }}</p>
        </div>
        <form method="GET" class="d-flex gap-2 align-items-end flex-wrap">
            <div>
                <label class="form-label small fw-semibold mb-1">{{ __('From') }}</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ $from->format('Y-m-d') }}">
            </div>
            <div>
                <label class="form-label small fw-semibold mb-1">{{ __('To') }}</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ $to->format('Y-m-d') }}">
            </div>
            <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-funnel me-1"></i>{{ __('Filter') }}</button>
            <a href="{{ route('admin.reports.purchasing') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
        </form>
    </div>

    {{-- Income vs Purchases --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-cash-stack text-success fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Total Income') }}</p>
                        <h5 class="fw-bold mb-0">${{ number_format($totalIncome, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-danger-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-cart-dash text-danger fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Net Spending') }}</p>
                        <h5 class="fw-bold mb-0">${{ number_format($netSpending, 2) }}</h5>
                        <p class="text-muted mb-0" style="font-size: 11px;">{{ __('Purchases after returns') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-graph-up-arrow text-primary fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Net Income') }}</p>
                        <h5 class="fw-bold mb-0 {{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">${{ number_format($netIncome, 2) }}</h5>
                        <p class="text-muted mb-0" style="font-size: 11px;">{{ __('Income after purchase spending') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-warning-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-percent text-warning fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Net Margin') }}</p>
                        <h5 class="fw-bold mb-0">{{ number_format($netMargin, 1) }}%</h5>
                        <p class="text-muted mb-0" style="font-size: 11px;">{{ __('Net income share of income') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary stats --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-receipt text-info fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Total Purchases') }}</p>
                        <h5 class="fw-bold mb-0">${{ number_format($netSpending, 2) }}</h5>
                        <p class="text-muted mb-0" style="font-size: 11px;">{{ __('After returns applied') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-bag-check text-success fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Purchase Orders') }}</p>
                        <h5 class="fw-bold mb-0">{{ number_format($totalPurchaseOrders) }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-danger-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-arrow-return-left text-danger fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Returned Value') }}</p>
                        <h5 class="fw-bold mb-0">${{ number_format($totalReturnedAmount, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-warning-subtle flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-box-seam text-warning fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">{{ __('Units Returned') }}</p>
                        <h5 class="fw-bold mb-0">{{ number_format($totalReturnedUnits) }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Income vs Purchases chart --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <h5 class="card-title fw-bold mb-1" style="font-size: 15px;">{{ __('Income vs Purchases') }}</h5>
            <p class="text-muted small mb-3">{{ __('Sales income compared to purchase spending per month') }}</p>
            @php $maxValue = max(max($incomeTotals->max() ?? 0, $purchaseTotals->max() ?? 0), 1); @endphp
            <div class="d-flex align-items-end gap-2" style="height: 200px;">
                @foreach ($chartMonths as $i => $month)
                    <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-end h-100" style="min-width: 0;">
                        <div class="d-flex align-items-end justify-content-center gap-1 w-100" style="height: 100%;">
                            <div style="width: 40%; max-width: 18px; height: {{ ($incomeTotals[$i] ?? 0) > 0 ? max((($incomeTotals[$i] ?? 0) / $maxValue) * 100, 2) : 2 }}%; border-radius: 4px 4px 0 0; background: linear-gradient(180deg, #34d399, #059669);" title="{{ __('Income') }}: ${{ number_format($incomeTotals[$i] ?? 0, 2) }}"></div>
                            <div style="width: 40%; max-width: 18px; height: {{ ($purchaseTotals[$i] ?? 0) > 0 ? max((($purchaseTotals[$i] ?? 0) / $maxValue) * 100, 2) : 2 }}%; border-radius: 4px 4px 0 0; background: linear-gradient(180deg, #f87171, #dc2626);" title="{{ __('Purchases') }}: ${{ number_format($purchaseTotals[$i] ?? 0, 2) }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-between small text-muted mt-2 px-1">
                @foreach ($chartMonths as $month)
                    <span style="font-size: 10px;">{{ $month }}</span>
                @endforeach
            </div>
            <div class="d-flex gap-4 mt-3 small">
                <span class="d-inline-flex align-items-center gap-2"><span style="width: 10px; height: 10px; border-radius: 3px; background: #059669; display: inline-block;"></span>{{ __('Income') }}</span>
                <span class="d-inline-flex align-items-center gap-2"><span style="width: 10px; height: 10px; border-radius: 3px; background: #dc2626; display: inline-block;"></span>{{ __('Purchases') }}</span>
            </div>
        </div>
    </div>

    {{-- Monthly charts --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3" style="font-size: 15px;">{{ __('Monthly Purchases') }}</h5>
                    @php $maxPurchase = max($purchaseTotals->max() ?? 0, 1); @endphp
                    <div class="d-flex align-items-end gap-2" style="height: 160px;">
                        @foreach ($purchaseTotals as $i => $total)
                            <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-end h-100" style="min-width: 0;">
                                <div style="width: 100%; max-width: 34px; height: {{ $total > 0 ? max(($total / $maxPurchase) * 100, 2) : 2 }}%; border-radius: 6px 6px 0 0; background: linear-gradient(180deg, #34d399, #059669);"></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-between small text-muted mt-2 px-1">
                        @foreach ($chartMonths as $month)
                            <span style="font-size: 10px;">{{ $month }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3" style="font-size: 15px;">{{ __('Monthly Returns') }}</h5>
                    @php $maxReturn = max($returnTotals->max() ?? 0, 1); @endphp
                    <div class="d-flex align-items-end gap-2" style="height: 160px;">
                        @foreach ($returnTotals as $i => $total)
                            <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-end h-100" style="min-width: 0;">
                                <div style="width: 100%; max-width: 34px; height: {{ $total > 0 ? max(($total / $maxReturn) * 100, 2) : 2 }}%; border-radius: 6px 6px 0 0; background: linear-gradient(180deg, #f87171, #dc2626);"></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-between small text-muted mt-2 px-1">
                        @foreach ($chartMonths as $month)
                            <span style="font-size: 10px;">{{ $month }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Supplier analytics --}}
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3" style="font-size: 15px;">{{ __('Spend by Supplier') }}</h5>
                    @forelse ($spendBySupplier as $row)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                                <span class="fw-semibold small text-truncate">{{ $row->supplier->name ?? __('Deleted') }}</span>
                                <span class="fw-bold small flex-shrink-0">${{ number_format($row->total, 2) }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" style="width: {{ ($row->total / $maxSpend) * 100 }}%; background: linear-gradient(90deg, #0ea5e9, #0284c7);"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4 mb-0 small">{{ __('No purchases in this period.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3" style="font-size: 15px;">{{ __('Order Status Breakdown') }}</h5>
                    @php
                        $badge = fn ($status) => match ($status) {
                            'draft' => ['var(--badge-gray-bg)', 'var(--badge-gray-text)'],
                            'pending' => ['var(--badge-warning-bg)', 'var(--badge-warning-text)'],
                            'approved', 'ordered' => ['var(--badge-info-bg)', 'var(--badge-info-text)'],
                            'partially_received' => ['var(--badge-warning-bg)', 'var(--badge-warning-text)'],
                            'received' => ['var(--badge-success-bg)', 'var(--badge-success-text)'],
                            'cancelled' => ['var(--badge-red-bg)', 'var(--badge-red-text)'],
                            default => ['var(--badge-gray-bg)', 'var(--badge-gray-text)'],
                        };
                    @endphp
                    <div class="d-flex flex-column gap-3">
                        @forelse ($statusBreakdown as $row)
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="badge rounded-pill text-uppercase" style="font-size: 10px; background: {{ $badge($row['status'])[0] }}; color: {{ $badge($row['status'])[1] }};">{{ str_replace('_', ' ', $row['status']) }}</span>
                                <span class="fw-bold">{{ number_format($row['count']) }}</span>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4 mb-0 small">{{ __('No orders in this period.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3" style="font-size: 15px;">{{ __('Top Suppliers') }}</h5>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-2 py-2 small fw-bold text-uppercase">{{ __('Supplier') }}</th>
                                    <th class="px-2 py-2 small fw-bold text-uppercase text-center">{{ __('Orders') }}</th>
                                    <th class="px-2 py-2 small fw-bold text-uppercase text-end">{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topSuppliers as $row)
                                    <tr>
                                        <td class="px-2 py-2 fw-semibold small">{{ $row->supplier->name ?? __('Deleted') }}</td>
                                        <td class="px-2 py-2 text-center small">{{ $row->order_count }}</td>
                                        <td class="px-2 py-2 text-end fw-bold small">${{ number_format($row->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-2 py-4 text-center text-muted small">{{ __('No data') }}</td>
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
