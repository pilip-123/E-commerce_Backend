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
    .saving-app .save-card, .saving-app .save-tile { background:var(--admin-surface); border:1px solid var(--admin-border); border-radius:18px; color:var(--admin-text); }
    .saving-app .kpi-card { position:relative; overflow:hidden; min-height:150px; padding:20px; border:1px solid var(--admin-border); border-radius:20px; background:var(--admin-surface); box-shadow:0 8px 24px rgba(15,23,42,.045); transition:transform .18s ease,box-shadow .18s ease; }
    .saving-app .kpi-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(15,23,42,.09); }
    .saving-app .kpi-card:after { content:""; position:absolute; width:110px; height:110px; right:-42px; bottom:-48px; border-radius:50%; background:var(--kpi-soft); opacity:.75; }
    .saving-app .kpi-head { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:18px; }
    .saving-app .kpi-label { color:var(--admin-text-muted); font-size:.82rem; font-weight:650; letter-spacing:.01em; }
    .saving-app .kpi-icon { width:40px; height:40px; display:grid; place-items:center; flex:none; border-radius:13px; color:var(--kpi-color); background:var(--kpi-soft); font-size:18px; }
    .saving-app .kpi-value { position:relative; z-index:1; overflow-wrap:anywhere; font-size:clamp(1.25rem,2.2vw,1.85rem); font-weight:780; line-height:1.15; letter-spacing:-.045em; }
    .saving-app .kpi-caption { position:relative; z-index:1; margin-top:7px; color:var(--admin-text-muted); font-size:.75rem; }
    .saving-app .kpi-saved { --kpi-color:#167d55; --kpi-soft:#d9f5e8; }
    .saving-app .kpi-spent { --kpi-color:#d97706; --kpi-soft:#fff0d2; }
    .saving-app .kpi-year { --kpi-color:#3978f6; --kpi-soft:#e1ecff; }
    .saving-app .kpi-net { --kpi-color:#7c3aed; --kpi-soft:#eee5ff; }
    [data-theme="dark"] .saving-app .kpi-saved { --kpi-color:#6ee7b7; --kpi-soft:#064e3b; }
    [data-theme="dark"] .saving-app .kpi-spent { --kpi-color:#fbbf24; --kpi-soft:#713f12; }
    [data-theme="dark"] .saving-app .kpi-year { --kpi-color:#93c5fd; --kpi-soft:#1e3a8a; }
    [data-theme="dark"] .saving-app .kpi-net { --kpi-color:#c4b5fd; --kpi-soft:#4c1d95; }
    .saving-app .save-tile { text-align:left; padding:16px; height:100%; transition:.18s ease; box-shadow:0 3px 12px rgba(15,23,42,.025); }
    .saving-app .save-tile:hover, .saving-app .save-tile.active { border-color:var(--admin-primary); box-shadow:0 8px 24px rgba(5,150,105,.1); transform:translateY(-1px); }
    .saving-app .save-tile.active { background:var(--admin-primary-light); box-shadow:inset 0 0 0 1px var(--admin-primary); }
    .saving-app .tile-icon { display:grid; place-items:center; width:38px; height:38px; border-radius:12px; color:var(--admin-primary); background:var(--admin-primary-light); font-size:18px; margin-bottom:12px; }
    .saving-app .metric { font-size:clamp(1.35rem,3vw,2rem); font-weight:750; letter-spacing:-.04em; }
    .saving-app .muted { color:var(--admin-text-muted); }
    .saving-app .section-panel { display:none; }
    .saving-app .section-panel.active { display:block; }
    .saving-app .form-control, .saving-app .form-select { background-color:var(--admin-surface); color:var(--admin-text); border-color:var(--admin-border); }
    .saving-app .form-control::placeholder { color:var(--admin-text-muted); }
    .saving-app .table { --bs-table-bg:transparent; --bs-table-color:var(--admin-text); --bs-table-border-color:var(--admin-border); }
    .saving-app .table thead th { color:var(--admin-text-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.06em; }
    .saving-app .chart-wrap { position:relative; min-height:250px; }
    .saving-app .chart-wrap svg { width:100%; height:260px; overflow:visible; }
    .saving-app .chart-tooltip { position:absolute; display:none; z-index:3; pointer-events:none; padding:9px 12px; border-radius:10px; background:#111827; color:white; font-size:12px; box-shadow:0 8px 24px #0003; }
    .saving-app .progress { height:10px; background:var(--admin-border); }
    .saving-app .legend-dot { display:inline-block; width:9px; height:9px; border-radius:50%; margin-right:6px; }
    .saving-app .entry-amount.saved { color:#2563eb; }
    .saving-app .entry-amount.spent { color:#d97706; }
    .saving-app .empty { color:var(--admin-text-muted); text-align:center; padding:28px 12px; }
    .saving-app .save-card { box-shadow:0 5px 20px rgba(15,23,42,.035); }
    .saving-app .entry-form .form-control, .saving-app .entry-form .form-select { min-height:44px; border-radius:11px; }
    .saving-app .entry-form textarea.form-control { min-height:84px; }
    .saving-app .note-preview { display:block; max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .saving-app .note-icon { color:var(--admin-primary); }
    .saving-app #journalEditor:empty:before { content:attr(data-placeholder); color:var(--admin-text-muted); }
    .saving-app .journal-item { border:1px solid var(--admin-border); background:var(--card-muted-bg); border-radius:13px; padding:12px; color:var(--admin-text); }
    .saving-app .journal-item:hover { border-color:var(--admin-primary); }
    .saving-app .journal-preview { max-height:2.8em; overflow:hidden; overflow-wrap:anywhere; line-height:1.4; }
    .saving-app .journal-preview p, .saving-app .journal-preview div, .saving-app .journal-preview blockquote { display:inline; margin:0; }
    .saving-app .table > :not(caption) > * > * { padding-top:.85rem; padding-bottom:.85rem; }
    .saving-app .table-toggle[aria-pressed="true"] { background:var(--admin-primary); color:#fff; }
    @media(max-width:575px) { .saving-app .save-tile { padding:12px; } .saving-app .save-tile small { font-size:.7rem; } .saving-app .chart-wrap svg { height:220px; } }
</style>
@endpush

@section('content')
<div class="container-fluid p-0 saving-app" id="savingApp">
    <div class="saving-hero mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-4">
            <div><div class="text-uppercase small fw-bold mb-2" style="opacity:.78;letter-spacing:.13em">PERSONAL FINANCE</div><h1 class="display-6 fw-bold mb-2">Make every dollar count.</h1><div style="opacity:.82">Track what you save, understand what you spend, and make your next goal feel closer.</div>
                <div class="d-flex flex-wrap gap-2 mt-4"><button type="button" class="hero-action" data-open-section="daily-saving"><i class="bi bi-plus-circle me-1"></i>Add saving</button><button type="button" class="hero-action" data-open-section="daily-spending"><i class="bi bi-receipt me-1"></i>Add spending</button><button type="button" class="hero-action" data-bs-toggle="modal" data-bs-target="#notesModal"><i class="bi bi-journal-text me-1"></i>Notes</button></div>
            </div>
            <div class="d-flex flex-column align-items-end gap-2"><div class="d-flex align-items-center gap-2"><label for="currencySelect" class="small" style="opacity:.85">Display currency</label><select id="currencySelect" class="form-select form-select-sm" style="width:auto;background-color:#ffffffed">
                @foreach($currencies as $code => $label)<option value="{{ $code }}">{{ $code }} · {{ $label }}</option>@endforeach
            </select></div><small id="trackerSaveStatus" role="status" aria-live="polite" style="opacity:.82"><i class="bi bi-cloud-check me-1"></i>Account data</small></div>
        </div>
    </div>

    <div class="row g-3 mb-4" id="tileMenu">
        <div class="col-6 col-lg-4"><button class="save-tile active w-100" data-section="overview"><span class="tile-icon"><i class="bi bi-grid-1x2"></i></span><strong>Overview</strong><br><small class="muted">Your money at a glance</small></button></div>
        <div class="col-6 col-lg-4"><button class="save-tile w-100" data-section="daily-saving"><span class="tile-icon"><i class="bi bi-piggy-bank"></i></span><strong>Daily saving</strong><br><small class="muted">Record money saved</small></button></div>
        <div class="col-6 col-lg-4"><button class="save-tile w-100" data-section="daily-spending"><span class="tile-icon"><i class="bi bi-credit-card"></i></span><strong>Daily spending</strong><br><small class="muted">Track money spent</small></button></div>
        <div class="col-6 col-lg-4"><button class="save-tile w-100" data-section="monthly"><span class="tile-icon"><i class="bi bi-calendar-month"></i></span><strong>Monthly saving</strong><br><small class="muted">Set a monthly target</small></button></div>
        <div class="col-6 col-lg-4"><button class="save-tile w-100" data-section="yearly"><span class="tile-icon"><i class="bi bi-calendar-check"></i></span><strong>Yearly saving</strong><br><small class="muted">Plan your whole year</small></button></div>
        <div class="col-6 col-lg-4"><button class="save-tile w-100" data-section="plans"><span class="tile-icon"><i class="bi bi-bullseye"></i></span><strong>Buying plans</strong><br><small class="muted">Save toward a purchase</small></button></div>
    </div>

    <section class="section-panel active" id="panel-overview">
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3"><div class="kpi-card kpi-saved h-100"><div class="kpi-head"><span class="kpi-label">Saved this month</span><span class="kpi-icon"><i class="bi bi-piggy-bank"></i></span></div><div class="kpi-value" id="overviewMonth">—</div><div class="kpi-caption"><i class="bi bi-arrow-down-left me-1"></i>Money added to savings</div></div></div>
            <div class="col-6 col-xl-3"><div class="kpi-card kpi-spent h-100"><div class="kpi-head"><span class="kpi-label">Spent this month</span><span class="kpi-icon"><i class="bi bi-credit-card"></i></span></div><div class="kpi-value" id="overviewSpent">—</div><div class="kpi-caption"><i class="bi bi-receipt me-1"></i>Tracked expenses</div></div></div>
            <div class="col-6 col-xl-3"><div class="kpi-card kpi-year h-100"><div class="kpi-head"><span class="kpi-label">Saved this year</span><span class="kpi-icon"><i class="bi bi-calendar2-check"></i></span></div><div class="kpi-value" id="overviewYear">—</div><div class="kpi-caption"><i class="bi bi-graph-up-arrow me-1"></i>Your yearly progress</div></div></div>
            <div class="col-6 col-xl-3"><div class="kpi-card kpi-net h-100"><div class="kpi-head"><span class="kpi-label">Net this year</span><span class="kpi-icon"><i class="bi bi-wallet2"></i></span></div><div class="kpi-value" id="overviewNet">—</div><div class="kpi-caption"><i class="bi bi-info-circle me-1"></i>Saved minus spent</div></div></div>
        </div>
        <div class="save-card p-3 p-md-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3"><div><h2 class="h5 fw-bold mb-1">Saved vs spent</h2><div class="small muted">Compare your money across time.</div></div>
                <div class="btn-group btn-group-sm" role="group" aria-label="Chart period"><button class="btn btn-outline-secondary period-btn" data-period="7">7 days</button><button class="btn btn-outline-secondary period-btn" data-period="30">30 days</button><button class="btn btn-outline-secondary period-btn" data-period="12">12 months</button></div>
            </div>
            <div class="d-flex gap-3 small mb-2"><span><i class="legend-dot" style="background:#3978f6"></i>Saved</span><span><i class="legend-dot" style="background:#f59e0b"></i>Spent</span></div>
            <div class="chart-wrap"><svg id="overviewChart" role="img" aria-label="Saved and spent chart"></svg><div class="chart-tooltip" id="chartTooltip"></div></div>
            <div class="text-end"><button class="btn btn-sm btn-outline-secondary table-toggle" id="chartTableToggle" aria-pressed="false">Show table</button></div>
            <div class="table-responsive mt-3 d-none" id="chartTableWrap"><table class="table table-sm align-middle mb-0"><thead><tr><th>Period</th><th class="text-end">Saved</th><th class="text-end">Spent</th></tr></thead><tbody id="chartTable"></tbody></table></div>
        </div>
    </section>

    <section class="section-panel" id="panel-daily-saving"><div class="row g-3">
        <div class="col-lg-4"><div class="save-card p-3 p-md-4"><h2 class="h5 fw-bold mb-3">Add daily saving</h2><form class="entry-form" data-kind="saved">
            <label class="form-label small">Amount</label><input name="amount" class="form-control mb-3" type="number" min="0.01" step="0.01" required placeholder="0.00">
            <label class="form-label small">Category</label><select name="category" class="form-select mb-3">@foreach($categories as $category)<option>{{ $category }}</option>@endforeach</select>
            <label class="form-label small">Date</label><input name="date" class="form-control mb-3" type="date" required>
            <label class="form-label small"><i class="bi bi-sticky note-icon me-1"></i>Note <span class="muted">(optional)</span></label><textarea name="note" class="form-control mb-3" rows="2" maxlength="180" placeholder="Add a reminder or what this saving is for..."></textarea>
            <button class="btn btn-success w-100"><i class="bi bi-plus-lg me-1"></i>Add saving</button>
        </form></div></div>
        <div class="col-lg-8"><div class="row g-3 mb-3"><div class="col-4"><div class="save-card p-3 h-100"><div class="small muted">Today</div><div class="fw-bold mt-2" id="savedToday">—</div></div></div><div class="col-4"><div class="save-card p-3 h-100"><div class="small muted">Last 7 days</div><div class="fw-bold mt-2" id="savedWeek">—</div></div></div><div class="col-4"><div class="save-card p-3 h-100"><div class="small muted">Daily average</div><div class="fw-bold mt-2" id="savedAverage">—</div></div></div></div>
            <div class="save-card p-3"><h2 class="h5 fw-bold mb-3">Recent savings</h2><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Date</th><th>Category / note</th><th class="text-end">Amount</th><th></th></tr></thead><tbody id="savedEntries"></tbody></table></div></div></div>
    </div></section>

    <section class="section-panel" id="panel-daily-spending"><div class="row g-3">
        <div class="col-lg-4"><div class="save-card p-3 p-md-4"><h2 class="h5 fw-bold mb-3">Add daily spending</h2><form class="entry-form" data-kind="spent">
            <label class="form-label small">Amount</label><input name="amount" class="form-control mb-3" type="number" min="0.01" step="0.01" required placeholder="0.00">
            <label class="form-label small">Category</label><select name="category" class="form-select mb-3">@foreach($categories as $category)<option>{{ $category }}</option>@endforeach</select>
            <label class="form-label small">Date</label><input name="date" class="form-control mb-3" type="date" required>
            <label class="form-label small"><i class="bi bi-sticky note-icon me-1"></i>Note <span class="muted">(optional)</span></label><textarea name="note" class="form-control mb-3" rows="2" maxlength="180" placeholder="Add a detail, reminder, or reason for this expense..."></textarea>
            <button class="btn btn-warning w-100"><i class="bi bi-plus-lg me-1"></i>Add spending</button>
        </form></div></div>
        <div class="col-lg-8"><div class="row g-3 mb-3"><div class="col-6"><div class="save-card p-3 h-100"><div class="small muted">Spent today</div><div class="fw-bold mt-2" id="spentToday">—</div></div></div><div class="col-6"><div class="save-card p-3 h-100"><div class="small muted">Spent last 7 days</div><div class="fw-bold mt-2" id="spentWeek">—</div></div></div></div>
            <div class="save-card p-3"><h2 class="h5 fw-bold mb-3">Recent spending</h2><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Date</th><th>Category / note</th><th class="text-end">Amount</th><th></th></tr></thead><tbody id="spentEntries"></tbody></table></div></div></div>
    </div></section>

    <section class="section-panel" id="panel-monthly"><div class="save-card p-3 p-md-4">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4"><div><h2 class="h5 fw-bold mb-1">Monthly saving goal</h2><div class="small muted">Choose a month and set a target.</div></div><div class="d-flex gap-2"><input id="monthPicker" type="month" class="form-control"><input id="monthTarget" type="number" min="0" step="0.01" class="form-control" placeholder="Target amount"><button id="saveMonthTarget" class="btn btn-success">Save</button></div></div>
        <div class="row g-3 mb-4"><div class="col-md-6"><div class="small muted mb-1">Saved toward target</div><div class="d-flex justify-content-between fw-semibold mb-2"><span id="monthProgressText">—</span><span id="monthPercent">0%</span></div><div class="progress"><div id="monthProgress" class="progress-bar bg-success" style="width:0%"></div></div></div><div class="col-md-3"><div class="small muted">Still needed</div><div class="h5 fw-bold mt-1" id="monthLeft">—</div></div><div class="col-md-3"><div class="small muted">Save each day</div><div class="h5 fw-bold mt-1" id="monthPerDay">—</div></div></div>
        <div class="row g-3"><div class="col-lg-7"><h3 class="h6 fw-bold">Daily saved this month</h3><div class="chart-wrap"><svg id="monthChart" role="img" aria-label="Daily savings for selected month"></svg></div></div><div class="col-lg-5"><h3 class="h6 fw-bold">Weekly summary</h3><div class="table-responsive"><table class="table table-sm"><thead><tr><th>Week</th><th>Date range</th><th class="text-end">Saved</th></tr></thead><tbody id="weekTable"></tbody></table></div></div></div>
    </div></section>

    <section class="section-panel" id="panel-yearly"><div class="save-card p-3 p-md-4">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4"><div><h2 class="h5 fw-bold mb-1">Yearly saving goal</h2><div class="small muted">Compare progress with a full year target.</div></div><div class="d-flex gap-2"><input id="yearPicker" type="number" class="form-control" style="width:120px"><input id="yearTarget" type="number" min="0" step="0.01" class="form-control" placeholder="Target amount"><button id="saveYearTarget" class="btn btn-success">Save</button></div></div>
        <div class="row g-3 mb-4"><div class="col-md-6"><div class="small muted mb-1">Year progress</div><div class="d-flex justify-content-between fw-semibold mb-2"><span id="yearProgressText">—</span><span id="yearPercent">0%</span></div><div class="progress"><div id="yearProgress" class="progress-bar bg-primary" style="width:0%"></div></div></div><div class="col-md-3"><div class="small muted">Current projection</div><div class="h5 fw-bold mt-1" id="yearProjection">—</div></div><div class="col-md-3"><div class="small muted">Target remaining</div><div class="h5 fw-bold mt-1" id="yearLeft">—</div></div></div>
        <h3 class="h6 fw-bold">Saved by month</h3><div class="chart-wrap"><svg id="yearChart" role="img" aria-label="Monthly savings for selected year"></svg></div>
    </div></section>

    <section class="section-panel" id="panel-plans"><div class="row g-3">
        <div class="col-lg-4"><div class="save-card p-3 p-md-4"><h2 class="h5 fw-bold mb-3">Create a buying plan</h2><form id="planForm">
            <label class="form-label small">Plan name</label><input name="name" class="form-control mb-3" required maxlength="70" placeholder="A name for your goal">
            <label class="form-label small">Type</label><select name="type" class="form-select mb-3">@foreach($planTypes as $type)<option>{{ $type }}</option>@endforeach</select>
            <label class="form-label small">Price</label><input name="price" class="form-control mb-3" type="number" min="0.01" step="0.01" required>
            <label class="form-label small">Already saved</label><input name="saved" class="form-control mb-3" type="number" min="0" step="0.01" value="0" required>
            <label class="form-label small">Deadline</label><input name="deadline" class="form-control mb-3" type="date" required>
            <button class="btn btn-success w-100"><i class="bi bi-plus-lg me-1"></i>Create plan</button>
        </form></div></div>
        <div class="col-lg-8"><div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="h5 fw-bold mb-0">Your buying plans</h2><small class="muted">Contributions are added to daily savings automatically.</small></div></div><div id="plansList" class="row g-3"></div></div>
    </div></section>
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
                <div class="col-lg-7"><label for="journalTitle" class="form-label small fw-semibold">Note title</label><input id="journalTitle" class="form-control mb-3" maxlength="100" placeholder="For example: My savings plan for this month">
                    <div class="d-flex flex-wrap align-items-center gap-1 p-2 border rounded-top" style="border-color:var(--admin-border)!important;background:var(--card-muted-bg)">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-format="bold" title="Bold"><i class="bi bi-type-bold"></i></button><button type="button" class="btn btn-sm btn-outline-secondary" data-format="italic" title="Italic"><i class="bi bi-type-italic"></i></button><button type="button" class="btn btn-sm btn-outline-secondary" data-format="underline" title="Underline"><i class="bi bi-type-underline"></i></button><span class="vr mx-1"></span><label for="journalTextColor" class="small muted mb-0">Text color</label><input type="color" id="journalTextColor" class="form-control form-control-color" value="#15803d" title="Choose text color" aria-label="Choose text color" style="width:34px;height:32px;padding:3px"><button type="button" class="btn btn-sm btn-outline-secondary" id="applyJournalColor" title="Apply text color"><i class="bi bi-palette"></i></button><span class="vr mx-1"></span><button type="button" class="btn btn-sm btn-outline-secondary" data-format="insertUnorderedList" title="Bulleted list"><i class="bi bi-list-ul"></i></button><button type="button" class="btn btn-sm btn-outline-secondary" data-format="insertOrderedList" title="Numbered list"><i class="bi bi-list-ol"></i></button><button type="button" class="btn btn-sm btn-outline-secondary" data-format="formatBlock" data-format-value="blockquote" title="Quote"><i class="bi bi-quote"></i></button>
                    </div>
                    <div id="journalEditor" class="form-control rounded-top-0" contenteditable="true" role="textbox" aria-label="Write your note" aria-multiline="true" data-placeholder="Write your note here…" style="min-height:230px;overflow-y:auto"></div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3"><small class="muted">Your formatted note is saved to your account database.</small><button type="button" class="btn btn-success" id="saveJournalNote"><i class="bi bi-bookmark-check me-1"></i>Save to history</button></div>
                </div>
                <div class="col-lg-5"><div class="d-flex justify-content-between align-items-center mb-2"><div><h3 class="h6 fw-bold mb-0">Note history</h3><small class="muted">View, edit, or remove a saved note.</small></div><span class="badge rounded-pill text-bg-light" id="journalCount">0 saved</span></div><div id="journalHistory" class="d-flex flex-column gap-2"></div><div id="journalViewer" class="journal-item mt-3 d-none"><div class="d-flex justify-content-between align-items-start gap-2"><div><h4 class="h6 fw-bold mb-1" id="journalViewTitle"></h4><small class="muted" id="journalViewDate"></small></div><button type="button" class="btn-close" id="closeJournalViewer" aria-label="Close note preview"></button></div><div class="border-top mt-3 pt-3" style="border-color:var(--admin-border)!important" id="journalViewContent"></div><div id="journalRevisions" class="small mt-3"></div></div></div>
            </div></div>
        </div></div>
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
    const needsLegacySync = Boolean(legacyState && JSON.stringify(state) !== JSON.stringify({ ...initial, ...(DATABASE_STATE || {}) }));
    const $ = (id) => document.getElementById(id);
    const today = localDate(new Date());
    let chartPeriod = 7;
    let editingNoteId = null;
    let editingJournalId = null;
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
    function money(value) {
        const code = state.currency || 'USD';
        try { return new Intl.NumberFormat(undefined, { style:'currency', currency:code, maximumFractionDigits: code === 'KHR' || code === 'JPY' ? 0 : 2 }).format(Number(value) || 0); }
        catch (_) { return `${code} ${(Number(value) || 0).toFixed(2)}`; }
    }
    function sum(entries) { return entries.reduce((total, item) => total + Number(item.amount || 0), 0); }
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
        document.querySelectorAll('.save-tile').forEach(tile => tile.classList.toggle('active', tile === button));
        document.querySelectorAll('.section-panel').forEach(panel => panel.classList.toggle('active', panel.id === `panel-${button.dataset.section}`));
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
            const note = state.notes.find(item => item.id === deleteButton.dataset.deleteNote); if (!note || !confirm(`Delete “${note.title}” and its saved versions?`)) return;
            state.notes = state.notes.filter(item => item.id !== note.id);
            if (editingJournalId === note.id) { editingJournalId = null; $('journalTitle').value = ''; $('journalEditor').innerHTML = ''; $('saveJournalNote').innerHTML = '<i class="bi bi-bookmark-check me-1"></i>Save to history'; }
            $('journalViewer').classList.add('d-none'); renderNoteHistory(); persist();
        }
    });
    $('closeJournalViewer').addEventListener('click', () => $('journalViewer').classList.add('d-none'));
    document.querySelectorAll('.entry-form').forEach(form => {
        form.elements.date.value = today;
        form.addEventListener('submit', event => {
            event.preventDefault(); const data = new FormData(form); const amount = Number(data.get('amount'));
            if (!Number.isFinite(amount) || amount <= 0) return;
            state.entries.push({ id: crypto.randomUUID ? crypto.randomUUID() : String(Date.now()), kind:form.dataset.kind, amount, category:data.get('category'), date:data.get('date'), note:data.get('note').trim(), source:'manual' });
            form.reset(); form.elements.date.value = today; persist();
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
    $('currencySelect').addEventListener('change', event => { state.currency = event.target.value; persist(); });

    function renderRows(kind, id) {
        const items = state.entries.filter(item => item.kind === kind).sort((a,b) => b.date.localeCompare(a.date) || b.id.localeCompare(a.id)).slice(0, 40);
        $(id).innerHTML = items.length ? items.map(item => `<tr><td class="text-nowrap">${safe(item.date)}</td><td><div class="fw-semibold">${safe(item.category)}</div><small class="muted note-preview" title="${safe(item.note || '')}">${item.note ? `<i class="bi bi-sticky note-icon me-1"></i>${safe(item.note)}` : '<span class="muted">No note</span>'}</small></td><td class="text-end text-nowrap entry-amount ${kind}">${money(item.amount)}</td><td class="text-end text-nowrap"><button class="btn btn-sm btn-outline-secondary me-1" data-edit-note="${safe(item.id)}" aria-label="Edit note" title="Edit note"><i class="bi bi-pencil-square"></i></button><button class="btn btn-sm btn-outline-danger" data-delete-entry="${safe(item.id)}" aria-label="Delete entry" title="Delete entry"><i class="bi bi-trash"></i></button></td></tr>`).join('') : emptyRow(4, 'No entries yet. Add your first one above.');
    }
    function renderDaily() {
        const weekStart = dayOffset(-6);
        const savedToday = sum(entriesFor('saved', today, today)); const savedWeek = sum(entriesFor('saved', weekStart, today));
        $('savedToday').textContent = money(savedToday); $('savedWeek').textContent = money(savedWeek); $('savedAverage').textContent = money(savedWeek / 7);
        $('spentToday').textContent = money(sum(entriesFor('spent', today, today))); $('spentWeek').textContent = money(sum(entriesFor('spent', weekStart, today)));
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
            const amount = Number(prompt(`How much would you like to add to “${plan.name}”?`));
            if (!Number.isFinite(amount) || amount <= 0) return;
            plan.saved = Number(plan.saved) + amount;
            state.entries.push({ id: crypto.randomUUID ? crypto.randomUUID() : String(Date.now()), kind:'saved', amount, category:'Buying plan', date:today, note:plan.name, source:'plan', planId:plan.id }); persist();
        }
        const remove = event.target.closest('[data-delete-plan]');
        if (remove) { state.plans = state.plans.filter(item => item.id !== remove.dataset.deletePlan); persist(); }
    });
    $('saveEditedNote').addEventListener('click', () => {
        const item = state.entries.find(entry => entry.id === editingNoteId); if (!item) return;
        item.note = $('noteEditorText').value.trim();
        bootstrap.Modal.getOrCreateInstance($('noteEditor')).hide(); editingNoteId = null; persist();
    });

    function renderOverview() {
        const month = today.slice(0,7), year = today.slice(0,4);
        const monthSaved = sum(state.entries.filter(i => i.kind === 'saved' && i.date.startsWith(month)));
        const monthSpent = sum(state.entries.filter(i => i.kind === 'spent' && i.date.startsWith(month)));
        const yearSaved = sum(state.entries.filter(i => i.kind === 'saved' && i.date.startsWith(year)));
        const yearSpent = sum(state.entries.filter(i => i.kind === 'spent' && i.date.startsWith(year)));
        $('overviewMonth').textContent = money(monthSaved); $('overviewSpent').textContent = money(monthSpent); $('overviewYear').textContent = money(yearSaved); $('overviewNet').textContent = money(yearSaved - yearSpent);
        renderChart();
    }
    function chartData() {
        if (chartPeriod === 12) return Array.from({length:12}, (_, n) => { const date = new Date(); date.setDate(1); date.setMonth(date.getMonth() - 11 + n); const key = localDate(date).slice(0,7); return {label:date.toLocaleDateString(undefined,{month:'short'}), key, saved:sum(state.entries.filter(i=>i.kind==='saved'&&i.date.startsWith(key))), spent:sum(state.entries.filter(i=>i.kind==='spent'&&i.date.startsWith(key)))}; });
        return Array.from({length:chartPeriod}, (_, n) => { const key=dayOffset(n-chartPeriod+1); return {label:new Date(`${key}T12:00:00`).toLocaleDateString(undefined,{month:'short',day:'numeric'}),key,saved:sum(entriesFor('saved',key,key)),spent:sum(entriesFor('spent',key,key))}; });
    }
    function drawBars(svgId, data, compact=false) {
        const svg=$(svgId), width=Math.max(svg.parentElement.clientWidth, 320), height=compact?230:260, left=42, right=8, top=14, bottom=compact?34:40, plotH=height-top-bottom, plotW=width-left-right;
        const max=Math.max(1,...data.flatMap(d=>[d.saved,d.spent]))*1.15, slot=plotW/data.length, barW=Math.max(3,Math.min(18,slot*.28));
        let html='';
        [0,.5,1].forEach(r=>{const y=top+plotH*r, val=max*(1-r);html+=`<line x1="${left}" y1="${y}" x2="${width-right}" y2="${y}" stroke="var(--admin-border)" stroke-dasharray="3 4"/><text x="${left-7}" y="${y+4}" text-anchor="end" fill="var(--admin-text-muted)" font-size="10">${val>=1000?(val/1000).toFixed(0)+'k':Math.round(val)}</text>`;});
        data.forEach((d,i)=>{const cx=left+slot*i+slot/2, h1=d.saved/max*plotH,h2=d.spent/max*plotH; html+=`<rect data-tip="${safe(d.label)}|Saved|${safe(money(d.saved))}" x="${cx-barW-1}" y="${top+plotH-h1}" width="${barW}" height="${Math.max(1,h1)}" rx="3" fill="#3978f6"/><rect data-tip="${safe(d.label)}|Spent|${safe(money(d.spent))}" x="${cx+1}" y="${top+plotH-h2}" width="${barW}" height="${Math.max(1,h2)}" rx="3" fill="#f59e0b"/>`; if(data.length<=12 || i%Math.ceil(data.length/7)===0) html+=`<text x="${cx}" y="${height-8}" text-anchor="middle" fill="var(--admin-text-muted)" font-size="10">${safe(d.label)}</text>`;});
        svg.setAttribute('viewBox',`0 0 ${width} ${height}`); svg.innerHTML=html;
        svg.querySelectorAll('[data-tip]').forEach(bar=>{if(svgId!=='overviewChart'){bar.setAttribute('title',bar.dataset.tip.replaceAll('|',': '));return;}bar.addEventListener('mousemove',e=>{const parts=bar.dataset.tip.split('|'), tip=$('chartTooltip'); tip.innerHTML=`<strong>${safe(parts[0])}</strong><br>${safe(parts[1])}: ${safe(parts[2])}`; tip.style.display='block';const r=svg.parentElement.getBoundingClientRect();tip.style.left=`${Math.min(e.clientX-r.left+10,r.width-150)}px`;tip.style.top=`${Math.max(0,e.clientY-r.top-45)}px`;});bar.addEventListener('mouseleave',()=>{$('chartTooltip').style.display='none';});});
    }
    function renderChart() {
        if (!$('overviewChart')) return; const data=chartData(); drawBars('overviewChart',data);
        $('chartTable').innerHTML=data.map(d=>`<tr><td>${safe(d.label)}</td><td class="text-end">${money(d.saved)}</td><td class="text-end">${money(d.spent)}</td></tr>`).join('');
    }
    $('monthPicker').value=today.slice(0,7); $('yearPicker').value=new Date().getFullYear();
    function renderMonthly() {
        const key=$('monthPicker').value || today.slice(0,7); $('monthTarget').value=state.monthlyTargets[key] ?? '';
        const [y,m]=key.split('-').map(Number), last=new Date(y,m,0).getDate();
        const saved=sum(state.entries.filter(i=>i.kind==='saved'&&i.date.startsWith(key))), target=Number(state.monthlyTargets[key]||0), left=Math.max(0,target-saved), daysLeft=Math.max(1,last-(key===today.slice(0,7)?new Date().getDate():0));
        const percent=target?Math.min(100,saved/target*100):0;
        $('monthProgressText').textContent=`${money(saved)} of ${money(target)}`; $('monthPercent').textContent=`${Math.round(percent)}%`; $('monthProgress').style.width=`${percent}%`; $('monthLeft').textContent=money(left); $('monthPerDay').textContent=money(left/daysLeft);
        const data=Array.from({length:last},(_,n)=>{const date=`${key}-${String(n+1).padStart(2,'0')}`;return{label:String(n+1),key:date,saved:sum(entriesFor('saved',date,date)),spent:0};});
        drawBars('monthChart',data,true);
        const weeks=[]; for(let start=1;start<=last;start+=7){const end=Math.min(start+6,last),from=`${key}-${String(start).padStart(2,'0')}`,through=`${key}-${String(end).padStart(2,'0')}`;weeks.push(`<tr><td>Week ${weeks.length+1}</td><td>${start}–${end}</td><td class="text-end">${money(sum(entriesFor('saved',from,through)))}</td></tr>`);}
        $('weekTable').innerHTML=weeks.join('');
    }
    $('monthPicker').addEventListener('change',renderMonthly);
    $('saveMonthTarget').addEventListener('click',()=>{const key=$('monthPicker').value,target=Number($('monthTarget').value);if(!key||target<0)return;state.monthlyTargets[key]=target;persist();});
    function renderYearly() {
        const year=String($('yearPicker').value||new Date().getFullYear()); $('yearTarget').value=state.yearlyTargets[year]??'';
        const data=Array.from({length:12},(_,n)=>{const key=`${year}-${String(n+1).padStart(2,'0')}`;return{label:new Date(Number(year),n,1).toLocaleDateString(undefined,{month:'short'}),key,saved:sum(state.entries.filter(i=>i.kind==='saved'&&i.date.startsWith(key))),spent:0};});
        const total=sum(data.map(d=>d.saved)), target=Number(state.yearlyTargets[year]||0), now=new Date(), elapsed=Math.max(1,year===String(now.getFullYear())?now.getMonth()+1:12), projection=total/elapsed*12, percent=target?Math.min(100,total/target*100):0;
        $('yearProgressText').textContent=`${money(total)} of ${money(target)}`; $('yearPercent').textContent=`${Math.round(percent)}%`; $('yearProgress').style.width=`${percent}%`; $('yearProjection').textContent=money(projection); $('yearLeft').textContent=money(Math.max(0,target-total)); drawBars('yearChart',data,true);
    }
    $('yearPicker').addEventListener('change',renderYearly);
    $('saveYearTarget').addEventListener('click',()=>{const year=String($('yearPicker').value),target=Number($('yearTarget').value);if(!year||target<0)return;state.yearlyTargets[year]=target;persist();});

    $('planForm').addEventListener('submit',event=>{event.preventDefault();const data=new FormData(event.currentTarget);state.plans.push({id:crypto.randomUUID?crypto.randomUUID():String(Date.now()),name:data.get('name').trim(),type:data.get('type'),price:Number(data.get('price')),saved:Number(data.get('saved')),deadline:data.get('deadline')});event.currentTarget.reset();persist();});
    function renderPlans() {
        const host=$('plansList');
        if(!state.plans.length){host.innerHTML='<div class="col-12"><div class="save-card empty">No buying plans yet. Create one to start saving for something specific.</div></div>';return;}
        host.innerHTML=state.plans.map(plan=>{const remaining=Math.max(0,plan.price-plan.saved),percent=Math.min(100,plan.saved/plan.price*100),months=Math.max(1,(new Date(`${plan.deadline}T12:00:00`)-new Date(`${today}T12:00:00`))/(30.4375*86400000));return `<div class="col-12"><div class="save-card p-3"><div class="d-flex justify-content-between gap-2"><div><span class="badge text-bg-light mb-2">${safe(plan.type)}</span><h3 class="h6 fw-bold mb-1">${safe(plan.name)}</h3><small class="muted">Deadline ${safe(plan.deadline)}</small></div><button class="btn btn-sm btn-outline-danger align-self-start" data-delete-plan="${safe(plan.id)}" aria-label="Delete plan"><i class="bi bi-trash"></i></button></div><div class="d-flex justify-content-between small mt-3 mb-2"><span>${money(plan.saved)} saved</span><span>${money(plan.price)} total</span></div><div class="progress mb-3"><div class="progress-bar bg-success" style="width:${percent}%"></div></div><div class="d-flex flex-wrap justify-content-between align-items-center gap-2"><small class="muted">${money(remaining)} left · about ${money(remaining/months)} / month</small><button class="btn btn-sm btn-outline-success" data-add-to-plan="${safe(plan.id)}"><i class="bi bi-plus-lg me-1"></i>Add money</button></div></div></div>`;}).join('');
    }
    function render(){ renderOverview();renderDaily();renderMonthly();renderYearly();renderPlans(); }
    document.querySelectorAll('.period-btn').forEach(b=>b.classList.toggle('active',Number(b.dataset.period)===chartPeriod));
    render();
    if (DATABASE_STATE && !needsLegacySync) {
        localStorage.removeItem(LEGACY_KEY);
        $('trackerSaveStatus').innerHTML = '<i class="bi bi-cloud-check me-1"></i>Loaded from your account';
    } else if (legacyState) {
        $('trackerSaveStatus').innerHTML = '<i class="bi bi-cloud-arrow-up me-1"></i>Moving existing data to your account…';
        queueDatabaseSave();
    }
    window.addEventListener('resize',()=>{renderChart();renderMonthly();renderYearly();});
})();
</script>
@endpush
