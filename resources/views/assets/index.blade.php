@extends('layouts.app')



@section('page-title', 'Assets')



@section('content')

<style>
    /* Assets table: stable column sizing so headers never overlap. */
    .assets-table {
        width: 100%;
        min-width: 1120px;
        table-layout: fixed;
    }

    .assets-table thead th {
        white-space: nowrap;
        vertical-align: middle;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 14px 12px;
    }

    .assets-table tbody td {
        vertical-align: middle;
        overflow-wrap: anywhere;
        word-break: break-word;
        padding: 14px 12px;
    }

    .assets-table th:nth-child(1),
    .assets-table td:nth-child(1) {
        width: 70px;
    }

    .assets-table th:nth-child(2),
    .assets-table td:nth-child(2) {
        width: 190px;
    }

    .assets-table th:nth-child(3),
    .assets-table td:nth-child(3) {
        width: 145px;
    }

    .assets-table th:nth-child(4),
    .assets-table td:nth-child(4) {
        width: 175px;
    }

    .assets-table th:nth-child(5),
    .assets-table td:nth-child(5) {
        width: 100px;
    }

    .assets-table th:nth-child(6),
    .assets-table td:nth-child(6) {
        width: 100px;
    }

    .assets-table th:nth-child(7),
    .assets-table td:nth-child(7) {
        width: 175px;
    }

    .assets-table th:nth-child(8),
    .assets-table td:nth-child(8) {
        width: 115px;
    }

    .assets-table th:nth-child(9),
    .assets-table td:nth-child(9) {
        width: 95px;
    }

    .assets-table th:nth-child(10),
    .assets-table td:nth-child(10) {
        width: 85px;
    }

    .assets-table th:nth-child(1) {
        font-size: 0;
    }

    .assets-table th:nth-child(1)::after {
        content: "S.no";
        font-size: 16px;
    }

    .assets-table th:nth-child(3) {
        font-size: 0;
    }

    .assets-table th:nth-child(3)::after {
        content: "Make & M...";
        font-size: 16px;
    }

    @media (max-width: 1200px) {
        .assets-table {
            min-width: 1120px;
        }
    }
</style>




<div class="d-flex justify-content-between align-items-center mb-3">



    <div>

        <h2>Assets</h2>

        <p class="text-muted mb-0">

            Departmental stock register

        </p>

    </div>



    <a

        href="{{ route('assets.create') }}"

        class="btn btn-primary">

        <i class="bi bi-plus-lg me-1"></i>

        Add Asset

    </a>



</div>





