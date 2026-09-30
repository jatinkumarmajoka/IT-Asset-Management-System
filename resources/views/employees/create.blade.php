@extends('layouts.app')

@section('page-title', 'Add Employee')

@section('content')

<div class="card shadow-sm table-card">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1">Add Employee</h3>
                <p class="text-muted mb-0">
                    Create a new employee and assign them to a department.
                </p>
            </div>

            <a
                href="{{ route('employees.index') }}"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back to Employees
            </a>
        </div>


        <form
            method="POST"
            action="{{ route('employees.store') }}"
        >

            @csrf


            <div class="row g-3">

                {{-- Employee Code --}}
                <div class="col-md-4">

                    <label
                        for="employee_code"
                        class="form-label"
                    >
                        Employee Code
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        id="employee_code"
                        name="employee_code"
                        class="form-control @error('employee_code') is-invalid @enderror"
                        value="{{ old('employee_code') }}"
                        required
                    >

                    @error('employee_code')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Name --}}
                <div class="col-md-4">

                    <label
                        for="name"
                        class="form-label"
                    >
                        Name
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        required
                    >

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Designation --}}
                <div class="col-md-4">

                    <label
                        for="designation"
                        class="form-label"
                    >
                        Designation
                    </label>

                    <input
                        type="text"
                        id="designation"
                        name="designation"
                        class="form-control @error('designation') is-invalid @enderror"
                        value="{{ old('designation') }}"
                    >

                    @error('designation')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Department --}}
                <div class="col-md-4">

                    <label
                        for="department_id"
                        class="form-label"
                    >
                        Department
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        id="department_id"
                        name="department_id"
                        class="form-select @error('department_id') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Select Department
                        </option>

                        @foreach($departments as $department)

                            <option
                                value="{{ $department->id }}"
                                @selected(
                                    old('department_id') == $department->id
                                )
                            >
                                {{ $department->name }}
                            </option>

                        @endforeach

                    </select>

                    @error('department_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Email --}}
                <div class="col-md-4">

                    <label
                        for="email"
                        class="form-label"
                    >
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                    >

                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Phone --}}
                <div class="col-md-4">

                    <label
                        for="phone"
                        class="form-label"
                    >
                        Phone
                    </label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        class="form-control @error('phone') is-invalid @enderror"
                        value="{{ old('phone') }}"
                    >

                    @error('phone')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Status --}}
                <div class="col-md-4">

                    <label
                        for="status"
                        class="form-label"
                    >
                        Status
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="form-select @error('status') is-invalid @enderror"
                        required
                    >

                        <option
                            value="active"
                            @selected(old('status', 'active') === 'active')
                        >
                            Active
                        </option>

                        <option
                            value="inactive"
                            @selected(old('status') === 'inactive')
                        >
                            Inactive
                        </option>

                    </select>

                    @error('status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>


            {{-- Actions --}}
            <div class="mt-4">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-plus-lg me-1"></i>
                    Create Employee
                </button>

                <a
                    href="{{ route('employees.index') }}"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

@endsection