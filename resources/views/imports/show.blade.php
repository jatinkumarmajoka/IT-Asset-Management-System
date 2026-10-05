<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta

        name="viewport"

        content="width=device-width, initial-scale=1.0">

    <title>

        Import #{{ $import->id }}

    </title>

    <style>

        * {

            box-sizing: border-box;

        }

        body {

            font-family: Arial, sans-serif;

            background: #f5f7fa;

            margin: 0;

            padding: 30px;

            color: #222;

        }

        .container {

            max-width: 1200px;

            margin: auto;

        }

        .header {

            background: white;

            padding: 25px;

            border-radius: 10px;

            margin-bottom: 20px;

            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);

        }

        h1 {

            margin-top: 0;

            margin-bottom: 20px;

        }

        h2 {

            margin-top: 0;

        }

        .header p {

            margin: 10px 0;

        }

        .actions {

            margin-top: 20px;

        }

        .grid {

            display: grid;

            grid-template-columns: repeat(4, 1fr);

            gap: 15px;

            margin-bottom: 20px;

        }

        .card {

            background: white;

            padding: 20px;

            border-radius: 10px;

            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);

        }

        .label {

            color: #666;

            font-size: 14px;

            margin-bottom: 8px;

        }

        .value {

            font-size: 28px;

            font-weight: bold;

        }

        .details {

            background: white;

            padding: 25px;

            border-radius: 10px;

            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);

            overflow-x: auto;

        }

        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 800px;

        }

        th,

        td {

            text-align: left;

            padding: 12px;

            border-bottom: 1px solid #ddd;

            vertical-align: top;

        }

        th {

            background: #f1f3f5;

        }

        pre {

            margin: 0;

            white-space: pre-wrap;

            word-break: break-word;

            font-size: 12px;

            max-width: 500px;

        }

        .status {

            display: inline-block;

            padding: 6px 12px;

            border-radius: 20px;

            background: #0dcaf0;

            color: #000;

            font-weight: bold;

        }

        .status-processing {

            background: #ffc107;

        }

        .status-parsed {

            background: #198754;

            color: white;

        }

        .status-validated {

            background: #0d6efd;

            color: white;

        }

        .status-needs-review {
            background: #ffc107;
            color: #212529;
        }

        .status-imported {

            background: #198754;

            color: white;

        }

        .status-failed {

            background: #dc3545;

            color: white;

        }

        .btn {

            display: inline-block;

            padding: 10px 16px;

            border: none;

            background: #0d6efd;

            color: white;

            text-decoration: none;

            border-radius: 6px;

            margin-right: 8px;

            cursor: pointer;

            font-size: 14px;

        }

        .btn:hover {

            opacity: .9;

        }

        .btn-process {

            background: #198754;

        }

        .btn-danger {

            background: #dc3545;

        }

        .alert {

            padding: 15px;

            border-radius: 8px;

            margin-bottom: 20px;

        }

        .alert-success {

            background: #d1e7dd;

            color: #0f5132;

        }

        .alert-danger {

            background: #f8d7da;

            color: #842029;

        }

        .empty {

            padding: 40px 20px;

            text-align: center;

            color: #777;

        }

        .empty h3 {

            color: #444;

        }

        .info-box {

            background: #eef5ff;

            border-left: 4px solid #0d6efd;

            padding: 15px;

            margin-bottom: 20px;

            border-radius: 5px;

        }

        @media (max-width: 900px) {

            .grid {

                grid-template-columns: repeat(2, 1fr);

            }

        }

        @media (max-width: 600px) {

            body {

                padding: 15px;

            }

            .grid {

                grid-template-columns: 1fr;

            }

        }

    </style>

</head>