<div class="card shadow-sm table-card">



    <div class="card-body">



        {{-- =====================================================

                Filters

                ====================================================== --}}

        <form class="row g-2 mb-3">



            {{-- Search --}}

            <div class="col-md-4">



                <input

                    name="search"

                    class="form-control"

                    placeholder="Search asset, barcode, serial..."

                    value="{{ request('search') }}">



            </div>





            {{-- Department --}}

            <div class="col-md-2">



                <select

                    name="department_id"

                    class="form-select">



                    <option value="">

                        All Departments

                    </option>



                    @foreach($departments as $d)



                    <option

                        value="{{ $d->id }}"

                        @selected(

                        request('department_id')==$d->id

                        )

                        >

                        {{ $d->name }}

                    </option>



                    @endforeach



                </select>



            </div>





            {{-- Category --}}

            <div class="col-md-2">



                <select

                    name="category_id"

                    class="form-select">



                    <option value="">

                        All Categories

                    </option>



                    @foreach($categories as $c)



                    <option

                        value="{{ $c->id }}"

                        @selected(

                        request('category_id')==$c->id

                        )

                        >

                        {{ $c->name }}

                    </option>



                    @endforeach



                </select>



            </div>





            {{-- Status --}}

            <div class="col-md-2">



                <select

                    name="asset_status"

                    class="form-select">



                    <option value="">

                        All Status

                    </option>



                    @foreach([

                    'Active',

                    'Assigned',

                    'Unassigned',

                    'Missing',

                    'Damaged',

                    'Disposed',

                    'Transferred',

                    'Under Repair',

                    'Retired'

                    ] as $s)



                    <option

                        value="{{ $s }}"

                        @selected(

                        request('asset_status')==$s

                        )>

                        {{ $s }}

                    </option>



                    @endforeach



                </select>



            </div>





            {{-- Filter Button --}}

            <div class="col-md-2">



                <button

                    type="submit"

                    class="btn btn-dark w-100">

                    Filter

                </button>



            </div>



        </form>





        {{-- =====================================================

        Assets Table

        ====================================================== --}}

        <div class="table-responsive">



            <table class="table table-hover align-middle assets-table">



                <thead>



                    <tr>



                        <th>S.No.</th>



                        <th>

                            Asset

                        </th>



                        <th>

                            Make & Model

                        </th>



                        <th>

                            Department

                        </th>



                        <th>

                            Qty Issued

                        </th>



                        <th>

                            Verified

                        </th>



                        <th>

                            Occupant

                        </th>



                        <th>

                            Barcode

                        </th>



                        <th>

                            Status

                        </th>



                        <th>

                        </th>



                    </tr>



                </thead>





                <tbody>



                    @forelse($assets as $asset)



                    <tr>



                        {{-- S.No. --}}

                        <td>

                            {{ $assets->firstItem() + $loop->index }}

                        </td>





                        {{-- Asset --}}

                        <td>



                            <a

                                class="fw-semibold text-decoration-none"

                                href="{{ route('assets.show', $asset) }}">

                                {{ $asset->item_name }}

                            </a>



                            <br>



                            <small class="text-muted">

                                {{ $asset->asset_code }}

                            </small>



                        </td>





                        {{-- Make & Model --}}

                        <td>



                            {{ trim(

                        ($asset->make ?? '') .

                        ' ' .

                        ($asset->model ?? '')

                        ) ?: '—' }}



                        </td>





                        {{-- Department --}}

                        <td>



                            {{ $asset->department?->name ?? '—' }}



                        </td>





                        {{-- Issued Quantity --}}

                        <td>

                            {{ $asset->issued_quantity }}

                        </td>





                        {{-- Verified Quantity --}}

                        <td>

                            {{ $asset->physically_verified_quantity }}

                        </td>





                        {{-- =================================================

                    Present Occupant



                    If employee exists:

                    show employee



                    If employee does not exist:

                    show department



                    IMPORTANT:

                    The department is NOT stored as an employee.

                    present_occupant_id remains NULL.

                    ================================================== --}}

                        <td>



                            @if($asset->occupant)



                            <span class="fw-semibold">

                                {{ $asset->occupant->name }}

                            </span>



                            @if($asset->occupant->employee_code)



                            <br>



                            <small class="text-muted">

                                {{ $asset->occupant->employee_code }}

                            </small>



                            @endif



                            @elseif($asset->department)



                            <span class="fw-semibold">

                                {{ $asset->department->name }}

                            </span>



                            <br>



                            <small class="text-muted">

                                Department

                            </small>



                            @else



                            —



                            @endif



                        </td>





                        {{-- Barcode --}}

                        <td>

                            <span class="text-truncate" title="{{ $asset->barcode_number ?? '—' }}">{{ $asset->barcode_number ?? '—' }}</span>

                        </td>





                        {{-- Status --}}

                        <td>



                            <span class="badge text-bg-light">

                                {{ $asset->asset_status }}

                            </span>



                        </td>





                        {{-- Edit --}}

                        <td>



                            <a

                                href="{{ route('assets.edit', $asset) }}"

                                class="btn btn-sm btn-outline-secondary"

                                title="Edit Asset">

                                <i class="bi bi-pencil"></i>

                            </a>



                        </td>



                    </tr>



                    @empty



                    <tr>



                        <td

                            colspan="10"

                            class="text-center py-5 text-muted">

                            No assets found.

                        </td>



                    </tr>



                    @endforelse



                </tbody>



            </table>



        </div>





        {{-- Pagination --}}

        {{ $assets->links() }}



    </div>



</div>



@endsection