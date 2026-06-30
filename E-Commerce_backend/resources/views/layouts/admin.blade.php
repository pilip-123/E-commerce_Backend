<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Laravel') }}</title>

    @include('partials.assets')

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        [x-cloak] {
            display: none !important;
        }

        :root {
            --admin-primary: #059669;
            --admin-primary-light: #ecfdf5;
            --admin-primary-dark: #047857;
            --admin-bg: #f0fdf4;
            --admin-surface: #ffffff;
            --admin-text: #1e293b;
            --admin-text-muted: #64748b;
            --admin-border: #e5e7eb;
            --sidebar-width: 250px;
            --navbar-height: 76px;
        }

        [data-theme="dark"] {
            --admin-primary: #34d399;
            --admin-primary-light: #064e3b;
            --admin-primary-dark: #6ee7b7;
            --admin-bg: #0f172a;
            --admin-surface: #1e293b;
            --admin-text: #f1f5f9;
            --admin-text-muted: #94a3b8;
            --admin-border: #334155;
        }

        body {
            background-color: var(--admin-bg);
            color: var(--admin-text);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            transition: background-color .2s ease, color .2s ease;
        }

        .app-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            transition: background-color .2s ease;
        }

        .app-wrapper {
            padding-top: var(--navbar-height);
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            top: var(--navbar-height);
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            z-index: 1020;
            overflow-y: auto;
            background-color: var(--admin-surface);
            border-color: var(--admin-border) !important;
            transition: background-color .2s ease, border-color .2s ease;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: calc(100vh - var(--navbar-height));
        }

        .sidebar .nav-link {
            color: var(--admin-text-muted);
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.15s ease;
        }

        .sidebar .nav-link:hover {
            background-color: var(--admin-primary-light);
            color: var(--admin-primary);
        }

        .sidebar .nav-link.active {
            background-color: var(--admin-primary-light);
            color: var(--admin-primary-dark);
        }

        .sidebar .nav-section {
            font-size: 0.625rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--admin-primary);
            padding: 0.75rem 0.75rem 0.25rem;
        }

        .admin-brand-title {
            color: var(--admin-text);
            line-height: 1.2;
            font-size: 0.95rem;
        }

        .admin-brand-sub {
            color: var(--admin-primary);
            line-height: 1.2;
            font-size: 0.6rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 700;
        }

        .rounded-4 {
            border-radius: 1rem !important;
        }

        .main-content {
            width: calc(100% - var(--sidebar-width));
            background-color: var(--admin-bg) !important;
            transition: background-color .2s ease;
        }

        .card,
        .modal-content,
        .dropdown-menu {
            background-color: var(--admin-surface);
            border-color: var(--admin-border) !important;
            transition: background-color .2s ease, border-color .2s ease;
        }

        .card-header {
            background-color: var(--admin-surface) !important;
            border-bottom-color: var(--admin-border) !important;
        }

        .table {
            --bs-table-bg: transparent;
            --bs-table-hover-bg: var(--admin-primary-light);
            color: var(--admin-text);
        }

        .table-light {
            --bs-table-bg: var(--admin-primary-light);
            --bs-table-color: var(--admin-text);
        }

        .table>thead {
            border-bottom-color: var(--admin-border);
        }

        .table> :not(caption)>*>* {
            border-bottom-color: var(--admin-border);
        }

        .text-muted {
            color: var(--admin-text-muted) !important;
        }

        .text-dark {
            color: var(--admin-text) !important;
        }

        .border,
        .border-top,
        .border-bottom,
        .border-start,
        .border-end {
            border-color: var(--admin-border) !important;
        }

        .bg-white {
            background-color: var(--admin-surface) !important;
        }

        .bg-light {
            background-color: var(--admin-bg) !important;
        }

        .shadow-sm {
            box-shadow: 0 1px 3px rgba(0, 0, 0, .08) !important;
        }

        [data-theme="dark"] .shadow-sm {
            box-shadow: 0 1px 3px rgba(0, 0, 0, .3) !important;
        }

        .navbar.bg-white {
            background-color: var(--admin-surface) !important;
            border-bottom-color: var(--admin-border) !important;
        }

        .navbar-nav .nav-link {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--admin-text);
            transition: all 0.15s ease;
            border-radius: 0.375rem;
        }

        .navbar-nav .nav-link:hover {
            color: var(--admin-primary);
            background-color: var(--admin-primary-light);
        }

        .navbar-nav .nav-link.active {
            background-color: var(--admin-primary-light);
            color: var(--admin-primary-dark);
        }

        .drop-zone {
            border: 2px dashed var(--admin-border);
            border-radius: 0.75rem;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: var(--admin-bg);
        }

        .drop-zone:hover,
        .drop-zone.drag-over {
            border-color: var(--admin-primary);
            background: var(--admin-primary-light);
        }

        .drop-zone.has-image {
            border-color: var(--admin-primary);
            background: var(--admin-primary-light);
        }

        .drop-zone img {
            max-height: 120px;
            max-width: 100%;
            object-fit: contain;
        }

        .btn-outline-secondary {
            --bs-btn-color: var(--admin-text);
            --bs-btn-border-color: var(--admin-border);
        }

        .btn-light {
            --bs-btn-bg: var(--admin-bg);
            --bs-btn-border-color: var(--admin-border);
            --bs-btn-color: var(--admin-text);
        }

        .form-control,
        .input-group-text {
            background-color: var(--admin-bg);
            border-color: var(--admin-border);
            color: var(--admin-text);
        }

        .form-control:focus {
            background-color: var(--admin-surface);
            border-color: var(--admin-primary);
            color: var(--admin-text);
        }

        .form-select {
            background-color: var(--admin-bg);
            border-color: var(--admin-border);
            color: var(--admin-text);
        }

        .modal-header {
            border-bottom-color: var(--admin-border);
        }

        .modal-footer {
            border-top-color: var(--admin-border);
        }

        .list-group-item {
            background-color: var(--admin-surface);
            border-color: var(--admin-border);
            color: var(--admin-text);
        }

        .page-link {
            background-color: var(--admin-surface);
            border-color: var(--admin-border);
            color: var(--admin-text);
        }

        .page-item.disabled .page-link {
            background-color: var(--admin-bg);
            border-color: var(--admin-border);
        }

        .btn-close {
            filter: var(--bs-btn-close-filter, none);
        }

        [data-theme="dark"] .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        .toast {
            background-color: var(--admin-surface);
            border-color: var(--admin-border);
        }

        .badge.bg-success-subtle {
            background-color: var(--admin-primary-light) !important;
            color: var(--admin-primary) !important;
        }

        [data-theme="dark"] .table-hover tbody tr:hover {
            --bs-table-hover-bg: var(--admin-primary-light);
        }

        @media (max-width: 991.98px) {
            .sidebar {
                display: none !important;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .navbar-collapse {
                max-height: 85vh;
                overflow-y: auto;
            }

            .navbar-collapse .navbar-nav {
                padding-top: 0.75rem;
                padding-bottom: 0.75rem;
            }

            .navbar-collapse .navbar-nav .nav-item {
                padding: 0.15rem 0;
            }

            .navbar-collapse .navbar-nav .nav-link {
                padding: 0.7rem 1rem;
                font-size: 1rem;
                border-radius: 0.5rem;
            }

            .navbar-collapse>div:last-child {
                padding: 1rem 0.5rem;
                border-top: 1px solid var(--admin-border);
                margin-top: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="app-wrapper">
        {{-- TOP NAVBAR --}}
        <nav class="app-navbar navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm py-3">
            <div class="container-fluid">
                <div class="d-flex align-items-center gap-2">
                    <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse"
                        data-bs-target="#adminNavbar" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <a href="{{ route('admin.dashboard') }}"
                        class="d-flex align-items-center gap-2 text-decoration-none">
                        <span
                            class="d-inline-flex align-items-center justify-content-center flex-shrink-0"
                            style="width: 54px; height: 54px; border-radius: 50%; overflow: hidden; background: #fff; box-shadow: 0 2px 12px rgba(0,0,0,0.08); padding: 3px;">
                            <img src="{{ asset('images/logo.png') }}" alt="E-Commerce"
                                style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                        </span>
                        <div>
                            <div class="fw-bold admin-brand-title">E-Commerce</div>
                            <div class="admin-brand-sub">Admin Panel</div>
                        </div>
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="adminNavbar"></div>

                <div class="d-flex align-items-center gap-3 flex-shrink-0 ms-auto">
                    <button type="button" id="themeToggle" class="btn btn-sm border-0 d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                        style="width: 36px; height: 36px; background: var(--admin-bg); color: var(--admin-text);"
                        title="Toggle theme">
                        <i class="bi bi-moon-fill" id="themeIcon"></i>
                    </button>
                    <a href="{{ route('admin.profile') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                        @if (auth()->user()->image_url)
                            <img src="{{ asset('storage/' . auth()->user()->image_url) }}" alt="Avatar"
                                class="rounded-circle shadow-sm flex-shrink-0 border border-2 border-success"
                                style="width: 36px; height: 36px; object-fit: cover;">
                        @else
                            <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold text-uppercase shadow-sm flex-shrink-0"
                                style="width: 36px; height: 36px; font-size: 13px; background: var(--admin-primary);">
                                {{ substr(auth()->user()->name ?? 'A', 0, 2) }}
                            </div>
                        @endif
                        <div class="d-none d-md-block lh-1">
                            <div class="fw-bold text-dark small">{{ auth()->user()->name ?? 'Administrator' }}
                            </div>
                            <div class="text-success" style="font-size: 10px; letter-spacing: 0.05em;">{{ ucfirst(auth()->user()->role) }}</div>
                        </div>
                    </a>
                    <button type="button" class="btn btn-sm fw-semibold text-white border-0 px-3"
                        data-bs-toggle="modal" data-bs-target="#logoutModal"
                        style="background: var(--admin-primary);">
                        <i class="bi bi-box-arrow-right me-1"></i>Sign Out
                    </button>
                </div>
            </div>
        </nav>

        {{-- SIDEBAR --}}
        <aside class="sidebar bg-white border-end d-none d-lg-flex flex-column py-3">
            <div class="px-3 pb-3 mb-2 border-bottom">
            </div>
            <nav class="nav flex-column px-2">
                @if (auth()->user()->hasPermission('dashboard.view'))
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i> Dashboard
                    </a>
                @endif

                @if (auth()->user()->hasPermission('products.view'))
                    <a href="{{ route('admin.products.index') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="bi bi-box"></i> Products
                    </a>
                @endif

                @if (auth()->user()->hasPermission('categories.view'))
                    <a href="{{ route('admin.categories.index') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="bi bi-grid"></i> Categories
                    </a>
                @endif

                @if (auth()->user()->hasPermission('users.view'))
                    <a href="{{ route('admin.customers') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('admin.customers') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Users
                    </a>
                @endif

                @if (auth()->user()->hasPermission('sales.view'))
                    <a href="{{ route('admin.orders.index') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i> Orders
                    </a>
                @endif

                @if (auth()->user()->hasPermission('promotions.view'))
                    <a href="{{ route('admin.promotions.index') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('admin.promotions.*') && !request()->routeIs('admin.promotions.vip-codes') ? 'active' : '' }}">
                        <i class="bi bi-percent"></i> Promotions
                    </a>
                @endif

                @if (auth()->user()->hasPermission('vipcodes.view'))
                    <a href="{{ route('admin.promotions.vip-codes') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('admin.promotions.vip-codes') ? 'active' : '' }}">
                        <i class="bi bi-lock"></i> VIP Codes
                    </a>
                @endif

                @if (auth()->user()->hasPermission('products.view'))
                    <a href="{{ route('admin.reviews.index') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                        <i class="bi bi-star"></i> Reviews
                    </a>
                @endif

                <hr class="my-2">

                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.permissions') }}"
                        class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('admin.permissions') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock"></i> Permissions
                    </a>
                @endif

                <a href="{{ route('admin.profile') }}"
                    class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i> Settings
                </a>
            </nav>
            </aside>

            {{-- MAIN CONTENT --}}
            <main class="main-content p-4 bg-light">
                @yield('content')
            </main>
    </div>

    {{-- Toast Container --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        @if (session('status'))
            <div class="toast align-items-center border-0 shadow rounded-3" role="alert" aria-live="assertive"
                aria-atomic="true" data-bs-delay="4000" data-bs-autohide="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2 fw-semibold" style="color: #047857;">
                        <i class="bi bi-check-circle-fill" style="color: #059669;"></i>
                        {{ session('status') }}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
        @if (session('success'))
            <div class="toast align-items-center border-0 shadow rounded-3" role="alert" aria-live="assertive"
                aria-atomic="true" data-bs-delay="4000" data-bs-autohide="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2 fw-semibold" style="color: #047857;">
                        <i class="bi bi-check-circle-fill" style="color: #059669;"></i>
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="toast align-items-center border-0 shadow rounded-3" role="alert" aria-live="assertive"
                aria-atomic="true" data-bs-delay="4000" data-bs-autohide="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2 fw-semibold" style="color: #dc2626;">
                        <i class="bi bi-exclamation-circle-fill" style="color: #dc2626;"></i>
                        {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
    </div>

    {{-- Logout Confirmation Modal --}}
    <div class="modal fade" id="logoutModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-body text-center py-5">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10 mb-3"
                        style="width: 64px; height: 64px;">
                        <i class="bi bi-box-arrow-right text-warning fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Sign Out</h5>
                    <p class="text-muted mb-0 small">Are you sure you want to sign out?</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4"
                        data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning px-4">
                            <i class="bi bi-box-arrow-right me-1"></i>Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-body text-center py-5">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 mb-3"
                        style="width: 64px; height: 64px;">
                        <i class="bi bi-trash3 text-danger fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Confirm Delete</h5>
                    <p class="text-muted mb-0 small">Are you sure you want to delete this item? This action cannot be
                        undone.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4"
                        data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function togglePwd(id, btn) {
        const input = document.getElementById(id);
        if (!input) return;
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        btn.innerHTML = isPassword
            ? '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
            : '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <script>
        (function() {
            var theme = localStorage.getItem('admin_theme');
            if (!theme) {
                theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-bs-theme', theme);
            document.documentElement.setAttribute('data-theme', theme);

            var icon = document.getElementById('themeIcon');
            if (icon) {
                icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
            }
        })();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var themeToggle = document.getElementById('themeToggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    var html = document.documentElement;
                    var current = html.getAttribute('data-theme');
                    var next = current === 'dark' ? 'light' : 'dark';
                    html.setAttribute('data-bs-theme', next);
                    html.setAttribute('data-theme', next);
                    localStorage.setItem('admin_theme', next);
                    var icon = document.getElementById('themeIcon');
                    if (icon) {
                        icon.className = next === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
                    }
                });
            }

            var deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;
                    var form = document.getElementById('deleteForm');
                    if (button && form) {
                        form.action = button.getAttribute('data-url');
                    }
                });
            }
            var toasts = document.querySelectorAll('.toast');
            toasts.forEach(function(t) {
                new bootstrap.Toast(t).show();
            });

            // Drag & Drop for image uploads
            document.querySelectorAll('.drop-zone').forEach(function(zone) {
                var input = zone.querySelector('input[type="file"]');
                var content = zone.querySelector('#dropContent');
                var preview = zone.querySelector('#dropPreview');
                var img = preview ? preview.querySelector('img') : null;
                var nameEl = preview ? preview.querySelector('#fileName') : null;

                if (!input) return;

                function showPreview(file) {
                    if (!file) return;
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        if (img) img.src = e.target.result;
                        if (nameEl) nameEl.textContent = file.name;
                        if (content) content.classList.add('d-none');
                        if (preview) preview.classList.remove('d-none');
                        zone.classList.add('has-image');
                    };
                    reader.readAsDataURL(file);
                }

                zone.addEventListener('click', function() {
                    input.click();
                });

                input.addEventListener('change', function() {
                    if (this.files && this.files[0]) showPreview(this.files[0]);
                });

                zone.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    zone.classList.add('drag-over');
                });

                zone.addEventListener('dragleave', function() {
                    zone.classList.remove('drag-over');
                });

                zone.addEventListener('drop', function(e) {
                    e.preventDefault();
                    zone.classList.remove('drag-over');
                    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                        input.files = e.dataTransfer.files;
                        showPreview(e.dataTransfer.files[0]);
                    }
                });
            });

            // ─── Global Notification System (works on every admin page) ───
            var notifBell = document.getElementById('notifBell');
            var notifList = document.getElementById('notifList');
            var notifBadge = document.getElementById('notifBadge');
            var markAllBtn = document.getElementById('markAllRead');

            function loadNotifications() {
                if (!notifList) return;
                fetch('{{ route('admin.notifications.index') }}')
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        var unread = data.unread_count || 0;
                        if (notifBadge) {
                            if (unread > 0) {
                                notifBadge.style.display = 'inline';
                                notifBadge.textContent = unread;
                            } else {
                                notifBadge.style.display = 'none';
                            }
                        }

                        if (!data.notifications || data.notifications.length === 0) {
                            notifList.innerHTML = '<div class="text-center text-muted py-4" style="font-size: 13px;">No notifications</div>';
                            return;
                        }

                        var html = '';
                        data.notifications.forEach(function(n) {
                            var icon = n.data.icon || 'bi-bell';
                            var title = n.data.title || 'Notification';
                            var message = n.data.message || '';
                            var url = n.data.url || '#';
                            var isUnread = n.read_at === null;
                            var bg = isUnread ? 'background:#f0fdf4;' : '';
                            html += '<a href="' + url + '" class="dropdown-item d-flex gap-2 px-3 py-2 border-bottom" style="' + bg + 'font-size: 13px;" data-id="' + n.id + '">';
                            html += '    <div><i class="' + icon + '" style="color:#059669;"></i></div>';
                            html += '    <div class="flex-grow-1 min-w-0">';
                            html += '        <div class="fw-semibold text-dark">' + title + '</div>';
                            html += '        <div class="text-muted text-truncate">' + message + '</div>';
                            html += '        <div style="font-size: 11px; color: #94a3b8;">' + n.created_at + '</div>';
                            html += '    </div>';
                            if (isUnread) {
                                html += '    <div><span class="badge bg-success rounded-pill" style="width: 8px; height: 8px; padding: 0; display: inline-block;"></span></div>';
                            }
                            html += '</a>';
                        });
                        notifList.innerHTML = html;

                        document.querySelectorAll('#notifList .dropdown-item').forEach(function(item) {
                            item.addEventListener('click', function(e) {
                                var id = this.getAttribute('data-id');
                                if (id) {
                                    fetch('{{ url('admin/notifications') }}/' + id + '/read', {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                    });
                                }
                            });
                        });
                    });
            }

            if (notifBell) {
                notifBell.addEventListener('click', function() {
                    setTimeout(loadNotifications, 100);
                });
            }

            if (markAllBtn) {
                markAllBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    fetch('{{ route('admin.notifications.readAll') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).then(function() {
                        loadNotifications();
                    });
                });
            }

            // Poll for new notifications every 30 seconds
            loadNotifications();
            setInterval(loadNotifications, 30000);
        });
    </script>
</body>

</html>