<body>

    <div class="container">

        {{-- Success message --}}

        @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

        </div>

        @endif

        {{-- Error messages --}}

        @if($errors->any())

        <div class="alert alert-danger">

            <strong>

                Import Processing Error

            </strong>

            <ul style="margin-bottom:0;">

                @foreach($errors->all() as $error)

                <li>

                    {{ $error }}

                </li>

                @endforeach

            </ul>

        </div>

        @endif

        {{-- Import information --}}

        <div class="header">

            <h1>

                Import #{{ $import->id }}

            </h1>

            <p>

                <strong>

                    File:

                </strong>

                {{ $import->original_filename }}

            </p>

            <p>

                <strong>

                    Type:

                </strong>

                {{ strtoupper($import->file_type) }}

            </p>

            <p>

                <strong>

                    Status:

                </strong>

                @php

                $statusClass = match($import->status) {

                'processing' => 'status-processing',

                'parsed' => 'status-parsed',

                'validated' => 'status-validated',

                'needs_review' => 'status-needs-review',
                'imported' => 'status-imported',

                'failed' => 'status-failed',

                default => '',

                };

                @endphp

                <span class="status {{ $statusClass }}">

                    {{ ucfirst($import->status) }}

                </span>

            </p>

            <div class="actions">

                {{-- Back --}}

                <a

                    href="{{ route('imports.index') }}"

                    class="btn">

                    ← Back to Imports

                </a>

                {{-- Process --}}

                @if(

                in_array(

                $import->status,

                ['uploaded', 'failed'],

                true

                )

                )

                <form

                    action="{{ route('imports.process', $import) }}"

                    method="POST"

                    style="display:inline;">

                    @csrf

                    <button

                        type="submit"

                        class="btn btn-process"

                        onclick="return confirm(

                    'Process this DOCX file and extract its records for review?'

                );">

                        ⚙ Process Import

                    </button>

                </form>

                @endif

                {{-- Validate --}}

                @if($import->status === 'parsed')

                <form

                    action="{{ route('imports.validate', $import) }}"

                    method="POST"

                    style="display:inline;">

                    @csrf

                    <button

                        type="submit"

                        class="btn"

                        onclick="return confirm(

                    'Validate the extracted import data? No assets will be created yet.'

                );">

                        ✓ Validate Import

                    </button>

                </form>

                @endif

                {{-- Confirm Import --}}
                @if(in_array($import->status, ['validated', 'needs_review'], true))
                    <form
                        action="{{ route('imports.confirm', $import) }}"
                        method="POST"
                        style="display:inline;"
                        onsubmit="return confirm(
                            'Import all valid records? Invalid rows will be skipped.'
                        );">
                        @csrf

                        <button
                            type="submit"
                            class="btn"
                            style="background:#198754;">

                            ✓ Confirm Import
                            @if($import->status === 'needs_review')
                                ({{ $import->valid_rows }} valid rows)
                            @endif
                        </button>
                    </form>
                @endif

            </div>

        </div>

        {{-- Statistics --}}

        <div class="grid">

            <div class="card">

                <div class="label">

                    Total Rows

                </div>

                <div class="value">

                    {{ $import->total_rows ?? 0 }}

                </div>

            </div>

            <div class="card">

                <div class="label">

                    Valid Rows

                </div>

                <div class="value">

                    {{ $import->valid_rows ?? 0 }}

                </div>

            </div>

            <div class="card">

                <div class="label">

                    Invalid Rows

                </div>

                <div class="value">

                    {{ $import->invalid_rows ?? 0 }}

                </div>

            </div>

            <div class="card">

                <div class="label">

                    Imported Rows

                </div>

                <div class="value">

                    {{ $import->imported_rows ?? 0 }}

                </div>

            </div>

        </div>

        {{-- Information before processing --}}

        @if($import->status === 'uploaded')

        <div class="info-box">

            <strong>

                Ready for processing

            </strong>

            <br>

            This file has been uploaded successfully.

            Click <strong>Process Import</strong> to read

            the DOCX tables and create temporary import

            records.

            <br><br>

            <strong>

                No assets will be created at this stage.

            </strong>

        </div>

        @endif

        {{-- Review information --}}
        @if($import->status === 'needs_review')
            <div class="info-box" style="background:#fff8e1; border-left-color:#ffc107;">
                <strong>Review Required</strong>
                <br>
                {{ $import->valid_rows }} source rows are valid and can be imported.
                {{ $import->invalid_rows }} source row{{ $import->invalid_rows == 1 ? '' : 's' }}
                contain{{ $import->invalid_rows == 1 ? 's' : '' }} validation errors and will be skipped.
                <br><br>
                <strong>Confirm Import</strong> will import only the valid rows.
            </div>
        @endif

        {{-- Extracted rows --}}

        <div class="details">

            <h2>

                Extracted Import Rows

            </h2>

            @if($import->rows->count() > 0)

            <table>

                <thead>

                    <tr>

                        <th>

                            Row

                        </th>

                        <th>

                            Status

                        </th>

                        <th>

                            Raw Data

                        </th>

                        <th>

                            Errors

                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($import->rows as $row)

                    <tr>

                        <td>

                            {{ $row->row_number }}

                        </td>

                        <td>

                            {{ ucfirst($row->status) }}

                        </td>

                        <td>

                            <pre>{{ json_encode(

                                    $row->raw_data,

                                    JSON_PRETTY_PRINT |

                                    JSON_UNESCAPED_UNICODE

                                ) }}</pre>

                        </td>

                        <td>

                            @if($row->errors)

                            <pre>{{ json_encode(

                                        $row->errors,

                                        JSON_PRETTY_PRINT |

                                        JSON_UNESCAPED_UNICODE

                                    ) }}</pre>

                            @else

                            —

                            @endif

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

            @else

            <div class="empty">

                <h3>

                    No extracted rows yet

                </h3>

                <p>

                    @if($import->status === 'uploaded')

                    This file has been uploaded but

                    has not been processed yet.

                    @elseif($import->status === 'processing')

                    The file is currently being processed.

                    @elseif($import->status === 'failed')

                    Processing failed. Check the

                    error message above and try again.

                    @else

                    No rows were extracted from this file.

                    @endif

                </p>

            </div>

            @endif

        </div>

    </div>

</body>

</html>
