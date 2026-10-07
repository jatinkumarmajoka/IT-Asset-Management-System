@extends('layouts.app')

@section('title', 'Transfer Details')

@section('content')

<div class="container-fluid">


    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

        <div>

            <h4 class="mb-1">
                <i class="bi bi-arrow-left-right me-2"></i>
                Transfer Details
            </h4>

            <div class="text-muted small">
                {{ $transfer->reference_number ?? 'Asset Transfer' }}
            </div>

        </div>


        <a href="{{ route('transfers.index') }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left me-1"></i>
            Back to Transfers

        </a>

    </div>



    {{-- Success --}}
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



    {{-- Errors --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <strong>Operation failed.</strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif



    <div class="row g-4">


        {{-- Main --}}
        <div class="col-lg-8">


            {{-- Transfer Status --}}
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <div class="text-muted small">
                                Transfer Status
                            </div>

                            @php
                                $statusClass = match($transfer->status) {
                                    'pending' => 'warning',
                                    'approved' => 'info',
                                    'completed' => 'success',
                                    'cancelled' => 'secondary',
                                    default => 'dark',
                                };
                            @endphp

                            <span class="badge text-bg-{{ $statusClass }} fs-6 mt-1">

                                {{ ucfirst($transfer->status) }}

                            </span>

                        </div>


                        <div class="text-end">

                            <div class="text-muted small">
                                Reference
                            </div>

                            <div class="fw-semibold">
                                {{ $transfer->reference_number ?? '—' }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>



            {{-- Asset --}}
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-white">

                    <h6 class="mb-0">
                        <i class="bi bi-box-seam me-2"></i>
                        Asset
                    </h6>

                </div>


                <div class="card-body">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <div class="text-muted small">
                                Asset Code
                            </div>

                            <div class="fw-semibold">
                                {{ $transfer->asset?->asset_code ?? '—' }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Item
                            </div>

                            <div class="fw-semibold">
                                {{ $transfer->asset?->item_name ?? '—' }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Category
                            </div>

                            <div>
                                {{ $transfer->asset?->category?->name ?? '—' }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Serial Number
                            </div>

                            <div>
                                {{ $transfer->asset?->asset_serial_number ?? '—' }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>



            {{-- Movement --}}
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-white">

                    <h6 class="mb-0">
                        <i class="bi bi-sign-turn-right me-2"></i>
                        Movement
                    </h6>

                </div>


                <div class="card-body">

                    <div class="row g-4">


                        {{-- From --}}
                        <div class="col-md-5">

                            <div class="text-muted small mb-1">
                                From Department
                            </div>

                            <div class="fw-semibold fs-5">

                                {{ $transfer->fromDepartment?->name ?? '—' }}

                            </div>


                            <div class="mt-3">

                                <div class="text-muted small">
                                    Previous Occupant
                                </div>

                                <div>

                                    {{ $transfer->fromOccupant?->name
                                        ?? $transfer->fromDepartment?->name
                                        ?? '—' }}

                                </div>

                            </div>

                        </div>


                        {{-- Arrow --}}
                        <div class="col-md-2 d-flex align-items-center justify-content-center">

                            <i class="bi bi-arrow-right fs-1 text-primary"></i>

                        </div>


                        {{-- To --}}
                        <div class="col-md-5">

                            <div class="text-muted small mb-1">
                                To Department
                            </div>

                            <div class="fw-semibold fs-5">

                                {{ $transfer->toDepartment?->name ?? '—' }}

                            </div>


                            <div class="mt-3">

                                <div class="text-muted small">
                                    New Occupant
                                </div>

                                <div>

                                    {{ $transfer->toOccupant?->name
                                        ?? $transfer->toDepartment?->name
                                        ?? '—' }}

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>



            {{-- Details --}}
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white">

                    <h6 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Transfer Information
                    </h6>

                </div>


                <div class="card-body">

                    <div class="row g-3">


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Transfer Date
                            </div>

                            <div>
                                {{ $transfer->transfer_date?->format('d-m-Y') ?? '—' }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Received Date
                            </div>

                            <div>
                                {{ $transfer->received_date?->format('d-m-Y') ?? 'Not completed' }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Reason
                            </div>

                            <div>
                                {{ $transfer->reason ?: '—' }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Initiated By
                            </div>

                            <div>
                                {{ $transfer->initiatedBy?->name ?? '—' }}
                            </div>

                        </div>


                        <div class="col-12">

                            <div class="text-muted small">
                                Remarks
                            </div>

                            <div>
                                {{ $transfer->remarks ?: '—' }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        {{-- Sidebar --}}
        <div class="col-lg-4">


            {{-- Workflow --}}
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-white">

                    <h6 class="mb-0">
                        <i class="bi bi-diagram-3 me-2"></i>
                        Transfer Workflow
                    </h6>

                </div>


                <div class="card-body">


                    {{-- Pending --}}
                    <div class="d-flex align-items-start mb-3">

                        <div class="me-3">

                            @if($transfer->status === 'pending')
                                <span class="badge rounded-pill text-bg-warning">
                                    1
                                </span>
                            @else
                                <span class="badge rounded-pill text-bg-success">
                                    <i class="bi bi-check"></i>
                                </span>
                            @endif

                        </div>

                        <div>

                            <div class="fw-semibold">
                                Transfer Request
                            </div>

                            <div class="small text-muted">
                                Request created
                            </div>

                        </div>

                    </div>


                    {{-- Approved --}}
                    <div class="d-flex align-items-start mb-3">

                        <div class="me-3">

                            @if(in_array($transfer->status, ['approved', 'completed']))

                                <span class="badge rounded-pill text-bg-success">
                                    <i class="bi bi-check"></i>
                                </span>

                            @else

                                <span class="badge rounded-pill text-bg-secondary">
                                    2
                                </span>

                            @endif

                        </div>

                        <div>

                            <div class="fw-semibold">
                                Approval
                            </div>

                            <div class="small text-muted">

                                @if($transfer->approvedBy)
                                    Approved by {{ $transfer->approvedBy->name }}
                                @else
                                    Waiting for approval
                                @endif

                            </div>

                        </div>

                    </div>


                    {{-- Completed --}}
                    <div class="d-flex align-items-start">

                        <div class="me-3">

                            @if($transfer->status === 'completed')

                                <span class="badge rounded-pill text-bg-success">
                                    <i class="bi bi-check"></i>
                                </span>

                            @else

                                <span class="badge rounded-pill text-bg-secondary">
                                    3
                                </span>

                            @endif

                        </div>

                        <div>

                            <div class="fw-semibold">
                                Completion
                            </div>

                            <div class="small text-muted">

                                @if($transfer->status === 'completed')
                                    Asset department updated
                                @else
                                    Asset has not moved yet
                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            </div>



            {{-- Actions --}}
            @if($transfer->status !== 'cancelled'
                && $transfer->status !== 'completed')

                <div class="card shadow-sm border-0 mb-4">

                    <div class="card-header bg-white">

                        <h6 class="mb-0">
                            <i class="bi bi-lightning me-2"></i>
                            Actions
                        </h6>

                    </div>


                    <div class="card-body">


                        {{-- Approve --}}
                        @if($transfer->status === 'pending')

                            <form method="POST"
                                  action="{{ route('transfers.approve', $transfer) }}"
                                  class="mb-2">

                                @csrf

                                <button type="submit"
                                        class="btn btn-primary w-100"
                                        onclick="return confirm('Approve this transfer?')">

                                    <i class="bi bi-check-circle me-1"></i>
                                    Approve Transfer

                                </button>

                            </form>

                        @endif


                        {{-- Complete --}}
                        @if($transfer->status === 'approved')

                            <form method="POST"
                                  action="{{ route('transfers.complete', $transfer) }}"
                                  class="mb-2">

                                @csrf

                                <button type="submit"
                                        class="btn btn-success w-100"
                                        onclick="return confirm('Complete this transfer? The asset department will be updated.')">

                                    <i class="bi bi-check2-all me-1"></i>
                                    Complete Transfer

                                </button>

                            </form>

                        @endif


                        {{-- Cancel --}}
                        <form method="POST"
                              action="{{ route('transfers.cancel', $transfer) }}">

                            @csrf

                            <button type="submit"
                                    class="btn btn-outline-danger w-100"
                                    onclick="return confirm('Cancel this transfer?')">

                                <i class="bi bi-x-circle me-1"></i>
                                Cancel Transfer

                            </button>

                        </form>

                    </div>

                </div>

            @endif



            {{-- Completed --}}
            @if($transfer->status === 'completed')

                <div class="alert alert-success">

                    <div class="fw-semibold mb-1">

                        <i class="bi bi-check-circle me-1"></i>
                        Transfer Completed

                    </div>

                    <div class="small">

                        The asset's current department has been updated
                        to {{ $transfer->toDepartment?->name ?? 'the destination department' }}.

                    </div>

                </div>

            @endif



            {{-- Cancelled --}}
            @if($transfer->status === 'cancelled')

                <div class="alert alert-secondary">

                    <div class="fw-semibold mb-1">

                        <i class="bi bi-x-circle me-1"></i>
                        Transfer Cancelled

                    </div>

                    <div class="small">
                        This transfer can no longer be completed.
                    </div>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection