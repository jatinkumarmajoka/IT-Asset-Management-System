@extends('layouts.admin')

@section('admin-sidebar')
<div class="p-4">
    <a href="{{ route('admin.index') }}"
       class="text-decoration-none d-flex flex-column align-items-start gap-0 mb-4">
        @if(config('branding.show_logo_in_sidebar') && config('branding.logo'))
            <img src="{{ asset(config('branding.logo')) }}"
                 alt="{{ config('branding.logo_alt') }}"
                 class="brand-logo mb-2"
                 style="height: 30px; width: auto;">
        @else
            <i class="bi bi-pc-display fs-1"></i>
        @endif
        <div class="brand-text ms-2">
            <div class="fw-bold fs-5 text-white">{{ config('branding.org_name') }}</div>
            <div class="small text-white-50">{{ config('branding.product_name') }}</div>
        </div>
    </a>

    <p class="section-heading">SUPER ADMIN</p>

    <nav class="flex-grow-1">
        <a href="{{ route('admin.index', ['tab' => 'overview']) }}"
           class="admin-nav-link">
            <i class="bi bi-speedometer2 me-2"></i> Overview
        </a>

        <a href="{{ route('admin.index', ['tab' => 'staff']) }}"
           class="admin-nav-link active"
           aria-current="page">
            <i class="bi bi-people me-2"></i> Staff Management
        </a>

        <a href="{{ route('admin.index', ['tab' => 'generate']) }}"
           class="admin-nav-link">
            <i class="bi bi-person-plus me-2"></i> Generate Account
        </a>

        <a href="{{ route('admin.index', ['tab' => 'permissions']) }}"
           class="admin-nav-link">
            <i class="bi bi-shield-lock me-2"></i> Permissions
        </a>

        <a href="{{ route('admin.index', ['tab' => 'requests']) }}"
           class="admin-nav-link">
            <i class="bi bi-inbox me-2"></i> Requests
        </a>

        <a href="{{ route('admin.index', ['tab' => 'audit']) }}"
           class="admin-nav-link">
            <i class="bi bi-journal-text me-2"></i> Audit Logs
        </a>
    </nav>

    <a href="{{ route('dashboard') }}"
       class="admin-back-link mt-4 d-flex align-items-center justify-content-center gap-2">
        <i class="bi bi-arrow-left"></i> Back to Application
    </a>
</div>
@endsection

@section('admin-header-subtitle', 'Administration')

@section('admin-header-title', 'Super Admin Panel')

@section('admin-header-description', 'Edit Staff Account')

@section('admin-header-actions')
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-box-arrow-right me-1"></i> Sign out
    </button>
</form>
@endsection

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.index', ['tab' => 'staff']) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Staff Management
    </a>

    <h1 class="h3 fw-bold mt-3 mb-1">Edit Staff Account</h1>
    <p class="text-sm text-slate-600 mb-0">Manage the complete staff account: personal details, password, account status, and deletion.</p>
