<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        {{ $title ?? 'IT Asset Management' }}
    </title>


    {{-- Bootstrap CSS --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    {{-- Bootstrap Icons --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >


    {{-- Application Styles --}}
    <style>

        body {
            background: #f5f7fb;
        }

        .sidebar {
            min-height: 100vh;
            background: #111827;
        }

        .sidebar a {
            color: #cbd5e1;
            text-decoration: none;
            display: block;
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 2px;
        }

        .sidebar a:hover {
            background: #1f2937;
            color: white;
        }

        .brand {
            font-weight: 700;
            color: white;
        }

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

    </style>

</head>


<body>

    <div class="d-flex">


        {{-- ===================================================== --}}
        {{-- Sidebar --}}
        {{-- ===================================================== --}}

        <aside
            class="sidebar p-3"
            style="width: 250px;"
        >

            {{-- Brand --}}
            <div class="brand fs-5 mb-4">

                <i class="bi bi-pc-display me-2"></i>

                IT Asset Manager

            </div>


            {{-- Main Menu --}}
            <div class="small text-uppercase text-secondary mb-2">
                Main
            </div>


            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}">

                <i class="bi bi-grid me-2"></i>

                Dashboard

            </a>


            {{-- Assets --}}
            <a href="{{ route('assets.index') }}">

                <i class="bi bi-box-seam me-2"></i>

                Assets

            </a>


            {{-- Departments --}}
            <a href="{{ route('departments.index') }}">

                <i class="bi bi-building me-2"></i>

                Departments

            </a>


            {{-- Categories --}}
            <a href="{{ route('categories.index') }}">

                <i class="bi bi-tags me-2"></i>

                Categories

            </a>


            {{-- Employees --}}
            <a href="{{ route('employees.index') }}">

                <i class="bi bi-people me-2"></i>

                Employees

            </a>


            <hr class="border-secondary">


            {{-- Coming Next --}}
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


            {{-- Imports --}}
            <a href="#">

                <i class="bi bi-file-earmark-arrow-up me-2"></i>

                Imports

            </a>


            {{-- Reports --}}
            <a href="#">

                <i class="bi bi-bar-chart me-2"></i>

                Reports

            </a>

        </aside>


        {{-- ===================================================== --}}
        {{-- Main Content --}}
        {{-- ===================================================== --}}

        <main class="flex-grow-1">


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
            <div class="p-4">


                {{-- Success Message --}}
                @if (session('success'))

                    <div
                        class="alert alert-success alert-dismissible fade show"
                        role="alert"
                    >

                        <i class="bi bi-check-circle me-1"></i>

                        {{ session('success') }}

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close"
                        ></button>

                    </div>

                @endif


                {{-- Error Message --}}
                @if (session('error'))

                    <div
                        class="alert alert-danger alert-dismissible fade show"
                        role="alert"
                    >

                        <i class="bi bi-exclamation-triangle me-1"></i>

                        {{ session('error') }}

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close"
                        ></button>

                    </div>

                @endif


                {{-- Validation Errors --}}
                @if ($errors->any())

                    <div
                        class="alert alert-danger alert-dismissible fade show"
                        role="alert"
                    >

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
                            aria-label="Close"
                        ></button>

                    </div>

                @endif


                {{-- Page Content --}}
                @yield('content')

            </div>

        </main>

    </div>


    {{-- ===================================================== --}}
    {{-- Bootstrap JavaScript --}}
    {{-- ===================================================== --}}

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>


    {{-- Page-Specific JavaScript --}}
    @stack('scripts')

</body>

</html>