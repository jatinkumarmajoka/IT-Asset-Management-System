<!doctype html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>
        {{ $title ?? config('branding.org_name') . ' - ' . config('branding.product_name') }}
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

           SIDEBAR

           ========================================================= */

        .sidebar {
            min-height: 100vh;
            background: var(--sidebar-bg);
            flex: 0 0 250px;
            width: 250px;
        }

        .sidebar a {
            color: var(--sidebar-text);
            text-decoration: none;
            display: block;
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 2px;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: var(--sidebar-hover);
            color: var(--sidebar-active);
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
            border-radius: var(--card-border-radius);
        }

        .table-card {
            border: 0;
            border-radius: var(--card-border-radius);
        }

        .navbar {
            background: var(--navbar-bg);
            border-bottom: 1px solid var(--navbar-border);
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

            <div class="brand fs-5 mb-4 d-flex flex-column align-items-start gap-0">

                @if(config('branding.show_logo_in_sidebar') && config('branding.logo'))
                    <img src="{{ asset(config('branding.logo')) }}"
                         alt="{{ config('branding.logo_alt') }}"
                         class="brand-logo"
                         style="height: 32px; width: auto;">
                @else
                    <i class="bi bi-pc-display"></i>
                @endif

                <div class="brand-text ms-2">
                    <div class="brand-org fw-bold fs-5 text-white">{{ config('branding.org_name') }}</div>
                    <div class="brand-product small text-white-50">{{ config('branding.product_name') }}</div>
                </div>

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


    {{-- Global confirmation modal --}}
    <div id="app-confirm-modal" class="app-confirm-overlay" hidden>
        <section
            class="app-confirm-dialog"
            role="alertdialog"
            aria-modal="true"
            aria-labelledby="app-confirm-title"
            aria-describedby="app-confirm-message"
        >
            <div id="app-confirm-icon" class="app-confirm-icon">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 9v4m0 4h.01M10.3 3.9 2.8 17.2A2 2 0 0 0 4.5 20h15a2 2 0 0 0 1.7-2.8L13.7 3.9a2 2 0 0 0-3.4 0Z"
                        stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <h2 id="app-confirm-title">Confirm action</h2>
            <p id="app-confirm-message">Are you sure you want to continue?</p>

            <div class="app-confirm-actions">
                <button type="button" id="app-confirm-cancel" class="app-confirm-cancel">
                    Cancel
                </button>
                <button type="button" id="app-confirm-accept" class="app-confirm-accept">
                    Continue
                </button>
            </div>
        </section>
    </div>

    <style>
        .app-confirm-overlay[hidden] { display: none !important; }

        .app-confirm-overlay {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: grid;
            place-items: center;
            padding: 20px;
            background: rgba(15, 23, 42, .52);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            animation: appConfirmFade .16s ease-out;
        }

        .app-confirm-dialog {
            width: min(100%, 430px);
            padding: 28px;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            background: #fff;
            color: #0f172a;
            box-shadow: 0 24px 80px rgba(15, 23, 42, .25);
            animation: appConfirmEnter .18s ease-out;
        }

        .app-confirm-icon {
            display: grid;
            width: 48px;
            height: 48px;
            place-items: center;
            margin-bottom: 18px;
            border-radius: 14px;
            background: #fff7ed;
            color: #c2410c;
        }

        .app-confirm-icon svg { width: 25px; height: 25px; }

        #app-confirm-title {
            margin: 0;
            font-size: 21px;
            font-weight: 700;
            letter-spacing: -.025em;
        }

        #app-confirm-message {
            margin: 10px 0 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.7;
            overflow-wrap: anywhere;
        }

        .app-confirm-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 26px;
        }

        .app-confirm-actions button {
            min-height: 42px;
            padding: 10px 17px;
            border: 1px solid transparent;
            border-radius: 10px;
            font: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s ease, transform .15s ease;
        }

        .app-confirm-actions button:active { transform: scale(.98); }

        .app-confirm-cancel {
            border-color: #dbe2ea !important;
            background: #fff;
            color: #334155;
        }

        .app-confirm-cancel:hover { background: #f8fafc; }

        .app-confirm-accept {
            background: var(--theme-primary);
            color: #fff;
        }

        .app-confirm-accept:hover { background: var(--theme-primary-light); }

        .app-confirm-accept.is-danger { background: #dc2626; }
        .app-confirm-accept.is-danger:hover { background: #b91c1c; }

        @keyframes appConfirmFade {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes appConfirmEnter {
            from { opacity: 0; transform: translateY(8px) scale(.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @media (max-width: 480px) {
            .app-confirm-dialog { padding: 23px; border-radius: 16px; }
            .app-confirm-actions button { flex: 1; }
        }

        @media (prefers-reduced-motion: reduce) {
            .app-confirm-overlay, .app-confirm-dialog { animation: none; }
        }
    </style>

    <script>
    (function () {
        const modal = document.getElementById('app-confirm-modal');
        if (!modal || modal.dataset.initialized === 'true') return;
        modal.dataset.initialized = 'true';

        const title = document.getElementById('app-confirm-title');
        const message = document.getElementById('app-confirm-message');
        const icon = document.getElementById('app-confirm-icon');
        const cancel = document.getElementById('app-confirm-cancel');
        const accept = document.getElementById('app-confirm-accept');

        let pendingAction = null;
        let previousFocus = null;

        function closeModal() {
            modal.hidden = true;
            pendingAction = null;
            if (previousFocus && previousFocus.isConnected) previousFocus.focus();
        }

        function openModal(options, action) {
            previousFocus = document.activeElement;
            pendingAction = action;

            title.textContent = options.title || 'Confirm action';
            message.textContent = options.message || 'Are you sure you want to continue?';
            cancel.textContent = options.cancelText || 'Cancel';
            accept.textContent = options.confirmText || 'Continue';

            const danger = options.danger === true;
            accept.classList.toggle('is-danger', danger);
            icon.style.background = danger ? '#fef2f2' : '#fff7ed';
            icon.style.color = danger ? '#b91c1c' : '#c2410c';

            modal.hidden = false;
            cancel.focus();
        }

        cancel.addEventListener('click', closeModal);

        accept.addEventListener('click', function () {
            const action = pendingAction;
            modal.hidden = true;
            pendingAction = null;
            if (action) action();
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) closeModal();
        });

        document.addEventListener('keydown', function (event) {
            if (modal.hidden) return;

            if (event.key === 'Escape') {
                event.preventDefault();
                closeModal();
            }

            if (event.key === 'Tab') {
                event.preventDefault();
                (document.activeElement === cancel ? accept : cancel).focus();
            }
        });

        /*
         * Intercept forms whose existing inline onsubmit handler calls confirm().
         * The original confirmation is replaced by this modal. On approval,
         * the original form is submitted without rerunning the inline handler.
         */
        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) return;

            const handler = form.getAttribute('onsubmit') || '';
            if (!/\bconfirm\s*\(/i.test(handler)) return;

            event.preventDefault();
            event.stopImmediatePropagation();

            const text = form.dataset.confirmMessage || '';
            const isDelete = (form.method || 'get').toLowerCase() === 'delete'
                || form.querySelector('input[name="_method"][value="DELETE"]')
                || /delete|remove|permanent|destroy/i.test(handler + ' ' + text);

            openModal({
                title: form.dataset.confirmTitle || (
                    isDelete ? 'Delete permanently?' : 'Are you sure?'
                ),
                message: text || (
                    isDelete
                        ? 'This action may be permanent. Do you want to continue?'
                        : 'Please confirm that you want to perform this action.'
                ),
                confirmText: form.dataset.confirmButton || (
                    isDelete ? 'Delete' : 'Confirm'
                ),
                cancelText: 'Cancel',
                danger: Boolean(isDelete)
            }, function () {
                /*
                 * Native form.submit() avoids firing the intercepted submit event
                 * a second time. Laravel still receives the form's CSRF token.
                 */
                HTMLFormElement.prototype.submit.call(form);
            });
        }, true);
    })();
    </script>


</body>

</html>


 {{-- Responsive table behaviour: wide data tables scroll inside their own wrappers. --}}