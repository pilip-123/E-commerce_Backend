@extends('layouts.admin')

@section('title', 'Saving tracker')

@push('styles')
<style>
    .saving-app { --save-blue:#3978f6; --save-orange:#f59e0b; color:var(--admin-text); }
    .saving-app .saving-hero { position:relative; overflow:hidden; border:0; border-radius:24px; padding:clamp(22px,4vw,38px); color:#fff; background:linear-gradient(120deg,#064e3b 0%,#047857 48%,#10b981 100%); box-shadow:0 18px 42px rgba(4,120,87,.18); }
    .saving-app .saving-hero:after { content:""; position:absolute; width:280px; height:280px; right:-65px; top:-120px; border:1px solid #ffffff2b; border-radius:50%; box-shadow:0 0 0 32px #ffffff0b,0 0 0 64px #ffffff08; }
    .saving-app .saving-hero > * { position:relative; z-index:1; }
    .saving-app .hero-action { border:1px solid #ffffff73; background:#ffffff16; color:#fff; border-radius:12px; padding:10px 15px; font-weight:650; }
    .saving-app .hero-action:hover { color:#064e3b; background:#fff; }
    .saving-app .currency-select { width:auto; min-width:145px; border:1px solid #ffffff70; border-radius:10px; background-color:#ffffffed; color:#064e3b; box-shadow:0 4px 14px rgba(0,0,0,.08); }
    .saving-app .currency-select:focus { border-color:#a7f3d0; box-shadow:0 0 0 .2rem rgba(167,243,208,.3); }
    .saving-app .save-card, .saving-app .save-tile { background:var(--admin-surface); border:1px solid var(--admin-border); border-radius:18px; color:var(--admin-text); }
    .saving-app .kpi-card { position:relative; overflow:hidden; min-height:172px; display:flex; flex-direction:column; padding:18px 18px 16px; border:1px solid var(--admin-border); border-radius:20px; background:radial-gradient(120% 90% at 100% 0%,var(--kpi-soft) 0%,transparent 55%),var(--admin-surface); box-shadow:0 8px 24px rgba(15,23,42,.045); transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease; }
    .saving-app .kpi-card:before { content:""; position:absolute; inset:0 0 auto 0; height:4px; background:linear-gradient(90deg,var(--kpi-color),transparent 85%); }
    .saving-app .kpi-card:hover { transform:translateY(-3px); border-color:color-mix(in srgb,var(--kpi-color) 40%,var(--admin-border)); box-shadow:0 16px 34px rgba(15,23,42,.1); }
    .saving-app .kpi-head { display:flex; align-items:center; gap:10px; margin-bottom:14px; }
    .saving-app .kpi-label { flex:1; min-width:0; color:var(--admin-text-muted); font-size:.8rem; font-weight:650; line-height:1.2; }
    .saving-app .kpi-icon { width:38px; height:38px; display:grid; place-items:center; flex:none; border-radius:12px; color:#fff; background:linear-gradient(145deg,color-mix(in srgb,var(--kpi-color) 75%,#fff),var(--kpi-color)); box-shadow:0 6px 14px color-mix(in srgb,var(--kpi-color) 35%,transparent); font-size:17px; }
    .saving-app .kpi-value { overflow-wrap:anywhere; font-size:clamp(1.3rem,2.3vw,1.95rem); font-weight:800; line-height:1.1; letter-spacing:-.045em; font-variant-numeric:tabular-nums; }
    .saving-app .kpi-foot { display:flex; align-items:flex-end; justify-content:space-between; gap:10px; margin-top:auto; padding-top:12px; }
    .saving-app .kpi-caption { min-width:0; color:var(--admin-text-muted); font-size:.74rem; line-height:1.35; }
    .saving-app .kpi-spark { width:96px; height:36px; flex:none; overflow:visible; }
    .saving-app .kpi-delta { display:inline-flex; align-items:center; gap:3px; flex:none; padding:3px 8px; border-radius:99px; font-size:.7rem; font-weight:750; background:var(--badge-gray-bg); color:var(--badge-gray-text); white-space:nowrap; }
    .saving-app .kpi-delta.good { background:var(--badge-green-bg); color:var(--badge-green-text); }
    .saving-app .kpi-delta.bad { background:var(--badge-red-bg); color:var(--badge-red-text); }
    .saving-app .kpi-meter { height:6px; margin-top:6px; border-radius:99px; background:var(--admin-border); overflow:hidden; display:flex; }
    .saving-app .kpi-meter > span { display:block; height:100%; border-radius:99px; transition:width .6s cubic-bezier(.2,.8,.2,1); }
    @media(max-width:575px) { .saving-app .kpi-card { min-height:150px; padding:14px 13px 12px; } .saving-app .kpi-icon { width:32px; height:32px; font-size:15px; border-radius:10px; } .saving-app .kpi-spark { width:64px; height:28px; } .saving-app .kpi-foot { flex-direction:column; align-items:stretch; } .saving-app .kpi-spark { align-self:flex-end; } }
    .saving-app .kpi-saved { --kpi-color:#167d55; --kpi-soft:#d9f5e8; }
    .saving-app .kpi-spent { --kpi-color:#d97706; --kpi-soft:#fff0d2; }
    .saving-app .kpi-year { --kpi-color:#3978f6; --kpi-soft:#e1ecff; }
    .saving-app .kpi-net { --kpi-color:#7c3aed; --kpi-soft:#eee5ff; }
    [data-theme="dark"] .saving-app .kpi-saved { --kpi-color:#6ee7b7; --kpi-soft:#064e3b; }
    [data-theme="dark"] .saving-app .kpi-spent { --kpi-color:#fbbf24; --kpi-soft:#713f12; }
    [data-theme="dark"] .saving-app .kpi-year { --kpi-color:#93c5fd; --kpi-soft:#1e3a8a; }
    [data-theme="dark"] .saving-app .kpi-net { --kpi-color:#c4b5fd; --kpi-soft:#4c1d95; }
    .saving-app .tile-icon { display:grid; place-items:center; width:38px; height:38px; border-radius:12px; color:var(--admin-primary); background:var(--admin-primary-light); font-size:18px; margin-bottom:12px; }
    .saving-app .save-tile { --tile:#10b981; --tile-ink:color-mix(in srgb,var(--tile) 78%,var(--admin-text)); position:relative; overflow:hidden; display:flex; flex-direction:column; align-items:flex-start; gap:2px; height:100%; padding:16px; text-align:left; border:1px solid var(--admin-border); border-radius:18px; background:var(--admin-surface); box-shadow:0 3px 12px rgba(15,23,42,.03); transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease,background .2s ease; }
    .saving-app .save-tile:before { content:""; position:absolute; width:150px; height:150px; right:-62px; top:-74px; border-radius:50%; background:radial-gradient(circle,color-mix(in srgb,var(--tile) 24%,transparent),transparent 70%); transition:transform .35s ease; pointer-events:none; }
    .saving-app .save-tile:hover { transform:translateY(-3px); border-color:color-mix(in srgb,var(--tile) 45%,var(--admin-border)); box-shadow:0 14px 28px color-mix(in srgb,var(--tile) 16%,transparent); }
    .saving-app .save-tile:hover:before { transform:scale(1.4); }
    .saving-app .save-tile.active { border-color:var(--tile); background:linear-gradient(150deg,color-mix(in srgb,var(--tile) 12%,var(--admin-surface)),var(--admin-surface) 72%); box-shadow:0 0 0 3px color-mix(in srgb,var(--tile) 16%,transparent),0 12px 26px color-mix(in srgb,var(--tile) 14%,transparent); }
    .saving-app .save-tile:focus-visible { outline:none; box-shadow:0 0 0 3px color-mix(in srgb,var(--tile) 45%,transparent); }
    .saving-app .tile-top { position:relative; display:flex; justify-content:space-between; align-items:center; width:100%; margin-bottom:10px; }
    .saving-app .tile-ico { width:42px; height:42px; display:grid; place-items:center; border-radius:13px; color:var(--tile); background:color-mix(in srgb,var(--tile) 14%,transparent); font-size:19px; transition:transform .25s ease,background .25s ease,color .25s ease,box-shadow .25s ease; }
    .saving-app .save-tile:hover .tile-ico, .saving-app .save-tile.active .tile-ico { color:#fff; background:linear-gradient(145deg,color-mix(in srgb,var(--tile) 68%,#fff),var(--tile)); box-shadow:0 6px 14px color-mix(in srgb,var(--tile) 38%,transparent); transform:rotate(-7deg) scale(1.06); }
    .saving-app .tile-arrow { width:28px; height:28px; display:grid; place-items:center; border-radius:50%; color:var(--admin-text-muted); background:var(--card-muted-bg); font-size:13px; transition:transform .2s ease,background .2s ease,color .2s ease; }
    .saving-app .save-tile:hover .tile-arrow { color:var(--tile-ink); transform:translateX(3px); }
    .saving-app .save-tile.active .tile-arrow { color:#fff; background:var(--tile); }
    .saving-app .tile-title { position:relative; font-size:.95rem; font-weight:750; }
    .saving-app .tile-sub { position:relative; color:var(--admin-text-muted); font-size:.75rem; }
    .saving-app .tile-stat { position:relative; display:flex; align-items:center; gap:8px; width:100%; margin-top:auto; padding-top:10px; border-top:1px dashed var(--admin-border); color:var(--tile-ink); font-size:.76rem; font-weight:750; font-variant-numeric:tabular-nums; }
    .saving-app .tile-sub + .tile-stat { margin-top:10px; }
    .saving-app .tile-stat-text { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .saving-app .tile-progress { flex:1; min-width:30px; height:5px; border-radius:99px; background:var(--admin-border); overflow:hidden; }
    .saving-app .tile-progress > span { display:block; height:100%; border-radius:99px; background:linear-gradient(90deg,color-mix(in srgb,var(--tile) 60%,#fff),var(--tile)); transition:width .6s cubic-bezier(.2,.8,.2,1); }
    .saving-app .kpi-mini-bars { width:96px; height:36px; flex:none; overflow:visible; }
    @media(max-width:575px) { .saving-app .save-tile { padding:12px; } .saving-app .tile-ico { width:36px; height:36px; font-size:16px; border-radius:11px; } .saving-app .tile-arrow { display:none; } .saving-app .tile-sub { font-size:.68rem; } .saving-app .kpi-mini-bars { width:64px; height:28px; align-self:flex-end; } }
    .saving-app .metric { font-size:clamp(1.35rem,3vw,2rem); font-weight:750; letter-spacing:-.04em; }
    .saving-app .muted { color:var(--admin-text-muted); }
    .saving-app .section-panel { display:none; }
    .saving-app .section-panel.active { display:block; }
    .saving-app .form-control, .saving-app .form-select { background-color:var(--admin-surface); color:var(--admin-text); border-color:var(--admin-border); }
    .saving-app .form-control::placeholder { color:var(--admin-text-muted); }
    .saving-app .table { --bs-table-bg:transparent; --bs-table-color:var(--admin-text); --bs-table-border-color:var(--admin-border); }
    .saving-app .table thead th { color:var(--admin-text-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.06em; }
    .saving-app .entry-table { border-collapse:separate; border-spacing:0 6px; margin-top:-6px; }
    .saving-app .entry-table thead th { padding:8px 14px; background:transparent; border:0; white-space:nowrap; font-size:.68rem; }
    .saving-app .entry-table tbody tr { transition:transform .15s ease,box-shadow .15s ease; }
    .saving-app .entry-table tbody td { padding:11px 14px; border:1px solid var(--admin-border); border-left:0; border-right:0; background:var(--admin-surface); transition:background .15s ease; }
    .saving-app .entry-table tbody td:first-child { border-left:1px solid var(--admin-border); border-radius:14px 0 0 14px; }
    .saving-app .entry-table tbody td:last-child { border-right:1px solid var(--admin-border); border-radius:0 14px 14px 0; }
    .saving-app .entry-table tbody tr:hover td { background:var(--card-muted-bg); }
    .saving-app .entry-table tbody tr:hover { box-shadow:0 6px 16px rgba(15,23,42,.06); }
    .saving-app .entry-table tbody tr:hover td:first-child { box-shadow:inset 3px 0 0 var(--row-accent,var(--admin-primary)); }
    .saving-app .entry-table tbody tr.row-saved { --row-accent:#10b981; }
    .saving-app .entry-table tbody tr.row-spent { --row-accent:#f59e0b; }
    .saving-app .entry-table tbody tr td.empty { border-radius:14px; border:1px dashed var(--admin-border); }
    .saving-app .entry-main { display:flex; align-items:center; gap:11px; min-width:0; }
    .saving-app .entry-avatar { width:38px; height:38px; flex:none; display:grid; place-items:center; border-radius:12px; font-size:16px; color:var(--cat-color); background:color-mix(in srgb,var(--cat-color) 14%,transparent); }
    .saving-app .entry-date { display:inline-flex; flex-direction:column; align-items:center; justify-content:center; width:46px; padding:5px 0; border-radius:11px; background:var(--card-muted-bg); border:1px solid var(--admin-border); line-height:1; white-space:nowrap; }
    .saving-app .entry-date b { font-size:1rem; font-weight:800; color:var(--admin-text); }
    .saving-app .entry-date small { margin-top:3px; color:var(--admin-text-muted); font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; }
    .saving-app .entry-category { display:block; font-size:.86rem; font-weight:700; color:var(--admin-text); }
    .saving-app .entry-note { display:block; max-width:260px; margin-top:2px; color:var(--admin-text-muted); font-size:.74rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .saving-app .entry-actions .btn { width:30px; height:30px; padding:0; display:inline-grid; place-items:center; border-radius:9px; opacity:.55; transition:opacity .15s ease; }
    .saving-app .entry-table tbody tr:hover .entry-actions .btn, .saving-app .entry-actions .btn:focus-visible { opacity:1; }
    .saving-app .entry-amount { display:inline-block; padding:6px 10px; border-radius:10px; background:var(--badge-green-bg); font-size:.86rem; font-weight:800; font-variant-numeric:tabular-nums; }
    .saving-app .entry-amount.spent { background:var(--badge-amber-bg); }
    .saving-app .entry-group td { padding:10px 4px 2px !important; background:transparent !important; border:0 !important; color:var(--admin-text-muted); font-size:.7rem; font-weight:750; text-transform:uppercase; letter-spacing:.07em; }
    .saving-app .entry-group span { float:right; text-transform:none; letter-spacing:0; font-weight:700; }
    .saving-app .rel-bar { position:relative; height:6px; min-width:60px; border-radius:99px; background:var(--admin-border); overflow:hidden; }
    .saving-app .rel-bar > span { position:absolute; inset:0 auto 0 0; border-radius:99px; background:linear-gradient(90deg,var(--bar-a,#34d399),var(--bar-b,#059669)); }
    .saving-app .rel-bar.blue { --bar-a:#60a5fa; --bar-b:#2563eb; }
    .saving-app .split-bar { display:flex; height:7px; min-width:90px; border-radius:99px; overflow:hidden; background:var(--admin-border); gap:2px; }
    .saving-app .split-bar > span:first-child { background:#3b82f6; }
    .saving-app .split-bar > span:last-child { background:#f59e0b; }
    .saving-app .summary-table { border-collapse:separate; border-spacing:0; }
    .saving-app .summary-table thead th { border:0; padding-top:.5rem; padding-bottom:.5rem; font-size:.68rem; }
    .saving-app .summary-table tbody td { border-color:var(--admin-border); font-variant-numeric:tabular-nums; }
    .saving-app .summary-table tbody tr:last-child td { border-bottom:0; }
    .saving-app .summary-table tbody tr { transition:background .15s ease; }
    .saving-app .summary-table tbody tr:hover { background:color-mix(in srgb,var(--admin-primary) 6%,transparent); }
    .saving-app .net-pill { display:inline-block; padding:3px 8px; border-radius:8px; font-size:.78rem; font-weight:750; }
    .saving-app .net-pill.pos { background:var(--badge-green-bg); color:var(--badge-green-text); }
    .saving-app .net-pill.neg { background:var(--badge-red-bg); color:var(--badge-red-text); }
    .saving-app .net-pill.zero { background:var(--badge-gray-bg); color:var(--badge-gray-text); }
    .saving-app .now-chip { display:inline-block; margin-left:4px; padding:1px 6px; border-radius:99px; font-size:.62rem; font-weight:750; color:#fff; background:#0ea5e9; vertical-align:middle; }
    .saving-app .chart-wrap { position:relative; min-height:250px; }
    .saving-app .chart-wrap svg { width:100%; height:260px; overflow:visible; }
    .saving-app .monthly-chart-panel { height:100%; border:1px solid var(--admin-border); border-radius:16px; background:var(--card-muted-bg); }
    .saving-app .monthly-chart-panel .chart-wrap { min-height:225px; }
    .saving-app .monthly-chart-panel .chart-wrap svg { height:225px; }
    .saving-app .monthly-goal { padding:clamp(16px,2.5vw,28px); border:1px solid var(--admin-border); border-radius:22px; background:var(--admin-surface); box-shadow:0 10px 30px rgba(15,23,42,.045); }
    .saving-app .monthly-heading { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:18px; margin-bottom:22px; }
    .saving-app .monthly-title { display:flex; align-items:center; gap:13px; }
    .saving-app .monthly-title-icon { width:52px; height:52px; display:grid; place-items:center; border-radius:16px; color:#07845a; background:linear-gradient(145deg,#d1fae5,#a7f3d0); font-size:24px; }
    .saving-app .month-controls { display:flex; flex-wrap:wrap; align-items:center; gap:9px; }
    .saving-app .month-control { min-height:42px; border-radius:11px; }
    .saving-app .month-kpi { position:relative; display:flex; align-items:center; gap:13px; min-height:105px; padding:16px; overflow:hidden; border:1px solid var(--admin-border); border-radius:16px; background:linear-gradient(145deg,var(--admin-surface),var(--kpi-soft)); }
    .saving-app .month-kpi-icon { width:48px; height:48px; flex:none; display:grid; place-items:center; border-radius:50%; color:var(--kpi-color); background:var(--kpi-soft); font-size:21px; }
    .saving-app .month-kpi-label { color:var(--admin-text-muted); font-size:.76rem; font-weight:650; }
    .saving-app .month-kpi-value { margin-top:4px; overflow-wrap:anywhere; font-size:clamp(1.05rem,1.7vw,1.48rem); font-weight:780; line-height:1.1; letter-spacing:-.035em; }
    .saving-app .month-kpi-target { --kpi-color:#3b82f6; --kpi-soft:#e8f0ff; }
    .saving-app .month-kpi-saved { --kpi-color:#16a36a; --kpi-soft:#dcfce9; }
    .saving-app .month-kpi-left { --kpi-color:#8b5cf6; --kpi-soft:#f0eaff; }
    .saving-app .month-kpi-progress { --kpi-color:#0d9488; --kpi-soft:#d8f7f0; }
    [data-theme="dark"] .saving-app .month-kpi-target { --kpi-color:#93c5fd; --kpi-soft:#1e3a8a; }
    [data-theme="dark"] .saving-app .month-kpi-saved { --kpi-color:#6ee7b7; --kpi-soft:#064e3b; }
    [data-theme="dark"] .saving-app .month-kpi-left { --kpi-color:#c4b5fd; --kpi-soft:#4c1d95; }
    [data-theme="dark"] .saving-app .month-kpi-progress { --kpi-color:#5eead4; --kpi-soft:#134e4a; }
    .saving-app .monthly-chart-panel { padding:18px; }
    .saving-app .weekly-table tbody tr.is-best-week { background:var(--admin-primary-light); }
    .saving-app .weekly-table tbody tr.is-best-week td:first-child { border-left:3px solid var(--admin-primary); }
    .saving-app .yearly-table tbody tr.is-best-week { background:var(--admin-primary-light); }
    .saving-app .yearly-table tbody tr.is-best-week td:first-child { border-left:3px solid var(--admin-primary); }
    .saving-app .year-table-wrap { max-height:350px; overflow:auto; }
    .saving-app .yearly-chart-panel .chart-wrap { min-height:250px; }
    .saving-app .yearly-chart-panel .chart-wrap svg { height:250px; }
    .saving-app .week-label { display:inline-flex; align-items:center; gap:6px; font-weight:650; white-space:nowrap; }
    .saving-app .week-best-icon { color:#e8a317; }
    .saving-app .month-bottom-card { height:100%; padding:18px; border:1px solid var(--admin-border); border-radius:16px; background:var(--card-muted-bg); }
    .saving-app .plans-heading { display:flex; align-items:center; justify-content:space-between; gap:14px; padding:20px 22px; border:1px solid var(--admin-border); border-radius:20px; background:linear-gradient(120deg,var(--admin-surface),var(--card-muted-bg)); }
    .saving-app .plans-heading-icon { display:grid; place-items:center; width:48px; height:48px; flex:none; border-radius:15px; color:#087f5b; background:#d1fae5; font-size:22px; }
    .saving-app .plan-form-card { position:sticky; top:18px; padding:20px; }
    .saving-app .plan-form-card .form-control, .saving-app .plan-form-card .form-select { min-height:44px; border-radius:11px; }
    .saving-app .plan-card { position:relative; overflow:hidden; padding:18px; border:1px solid var(--admin-border); border-radius:19px; background:linear-gradient(145deg,var(--admin-surface),var(--card-muted-bg)); box-shadow:0 7px 22px rgba(15,23,42,.045); transition:transform .18s ease,box-shadow .18s ease; }
    .saving-app .plan-card:hover { transform:translateY(-2px); box-shadow:0 13px 28px rgba(15,23,42,.09); }
    .saving-app .plan-card-icon { display:grid; place-items:center; width:43px; height:43px; flex:none; border-radius:14px; color:#087f5b; background:#d1fae5; font-size:19px; }
    .saving-app .plan-card .progress { height:11px; overflow:hidden; border-radius:99px; }
    .saving-app .plan-card .progress-bar { border-radius:99px; background:linear-gradient(90deg,#10b981,#34d399); }
    .saving-app .plan-detail { padding:11px 12px; border:1px solid var(--admin-border); border-radius:12px; background:var(--admin-surface); }
    .saving-app .plan-detail-label { color:var(--admin-text-muted); font-size:.68rem; }
    .saving-app .plan-detail-value { margin-top:3px; font-size:.9rem; font-weight:750; }
    .saving-app .coach-card { height:100%; padding:19px; border:1px solid var(--admin-border); border-radius:17px; background:linear-gradient(135deg,var(--admin-surface),var(--card-muted-bg)); }
    .saving-app .coach-icon { display:grid; place-items:center; width:39px; height:39px; flex:none; border-radius:12px; color:#087f5b; background:#d1fae5; }
    .saving-app .coach-stat { height:100%; padding:12px; border:1px solid var(--admin-border); border-radius:12px; background:var(--admin-surface); }
    .saving-app .coach-stat-label { color:var(--admin-text-muted); font-size:.7rem; }
    .saving-app .coach-stat-value { margin-top:4px; font-size:clamp(.95rem,1.7vw,1.2rem); font-weight:780; overflow-wrap:anywhere; }
    [data-theme="dark"] .saving-app .coach-icon { color:#6ee7b7; background:#064e3b; }
    [data-theme="dark"] .saving-app .plans-heading-icon, [data-theme="dark"] .saving-app .plan-card-icon { color:#6ee7b7; background:#064e3b; }
    @media(max-width:575px) { .saving-app .monthly-heading { align-items:flex-start; } .saving-app .month-controls { display:grid; grid-template-columns:1fr 1fr; width:100%; } .saving-app .month-controls .btn { grid-column:span 2; } .saving-app .month-kpi { padding:12px 10px; gap:9px; min-height:92px; } .saving-app .month-kpi-icon { width:38px; height:38px; font-size:17px; } .saving-app .month-kpi-label { font-size:.68rem; } .saving-app .monthly-chart-panel { padding:12px; } }
    .saving-app .chart-tooltip { position:absolute; display:none; z-index:3; pointer-events:none; min-width:150px; padding:10px 12px; border-radius:12px; background:rgba(17,24,39,.94); color:#fff; font-size:12px; box-shadow:0 12px 28px #0004; backdrop-filter:blur(6px); }
    .saving-app .chart-tooltip .tt-title { margin-bottom:6px; font-weight:750; font-size:12.5px; }
    .saving-app .chart-tooltip .tt-row { display:flex; align-items:center; justify-content:space-between; gap:14px; line-height:1.7; }
    .saving-app .chart-tooltip .tt-row i { display:inline-block; width:8px; height:8px; margin-right:6px; border-radius:3px; }
    .saving-app .chart-tooltip .tt-row b { font-variant-numeric:tabular-nums; }
    .saving-app .chart-tooltip .tt-foot { margin-top:6px; padding-top:6px; border-top:1px solid #ffffff26; color:#cbd5e1; }
    .saving-app .chart-hover-band { fill:var(--admin-text-muted); opacity:0; transition:opacity .12s ease; }
    .saving-app .chart-hover-band.on { opacity:.09; }
    .saving-app .bar-anim { transform-box:fill-box; transform-origin:50% 100%; animation:bar-grow .7s cubic-bezier(.2,.8,.2,1) both; animation-delay:calc(var(--i,0) * 22ms); }
    .saving-app .spark-anim { stroke-dasharray:400; stroke-dashoffset:400; animation:spark-draw 1s ease-out .15s forwards; }
    @keyframes bar-grow { from { transform:scaleY(0); } to { transform:scaleY(1); } }
    @keyframes spark-draw { to { stroke-dashoffset:0; } }
    @media (prefers-reduced-motion:reduce) { .saving-app .bar-anim, .saving-app .spark-anim { animation:none; stroke-dashoffset:0; } }
    .saving-app .progress { height:10px; background:var(--admin-border); }
    .saving-app .legend-dot { display:inline-block; width:9px; height:9px; border-radius:50%; margin-right:6px; }
    .saving-app .entry-amount.saved { color:#167d55; }
    .saving-app .entry-amount.spent { color:#d97706; }
    .saving-app .empty { color:var(--admin-text-muted); text-align:center; padding:28px 12px; }
    .saving-app .save-card { box-shadow:0 5px 20px rgba(15,23,42,.035); }
    .saving-app .entry-form .form-control, .saving-app .entry-form .form-select { min-height:44px; border-radius:11px; }
    .saving-app .entry-form textarea.form-control { min-height:84px; }
    .saving-app .entry-form__head { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:18px; }
    .saving-app .entry-form__head h2 { min-width:0; font-size:clamp(1rem,1.4vw,1.15rem); line-height:1.25; }
    /* Sliding $ / ៛ switch */
    .saving-app .cur-switch { flex:none; position:relative; display:inline-grid; grid-template-columns:1fr 1fr; padding:3px; border-radius:999px; background:var(--card-muted-bg); border:1px solid var(--admin-border); isolation:isolate; }
    .saving-app .cur-switch input { position:absolute; opacity:0; pointer-events:none; }
    .saving-app .cur-switch label { position:relative; z-index:1; display:inline-flex; align-items:center; justify-content:center; gap:4px; min-width:50px; padding:4px 8px; border-radius:999px; font-size:.68rem; font-weight:700; color:var(--admin-text-muted); cursor:pointer; user-select:none; transition:color .2s ease; }
    .saving-app .cur-switch label b { font-size:1rem; line-height:1; font-family:'Kantumruy Pro',system-ui,sans-serif; }
    .saving-app .cur-switch__thumb { position:absolute; z-index:0; top:3px; bottom:3px; left:3px; width:calc(50% - 3px); border-radius:999px; background:linear-gradient(135deg,#10b981,#047857); box-shadow:0 4px 10px -4px rgba(4,120,87,.6); transition:transform .25s cubic-bezier(.2,.8,.2,1); }
    .saving-app .cur-switch input:nth-of-type(2):checked ~ .cur-switch__thumb { transform:translateX(100%); }
    .saving-app .cur-switch input:checked + label { color:#fff; }
    .saving-app .cur-switch input:focus-visible + label { outline:2px solid #10b981; outline-offset:2px; }
    .saving-app #planForm .entry-form__head h3 { font-size:clamp(1rem,1.4vw,1.15rem); }
    .saving-app #planForm .cur-switch__thumb { background:linear-gradient(135deg,#f472b6,#db2777); box-shadow:0 4px 10px -4px rgba(219,39,119,.6); }
    .saving-app .entry-form[data-kind="spent"] .cur-switch__thumb { background:linear-gradient(135deg,#fbbf24,#d97706); box-shadow:0 4px 10px -4px rgba(217,119,6,.6); }
    .saving-app .amount-field { position:relative; }
    .saving-app .amount-field__sym { position:absolute; left:14px; top:50%; transform:translateY(-50%); font-size:1.05rem; font-weight:800; font-family:'Kantumruy Pro',system-ui,sans-serif; color:var(--admin-text-muted); pointer-events:none; transition:transform .2s ease; }
    .saving-app .amount-field .form-control { padding-left:34px; font-weight:700; }
    .saving-app .amount-field.is-bump .amount-field__sym { transform:translateY(-50%) scale(1.3); color:#10b981; }
    .saving-app .note-preview { display:block; max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .saving-app .note-icon { color:var(--admin-primary); }
    .saving-app #journalEditor:empty:before { content:attr(data-placeholder); color:var(--admin-text-muted); }
    .saving-app .journal-item { border:1px solid var(--admin-border); background:var(--card-muted-bg); border-radius:13px; padding:12px; color:var(--admin-text); }
    .saving-app .journal-item:hover { border-color:var(--admin-primary); }
    .saving-app .journal-preview { max-height:2.8em; overflow:hidden; overflow-wrap:anywhere; line-height:1.4; }
    .saving-app .journal-preview p, .saving-app .journal-preview div, .saving-app .journal-preview blockquote { display:inline; margin:0; }
    .saving-app .note-delete-overlay { position:fixed; inset:0; z-index:2000; display:grid; place-items:center; padding:18px; background:rgba(15,23,42,.58); backdrop-filter:blur(5px); }
    .saving-app .note-delete-overlay.d-none { display:none; }
    .saving-app .note-delete-card { width:min(100%,340px); padding:22px 24px 20px; border:1px solid var(--admin-border); border-radius:18px; background:var(--admin-surface); color:var(--admin-text); text-align:center; box-shadow:0 20px 60px rgba(0,0,0,.24); animation:note-dialog-in .18s ease-out; }
    @keyframes note-dialog-in { from { opacity:0; transform:translateY(8px) scale(.98); } to { opacity:1; transform:translateY(0) scale(1); } }
    .saving-app .table > :not(caption) > * > * { padding-top:.85rem; padding-bottom:.85rem; }
    .saving-app .table-toggle[aria-pressed="true"] { background:var(--admin-primary); color:#fff; }
    @media(max-width:575px) { .saving-app .chart-wrap svg { height:220px; } }
</style>
@endpush

@section('content')
<div class="container-fluid p-0 saving-app" id="savingApp">
    <div class="saving-hero mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-4">
            <div><div class="text-uppercase small fw-bold mb-2" style="opacity:.78;letter-spacing:.13em">PERSONAL FINANCE</div><h1 class="display-6 fw-bold mb-2">Make every dollar count.</h1><div style="opacity:.82">Track what you save, understand what you spend, and make your next goal feel closer.</div>
                <div class="d-flex flex-wrap gap-2 mt-4"><button type="button" class="hero-action" data-open-section="daily-saving"><i class="bi bi-plus-circle me-1"></i>Add saving</button><button type="button" class="hero-action" data-open-section="daily-spending"><i class="bi bi-receipt me-1"></i>Add spending</button><button type="button" class="hero-action" data-bs-toggle="modal" data-bs-target="#notesModal"><i class="bi bi-journal-text me-1"></i>Notes</button></div>
            </div>
            <div class="d-flex flex-column align-items-end gap-2"><div class="d-flex align-items-center gap-2"><select id="currencySelect" class="form-select form-select-sm currency-select" aria-label="Choose currency">
                @foreach($currencies as $code => $label)<option value="{{ $code }}">{{ $code }} · {{ $label }}</option>@endforeach
            </select></div><small style="opacity:.82">1 USD = {{ number_format($exchangeRates['KHR']) }} KHR</small><small id="trackerSaveStatus" role="status" aria-live="polite" style="opacity:.82"><i class="bi bi-cloud-check me-1"></i>Account data</small></div>
        </div>
    </div>

    <div class="row g-3 mb-4" id="tileMenu">
        <div class="col-6 col-lg-4"><button type="button" class="save-tile w-100 active" data-section="overview" style="--tile:#10b981" aria-pressed="true"><span class="tile-top"><span class="tile-ico"><i class="bi bi-grid-1x2"></i></span><i class="bi bi-arrow-right tile-arrow" aria-hidden="true"></i></span><span class="tile-title">Overview</span><span class="tile-sub">Your money at a glance</span><span class="tile-stat" data-tile-stat="overview"><span class="tile-stat-text">—</span></span></button></div>
        <div class="col-6 col-lg-4"><button type="button" class="save-tile w-100" data-section="daily-saving" style="--tile:#3b82f6" aria-pressed="false"><span class="tile-top"><span class="tile-ico"><i class="bi bi-piggy-bank"></i></span><i class="bi bi-arrow-right tile-arrow" aria-hidden="true"></i></span><span class="tile-title">Daily saving</span><span class="tile-sub">Record money saved</span><span class="tile-stat" data-tile-stat="daily-saving"><span class="tile-stat-text">—</span></span></button></div>
        <div class="col-6 col-lg-4"><button type="button" class="save-tile w-100" data-section="daily-spending" style="--tile:#f59e0b" aria-pressed="false"><span class="tile-top"><span class="tile-ico"><i class="bi bi-credit-card"></i></span><i class="bi bi-arrow-right tile-arrow" aria-hidden="true"></i></span><span class="tile-title">Daily spending</span><span class="tile-sub">Track money spent</span><span class="tile-stat" data-tile-stat="daily-spending"><span class="tile-stat-text">—</span></span></button></div>
        <div class="col-6 col-lg-4"><button type="button" class="save-tile w-100" data-section="monthly" style="--tile:#8b5cf6" aria-pressed="false"><span class="tile-top"><span class="tile-ico"><i class="bi bi-calendar-month"></i></span><i class="bi bi-arrow-right tile-arrow" aria-hidden="true"></i></span><span class="tile-title">Monthly saving</span><span class="tile-sub">Set a monthly target</span><span class="tile-stat" data-tile-stat="monthly"><span class="tile-stat-text">—</span></span></button></div>
        <div class="col-6 col-lg-4"><button type="button" class="save-tile w-100" data-section="yearly" style="--tile:#0ea5e9" aria-pressed="false"><span class="tile-top"><span class="tile-ico"><i class="bi bi-calendar-check"></i></span><i class="bi bi-arrow-right tile-arrow" aria-hidden="true"></i></span><span class="tile-title">Yearly saving</span><span class="tile-sub">Plan your whole year</span><span class="tile-stat" data-tile-stat="yearly"><span class="tile-stat-text">—</span></span></button></div>
        <div class="col-6 col-lg-4"><button type="button" class="save-tile w-100" data-section="plans" style="--tile:#ec4899" aria-pressed="false"><span class="tile-top"><span class="tile-ico"><i class="bi bi-bag-heart"></i></span><i class="bi bi-arrow-right tile-arrow" aria-hidden="true"></i></span><span class="tile-title">Buying plans</span><span class="tile-sub">Save toward a purchase</span><span class="tile-stat" data-tile-stat="plans"><span class="tile-stat-text">—</span></span></button></div>
    </div>

    <section class="section-panel active" id="panel-overview">
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3"><div class="kpi-card kpi-saved h-100"><div class="kpi-head"><span class="kpi-icon"><i class="bi bi-piggy-bank"></i></span><span class="kpi-label">Saved this month</span><span class="kpi-delta" id="overviewMonthDelta">—</span></div><div class="kpi-value" id="overviewMonth">—</div><div class="kpi-foot"><div class="kpi-caption" id="overviewMonthCaption">vs last month</div><svg class="kpi-spark" id="overviewMonthSpark" aria-hidden="true"></svg></div></div></div>
            <div class="col-6 col-xl-3"><div class="kpi-card kpi-spent h-100"><div class="kpi-head"><span class="kpi-icon"><i class="bi bi-credit-card"></i></span><span class="kpi-label">Spent this month</span><span class="kpi-delta" id="overviewSpentDelta">—</span></div><div class="kpi-value" id="overviewSpent">—</div><div class="kpi-foot"><div class="kpi-caption" id="overviewSpentCaption">vs last month</div><svg class="kpi-spark" id="overviewSpentSpark" aria-hidden="true"></svg></div></div></div>
            <div class="col-6 col-xl-3"><div class="kpi-card kpi-year h-100"><div class="kpi-head"><span class="kpi-icon"><i class="bi bi-calendar2-check"></i></span><span class="kpi-label">Saved this year</span><span class="kpi-delta" id="overviewYearDelta">—</span></div><div class="kpi-value" id="overviewYear">—</div><div class="kpi-foot"><div class="kpi-caption flex-grow-1"><span id="overviewYearCaption">Set a yearly goal</span><div class="kpi-meter"><span id="overviewYearMeter" style="width:0%;background:linear-gradient(90deg,#60a5fa,#2563eb)"></span></div></div><svg class="kpi-spark" id="overviewYearSpark" aria-hidden="true"></svg></div></div></div>
            <div class="col-6 col-xl-3"><div class="kpi-card kpi-net h-100"><div class="kpi-head"><span class="kpi-icon"><i class="bi bi-wallet2"></i></span><span class="kpi-label">Net this year</span><span class="kpi-delta" id="overviewNetDelta">—</span></div><div class="kpi-value" id="overviewNet">—</div><div class="kpi-foot"><div class="kpi-caption flex-grow-1"><span id="overviewNetCaption">Saved minus spent</span><div class="kpi-meter" style="gap:2px"><span id="overviewNetSaved" style="width:50%;background:#3b82f6"></span><span id="overviewNetSpent" style="width:50%;background:#f59e0b"></span></div></div></div></div></div>
        </div>
        <div class="save-card p-3 p-md-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3"><div><h2 class="h5 fw-bold mb-1">Saved vs spent</h2><div class="small muted">Compare your money across time.</div></div>
                <div class="btn-group btn-group-sm" role="group" aria-label="Chart period"><button class="btn btn-outline-secondary period-btn" data-period="7">7 days</button><button class="btn btn-outline-secondary period-btn" data-period="30">30 days</button><button class="btn btn-outline-secondary period-btn" data-period="12">12 months</button></div>
            </div>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 small mb-2"><div class="d-flex gap-3"><span><i class="legend-dot" style="background:#3b82f6;border-radius:3px"></i>Saved</span><span><i class="legend-dot" style="background:#f59e0b;border-radius:3px"></i>Spent</span><span class="muted"><i class="legend-dot" style="background:transparent;border:1.5px dashed #10b981"></i>Avg saved</span></div><div class="d-flex gap-2"><span class="net-pill pos" id="chartSumSaved">—</span><span class="net-pill zero" id="chartSumSpent">—</span></div></div>
            <div class="chart-wrap"><svg id="overviewChart" role="img" aria-label="Saved and spent chart"></svg><div class="chart-tooltip" id="chartTooltip"></div></div>
            <div class="text-end"><button class="btn btn-sm btn-outline-secondary table-toggle" id="chartTableToggle" aria-pressed="false"><i class="bi bi-table me-1"></i>Show table</button></div>
            <div class="table-responsive mt-3 d-none" id="chartTableWrap"><table class="table table-sm summary-table align-middle mb-0"><thead><tr><th>Period</th><th class="text-end">Saved</th><th class="text-end">Spent</th><th class="text-end">Net</th><th style="width:22%">Saved / spent split</th></tr></thead><tbody id="chartTable"></tbody></table></div>
        </div>
    </section>

    <section class="section-panel" id="panel-daily-saving"><div class="row g-3">
        <div class="col-lg-4"><div class="save-card p-3 p-md-4"><form class="entry-form" data-kind="saved">
            <div class="entry-form__head"><h2 class="h5 fw-bold mb-0">Add daily saving</h2>
                <div class="cur-switch" role="radiogroup" aria-label="Currency for this entry">
                    <input type="radio" id="saved-cur-usd" name="currency" value="USD"><label for="saved-cur-usd" title="US Dollar"><b>$</b> USD</label>
                    <input type="radio" id="saved-cur-khr" name="currency" value="KHR"><label for="saved-cur-khr" title="Cambodian Riel"><b>៛</b> KHR</label>
                    <span class="cur-switch__thumb" aria-hidden="true"></span>
                </div>
            </div>
            <label class="form-label small" for="saved-amount">Amount</label><div class="amount-field mb-3"><span class="amount-field__sym" aria-hidden="true">$</span><input id="saved-amount" name="amount" class="form-control" type="number" min="0.01" step="0.01" required placeholder="0.00"></div>
            <label class="form-label small">Category</label><select name="category" class="form-select mb-3">@foreach($categories as $category)<option>{{ $category }}</option>@endforeach</select>
            <label class="form-label small">Date</label><input name="date" class="form-control mb-3" type="date" required>
            <label class="form-label small"><i class="bi bi-sticky note-icon me-1"></i>Note <span class="muted">(optional)</span></label><textarea name="note" class="form-control mb-3" rows="2" maxlength="180" placeholder="Add a reminder or what this saving is for..."></textarea>
            <button class="btn btn-success w-100"><i class="bi bi-plus-lg me-1"></i>Add saving</button>
        </form></div></div>
        <div class="col-lg-8"><div class="row g-2 g-md-3 mb-3"><div class="col-12 col-sm-4"><div class="kpi-card kpi-saved h-100"><div class="kpi-head"><span class="kpi-icon"><i class="bi bi-sunrise"></i></span><span class="kpi-label">Saved today</span><span class="kpi-delta" id="savedTodayDelta">—</span></div><div class="kpi-value" id="savedToday">—</div><div class="kpi-foot"><div class="kpi-caption" id="savedTodayCaption">vs yesterday</div></div></div></div><div class="col-6 col-sm-4"><div class="kpi-card kpi-year h-100"><div class="kpi-head"><span class="kpi-icon"><i class="bi bi-calendar-week"></i></span><span class="kpi-label">Last 7 days</span><span class="kpi-delta" id="savedWeekDelta">—</span></div><div class="kpi-value" id="savedWeek">—</div><div class="kpi-foot"><div class="kpi-caption" id="savedWeekCaption">vs previous 7 days</div><svg class="kpi-mini-bars" id="savedWeekBars" aria-hidden="true"></svg></div></div></div><div class="col-6 col-sm-4"><div class="kpi-card kpi-net h-100"><div class="kpi-head"><span class="kpi-icon"><i class="bi bi-fire"></i></span><span class="kpi-label">Daily average</span><span class="kpi-delta" id="savedStreak">—</span></div><div class="kpi-value" id="savedAverage">—</div><div class="kpi-foot"><div class="kpi-caption" id="savedAverageCaption">Across 7 days</div></div></div></div></div>
            <div class="save-card p-3 p-md-4"><div class="d-flex justify-content-between align-items-center gap-2 mb-3"><div><h2 class="h5 fw-bold mb-1">Recent savings</h2><small class="muted">Your latest saving activity</small></div><span class="badge rounded-pill" style="background:var(--badge-green-bg);color:var(--badge-green-text)"><i class="bi bi-arrow-down-left me-1"></i>Saved</span></div><div class="table-responsive"><table class="table entry-table align-middle mb-0"><thead><tr><th>Date</th><th>Category and note</th><th class="text-end">Amount</th><th class="text-end"><span class="visually-hidden">Actions</span></th></tr></thead><tbody id="savedEntries"></tbody></table></div></div></div>
    </div></section>

    <section class="section-panel" id="panel-daily-spending"><div class="row g-3">
        <div class="col-lg-4"><div class="save-card p-3 p-md-4"><form class="entry-form" data-kind="spent">
            <div class="entry-form__head"><h2 class="h5 fw-bold mb-0">Add daily spending</h2>
                <div class="cur-switch" role="radiogroup" aria-label="Currency for this entry">
                    <input type="radio" id="spent-cur-usd" name="currency" value="USD"><label for="spent-cur-usd" title="US Dollar"><b>$</b> USD</label>
                    <input type="radio" id="spent-cur-khr" name="currency" value="KHR"><label for="spent-cur-khr" title="Cambodian Riel"><b>៛</b> KHR</label>
                    <span class="cur-switch__thumb" aria-hidden="true"></span>
                </div>
            </div>
            <label class="form-label small" for="spent-amount">Amount</label><div class="amount-field mb-3"><span class="amount-field__sym" aria-hidden="true">$</span><input id="spent-amount" name="amount" class="form-control" type="number" min="0.01" step="0.01" required placeholder="0.00"></div>
            <label class="form-label small">Category</label><select name="category" class="form-select mb-3">@foreach($categories as $category)<option>{{ $category }}</option>@endforeach</select>
            <label class="form-label small">Date</label><input name="date" class="form-control mb-3" type="date" required>
            <label class="form-label small"><i class="bi bi-sticky note-icon me-1"></i>Note <span class="muted">(optional)</span></label><textarea name="note" class="form-control mb-3" rows="2" maxlength="180" placeholder="Add a detail, reminder, or reason for this expense..."></textarea>
            <button class="btn btn-warning w-100"><i class="bi bi-plus-lg me-1"></i>Add spending</button>
        </form></div></div>
        <div class="col-lg-8"><div class="row g-2 g-md-3 mb-3"><div class="col-6"><div class="kpi-card kpi-spent h-100"><div class="kpi-head"><span class="kpi-icon"><i class="bi bi-receipt"></i></span><span class="kpi-label">Spent today</span><span class="kpi-delta" id="spentTodayDelta">—</span></div><div class="kpi-value" id="spentToday">—</div><div class="kpi-foot"><div class="kpi-caption" id="spentTodayCaption">vs yesterday</div></div></div></div><div class="col-6"><div class="kpi-card kpi-spent h-100"><div class="kpi-head"><span class="kpi-icon"><i class="bi bi-calendar-week"></i></span><span class="kpi-label">Last 7 days</span><span class="kpi-delta" id="spentWeekDelta">—</span></div><div class="kpi-value" id="spentWeek">—</div><div class="kpi-foot"><div class="kpi-caption" id="spentWeekCaption">vs previous 7 days</div><svg class="kpi-mini-bars" id="spentWeekBars" aria-hidden="true"></svg></div></div></div></div>
            <div class="save-card p-3 p-md-4"><div class="d-flex justify-content-between align-items-center gap-2 mb-3"><div><h2 class="h5 fw-bold mb-1">Recent spending</h2><small class="muted">Your latest expense activity</small></div><span class="badge rounded-pill" style="background:var(--badge-amber-bg);color:var(--badge-amber-text)"><i class="bi bi-arrow-up-right me-1"></i>Spent</span></div><div class="table-responsive"><table class="table entry-table align-middle mb-0"><thead><tr><th>Date</th><th>Category and note</th><th class="text-end">Amount</th><th class="text-end"><span class="visually-hidden">Actions</span></th></tr></thead><tbody id="spentEntries"></tbody></table></div></div></div>
    </div></section>

    <section class="section-panel" id="panel-monthly"><div class="monthly-goal">
        <div class="monthly-heading">
            <div class="monthly-title"><span class="monthly-title-icon"><i class="bi bi-piggy-bank"></i></span><div><h2 class="h4 fw-bold mb-1">Monthly saving goal</h2><div class="small muted">Choose a period and track your saving progress.</div></div></div>
            <div class="month-controls"><input id="monthPicker" type="month" class="form-control month-control" aria-label="Choose month"><input id="monthTarget" type="number" min="0" step="0.01" class="form-control month-control" placeholder="Target amount" aria-label="Monthly target amount"><button id="saveMonthTarget" class="btn btn-success month-control px-3"><i class="bi bi-floppy me-1"></i>Save goal</button></div>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3"><div class="month-kpi month-kpi-target h-100"><span class="month-kpi-icon"><i class="bi bi-wallet2"></i></span><div><div class="month-kpi-label">Target amount</div><div class="month-kpi-value" id="monthKpiTarget">—</div></div></div></div>
            <div class="col-6 col-xl-3"><div class="month-kpi month-kpi-saved h-100"><span class="month-kpi-icon"><i class="bi bi-piggy-bank"></i></span><div><div class="month-kpi-label">Saved amount</div><div class="month-kpi-value" id="monthKpiSaved">—</div></div></div></div>
            <div class="col-6 col-xl-3"><div class="month-kpi month-kpi-left h-100"><span class="month-kpi-icon"><i class="bi bi-bullseye"></i></span><div><div class="month-kpi-label">Remaining</div><div class="month-kpi-value" id="monthKpiRemaining">—</div></div></div></div>
            <div class="col-6 col-xl-3"><div class="month-kpi month-kpi-progress h-100"><span class="month-kpi-icon"><i class="bi bi-bar-chart-line-fill"></i></span><div class="flex-grow-1"><div class="month-kpi-label">Progress</div><div class="d-flex align-items-center justify-content-between gap-2"><div class="month-kpi-value" id="monthKpiPercent">0%</div><small class="muted" id="monthPerDay">— / day</small></div><div class="progress mt-2"><div id="monthProgress" class="progress-bar bg-success" style="width:0%"></div></div></div></div></div>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-lg-7"><div class="monthly-chart-panel"><div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2"><div class="d-flex align-items-center gap-2"><i class="bi bi-graph-up-arrow text-info fs-5"></i><div><h3 class="h6 fw-bold mb-0">Daily savings</h3><small class="muted">Track your daily saving for this month.</small></div></div><div class="d-flex gap-3 small"><span><i class="legend-dot" style="background:#10b981;border-radius:3px"></i>Saved</span><span><i class="legend-dot" style="background:transparent;border:1.5px dashed #64748b"></i>Daily target</span></div></div><span class="visually-hidden" id="monthProgressText">—</span><span class="visually-hidden" id="monthPercent">0%</span><span class="visually-hidden" id="monthLeft">—</span><div class="d-flex justify-content-end mb-1"><span class="badge rounded-pill" style="background:var(--badge-green-bg);color:var(--badge-green-text)" id="monthChartTotal">—</span></div><div class="chart-wrap"><svg id="monthChart" role="img" aria-label="Daily savings and monthly target chart"></svg></div></div></div>
            <div class="col-lg-5"><div class="monthly-chart-panel"><div class="d-flex justify-content-between align-items-center mb-2"><div class="d-flex align-items-center gap-2"><i class="bi bi-calendar3 text-info fs-5"></i><div><h3 class="h6 fw-bold mb-0">Weekly summary</h3><small class="muted">Your savings by week</small></div></div><span class="small text-info fw-semibold">Best week <i class="bi bi-arrow-right"></i></span></div><div class="table-responsive"><table class="table weekly-table align-middle mb-0"><thead><tr><th>Week</th><th>Date range</th><th class="text-end">Saved</th></tr></thead><tbody id="weekTable"></tbody></table></div></div></div>
        </div>
        <div class="row g-3"><div class="col-md-6"><div class="coach-card"><div class="d-flex align-items-center gap-2 mb-3"><span class="coach-icon"><i class="bi bi-lightning-charge-fill"></i></span><div><h3 class="h6 fw-bold mb-0">Monthly saving pace</h3><small class="muted">A simple daily plan for your goal.</small></div></div><div class="row g-2"><div class="col-6"><div class="coach-stat"><div class="coach-stat-label">Save each day</div><div class="coach-stat-value text-success" id="monthCoachDaily">—</div></div></div><div class="col-6"><div class="coach-stat"><div class="coach-stat-label">Weekly pace</div><div class="coach-stat-value" id="monthCoachWeekly">—</div></div></div></div><div class="small muted mt-3" id="monthCoachMessage">Set a monthly goal to get a personalized saving pace.</div></div></div><div class="col-md-6"><div class="month-bottom-card"><div class="d-flex justify-content-between align-items-center gap-2 mb-2"><div><h3 class="h6 fw-bold mb-1">Your goal progress</h3><small class="muted" id="monthBottomProgress">—</small></div><strong class="text-success" id="monthBottomPercent">0%</strong></div><div class="progress"><div id="monthBottomBar" class="progress-bar bg-success" style="width:0%"></div></div><small class="muted d-block mt-2">Still to save: <strong id="monthBottomLeft">—</strong></small></div></div></div>
    </div></section>

    <section class="section-panel" id="panel-yearly"><div class="monthly-goal">
        <div class="monthly-heading">
            <div class="monthly-title"><span class="monthly-title-icon"><i class="bi bi-calendar2-check"></i></span><div><h2 class="h4 fw-bold mb-1">Yearly saving goal</h2><div class="small muted">Choose a year and follow your savings progress.</div></div></div>
            <div class="month-controls"><input id="yearPicker" type="number" class="form-control month-control" aria-label="Choose year" style="width:125px"><input id="yearTarget" type="number" min="0" step="0.01" class="form-control month-control" placeholder="Year target" aria-label="Yearly target amount"><button id="saveYearTarget" class="btn btn-success month-control px-3"><i class="bi bi-floppy me-1"></i>Save goal</button></div>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3"><div class="month-kpi month-kpi-target h-100"><span class="month-kpi-icon"><i class="bi bi-wallet2"></i></span><div><div class="month-kpi-label">Target amount</div><div class="month-kpi-value" id="yearKpiTarget">—</div></div></div></div>
            <div class="col-6 col-xl-3"><div class="month-kpi month-kpi-saved h-100"><span class="month-kpi-icon"><i class="bi bi-piggy-bank"></i></span><div><div class="month-kpi-label">Saved this year</div><div class="month-kpi-value" id="yearKpiSaved">—</div></div></div></div>
            <div class="col-6 col-xl-3"><div class="month-kpi month-kpi-left h-100"><span class="month-kpi-icon"><i class="bi bi-bullseye"></i></span><div><div class="month-kpi-label">Remaining</div><div class="month-kpi-value" id="yearKpiRemaining">—</div></div></div></div>
            <div class="col-6 col-xl-3"><div class="month-kpi month-kpi-progress h-100"><span class="month-kpi-icon"><i class="bi bi-bar-chart-line-fill"></i></span><div class="flex-grow-1"><div class="month-kpi-label">Goal progress</div><div class="month-kpi-value" id="yearKpiPercent">0%</div><div class="progress mt-2"><div id="yearProgress" class="progress-bar bg-success" style="width:0%"></div></div></div></div></div>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-lg-7"><div class="monthly-chart-panel yearly-chart-panel"><div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2"><div class="d-flex align-items-center gap-2"><i class="bi bi-graph-up-arrow text-info fs-5"></i><div><h3 class="h6 fw-bold mb-0">Monthly savings</h3><small class="muted">Track your savings month by month.</small></div></div><div class="d-flex gap-3 small"><span><i class="legend-dot" style="background:#3b82f6;border-radius:3px"></i>Saved</span><span><i class="legend-dot" style="background:transparent;border:1.5px dashed #64748b"></i>Monthly pace</span></div></div><div class="d-flex justify-content-end mb-1"><span class="badge rounded-pill" style="background:var(--badge-blue-bg);color:var(--badge-blue-text)" id="yearChartTotal">—</span></div><div class="chart-wrap"><svg id="yearChart" role="img" aria-label="Monthly savings against yearly target pace"></svg></div></div></div>
            <div class="col-lg-5"><div class="monthly-chart-panel"><div class="d-flex align-items-center gap-2 mb-2"><i class="bi bi-calendar3 text-info fs-5"></i><div><h3 class="h6 fw-bold mb-0">Month-by-month</h3><small class="muted">Compare each month with your goal.</small></div></div><div class="year-table-wrap"><table class="table yearly-table align-middle mb-0"><thead><tr><th>Month</th><th class="text-end">Saved</th><th class="text-end">Pace</th></tr></thead><tbody id="yearTable"></tbody></table></div></div></div>
        </div>
        <div class="row g-3"><div class="col-md-6"><div class="coach-card"><div class="d-flex align-items-center gap-2 mb-3"><span class="coach-icon"><i class="bi bi-signpost-split-fill"></i></span><div><h3 class="h6 fw-bold mb-0">Yearly saving pace</h3><small class="muted">See the monthly amount needed to reach your target.</small></div></div><div class="row g-2"><div class="col-6"><div class="coach-stat"><div class="coach-stat-label">Needed each month</div><div class="coach-stat-value text-success" id="yearCoachNeeded">—</div></div></div><div class="col-6"><div class="coach-stat"><div class="coach-stat-label">Your monthly average</div><div class="coach-stat-value" id="yearCoachAverage">—</div></div></div></div><div class="small fw-semibold mt-3" id="yearCoachStatus">Set a yearly goal to get your saving plan.</div></div></div><div class="col-md-6"><div class="month-bottom-card"><div class="d-flex justify-content-between align-items-center gap-2"><div><h3 class="h6 fw-bold mb-1">Estimated year-end total</h3><small class="muted">Based on your average pace so far.</small></div><strong class="fs-5 text-success" id="yearProjection">—</strong></div><div class="d-flex justify-content-between small muted mt-3"><span id="yearProgressText">—</span><span id="yearPercent">0%</span></div><div class="progress mt-1"><div id="yearBottomProgress" class="progress-bar bg-success" style="width:0%"></div></div><small class="muted d-block mt-2">Still to save: <strong id="yearLeft">—</strong></small></div></div></div>
    </div></section>

    <section class="section-panel" id="panel-plans">
        <div class="plans-heading mb-3"><div class="d-flex align-items-center gap-3"><span class="plans-heading-icon"><i class="bi bi-bag-heart"></i></span><div><h2 class="h4 fw-bold mb-1">Buying plans</h2><div class="small muted">Turn the things you want into savings goals you can track.</div></div></div><span class="badge rounded-pill text-bg-success px-3 py-2"><i class="bi bi-check2-circle me-1"></i>Progress saved</span></div>
        <div class="row g-3 mb-4">
            
            <div class="col-6 col-xl-3"><div class="kpi-card kpi-year h-100"><div class="kpi-head"><span class="kpi-icon"><i class="bi bi-bullseye"></i></span><span class="kpi-label">Total planned</span><span class="kpi-delta" id="planCountChip">—</span></div><div class="kpi-value" id="planTotalTarget">—</div><div class="kpi-foot"><div class="kpi-caption" id="planTotalCaption">Across all plans</div></div></div></div>
            <div class="col-6 col-xl-3"><div class="kpi-card kpi-saved h-100"><div class="kpi-head"><span class="kpi-icon"><i class="bi bi-piggy-bank"></i></span><span class="kpi-label">Already saved</span><span class="kpi-delta" id="planSavedChip">—</span></div><div class="kpi-value" id="planTotalSaved">—</div><div class="kpi-foot"><div class="kpi-caption flex-grow-1"><span id="planSavedCaption">—</span><div class="kpi-meter"><span id="planSavedMeter" style="width:0%;background:linear-gradient(90deg,#34d399,#059669)"></span></div></div></div></div></div>
            <div class="col-6 col-xl-3"><div class="kpi-card kpi-spent h-100"><div class="kpi-head"><span class="kpi-icon"><i class="bi bi-hourglass-split"></i></span><span class="kpi-label">Still to save</span><span class="kpi-delta" id="planLeftChip">—</span></div><div class="kpi-value" id="planTotalLeft">—</div><div class="kpi-foot"><div class="kpi-caption" id="planLeftCaption">—</div></div></div></div>
            <div class="col-6 col-xl-3"><div class="kpi-card kpi-net h-100"><div class="kpi-head"><span class="kpi-icon"><i class="bi bi-collection"></i></span><span class="kpi-label">Active plans</span><span class="kpi-delta" id="planNextChip">—</span></div><div class="kpi-value" id="planActiveCount">—</div><div class="kpi-foot"><div class="kpi-caption" id="planActiveCaption">—</div></div></div></div>
        </div>
        <div class="row g-4">
            <div class="col-lg-4"><div class="save-card plan-form-card"><form id="planForm" data-kind="plan">
                <div class="entry-form__head mb-1"><div class="d-flex align-items-center gap-2" style="min-width:0"><span class="tile-icon mb-0"><i class="bi bi-plus-lg"></i></span><h3 class="h5 fw-bold mb-0">Create a plan</h3></div>
                    <div class="cur-switch" role="radiogroup" aria-label="Currency for this plan">
                        <input type="radio" id="plan-cur-usd" name="currency" value="USD"><label for="plan-cur-usd" title="US Dollar"><b>$</b> USD</label>
                        <input type="radio" id="plan-cur-khr" name="currency" value="KHR"><label for="plan-cur-khr" title="Cambodian Riel"><b>៛</b> KHR</label>
                        <span class="cur-switch__thumb" aria-hidden="true"></span>
                    </div>
                </div>
                <p class="small muted mb-4 mt-2">Set a price and deadline to get a monthly saving pace.</p>
                <label class="form-label small fw-semibold">What are you saving for?</label><input name="name" class="form-control mb-3" required maxlength="70" placeholder="e.g. New bicycle">
                <label class="form-label small fw-semibold">Plan type</label><select name="type" class="form-select mb-3">@foreach($planTypes as $type)<option>{{ $type }}</option>@endforeach</select>
                <div class="row g-2"><div class="col-6"><label class="form-label small fw-semibold" for="plan-price">Price</label><div class="amount-field mb-3"><span class="amount-field__sym" aria-hidden="true">$</span><input id="plan-price" name="price" class="form-control" type="number" min="0.01" step="0.01" required placeholder="0.00"></div></div><div class="col-6"><label class="form-label small fw-semibold" for="plan-saved">Saved so far</label><div class="amount-field mb-3"><span class="amount-field__sym" aria-hidden="true">$</span><input id="plan-saved" name="saved" class="form-control" type="number" min="0" step="0.01" value="0" required></div></div></div>
                <label class="form-label small fw-semibold">Target date</label><input name="deadline" class="form-control mb-3" type="date" required>
                <button class="btn btn-success w-100 py-2"><i class="bi bi-plus-circle me-1"></i>Create buying plan</button>
            </form></div></div>
            <div class="col-lg-8"><div class="d-flex justify-content-between align-items-end gap-2 mb-3"><div><h3 class="h5 fw-bold mb-1">Your goals</h3><small class="muted">Add money anytime; each contribution is recorded as a saving.</small></div><span class="small muted d-none d-sm-inline"><i class="bi bi-calendar-check me-1"></i>Deadline based</span></div><div id="plansList" class="row g-3"></div></div>
        </div>
    </section>
    <div class="small muted text-center mt-4"><i class="bi bi-shield-lock me-1"></i>Your tracker entries, plans, goals, currency, and notes are saved to your account database.</div>

    <div class="modal fade" id="noteEditor" tabindex="-1" aria-labelledby="noteEditorTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content" style="background:var(--admin-surface);color:var(--admin-text);border-color:var(--admin-border);border-radius:18px">
            <div class="modal-header border-bottom" style="border-color:var(--admin-border)!important"><div><h2 class="modal-title h5 fw-bold" id="noteEditorTitle">Edit entry note</h2><small class="muted">Keep a useful reminder with this transaction.</small></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body"><label for="noteEditorText" class="form-label small fw-semibold">Note</label><textarea id="noteEditorText" class="form-control" rows="4" maxlength="180" placeholder="Add a note..."></textarea><div class="small muted text-end mt-1">Up to 180 characters</div></div>
            <div class="modal-footer border-0 pt-0"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-success" id="saveEditedNote"><i class="bi bi-check2 me-1"></i>Save note</button></div>
        </div></div>
    </div>
    <div class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content" style="background:var(--admin-surface);color:var(--admin-text);border-color:var(--admin-border);border-radius:20px">
            <div class="modal-header border-bottom" style="border-color:var(--admin-border)!important"><div><h2 class="modal-title h5 fw-bold" id="notesModalTitle"><i class="bi bi-journal-richtext me-2 text-success"></i>Money notes</h2><small class="muted">Write reminders, ideas, or reflections. Each save stays in your note history.</small></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body"><div class="row g-4">
                <div class="col-lg-7"><label for="journalTitle" class="form-label small fw-semibold">Note title</label><input id="journalTitle" class="form-control mb-3" maxlength="100" placeholder="Monthly saving">
                    <div class="d-flex flex-wrap align-items-center gap-1 p-2 border rounded-top" style="border-color:var(--admin-border)!important;background:var(--card-muted-bg)">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-format="bold" title="Bold"><i class="bi bi-type-bold"></i></button><button type="button" class="btn btn-sm btn-outline-secondary" data-format="italic" title="Italic"><i class="bi bi-type-italic"></i></button><button type="button" class="btn btn-sm btn-outline-secondary" data-format="underline" title="Underline"><i class="bi bi-type-underline"></i></button><span class="vr mx-1"></span><label for="journalTextColor" class="small muted mb-0">Text color</label><input type="color" id="journalTextColor" class="form-control form-control-color" value="#15803d" title="Choose text color" aria-label="Choose text color" style="width:34px;height:32px;padding:3px"><button type="button" class="btn btn-sm btn-outline-secondary" id="applyJournalColor" title="Apply text color"><i class="bi bi-palette"></i></button><span class="vr mx-1"></span><button type="button" class="btn btn-sm btn-outline-secondary" data-format="insertUnorderedList" title="Bulleted list"><i class="bi bi-list-ul"></i></button><button type="button" class="btn btn-sm btn-outline-secondary" data-format="insertOrderedList" title="Numbered list"><i class="bi bi-list-ol"></i></button><button type="button" class="btn btn-sm btn-outline-secondary" data-format="formatBlock" data-format-value="blockquote" title="Quote"><i class="bi bi-quote"></i></button>
                    </div>
                    <div id="journalEditor" class="form-control rounded-top-0" contenteditable="true" role="textbox" aria-label="Write your note" aria-multiline="true" data-placeholder="Write your note here…" style="min-height:230px;overflow-y:auto"></div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3"><small class="muted">Every things happen from one day so please do harder to get one day!</small><button type="button" class="btn btn-success" id="saveJournalNote"><i class="bi bi-bookmark-check me-1"></i>Save to history</button></div>
                </div>
                <div class="col-lg-5"><div class="d-flex justify-content-between align-items-center mb-2"><div><h3 class="h6 fw-bold mb-0">Note history</h3><small class="muted">Every things will reminder !</small></div><span class="badge rounded-pill text-bg-light" id="journalCount">0 saved</span></div><div id="journalHistory" class="d-flex flex-column gap-2"></div><div id="journalViewer" class="journal-item mt-3 d-none"><div class="d-flex justify-content-between align-items-start gap-2"><div><h4 class="h6 fw-bold mb-1" id="journalViewTitle"></h4><small class="muted" id="journalViewDate"></small></div><button type="button" class="btn-close" id="closeJournalViewer" aria-label="Close note preview"></button></div><div class="border-top mt-3 pt-3" style="border-color:var(--admin-border)!important" id="journalViewContent"></div><div id="journalRevisions" class="small mt-3"></div></div></div>
            </div></div>
        </div></div>
    </div>
    <div class="note-delete-overlay d-none" id="deleteNoteDialog" role="alertdialog" aria-modal="true" aria-labelledby="deleteNoteTitle" aria-describedby="deleteNoteDescription">
        <div class="note-delete-card">
            <div class="mx-auto mb-2 d-grid rounded-circle" style="place-items:center;width:46px;height:46px;background:var(--badge-red-bg);color:var(--badge-red-text);font-size:19px"><i class="bi bi-trash3"></i></div>
            <h2 class="h6 fw-bold mb-1" id="deleteNoteTitle"></h2>
            <div class="d-flex justify-content-center gap-2"><button type="button" class="btn btn-light btn-sm rounded-3 px-3" id="cancelDeleteNote">Keep note</button><button type="button" class="btn btn-danger btn-sm rounded-3 px-3 fw-semibold" id="confirmDeleteNote"><i class="bi bi-trash3 me-1"></i>Delete</button></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    'use strict';
    const LEGACY_KEY = 'personal_saving_tracker_v1_{{ auth()->id() }}';
    const DATABASE_STATE = @json($trackerState);
    const SAVE_URL = @json(route('admin.saving.state'));
    const CSRF_TOKEN = @json(csrf_token());
    const initial = { entries: [], plans: [], monthlyTargets: {}, yearlyTargets: {}, notes: [], currency: 'USD' };
    const legacyState = loadLegacyState();
    const state = DATABASE_STATE ? mergeTrackerStates(DATABASE_STATE, legacyState) : { ...initial, ...(legacyState || {}) };
    const needsCurrencySync = Boolean(DATABASE_STATE && !['USD', 'KHR'].includes(DATABASE_STATE.currency));
    if (!['USD', 'KHR'].includes(state.currency)) state.currency = 'USD';
    const needsLegacySync = Boolean(legacyState && JSON.stringify(state) !== JSON.stringify({ ...initial, ...(DATABASE_STATE || {}) }));
    // Units of each currency per 1 USD. Entries keep the currency they were typed in;
    // plans and goals are kept in USD. Everything is converted to the chosen currency on screen.
    const RATES = @json($exchangeRates);
    function convert(amount, from, to) { const value = Number(amount) || 0; from = from || 'USD'; to = to || 'USD'; return from === to ? value : value / (RATES[from] || 1) * (RATES[to] || 1); }
    const toUsd = (amount, from = state.currency) => convert(amount, from, 'USD');
    const fromUsd = usd => convert(usd, 'USD', state.currency);
    const entryUsd = item => convert(item.amount, item.currency, 'USD');
    const inputValue = usd => { const value = fromUsd(usd); return state.currency === 'KHR' ? Math.round(value) : Math.round(value * 100) / 100; };
    // Older data had no currency stored: amounts were whatever the user typed
    // while their saved currency was selected, so adopt that currency once.
    // PHP sends empty maps back as [] — use plain objects so goal keys survive JSON.stringify.
    ['monthlyTargets', 'yearlyTargets'].forEach(key => { if (!state[key] || Array.isArray(state[key])) state[key] = { ...(state[key] || {}) }; });
    const needsAmountSync = state.amountBase !== 'USD';
    if (needsAmountSync) {
        const typedIn = state.currency;
        state.entries.forEach(entry => { if (!entry.currency) entry.currency = typedIn; });
        state.plans.forEach(plan => { plan.price = toUsd(plan.price, typedIn); plan.saved = toUsd(plan.saved, typedIn); });
        Object.keys(state.monthlyTargets).forEach(key => { state.monthlyTargets[key] = toUsd(state.monthlyTargets[key], typedIn); });
        Object.keys(state.yearlyTargets).forEach(key => { state.yearlyTargets[key] = toUsd(state.yearlyTargets[key], typedIn); });
        state.amountBase = 'USD';
    }
    const $ = (id) => document.getElementById(id);
    const today = localDate(new Date());
    let chartPeriod = 7;
    let editingNoteId = null;
    let editingJournalId = null;
    let pendingNoteDeleteId = null;
    let deleteNoteTrigger = null;
    let saveTimer = null;
    let saveInFlight = false;
    let savePending = false;

    function loadLegacyState() {
        try { return JSON.parse(localStorage.getItem(LEGACY_KEY) || 'null'); }
        catch (_) { return null; }
    }
    function mergeTrackerStates(database, legacy) {
        const current = { ...initial, ...(database || {}) };
        if (!legacy) return current;
        const old = { ...initial, ...legacy };
        const mergeById = (primary, secondary, fingerprint = null) => {
            const result = [...(Array.isArray(primary) ? primary : [])];
            const ids = new Set(result.map(item => String(item.id)));
            const fingerprints = fingerprint ? new Set(result.map(fingerprint)) : null;
            (Array.isArray(secondary) ? secondary : []).forEach(item => {
                if (ids.has(String(item.id))) return;
                const key = fingerprint ? fingerprint(item) : null;
                if (fingerprints && fingerprints.has(key)) return;
                result.push(item); ids.add(String(item.id)); if (fingerprints) fingerprints.add(key);
            });
            return result;
        };
        current.entries = mergeById(current.entries, old.entries, item => [item.kind, Number(item.amount).toFixed(2), item.category, item.date, item.note || ''].join('|'));
        current.plans = mergeById(current.plans, old.plans);
        current.notes = mergeById(current.notes, old.notes);
        current.monthlyTargets = { ...old.monthlyTargets, ...current.monthlyTargets };
        current.yearlyTargets = { ...old.yearlyTargets, ...current.yearlyTargets };
        current.currency = current.currency === 'USD' && old.currency ? old.currency : current.currency;
        return current;
    }
    function persist() { render(); queueDatabaseSave(); }
    function queueDatabaseSave() {
        clearTimeout(saveTimer);
        $('trackerSaveStatus').innerHTML = '<i class="bi bi-cloud-arrow-up me-1"></i>Saving to your account…';
        saveTimer = setTimeout(async () => {
            if (saveInFlight) { savePending = true; return; }
            saveInFlight = true;
            try {
                const response = await fetch(SAVE_URL, {
                    method: 'PUT',
                    headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN':CSRF_TOKEN },
                    body: JSON.stringify({ data:state })
                });
                if (!response.ok || response.redirected || !response.headers.get('content-type')?.includes('application/json')) {
                    const failure = await response.json().catch(() => null);
                    throw new Error(failure?.message || `Save failed (${response.status || 'invalid response'})`);
                }
                localStorage.removeItem(LEGACY_KEY);
                $('trackerSaveStatus').innerHTML = '<i class="bi bi-cloud-check me-1"></i>Saved to your account';
            } catch (error) {
                $('trackerSaveStatus').innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Could not save — check connection and retry';
                console.error('Saving tracker database sync failed', error);
            } finally {
                saveInFlight = false;
                if (savePending) { savePending = false; queueDatabaseSave(); }
            }
        }, 250);
    }
    function localDate(date) { const copy = new Date(date.getTime() - date.getTimezoneOffset() * 60000); return copy.toISOString().slice(0, 10); }
    // Format a USD amount in the currently chosen currency.
    function money(usd, code = state.currency || 'USD') {
        const value = convert(usd, 'USD', code), digits = code === 'KHR' ? 0 : 2;
        try { return new Intl.NumberFormat(undefined, { style:'currency', currency:code, currencyDisplay:'narrowSymbol', minimumFractionDigits:digits, maximumFractionDigits:digits }).format(value); }
        catch (_) { return `${code === 'KHR' ? '៛' : '$'}${value.toFixed(digits)}`; }
    }
    // Totals are always in USD (each entry converted from its own currency).
    function sum(entries) { return entries.reduce((total, item) => total + entryUsd(item), 0); }
    function entriesFor(kind, from, through) { return state.entries.filter(item => item.kind === kind && item.date >= from && item.date <= through); }
    function ymd(date) { return localDate(date); }
    function dayOffset(offset) { const d = new Date(); d.setDate(d.getDate() + offset); return ymd(d); }
    function safe(text) { return String(text ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }
    function emptyRow(cols, message) { return `<tr><td colspan="${cols}" class="empty">${message}</td></tr>`; }
    function normalizeNoteColor(value) {
        const color = String(value || '').trim();
        const hex = color.match(/^#([0-9a-f]{3}|[0-9a-f]{6})$/i);
        if (hex) {
            const digits = hex[1].length === 3 ? [...hex[1]].map(char => char + char).join('') : hex[1];
            return `#${digits.toLowerCase()}`;
        }
        const rgb = color.match(/^rgb\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)$/i);
        if (!rgb) return '';
        const channels = rgb.slice(1).map(Number);
        if (channels.some(channel => channel > 255)) return '';
        return `#${channels.map(channel => channel.toString(16).padStart(2, '0')).join('')}`;
    }
    function cleanRichHtml(html) {
        const parsed = new DOMParser().parseFromString(String(html || ''), 'text/html');
        const allowed = new Set(['B','STRONG','I','EM','U','S','P','BR','UL','OL','LI','BLOCKQUOTE','DIV','SPAN','FONT']);
        const cleanNode = node => [...node.childNodes].forEach(child => {
            if (child.nodeType === Node.ELEMENT_NODE) {
                cleanNode(child);
                if (!allowed.has(child.tagName)) child.replaceWith(...child.childNodes);
                else {
                    const rawColor = child.getAttribute('color') || child.style.color || '';
                    const color = normalizeNoteColor(rawColor);
                    [...child.attributes].forEach(attribute => child.removeAttribute(attribute.name));
                    if (color && (child.tagName === 'SPAN' || child.tagName === 'FONT')) {
                        if (child.tagName === 'FONT') {
                            const span = parsed.createElement('span');
                            span.style.color = color;
                            span.append(...child.childNodes);
                            child.replaceWith(span);
                        } else child.style.color = color;
                    }
                }
            } else if (child.nodeType !== Node.TEXT_NODE) child.remove();
        });
        cleanNode(parsed.body);
        return parsed.body.innerHTML;
    }
    function richText(html) { return new DOMParser().parseFromString(String(html || ''), 'text/html').body.textContent || ''; }
    function showJournalNote(note, revisionIndex = null) {
        const revision = revisionIndex === null ? null : note.revisions?.[revisionIndex];
        const title = revision?.title || note.title;
        const content = revision?.content ?? note.content;
        const savedAt = revision?.savedAt || note.updatedAt || note.savedAt;
        $('journalViewTitle').textContent = title;
        $('journalViewDate').textContent = `${revisionIndex === null ? 'Saved' : 'Earlier version'} · ${new Date(savedAt).toLocaleString(undefined, { dateStyle:'medium', timeStyle:'short' })}`;
        $('journalViewContent').innerHTML = cleanRichHtml(content) || '<span class="muted">This note has no text.</span>';
        const revisions = Array.isArray(note.revisions) ? note.revisions : [];
        $('journalRevisions').innerHTML = revisions.length ? `<div class="fw-semibold mb-1">Earlier versions</div><div class="d-flex flex-wrap gap-1">${revisions.map((item, index) => `<button type="button" class="btn btn-sm btn-outline-secondary" data-view-revision="${index}" data-revision-note="${safe(note.id)}">${safe(new Date(item.savedAt).toLocaleDateString())}</button>`).join('')}<button type="button" class="btn btn-sm btn-outline-success" data-view-revision="current" data-revision-note="${safe(note.id)}">Current</button></div>` : '';
        $('journalViewer').classList.remove('d-none');
    }
    function renderNoteHistory() {
        const notes = (Array.isArray(state.notes) ? state.notes : []).slice().sort((a,b) => (b.updatedAt || b.savedAt).localeCompare(a.updatedAt || a.savedAt));
        $('journalCount').textContent = `${notes.length} saved`;
        $('journalHistory').innerHTML = notes.length ? notes.map(note => {
            const preview = cleanRichHtml(note.content || '').trim();
            const date = new Date(note.updatedAt || note.savedAt).toLocaleString(undefined, { dateStyle:'medium', timeStyle:'short' });
            return `<div class="journal-item"><div class="d-flex justify-content-between align-items-start gap-2"><div class="min-w-0 w-100"><div class="fw-semibold text-body text-truncate">${safe(note.title)}</div><div class="small muted mt-1 journal-preview">${preview || '<span class="muted">No note text</span>'}</div><div class="small muted mt-2"><i class="bi bi-clock me-1"></i>${safe(date)}${note.revisions?.length ? ` · ${note.revisions.length} earlier version${note.revisions.length === 1 ? '' : 's'}` : ''}</div></div></div><div class="d-flex gap-1 mt-3"><button type="button" class="btn btn-sm btn-outline-secondary" data-view-note="${safe(note.id)}" title="View note"><i class="bi bi-eye me-1"></i>View</button><button type="button" class="btn btn-sm btn-outline-primary" data-edit-journal-note="${safe(note.id)}" title="Edit note"><i class="bi bi-pencil me-1"></i>Edit</button><button type="button" class="btn btn-sm btn-outline-danger ms-auto" data-delete-note="${safe(note.id)}" title="Delete note"><i class="bi bi-trash"></i></button></div></div>`;
        }).join('') : '<div class="journal-item text-center small muted py-4"><i class="bi bi-journal-plus fs-4 d-block mb-2"></i>Your saved notes will appear here.</div>';
    }
    $('journalViewer').addEventListener('click', event => {
        const button = event.target.closest('[data-view-revision]'); if (!button) return;
        const note = state.notes.find(item => item.id === button.dataset.revisionNote); if (!note) return;
        showJournalNote(note, button.dataset.viewRevision === 'current' ? null : Number(button.dataset.viewRevision));
    });

    document.querySelectorAll('#tileMenu [data-section]').forEach(button => button.addEventListener('click', () => {
        document.querySelectorAll('.save-tile').forEach(tile => { tile.classList.toggle('active', tile === button); tile.setAttribute('aria-pressed', tile === button); });
        document.querySelectorAll('.section-panel').forEach(panel => panel.classList.toggle('active', panel.id === `panel-${button.dataset.section}`));
        if (button.dataset.section === 'monthly') renderMonthly();
        if (button.dataset.section === 'yearly') renderYearly();
    }));
    document.querySelectorAll('[data-open-section]').forEach(button => button.addEventListener('click', () => {
        const section = button.dataset.openSection;
        document.querySelector(`#tileMenu [data-section="${section}"]`)?.click();
        document.querySelector(`#panel-${section} input[name="amount"]`)?.focus({ preventScroll:true });
        document.querySelector(`#panel-${section}`)?.scrollIntoView({ behavior:'smooth', block:'start' });
    }));
    document.querySelectorAll('[data-format]').forEach(button => {
        button.addEventListener('mousedown', event => event.preventDefault());
        button.addEventListener('click', () => {
            $('journalEditor').focus();
            document.execCommand(button.dataset.format, false, button.dataset.formatValue || null);
        });
    });
    let journalSelection = null;
    const rememberJournalSelection = () => {
        const selection = window.getSelection();
        if (selection && selection.rangeCount && $('journalEditor').contains(selection.anchorNode)) journalSelection = selection.getRangeAt(0).cloneRange();
    };
    document.addEventListener('selectionchange', rememberJournalSelection);
    $('journalTextColor').addEventListener('mousedown', rememberJournalSelection);
    $('applyJournalColor').addEventListener('mousedown', event => event.preventDefault());
    $('applyJournalColor').addEventListener('click', () => {
        const editor = $('journalEditor'); editor.focus();
        if (journalSelection) { const selection = window.getSelection(); selection.removeAllRanges(); selection.addRange(journalSelection); }
        document.execCommand('foreColor', false, $('journalTextColor').value);
        rememberJournalSelection();
    });
    $('notesModal').addEventListener('show.bs.modal', renderNoteHistory);
    $('saveJournalNote').addEventListener('click', () => {
        const title = $('journalTitle').value.trim();
        const content = cleanRichHtml($('journalEditor').innerHTML);
        if (!title && !richText(content).trim()) { $('journalTitle').focus(); return; }
        const now = new Date().toISOString();
        if (editingJournalId) {
            const note = state.notes.find(item => item.id === editingJournalId);
            if (note) {
                note.revisions = Array.isArray(note.revisions) ? note.revisions : [];
                note.revisions.unshift({ title:note.title, content:note.content, savedAt:note.updatedAt || note.savedAt });
                note.title = title || 'Untitled note'; note.content = content; note.updatedAt = now;
            }
        } else {
            state.notes.unshift({ id:crypto.randomUUID ? crypto.randomUUID() : String(Date.now()), title:title || 'Untitled note', content, savedAt:now, revisions:[] });
        }
        editingJournalId = null; $('journalTitle').value = ''; $('journalEditor').innerHTML = ''; $('saveJournalNote').innerHTML = '<i class="bi bi-bookmark-check me-1"></i>Save to history'; renderNoteHistory(); persist();
    });
    $('journalHistory').addEventListener('click', event => {
        const viewButton = event.target.closest('[data-view-note]');
        if (viewButton) {
            const note = state.notes.find(item => item.id === viewButton.dataset.viewNote); if (note) showJournalNote(note);
            return;
        }
        const editButton = event.target.closest('[data-edit-journal-note]');
        if (editButton) {
            const note = state.notes.find(item => item.id === editButton.dataset.editJournalNote); if (!note) return;
            editingJournalId = note.id; $('journalTitle').value = note.title; $('journalEditor').innerHTML = cleanRichHtml(note.content || ''); $('saveJournalNote').innerHTML = '<i class="bi bi-check2 me-1"></i>Save changes'; $('journalEditor').focus();
            $('journalEditor').scrollIntoView({ behavior:'smooth', block:'center' }); return;
        }
        const deleteButton = event.target.closest('[data-delete-note]');
        if (deleteButton) {
            const note = state.notes.find(item => item.id === deleteButton.dataset.deleteNote); if (!note) return;
            pendingNoteDeleteId = note.id;
            deleteNoteTrigger = deleteButton;
            $('deleteNoteTitle').textContent = ` Delete "${note.title}" ?`;
            $('deleteNoteDialog').classList.remove('d-none');
            $('confirmDeleteNote').focus();
        }
    });
    function closeDeleteNoteDialog() {
        $('deleteNoteDialog').classList.add('d-none');
        pendingNoteDeleteId = null;
        deleteNoteTrigger?.focus();
    }
    $('cancelDeleteNote').addEventListener('click', closeDeleteNoteDialog);
    $('deleteNoteDialog').addEventListener('click', event => {
        if (event.target === $('deleteNoteDialog')) closeDeleteNoteDialog();
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && !$('deleteNoteDialog').classList.contains('d-none')) closeDeleteNoteDialog();
    });
    $('confirmDeleteNote').addEventListener('click', () => {
        const note = state.notes.find(item => item.id === pendingNoteDeleteId);
        if (!note) return;
        state.notes = state.notes.filter(item => item.id !== note.id);
        if (editingJournalId === note.id) { editingJournalId = null; $('journalTitle').value = ''; $('journalEditor').innerHTML = ''; $('saveJournalNote').innerHTML = '<i class="bi bi-bookmark-check me-1"></i>Save to history'; }
        $('journalViewer').classList.add('d-none');
        closeDeleteNoteDialog(); renderNoteHistory(); persist();
    });
    $('closeJournalViewer').addEventListener('click', () => $('journalViewer').classList.add('d-none'));
    // Each entry form has its own $ / ៛ switch; it starts on the page currency.
    function setFormCurrency(form, code, animate = false) {
        const radio = form.querySelector(`input[name="currency"][value="${code}"]`); if (radio) radio.checked = true;
        const khr = code === 'KHR';
        form.querySelectorAll('.amount-field').forEach(field => {
            const input = field.querySelector('input'), optional = input.name === 'saved';
            field.querySelector('.amount-field__sym').textContent = khr ? '៛' : '$';
            input.step = khr ? '100' : '0.01';
            input.min = optional ? '0' : (khr ? '100' : '0.01');
            if (input.placeholder) input.placeholder = khr ? '0' : '0.00';
            if (animate) { field.classList.remove('is-bump'); void field.offsetWidth; field.classList.add('is-bump'); setTimeout(() => field.classList.remove('is-bump'), 250); }
        });
    }
    document.querySelectorAll('.entry-form').forEach(form => {
        form.elements.date.value = today;
        setFormCurrency(form, state.currency || 'USD');
        form.querySelectorAll('input[name="currency"]').forEach(radio => radio.addEventListener('change', () => setFormCurrency(form, radio.value, true)));
        form.addEventListener('submit', event => {
            event.preventDefault(); const data = new FormData(form); const amount = Number(data.get('amount'));
            if (!Number.isFinite(amount) || amount <= 0) return;
            const currency = data.get('currency') === 'KHR' ? 'KHR' : 'USD';
            state.entries.push({ id: crypto.randomUUID ? crypto.randomUUID() : String(Date.now()), kind:form.dataset.kind, amount, currency, category:data.get('category'), date:data.get('date'), note:data.get('note').trim(), source:'manual' });
            form.reset(); form.elements.date.value = today; setFormCurrency(form, currency); persist();
        });
    });
    document.querySelectorAll('[data-period]').forEach(button => button.addEventListener('click', () => {
        chartPeriod = Number(button.dataset.period); document.querySelectorAll('[data-period]').forEach(b => b.classList.toggle('active', b === button)); renderChart();
    }));
    $('chartTableToggle').addEventListener('click', () => {
        const wrap = $('chartTableWrap'); const shown = wrap.classList.toggle('d-none') === false;
        $('chartTableToggle').setAttribute('aria-pressed', shown); $('chartTableToggle').textContent = shown ? 'Hide table' : 'Show table';
    });
    $('currencySelect').value = state.currency || 'USD';
    $('currencySelect').addEventListener('change', event => {
        state.currency = event.target.value;
        document.querySelectorAll('.entry-form, #planForm').forEach(form => setFormCurrency(form, state.currency));
        persist();
    });
    function renderCurrencyLabels() { document.querySelectorAll('.cur-code').forEach(el => { el.textContent = state.currency === 'KHR' ? '(៛ KHR)' : '($ USD)'; }); document.querySelectorAll('#monthTarget, #yearTarget').forEach(input => { input.step = state.currency === 'KHR' ? '1' : '0.01'; if (input.name === 'amount' || input.name === 'price') input.min = state.currency === 'KHR' ? '1' : '0.01'; if (input.name !== 'saved' && input.placeholder.match(/^0/)) input.placeholder = state.currency === 'KHR' ? '0' : '0.00'; }); }

    const CATEGORY_STYLE = {
        'general':['bi-wallet2','#10b981'], 'emergency fund':['bi-shield-check','#ef4444'], 'food':['bi-cup-hot','#f97316'],
        'transport':['bi-bus-front','#3b82f6'], 'bills':['bi-receipt','#8b5cf6'], 'shopping':['bi-bag','#ec4899'],
        'health':['bi-heart-pulse','#e11d48'], 'travel':['bi-airplane','#0ea5e9'], 'buying plan':['bi-bag-heart','#14b8a6'], 'other':['bi-three-dots','#64748b'],
    };
    function categoryStyle(name) { return CATEGORY_STYLE[String(name || '').toLowerCase()] || CATEGORY_STYLE.other; }
    function renderRows(kind, id) {
        const items = state.entries.filter(item => item.kind === kind).sort((a,b) => b.date.localeCompare(a.date) || String(b.id).localeCompare(String(a.id))).slice(0, 40);
        if (!items.length) { $(id).innerHTML = emptyRow(4, `<i class="bi bi-inbox fs-4 d-block mb-2"></i>No entries yet. Add your first one with the form.`); return; }
        const monthTotals = {};
        items.forEach(item => { const key = item.date.slice(0,7); monthTotals[key] = (monthTotals[key] || 0) + entryUsd(item); });
        let lastMonth = '';
        $(id).innerHTML = items.map(item => {
            const d = new Date(`${item.date}T12:00:00`), monthKey = item.date.slice(0,7);
            let group = '';
            if (monthKey !== lastMonth) {
                lastMonth = monthKey;
                const label = monthKey === today.slice(0,7) ? 'This month' : d.toLocaleDateString(undefined, { month:'long', year:'numeric' });
                group = `<tr class="entry-group"><td colspan="4">${safe(label)}<span>${kind === 'saved' ? '+' : '−'}${money(monthTotals[monthKey])}</span></td></tr>`;
            }
            const [icon, color] = categoryStyle(item.category);
            const dayLabel = item.date === today ? 'Today' : d.toLocaleDateString(undefined, { month:'short' });
            return `${group}<tr class="row-${kind}"><td style="width:1%"><span class="entry-date" title="${safe(d.toLocaleDateString(undefined, { dateStyle:'full' }))}"><b>${d.getDate()}</b><small>${safe(dayLabel)}</small></span></td><td><div class="entry-main"><span class="entry-avatar" style="--cat-color:${color}"><i class="bi ${icon}"></i></span><div class="min-w-0"><span class="entry-category">${safe(item.category)}</span><small class="entry-note" title="${safe(item.note || '')}">${item.note ? `<i class="bi bi-sticky note-icon me-1"></i>${safe(item.note)}` : 'No note added'}</small></div></div></td><td class="text-end text-nowrap"><span class="entry-amount ${kind}" ${(item.currency || 'USD') !== state.currency ? `title="Entered as ${safe(money(entryUsd(item), item.currency || 'USD'))}"` : ''}>${kind === 'saved' ? '+' : '−'}${money(entryUsd(item))}</span></td><td class="text-end text-nowrap entry-actions" style="width:1%"><button class="btn btn-sm btn-outline-secondary me-1" data-edit-note="${safe(item.id)}" aria-label="Edit note" title="Edit note"><i class="bi bi-pencil-square"></i></button><button class="btn btn-sm btn-outline-danger" data-delete-entry="${safe(item.id)}" aria-label="Delete entry" title="Delete entry"><i class="bi bi-trash"></i></button></td></tr>`;
        }).join('');
    }
    function renderDaily() {
        const weekStart = dayOffset(-6);
        const savedToday = sum(entriesFor('saved', today, today)); const savedWeek = sum(entriesFor('saved', weekStart, today));
        const yesterday = dayOffset(-1), prevStart = dayOffset(-13), prevEnd = dayOffset(-7);
        const savedYesterday = sum(entriesFor('saved', yesterday, yesterday)), savedPrevWeek = sum(entriesFor('saved', prevStart, prevEnd));
        $('savedToday').textContent = money(savedToday); $('savedWeek').textContent = money(savedWeek); $('savedAverage').textContent = money(savedWeek / 7);
        setDelta('savedTodayDelta', savedToday, savedYesterday, true, 'Compared with yesterday');
        setDelta('savedWeekDelta', savedWeek, savedPrevWeek, true, 'Compared with the previous 7 days');
        $('savedTodayCaption').textContent = `vs ${money(savedYesterday)} yesterday`;
        $('savedWeekCaption').textContent = `vs ${money(savedPrevWeek)} before`;
        const savedDays = new Set(state.entries.filter(i => i.kind === 'saved').map(i => i.date));
        let streak = 0; for (let n = savedDays.has(today) ? 0 : -1; savedDays.has(dayOffset(n)); n--) streak++;
        const streakChip = $('savedStreak'); streakChip.className = `kpi-delta ${streak ? 'good' : ''}`; streakChip.title = 'Days in a row with a saving';
        streakChip.innerHTML = streak ? `<i class="bi bi-fire"></i>${streak} day${streak === 1 ? '' : 's'}` : 'No streak';
        const activeDays = [...Array(7)].filter((_, n) => savedDays.has(dayOffset(-n))).length;
        $('savedAverageCaption').textContent = `Saved on ${activeDays} of the last 7 days`;
        drawMiniBars('savedWeekBars', [...Array(7)].map((_, n) => sum(entriesFor('saved', dayOffset(n - 6), dayOffset(n - 6)))));

        const spentToday = sum(entriesFor('spent', today, today)), spentWeek = sum(entriesFor('spent', weekStart, today));
        const spentYesterday = sum(entriesFor('spent', yesterday, yesterday)), spentPrevWeek = sum(entriesFor('spent', prevStart, prevEnd));
        $('spentToday').textContent = money(spentToday); $('spentWeek').textContent = money(spentWeek);
        setDelta('spentTodayDelta', spentToday, spentYesterday, false, 'Compared with yesterday');
        setDelta('spentWeekDelta', spentWeek, spentPrevWeek, false, 'Compared with the previous 7 days');
        $('spentTodayCaption').textContent = `vs ${money(spentYesterday)} yesterday`;
        $('spentWeekCaption').textContent = `vs ${money(spentPrevWeek)} before · ${money(spentWeek / 7)}/day`;
        drawMiniBars('spentWeekBars', [...Array(7)].map((_, n) => sum(entriesFor('spent', dayOffset(n - 6), dayOffset(n - 6)))));
        renderRows('saved', 'savedEntries'); renderRows('spent', 'spentEntries');
    }
    document.addEventListener('click', event => {
        const editNote = event.target.closest('[data-edit-note]');
        if (editNote) {
            const item = state.entries.find(entry => entry.id === editNote.dataset.editNote); if (!item) return;
            editingNoteId = item.id; $('noteEditorText').value = item.note || '';
            bootstrap.Modal.getOrCreateInstance($('noteEditor')).show(); return;
        }
        const button = event.target.closest('[data-delete-entry]');
        if (button) { state.entries = state.entries.filter(item => item.id !== button.dataset.deleteEntry); persist(); return; }
        const add = event.target.closest('[data-add-to-plan]');
        if (add) {
            const plan = state.plans.find(item => item.id === add.dataset.addToPlan); if (!plan) return;
            const amount = Number(prompt(`How much (${state.currency}) would you like to add to “${plan.name}”?`));
            if (!Number.isFinite(amount) || amount <= 0) return;
            plan.saved = Number(plan.saved) + toUsd(amount);
            state.entries.push({ id: crypto.randomUUID ? crypto.randomUUID() : String(Date.now()), kind:'saved', amount, currency:state.currency, category:'Buying plan', date:today, note:plan.name, source:'plan', planId:plan.id }); persist();
        }
        const remove = event.target.closest('[data-delete-plan]');
        if (remove) { state.plans = state.plans.filter(item => item.id !== remove.dataset.deletePlan); persist(); }
    });
    $('saveEditedNote').addEventListener('click', () => {
        const item = state.entries.find(entry => entry.id === editingNoteId); if (!item) return;
        item.note = $('noteEditorText').value.trim();
        bootstrap.Modal.getOrCreateInstance($('noteEditor')).hide(); editingNoteId = null; persist();
    });

    let animateCharts = true;
    function monthKeyOffset(offset) { const d = new Date(); d.setDate(1); d.setMonth(d.getMonth() + offset); return localDate(d).slice(0,7); }
    function totalFor(kind, prefix) { return sum(state.entries.filter(i => i.kind === kind && i.date.startsWith(prefix))); }
    function setDelta(id, current, previous, higherIsGood, label) {
        const el = $(id); el.className = 'kpi-delta'; el.title = label;
        if (!previous && !current) { el.textContent = '—'; return; }
        if (!previous) { el.innerHTML = '<i class="bi bi-stars"></i>New'; el.classList.add(higherIsGood ? 'good' : 'bad'); return; }
        const change = (current - previous) / previous * 100, up = change >= 0, size = Math.abs(change);
        el.innerHTML = `<i class="bi bi-arrow-${up ? 'up' : 'down'}-right"></i>${size >= 999 ? '999+' : size.toFixed(0)}%`;
        if (Math.round(change) !== 0) el.classList.add(up === higherIsGood ? 'good' : 'bad');
    }
    function drawSpark(id, values) {
        const svg = $(id); if (!svg || !values.length) return;
        const w = 96, h = 36, pad = 4, max = Math.max(...values, 0), min = Math.min(...values, 0), range = max - min || 1;
        const pts = values.map((v, i) => [pad + (w - pad * 2) * i / Math.max(1, values.length - 1), h - pad - (v - min) / range * (h - pad * 2)]);
        const line = pts.map((p, i) => `${i ? 'L' : 'M'}${p[0].toFixed(1)},${p[1].toFixed(1)}`).join(''), end = pts[pts.length - 1], gid = `${id}Grad`;
        svg.setAttribute('viewBox', `0 0 ${w} ${h}`);
        svg.innerHTML = `<defs><linearGradient id="${gid}" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="var(--kpi-color)" stop-opacity=".3"/><stop offset="100%" stop-color="var(--kpi-color)" stop-opacity="0"/></linearGradient></defs><path d="${line}L${end[0]},${h}L${pts[0][0]},${h}Z" fill="url(#${gid})"/><path class="${animateCharts ? 'spark-anim' : ''}" d="${line}" fill="none" stroke="var(--kpi-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="${end[0]}" cy="${end[1]}" r="3.2" fill="var(--admin-surface)" stroke="var(--kpi-color)" stroke-width="2"/>`;
    }
    // Seven small bars (oldest → today); today's bar is solid, the rest faded.
    function drawMiniBars(id, values) {
        const svg = $(id); if (!svg) return;
        const w = 96, h = 36, gap = 4, barW = (w - gap * (values.length - 1)) / values.length, max = Math.max(...values, 0) || 1;
        svg.setAttribute('viewBox', `0 0 ${w} ${h}`);
        svg.innerHTML = values.map((v, i) => { const bh = Math.max(3, v / max * (h - 2)), last = i === values.length - 1; return `<rect class="${animateCharts ? 'bar-anim' : ''}" style="--i:${i}" x="${i * (barW + gap)}" y="${h - bh}" width="${barW}" height="${bh}" rx="3" fill="var(--kpi-color)" opacity="${v ? (last ? 1 : .45) : .15}"/>`; }).join('');
    }
    function renderTiles() {
        const setTile = (section, text, percent = null) => {
            const el = document.querySelector(`[data-tile-stat="${section}"]`); if (!el) return;
            el.innerHTML = `<span class="tile-stat-text">${text}</span>${percent === null ? '' : `<span class="tile-progress"><span style="width:${Math.min(100, percent)}%"></span></span>`}`;
        };
        const month = today.slice(0,7), year = today.slice(0,4);
        const yearNet = totalFor('saved', year) - totalFor('spent', year);
        setTile('overview', `<i class="bi bi-wallet2 me-1"></i>Net ${yearNet < 0 ? '−' : ''}${money(Math.abs(yearNet))} this year`);
        setTile('daily-saving', `<i class="bi bi-arrow-down-left me-1"></i>+${money(sum(entriesFor('saved', today, today)))} today`);
        setTile('daily-spending', `<i class="bi bi-arrow-up-right me-1"></i>−${money(sum(entriesFor('spent', today, today)))} today`);
        const monthGoal = Number(state.monthlyTargets[month] || 0), monthSaved = totalFor('saved', month);
        setTile('monthly', monthGoal ? `${Math.round(Math.min(100, monthSaved / monthGoal * 100))}% of goal` : 'No goal yet', monthGoal ? monthSaved / monthGoal * 100 : 0);
        const yearGoal = Number(state.yearlyTargets[year] || 0), yearSaved = totalFor('saved', year);
        setTile('yearly', yearGoal ? `${Math.round(Math.min(100, yearSaved / yearGoal * 100))}% of goal` : 'No goal yet', yearGoal ? yearSaved / yearGoal * 100 : 0);
        const price = state.plans.reduce((t, p) => t + (Number(p.price) || 0), 0), saved = state.plans.reduce((t, p) => t + Math.min(Number(p.saved) || 0, Number(p.price) || 0), 0);
        setTile('plans', state.plans.length ? `${state.plans.length} plan${state.plans.length === 1 ? '' : 's'}` : 'No plans yet', price ? saved / price * 100 : 0);
    }
    function renderOverview() {
        const month = today.slice(0,7), year = today.slice(0,4), prevMonth = monthKeyOffset(-1);
        const monthSaved = totalFor('saved', month), monthSpent = totalFor('spent', month);
        const yearSaved = totalFor('saved', year), yearSpent = totalFor('spent', year);
        const prevSaved = totalFor('saved', prevMonth), prevSpent = totalFor('spent', prevMonth);
        $('overviewMonth').textContent = money(monthSaved); $('overviewSpent').textContent = money(monthSpent); $('overviewYear').textContent = money(yearSaved);
        const net = yearSaved - yearSpent;
        $('overviewNet').textContent = money(net); $('overviewNet').style.color = net < 0 ? 'var(--badge-red-text)' : '';

        setDelta('overviewMonthDelta', monthSaved, prevSaved, true, 'Compared with last month');
        setDelta('overviewSpentDelta', monthSpent, prevSpent, false, 'Compared with last month');
        $('overviewMonthCaption').textContent = `vs ${money(prevSaved)} last month`;
        $('overviewSpentCaption').textContent = `vs ${money(prevSpent)} last month`;
        const lastSix = Array.from({length:6}, (_, n) => monthKeyOffset(n - 5));
        drawSpark('overviewMonthSpark', lastSix.map(key => totalFor('saved', key)));
        drawSpark('overviewSpentSpark', lastSix.map(key => totalFor('spent', key)));

        const lastYear = String(Number(year) - 1), lastYearToDate = sum(state.entries.filter(i => i.kind === 'saved' && i.date >= `${lastYear}-01-01` && i.date <= `${lastYear}${today.slice(4)}`));
        setDelta('overviewYearDelta', yearSaved, lastYearToDate, true, 'Compared with the same time last year');
        const yearTarget = Number(state.yearlyTargets[year] || 0), yearPct = yearTarget ? Math.min(100, yearSaved / yearTarget * 100) : 0;
        $('overviewYearCaption').textContent = yearTarget ? `${Math.round(yearPct)}% of ${money(yearTarget)} goal` : 'No yearly goal set yet';
        $('overviewYearMeter').style.width = `${yearPct}%`;
        let running = 0;
        drawSpark('overviewYearSpark', [0, ...Array.from({length:new Date().getMonth() + 1}, (_, n) => running += totalFor('saved', `${year}-${String(n + 1).padStart(2,'0')}`))]);

        const flow = yearSaved + yearSpent, rate = flow ? yearSaved / flow * 100 : 50, netChip = $('overviewNetDelta');
        $('overviewNetSaved').style.width = `${rate}%`; $('overviewNetSpent').style.width = `${100 - rate}%`;
        $('overviewNetCaption').textContent = flow ? `${Math.round(rate)}% saved · ${Math.round(100 - rate)}% spent` : 'Saved minus spent';
        netChip.className = `kpi-delta ${net > 0 ? 'good' : net < 0 ? 'bad' : ''}`; netChip.title = 'Saved minus spent this year';
        netChip.innerHTML = net > 0 ? '<i class="bi bi-check2-circle"></i>Surplus' : net < 0 ? '<i class="bi bi-exclamation-circle"></i>Deficit' : '—';
        renderChart();
    }
    function chartData() {
        if (chartPeriod === 12) return Array.from({length:12}, (_, n) => { const date = new Date(); date.setDate(1); date.setMonth(date.getMonth() - 11 + n); const key = localDate(date).slice(0,7); return {label:date.toLocaleDateString(undefined,{month:'short'}), key, saved:sum(state.entries.filter(i=>i.kind==='saved'&&i.date.startsWith(key))), spent:sum(state.entries.filter(i=>i.kind==='spent'&&i.date.startsWith(key))),isCurrent:n===11}; });
        return Array.from({length:chartPeriod}, (_, n) => { const key=dayOffset(n-chartPeriod+1); return {label:new Date(`${key}T12:00:00`).toLocaleDateString(undefined,{month:'short',day:'numeric'}),key,saved:sum(entriesFor('saved',key,key)),spent:sum(entriesFor('spent',key,key)),isCurrent:n===chartPeriod-1}; });
    }
    function niceCeil(value) { if (!(value > 0)) return 1; const exp = 10 ** Math.floor(Math.log10(value)), f = value / exp; return (f <= 1 ? 1 : f <= 2 ? 2 : f <= 2.5 ? 2.5 : f <= 5 ? 5 : 10) * exp; }
    function shortNumber(value) { const a = Math.abs(value); if (a >= 1e9) return `${+(value / 1e9).toFixed(1)}B`; if (a >= 1e6) return `${+(value / 1e6).toFixed(1)}M`; if (a >= 1e3) return `${+(value / 1e3).toFixed(1)}k`; return String(+value.toFixed(a < 100 ? 1 : 0)); }
    function topRoundedBar(x, y, w, h, r) { if (h <= 0) return ''; r = Math.min(r, w / 2, h); return `M${x},${y + h}V${y + r}Q${x},${y} ${x + r},${y}H${x + w - r}Q${x + w},${y} ${x + w},${y + r}V${y + h}Z`; }
    function periodTitle(d) {
        if (d.key?.length === 10) return new Date(`${d.key}T12:00:00`).toLocaleDateString(undefined, { weekday:'short', month:'short', day:'numeric', year:'numeric' });
        if (d.key?.length === 7) { const [y, m] = d.key.split('-').map(Number); return new Date(y, m - 1, 1).toLocaleDateString(undefined, { month:'long', year:'numeric' }); }
        return d.label;
    }
    function chartTooltipFor(svg) {
        let tip = svg.parentElement.querySelector('.chart-tooltip');
        if (!tip) { tip = document.createElement('div'); tip.className = 'chart-tooltip'; svg.parentElement.appendChild(tip); }
        return tip;
    }
    function bindChartHover(svg) {
        if (svg.dataset.hoverBound) return; svg.dataset.hoverBound = '1';
        const tip = chartTooltipFor(svg);
        const hide = () => { tip.style.display = 'none'; svg.querySelector('.chart-hover-band')?.classList.remove('on'); };
        const row = (color, label, value) => `<div class="tt-row"><span><i style="background:${color}"></i>${label}</span><b>${value}</b></div>`;
        svg.addEventListener('pointermove', event => {
            const hit = event.target.closest?.('.chart-hit'); const c = svg._chart;
            if (!hit || !c) { hide(); return; }
            const i = Number(hit.dataset.i), d = c.data[i], band = svg.querySelector('.chart-hover-band');
            band.setAttribute('x', c.left + c.slot * i + c.slot * .08); band.setAttribute('width', c.slot * .84); band.classList.add('on');
            let body;
            if (c.goalChart) {
                body = row(c.savedColor, 'Saved', safe(money(d.saved)));
                if (c.target > 0) body += row('#64748b', c.monthly ? 'Daily target' : 'Monthly pace', safe(money(c.target))) + `<div class="tt-foot">${d.saved >= c.target ? '<i class="bi bi-check-circle-fill text-success me-1"></i>Target reached' : `${safe(money(c.target - d.saved))} below target`}</div>`;
            } else {
                const net = d.saved - d.spent;
                body = row('#3b82f6', 'Saved', safe(money(d.saved))) + row('#f59e0b', 'Spent', safe(money(d.spent))) + `<div class="tt-foot tt-row"><span>Net</span><b style="color:${net < 0 ? '#fca5a5' : '#86efac'}">${net < 0 ? '−' : '+'}${safe(money(Math.abs(net)))}</b></div>`;
            }
            tip.innerHTML = `<div class="tt-title">${safe(periodTitle(d))}</div>${body}`;
            tip.style.display = 'block';
            const r = svg.parentElement.getBoundingClientRect(), tw = tip.offsetWidth, th = tip.offsetHeight;
            let x = event.clientX - r.left + 14; if (x + tw > r.width) x = event.clientX - r.left - tw - 14;
            tip.style.left = `${Math.max(0, x)}px`; tip.style.top = `${Math.max(0, event.clientY - r.top - th - 12)}px`;
        });
        svg.addEventListener('pointerleave', hide);
    }
    function drawBars(svgId, data, compact=false) {
        const svg = $(svgId); if (!svg || !data.length) return;
        const width = Math.max(svg.parentElement.clientWidth, 320), height = compact ? 230 : 270, left = 46, right = 10, top = 20, bottom = compact ? 30 : 34, plotH = height - top - bottom, plotW = width - left - right;
        const monthly = svgId === 'monthChart', goalChart = svgId !== 'overviewChart', target = goalChart ? Number(data[0]?.target || 0) : 0;
        const max = niceCeil(fromUsd(Math.max(...data.flatMap(d => [d.saved, d.spent]), target)) * 1.1), slot = plotW / data.length; // axis in the chosen currency
        const barW = goalChart ? Math.max(4, Math.min(24, slot * .6)) : Math.max(3, Math.min(28, slot * .32)), gap = goalChart ? 0 : Math.max(1, Math.min(4, slot * .06));
        const yDisplay = value => top + plotH - value / max * plotH, yOf = usd => yDisplay(fromUsd(usd)), hOf = usd => fromUsd(usd) / max * plotH, radius = Math.min(6, barW / 2), anim = animateCharts ? 'bar-anim' : '';
        const saved = monthly ? ['#34d399', '#059669'] : ['#60a5fa', '#2563eb'];
        let html = `<defs><linearGradient id="${svgId}Saved" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="${saved[0]}"/><stop offset="100%" stop-color="${saved[1]}"/></linearGradient><linearGradient id="${svgId}Spent" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#fcd34d"/><stop offset="100%" stop-color="#f97316"/></linearGradient><filter id="${svgId}Glow" x="-50%" y="-20%" width="200%" height="140%"><feDropShadow dx="0" dy="3" stdDeviation="3" flood-color="${saved[1]}" flood-opacity=".35"/></filter></defs>`;
        for (let t = 0; t <= 4; t++) {
            const v = max * t / 4, y = yDisplay(v);
            html += `<line x1="${left}" y1="${y}" x2="${width - right}" y2="${y}" stroke="var(--admin-border)" ${t ? 'stroke-dasharray="2 5"' : 'stroke-width="1.2"'}/><text x="${left - 8}" y="${y + 3.5}" text-anchor="end" fill="var(--admin-text-muted)" font-size="10">${shortNumber(v)}</text>`;
        }
        html += `<rect class="chart-hover-band" x="0" y="${top - 6}" width="0" height="${plotH + 6}" rx="8"/>`;
        const labelEvery = data.length <= 12 ? 1 : Math.ceil(data.length / 8);
        data.forEach((d, i) => {
            const cx = left + slot * i + slot / 2, delay = `style="--i:${i}"`;
            if (goalChart) {
                const hit = target > 0 && d.saved >= target, h = hOf(d.saved);
                html += `<rect x="${cx - barW / 2}" y="${top}" width="${barW}" height="${plotH}" rx="${radius}" fill="var(--admin-border)" opacity=".2"/>`;
                if (h > 0) html += `<path class="${anim}" ${delay} d="${topRoundedBar(cx - barW / 2, yOf(d.saved), barW, h, radius)}" fill="url(#${svgId}Saved)" opacity="${target > 0 && !hit ? .55 : 1}" ${hit ? `filter="url(#${svgId}Glow)"` : ''}/>`;
                if (hit && barW >= 6) html += `<circle class="${anim}" ${delay} cx="${cx}" cy="${yOf(d.saved) - 7}" r="2.6" fill="#f59e0b"/>`;
            } else {
                const h1 = hOf(d.saved), h2 = hOf(d.spent);
                if (h1 > 0) html += `<path class="${anim}" ${delay} d="${topRoundedBar(cx - gap / 2 - barW, yOf(d.saved), barW, h1, radius)}" fill="url(#${svgId}Saved)"/>`;
                if (h2 > 0) html += `<path class="${anim}" ${delay} d="${topRoundedBar(cx + gap / 2, yOf(d.spent), barW, h2, radius)}" fill="url(#${svgId}Spent)"/>`;
            }
            if (d.isCurrent) html += `<circle cx="${cx}" cy="${top + plotH + 6}" r="2.5" fill="#0ea5e9"/>`;
            if (i % labelEvery === 0) html += `<text x="${cx}" y="${height - 8}" text-anchor="middle" fill="${d.isCurrent ? '#0ea5e9' : 'var(--admin-text-muted)'}" font-size="10" ${d.isCurrent ? 'font-weight="700"' : ''}>${safe(d.label)}</text>`;
        });
        const refLine = (value, color, text) => { const y = yOf(value); return `<line x1="${left}" y1="${y}" x2="${width - right}" y2="${y}" stroke="${color}" stroke-width="1.5" stroke-dasharray="6 5" opacity=".9"/><text x="${width - right}" y="${y - 6}" text-anchor="end" font-size="10" font-weight="700" fill="${color}" paint-order="stroke" stroke="var(--admin-surface)" stroke-width="3">${safe(text)}</text>`; };
        if (goalChart && target > 0) html += refLine(target, '#64748b', `${monthly ? 'Daily target' : 'Pace'} ${shortNumber(fromUsd(target))}`);
        if (!goalChart) { const avg = sum(data.map(d => ({ amount:d.saved }))) / data.length; if (avg > 0) html += refLine(avg, '#10b981', `Avg ${shortNumber(fromUsd(avg))}`); }
        data.forEach((d, i) => { html += `<rect class="chart-hit" data-i="${i}" x="${left + slot * i}" y="${top - 6}" width="${slot}" height="${plotH + bottom + 6}" fill="transparent"/>`; });
        svg.setAttribute('viewBox', `0 0 ${width} ${height}`); svg.innerHTML = html;
        svg._chart = { data, target, goalChart, monthly, slot, left, savedColor:saved[1] };
        bindChartHover(svg);
    }
    function renderChart() {
        if (!$('overviewChart')) return; const data = chartData(); drawBars('overviewChart', data);
        const totalSaved = sum(data.map(d => ({ amount:d.saved }))), totalSpent = sum(data.map(d => ({ amount:d.spent })));
        $('chartSumSaved').textContent = `Saved ${money(totalSaved)}`; $('chartSumSpent').textContent = `Spent ${money(totalSpent)}`;
        $('chartTable').innerHTML = data.slice().reverse().map(d => {
            const net = d.saved - d.spent, flow = d.saved + d.spent, share = flow ? d.saved / flow * 100 : 0;
            const split = flow ? `<div class="split-bar" title="${Math.round(share)}% saved · ${Math.round(100 - share)}% spent">${d.saved ? `<span style="width:${share}%;background:#3b82f6"></span>` : ''}${d.spent ? `<span style="width:${100 - share}%;background:#f59e0b"></span>` : ''}</div>` : '<span class="small muted">No activity</span>';
            return `<tr><td class="fw-semibold">${safe(d.label)}${d.isCurrent ? '<span class="now-chip">Now</span>' : ''}</td><td class="text-end">${money(d.saved)}</td><td class="text-end">${money(d.spent)}</td><td class="text-end"><span class="net-pill ${net > 0 ? 'pos' : net < 0 ? 'neg' : 'zero'}">${net > 0 ? '+' : net < 0 ? '−' : ''}${money(Math.abs(net))}</span></td><td>${split}</td></tr>`;
        }).join('');
    }
    $('monthPicker').value=today.slice(0,7); $('yearPicker').value=new Date().getFullYear();
    function renderMonthly() {
        const key=$('monthPicker').value || today.slice(0,7); $('monthTarget').value=state.monthlyTargets[key]!=null?inputValue(state.monthlyTargets[key]):'';
        const [y,m]=key.split('-').map(Number), last=new Date(y,m,0).getDate();
        const saved=sum(state.entries.filter(i=>i.kind==='saved'&&i.date.startsWith(key))), target=Number(state.monthlyTargets[key]||0), left=Math.max(0,target-saved), currentMonth=today.slice(0,7), daysRemaining=key<currentMonth?0:key===currentMonth?Math.max(1,last-new Date().getDate()+1):last, daysLeft=Math.max(1,last-(key===currentMonth?new Date().getDate():0));
        $('monthChartTotal').textContent=money(saved);
        const percent=target?Math.min(100,saved/target*100):0;
        $('monthKpiTarget').textContent=money(target); $('monthKpiSaved').textContent=money(saved); $('monthKpiRemaining').textContent=money(left); $('monthKpiPercent').textContent=`${Math.round(percent)}%`;
        $('monthProgressText').textContent=`${money(saved)} of ${money(target)}`; $('monthPercent').textContent=`${Math.round(percent)}%`; $('monthProgress').style.width=`${percent}%`; $('monthLeft').textContent=money(left); $('monthPerDay').textContent=`${money(left/daysLeft)} / day`;
        $('monthBottomProgress').textContent=`${money(saved)} saved of ${money(target)}`; $('monthBottomPercent').textContent=`${Math.round(percent)}%`; $('monthBottomBar').style.width=`${percent}%`; $('monthBottomLeft').textContent=money(left);
        const dailyPace=daysRemaining?left/daysRemaining:0,weeklyPace=Math.min(left,dailyPace*7);
        $('monthCoachDaily').textContent=money(dailyPace);$('monthCoachWeekly').textContent=money(weeklyPace);
        $('monthCoachMessage').textContent=!target?'Set a monthly goal to get a personalized saving pace.':left<=0?'Goal reached! You have saved enough for this month.':!daysRemaining?'This goal period has ended. Choose the current or a future month to see a saving pace.':`Save about ${money(dailyPace)} each day (${money(weeklyPace)} this week) to reach your goal.`;
        const data=Array.from({length:last},(_,n)=>{const date=`${key}-${String(n+1).padStart(2,'0')}`;return{label:String(n+1),key:date,saved:sum(entriesFor('saved',date,date)),spent:0,target:target/last,isCurrent:date===today};});
        drawBars('monthChart',data,true);
        const weeks=[]; for(let start=1;start<=last;start+=7){const end=Math.min(start+6,last),from=`${key}-${String(start).padStart(2,'0')}`,through=`${key}-${String(end).padStart(2,'0')}`;weeks.push({number:weeks.length+1,start,end,total:sum(entriesFor('saved',from,through))});}
        const bestWeek=Math.max(0,...weeks.map(week=>week.total));
        const currentWeek=key===today.slice(0,7)?Math.ceil(new Date().getDate()/7):0;
        $('weekTable').innerHTML=weeks.map(week=>{const isBest=bestWeek>0&&week.total===bestWeek,isCurrent=week.number===currentWeek;return `<tr class="${isBest?'is-best-week':''}"><td><span class="week-label">${isBest?'<i class="bi bi-trophy-fill week-best-icon"></i>':''}Week ${week.number}${isCurrent?'<span class="now-chip">Now</span>':''}</span></td><td class="muted small">${week.start}–${week.end}</td><td class="text-end"><div class="fw-semibold">${money(week.total)}</div><div class="rel-bar mt-1 ms-auto" style="max-width:110px"><span style="width:${bestWeek?week.total/bestWeek*100:0}%"></span></div></td></tr>`;}).join('');
    }
    $('monthPicker').addEventListener('change',renderMonthly);
    $('saveMonthTarget').addEventListener('click',()=>{const key=$('monthPicker').value,target=Number($('monthTarget').value);if(!key||target<0)return;state.monthlyTargets[key]=toUsd(target);persist();});
    function renderYearly() {
        const year=String($('yearPicker').value||new Date().getFullYear()); $('yearTarget').value=state.yearlyTargets[year]!=null?inputValue(state.yearlyTargets[year]):'';
        const target=Number(state.yearlyTargets[year]||0), monthlyPace=target/12;
        const data=Array.from({length:12},(_,n)=>{const key=`${year}-${String(n+1).padStart(2,'0')}`;return{label:new Date(Number(year),n,1).toLocaleDateString(undefined,{month:'short'}),key,saved:sum(state.entries.filter(i=>i.kind==='saved'&&i.date.startsWith(key))),spent:0,target:monthlyPace,isCurrent:key===today.slice(0,7)};});
        const total=sum(data.map(d=>d.saved)), now=new Date(), selectedYear=Number(year), elapsed=Math.max(1,selectedYear===now.getFullYear()?now.getMonth()+1:(selectedYear<now.getFullYear()?12:1)), monthsRemaining=selectedYear>now.getFullYear()?12:selectedYear===now.getFullYear()?12-now.getMonth():0, average=total/elapsed, projection=selectedYear>now.getFullYear()?0:average*12, percent=target?Math.min(100,total/target*100):0, remaining=Math.max(0,target-total), needed=monthsRemaining?remaining/monthsRemaining:0;
        $('yearKpiTarget').textContent=money(target); $('yearKpiSaved').textContent=money(total); $('yearKpiRemaining').textContent=money(remaining); $('yearKpiPercent').textContent=`${Math.round(percent)}%`;
        $('yearProgressText').textContent=`${money(total)} saved of ${money(target)}`; $('yearPercent').textContent=`${Math.round(percent)}%`; $('yearProgress').style.width=`${percent}%`; $('yearBottomProgress').style.width=`${percent}%`; $('yearProjection').textContent=money(projection); $('yearLeft').textContent=money(remaining); $('yearChartTotal').textContent=money(total);
        $('yearCoachNeeded').textContent=target?(remaining<=0?money(0):monthsRemaining?money(needed):'—'):'—';$('yearCoachAverage').textContent=money(average);
        $('yearCoachStatus').textContent=!target?'Set a yearly goal to get your saving plan.':remaining<=0?'Yearly goal reached. Great work!':!monthsRemaining?'The year has ended. Review your total and set a new target for the next year.':projection>=target?`On pace. Keep saving about ${money(needed)} per month to finish strong.`:`To reach your target, aim for about ${money(needed)} per month from now on.`;
        drawBars('yearChart',data,true);
        const bestMonth=Math.max(0,...data.map(month=>month.saved));
        $('yearTable').innerHTML=data.map((month,index)=>{
            const isBest=bestMonth>0&&month.saved===bestMonth, isCurrent=selectedYear===now.getFullYear()&&index===now.getMonth();
            const paceLabel=month.saved>=monthlyPace&&monthlyPace>0?'On pace':'Building';
            return `<tr class="${isBest?'is-best-week':''}"><td><span class="week-label">${isBest?'<i class="bi bi-trophy-fill week-best-icon"></i>':''}${safe(month.label)}${isCurrent?'<span class="now-chip">Now</span>':''}</span></td><td class="text-end"><div class="fw-semibold">${money(month.saved)}</div><div class="rel-bar blue mt-1 ms-auto" style="max-width:110px"><span style="width:${bestMonth?month.saved/bestMonth*100:0}%"></span></div></td><td class="text-end"><span class="badge rounded-pill" style="background:${paceLabel==='On pace'?'var(--badge-green-bg)':'var(--badge-gray-bg)'};color:${paceLabel==='On pace'?'var(--badge-green-text)':'var(--badge-gray-text)'}">${paceLabel}</span></td></tr>`;
        }).join('');
    }
    $('yearPicker').addEventListener('change',renderYearly);
    $('saveYearTarget').addEventListener('click',()=>{const year=String($('yearPicker').value),target=Number($('yearTarget').value);if(!year||target<0)return;state.yearlyTargets[year]=toUsd(target);persist();});

    // Buying plan form: price / saved are typed in the currency picked on its own $ / ៛ switch.
    setFormCurrency($('planForm'), state.currency || 'USD');
    $('planForm').querySelectorAll('input[name="currency"]').forEach(radio => radio.addEventListener('change', () => setFormCurrency($('planForm'), radio.value, true)));
    $('planForm').addEventListener('submit',event=>{
        event.preventDefault();
        const form=event.currentTarget, data=new FormData(form), currency=data.get('currency')==='KHR'?'KHR':'USD';
        state.plans.push({id:crypto.randomUUID?crypto.randomUUID():String(Date.now()),name:data.get('name').trim(),type:data.get('type'),price:toUsd(data.get('price'),currency),saved:toUsd(data.get('saved'),currency),deadline:data.get('deadline')});
        form.reset(); setFormCurrency(form, currency); persist();
    });
    function renderPlans() {
        const host=$('plansList');
        const total=state.plans.reduce((sum,plan)=>sum+(Number(plan.price)||0),0),saved=state.plans.reduce((sum,plan)=>sum+(Number(plan.saved)||0),0),left=state.plans.reduce((sum,plan)=>sum+Math.max(0,(Number(plan.price)||0)-(Number(plan.saved)||0)),0);
        $('planTotalTarget').textContent=money(total);$('planTotalSaved').textContent=money(saved);$('planTotalLeft').textContent=money(left);
        const planInfo=state.plans.map(plan=>{const remaining=Math.max(0,(Number(plan.price)||0)-(Number(plan.saved)||0)),days=Math.max(0,Math.ceil((new Date(`${plan.deadline}T12:00:00`)-new Date(`${today}T12:00:00`))/86400000));return{plan,remaining,days,monthly:remaining/Math.max(1,Math.ceil(days/30.4375))};});
        const active=planInfo.filter(p=>p.remaining>0), done=planInfo.length-active.length, savedPct=total?Math.min(100,saved/total*100):0, next=active.slice().sort((a,b)=>a.days-b.days)[0];
        $('planActiveCount').textContent=active.length;
        $('planCountChip').className='kpi-delta';$('planCountChip').innerHTML=`<i class="bi bi-list-check"></i>${state.plans.length} plan${state.plans.length===1?'':'s'}`;
        $('planTotalCaption').textContent=state.plans.length?`Average ${money(total/state.plans.length)} per plan`:'Create your first plan';
        $('planSavedChip').className=`kpi-delta ${savedPct>0?'good':''}`;$('planSavedChip').textContent=`${Math.round(savedPct)}%`;
        $('planSavedCaption').textContent=total?`${Math.round(savedPct)}% of everything you planned`:'Nothing saved yet';$('planSavedMeter').style.width=`${savedPct}%`;
        const monthlyNeed=active.reduce((t,p)=>t+p.monthly,0);
        $('planLeftChip').className='kpi-delta';$('planLeftChip').innerHTML=`<i class="bi bi-hourglass-split"></i>${active.length} left`;
        $('planLeftCaption').textContent=active.length?`≈ ${money(monthlyNeed)} / month to stay on pace`:'Every plan is fully funded';
        $('planNextChip').className=`kpi-delta ${next&&next.days<=30?'bad':''}`;$('planNextChip').title='Days until the next deadline';$('planNextChip').innerHTML=next?`<i class="bi bi-alarm"></i>${next.days}d`:'—';
        $('planActiveCaption').textContent=next?`Next: ${next.plan.name} · ${done} completed`:`${done} completed`;
        if(!state.plans.length){host.innerHTML='<div class="col-12"><div class="save-card empty py-5"><span class="plans-heading-icon mx-auto mb-3"><i class="bi bi-bag-heart"></i></span><h4 class="h6 fw-bold">Your first goal starts here</h4><div class="small muted">Create a buying plan and track each step toward it.</div></div></div>';return;}
        const icons={car:'bi-car-front',motorcycle:'bi-bicycle',bike:'bi-bicycle',clothes:'bi-bag',trip:'bi-airplane',other:'bi-stars'};
        host.innerHTML=state.plans.map(plan=>{const price=Math.max(0,Number(plan.price)||0),amount=Math.max(0,Number(plan.saved)||0),remaining=Math.max(0,price-amount),percent=price?Math.min(100,amount/price*100):0,deadlineDate=new Date(`${plan.deadline}T12:00:00`),days=Math.max(0,Math.ceil((deadlineDate-new Date(`${today}T12:00:00`))/86400000)),months=Math.max(1,Math.ceil(days/30.4375)),monthly=remaining/months,complete=remaining<=0,icon=icons[String(plan.type).toLowerCase()]||icons.other;return `<div class="col-12"><article class="plan-card"><div class="d-flex justify-content-between align-items-start gap-3"><div class="d-flex align-items-center gap-3 min-w-0"><span class="plan-card-icon"><i class="bi ${icon}"></i></span><div class="min-w-0"><span class="badge rounded-pill text-bg-light mb-1">${safe(plan.type)}</span><h4 class="h6 fw-bold mb-1 text-break">${safe(plan.name)}</h4><small class="muted"><i class="bi bi-calendar3 me-1"></i>${complete?'Goal completed':`Due ${safe(plan.deadline)}`}</small></div></div><button class="btn btn-sm btn-outline-secondary rounded-circle" data-delete-plan="${safe(plan.id)}" aria-label="Delete ${safe(plan.name)}" title="Delete plan"><i class="bi bi-trash"></i></button></div><div class="d-flex justify-content-between align-items-center small mt-4 mb-2"><span class="fw-semibold">Goal progress</span><strong class="${complete?'text-success':''}">${percent.toFixed(0)}%</strong></div><div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="${percent.toFixed(0)}" aria-valuemin="0" aria-valuemax="100" style="width:${percent}%"></div></div><div class="row g-2 mt-2"><div class="col-4"><div class="plan-detail"><div class="plan-detail-label">Saved</div><div class="plan-detail-value text-success">${money(amount)}</div></div></div><div class="col-4"><div class="plan-detail"><div class="plan-detail-label">Goal price</div><div class="plan-detail-value">${money(price)}</div></div></div><div class="col-4"><div class="plan-detail"><div class="plan-detail-label">Remaining</div><div class="plan-detail-value">${money(remaining)}</div></div></div></div><div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3"><small class="muted"><i class="bi bi-speedometer2 me-1"></i>${complete?'Ready to enjoy your goal':`${money(monthly)} / month to stay on pace`}</small>${complete?'':'<button class="btn btn-sm btn-success rounded-pill px-3" data-add-to-plan="'+safe(plan.id)+'"><i class="bi bi-plus-lg me-1"></i>Add money</button>'}</div></article></div>`;}).join('');
    }
    function render(){ renderCurrencyLabels(); renderTiles(); renderOverview();renderDaily();renderMonthly();renderYearly();renderPlans(); }
    document.querySelectorAll('.period-btn').forEach(b=>b.classList.toggle('active',Number(b.dataset.period)===chartPeriod));
    render();
    if (DATABASE_STATE && !needsLegacySync && !needsCurrencySync && !needsAmountSync) {
        localStorage.removeItem(LEGACY_KEY);
        $('trackerSaveStatus').innerHTML = '<i class="bi bi-cloud-check me-1"></i>Loaded from your account';
    } else if (legacyState || needsCurrencySync || needsAmountSync) {
        $('trackerSaveStatus').innerHTML = '<i class="bi bi-cloud-arrow-up me-1"></i>Moving existing data to your account…';
        queueDatabaseSave();
    }
    let resizeTimer=null;
    window.addEventListener('resize',()=>{clearTimeout(resizeTimer);resizeTimer=setTimeout(()=>{animateCharts=false;renderChart();renderMonthly();renderYearly();animateCharts=true;},120);});
})();
</script>
@endpush
