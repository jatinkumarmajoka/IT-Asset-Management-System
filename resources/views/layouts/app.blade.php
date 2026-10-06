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

    <style>
        /* =========================================================
           GLOBAL PAGE
           ========================================================= */

        html,
        body {
            max-width: 100%;
            overflow-x: hidden;
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
    overflow-x: hidden;
}

/* Every table gets its own horizontal scrolling area.
   It will NEVER break the page layout. */
.table-responsive {
    display: block;
    width: 100%;
    max-width: 100%;
    overflow-x: auto !important;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
}

/* Keep table columns readable instead of crushing them */
.table-responsive > table {
    width: 100%;
    min-width: 1050px;
    margin-bottom: 0;
}

/* Never let long text destroy the table */
.table-responsive th,
.table-responsive td {
    vertical-align: middle;
    white-space: normal;
    overflow-wrap: anywhere;
    word-break: normal;
}

/* Table headings stay on one line */
.table-responsive thead th {
    white-space: nowrap;
    font-weight: 600;
}

/* Better spacing */
.table-responsive .table {
    margin-bottom: 0;
}


        /* =========================================================
           GLOBAL TABLE
           ========================================================= */

        .table {
            width: 100%;
            margin-bottom: 0;
            vertical-align: middle;
        }

        /*
         * IMPORTANT:
         * Table headers must NEVER break words.
         *
         * Description stays "Description"
         * Assets stays "Assets"
         * Uploaded By stays "Uploaded By"
         */
        .table thead th {
            white-space: nowrap !important;
            word-break: normal !important;
            overflow-wrap: normal !important;
            vertical-align: middle;
        }

        .table tbody td {
            vertical-align: middle;
            word-break: normal;
            overflow-wrap: normal;
        }

        .table th,
        .table td {
            padding: 12px 14px;
        }


        /* =========================================================
           LONG FILE NAMES / LONG TEXT
           ========================================================= */

        /*
         * Long filenames should be shortened with ...
         * instead of destroying the table layout.
         */
        .table .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /*
         * Any element explicitly marked as a long-text cell
         * gets ellipsis instead of pushing the page wider.
         */
        .table .table-long-text {
            max-width: 280px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }


        /* =========================================================
           TABLE ACTIONS
           ========================================================= */

        .table .table-actions {
            white-space: nowrap !important;
            word-break: normal !important;
            overflow-wrap: normal !important;
        }

        .table .table-actions .btn {
            white-space: nowrap;
        }


        /* =========================================================
           TABLE BADGES
           ========================================================= */

        .table .badge {
            white-space: nowrap;
        }


        /* =========================================================
           RESPONSIVE TABLE
           ========================================================= */

        @media (max-width: 992px) {

            .sidebar {
                flex-basis: 220px;
                width: 220px;
            }

            .app-content {
                padding: 1rem !important;
            }

        }
    </style>

</head>


<body>

    <div class="d-flex w-100">


        {{-- =========================================================
         SIDEBAR
         ========================================================= --}}

        <aside
            class="sidebar p-3"
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


            <hr class="border-secondary">


            <div class="small text-uppercase text-secondary mb-2">
                Coming Next
            </div>


            {{-- Verification --}}
            <a href="#">

                <i class="bi bi-check2-square me-2"></i>

                Verification

            </a>


            {{-- Transfers --}}
            <a href="#">

                <i class="bi bi-arrow-left-right me-2"></i>

                Transfers

            </a>


            {{-- Reports --}}
            <a href="#">

                <i class="bi bi-bar-chart me-2"></i>

                Reports

            </a>

        </aside>


        {{-- =========================================================
         MAIN
         ========================================================= --}}

        <main class="app-main">


            {{-- Top Navbar --}}
            <nav class="navbar px-4 py-3">

                <span class="fw-semibold">

                    @yield('page-title', 'Dashboard')

                </span>


                <span class="text-muted">

                    <i class="bi bi-person-circle me-1"></i>

                    Administrator

                </span>

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

</body>

</html>