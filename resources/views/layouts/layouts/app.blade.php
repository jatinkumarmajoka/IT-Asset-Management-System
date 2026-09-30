<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'IT Asset Management' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body{background:#f5f7fb}.sidebar{min-height:100vh;background:#111827}.sidebar a{color:#cbd5e1;text-decoration:none;display:block;padding:10px 14px;border-radius:8px}.sidebar a:hover{background:#1f2937;color:white}.brand{font-weight:700;color:white}.card-stat{border:0;border-radius:14px}.table-card{border:0;border-radius:14px}.navbar{background:white;border-bottom:1px solid #e5e7eb}
    </style>
</head>
<body>
<div class="d-flex">
    <aside class="sidebar p-3" style="width:250px">
        <div class="brand fs-5 mb-4"><i class="bi bi-pc-display me-2"></i>IT Asset Manager</div>
        <div class="small text-uppercase text-secondary mb-2">Main</div>
        <a href="{{ route('dashboard') }}"><i class="bi bi-grid me-2"></i>Dashboard</a>
        <a href="{{ route('assets.index') }}"><i class="bi bi-box-seam me-2"></i>Assets</a>
        <a href="{{ route('departments.index') }}"><i class="bi bi-building me-2"></i>Departments</a>
        <a href="{{ route('categories.index') }}"><i class="bi bi-tags me-2"></i>Categories</a>
        <a href="{{ route('employees.index') }}"><i class="bi bi-people me-2"></i>Employees</a>
        <hr class="border-secondary">
        <div class="small text-uppercase text-secondary mb-2">Coming next</div>
        <a href="#"><i class="bi bi-check2-square me-2"></i>Verification</a>
        <a href="#"><i class="bi bi-arrow-left-right me-2"></i>Transfers</a>
        <a href="#"><i class="bi bi-file-earmark-arrow-up me-2"></i>Imports</a>
        <a href="#"><i class="bi bi-bar-chart me-2"></i>Reports</a>
    </aside>

    <main class="flex-grow-1">
        <nav class="navbar px-4 py-3">
            <span class="fw-semibold">@yield('page-title','Dashboard')</span>
            <span class="text-muted"><i class="bi bi-person-circle me-1"></i> Administrator</span>
        </nav>

        <div class="p-4">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
