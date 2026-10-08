@extends('layouts.app')

@section('title', 'Asset Transfers')

@section('content')

<style>
    /* =========================================================
       TRANSFER PAGE
       ========================================================= */

    .transfer-page {
        width: 100%;
        min-width: 0;
    }

    .transfer-page .transfer-card {
        width: 100%;
        border: 0;
        border-radius: 12px;
        overflow: hidden;
    }

    /* =========================================================
       TABLE WRAPPER
       ========================================================= */

    .transfer-table-wrap {
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }

    /*
     * The table intentionally has a minimum width.
     * This prevents Bootstrap from squeezing all 10 columns
     * into the available screen width.
     */
    .transfer-table {
        width: 100%;
        min-width: 1500px;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
        table-layout: fixed;
    }

    /* =========================================================
       TABLE CELLS
       ========================================================= */

    .transfer-table th,
    .transfer-table td {
        padding: 14px 14px;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }

    .transfer-table thead th {
        background: #f8f9fa;
        color: #212529;
        font-size: 0.9rem;
        font-weight: 700;
        white-space: nowrap;
        text-align: left;
        height: 58px;
    }

    .transfer-table tbody td {
        font-size: 0.9rem;
    }

    .transfer-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .transfer-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* =========================================================
       COLUMN WIDTHS
       ========================================================= */

    .transfer-table th:nth-child(1),
    .transfer-table td:nth-child(1) {
        width: 70px;
        min-width: 70px;
        text-align: center;
    }

    .transfer-table th:nth-child(2),
    .transfer-table td:nth-child(2) {
        width: 165px;
        min-width: 165px;
    }

    .transfer-table th:nth-child(3),
    .transfer-table td:nth-child(3) {
        width: 180px;
        min-width: 180px;
    }

    .transfer-table th:nth-child(4),
    .transfer-table td:nth-child(4) {
        width: 175px;
        min-width: 175px;
    }

    .transfer-table th:nth-child(5),
    .transfer-table td:nth-child(5) {
        width: 175px;
        min-width: 175px;
    }

    .transfer-table th:nth-child(6),
    .transfer-table td:nth-child(6) {
        width: 175px;
        min-width: 175px;
    }

    .transfer-table th:nth-child(7),
    .transfer-table td:nth-child(7) {
        width: 175px;
        min-width: 175px;
    }

    .transfer-table th:nth-child(8),
    .transfer-table td:nth-child(8) {
        width: 125px;
        min-width: 125px;
    }

    .transfer-table th:nth-child(9),
    .transfer-table td:nth-child(9) {
        width: 115px;
        min-width: 115px;
    }

    .transfer-table th:nth-child(10),
    .transfer-table td:nth-child(10) {
        width: 100px;
        min-width: 100px;
        text-align: center;
    }

    /* =========================================================
       CELL CONTENT
       ========================================================= */

    .transfer-reference {
        display: block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-weight: 600;
    }

    .transfer-asset-code {
        display: block;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .transfer-asset-name {
        display: block;
        margin-top: 3px;
        color: #6c757d;
        font-size: 0.82rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .transfer-person,
    .transfer-department {
        display: block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .transfer-date {
        white-space: nowrap;
    }

    /* =========================================================
       STATUS
       ========================================================= */

    .transfer-status {
        white-space: nowrap;
    }

    /* =========================================================
       ACTION
       ========================================================= */

    .transfer-action {
        white-space: nowrap;
    }

    .transfer-action .btn {
        min-width: 72px;
    }

    /* =========================================================
       EMPTY STATE
       ========================================================= */

    .transfer-empty {
        padding: 60px 20px !important;
        text-align: center;
        color: #6c757d;
    }

    .transfer-empty-icon {
        font-size: 2.5rem;
        display: block;
        margin-bottom: 12px;
    }

    /* =========================================================
       SCROLLBAR
       ========================================================= */

    .transfer-table-wrap::-webkit-scrollbar {
        height: 8px;
    }

    .transfer-table-wrap::-webkit-scrollbar-track {
        background: #f1f3f5;
    }

    .transfer-table-wrap::-webkit-scrollbar-thumb {
        background: #adb5bd;
        border-radius: 10px;
    }

    .transfer-table-wrap::-webkit-scrollbar-thumb:hover {
        background: #868e96;
    }

    /* =========================================================
       MOBILE
       ========================================================= */

    @media (max-width: 767.98px) {

        .transfer-page .page-header {
            align-items: flex-start !important;
        }

        .transfer-table {
            min-width: 1500px;
        }

        .transfer-table th,
        .transfer-table td {
            padding: 12px 12px;
        }
    }
</style>


<div class="transfer-page">

    {{-- =====================================================
         HEADER
         ===================================================== --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 page-header">

        <div>
            <h4 class="mb-1">
                <i class="bi bi-arrow-left-right me-2"></i>
                Asset Transfers
            </h4>

            <div class="text-muted small">
                Track movement of assets between departments and occupants.
            </div>
        </div>

        <a href="{{ route('transfers.create') }}"
           class="btn btn-primary">

            <i class="bi bi-plus-lg me-1"></i>
            New Transfer

        </a>

    </div>


    {{-- =====================================================
         SUCCESS MESSAGE
         ===================================================== --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle me-2"></i>

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- =====================================================
         VALIDATION ERRORS
         ===================================================== --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <strong>Please fix the following:</strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =====================================================
         TRANSFERS TABLE
         ===================================================== --}}
    <div class="card shadow-sm transfer-card">

        <div class="card-body p-0">

            <div class="transfer-table-wrap">

                <table class="table table-hover align-middle transfer-table">

                    <thead>

                        <tr>

                            <th>S.No.</th>

                            <th>
                                Reference
                            </th>

                            <th>
                                Asset
                            </th>

                            <th>
                                From Department
                            </th>

                            <th>
                                To Department
                            </th>

                            <th>
                                From Occupant
                            </th>

                            <th>
                                To Occupant
                            </th>

                            <th>
                                Transfer Date
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($transfers as $transfer)

                            <tr>

                                {{-- =================================================
                                     S.NO.
                                     ================================================= --}}
                                <td>

                                    {{ $transfers->firstItem() + $loop->index }}

                                </td>


                                {{-- =================================================
                                     REFERENCE
                                     ================================================= --}}
                                <td>

                                    <span class="transfer-reference"
                                          title="{{ $transfer->reference_number }}">

                                        {{ $transfer->reference_number ?? '—' }}

                                    </span>

                                </td>


                                {{-- =================================================
                                     ASSET
                                     ================================================= --}}
                                <td>

                                    <span class="transfer-asset-code"
                                          title="{{ $transfer->asset?->asset_code }}">

                                        {{ $transfer->asset?->asset_code ?? '—' }}

                                    </span>

                                    <span class="transfer-asset-name"
                                          title="{{ $transfer->asset?->item_name }}">

                                        {{ $transfer->asset?->item_name ?? 'Unknown Asset' }}

                                    </span>

                                </td>


                                {{-- =================================================
                                     FROM DEPARTMENT
                                     ================================================= --}}
                                <td>

                                    <span class="transfer-department"
                                          title="{{ $transfer->fromDepartment?->name }}">

                                        {{ $transfer->fromDepartment?->name ?? '—' }}

                                    </span>

                                </td>


                                {{-- =================================================
                                     TO DEPARTMENT
                                     ================================================= --}}
                                <td>

                                    <span class="transfer-department"
                                          title="{{ $transfer->toDepartment?->name }}">

                                        {{ $transfer->toDepartment?->name ?? '—' }}

                                    </span>

                                </td>


                                {{-- =================================================
                                     FROM OCCUPANT
                                     ================================================= --}}
                                <td>

                                    <span class="transfer-person"
                                          title="{{ $transfer->fromOccupant?->name
                                              ?? $transfer->fromDepartment?->name
                                              ?? '—' }}">

                                        {{ $transfer->fromOccupant?->name
                                            ?? $transfer->fromDepartment?->name
                                            ?? '—' }}

                                    </span>

                                </td>


                                {{-- =================================================
                                     TO OCCUPANT
                                     ================================================= --}}
                                <td>

                                    <span class="transfer-person"
                                          title="{{ $transfer->toOccupant?->name
                                              ?? $transfer->toDepartment?->name
                                              ?? '—' }}">

                                        {{ $transfer->toOccupant?->name
                                            ?? $transfer->toDepartment?->name
                                            ?? '—' }}

                                    </span>

                                </td>


                                {{-- =================================================
                                     TRANSFER DATE
                                     ================================================= --}}
                                <td>

                                    <span class="transfer-date">

                                        {{ $transfer->transfer_date?->format('d-m-Y') ?? '—' }}

                                    </span>

                                </td>


                                {{-- =================================================
                                     STATUS
                                     ================================================= --}}
                                <td>

                                    @php

                                        $statusClass = match($transfer->status) {

                                            'pending' =>
                                                'warning',

                                            'approved' =>
                                                'info',

                                            'completed' =>
                                                'success',

                                            'cancelled' =>
                                                'secondary',

                                            default =>
                                                'dark',

                                        };

                                    @endphp

                                    <span class="badge text-bg-{{ $statusClass }} transfer-status">

                                        {{ ucfirst($transfer->status) }}

                                    </span>

                                </td>


                                {{-- =================================================
                                     ACTION
                                     ================================================= --}}
                                <td class="transfer-action">

                                    <a href="{{ route('transfers.show', $transfer) }}"
                                       class="btn btn-sm btn-outline-primary">

                                        <i class="bi bi-eye me-1"></i>
                                        View

                                    </a>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td colspan="10"
                                    class="transfer-empty">

                                    <i class="bi bi-arrow-left-right transfer-empty-icon"></i>

                                    <div class="fw-semibold">
                                        No transfers found
                                    </div>

                                    <div class="small mb-3">
                                        No asset transfers have been created yet.
                                    </div>

                                    <a href="{{ route('transfers.create') }}"
                                       class="btn btn-primary btn-sm">

                                        <i class="bi bi-plus-lg me-1"></i>
                                        Create First Transfer

                                    </a>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- =====================================================
             PAGINATION
             ===================================================== --}}
        @if($transfers->hasPages())

            <div class="card-footer bg-white border-0">

                {{ $transfers->links() }}

            </div>

        @endif

    </div>

</div>

@endsection