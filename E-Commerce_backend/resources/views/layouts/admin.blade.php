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
            --sidebar-width: 250px;
            --navbar-height: 76px;
        }

        body {
            background-color: var(--admin-bg);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .app-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
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
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: calc(100vh - var(--navbar-height));
        }

        .sidebar .nav-link {
            color: #334155;
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
            color: #000;
        }

        .sidebar .nav-section {
            font-size: 0.625rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--admin-primary);
            padding: 0.75rem 0.75rem 0.25rem;
        }

        .rounded-4 {
            border-radius: 1rem !important;
        }

        .main-content {
            width: calc(100% - var(--sidebar-width));
        }

        .drop-zone {
            border: 2px dashed #d1d5db;
            border-radius: 0.75rem;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: #f9fafb;
        }

        .drop-zone:hover,
        .drop-zone.drag-over {
            border-color: #059669;
            background: #ecfdf5;
        }

        .drop-zone.has-image {
            border-color: #059669;
            background: #f0fdf4;
        }

        .drop-zone img {
            max-height: 120px;
            max-width: 100%;
            object-fit: contain;
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
                border-top: 1px solid #e5e7eb;
                margin-top: 0.5rem;
            }
        }

        .navbar-nav .nav-link {
            font-size: 0.875rem;
            font-weight: 600;
            color: #000;
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
                            class="d-inline-flex align-items-center justify-content-center rounded-3 text-white flex-shrink-0"
                            style="width: 36px; height: 36px; background: linear-gradient(135deg, #059669, #047857); box-shadow: 0 3px 10px rgba(5,150,105,0.35);">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                        </span>
                        <div>
                            <div class="fw-bold" style="color: #1e293b; line-height: 1.2; font-size: 0.95rem;">
                                E-Commerce</div>
                            <div
                                style="color: #059669; line-height: 1.2; font-size: 0.6rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700;">
                                Admin Panel</div>
                        </div>
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="adminNavbar">
                    <div class="d-flex align-items-center gap-3 flex-shrink-0 ms-auto">
                        <div class="d-flex align-items-center gap-2">
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
                                <div class="text-success" style="font-size: 10px; letter-spacing: 0.05em;">Admin</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm fw-semibold text-white border-0 px-3"
                            data-bs-toggle="modal" data-bs-target="#logoutModal"
                            style="background: var(--admin-primary);">
                            <i class="bi bi-box-arrow-right me-1"></i>Sign Out
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        {{-- SIDEBAR --}}
        <aside class="sidebar bg-white border-end d-none d-lg-flex flex-column py-3">
            <div class="px-3 pb-3 mb-2 border-bottom">
            </div>
            <nav class="nav flex-column px-2">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-graph-up"></i> Dashboard
                </a>

                <a href="{{ route('admin.products.index') }}"
                    class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="bi bi-box"></i> Products
                </a>

                <a href="{{ route('admin.categories.index') }}"
                    class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-grid"></i> Categories
                </a>

                <a href="{{ route('admin.customers') }}"
                    class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('admin.customers') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Users
                </a>

                <a href="{{ route('admin.orders.index') }}"
                    class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> Orders
                </a>

                <a href="{{ route('admin.promotions.index') }}"
                    class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
                    <i class="bi bi-percent"></i> Promotions
                </a>

                <a href="{{ route('admin.reviews.index') }}"
                    class="nav-link d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <i class="bi bi-star"></i> Reviews
                </a>

                <hr class="my-2">

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
</body>

</html>
