@extends('layouts.admin')

@section('title', __('Transaction Details'))

@section('content')
<div class="container-fluid p-0">
    <div class="mb-3">
        <a href="{{ route('admin.inventory.history', request()->query()) }}" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to History') }}
        </a>
    </div>

    @php
        $typeConfig = [
            'stock_in' => ['icon' => 'bi-box-arrow-in-down', 'color' => '#059669', 'bg' => '#ecfdf5', 'label' => 'Stock In'],
            'stock_out' => ['icon' => 'bi-box-arrow-up', 'color' => '#dc2626', 'bg' => '#fef2f2', 'label' => 'Stock Out'],
            'transfer_out' => ['icon' => 'bi-arrow-right-circle', 'color' => '#0284c7', 'bg' => '#f0f9ff', 'label' => 'Transfer Out'],
            'transfer_in' => ['icon' => 'bi-arrow-left-circle', 'color' => '#0284c7', 'bg' => '#f0f9ff', 'label' => 'Transfer In'],
            'adjustment' => ['icon' => 'bi-sliders', 'color' => '#d97706', 'bg' => '#fffbeb', 'label' => 'Adjustment'],
            'stock_count' => ['icon' => 'bi-clipboard-check', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'label' => 'Stock Count'],
        ];
        $cfg = $typeConfig[$transaction->type] ?? ['icon' => 'bi-question-circle', 'color' => '#64748b', 'bg' => '#f8fafc', 'label' => ucfirst(str_replace('_', ' ', $transaction->type))];
    @endphp

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="px-4 py-4 d-flex align-items-center gap-3" style="background: {{ $cfg['bg'] }}; border-bottom: 3px solid {{ $cfg['color'] }};">
            <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 52px; height: 52px; background: {{ $cfg['color'] }}20; color: {{ $cfg['color'] }};">
                <i class="bi {{ $cfg['icon'] }} fs-4"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge fs-6 px-3 py-2" style="background: {{ $cfg['color'] }}; color: #fff;">{{ $cfg['label'] }}</span>
                    <small class="text-muted">#{{ $transaction->id }}</small>
                </div>
                <p class="mb-0 text-muted small">{{ $transaction->created_at->format('l, F d, Y \a\t h:i A') }}</p>
            </div>
            <div class="text-end d-none d-md-block">
                <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.65rem;">{{ __('Total Value') }}</small>
                <span class="fw-bold fs-5" style="color: {{ $cfg['color'] }};">
                    ${{ number_format(($transaction->unit_cost ?? 0) * abs($transaction->quantity), 2) }}
                </span>
            </div>
        </div>

        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 border" style="background: #fafafa;">
                            <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 44px; height: 44px; background: #ecfdf5; color: #059669;">
                                <i class="bi bi-box fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.65rem;">{{ __('Product') }}</small>
                                <span class="fw-semibold">{{ $transaction->product->name ?? __('Deleted') }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 border" style="background: #fafafa;">
                            <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 44px; height: 44px; background: {{ $transaction->quantity > 0 ? '#ecfdf5' : '#fef2f2' }}; color: {{ $transaction->quantity > 0 ? '#059669' : '#dc2626' }};">
                                <i class="bi bi-sort-numeric-up fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.65rem;">{{ __('Quantity') }}</small>
                                <span class="fw-bold fs-5 {{ $transaction->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->quantity > 0 ? '+' . $transaction->quantity : $transaction->quantity }}
                                </span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 border" style="background: #fafafa;">
                            <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 44px; height: 44px; background: #f0f9ff; color: #0284c7;">
                                <i class="bi bi-currency-dollar fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.65rem;">{{ __('Unit Cost') }}</small>
                                <span class="fw-semibold">${{ number_format($transaction->unit_cost ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 border" style="background: #fafafa;">
                            <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 44px; height: 44px; background: #f5f3ff; color: #7c3aed;">
                                <i class="bi bi-clipboard-data fs-5"></i>
                            </div>
                            <div class="d-flex gap-4 flex-wrap">
                                <div>
                                    <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.65rem;">{{ __('Before') }}</small>
                                    <span class="fw-semibold fs-5">{{ $transaction->stock_before ?? '—' }}</span>
                                </div>
                                <div>
                                    <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.65rem;">{{ __('After') }}</small>
                                    <span class="fw-semibold fs-5">{{ $transaction->stock_after ?? '—' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 border" style="background: #fafafa;">
                            <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 44px; height: 44px; background: #fffbeb; color: #d97706;">
                                <i class="bi bi-tag fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.65rem;">{{ __('Reference') }}</small>
                                <span class="fw-semibold">{{ $transaction->reference ?? '—' }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 border" style="background: #fafafa;">
                            <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 44px; height: 44px; background: #f0fdf4; color: #059669;">
                                <i class="bi bi-person fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.65rem;">{{ __('Performed By') }}</small>
                                <span class="fw-semibold">{{ $transaction->user->name ?? __('System') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($transaction->notes)
                <div class="mt-4 p-3 rounded-3 border" style="background: #fafafa;">
                    <div class="d-flex align-items-start gap-3">
                        <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 36px; height: 36px; background: #f8fafc; color: #64748b;">
                            <i class="bi bi-chat-text fs-5"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 0.65rem;">{{ __('Notes') }}</small>
                            <span class="text-muted">{{ $transaction->notes }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
