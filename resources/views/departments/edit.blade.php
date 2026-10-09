
@extends('layouts.app')

@section('page-title', 'Edit Department')

@section('content')
    @php
        $canManageDepartments = auth()->user()->isSuperAdmin()
            || auth()->user()->canManageModule('departments');
    @endphp

    <div class="card shadow-sm">
        <div class="card-body">
            <h3>Edit Department</h3>

            @if (!$canManageDepartments)
                <div class="alert alert-info">
                    You have view-only access to Departments.
                    You cannot edit departments.
                </div>

                <a href="{{ route('departments.index') }}"
                   class="btn btn-secondary">
                    Back to Departments
                </a>
            @else
                <form method="POST"
                      action="{{ route('departments.update', $department) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Name</label>
                            <input
                                id="name"
                                name="name"
                                class="form-control"
                                value="{{ old('name', $department->name) }}"
                                required
                            >
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="code" class="form-label">Code</label>
                            <input
                                id="code"
                                name="code"
                                class="form-control"
                                value="{{ old('code', $department->code) }}"
                                required
                            >
                            @error('code')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-select">
                                <option value="active" @selected(old('status', $department->status) === 'active')>
                                    Active
                                </option>
                                <option value="inactive" @selected(old('status', $department->status) === 'inactive')>
                                    Inactive
                                </option>
                            </select>
                            @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea
                                id="description"
                                name="description"
                                class="form-control"
                                rows="3"
                            >{{ old('description', $department->description) }}</textarea>
                            @error('description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">
                        Save
                    </button>
                </form>
            @endif
        </div>
    </div>
@endsection
