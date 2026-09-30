@extends('layouts.app')

@section('page-title', 'Employees')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2 class="mb-1">Employees</h2>

        <p class="text-muted mb-0">
            Manage employees and their department assignments.
        </p>
    </div>

    <a
        href="{{ route('employees.create') }}"
        class="btn btn-primary"
    >
        <i class="bi bi-plus-lg me-1"></i>
        Add Employee
    </a>

</div>


<div class="card shadow-sm table-card">

    <div class="card-body">

        {{-- Search --}}
        <form
            method="GET"
            action="{{ route('employees.index') }}"
            class="mb-4"
        >

            <div class="input-group">

                <span class="input-group-text bg-white">
                    <i class="bi bi-search"></i>
                </span>

                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Search employee..."
                    value="{{ request('search') }}"
                >

                <button
                    type="submit"
                    class="btn btn-dark"
                >
                    Search
                </button>

            </div>

        </form>


        {{-- Employees Table --}}
        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>Code</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Assets</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($employees as $employee)

                        <tr>

                            <td>
                                <span class="fw-semibold">
                                    {{ $employee->employee_code }}
                                </span>
                            </td>

                            <td>
                                {{ $employee->name }}
                            </td>

                            <td>
                                {{ $employee->designation ?? '—' }}
                            </td>

                            <td>
                                {{ $employee->department->name ?? '—' }}
                            </td>

                            <td>
                                <span class="badge text-bg-primary">
                                    {{ $employee->assets_count }}
                                </span>
                            </td>

                            <td>

                                @if($employee->status === 'active')

                                    <span class="badge text-bg-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Active
                                    </span>

                                @else

                                    <span class="badge text-bg-secondary">
                                        Inactive
                                    </span>

                                @endif

                            </td>

                            <td class="text-end">

                                <a
                                    href="{{ route('employees.edit', $employee) }}"
                                    class="btn btn-sm btn-outline-secondary"
                                >
                                    <i class="bi bi-pencil me-1"></i>
                                    Edit
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="text-center text-muted py-5"
                            >

                                <i class="bi bi-people fs-2 d-block mb-2"></i>

                                No employees found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        <div class="mt-3">

            {{ $employees->links() }}

        </div>

    </div>

</div>

@endsection