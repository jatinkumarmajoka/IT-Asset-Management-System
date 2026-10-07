@extends('layouts.app')

@section('title', 'Asset Transfers')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

        <div>
            <h4 class="mb-1">
                <i class="bi bi-arrow-left-right me-2"></i>
                Asset Transfers
            </h4>

            <div class="text-muted small">
                Track movement of assets between departments and occupants.
            </div>
        </div>

        <a href="{{ route('transfers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            New Transfer
        </a>

    </div>


    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- Validation errors --}}
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


    {{-- Transfers table --}}
    <div class="card shadow-sm border-0">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th class="px-3">S.No.</th>
                            <th>Reference</th>
                            <th>Asset</th>
                            <th>From Department</th>
                            <th>To Department</th>
                            <th>From Occupant</th>
                            <th>To Occupant</th>
                            <th>Transfer Date</th>
                            <th>Status</th>
                            <th class="text-end px-3">Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($transfers as $transfer)

                            <tr>

                                {{-- S.No --}}
                                <td class="px-3">
                                    {{ $transfers->firstItem() + $loop->index }}
                                </td>


                                {{-- Reference --}}
                                <td>
                                    <span class="fw-semibold">
                                        {{ $transfer->reference_number ?? '—' }}
                                    </span>
                                </td>


                                {{-- Asset --}}
                                <td>

                                    <div class="fw-semibold">
                                        {{ $transfer->asset?->asset_code ?? '—' }}
                                    </div>

                                    <div class="small text-muted">
                                        {{ $transfer->asset?->item_name ?? 'Unknown Asset' }}
                                    </div>

                                </td>


                                {{-- From Department --}}
                                <td>
                                    {{ $transfer->fromDepartment?->name ?? '—' }}
                                </td>


                                {{-- To Department --}}
                                <td>
                                    {{ $transfer->toDepartment?->name ?? '—' }}
                                </td>


                                {{-- From Occupant --}}
                                <td>
                                    {{ $transfer->fromOccupant?->name
                                        ?? $transfer->fromDepartment?->name
                                        ?? '—' }}
                                </td>


                                {{-- To Occupant --}}
                                <td>
                                    {{ $transfer->toOccupant?->name
                                        ?? $transfer->toDepartment?->name
                                        ?? '—' }}
                                </td>


                                {{-- Date --}}
                                <td>
                                    {{ $transfer->transfer_date?->format('d-m-Y') ?? '—' }}
                                </td>


                                {{-- Status --}}
                                <td>

                                    @php
                                        $statusClass = match($transfer->status) {
                                            'pending' => 'warning',
                                            'approved' => 'info',
                                            'completed' => 'success',
                                            'cancelled' => 'secondary',
                                            default => 'dark',
                                        };
                                    @endphp

                                    <span class="badge text-bg-{{ $statusClass }}">
                                        {{ ucfirst($transfer->status) }}
                                    </span>

                                </td>


                                {{-- Action --}}
                                <td class="text-end px-3">

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
                                    class="text-center py-5">

                                    <div class="text-muted">

                                        <i class="bi bi-arrow-left-right fs-2 d-block mb-2"></i>

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

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Pagination --}}
        @if($transfers->hasPages())

            <div class="card-footer bg-white border-0">

                {{ $transfers->links() }}

            </div>

        @endif

    </div>

</div>

@endsection