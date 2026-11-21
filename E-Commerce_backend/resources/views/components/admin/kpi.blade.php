{{--
    Reusable KPI card (styles live in admin/partials/kpi-styles).

    <x-admin.kpi color="green" icon="bi-cash-stack" :label="__('Total Income')" :value="'$'.number_format($x, 2)"
        :trend="12.5" :spark="$monthlyTotals" :meter="40" :caption="__('Sales in period')" href="…" />

    color    green|blue|cyan|sky|violet|amber|red|teal
    trend    % change → ▲/▼ chip, green when good (see higherIsGood); null hides it
    chip     custom chip text instead of a trend (chipTone: good|bad|warn, chipIcon: bi-…)
    meter    0–100 progress bar under the caption
    spark    array/collection of numbers → small trend line
    slot     extra caption content (legends, segmented meters, …)
--}}
@props([
    'color' => 'green', 'icon' => 'bi-bar-chart', 'label' => '', 'value' => '—', 'valueClass' => '', 'valueId' => null,
    'href' => null, 'caption' => null,
    'trend' => null, 'higherIsGood' => true, 'trendTitle' => null,
    'chip' => null, 'chipTone' => '', 'chipIcon' => null, 'chipTitle' => null,
    'meter' => null, 'spark' => null,
])
@include('admin.partials.kpi-styles')
@php
    $tag = $href ? 'a' : 'div';
    if (! is_null($trend)) {
        $isFlat = round($trend, 1) == 0;
        $chipTone = $isFlat ? '' : ((($trend > 0) === (bool) $higherIsGood) ? 'good' : 'bad');
        $chipIcon = $trend >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right';
        $chip = (abs($trend) >= 999 ? '999+' : number_format(abs($trend), 1)) . '%';
        $chipTitle = $trendTitle ?? __('Latest month vs the month before');
    }
    $sparkValues = $spark ? collect($spark)->map(fn ($v) => (float) $v)->values()->all() : null;
@endphp
<{{ $tag }} @if ($href) href="{{ $href }}" @endif {{ $attributes->class(['app-kpi', 'kpi-' . $color, 'text-decoration-none' => (bool) $href]) }}>
    <div class="app-kpi-head">
        <span class="app-kpi-icon"><i class="bi {{ $icon }}"></i></span>
        <span class="app-kpi-label">{{ $label }}</span>
        @if (! is_null($chip))
            <span class="app-kpi-chip {{ $chipTone }}" @if ($chipTitle) title="{{ $chipTitle }}" @endif>@if ($chipIcon)<i class="bi {{ $chipIcon }}"></i>@endif{{ $chip }}</span>
        @endif
    </div>
    <div class="app-kpi-value {{ $valueClass }}" @if ($valueId) id="{{ $valueId }}" @endif>{{ $value }}</div>
    <div class="app-kpi-foot">
        <div class="app-kpi-caption">
            {{ $caption }}{{ $slot }}
            @if (! is_null($meter))
                <div class="app-kpi-meter"><span style="width:{{ max(0, min(100, (float) $meter)) }}%"></span></div>
            @endif
        </div>
        @if ($sparkValues && count($sparkValues) > 1)
            <svg class="app-kpi-spark" data-spark="{{ json_encode($sparkValues) }}" aria-hidden="true"></svg>
        @endif
        @if ($href)
            <i class="bi bi-arrow-right text-muted" aria-hidden="true"></i>
        @endif
    </div>
</{{ $tag }}>
