<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Import #{{ $import->id }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 30px;
            color: #222;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        .header {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
        }

        h1 {
            margin-top: 0;
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
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
        }

        .label {
            color: #666;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .value {
            font-size: 24px;
            font-weight: bold;
        }

        .details {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f1f3f5;
        }

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            background: #0dcaf0;
            color: #000;
            font-weight: bold;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            background: #0d6efd;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-right: 8px;
        }

        .empty {
            padding: 30px;
            text-align: center;
            color: #777;
        }

        @media (max-width: 800px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="header">

        <h1>Import #{{ $import->id }}</h1>

        <p>
            <strong>File:</strong>
            {{ $import->original_filename }}
        </p>

        <p>
            <strong>Type:</strong>
            {{ strtoupper($import->file_type) }}
        </p>

        <p>
            <strong>Status:</strong>
            <span class="status">
                {{ ucfirst($import->status) }}
            </span>
        </p>

        <a href="{{ route('imports.index') }}" class="btn">
            ← Back to Imports
        </a>

    </div>


    <div class="grid">

        <div class="card">
            <div class="label">Total Rows</div>
            <div class="value">
                {{ $import->total_rows }}
            </div>
        </div>

        <div class="card">
            <div class="label">Valid Rows</div>
            <div class="value">
                {{ $import->valid_rows }}
            </div>
        </div>

        <div class="card">
            <div class="label">Invalid Rows</div>
            <div class="value">
                {{ $import->invalid_rows }}
            </div>
        </div>

        <div class="card">
            <div class="label">Imported Rows</div>
            <div class="value">
                {{ $import->imported_rows }}
            </div>
        </div>

    </div>


    <div class="details">

        <h2>Extracted Import Rows</h2>

        @if($import->rows->count() > 0)

            <table>

                <thead>
                    <tr>
                        <th>Row</th>
                        <th>Status</th>
                        <th>Raw Data</th>
                        <th>Errors</th>
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
                            <pre>{{ json_encode($row->raw_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </td>

                        <td>
                            @if($row->errors)
                                <pre>{{ json_encode($row->errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
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

                <h3>No extracted rows yet</h3>

                <p>
                    This file has been uploaded but has not been processed.
                </p>

                <p>
                    Once the DOCX extraction process is implemented,
                    extracted records will appear here.
                </p>

            </div>

        @endif

    </div>

</div>

</body>
</html>