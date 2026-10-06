@extends('layouts.app')



@section('page-title', 'Import Assets')



@section('content')

<style>
.import-page{width:100%;max-width:100%;min-width:0}
.import-page .page-header{
    width:100%;
    margin:0 0 32px;
    text-align:center;
}
.import-page .page-header h2{
    margin:0 0 8px;
    font-size:2.5rem;
    font-weight:700;
    line-height:1.2;
}
.import-page .page-header p{
    margin:0;
    color:#6c757d;
    font-size:1.15rem;
}
.import-page .upload-card,.import-page .history-card{width:100%;border:0;border-radius:14px}
.import-page .upload-card .card-body{
    padding:36px 40px;
    min-width:0;
}

.import-page .upload-header{
    text-align:center;
    margin-bottom:28px;
}

.import-page .upload-header h5{
    font-size:1.45rem;
    font-weight:600;
    margin-bottom:8px;
}

.import-page .upload-header p{
    margin-bottom:0;
    color:#6c757d;
}

.import-page .upload-form{
    width:100%;
    max-width:900px;
    margin:0 auto;
}

.import-page .upload-file-row{
    display:grid;
    grid-template-columns:minmax(0,1fr) 170px;
    gap:14px;
    align-items:end;
}

.import-page .upload-file-field{
    min-width:0;
}

.import-page .upload-file-field .form-label{
    display:block;
    margin-bottom:7px;
    font-weight:600;
}

.import-page .upload-file-field .form-control{
    width:100%;
    min-width:0;
    height:44px;
}

.import-page .upload-button{
    height:44px;
    width:100%;
    white-space:nowrap;
}

.import-page .upload-help{
    margin-top:10px;
    color:#6c757d;
    font-size:.9rem;
    line-height:1.5;
}

.import-page .upload-help strong{
    color:#495057;
}

.import-page .upload-submit{
    margin-top:18px;
    text-align:center;
}

.import-page .upload-submit .upload-button{
    width:170px;
}

@media(max-width:767.98px){
    .import-page .upload-card .card-body{
        padding:24px 20px;
    }

    .import-page .upload-file-row{
        grid-template-columns:1fr;
    }

    .import-page .upload-submit{
        text-align:stretch;
    }

    .import-page .upload-submit .upload-button{
        width:100%;
    }
}
.import-page .history-table-wrap{
    width:100%;
    max-width:100%;
    overflow-x:auto;
    overflow-y:hidden;
    border:1px solid #dee2e6;
    border-radius:10px;
}

.import-page .history-table{
    width:100%;
    min-width:0;
    table-layout:fixed;
    border-collapse:separate;
    border-spacing:0;
    margin:0;
}

.import-page .history-table th,
.import-page .history-table td{
    padding:14px 8px;
    vertical-align:middle;
    border-bottom:1px solid #e9ecef;
    overflow:hidden;
    white-space:nowrap;
}

.import-page .history-table thead th{
    background:#f8f9fa;
    color:#212529;
    font-size:.95rem;
    font-weight:700;
    text-align:center;
}

.import-page .history-table tbody td{
    text-align:center;
}

.import-page .history-table tbody tr:last-child td{
    border-bottom:0;
}

/* Compact File column + full labels for all remaining columns. */
.import-page .history-table th:nth-child(1),
.import-page .history-table td:nth-child(1){
    width:22%;
    text-align:left;
}

.import-page .history-table th:nth-child(2),
.import-page .history-table td:nth-child(2){width:9%;}

.import-page .history-table th:nth-child(3),
.import-page .history-table td:nth-child(3){width:12%;}

.import-page .history-table th:nth-child(4),
.import-page .history-table td:nth-child(4){width:7%;}

.import-page .history-table th:nth-child(5),
.import-page .history-table td:nth-child(5){width:7%;}

.import-page .history-table th:nth-child(6),
.import-page .history-table td:nth-child(6){width:8%;}

.import-page .history-table th:nth-child(7),
.import-page .history-table td:nth-child(7){width:9%;}

.import-page .history-table th:nth-child(8),
.import-page .history-table td:nth-child(8){width:10%;}

.import-page .history-table th:nth-child(9),
.import-page .history-table td:nth-child(9){width:16%;}

