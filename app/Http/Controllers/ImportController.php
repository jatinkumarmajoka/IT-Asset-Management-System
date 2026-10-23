<?php

namespace App\Http\Controllers;

use App\Models\ImportBatch;
use App\Services\Imports\AssetImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function index()
    {
        $imports = ImportBatch::with('uploader')
            ->latest()
            ->paginate(15);

        return view('imports.index', compact('imports'));
    }

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
            return back()->withErrors([
                'file' => 'Only XLSX, XLS, CSV, PDF and DOCX files are allowed.',
            ])->withInput();
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
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()
            ->route('imports.index')
            ->with(
                'success',
                'File uploaded successfully. Import #'.
                $import->id.
                ' is ready for processing.'
            );
    }

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

    public function process(
        ImportBatch $import,
        AssetImportService $assetImportService
    ) {
        try {
            $assetImportService->process($import);

            $import->refresh();

            return redirect()
                ->route('imports.show', $import)
                ->with(
                    'success',
                    'Document processed successfully. '.
                    $import->total_rows.
                    ' source rows extracted for review.'
                );
        } catch (\Throwable $e) {

            report($e);

            return redirect()
                ->route('imports.show', $import)
                ->withErrors([
                    'import' => 'Import processing failed: '.
                        $e->getMessage(),
                ]);
        }
    }

    public function validateImport(
        ImportBatch $import,
        AssetImportService $assetImportService
    ) {
        try {
            $assetImportService->validate($import);

            $import->refresh();

            return redirect()
                ->route('imports.show', $import)
                ->with(
                    'success',
                    'Import data validated and prepared for review.'
                );
        } catch (\Throwable $e) {

            report($e);

            return redirect()
                ->route('imports.show', $import)
                ->withErrors([
                    'import' => 'Validation failed: '.
                        $e->getMessage(),
                ]);
        }
    }

    /**
     * Confirm the import.
     *
     * IMPORTANT:
     * A needs_review import is allowed to continue.
     *
     * Valid rows are imported.
     * Invalid rows are skipped.
     */
    public function confirm(
        ImportBatch $import,
        AssetImportService $assetImportService
    ) {
        try {

            if (! in_array(
                $import->status,
                ['validated', 'needs_review'],
                true
            )) {
                return redirect()
                    ->route('imports.show', $import)
                    ->withErrors([
                        'import' => 'This import is not ready for confirmation. '.
                            'Current status: '.
                            $import->status,
                    ]);
            }

            $imported = $assetImportService->confirmImport($import);

            return redirect()
                ->route('imports.show', $import)
                ->with(
                    'success',
                    $imported.
                    ' valid assets imported successfully. '.
                    'Invalid rows were skipped.'
                );

        } catch (\Throwable $e) {

            report($e);

            return redirect()
                ->route('imports.show', $import)
                ->withErrors([
                    'import' => 'Final import failed: '.
                        $e->getMessage(),
                ]);
        }
    }

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
