
@extends('layouts.app')

@section('page-title', 'Asset Details')

@section('content')
@php
    $currentUser = auth()->user();

    $isSuperAdmin = $currentUser
        && method_exists($currentUser, 'isSuperAdmin')
        && $currentUser->isSuperAdmin();

    $canManageAssets = $isSuperAdmin
        || ($currentUser
            && method_exists($currentUser, 'canManageModule')
            && $currentUser->canManageModule('assets'));

    $canManageBills = $isSuperAdmin
        || ($currentUser
            && method_exists($currentUser, 'canManageModule')
            && $currentUser->canManageModule('bills'));

    // Query saved documents directly so the view uses current database data.
    $assetDocuments = $asset->documents()->get();

    $assetImageDocument = $assetDocuments->firstWhere(
        'document_type',
        \App\Models\AssetDocument::TYPE_ASSET_IMAGE
    );

    $billDocument = $assetDocuments->firstWhere(
        'document_type',
        \App\Models\AssetDocument::TYPE_BILL_INVOICE
    );
@endphp

<div class="container-fluid">
    {{-- Page heading --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h3 class="mb-1">{{ $asset->item_name }}</h3>
            <div class="text-muted">{{ $asset->asset_code }}</div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>

            <a href="{{ route('assets.index') }}" class="btn btn-light">
                Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left column --}}
        <div class="col-lg-8">
            {{-- Asset information --}}
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
                            <div class="text-muted small">Asset Code</div>
                            <div class="fw-semibold">{{ $asset->asset_code }}</div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Item Name</div>
                            <div class="fw-semibold">{{ $asset->item_name }}</div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Category</div>
                            <div class="fw-semibold">
                                {{ $asset->category?->name ?? '—' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Make</div>
                            <div class="fw-semibold">{{ $asset->make ?: '—' }}</div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Model</div>
                            <div class="fw-semibold">{{ $asset->model ?: '—' }}</div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Serial Number</div>
                            <div class="fw-semibold">
                                {{ $asset->asset_serial_number ?: '—' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Barcode / Sticker</div>
                            <div class="fw-semibold">
                                {{ $asset->barcode_number ?: '—' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Issued Quantity</div>
                            <div class="fw-semibold">
                                {{ $asset->issued_quantity }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Physically Verified</div>
                            <div class="fw-semibold">
                                {{ $asset->physically_verified_quantity }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Issue Date</div>
                            <div class="fw-semibold">
                                {{ $asset->date_of_issue?->format('d-m-Y') ?? '—' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Status</div>
                            <div>
                                <span class="badge text-bg-primary">
                                    {{ $asset->asset_status }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Condition</div>
                            <div class="fw-semibold">{{ $asset->condition }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Asset image and bill / invoice --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-paperclip me-2"></i>
                        Asset Image &amp; Bill/Invoice
                    </h6>
                </div>

                <div class="card-body">
                    <div class="row g-4">
                        {{-- Asset image --}}
                        <div class="col-md-6">
                            <h6 class="mb-3">Asset Image</h6>

                            @if($assetImageDocument)
                                <div class="border rounded bg-light p-2 mb-3 text-center">
                                    <a
                                        href="{{ route('asset-documents.show', [$asset, $assetImageDocument]) }}"
                                        target="_blank"
                                        rel="noopener"
                                    >
                                        <img
                                            src="{{ route('asset-documents.show', [$asset, $assetImageDocument]) }}"
                                            alt="{{ $asset->item_name }} image"
                                            class="img-fluid rounded"
                                            style="max-height: 260px; object-fit: contain;"
                                        >
                                    </a>
                                </div>

                                <div class="small text-muted mb-2">
                                    <strong>File:</strong>
                                    {{ $assetImageDocument->original_name }}
                                </div>

                                <a
                                    href="{{ route('asset-documents.show', [$asset, $assetImageDocument]) }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="btn btn-sm btn-outline-primary mb-3"
                                >
                                    <i class="bi bi-eye me-1"></i> View Image
                                </a>
                            @else
                                <div class="border rounded bg-light text-muted text-center p-4 mb-3">
                                    <i class="bi bi-image fs-2 d-block mb-2"></i>
                                    No asset image uploaded.
                                </div>
                            @endif

                            @if($canManageAssets)
                                <form
                                    method="POST"
                                    action="{{ route('asset-documents.image.replace', $asset) }}"
                                    enctype="multipart/form-data"
                                >
                                    @csrf

                                    <label class="form-label small">
                                        {{ $assetImageDocument ? 'Replace image' : 'Upload image' }}
                                    </label>

                                    <input
                                        type="file"
                                        name="file"
                                        class="form-control form-control-sm mb-2"
                                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                        required
                                    >

                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="bi bi-upload me-1"></i>
                                        {{ $assetImageDocument ? 'Replace Image' : 'Upload Image' }}
                                    </button>
                                </form>

                                @if($assetImageDocument)
                                    <form
                                        method="POST"
                                        action="{{ route('asset-documents.image.destroy', $asset) }}"
                                        class="mt-2"
                                        onsubmit="return confirm('Delete the asset image?');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash me-1"></i>
                                            Delete Image
                                        </button>
                                    </form>
                                @endif
                            @else
                                <div class="small text-muted">
                                    You do not have permission to manage the asset image.
                                </div>
                            @endif
                        </div>

                        {{-- Bill / invoice --}}
                        <div class="col-md-6">
                            <h6 class="mb-3">Bill / Invoice</h6>

                            @if($billDocument)
                                <div class="border rounded p-3 mb-3">
                                    <i class="bi bi-file-earmark-text fs-3 me-2"></i>
                                    <span class="fw-semibold">
                                        {{ $billDocument->original_name }}
                                    </span>

                                    <div class="small text-muted mt-2">
                                        <strong>Size:</strong>
                                        {{ number_format($billDocument->file_size / 1024, 2) }} KB
                                    </div>

                                    @if($billDocument->locked_at)
                                        <div class="small text-success mt-1">
                                            <i class="bi bi-lock-fill me-1"></i>
                                            Uploaded and marked locked
                                        </div>
                                    @endif
                                </div>

                                <a
                                    href="{{ route('asset-documents.show', [$asset, $billDocument]) }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="btn btn-sm btn-outline-primary mb-3"
                                >
                                    <i class="bi bi-eye me-1"></i>
                                    View Bill/Invoice
                                </a>
                            @else
                                <div class="border rounded bg-light text-muted text-center p-4 mb-3">
                                    <i class="bi bi-file-earmark-x fs-2 d-block mb-2"></i>
                                    No bill or invoice uploaded.
                                </div>
                            @endif

                            @if($canManageBills)
                                <form
                                    method="POST"
                                    action="{{ route('asset-documents.bill.replace', $asset) }}"
                                    enctype="multipart/form-data"
                                >
                                    @csrf

                                    <label class="form-label small">
                                        {{ $billDocument ? 'Replace bill/invoice' : 'Upload bill/invoice' }}
                                    </label>

                                    <input
                                        type="file"
                                        name="file"
                                        class="form-control form-control-sm mb-2"
                                        accept=".jpg,.jpeg,.png,.webp,.pdf,image/jpeg,image/png,image/webp,application/pdf"
                                        required
                                    >

                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="bi bi-upload me-1"></i>
                                        {{ $billDocument ? 'Replace Bill/Invoice' : 'Upload Bill/Invoice' }}
                                    </button>
                                </form>

                                @if($billDocument)
                                    <form
                                        method="POST"
                                        action="{{ route('asset-documents.bill.destroy', $asset) }}"
                                        class="mt-2"
                                        onsubmit="return confirm('Delete this bill/invoice?');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash me-1"></i>
                                            Delete Bill/Invoice
                                        </button>
                                    </form>
                                @endif
                            @else
                                <div class="small text-muted">
                                    You do not have permission to manage bills/invoices.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Current assignment --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-diagram-3 me-2"></i>
                        Current Assignment
                    </h6>
                </div>

                <div class="card-body">
                    <h6 class="text-muted mb-3">Previous Occupancy</h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="text-muted small">Previous Occupant Department</div>
                            <div class="fw-semibold">
                                {{ $asset->previous_department_name ?: '—' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Previous Occupant Location</div>
                            <div class="fw-semibold">
                                {{ $asset->previous_location ?: '—' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Previous Room Number</div>
                            <div class="fw-semibold">
                                {{ $asset->previous_room_number ?: '—' }}
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="text-muted mb-3">Present Occupancy</h6>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-muted small">Present Occupant Department</div>
                            <div class="fw-semibold">
                                {{ $asset->department?->name ?? '—' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Present Occupant</div>
                            <div class="fw-semibold">
                                {{ $asset->occupant?->name
                                    ?? $asset->present_occupant_name
                                    ?? $asset->department?->name
                                    ?? 'Unassigned' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Present Occupant Location</div>
                            <div class="fw-semibold">
                                {{ $asset->present_location ?: $asset->location ?: '—' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Present Room Number</div>
                            <div class="fw-semibold">
                                {{ $asset->present_room_number ?: '—' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Occupancy history --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Occupancy History
                    </h6>
                </div>

                <div class="card-body">
                    @forelse($asset->occupancyHistories as $occupancy)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                                <span class="fw-semibold">
                                    {{ $occupancy->effective_at?->format('d-m-Y H:i') ?? '—' }}
                                </span>

                                <span class="small text-muted">
                                    Recorded by: {{ $occupancy->recorder?->name ?? 'System' }}
                                </span>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="small text-muted mb-1">Previous</div>
                                    <div>
                                        <strong>Department:</strong>
                                        {{ $occupancy->previous_department_name
                                            ?: $occupancy->previousDepartment?->name
                                            ?: '—' }}
                                    </div>
                                    <div>
                                        <strong>Location:</strong>
                                        {{ $occupancy->previous_location ?: '—' }}
                                    </div>
                                    <div>
                                        <strong>Room:</strong>
                                        {{ $occupancy->previous_room_number ?: '—' }}
                                    </div>
                                    <div>
                                        <strong>Occupant:</strong>
                                        {{ $occupancy->previous_occupant_name ?: '—' }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="small text-muted mb-1">Present</div>
                                    <div>
                                        <strong>Department:</strong>
                                        {{ $occupancy->present_department_name
                                            ?: $occupancy->presentDepartment?->name
                                            ?: '—' }}
                                    </div>
                                    <div>
                                        <strong>Location:</strong>
                                        {{ $occupancy->present_location ?: '—' }}
                                    </div>
                                    <div>
                                        <strong>Room:</strong>
                                        {{ $occupancy->present_room_number ?: '—' }}
                                    </div>
                                    <div>
                                        <strong>Occupant:</strong>
                                        {{ $occupancy->present_occupant_name ?: '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">
                            No occupancy history has been recorded yet.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Transfer history --}}
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
                                        $transferStatusClass = match ($transfer->status) {
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

        {{-- Right column: audit history --}}
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
                        <div class="text-muted">No history yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
