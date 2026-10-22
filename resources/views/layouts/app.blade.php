<!doctype html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta

        name="viewport"

        content="width=device-width, initial-scale=1">

    <title>

        {{ $title ?? 'IT Asset Management' }}

    </title>

    {{-- Bootstrap CSS --}}

    <link

        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"

        rel="stylesheet">

    {{-- Bootstrap Icons --}}

    <link

        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"

        rel="stylesheet">

    {{-- Mobile-first responsive CSS --}}
    <link href="{{ asset('css/mobile-first.css') }}" rel="stylesheet">

    <style>
        /* =========================================================

           GLOBAL PAGE

           ========================================================= */

        html,

        body {

            max-width: 100%;

            overflow-x: clip;

        }

        body {

            background: #f5f7fb;

        }

        /* =========================================================

           SIDEBAR

           ========================================================= */

        .sidebar {

            min-height: 100vh;

            background: #111827;

            flex: 0 0 250px;

            width: 250px;

        }

        .sidebar a {

            color: #cbd5e1;

            text-decoration: none;

            display: block;

            padding: 10px 14px;

            border-radius: 8px;

            margin-bottom: 2px;

        }

        .sidebar a:hover,

        .sidebar a.active {

            background: #1f2937;

            color: white;

        }

        .brand {

            font-weight: 700;

            color: white;

        }

        /* =========================================================

           COMMON UI

           ========================================================= */

        .card-stat {

            border: 0;

            border-radius: 14px;

        }

        .table-card {

            border: 0;

            border-radius: 14px;

        }

        .navbar {

            background: white;

            border-bottom: 1px solid #e5e7eb;

        }

        /* =========================================================

           MAIN CONTENT

           ========================================================= */

        .app-main {

            min-width: 0;

            flex: 1 1 auto;

            width: calc(100% - 250px);

        }

        .app-content {

            min-width: 0;

            width: 100%;

            max-width: 100%;

            box-sizing: border-box;

        }

        /* =========================================================

   RESPONSIVE TABLE SYSTEM

   ========================================================= */

        .table-responsive {

            width: 100%;

            max-width: 100%;

            overflow-x: auto;

            overflow-y: visible;

            -webkit-overflow-scrolling: touch;

        }

        .table-responsive>.table {
            width: max-content;
            min-width: 100%;
            max-width: none;
            margin-bottom: 0;
            table-layout: auto;
        }

        .table {

            width: 100%;

            max-width: 100%;

            margin-bottom: 0;

            vertical-align: middle;

        }

        .table thead th {

            white-space: nowrap !important;

            word-break: normal !important;

            overflow-wrap: normal !important;

            vertical-align: middle;

            font-weight: 600;

        }

        .table tbody td {

            vertical-align: middle;

            white-space: normal;

            word-break: normal;

            overflow-wrap: anywhere;

        }

        .table th,

        .table td {

            padding: 12px 10px;

        }

        .table .text-truncate {

            display: block;

            width: 100%;

            max-width: 100%;

            overflow: hidden;

            text-overflow: ellipsis;

            white-space: nowrap;

        }

        .table .table-long-text {

            display: block;

            max-width: 100%;

            overflow: hidden;

            text-overflow: ellipsis;

            white-space: nowrap;

        }

        .table .table-actions {

            white-space: nowrap !important;

            word-break: normal !important;

            overflow-wrap: normal !important;

        }

        .table .table-actions .btn,

        .table .badge,

        .table .btn {

            white-space: nowrap;

        }

        /* Assets table: all 10 columns fit the available content width. */

        .assets-table {

            table-layout: fixed !important;

            width: 100% !important;

        }

        .assets-table th:nth-child(1),

        .assets-table td:nth-child(1) {

            width: 5%;

        }

        .assets-table th:nth-child(2),

        .assets-table td:nth-child(2) {

            width: 16%;

        }

        .assets-table th:nth-child(3),

        .assets-table td:nth-child(3) {

            width: 12%;

        }

        .assets-table th:nth-child(4),

        .assets-table td:nth-child(4) {

            width: 17%;

        }

        .assets-table th:nth-child(5),

        .assets-table td:nth-child(5) {

            width: 8%;

        }

        .assets-table th:nth-child(6),

        .assets-table td:nth-child(6) {

            width: 8%;

        }

        .assets-table th:nth-child(7),

        .assets-table td:nth-child(7) {

            width: 14%;

        }

        .assets-table th:nth-child(8),

        .assets-table td:nth-child(8) {

            width: 7%;

        }

        .assets-table th:nth-child(9),

        .assets-table td:nth-child(9) {

            width: 7%;

        }

        .assets-table th:nth-child(10),

        .assets-table td:nth-child(10) {

            width: 6%;

            white-space: nowrap;

        }

        @media (max-width: 992px) {
            .app-content {
                padding: 1rem !important;
            }
        }

        /* On phones, stack navigation above the page instead of squeezing content. */
        @media (max-width: 767.98px) {
            .app-shell {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
                flex: 0 0 auto;
            }

            .sidebar .brand {
                margin-bottom: 1rem !important;
            }

            .sidebar a {
                display: inline-block;
                margin: 2px 4px 2px 0;
            }

            .app-main {
                width: 100%;
                min-width: 0;
            }

            .navbar {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            .app-content {
                padding: 1rem !important;
            }
        }
    </style>

</head>

<body>

    <div class="d-flex w-100 app-shell">

        {{-- =========================================================

         SIDEBAR

         ========================================================= --}}

        <aside

            class="sidebar p-3"
            id="sidebar"
            style="width: 250px;">

            <div class="brand fs-5 mb-4">

                <i class="bi bi-pc-display me-2"></i>

                IT Asset Manager

            </div>

            <div class="small text-uppercase text-secondary mb-2">

                Main

            </div>

            {{-- Dashboard --}}

            <a

                href="{{ route('dashboard') }}"

                class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">

                <i class="bi bi-grid me-2"></i>

                Dashboard

            </a>

            {{-- Assets --}}

            <a

                href="{{ route('assets.index') }}"

                class="{{ request()->routeIs('assets.*') ? 'active' : '' }}">

                <i class="bi bi-box-seam me-2"></i>

                Assets

            </a>

            {{-- Departments --}}

            <a

                href="{{ route('departments.index') }}"

                class="{{ request()->routeIs('departments.*') ? 'active' : '' }}">

                <i class="bi bi-building me-2"></i>

                Departments

            </a>

            {{-- Categories --}}

            <a

                href="{{ route('categories.index') }}"

                class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">

                <i class="bi bi-tags me-2"></i>

                Categories

            </a>

            {{-- Employees --}}

            <a

                href="{{ route('employees.index') }}"

                class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">

                <i class="bi bi-people me-2"></i>

                Employees

            </a>

            {{-- Imports --}}

            <a

                href="{{ route('imports.index') }}"

                class="{{ request()->routeIs('imports.*') ? 'active' : '' }}">

                <i class="bi bi-file-earmark-arrow-up me-2"></i>

                Imports

            </a>

            {{-- Transfers --}}

            <a

                href="{{ route('transfers.index') }}"

                class="{{ request()->routeIs('transfers.*') ? 'active' : '' }}">

                <i class="bi bi-arrow-left-right me-2"></i>

                Transfers

            </a>

            {{-- Super Admin --}}
            @if (auth()->check() && auth()->user()->isSuperAdmin())
            <hr class="border-secondary">

            <div class="small text-uppercase text-secondary mb-2">
                Administration
            </div>

            <a href="{{ route('admin.index') }}"
                class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock me-2"></i>
                Super Admin
            </a>
            @endif



            <hr class="border-secondary">

            <div class="small text-uppercase text-secondary mb-2">

                Coming Next

            </div>

            {{-- Verification --}}

            <a href="#">

                <i class="bi bi-check2-square me-2"></i>

                Verification

            </a>

            {{-- Reports --}}

            <a href="#">

                <i class="bi bi-bar-chart me-2"></i>

                Reports

            </a>

        </aside>

        {{-- Sidebar backdrop for mobile --}}
        <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()" hidden></div>

        {{-- =========================================================

         MAIN

         ========================================================= --}}

        <main class="app-main">

            {{-- Top Navbar --}}

            <nav class="navbar px-4 py-3">

                {{-- Mobile sidebar toggle --}}
                <button class="sidebar-toggle d-lg-none me-3" type="button" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="sidebar" onclick="toggleSidebar()">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <span class="fw-semibold">

                    @yield('page-title', 'Dashboard')

                </span>


                <div class="dropdown">
                    <button
                        class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border-0"
                        type="button"
                        id="userDropdown"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-person-circle fs-5"></i>

                        <span class="text-muted">
                            {{ auth()->user()->name }}
                        </span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow"
                        aria-labelledby="userDropdown">

                        <li>
                            <div class="dropdown-item-text">
                                <strong>{{ auth()->user()->name }}</strong>
                                <br>
                                <small class="text-muted text-capitalize">
                                    {{ str_replace('_', ' ', auth()->user()->role) }}
                                </small>
                            </div>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>


            </nav>

            {{-- Page Content --}}

            <div class="app-content p-4">

                {{-- Success --}}

                @if (session('success'))

                <div

                    class="alert alert-success alert-dismissible fade show"

                    role="alert">

                    <i class="bi bi-check-circle me-1"></i>

                    {{ session('success') }}

                    <button

                        type="button"

                        class="btn-close"

                        data-bs-dismiss="alert"

                        aria-label="Close"></button>

                </div>

                @endif

                {{-- Error --}}

                @if (session('error'))

                <div

                    class="alert alert-danger alert-dismissible fade show"

                    role="alert">

                    <i class="bi bi-exclamation-triangle me-1"></i>

                    {{ session('error') }}

                    <button

                        type="button"

                        class="btn-close"

                        data-bs-dismiss="alert"

                        aria-label="Close"></button>

                </div>

                @endif

                {{-- Validation Errors --}}

                @if ($errors->any())

                <div

                    class="alert alert-danger alert-dismissible fade show"

                    role="alert">

                    <strong>

                        Please fix the following:

                    </strong>

                    <ul class="mb-0 mt-2">

                        @foreach ($errors->all() as $error)

                        <li>

                            {{ $error }}

                        </li>

                        @endforeach

                    </ul>

                    <button

                        type="button"

                        class="btn-close"

                        data-bs-dismiss="alert"

                        aria-label="Close"></button>

                </div>

                @endif

                @yield('content')

            </div>

        </main>

    </div>

    {{-- Bootstrap JavaScript --}}

    <script

        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')


    {{-- Mobile Sidebar Toggle Script --}}
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const toggle = document.querySelector('.sidebar-toggle');

            if (sidebar && backdrop) {
                const isOpen = sidebar.classList.toggle('open');
                backdrop.hidden = !isOpen;
                backdrop.classList.toggle('show', isOpen);

                if (toggle) {
                    toggle.setAttribute('aria-expanded', isOpen);
                    toggle.innerHTML = isOpen ? '<i class="bi bi-x-lg fs-4"></i>' : '<i class="bi bi-list fs-4"></i>';
                }

                // Prevent body scroll when sidebar is open
                document.body.style.overflow = isOpen ? 'hidden' : '';
            }
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
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

        // Close sidebar on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });

        // Close sidebar when clicking a link on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth < 992) {
                const sidebar = document.getElementById('sidebar');
                if (sidebar && sidebar.classList.contains('open') && e.target.closest('.sidebar a')) {
                    closeSidebar();
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                closeSidebar();
            }
        });
    </script>

</body>

</html>


{{-- Responsive table behaviour: wide data tables scroll inside their own wrappers. --}}