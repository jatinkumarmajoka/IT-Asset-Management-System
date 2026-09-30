<?php

namespace App\Http\Controllers;

use App\Models\ImportBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    /**
     * Display the import page and import history.
     */
    public function index()
{
    $imports = ImportBatch::with('uploader')
        ->latest()
        ->paginate(15);

    return view('imports.index', compact('imports'));
}

    /**
     * Upload an import file.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:20480',
                'mimes:xlsx,xls,csv,pdf,docx',
            ],
        ]);

        $file = $request->file('file');

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        $allowedExtensions = [
            'xlsx',
            'xls',
            'csv',
            'pdf',
            'docx',
        ];

        if (! in_array($extension, $allowedExtensions, true)) {
            return back()
                ->withErrors([
                    'file' => 'Only XLSX, XLS, CSV, PDF and DOCX files are allowed.',
                ])
                ->withInput();
        }

        $originalFilename = $file->getClientOriginalName();

        $storedPath = $file->store(
            'imports',
            'local'
        );

        $import = ImportBatch::create([
            'original_filename' => $originalFilename,
            'stored_path' => $storedPath,
            'file_type' => $extension,
            'status' => 'uploaded',
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()
            ->route('imports.index')
            ->with(
                'success',
                'File uploaded successfully. Import #'
                . $import->id
                . ' is ready for processing.'
            );
    }

    /**
     * Display a single import.
     */
    public function show(ImportBatch $import)
    {
        $import->load([
            'uploader',
            'rows',
        ]);

        return view(
            'imports.show',
            compact('import')
        );
    }

    /**
     * Delete an import batch and its stored file.
     */
    public function destroy(ImportBatch $import)
    {
        if ($import->stored_path) {
            Storage::disk('local')->delete(
                $import->stored_path
            );
        }

        $import->delete();

        return redirect()
            ->route('imports.index')
            ->with(
                'success',
                'Import deleted successfully.'
            );
    }
}