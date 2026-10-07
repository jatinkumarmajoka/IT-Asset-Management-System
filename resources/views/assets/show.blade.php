@extends('layouts.app')

@section('page-title', 'Asset Details')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

        <div>
            <h3 class="mb-1">
                {{ $asset->item_name }}
            </h3>

            <div class="text-muted">
                {{ $asset->asset_code }}
            </div>
        </div>

        <div class="d-flex gap-2">

            <a
                href="{{ route('assets.edit', $asset) }}"
                class="btn btn-primary"
            >
                <i class="bi bi-pencil me-1"></i>
                Edit
            </a>

            <a
                href="{{ route('assets.index') }}"
                class="btn btn-light"
            >
                Back
            </a>

        </div>

    </div>


    <div class="row g-4">

        {{-- =====================================================
             LEFT
        ====================================================== --}}
        <div class="col-lg-8">

            {{-- Asset Information --}}
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-box-seam me-2"></i>
                        Asset Information
                    </h6>
                </div>

                <div class="card-body">

                    <div class="row g-4">

                        <div class="col-md-4">
                            <div class="text-muted small">
                                Asset Code
                            </div>
                            <div class="fw-semibold">
                                {{ $asset->asset_code }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">
                                Item Name
                            </div>
                            <div class="fw-semibold">
                                {{ $asset->item_name }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">
                                Category
                            </div>
                            <div class="fw-semibold">
                                {{ $asset->category?->name ?? '—' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">
                                Make
                            </div>
                            <div class="fw-semibold">
                                {{ $asset->make ?: '—' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">
                                Model
                            </div>
                            <div class="fw-semibold">
                                {{ $asset->model ?: '—' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">
                                Serial Number
                            </div>
                            <div class="fw-semibold">
                                {{ $asset->asset_serial_number ?: '—' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">
                                Barcode / Sticker
                            </div>
                            <div class="fw-semibold">
                                {{ $asset->barcode_number ?: '—' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">
                                Issued Quantity
                            </div>
                            <div class="fw-semibold">
                                {{ $asset->issued_quantity }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">
                                Physically Verified
                            </div>
                            <div class="fw-semibold">
                                {{ $asset->physically_verified_quantity }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">
                                Issue Date
                            </div>
                            <div class="fw-semibold">
                                {{ $asset->date_of_issue?->format('d-m-Y') ?? '—' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">
                                Status
                            </div>

                            <div>
                                <span class="badge text-bg-primary">
                                    {{ $asset->asset_status }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">
                                Condition
                            </div>
                            <div class="fw-semibold">
                                {{ $asset->condition ?: '—' }}
                            </div>
                        </div>

                    </div>

                </div>
            </div>


            {{-- =================================================
                 CURRENT ASSIGNMENT
            ================================================== --}}
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-diagram-3 me-2"></i>
                        Current Assignment
                    </h6>
                </div>

                <div class="card-body">

                    <div class="row g-3">

                        {{-- Department --}}
                        <div class="col-md-4">

                            <div class="text-muted small">
                                Department
                            </div>

                            <div class="fw-semibold fs-6">
                                {{ $asset->department?->name ?? '—' }}
                            </div>

                        </div>


                        {{-- Previous Occupant --}}
                        @if($previousOccupant)

                            <div class="col-md-4">

                                <div class="text-muted small">
                                    Previous Occupant
                                </div>

                                <div class="fw-semibold fs-6">
                                    {{ $previousOccupant }}
                                </div>

                            </div>

                        @endif


                        {{-- Present Occupant --}}
                        <div class="{{ $previousOccupant ? 'col-md-4' : 'col-md-6' }}">

                            <div class="text-muted small">
                                Present Occupant
                            </div>

                            <div class="fw-semibold fs-6">

                                @if($asset->occupant)
                                    {{ $asset->occupant->name }}
                                @else
                                    {{ $asset->department?->name ?? 'Unassigned' }}
                                @endif

                            </div>

                        </div>

                    </div>

                </div>
            </div>


            {{-- =================================================
                 TRANSFER HISTORY
            ================================================== --}}
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white">

                    <h6 class="mb-0">
                        <i class="bi bi-arrow-left-right me-2"></i>
                        Transfer History
                    </h6>

                </div>

                <div class="card-body p-0">

                    @forelse($asset->transfers as $transfer)

                        <div class="p-3 border-bottom">

                            <div class="d-flex justify-content-between align-items-start">

                                <div>

                                    <div class="fw-semibold">

                                        {{ $transfer->fromDepartment?->name ?? '—' }}

                                        <i class="bi bi-arrow-right mx-2 text-primary"></i>

                                        {{ $transfer->toDepartment?->name ?? '—' }}

                                    </div>

                                    <div class="small text-muted mt-1">

                                        {{ $transfer->fromOccupant?->name
                                            ?? $transfer->fromDepartment?->name
                                            ?? '—' }}

                                        <i class="bi bi-arrow-right mx-1"></i>

                                        {{ $transfer->toOccupant?->name
                                            ?? $transfer->toDepartment?->name
                                            ?? '—' }}

                                    </div>

                                </div>

                                <div class="text-end">

                                    @php
                                        $transferStatusClass = match($transfer->status) {
                                            'pending' => 'warning',
                                            'approved' => 'info',
                                            'completed' => 'success',
                                            'cancelled' => 'secondary',
                                            default => 'dark',
                                        };
                                    @endphp

                                    <span class="badge text-bg-{{ $transferStatusClass }}">
                                        {{ ucfirst($transfer->status) }}
                                    </span>

                                    <div class="small text-muted mt-1">
                                        {{ $transfer->transfer_date?->format('d-m-Y') ?? '—' }}
                                    </div>

                                </div>

                            </div>

                            <div class="mt-2">

                                <a
                                    href="{{ route('transfers.show', $transfer) }}"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    <i class="bi bi-eye me-1"></i>
                                    View Transfer
                                </a>

                            </div>

                        </div>

                    @empty

                        <div class="p-4 text-center text-muted">

                            <i class="bi bi-arrow-left-right fs-2 d-block mb-2"></i>

                            No transfer history exists for this asset.

                        </div>

                    @endforelse

                </div>

            </div>

        </div>


        {{-- =====================================================
             RIGHT - AUDIT HISTORY
        ====================================================== --}}
        <div class="col-lg-4">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white">

                    <h6 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Asset History
                    </h6>

                </div>

                <div class="card-body">

                    @forelse($asset->history->sortByDesc('created_at') as $history)

                        <div class="border-bottom pb-3 mb-3">

                            <div class="fw-semibold">
                                {{ ucfirst($history->action) }}
                            </div>

                            <div class="small text-muted">
                                {{ $history->created_at?->format('d-m-Y H:i') ?? '—' }}
                            </div>

                            @if($history->ip_address)

                                <div class="small text-muted">
                                    IP: {{ $history->ip_address }}
                                </div>

                            @endif

                        </div>

                    @empty

                        <div class="text-muted">
                            No history yet.
                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

</div>

@endsection