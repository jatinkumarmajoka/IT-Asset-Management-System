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
           class="admin-nav-link {{ request('tab', 'overview') === 'overview' ? 'active' : '' }}"
           aria-current="{{ request('tab', 'overview') === 'overview' ? 'page' : 'false' }}">
            <i class="bi bi-speedometer2 me-2"></i> Overview
        </a>

        <a href="{{ route('admin.index', ['tab' => 'staff']) }}"
           class="admin-nav-link {{ request('tab') === 'staff' ? 'active' : '' }}"
           aria-current="{{ request('tab') === 'staff' ? 'page' : 'false' }}">
            <i class="bi bi-people me-2"></i> Staff Management
        </a>

        <a href="{{ route('admin.index', ['tab' => 'generate']) }}"
           class="admin-nav-link {{ request('tab') === 'generate' ? 'active' : '' }}"
           aria-current="{{ request('tab') === 'generate' ? 'page' : 'false' }}">
            <i class="bi bi-person-plus me-2"></i> Generate Account
        </a>

        <a href="{{ route('admin.index', ['tab' => 'permissions']) }}"
           class="admin-nav-link {{ request('tab') === 'permissions' ? 'active' : '' }}"
           aria-current="{{ request('tab') === 'permissions' ? 'page' : 'false' }}">
            <i class="bi bi-shield-lock me-2"></i> Permissions
        </a>

        <a href="{{ route('admin.index', ['tab' => 'requests']) }}"
           class="admin-nav-link {{ request('tab') === 'requests' ? 'active' : '' }}"
           aria-current="{{ request('tab') === 'requests' ? 'page' : 'false' }}">
            <i class="bi bi-inbox me-2"></i> Requests
        </a>

        <a href="{{ route('admin.index', ['tab' => 'audit']) }}"
           class="admin-nav-link {{ request('tab') === 'audit' ? 'active' : '' }}"
           aria-current="{{ request('tab') === 'audit' ? 'page' : 'false' }}">
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

@section('admin-header-description', 'Manage access to your IT Asset Management System.')

@section('admin-header-actions')
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-box-arrow-right me-1"></i> Sign out
    </button>
</form>
@endsection

@section('content')
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
@endsection