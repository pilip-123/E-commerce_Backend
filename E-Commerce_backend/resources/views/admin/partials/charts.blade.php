{{--
    Shared SVG charts for admin pages (no external library).
    Include once per page:  include admin.partials.charts
    Then in JS:
        AppCharts.line(el, { labels: [...], series: [{ name, values, color, format: 'money'|'number', axis: 'right', dashed: true }] });
        AppCharts.bars(el, { labels: [...], series: [{ name, values, color, format }], line: { name, values, color, format } });
    `el` is an element (or id) with a fixed height. Charts redraw on resize.
--}}
@once
@push('styles')
<style>
    .ac-chart { position:relative; }
    .ac-inner { position:relative; width:100%; height:100%; }
    .ac-inner svg { display:block; width:100%; height:100%; overflow:visible; }
    .ac-inner text { font-family:inherit; }
    .ac-tip { position:absolute; display:none; z-index:20; min-width:160px; padding:10px 12px; border-radius:12px; pointer-events:none; color:#fff; font-size:12px; line-height:1.4; background:rgba(17,24,39,.94); box-shadow:0 12px 28px rgba(0,0,0,.25); }
    .ac-tip .ac-tt-title { margin-bottom:6px; font-weight:750; }
    .ac-tip .ac-tt-row { display:flex; justify-content:space-between; gap:16px; line-height:1.75; }
    .ac-tip .ac-tt-row i { display:inline-block; width:8px; height:8px; margin-right:6px; border-radius:2px; }
    .ac-tip .ac-tt-row b { font-variant-numeric:tabular-nums; }
    .ac-tip .ac-tt-foot { margin-top:6px; padding-top:6px; border-top:1px solid rgba(255,255,255,.15); color:#cbd5e1; }
    .ac-empty { position:absolute; inset:0; display:grid; place-items:center; color:var(--admin-text-muted); font-size:13px; pointer-events:none; }
    .ac-cross, .ac-hdot, .ac-band { opacity:0; transition:opacity .12s ease; pointer-events:none; }
    .ac-hover .ac-cross, .ac-hover .ac-hdot { opacity:1; }
    .ac-hover .ac-band { opacity:.09; }
    .ac-draw { stroke-dasharray:4000; stroke-dashoffset:4000; animation:ac-draw 1.3s ease-out forwards; }
    .ac-fade { opacity:0; animation:ac-fade .8s ease-out .35s forwards; }
    .ac-grow { transform-box:fill-box; transform-origin:50% 100%; animation:ac-grow .7s cubic-bezier(.2,.8,.2,1) both; animation-delay:calc(var(--i,0) * 25ms); }
    .ac-pop { opacity:0; animation:ac-fade .4s ease-out forwards; animation-delay:calc(.45s + var(--i,0) * 25ms); }
    .ac-pulse { transform-box:fill-box; transform-origin:center; animation:ac-pulse 1.8s ease-out infinite; }
    @keyframes ac-draw { to { stroke-dashoffset:0; } }
    @keyframes ac-fade { to { opacity:1; } }
    @keyframes ac-grow { from { transform:scaleY(0); } to { transform:scaleY(1); } }
    @keyframes ac-pulse { 0% { transform:scale(1); opacity:.6; } 100% { transform:scale(3.2); opacity:0; } }
    .ac-legend { display:flex; flex-wrap:wrap; align-items:center; gap:6px 16px; font-size:12.5px; color:var(--admin-text-muted); }
    .ac-legend i { display:inline-block; width:10px; height:10px; margin-right:6px; border-radius:3px; vertical-align:-1px; }
    .ac-legend i.ac-dash { height:0; border-top:2px dashed currentColor; border-radius:0; vertical-align:3px; }
    .ac-legend b { color:var(--admin-text); font-variant-numeric:tabular-nums; }
    @media (prefers-reduced-motion:reduce) { .ac-draw, .ac-fade, .ac-grow, .ac-pop { animation:none; stroke-dashoffset:0; opacity:1; } .ac-pulse { animation:none; } }
</style>
@endpush
@push('scripts')
<script>
window.AppCharts = (function () {
    'use strict';
    var uid = 0;

    function fmt(v, f) {
        if (typeof f === 'function') return f(v);
        if (f === 'money') return '$' + Number(v || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        return Number(v || 0).toLocaleString();
    }
    function short(v, f) {
        var a = Math.abs(v), s = a >= 1e6 ? +(v / 1e6).toFixed(1) + 'M' : a >= 1e3 ? +(v / 1e3).toFixed(1) + 'k' : String(+(+v).toFixed(a && a < 1 ? 2 : a < 10 && a % 1 ? 1 : 0));
        return (f === 'money' ? (v < 0 ? '-$' + s.replace('-', '') : '$' + s) : s);
    }
    function niceCeil(v) { if (!(v > 0)) return 1; var e = Math.pow(10, Math.floor(Math.log10(v))), f = v / e; return (f <= 1 ? 1 : f <= 2 ? 2 : f <= 2.5 ? 2.5 : f <= 5 ? 5 : 10) * e; }
    function esc(t) { return String(t == null ? '' : t).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
    function num(a) { return (a || []).map(function (v) { return Number(v) || 0; }); }
    function smooth(p) {
        return p.map(function (q, i) {
            if (!i) return 'M' + q[0] + ',' + q[1];
            var o = p[i - 1], mx = ((o[0] + q[0]) / 2).toFixed(1);
            return 'C' + mx + ',' + o[1] + ' ' + mx + ',' + q[1] + ' ' + q[0] + ',' + q[1];
        }).join('');
    }
    function roundTop(x, y, w, h, r) {
        if (h <= 0) return '';
        r = Math.min(r, w / 2, h);
        return 'M' + x + ',' + (y + h) + 'V' + (y + r) + 'Q' + x + ',' + y + ' ' + (x + r) + ',' + y + 'H' + (x + w - r) + 'Q' + (x + w) + ',' + y + ' ' + (x + w) + ',' + (y + r) + 'V' + (y + h) + 'Z';
    }
    function lighten(hex, amt) {
        var n = parseInt(hex.replace('#', ''), 16), r = n >> 16, g = (n >> 8) & 255, b = n & 255;
        var m = function (c) { return Math.round(c + (255 - c) * amt); };
        return 'rgb(' + m(r) + ',' + m(g) + ',' + m(b) + ')';
    }
    function scale(values, pad) {
        var max = Math.max.apply(null, values.concat(0)), min = Math.min.apply(null, values.concat(0));
        var top = max > 0 ? niceCeil(max * (pad || 1.15)) : 4, // empty chart → tidy 0–4 axis
             bottom = min < 0 ? -niceCeil(-min * (pad || 1.15)) : 0;
        return { max: top, min: bottom, range: (top - bottom) || 1 };
    }

    // Shared plumbing: wrapper, tooltip, resize redraw, hover → callback(index).
    function mount(el, opts, draw) {
        if (typeof el === 'string') el = document.getElementById(el);
        if (!el) return null;
        if (getComputedStyle(el).position === 'static') el.style.position = 'relative';
        el.classList.add('ac-chart');
        el.innerHTML = '<div class="ac-inner"><svg role="img" aria-label="' + esc(opts.title || 'Chart') + '"></svg><div class="ac-tip"></div><div class="ac-empty" hidden></div></div>';
        el._ac = { opts: opts, draw: draw };
        var inner = el.firstChild, svg = inner.querySelector('svg'), tip = inner.querySelector('.ac-tip');
        function render(animate) {
            var W = Math.max(inner.clientWidth, 280), H = opts.height || inner.clientHeight || 220;
            svg.setAttribute('viewBox', '0 0 ' + W + ' ' + H);
            svg.style.height = H + 'px';
            el._acGeo = draw(svg, W, H, animate !== false);
            var empty = inner.querySelector('.ac-empty');
            empty.textContent = opts.emptyText || 'No data yet';
            empty.hidden = !el._acGeo.empty;
        }
        render(true);
        svg.addEventListener('pointermove', function (e) {
            var g = el._acGeo; if (!g || !g.n) return;
            var r = svg.getBoundingClientRect(), mx = (e.clientX - r.left) * (g.W / r.width);
            var i = g.indexAt(mx); if (i < 0) return hide();
            inner.classList.add('ac-hover');
            var pos = g.hover(i);
            tip.innerHTML = g.tooltip(i);
            tip.style.display = 'block';
            var px = pos.x * (r.width / g.W), py = pos.y * (r.height / g.H), tw = tip.offsetWidth, th = tip.offsetHeight;
            var left = px + 16; if (left + tw > r.width) left = px - tw - 16;
            tip.style.left = Math.max(0, left) + 'px';
            tip.style.top = Math.min(Math.max(0, py - th / 2), r.height - th) + 'px';
        });
        function hide() { inner.classList.remove('ac-hover'); tip.style.display = 'none'; }
        svg.addEventListener('pointerleave', hide);
        if (window.ResizeObserver && !el._acObs) {
            var lastW = inner.clientWidth;
            el._acObs = new ResizeObserver(function () {
                var w = el.firstChild ? el.firstChild.clientWidth : 0;
                if (w && Math.abs(w - lastW) > 4) { lastW = w; render(false); }
            });
            el._acObs.observe(el);
        }
        return el;
    }

    function axes(o) {
        // o: {W,H,L,R,T,B,sc,format,right:{sc,format,color}}
        var h = '', ph = o.H - o.T - o.B;
        for (var t = 0; t <= 4; t++) {
            var v = o.sc.min + o.sc.range * t / 4, y = o.T + ph - ph * t / 4;
            h += '<line x1="' + o.L + '" y1="' + y + '" x2="' + (o.W - o.R) + '" y2="' + y + '" stroke="var(--admin-border)" ' + (Math.abs(v) < 1e-9 ? 'stroke-width="1.2"' : 'stroke-dasharray="2 5"') + '/>';
            h += '<text x="' + (o.L - 8) + '" y="' + (y + 3.5) + '" text-anchor="end" font-size="10" fill="var(--admin-text-muted)">' + esc(short(v, o.format)) + '</text>';
            if (o.right) {
                var rv = o.right.sc.min + o.right.sc.range * t / 4;
                h += '<text x="' + (o.W - o.R + 8) + '" y="' + (y + 3.5) + '" font-size="10" fill="' + o.right.color + '">' + esc(short(rv, o.right.format)) + '</text>';
            }
        }
        return h;
    }
    function xLabels(labels, xs, W, H, L, R) {
        var n = labels.length, every = Math.ceil(n / Math.max(3, Math.floor((W - L - R) / 64))), h = '';
        labels.forEach(function (lab, i) {
            if (i !== n - 1 && (i % every || n - 1 - i < every * .7)) return;
            var t = String(lab == null ? '' : lab); if (t.length > 12) t = t.slice(0, 11) + '…';
            h += '<text x="' + xs(i) + '" y="' + (H - 6) + '" text-anchor="middle" font-size="10" fill="var(--admin-text-muted)">' + esc(t) + '</text>';
        });
        return h;
    }
    function row(color, name, value) { return '<div class="ac-tt-row"><span><i style="background:' + color + '"></i>' + esc(name) + '</span><b>' + esc(value) + '</b></div>'; }

    // ── Line / area chart ─────────────────────────────────────────────
    function line(el, opts) {
        var labels = opts.labels || [], series = (opts.series || []).map(function (s, k) {
            return { name: s.name || ('Series ' + (k + 1)), values: num(s.values), color: s.color || '#10b981', format: s.format || opts.format, axis: s.axis === 'right' ? 'right' : 'left', dashed: !!s.dashed };
        });
        var id = 'ac' + (++uid);
        return mount(el, opts, function (svg, W, H, animate) {
            var n = labels.length, left = series.filter(function (s) { return s.axis === 'left'; }), right = series.filter(function (s) { return s.axis === 'right'; });
            var main = series[0] || { values: [], color: '#10b981' };
            var L = 52, R = right.length ? 40 : 14, T = 28, B = 26, pw = W - L - R, ph = H - T - B;
            var scL = scale([].concat.apply([], left.map(function (s) { return s.values; })));
            var scR = right.length ? scale([].concat.apply([], right.map(function (s) { return s.values; }))) : null;
            var xs = function (i) { return +(n > 1 ? L + pw * i / (n - 1) : L + pw / 2).toFixed(1); };
            var ys = function (v, s) { var sc = s.axis === 'right' ? scR : scL; return +(T + ph - (v - sc.min) / sc.range * ph).toFixed(1); };
            var html = '<defs>' + series.map(function (s, k) {
                return '<linearGradient id="' + id + 'a' + k + '" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="' + s.color + '" stop-opacity="' + (k ? .12 : .32) + '"/><stop offset="100%" stop-color="' + s.color + '" stop-opacity="0"/></linearGradient>';
            }).join('') + '<filter id="' + id + 'g" x="-10%" y="-40%" width="120%" height="180%"><feDropShadow dx="0" dy="4" stdDeviation="4" flood-color="' + main.color + '" flood-opacity=".3"/></filter></defs>';
            html += axes({ W: W, H: H, L: L, R: R, T: T, B: B, sc: scL, format: left[0] ? left[0].format : opts.format, right: scR ? { sc: scR, format: right[0].format, color: right[0].color } : null });
            var allPts = series.map(function (s) { return s.values.map(function (v, i) { return [xs(i), ys(v, s)]; }); });
            // draw secondary series first so the main line sits on top
            series.slice().reverse().forEach(function (s, rk) {
                var k = series.length - 1 - rk, pts = allPts[k]; if (!pts.length) return;
                var d = smooth(pts), base = ys(Math.max(0, (s.axis === 'right' ? scR : scL).min), s);
                html += '<path class="' + (animate ? 'ac-fade' : '') + '" d="' + d + 'L' + pts[pts.length - 1][0] + ',' + base + 'L' + pts[0][0] + ',' + base + 'Z" fill="url(#' + id + 'a' + k + ')"/>';
                html += '<path class="' + (animate && !s.dashed ? 'ac-draw' : '') + '" d="' + d + '" fill="none" stroke="' + s.color + '" stroke-width="' + (k ? 2 : 2.6) + '" stroke-linecap="round" stroke-linejoin="round"' + (s.dashed ? ' stroke-dasharray="6 4"' : '') + (k ? '' : ' filter="url(#' + id + 'g)"') + '/>';
            });
            var hasData = main.values.some(function (v) { return v; });
            if (hasData && n > 2 && opts.average !== false) {
                var avg = main.values.reduce(function (a, b) { return a + b; }, 0) / n, ay = ys(avg, main);
                html += '<line x1="' + L + '" y1="' + ay + '" x2="' + (W - R) + '" y2="' + ay + '" stroke="' + main.color + '" stroke-opacity=".55" stroke-dasharray="3 4"/>'
                    + '<text x="' + (W - R - 4) + '" y="' + (ay - 5) + '" text-anchor="end" font-size="10" font-weight="700" fill="' + main.color + '" paint-order="stroke" stroke="var(--admin-surface)" stroke-width="3">' + esc('Avg ' + short(avg, main.format)) + '</text>';
            }
            if (hasData && opts.peak !== false) {
                var pk = main.values.indexOf(Math.max.apply(null, main.values)), p = allPts[0][pk], label = 'Peak ' + short(main.values[pk], main.format), lw = label.length * 6 + 16;
                var lx = Math.min(Math.max(p[0] - lw / 2, L), W - R - lw), ly = Math.max(2, p[1] - 28);
                html += '<g class="' + (animate ? 'ac-pop' : '') + '"><circle cx="' + p[0] + '" cy="' + p[1] + '" r="4.5" fill="' + main.color + '" stroke="var(--admin-surface)" stroke-width="2"/>'
                    + '<rect x="' + lx + '" y="' + ly + '" width="' + lw + '" height="18" rx="9" fill="' + main.color + '"/><text x="' + (lx + lw / 2) + '" y="' + (ly + 12.5) + '" text-anchor="middle" font-size="10" font-weight="700" fill="#fff">' + esc(label) + '</text></g>';
            }
            if (n && opts.pulseLast !== false) {
                var lp = allPts[0][n - 1];
                html += '<circle class="ac-pulse" cx="' + lp[0] + '" cy="' + lp[1] + '" r="4" fill="' + main.color + '"/><circle cx="' + lp[0] + '" cy="' + lp[1] + '" r="3.5" fill="var(--admin-surface)" stroke="' + main.color + '" stroke-width="2.4"/>';
            }
            html += xLabels(labels, xs, W, H, L, R);
            html += '<line class="ac-cross" x1="0" x2="0" y1="' + T + '" y2="' + (T + ph) + '" stroke="' + main.color + '" stroke-dasharray="3 3"/>';
            series.forEach(function (s, k) { html += '<circle class="ac-hdot" data-k="' + k + '" r="5" fill="' + s.color + '" stroke="var(--admin-surface)" stroke-width="2.5"/>'; });
            svg.innerHTML = html;
            var cross = svg.querySelector('.ac-cross'), dots = svg.querySelectorAll('.ac-hdot');
            return {
                W: W, H: H, n: n, empty: !series.some(function (s) { return s.values.some(function (v) { return v; }); }),
                indexAt: function (mx) { return n ? Math.max(0, Math.min(n - 1, Math.round((mx - L) / (n > 1 ? pw / (n - 1) : 1)))) : -1; },
                hover: function (i) {
                    var x = xs(i); cross.setAttribute('x1', x); cross.setAttribute('x2', x);
                    dots.forEach(function (d) { var p = allPts[+d.dataset.k][i]; if (p) { d.setAttribute('cx', p[0]); d.setAttribute('cy', p[1]); } });
                    return { x: x, y: allPts[0][i] ? allPts[0][i][1] : T };
                },
                tooltip: function (i) {
                    var h = '<div class="ac-tt-title">' + esc(labels[i]) + '</div>' + series.map(function (s) { return row(s.color, s.name, fmt(s.values[i], s.format)); }).join('');
                    if (i > 0 && main.values[i - 1]) {
                        var ch = (main.values[i] - main.values[i - 1]) / main.values[i - 1] * 100;
                        h += '<div class="ac-tt-foot">' + (ch >= 0 ? '▲ ' : '▼ ') + Math.abs(ch).toFixed(1) + '% vs previous</div>';
                    }
                    return h;
                }
            };
        });
    }

    // ── Bar chart (grouped bars + optional overlay line) ──────────────
    function bars(el, opts) {
        var labels = opts.labels || [], series = (opts.series || []).map(function (s, k) {
            return { name: s.name || ('Series ' + (k + 1)), values: num(s.values), color: s.color || '#10b981', format: s.format || opts.format };
        });
        var ov = opts.line ? { name: opts.line.name || 'Line', values: num(opts.line.values), color: opts.line.color || '#7c3aed', format: opts.line.format || opts.format } : null;
        var id = 'ac' + (++uid);
        return mount(el, opts, function (svg, W, H, animate) {
            var n = labels.length, m = series.length || 1, L = 52, R = 14, T = 26, B = opts.rotateLabels ? 40 : 26, pw = W - L - R, ph = H - T - B;
            var all = [].concat.apply([], series.map(function (s) { return s.values; })).concat(ov ? ov.values : []);
            var sc = scale(all, 1.18), slot = pw / Math.max(n, 1);
            var barW = Math.max(3, Math.min(m > 1 ? 18 : 34, slot * .72 / m)), gap = m > 1 ? Math.min(4, barW * .25) : 0, groupW = barW * m + gap * (m - 1);
            var cx = function (i) { return L + slot * i + slot / 2; };
            var ys = function (v) { return T + ph - (v - sc.min) / sc.range * ph; }, zero = ys(0);
            var html = '<defs>' + series.map(function (s, k) {
                return '<linearGradient id="' + id + 'b' + k + '" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="' + lighten(s.color, .35) + '"/><stop offset="100%" stop-color="' + s.color + '"/></linearGradient>';
            }).join('') + '<filter id="' + id + 'g" x="-50%" y="-30%" width="200%" height="160%"><feDropShadow dx="0" dy="4" stdDeviation="4" flood-color="' + (series[0] ? series[0].color : '#10b981') + '" flood-opacity=".35"/></filter></defs>';
            html += axes({ W: W, H: H, L: L, R: R, T: T, B: B, sc: sc, format: series[0] ? series[0].format : opts.format });
            html += '<rect class="ac-band" x="0" y="' + (T - 6) + '" width="' + (slot * .9) + '" height="' + (ph + 6) + '" rx="8" fill="var(--admin-text-muted)"/>';
            var single = m === 1 && opts.highlightMax !== false, maxIdx = single ? series[0].values.indexOf(Math.max.apply(null, series[0].values)) : -1;
            var showValues = opts.valueLabels !== false && slot / m >= 26 && n <= 24;
            series.forEach(function (s, k) {
                s.values.forEach(function (v, i) {
                    if (!v) return;
                    var x = cx(i) - groupW / 2 + k * (barW + gap), y = Math.min(ys(v), zero), h = Math.abs(ys(v) - zero), top = i === maxIdx && v > 0;
                    html += '<path class="' + (animate ? 'ac-grow' : '') + '" style="--i:' + i + '" d="' + roundTop(+x.toFixed(1), +y.toFixed(1), +barW.toFixed(1), +h.toFixed(1), Math.min(7, barW / 2)) + '" fill="url(#' + id + 'b' + k + ')"' + (single && !top && maxIdx >= 0 ? ' opacity=".72"' : '') + (top ? ' filter="url(#' + id + 'g)"' : '') + '/>';
                    if (showValues) html += '<text class="' + (animate ? 'ac-pop' : '') + '" style="--i:' + i + '" x="' + (x + barW / 2) + '" y="' + (y - 5) + '" text-anchor="middle" font-size="9.5" font-weight="700" fill="' + s.color + '">' + esc(short(v, s.format)) + '</text>';
                    if (top && n > 1) html += '<g class="' + (animate ? 'ac-pop' : '') + '" style="--i:' + i + '"><rect x="' + (x + barW / 2 - 17) + '" y="' + Math.max(0, y - (showValues ? 34 : 22)) + '" width="34" height="15" rx="7.5" fill="' + s.color + '"/><text x="' + (x + barW / 2) + '" y="' + (Math.max(0, y - (showValues ? 34 : 22)) + 10.5) + '" text-anchor="middle" font-size="9" font-weight="800" fill="#fff">TOP</text></g>';
                });
            });
            var ovPts = ov ? ov.values.map(function (v, i) { return [+cx(i).toFixed(1), +ys(v).toFixed(1)]; }) : [];
            if (ov && ovPts.length) {
                html += '<path class="' + (animate ? 'ac-draw' : '') + '" d="' + smooth(ovPts) + '" fill="none" stroke="' + ov.color + '" stroke-width="2.4" stroke-linecap="round"/>';
                ovPts.forEach(function (p, i) { html += '<circle class="' + (animate ? 'ac-pop' : '') + '" style="--i:' + i + '" cx="' + p[0] + '" cy="' + p[1] + '" r="3.2" fill="var(--admin-surface)" stroke="' + ov.color + '" stroke-width="2"/>'; });
            }
            if (opts.rotateLabels) {
                labels.forEach(function (lab, i) {
                    var t = String(lab == null ? '' : lab); if (t.length > 14) t = t.slice(0, 13) + '…';
                    html += '<text transform="translate(' + cx(i) + ',' + (H - B + 14) + ') rotate(-25)" text-anchor="end" font-size="10" fill="var(--admin-text-muted)">' + esc(t) + '</text>';
                });
            } else {
                html += xLabels(labels, cx, W, H, L, R);
            }
            svg.innerHTML = html;
            var band = svg.querySelector('.ac-band');
            return {
                W: W, H: H, n: n, empty: !all.some(function (v) { return v; }),
                indexAt: function (mx) { var i = Math.floor((mx - L) / slot); return i >= 0 && i < n ? i : -1; },
                hover: function (i) {
                    band.setAttribute('x', L + slot * i + slot * .05);
                    var peak = Math.max.apply(null, series.map(function (s) { return s.values[i] || 0; }).concat(ov ? [ov.values[i] || 0] : []));
                    return { x: cx(i), y: ys(peak) };
                },
                tooltip: function (i) {
                    var total = series.reduce(function (a, s) { return a + (s.values[i] || 0); }, 0);
                    var h = '<div class="ac-tt-title">' + esc(labels[i]) + '</div>' + series.map(function (s) { return row(s.color, s.name, fmt(s.values[i], s.format)); }).join('');
                    if (ov) h += row(ov.color, ov.name, fmt(ov.values[i], ov.format));
                    if (m === 1 && opts.share !== false) {
                        var sum = series[0].values.reduce(function (a, b) { return a + b; }, 0);
                        if (sum) h += '<div class="ac-tt-foot">' + (total / sum * 100).toFixed(1) + '% of total</div>';
                    }
                    return h;
                }
            };
        });
    }

    return { line: line, bars: bars, format: fmt, short: short };
})();
</script>
@endpush
@endonce