</div>

            {{-- ============================================================
                 SECTION 1: PERSONAL INFORMATION
                 ============================================================ --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i> Personal Information</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.staff.update', $user) }}" class="row g-3">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            <label for="name" class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required maxlength="255" autocomplete="name"
                                class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="255" autocomplete="email"
                                class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">This email is used for staff login and password reset.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="department_id" class="form-label fw-bold">Department <span class="text-danger">*</span></label>
                            <select id="department_id" name="department_id" required class="form-select @error('department_id') is-invalid @enderror">
                                <option value="">Select a department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}"
                                        @selected((string) old('department_id', $user->department_id) === (string) $department->id)>
                                        {{ $department->name }}
                                        @if ($department->code)
                                            ({{ $department->code }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ($departments->isEmpty())
                                <div class="form-text text-danger">No departments exist yet. Create a department before editing this account.</div>
                            @endif
                        </div>

                        <div class="col-12">
                            <hr>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Save Personal Information
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ============================================================
                 SECTION 2: PASSWORD MANAGEMENT
                 ============================================================ --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-key-fill me-2"></i> Password Management</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.staff.password.update', $user) }}" class="row g-3">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            <label for="password" class="form-label fw-bold">New Password</label>
                            <input id="password" name="password" type="password" autocomplete="new-password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="Leave blank to keep current password" minlength="12">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Leave blank to keep current password. Minimum 12 characters.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-bold">Confirm New Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                                class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirm new password">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <hr>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-key me-1"></i> Update Password
                            </button>
                        </div>
                    </form>
                    <div class="alert alert-info mt-3 mb-0 small">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Note:</strong> Leave both fields blank to keep the current password unchanged. Password changes require confirmation. The current password is never displayed for security.
                    </div>
                </div>
            </div>

            {{-- ============================================================
                 SECTION 3: ACCOUNT STATUS
                 ============================================================ --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-toggle-on me-2"></i> Account Status</h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted">Current:</span>
                        <span class="badge px-3 py-2 fs-6
                            {{ $user->account_status === 'approved' ? 'bg-success' : ($user->account_status === 'pending' ? 'bg-warning text-dark' : ($user->account_status === 'rejected' ? 'bg-danger' : 'bg-secondary')) }}">
                            {{ ucfirst($user->account_status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-8">
                            <h6 class="fw-semibold mb-1">Account Status</h6>
                            <p class="text-muted small mb-0">
                                @if($user->account_status === 'approved')
                                    The account is <strong>active</strong>. The staff member can log in and access the application based on their assigned permissions.
                                @elseif($user->account_status === 'pending')
                                    The account is <strong>pending approval</strong>. The staff member cannot log in until a Super Admin approves the account.
                                @elseif($user->account_status === 'rejected')
                                    The account has been <strong>rejected</strong>. The staff member cannot log in.
                                @elseif($user->account_status === 'inactive')
                                    The account is <strong>deactivated</strong>. The staff member cannot log in, but the account is preserved for management.
                                @else
                                    Status: {{ ucfirst($user->account_status) }}
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            @if($user->account_status === 'approved')
                                <form method="POST" action="{{ route('admin.staff.deactivate', $user) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to deactivate this staff account? The staff member will no longer be able to log in, but the account will be preserved.');">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-pause-circle me-1"></i> Deactivate Account
                                    </button>
                                </form>
                            @elseif($user->account_status === 'inactive')
                                <form method="POST" action="{{ route('admin.staff.activate', $user) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to activate this staff account? The staff member will be able to log in again.');">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-play-circle me-1"></i> Activate Account
                                    </button>
                                </form>
                            @elseif($user->account_status === 'pending')
                                <form method="POST" action="{{ route('admin.accounts.approve', $user) }}" class="d-inline" onsubmit="return confirm('Approve this staff account?');">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle me-1"></i> Approve Account
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.accounts.reject', $user) }}" class="d-inline ms-2" onsubmit="return confirm('Reject this staff account?');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-x-circle me-1"></i> Reject Account
                                    </button>
                                </form>
                            @elseif($user->account_status === 'rejected')
                                <form method="POST" action="{{ route('admin.accounts.approve', $user) }}" class="d-inline" onsubmit="return confirm('Approve this staff account?');">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle me-1"></i> Approve Account
                                    </button>
                                </form>
                            @elseif($user->account_status === 'inactive')
                                <form method="POST" action="{{ route('admin.staff.activate', $user) }}" class="d-inline" onsubmit="return confirm('Activate this staff account?');">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-play-circle me-1"></i> Activate Account
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================
                 SECTION 4: DANGER ZONE
                 ============================================================ --}}
            <div class="card shadow-sm border-0 mb-4 border-danger">
                <div class="card-header bg-white d-flex justify-content-between align-items-center bg-danger bg-opacity-10 border-danger">
                    <h5 class="mb-0 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> Danger Zone</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-8">
                            <h6 class="fw-semibold mb-1">Delete Staff Account</h6>
                            <p class="text-muted small mb-0">
                                This action is <strong>permanent and irreversible</strong>. It will permanently delete the staff account and all associated data.
                                <strong>This cannot be undone.</strong>
                            </p>
                            <ul class="text-muted small mt-2 mb-0">
                                <li>The staff member will permanently lose access to the application.</li>
                                <li>All associated records (transfers, requests, asset history) will be affected.</li>
                                <li>This action requires that no dependent records block the deletion.</li>
                            </ul>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <form method="POST" action="{{ route('admin.staff.delete', $user) }}" class="d-inline" onsubmit="return confirm('WARNING: This will permanently delete the staff account. This action cannot be undone. Are you absolutely sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash me-1"></i> Delete Account
                                </button>
                            </form>
                        </div>
                    </div>
                    @if ($errors->has('user'))
                        <div class="alert alert-danger mt-3 mb-0 small">
                            {{ $errors->first('user') }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Current Status Badge for Quick Reference --}}
            <div class="row g-3">
                <div class="col-12">
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge px-3 py-2 fs-6
                            {{ $user->account_status === 'approved' ? 'bg-success' : ($user->account_status === 'pending' ? 'bg-warning text-dark' : ($user->account_status === 'rejected' ? 'bg-danger' : 'bg-secondary')) }}">
                            <i class="bi bi-circle-fill me-1"></i> Account: {{ ucfirst($user->account_status) }}
                        </span>
                        <span class="badge bg-primary px-3 py-2 fs-6">
                            <i class="bi bi-person-badge me-1"></i> Role: Staff
                        </span>
                        <span class="badge bg-secondary px-3 py-2 fs-6">
                            <i class="bi bi-building me-1"></i> {{ $user->department?->name ?? 'No Department' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

@push('scripts')
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
@endpush