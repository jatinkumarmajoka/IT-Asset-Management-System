@extends('layouts.app')

@section('page-title', 'Import Assets')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2 class="mb-1">Import Assets</h2>

        <p class="text-muted mb-0">
            Import asset records from Excel, CSV, PDF and Word files.
        </p>
    </div>

</div>


{{-- Success Message --}}

@if(session('success'))

    <div class="alert alert-success alert-dismissible fade show" role="alert">

        <i class="bi bi-check-circle me-1"></i>

        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>

    </div>

@endif


{{-- Validation Errors --}}

@if($errors->any())

    <div class="alert alert-danger alert-dismissible fade show" role="alert">

        <div class="fw-semibold mb-2">
            Please correct the following:
        </div>

        <ul class="mb-0">

            @foreach($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>

    </div>

@endif


{{-- Upload Card --}}

<div class="card shadow-sm border-0 mb-4">

    <div class="card-body">

        <h5 class="mb-2">

            <i class="bi bi-cloud-arrow-up me-1"></i>

            Upload Asset File

        </h5>

        <p class="text-muted mb-4">

            Select a file containing asset records.

        </p>


        <form
            method="POST"
            action="{{ route('imports.store') }}"
            enctype="multipart/form-data"
        >

            @csrf

            <div class="row g-3 align-items-end">

                <div class="col-md-8">

                    <label
                        for="file"
                        class="form-label"
                    >
                        Select File
                    </label>

                    <input
                        type="file"
                        id="file"
                        name="file"
                        class="form-control @error('file') is-invalid @enderror"
                        accept=".xlsx,.xls,.csv,.pdf,.docx"
                        required
                    >

                    @error('file')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                    <div class="form-text">

                        Supported formats:
                        <strong>XLSX, XLS, CSV, PDF, DOCX</strong>.

                        Maximum size: <strong>20 MB</strong>.

                    </div>

                </div>


                <div class="col-md-4">

                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                    >

                        <i class="bi bi-upload me-1"></i>

                        Upload File

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- Import History --}}

<div class="card shadow-sm border-0">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <h5 class="mb-1">

                    <i class="bi bi-clock-history me-1"></i>

                    Import History

                </h5>

                <small class="text-muted">

                    Previously uploaded files

                </small>

            </div>

        </div>


        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead>

                    <tr>

                        <th>#</th>

                        <th>File</th>

                        <th>Type</th>

                        <th>Status</th>

                        <th>Total</th>

                        <th>Valid</th>

                        <th>Invalid</th>

                        <th>Imported</th>

                        <th>Uploaded By</th>

                        <th class="text-end">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($imports as $import)

                        <tr>

                            <td>
                                #{{ $import->id }}
                            </td>


                            <td>

                                <div class="fw-semibold">

                                    {{ $import->original_filename }}

                                </div>

                                <small class="text-muted">

                                    {{ $import->created_at->format('d M Y, h:i A') }}

                                </small>

                            </td>


                            <td>

                                <span class="badge text-bg-secondary text-uppercase">

                                    {{ $import->file_type }}

                                </span>

                            </td>


                            <td>

                                @if($import->status === 'uploaded')

                                    <span class="badge text-bg-info">
                                        Uploaded
                                    </span>

                                @elseif($import->status === 'processing')

                                    <span class="badge text-bg-warning">
                                        Processing
                                    </span>

                                @elseif($import->status === 'preview')

                                    <span class="badge text-bg-primary">
                                        Ready for Preview
                                    </span>

                                @elseif($import->status === 'completed')

                                    <span class="badge text-bg-success">
                                        Completed
                                    </span>

                                @elseif($import->status === 'failed')

                                    <span class="badge text-bg-danger">
                                        Failed
                                    </span>

                                @else

                                    <span class="badge text-bg-secondary">
                                        {{ ucfirst($import->status) }}
                                    </span>

                                @endif

                            </td>


                            <td>
                                {{ $import->total_rows }}
                            </td>


                            <td>
                                {{ $import->valid_rows }}
                            </td>


                            <td>
                                {{ $import->invalid_rows }}
                            </td>


                            <td>
                                {{ $import->imported_rows }}
                            </td>


                            <td>
                                {{ $import->uploader?->name ?? 'System' }}
                            </td>


                            <td class="text-end">

                                <a
                                    href="{{ route('imports.show', $import) }}"
                                    class="btn btn-sm btn-outline-primary"
                                    title="View Import"
                                >

                                    <i class="bi bi-eye"></i>

                                </a>


                                <form
                                    method="POST"
                                    action="{{ route('imports.destroy', $import) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this import?');"
                                >

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Delete Import"
                                    >

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="10"
                                class="text-center text-muted py-5"
                            >

                                <i class="bi bi-file-earmark-arrow-up fs-2 d-block mb-2"></i>

                                <div class="fw-semibold">
                                    No imports yet
                                </div>

                                <small>
                                    Upload an XLSX, XLS, CSV, PDF or DOCX file to begin.
                                </small>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($imports->hasPages())

            <div class="mt-3">

                {{ $imports->links() }}

            </div>

        @endif

    </div>

</div>

@endsection