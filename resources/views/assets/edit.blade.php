@extends('layouts.app')

@section('page-title', 'Edit Asset')

@section('content')

<div class="container-fluid">

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="mb-1">Edit Asset</h3>

            <div class="text-muted">
                Asset Code:
                <strong>{{ $asset->asset_code }}</strong>
            </div>
        </div>

        <div class="d-flex gap-2">

            <a
                href="{{ route('assets.show', $asset) }}"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-eye me-1"></i>
                View Asset
            </a>

            <a
                href="{{ route('assets.index') }}"
                class="btn btn-outline-dark"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back
            </a>

        </div>

    </div>


    {{-- =========================================================
         MESSAGES
    ========================================================== --}}

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle me-1"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    @if($errors->any())

        <div class="alert alert-danger">

            <strong>Please correct the following:</strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif


    {{-- =========================================================
         ASSET INFORMATION
    ========================================================== --}}

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-white py-3">

            <h5 class="mb-0">
                <i class="bi bi-box-seam me-2"></i>
                Asset Information
            </h5>

        </div>

        <div class="card-body">

            <form
                method="POST"
                action="{{ route('assets.update', $asset) }}"
            >

                @method('PUT')

                @include('assets._form', [
                    'buttonText' => 'Save Changes'
                ])

            </form>

        </div>

    </div>


    {{-- =========================================================
         DOCUMENT DATA
    ========================================================== --}}

    @php

        $assetImage = $asset->documents
            ->firstWhere('document_type', 'asset_image');

        $billInvoice = $asset->documents
            ->firstWhere('document_type', 'bill_invoice');

    @endphp


    {{-- =========================================================
         DOCUMENTS CARD
    ========================================================== --}}

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h5 class="mb-1">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        Asset Documents
                    </h5>

                    <small class="text-muted">
                        Asset images can be replaced or deleted.
                        Bill / Invoice documents are locked after upload.
                    </small>

                </div>

                <span class="badge bg-info text-dark">
                    <i class="bi bi-shield-check me-1"></i>
                    Document Controls
                </span>

            </div>

        </div>


        <div class="card-body">

            {{-- IMPORTANT:
                 This row contains exactly two columns.
            --}}

            <div class="row g-4 align-items-stretch">


                {{-- =================================================
                     ASSET IMAGE
                ================================================== --}}

                <div class="col-12 col-lg-6">

                    <div class="border rounded-3 p-3 h-100">

                        {{-- Header --}}

                        <div class="d-flex justify-content-between align-items-start mb-3">

                            <div>

                                <h5 class="mb-1">
                                    <i class="bi bi-image me-2"></i>
                                    Asset Image
                                </h5>

                                <small class="text-muted">
                                    JPG, JPEG, PNG or WEBP
                                </small>

                            </div>


                            @if($assetImage)

                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Uploaded
                                </span>

                            @else

                                <span class="badge bg-secondary">
                                    Not Uploaded
                                </span>

                            @endif

                        </div>


                        {{-- =========================================
                             EXISTING IMAGE
                        ========================================== --}}

                        @if($assetImage)

                            @if($assetImage->isImage())

                                <div class="text-center mb-3">

                                    <a
                                        href="{{ route(
                                            'asset-documents.show',
                                            [
                                                'asset' => $asset,
                                                'document' => $assetImage
                                            ]
                                        ) }}"
                                        target="_blank"
                                    >

                                        <img
                                            src="{{ route(
                                                'asset-documents.show',
                                                [
                                                    'asset' => $asset,
                                                    'document' => $assetImage
                                                ]
                                            ) }}"
                                            alt="Asset Image"
                                            class="img-fluid rounded border"
                                            style="
                                                width: 100%;
                                                max-height: 320px;
                                                object-fit: contain;
                                            "
                                        >

                                    </a>

                                </div>

                            @endif


                            {{-- File information --}}

                            <div class="small mb-3">

                                <div class="mb-1">
                                    <strong>File:</strong>
                                    {{ $assetImage->original_name }}
                                </div>

                                <div class="mb-1">
                                    <strong>Size:</strong>
                                    {{ number_format($assetImage->file_size / 1024, 2) }}
                                    KB
                                </div>

                                <div>
                                    <strong>Uploaded:</strong>
                                    {{ $assetImage->created_at?->format('d M Y, h:i A') }}
                                </div>

                            </div>


                            {{-- =====================================
                                 ACTIONS
                            ====================================== --}}

                            <div class="d-flex flex-wrap gap-2">

                                {{-- View --}}

                                <a
                                    href="{{ route(
                                        'asset-documents.show',
                                        [
                                            'asset' => $asset,
                                            'document' => $assetImage
                                        ]
                                    ) }}"
                                    target="_blank"
                                    class="btn btn-outline-primary btn-sm"
                                >

                                    <i class="bi bi-eye me-1"></i>
                                    View Image

                                </a>


                                {{-- Replace --}}

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'asset-documents.image.replace',
                                        $asset
                                    ) }}"
                                    enctype="multipart/form-data"
                                    class="d-flex flex-wrap gap-2"
                                >

                                    @csrf

                                    <input
                                        type="file"
                                        name="file"
                                        class="form-control form-control-sm"
                                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                        required
                                        style="max-width: 230px;"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-warning btn-sm"
                                        onclick="return confirm('Replace the current asset image?')"
                                    >

                                        <i class="bi bi-arrow-repeat me-1"></i>
                                        Replace

                                    </button>

                                </form>


                                {{-- Delete --}}

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'asset-documents.image.destroy',
                                        $asset
                                    ) }}"
                                    onsubmit="return confirm('Delete the current asset image?')"
                                >

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-outline-danger btn-sm"
                                    >

                                        <i class="bi bi-trash me-1"></i>
                                        Delete

                                    </button>

                                </form>

                            </div>


                            <div class="alert alert-info mt-3 mb-0">

                                <small>

                                    <i class="bi bi-info-circle me-1"></i>

                                    Authorized administrators can replace
                                    or delete the asset image.

                                </small>

                            </div>


                        @else

                            {{-- =========================================
                                 NEW IMAGE UPLOAD
                            ========================================== --}}

                            <form
                                method="POST"
                                action="{{ route(
                                    'asset-documents.store',
                                    $asset
                                ) }}"
                                enctype="multipart/form-data"
                            >

                                @csrf

                                <input
                                    type="hidden"
                                    name="document_type"
                                    value="asset_image"
                                >

                                <div class="mb-3">

                                    <label
                                        for="asset_image"
                                        class="form-label fw-semibold"
                                    >
                                        Select Asset Image
                                    </label>

                                    <input
                                        type="file"
                                        name="file"
                                        id="asset_image"
                                        class="form-control"
                                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                        required
                                    >

                                    <div class="form-text">
                                        Maximum file size: 10 MB.
                                    </div>

                                </div>

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                >

                                    <i class="bi bi-upload me-1"></i>
                                    Upload Asset Image

                                </button>

                            </form>

                        @endif

                    </div>

                </div>


                {{-- =================================================
                     BILL / INVOICE
                ================================================== --}}

                <div class="col-12 col-lg-6">

                    <div class="border rounded-3 p-3 h-100">

                        {{-- Header --}}

                        <div class="d-flex justify-content-between align-items-start mb-3">

                            <div>

                                <h5 class="mb-1">
                                    <i class="bi bi-receipt me-2"></i>
                                    Bill / Invoice
                                </h5>

                                <small class="text-muted">
                                    PDF or supported image
                                </small>

                            </div>


                            @if($billInvoice)

                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Uploaded
                                </span>

                            @else

                                <span class="badge bg-secondary">
                                    Not Uploaded
                                </span>

                            @endif

                        </div>


                        {{-- =========================================
                             EXISTING BILL
                        ========================================== --}}

                        @if($billInvoice)

                            <div class="text-center mb-4">

                                @if($billInvoice->mime_type === 'application/pdf')

                                    <i
                                        class="bi bi-file-earmark-pdf text-danger"
                                        style="font-size: 5rem;"
                                    ></i>

                                    <div class="mt-2 fw-semibold">
                                        PDF Document
                                    </div>

                                @elseif($billInvoice->isImage())

                                    <img
                                        src="{{ route(
                                            'asset-documents.show',
                                            [
                                                'asset' => $asset,
                                                'document' => $billInvoice
                                            ]
                                        ) }}"
                                        alt="Bill / Invoice"
                                        class="img-fluid rounded border"
                                        style="
                                            width: 100%;
                                            max-height: 320px;
                                            object-fit: contain;
                                        "
                                    >

                                @else

                                    <i
                                        class="bi bi-file-earmark-text"
                                        style="font-size: 5rem;"
                                    ></i>

                                @endif

                            </div>


                            {{-- File information --}}

                            <div class="small mb-3">

                                <div class="mb-1">
                                    <strong>File:</strong>
                                    {{ $billInvoice->original_name }}
                                </div>

                                <div class="mb-1">
                                    <strong>Size:</strong>
                                    {{ number_format($billInvoice->file_size / 1024, 2) }}
                                    KB
                                </div>

                                <div class="mb-1">
                                    <strong>Uploaded:</strong>
                                    {{ $billInvoice->created_at?->format('d M Y, h:i A') }}
                                </div>

                                <div>

                                    <strong>Status:</strong>

                                    <span class="text-success">
                                        <i class="bi bi-lock-fill"></i>
                                        Locked
                                    </span>

                                </div>

                            </div>


                            {{-- View --}}

                            <a
                                href="{{ route(
                                    'asset-documents.show',
                                    [
                                        'asset' => $asset,
                                        'document' => $billInvoice
                                    ]
                                ) }}"
                                target="_blank"
                                class="btn btn-outline-primary btn-sm"
                            >

                                <i class="bi bi-eye me-1"></i>
                                View Bill / Invoice

                            </a>


                            <div class="alert alert-warning mt-3 mb-0">

                                <small>

                                    <i class="bi bi-shield-lock me-1"></i>

                                    This Bill / Invoice cannot be replaced
                                    or deleted after upload.

                                </small>

                            </div>


                        @else

                            {{-- =========================================
                                 NEW BILL UPLOAD
                            ========================================== --}}

                            <form
                                method="POST"
                                action="{{ route(
                                    'asset-documents.store',
                                    $asset
                                ) }}"
                                enctype="multipart/form-data"
                            >

                                @csrf

                                <input
                                    type="hidden"
                                    name="document_type"
                                    value="bill_invoice"
                                >

                                <div class="mb-3">

                                    <label
                                        for="bill_invoice"
                                        class="form-label fw-semibold"
                                    >
                                        Select Bill / Invoice
                                    </label>

                                    <input
                                        type="file"
                                        name="file"
                                        id="bill_invoice"
                                        class="form-control"
                                        accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/jpeg,image/png,image/webp"
                                        required
                                    >

                                    <div class="form-text">
                                        Maximum file size: 10 MB.
                                    </div>

                                </div>

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                >

                                    <i class="bi bi-upload me-1"></i>
                                    Upload Bill / Invoice

                                </button>

                            </form>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection