@extends('layouts.app')

@section('page-title', 'Super Admin')

@section('content')
<div class="d-flex flex-column flex-md-row min-vh-100">
    {{-- Admin Sidebar --}}
    <aside class="admin-sidebar d-none d-md-block border-end" style="width: 260px; background: var(--sidebar-bg); min-height: 100vh;">
        <div class="p-4">
            <a href="{{ route('admin.index') }}" class="text-lg font-bold text-white d-flex flex-column align-items-start gap-0 text-decoration-none">
                @if(config('branding.show_logo_in_sidebar') && config('branding.logo'))
                    <img src="{{ asset(config('branding.logo')) }}"
                         alt="{{ config('branding.logo_alt') }}"
                         class="brand-logo mb-2"
                         style="height: 28px; width: auto;">
                @else
                    <i class="bi bi-pc-display fs-1"></i>
                @endif
                <div class="brand-text">
                    <div class="fw-bold fs-5 text-white">{{ config('branding.org_name') }}</div>
                    <div class="small text-slate-400">{{ config('branding.product_name') }}</div>
                </div>
            </a>

            <p class="mt-2 text-xs text-slate-400">SUPER ADMIN</p>

            <nav class="mt-4">
                <a href="{{ route('admin.index', ['tab' => 'overview']) }}"
                    class="d-block rounded px-3 py-2 mb-1 text-white-50 hover:text-white hover:bg-slate-700/50 transition
                           {{ request('tab', 'overview') === 'overview' ? 'bg-slate-700/50 fw-semibold' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Overview
                </a>

                <a href="{{ route('admin.index', ['tab' => 'staff']) }}"
                    class="d-block rounded px-3 py-2 mb-1 text-white-50 hover:text-white hover:bg-slate-700/50 transition
                           {{ request('tab') === 'staff' ? 'bg-slate-700/50 fw-semibold' : '' }}">
                    <i class="bi bi-people me-2"></i> Staff Management
                </a>

                <a href="{{ route('admin.index', ['tab' => 'generate']) }}"
                    class="d-block rounded px-3 py-2 mb-1 text-white-50 hover:text-white hover:bg-slate-700/50 transition
                           {{ request('tab') === 'generate' ? 'bg-slate-700/50 fw-semibold' : '' }}">
                    <i class="bi bi-person-plus me-2"></i> Generate Account
                </a>

                <a href="{{ route('admin.index', ['tab' => 'permissions']) }}"
                    class="d-block rounded px-3 py-2 mb-1 text-white-50 hover:text-white hover:bg-slate-700/50 transition
                           {{ request('tab') === 'permissions' ? 'bg-slate-700/50 fw-semibold' : '' }}">
                    <i class="bi bi-shield-lock me-2"></i> Permissions
                </a>

                <a href="{{ route('admin.index', ['tab' => 'requests']) }}"
                    class="d-block rounded px-3 py-2 mb-1 text-white-50 hover:text-white hover:bg-slate-700/50 transition
                           {{ request('tab') === 'requests' ? 'bg-slate-700/50 fw-semibold' : '' }}">
                    <i class="bi bi-inbox me-2"></i> Requests
                </a>

                <a href="{{ route('admin.index', ['tab' => 'audit']) }}"
                    class="d-block rounded px-3 py-2 mb-1 text-white-50 hover:text-white hover:bg-slate-700/50 transition
                           {{ request('tab') === 'audit' ? 'bg-slate-700/50 fw-semibold' : '' }}">
                    <i class="bi bi-journal-text me-2"></i> Audit Logs
                </a>
            </nav>

            <a href="{{ route('dashboard') }}"
                class="mt-4 d-block rounded px-3 py-2 text-center small border border-slate-600 hover:bg-slate-700 transition">
                &larr; Back to application
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 min-vh-100">
        <header class="bg-white border-bottom px-4 py-3 d-flex flex-wrap align-items-center justify-content-between gap-3 sticky-top">
            <button class="sidebar-toggle d-lg-none me-3 p-2 rounded border bg-white" type="button"
                    onclick="toggleAdminSidebar()" aria-label="Toggle navigation" aria-expanded="false">
                <i class="bi bi-list fs-4"></i>
            </button>

            <div class="flex-1">
                <p class="text-sm text-slate-500 mb-0">Administration</p>
                <h1 class="h4 fw-bold mb-0" style="color: var(--theme-primary);">Super Admin Panel</h1>
                <p class="text-sm text-slate-500 mb-0">Manage access to your IT Asset Management System.</p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-box-arrow-right me-1"></i> Sign out
                </button>
            </form>
        </header>

        <div class="p-4">
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

            @php
                $tab = request('tab', 'overview');
                $validTabs = ['overview', 'staff', 'generate', 'permissions', 'requests', 'audit'];
                if (!in_array($tab, $validTabs)) $tab = 'overview';
            @endphp

            <!-- Mobile Tab Navigation -->
            <nav class="mb-4 d-md-none overflow-x-auto pb-2">
                @foreach(['overview' => 'Overview', 'staff' => 'Staff', 'generate' => 'Generate', 'permissions' => 'Permissions', 'requests' => 'Requests', 'audit' => 'Audit'] as $key => $label)
                    <a href="{{ route('admin.index', ['tab' => $key]) }}"
                        class="btn btn-sm px-3 py-2 me-2 rounded-pill white-space-nowrap
                               {{ $tab === $key ? 'btn-primary' : 'btn-outline-secondary' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            @if($tab === 'overview')
                @include('admin.partials.overview')
            @elseif($tab === 'staff')
                @include('admin.partials.staff')
            @elseif($tab === 'generate')
                @include('admin.partials.generate')
            @elseif($tab === 'permissions')
                @include('admin.partials.permissions')
            @elseif($tab === 'requests')
                @include('admin.partials.requests')
            @elseif($tab === 'audit')
                @include('admin.partials.audit')
            @endif
        </div>
    </main>

    <!-- Mobile Sidebar Backdrop -->
    <div class="sidebar-backdrop d-lg-none" id="adminSidebarBackdrop" onclick="closeAdminSidebar()" hidden></div>
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