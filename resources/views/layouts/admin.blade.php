<!doctype html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>
        {{ $title ?? config('branding.org_name') . ' - Super Admin' }}
    </title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Bootstrap CSS --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">

    {{-- SweetAlert2 --}}
    <link
        href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"
        rel="stylesheet">

    {{-- Mobile-first responsive CSS --}}
    <link href="{{ asset('css/mobile-first.css') }}" rel="stylesheet">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --theme-primary: #08203C;
            --theme-primary-light: #0d2f56;
            --theme-primary-lighter: #123d6e;
            --theme-primary-rgb: 8, 32, 60;
            --sidebar-bg: #08203C;
            --sidebar-hover: #0d2f56;
            --sidebar-text: #cbd5e1;
            --sidebar-active: #ffffff;
            --navbar-bg: #ffffff;
            --navbar-border: #e5e7eb;
            --body-bg: #f5f7fb;
            --card-border-radius: 14px;
            --focus-ring: rgba(8, 32, 60, 0.25);
        }

        /* =========================================================

           GLOBAL PAGE

           ========================================================= */

        html,
        body {
            max-width: 100%;
            overflow-x: clip;
        }

        body {
            background: var(--body-bg);
        }

        /* =========================================================

           ADMIN SHELL (matches .app-shell pattern)

           ========================================================= */

        .admin-shell {
            display: flex;
            width: 100%;
        }

        .admin-sidebar {
            flex: 0 0 260px;
            width: 260px;
            min-width: 260px;
            min-height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid rgba(255,255,255,0.1);
        }

        .admin-main {
            flex: 1 1 auto;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .admin-header {
            background: var(--navbar-bg);
            border-bottom: 1px solid var(--navbar-border);
        }

        .admin-content {
            flex: 1;
            min-width: 0;
        }

        /* =========================================================

           ADMIN SIDEBAR

           ========================================================= */

        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 4px;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .admin-nav-link:hover,
        .admin-nav-link.active {
            background: var(--sidebar-hover);
            color: var(--sidebar-active);
        }

        .admin-back-link {
            color: var(--sidebar-text);
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .admin-back-link:hover {
            color: var(--sidebar-active);
        }

        .section-heading {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--sidebar-text);
            opacity: 0.7;
            margin-bottom: 8px;
        }

        .sidebar-toggle {
            background: var(--navbar-bg);
            border: 1px solid var(--navbar-border);
        }

        /* =========================================================

           SIDEBAR BACKDROP (for mobile drawer)

           ========================================================= */

        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1030;
            background: rgba(15, 23, 42, .5);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
        }

        .sidebar-backdrop[hidden] {
            display: none !important;
        }

        .sidebar-backdrop.show {
            display: block !important;
        }

        /* =========================================================

           RESPONSIVE (matches .app-shell pattern)

           ========================================================= */

        @media (max-width: 767.98px) {
            .admin-shell {
                flex-direction: column;
            }

            .admin-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                z-index: 1040;
                width: 260px;
                min-width: 260px;
                max-width: 85vw;
                transform: translateX(-100%);
                transition: transform 0.25s ease;
                min-height: auto;
            }

            .admin-sidebar.open {
                transform: translateX(0);
            }

            .admin-main {
                width: 100%;
            }

            .admin-content {
                padding: 1rem !important;
            }
        }

        /* =========================================================

           COMMON UI

           ========================================================= */

        .card-stat {
            border: 0;
            border-radius: var(--card-border-radius);
        }

        .table-card {
            border: 0;
            border-radius: var(--card-border-radius);
        }

        /* Focus visible for accessibility */
        :focus-visible {
            outline: 3px solid var(--theme-primary) !important;
            outline-offset: 2px !important;
        }

        .btn:focus-visible,
        .form-control:focus-visible,
        .form-select:focus-visible,
        .dropdown-item:focus-visible,
        .nav-link:focus-visible,
        .pagination .page-link:focus-visible {
            outline: 3px solid var(--theme-primary) !important;
            outline-offset: 2px !important;
        }

    </style>

    @stack('admin-head')

</head>

<body>

    <div class="admin-shell">
        {{-- Admin Sidebar --}}
        <aside class="admin-sidebar"
               id="adminSidebar">
            @yield('admin-sidebar')
        </aside>

        {{-- Sidebar backdrop for mobile --}}
        <div class="sidebar-backdrop d-lg-none" id="adminSidebarBackdrop" onclick="closeAdminSidebar()" hidden></div>

        {{-- Main Content --}}
        <main class="admin-main flex-grow-1 min-w-0 d-flex flex-column">
            {{-- Admin Header --}}
            <header class="admin-header bg-white border-bottom px-4 py-3 d-flex flex-wrap align-items-center justify-content-between gap-3 sticky-top">
                {{-- Mobile sidebar toggle --}}
                <button class="sidebar-toggle d-lg-none me-3 p-2 rounded border bg-white" type="button"
                        onclick="toggleAdminSidebar()" aria-label="Toggle navigation" aria-expanded="false" aria-controls="adminSidebar">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <div class="flex-1 min-w-0">
                    <p class="text-sm text-slate-500 mb-0">@yield('admin-header-subtitle', 'Administration')</p>
                    <h1 class="h4 fw-bold mb-0" style="color: var(--theme-primary);">@yield('admin-header-title', 'Super Admin Panel')</h1>
                    <p class="text-sm text-slate-500 mb-0">@yield('admin-header-description', 'Manage access to your IT Asset Management System.')</p>
                </div>

                @yield('admin-header-actions')
            </header>

            {{-- Page Content --}}
            <div class="admin-content flex-grow-1 p-4" style="min-width: 0;">
                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    {{-- Bootstrap JavaScript --}}
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')

    {{-- Admin Sidebar Toggle Script --}}
    <script>
        function toggleAdminSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('adminSidebarBackdrop');
            const toggle = document.querySelector('.sidebar-toggle');

            if (sidebar && backdrop) {
                const isOpen = sidebar.classList.toggle('open');
                backdrop.hidden = !isOpen;
                backdrop.classList.toggle('show', isOpen);

                if (toggle) {
                    toggle.setAttribute('aria-expanded', isOpen);
                    toggle.innerHTML = isOpen ? '<i class="bi bi-x-lg fs-4"></i>' : '<i class="bi bi-list fs-4"></i>';
                }

                document.body.style.overflow = isOpen ? 'hidden' : '';
            }
        }

        function closeAdminSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('adminSidebarBackdrop');
            const toggle = document.querySelector('.sidebar-toggle');

            if (sidebar && backdrop) {
                sidebar.classList.remove('open');
                backdrop.hidden = true;
                backdrop.classList.remove('show');

                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                    toggle.innerHTML = '<i class="bi bi-list fs-4"></i>';
                }

                document.body.style.overflow = '';
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeAdminSidebar();
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) closeAdminSidebar();
        });
    </script>

    @stack('admin-scripts')

</body>

</html>