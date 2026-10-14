@extends('layouts.app')

@section('page-title', 'Add Department')

@section('content')
    @php
        $canManageDepartments = auth()->user()->isSuperAdmin()
            || auth()->user()->canManageModule('departments');
    @endphp

    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="mb-3">Add Department</h3>

            @if (!$canManageDepartments)
                <div class="alert alert-info">
                    You have view-only access to Departments. You cannot create departments.
                </div>

                <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                    Back to Departments
                </a>
            @else
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Please correct the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('departments.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Department Name</label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                class="form-control"
                                value="{{ old('name') }}"
                                maxlength="150"
                                required
                                autofocus
                            >
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="code" class="form-label">Department Code</label>
                            <input
                                id="code"
                                name="code"
                                type="text"
                                class="form-control"
                                value="{{ old('code') }}"
                                maxlength="50"
                                required
                            >
                            <div class="form-text">Use a unique code, for example CSE or ADMIN.</div>
                            @error('code')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="type" class="form-label">Department Type</label>
                            <select id="type" name="type" class="form-select" required>
                                <option value="academic" @selected(old('type', 'academic') === 'academic')>
                                    Teaching / Academic
                                </option>
                                <option value="non_teaching" @selected(old('type') === 'non_teaching')>
                                    Non-Teaching / Administrative
                                </option>
                            </select>
                            @error('type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-select" required>
                                <option value="active" @selected(old('status', 'active') === 'active')>
                                    Active
                                </option>
                                <option value="inactive" @selected(old('status') === 'inactive')>
                                    Inactive
                                </option>
                            </select>
                            @error('status')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea
                                id="description"
                                name="description"
                                class="form-control"
                                rows="3"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Create Department</button>
                        <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