.import-page .file-name{
    display:block;
    width:100%;
    max-width:100%;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
    font-weight:600;
}

.import-page .file-date{
    display:block;
    margin-top:3px;
    color:#6c757d;
    font-size:.8rem;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}

.import-page .actions{
    white-space:nowrap;
    overflow:visible !important;
    text-overflow:clip !important;
}

.import-page .actions .btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:36px;
    height:36px;
    margin:0 3px;
    padding:0;
}

.import-page .empty-state{
    padding:45px 20px;
    text-align:center;
    color:#6c757d;
}

@media(max-width:1100px){.import-page .history-table{min-width:1120px}}
@media(max-width:767.98px){.import-page .history-card .card-body{padding:20px}.import-page .history-table{min-width:1120px}}
</style>

<div class="import-page">




<div class="page-header">
    <h2>Import Assets</h2>
    <p>Import asset records from Excel, CSV, PDF and Word files.</p>
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



<div class="card shadow-sm upload-card mb-4">



    <div class="card-body">



        <div class="upload-header">
            <h5>
                <i class="bi bi-cloud-arrow-up me-1"></i>
                Upload Asset File
            </h5>

            <p>
                Select a file containing asset records.
            </p>
        </div>





        <form

            method="POST"

            action="{{ route('imports.store') }}"

            enctype="multipart/form-data"

        >



            @csrf



            <div class="upload-form">
            <div class="upload-file-row">

                <div class="upload-file-field">
                    <label for="file" class="form-label">
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

                    <div class="upload-help">
                        Supported formats:
                        <strong>XLSX, XLS, CSV, PDF, DOCX</strong>
                        <span class="mx-2">•</span>
                        Maximum size:
                        <strong>20 MB</strong>
                    </div>
                </div>

                <div class="upload-submit">
                    <button type="submit" class="btn btn-primary upload-button">
                        <i class="bi bi-upload me-1"></i>
                        Upload File
                    </button>
                </div>

            </div>
        </div>
        </form>



    </div>



</div>





{{-- Import History --}}



<div class="card shadow-sm history-card">



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





        <div class="history-table-wrap">
            <table class="history-table">
                <thead>
                    <tr><th>File</th><th>Type</th><th>Status</th>
                        <th>Total</th><th>Valid</th><th>Invalid</th><th>Imported</th>
                        <th>Uploaded By</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($imports as $import)
                    <tr>
                        <td>
                            <span class="file-name" title="{{ $import->original_filename }}">{{ $import->original_filename }}</span>
                            <span class="file-date">{{ $import->created_at->format('d M Y, h:i A') }}</span>
                        </td>
                        <td><span class="badge text-bg-secondary text-uppercase">{{ $import->file_type }}</span></td>
                        <td>
                            @if($import->status === 'uploaded')
                                <span class="badge text-bg-info">Uploaded</span>
                            @elseif($import->status === 'processing')
                                <span class="badge text-bg-warning">Processing</span>
                            @elseif($import->status === 'preview')
                                <span class="badge text-bg-primary">Ready for Preview</span>
                            @elseif($import->status === 'completed' || $import->status === 'imported')
                                <span class="badge text-bg-success">{{ ucfirst($import->status) }}</span>
                            @elseif($import->status === 'failed')
                                <span class="badge text-bg-danger">Failed</span>
                            @else
                                <span class="badge text-bg-secondary">{{ ucfirst($import->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $import->total_rows }}</td>
                        <td>{{ $import->valid_rows }}</td>
                        <td>{{ $import->invalid_rows }}</td>
                        <td>{{ $import->imported_rows }}</td>
                        <td>{{ $import->uploader?->name ?? 'System' }}</td>
                        <td class="actions">
                            <a href="{{ route('imports.show', $import) }}" class="btn btn-sm btn-outline-primary" title="View Import">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form method="POST" action="{{ route('imports.destroy', $import) }}" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this import?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Import">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <i class="bi bi-file-earmark-arrow-up fs-2 d-block mb-2"></i>
                                <div class="fw-semibold">No imports yet</div>
                                <small>Upload an XLSX, XLS, CSV, PDF or DOCX file to begin.</small>
                            </div>
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



</div>

@endsection