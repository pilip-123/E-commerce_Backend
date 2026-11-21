@extends('layouts.admin')

@section('title', __('Purchasing Report'))

@section('content')
<div class="container-fluid p-0">

    {{-- Header + period filter --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">{{ __('Purchasing Report') }}</h4>
            {{-- <p class="text-muted small mb-0">{{ __('Purchase orders, returns and supplier spending for the selected period') }}</p>  --}}
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

    @php
        $hiden = false;
        // % change between the last two months of a series (null when there is no base month).
        $lastChange = function ($series) {
            $s = collect($series)->values();
            if ($s->count() < 2 || (float) $s[$s->count() - 2] <= 0) return null;
            return ((float) $s->last() - (float) $s[$s->count() - 2]) / (float) $s[$s->count() - 2] * 100;
        };
        $returnRate = $totalPurchaseAmount > 0 ? $totalReturnedAmount / $totalPurchaseAmount * 100 : 0;
        $flow = $totalIncome + $netSpending;
        $incomeShare = $flow > 0 ? $totalIncome / $flow * 100 : 50;
        $marginTone = $netMargin >= 20 ? 'good' : ($netMargin >= 0 ? 'warn' : 'bad');
        $statusColors = ['pending' => '#f59e0b', 'approved' => '#3b82f6', 'ordered' => '#8b5cf6', 'partially_received' => '#06b6d4', 'received' => '#10b981', 'cancelled' => '#ef4444'];
        $statusTotal = max(1, $statusBreakdown->sum('count'));
        $periodLabel = $from->translatedFormat('M j, Y') . ' – ' . $to->translatedFormat('M j, Y');
    @endphp

    {{-- Income vs Purchases --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3">
            <x-admin.kpi color="green" icon="bi-cash-stack" :label="__('Total Income')" :value="'$' . number_format($totalIncome, 2)"
                :trend="$lastChange($incomeTotals)" :spark="$incomeTotals" :caption="__('Sales income') . ' · ' . $chartMonths->count() . ' ' . __('months')" />
        </div>
        <div class="col-6 col-xl-3">
            <x-admin.kpi color="red" icon="bi-cart-dash" :label="__('Net Spending')" :value="'$' . number_format($netSpending, 2)"
                :trend="$lastChange($purchaseTotals)" :higher-is-good="false" :spark="$purchaseTotals" :caption="__('Purchases after returns')" />
        </div>
        <div class="col-6 col-xl-3">
            <x-admin.kpi color="violet" icon="bi-graph-up-arrow" :label="__('Net Income')" :value="($netIncome < 0 ? '−$' : '$') . number_format(abs($netIncome), 2)"
                :value-class="$netIncome < 0 ? 'text-danger' : ''" :chip="$netIncome >= 0 ? __('Profit') : __('Loss')" :chip-tone="$netIncome >= 0 ? 'good' : 'bad'"
                :chip-icon="$netIncome >= 0 ? 'bi-check2-circle' : 'bi-exclamation-circle'">
                {{ __('Income vs spending') }}
                <div class="app-kpi-meter"><span style="width:{{ $incomeShare }}%;background:#10b981"></span><span style="width:{{ 100 - $incomeShare }}%;background:#ef4444"></span></div>
            </x-admin.kpi>
        </div>
        <div class="col-6 col-xl-3">
            <x-admin.kpi color="amber" icon="bi-percent" :label="__('Net Margin')" :value="number_format($netMargin, 1) . '%'"
                :chip="$netMargin >= 20 ? __('Healthy') : ($netMargin >= 0 ? __('Thin') : __('Negative'))" :chip-tone="$marginTone"
                :caption="__('Net income share of income')" :meter="$netMargin" />
        </div>
    </div>

    {{-- Summary stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <x-admin.kpi color="sky" icon="bi-receipt" :label="__('Total Purchases')" :value="'$' . number_format($netSpending, 2)"
                :chip="$totalPurchaseOrders ? '$' . number_format($totalPurchaseAmount / $totalPurchaseOrders, 0) . ' ' . __('avg') : null" chip-icon="bi-calculator" :chip-title="__('Average purchase order value')"
                :caption="__('Gross') . ' $' . number_format($totalPurchaseAmount, 2) . ' · ' . $periodLabel" />
        </div>
        <div class="col-6 col-xl-3">
            <x-admin.kpi color="teal" icon="bi-bag-check" :label="__('Purchase Orders')" :value="number_format($totalPurchaseOrders)"
                :chip="$statusBreakdown->isNotEmpty() ? $statusBreakdown->count() . ' ' . __('statuses') : null" chip-icon="bi-diagram-3">
                <div class="app-kpi-meter" role="img" aria-label="{{ __('Purchase orders by status') }}">
                    @foreach ($statusBreakdown as $row)
                        <span style="width:{{ $row['count'] / $statusTotal * 100 }}%;background:{{ $statusColors[$row['status']] ?? '#94a3b8' }}" title="{{ __(ucfirst(str_replace('_', ' ', $row['status']))) }}: {{ $row['count'] }}"></span>
                    @endforeach
                </div>
                <div class="app-kpi-legend">
                    @forelse ($statusBreakdown->take(3) as $row)
                        <span><i style="background:{{ $statusColors[$row['status']] ?? '#94a3b8' }}"></i>{{ $row['count'] }} {{ __(str_replace('_', ' ', $row['status'])) }}</span>
                    @empty
                        <span>{{ __('No purchase orders in this period') }}</span>
                    @endforelse
                </div>
            </x-admin.kpi>
        </div>
        <div class="col-6 col-xl-3">
            <x-admin.kpi color="red" icon="bi-arrow-return-left" :label="__('Returned Value')" :value="'$' . number_format($totalReturnedAmount, 2)"
                :chip="number_format($returnRate, 1) . '%'" :chip-tone="$returnRate >= 10 ? 'bad' : ($returnRate > 0 ? 'warn' : 'good')" :chip-title="__('Return rate (share of purchases returned)')"
                :spark="$returnTotals" :caption="__('of purchases sent back')" :meter="$returnRate" />
        </div>
        <div class="col-6 col-xl-3">
            <x-admin.kpi color="amber" icon="bi-box-seam" :label="__('Units Returned')" :value="number_format($totalReturnedUnits)"
                :chip="$totalReturnedUnits ? '$' . number_format($totalReturnedAmount / $totalReturnedUnits, 2) . '/' . __('unit') : null" chip-icon="bi-tag"
                :caption="$totalReturnedUnits ? __('Average value per returned unit') : __('Nothing returned in this period')" />
        </div>
    </div>

    {{-- Income vs Purchases chart --}}
    @include('admin.partials.charts')
    @php $netTotals = $incomeTotals->map(fn ($v, $i) => round($v - ($purchaseTotals[$i] ?? 0), 2)); @endphp
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                <div>
                    <h5 class="card-title fw-bold mb-0" style="font-size: 15px;">{{ __('Income vs Purchases') }}</h5>
                    <small class="text-muted">{{ __('Monthly sales income, purchase spending and what is left over') }}</small>
                </div>
                <div class="ac-legend">
                    <span><i style="background:#10b981"></i>{{ __('Income') }} <b>${{ number_format($incomeTotals->sum(), 2) }}</b></span>
                    <span><i style="background:#ef4444"></i>{{ __('Purchases') }} <b>${{ number_format($purchaseTotals->sum(), 2) }}</b></span>
                    <span><i style="background:#7c3aed"></i>{{ __('Net') }} <b class="{{ $netTotals->sum() < 0 ? 'text-danger' : '' }}">${{ number_format($netTotals->sum(), 2) }}</b></span>
                </div>
            </div>
            <div id="incomePurchaseChart" style="height: 280px;"></div>
        </div>
    </div>

    {{-- Monthly charts --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                        <h5 class="card-title fw-bold mb-0" style="font-size: 15px;">{{ __('Monthly Purchases') }}</h5>
                        <div class="ac-legend"><span><i style="background:#0ea5e9"></i>{{ __('Avg') }} <b>${{ number_format($purchaseTotals->avg() ?? 0, 2) }}</b> / {{ __('month') }}</span></div>
                    </div>
                    <div id="monthlyPurchaseChart" style="height: 220px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                        <h5 class="card-title fw-bold mb-0" style="font-size: 15px;">{{ __('Monthly Returns') }}</h5>
                        <div class="ac-legend"><span><i style="background:#ef4444"></i>{{ __('Total') }} <b>${{ number_format($returnTotals->sum(), 2) }}</b></span></div>
                    </div>
                    <div id="monthlyReturnChart" style="height: 220px;"></div>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var months = @json($chartMonths);
        AppCharts.bars('incomePurchaseChart', {
            labels: months,
            emptyText: @json(__('No income or purchases in this period')),
            series: [
                { name: @json(__('Income')), values: @json($incomeTotals), color: '#10b981', format: 'money' },
                { name: @json(__('Purchases')), values: @json($purchaseTotals), color: '#ef4444', format: 'money' }
            ],
            line: { name: @json(__('Net')), values: @json($netTotals), color: '#7c3aed', format: 'money' }
        });
        AppCharts.line('monthlyPurchaseChart', {
            labels: months,
            emptyText: @json(__('No purchases in this period')),
            series: [{ name: @json(__('Purchases')), values: @json($purchaseTotals), color: '#0ea5e9', format: 'money' }]
        });
        AppCharts.bars('monthlyReturnChart', {
            labels: months,
            emptyText: @json(__('No returns in this period')),
            series: [{ name: @json(__('Returns')), values: @json($returnTotals), color: '#ef4444', format: 'money' }]
        });
    });
</script>
@endpush
