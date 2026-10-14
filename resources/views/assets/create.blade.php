@extends('layouts.app')

@section('page-title', 'Create Asset')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="mb-1">Create Asset</h2>
        <p class="text-muted mb-0">Add a new asset to the departmental stock register.</p>
    </div>

    <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>
        Back to Assets
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please fix the following:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@php
    $currentUser = auth()->user();
    $isSuperAdmin = $currentUser
        && method_exists($currentUser, 'isSuperAdmin')
        && $currentUser->isSuperAdmin();

    $canManageAssets = $isSuperAdmin
        || ($currentUser
            && method_exists($currentUser, 'canManageModule')
            && $currentUser->canManageModule('assets'));

    $canManageBills = $isSuperAdmin
        || ($currentUser
            && method_exists($currentUser, 'canManageModule')
            && $currentUser->canManageModule('bills'));

    $asset = $asset ?? null;
    $isEdit = false;
@endphp

<div class="card shadow-sm border-0">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-box-seam me-2"></i>
            Asset Information
        </h5>
    </div>

    <div class="card-body">
        <form
            method="POST"
            action="{{ route('assets.store') }}"
            enctype="multipart/form-data"
            novalidate
        >
            @csrf

            @include('assets._form', [
                'asset' => $asset,
                'isEdit' => false,
                'canManageAssets' => $canManageAssets,
                'canManageBills' => $canManageBills,
                'buttonText' => 'Create Asset',
            ])

        </form>
    </div>
</div>
@endsection
