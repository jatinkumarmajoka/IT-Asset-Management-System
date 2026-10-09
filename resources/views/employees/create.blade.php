
@extends('layouts.app')

@section('page-title', 'Add Employee')

@section('content')
    @php
        $canManageEmployees = auth()->user()->isSuperAdmin()
            || auth()->user()->canManageModule('employees');
    @endphp

    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="mb-4">Add Employee</h3>

            @if (!$canManageEmployees)
                <div class="alert alert-info">
                    You have view-only access to Employees.
                    You cannot create employees.
                </div>

                <a href="{{ route('employees.index') }}"
                   class="btn btn-secondary">
                    Back to Employees
                </a>
            @else
                <form method="POST" action="{{ route('employees.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="employee_code" class="form-label">
                                Employee Code
                            </label>
                            <input
                                id="employee_code"
                                name="employee_code"
                                class="form-control"
                                value="{{ old('employee_code') }}"
                                required
                            >
                            @error('employee_code')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name</label>
                            <input
                                id="name"
                                name="name"
                                class="form-control"
                                value="{{ old('name') }}"
                                required
                            >
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email') }}"
                            >
                            @error('email')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input
                                id="phone"
                                name="phone"
                                class="form-control"
                                value="{{ old('phone') }}"
                            >
                            @error('phone')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="department_id" class="form-label">
                                Department
                            </label>
                            <select
                                id="department_id"
                                name="department_id"
                                class="form-select"
                                required
                            >
                                <option value="">Select Department</option>
                                @foreach ($departments as $department)
                                    <option
                                        value="{{ $department->id }}"
                                        @selected(old('department_id') == $department->id)
                                    >
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="designation" class="form-label">
                                Designation
                            </label>
                            <input
                                id="designation"
                                name="designation"
                                class="form-control"
                                value="{{ old('designation') }}"
                            >
                            @error('designation')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-select">
                                <option value="active" @selected(old('status', 'active') === 'active')>
                                    Active
                                </option>
                                <option value="inactive" @selected(old('status') === 'inactive')>
                                    Inactive
                                </option>
                            </select>
                            @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">
                        Create Employee
                    </button>
                </form>
            @endif
        </div>
    </div>
@endsection
