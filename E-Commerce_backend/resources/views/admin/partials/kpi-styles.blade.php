{{--
    Shared KPI card + feature tile styles for admin pages.
    Usage: @include('admin.partials.kpi-styles') once per page, then
    <div class="app-kpi kpi-green"> … </div>  /  <a class="feature-tile" style="--tile:#10b981"> … </a>
--}}
@once
@push('styles')
<style>
    .app-kpi { --kpi-color:#10b981; --kpi-soft:color-mix(in srgb,var(--kpi-color) 14%,transparent); position:relative; overflow:hidden; display:flex; flex-direction:column; height:100%; min-height:168px; padding:18px 18px 16px; border:1px solid var(--admin-border); border-radius:20px; color:var(--admin-text); background:radial-gradient(120% 90% at 100% 0%,var(--kpi-soft) 0%,transparent 55%),var(--admin-surface); box-shadow:0 8px 24px rgba(15,23,42,.045); transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease; }
    .app-kpi:before { content:""; position:absolute; inset:0 0 auto 0; height:4px; background:linear-gradient(90deg,var(--kpi-color),transparent 85%); }
    .app-kpi:hover { transform:translateY(-3px); border-color:color-mix(in srgb,var(--kpi-color) 40%,var(--admin-border)); box-shadow:0 16px 34px rgba(15,23,42,.1); }
    a.app-kpi, a.app-kpi:hover { color:var(--admin-text); }
    a.app-kpi:focus-visible { outline:none; box-shadow:0 0 0 3px color-mix(in srgb,var(--kpi-color) 40%,transparent); }
    .app-kpi-head { display:flex; align-items:center; gap:10px; margin-bottom:14px; }
    .app-kpi-icon { width:40px; height:40px; flex:none; display:grid; place-items:center; border-radius:12px; color:#fff; font-size:18px; background:linear-gradient(145deg,color-mix(in srgb,var(--kpi-color) 72%,#fff),var(--kpi-color)); box-shadow:0 6px 14px color-mix(in srgb,var(--kpi-color) 35%,transparent); }
    .app-kpi-label { flex:1; min-width:0; color:var(--admin-text-muted); font-size:.8rem; font-weight:650; line-height:1.25; }
    .app-kpi-value { font-size:clamp(1.45rem,2.4vw,2.05rem); font-weight:800; line-height:1.1; letter-spacing:-.04em; font-variant-numeric:tabular-nums; overflow-wrap:anywhere; }
    .app-kpi-foot { display:flex; align-items:flex-end; justify-content:space-between; gap:10px; margin-top:auto; padding-top:12px; }
    .app-kpi-caption { min-width:0; flex:1; color:var(--admin-text-muted); font-size:.74rem; line-height:1.35; }
    .app-kpi-spark { width:96px; height:36px; flex:none; overflow:visible; }
    .app-kpi-chip { display:inline-flex; align-items:center; gap:3px; flex:none; padding:3px 8px; border-radius:99px; font-size:.7rem; font-weight:750; white-space:nowrap; background:var(--badge-gray-bg); color:var(--badge-gray-text); }
    .app-kpi-chip.good { background:var(--badge-green-bg); color:var(--badge-green-text); }
    .app-kpi-chip.bad { background:var(--badge-red-bg); color:var(--badge-red-text); }
    .app-kpi-chip.warn { background:var(--badge-amber-bg); color:var(--badge-amber-text); }
    .app-kpi-meter { display:flex; gap:2px; height:6px; margin-top:6px; border-radius:99px; background:var(--admin-border); overflow:hidden; }
    .app-kpi-meter > span { display:block; height:100%; border-radius:99px; background:var(--kpi-color); transition:width .6s cubic-bezier(.2,.8,.2,1); }
    .app-kpi-legend { display:flex; flex-wrap:wrap; gap:4px 10px; margin-top:6px; font-size:.68rem; color:var(--admin-text-muted); }
    .app-kpi-legend i { display:inline-block; width:7px; height:7px; margin-right:4px; border-radius:2px; }
    .kpi-cyan { --kpi-color:#06b6d4; } .kpi-green { --kpi-color:#10b981; } .kpi-blue { --kpi-color:#3b82f6; } .kpi-amber { --kpi-color:#f59e0b; }
    .kpi-sky { --kpi-color:#0ea5e9; } .kpi-violet { --kpi-color:#8b5cf6; } .kpi-red { --kpi-color:#ef4444; } .kpi-teal { --kpi-color:#14b8a6; }
    .app-kpi .spark-line { stroke-dasharray:400; stroke-dashoffset:400; animation:app-spark-draw 1s ease-out .15s forwards; }
    @keyframes app-spark-draw { to { stroke-dashoffset:0; } }

    .feature-tile { --tile:#10b981; --tile-ink:color-mix(in srgb,var(--tile) 78%,var(--admin-text)); position:relative; overflow:hidden; display:flex; flex-direction:column; align-items:flex-start; height:100%; padding:18px; text-decoration:none; color:var(--admin-text); border:1px solid var(--admin-border); border-radius:18px; background:var(--admin-surface); box-shadow:0 3px 12px rgba(15,23,42,.03); transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease; }
    .feature-tile:before { content:""; position:absolute; width:150px; height:150px; right:-62px; top:-74px; border-radius:50%; background:radial-gradient(circle,color-mix(in srgb,var(--tile) 24%,transparent),transparent 70%); transition:transform .35s ease; pointer-events:none; }
    .feature-tile:hover, .feature-tile:focus-visible { color:var(--admin-text); transform:translateY(-3px); border-color:color-mix(in srgb,var(--tile) 45%,var(--admin-border)); box-shadow:0 14px 28px color-mix(in srgb,var(--tile) 16%,transparent); outline:none; }
    .feature-tile:focus-visible { box-shadow:0 0 0 3px color-mix(in srgb,var(--tile) 40%,transparent); }
    .feature-tile:hover:before { transform:scale(1.4); }
    .feature-tile-top { position:relative; display:flex; justify-content:space-between; align-items:center; width:100%; margin-bottom:14px; }
    .feature-tile-icon { width:46px; height:46px; display:grid; place-items:center; border-radius:14px; color:var(--tile); background:color-mix(in srgb,var(--tile) 14%,transparent); font-size:21px; transition:transform .25s ease,background .25s ease,color .25s ease,box-shadow .25s ease; }
    .feature-tile:hover .feature-tile-icon, .feature-tile:focus-visible .feature-tile-icon { color:#fff; background:linear-gradient(145deg,color-mix(in srgb,var(--tile) 68%,#fff),var(--tile)); box-shadow:0 6px 14px color-mix(in srgb,var(--tile) 38%,transparent); transform:rotate(-7deg) scale(1.06); }
    .feature-tile-arrow { width:28px; height:28px; display:grid; place-items:center; border-radius:50%; color:var(--admin-text-muted); background:var(--card-muted-bg); font-size:13px; transition:transform .2s ease,color .2s ease; }
    .feature-tile:hover .feature-tile-arrow { color:var(--tile-ink); transform:translateX(3px); }
    .feature-tile-title { position:relative; font-size:.95rem; font-weight:750; }
    .feature-tile-sub { position:relative; margin-top:2px; color:var(--admin-text-muted); font-size:.76rem; }
    .feature-tile-badge { position:relative; margin-top:12px; padding:3px 9px; border-radius:99px; font-size:.7rem; font-weight:750; color:var(--tile-ink); background:color-mix(in srgb,var(--tile) 12%,transparent); }

    /* Compact stat card (Reports tabs): same accent strip, gradient icon and hover as .app-kpi */
    .mini-kpi { --kpi-color:#10b981; position:relative; overflow:hidden; height:100%; border:1px solid var(--admin-border) !important; border-radius:18px !important; background:radial-gradient(120% 120% at 100% 0%,color-mix(in srgb,var(--kpi-color) 12%,transparent) 0%,transparent 55%),var(--admin-surface) !important; box-shadow:0 6px 18px rgba(15,23,42,.04) !important; transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease; }
    .mini-kpi:before { content:""; position:absolute; inset:0 0 auto 0; height:3px; background:linear-gradient(90deg,var(--kpi-color),transparent 85%); }
    .mini-kpi:hover { transform:translateY(-2px); border-color:color-mix(in srgb,var(--kpi-color) 40%,var(--admin-border)) !important; box-shadow:0 12px 26px rgba(15,23,42,.09) !important; }
    .mini-kpi .mini-kpi-icon { background:linear-gradient(145deg,color-mix(in srgb,var(--kpi-color) 72%,#fff),var(--kpi-color)) !important; box-shadow:0 6px 14px color-mix(in srgb,var(--kpi-color) 32%,transparent); border-radius:13px !important; }
    .mini-kpi .mini-kpi-icon i { color:#fff !important; }
    .mini-kpi h4, .mini-kpi h5 { font-weight:800 !important; letter-spacing:-.03em; font-variant-numeric:tabular-nums; }
    .mini-kpi.tone-success { --kpi-color:#10b981; } .mini-kpi.tone-primary { --kpi-color:#4f46e5; } .mini-kpi.tone-info { --kpi-color:#06b6d4; }
    .mini-kpi.tone-warning { --kpi-color:#f59e0b; } .mini-kpi.tone-danger { --kpi-color:#ef4444; } .mini-kpi.tone-secondary { --kpi-color:#64748b; }

    @media (max-width:575px) { .app-kpi { min-height:150px; padding:14px 13px 12px; } .app-kpi-icon { width:34px; height:34px; font-size:15px; border-radius:10px; } .app-kpi-spark { width:64px; height:28px; } .feature-tile { padding:14px; } .feature-tile-arrow { display:none; } }
    @media (prefers-reduced-motion:reduce) { .app-kpi, .feature-tile, .feature-tile-icon { transition:none; } .app-kpi .spark-line { animation:none; stroke-dashoffset:0; } }
</style>
@endpush
@push('scripts')
<script>
    // Draws a small trend line into <svg class="app-kpi-spark">, coloured by the card's --kpi-color.
    window.appKpiSpark = function (svg, values) {
        if (!svg || !values || !values.length) return;
        values = values.map(Number);
        var w = 96, h = 36, pad = 4, max = Math.max.apply(null, values.concat(0)), min = Math.min.apply(null, values.concat(0)), range = (max - min) || 1;
        var pts = values.map(function (v, i) { return [pad + (w - pad * 2) * i / Math.max(1, values.length - 1), h - pad - (v - min) / range * (h - pad * 2)]; });
        var line = pts.map(function (p, i) { return (i ? 'L' : 'M') + p[0].toFixed(1) + ',' + p[1].toFixed(1); }).join('');
        var end = pts[pts.length - 1], gid = (svg.id || 'spark') + 'Fill';
        svg.setAttribute('viewBox', '0 0 ' + w + ' ' + h);
        svg.innerHTML = '<defs><linearGradient id="' + gid + '" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="var(--kpi-color)" stop-opacity=".3"/><stop offset="100%" stop-color="var(--kpi-color)" stop-opacity="0"/></linearGradient></defs>'
            + '<path d="' + line + 'L' + end[0] + ',' + h + 'L' + pts[0][0] + ',' + h + 'Z" fill="url(#' + gid + ')"/>'
            + '<path class="spark-line" d="' + line + '" fill="none" stroke="var(--kpi-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'
            + '<circle cx="' + end[0] + '" cy="' + end[1] + '" r="3.2" fill="var(--admin-surface)" stroke="var(--kpi-color)" stroke-width="2"/>';
    };
    // Auto-draw every svg[data-spark] sparkline (used by the admin KPI card component).
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('svg[data-spark]').forEach(function (svg, i) {
            if (!svg.id) svg.id = 'kpiSpark' + i; // unique gradient id per card
            try { window.appKpiSpark(svg, JSON.parse(svg.dataset.spark)); } catch (e) { /* bad data: leave empty */ }
        });
    });
    // Sets a trend chip: ▲/▼ percent, green when the change is good, red when bad.
    window.appKpiTrend = function (el, percent, higherIsGood, title) {
        if (!el) return;
        var v = Number(percent) || 0;
        el.className = 'app-kpi-chip' + (Math.round(v * 10) === 0 ? '' : ((v > 0) === higherIsGood ? ' good' : ' bad'));
        el.innerHTML = '<i class="bi bi-arrow-' + (v >= 0 ? 'up' : 'down') + '-right"></i>' + Math.abs(v).toFixed(1) + '%';
        if (title) el.title = title;
    };
</script>
@endpush
@endonce
