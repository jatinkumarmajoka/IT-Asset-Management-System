
@extends('layouts.app')

@section('page-title', 'Add Asset')

@section('content')
    @php
        $canManageAssets = auth()->user()->isSuperAdmin()
            || auth()->user()->canManageModule('assets');
    @endphp

    <div class="card shadow-sm table-card">
        <div class="card-body">
            <h3 class="mb-4">Add Asset</h3>

            @if (!$canManageAssets)
                <div class="alert alert-info">
                    You have view-only access to Assets.
                    You cannot create assets.
                </div>

                <a href="{{ route('assets.index') }}"
                   class="btn btn-secondary">
                    Back to Assets
                </a>
            @else
                <form method="POST" action="{{ route('assets.store') }}">
                    @csrf

                    @include('assets._form', [
                        'buttonText' => 'Create Asset'
                    ])
                </form>
            @endif
        </div>
    </div>
@endsection
