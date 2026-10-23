<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetDocumentController extends Controller
{
    /**
     * Check permission for an asset document action.
     */
    private function authorizeDocumentAction(string $type): void
    {
        $user = auth()->user();

        abort_unless($user, 401);

        if ($user->isSuperAdmin()) {
            return;
        }

        $permission = match ($type) {
            AssetDocument::TYPE_ASSET_IMAGE => 'assets',
            AssetDocument::TYPE_BILL_INVOICE => 'bills',
            default => null,
        };

        abort_unless(
            $permission && $user->canManageModule($permission),
            403,
            'You do not have permission to manage this document.'
        );
    }

    /**
     * Validate uploaded documents.
     */
    private function validateDocument(
        Request $request,
        bool $imageOnly = false
    ): void {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240',
                $imageOnly
                    ? 'mimes:jpg,jpeg,png,webp'
                    : 'mimes:jpg,jpeg,png,webp,pdf',
            ],
        ]);
    }

    /**
     * Store a file on the private disk.
     */
    private function storePrivateFile(
        Request $request,
        Asset $asset
    ): array {
        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = Str::uuid()->toString().'.'.$extension;

        $path = $file->storeAs(
            'assets/'.$asset->id,
            $filename,
            'private'
        );

        if (! $path) {
            abort(500, 'The uploaded file could not be stored.');
        }

        return [
            'file' => $file,
            'path' => $path,
        ];
    }

    /**
     * Upload an asset image or bill/invoice when no document of
     * that type already exists.
     */
    public function store(
        Request $request,
        Asset $asset
    ): RedirectResponse {
        $request->validate([
            'document_type' => [
                'required',
                'in:asset_image,bill_invoice',
            ],
        ]);

        $type = $request->input('document_type');

        $this->authorizeDocumentAction($type);

        $this->validateDocument(
            $request,
            $type === AssetDocument::TYPE_ASSET_IMAGE
        );

        $existing = $asset->documents()
            ->where('document_type', $type)
            ->first();

        if ($existing) {
            return back()->withErrors([
                'file' => $type === AssetDocument::TYPE_ASSET_IMAGE
                    ? 'An asset image already exists. Use Replace Image.'
                    : 'A bill already exists. Use Replace Bill to upload a new version.',
            ]);
        }

        $stored = $this->storePrivateFile($request, $asset);
        $file = $stored['file'];

        try {
            $asset->documents()->create([
                'document_type' => $type,
                'original_name' => $file->getClientOriginalName(),
                'stored_path' => $stored['path'],
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
                'locked_at' => $type === AssetDocument::TYPE_BILL_INVOICE
                    ? now()
                    : null,
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('private')->delete($stored['path']);
            throw $exception;
        }

        return back()->with(
            'success',
            $type === AssetDocument::TYPE_ASSET_IMAGE
                ? 'Asset image uploaded successfully.'
                : 'Bill/invoice uploaded successfully.'
        );
    }

    /**
     * Upload or replace the asset image.
     */
    public function replaceImage(
        Request $request,
        Asset $asset
    ): RedirectResponse {
        $this->authorizeDocumentAction(
            AssetDocument::TYPE_ASSET_IMAGE
        );

        $this->validateDocument($request, true);

        $stored = $this->storePrivateFile($request, $asset);
        $file = $stored['file'];

        $document = $asset->documents()
            ->where(
                'document_type',
                AssetDocument::TYPE_ASSET_IMAGE
            )
            ->first();

        $oldPath = $document?->stored_path;

        try {
            if ($document) {
                $document->update([
                    'original_name' => $file->getClientOriginalName(),
                    'stored_path' => $stored['path'],
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => auth()->id(),
                    'locked_at' => null,
                ]);
            } else {
                $asset->documents()->create([
                    'document_type' => AssetDocument::TYPE_ASSET_IMAGE,
                    'original_name' => $file->getClientOriginalName(),
                    'stored_path' => $stored['path'],
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => auth()->id(),
                    'locked_at' => null,
                ]);
            }
        } catch (\Throwable $exception) {
            Storage::disk('private')->delete($stored['path']);
            throw $exception;
        }

        if (
            $oldPath &&
            $oldPath !== $stored['path'] &&
            Storage::disk('private')->exists($oldPath)
        ) {
            Storage::disk('private')->delete($oldPath);
        }

        return back()->with(
            'success',
            'Asset image uploaded/replaced successfully.'
        );
    }

    /**
     * Upload or replace the bill/invoice.
     * Requires Super Admin or Bills module permission.
     */
    public function replaceBill(
        Request $request,
        Asset $asset
    ): RedirectResponse {
        $this->authorizeDocumentAction(
            AssetDocument::TYPE_BILL_INVOICE
        );

        $this->validateDocument($request);

        $stored = $this->storePrivateFile($request, $asset);
        $file = $stored['file'];

        $document = $asset->documents()
            ->where(
                'document_type',
                AssetDocument::TYPE_BILL_INVOICE
            )
            ->first();

        $oldPath = $document?->stored_path;

        try {
            DB::transaction(function () use (
                $asset,
                $document,
                $file,
                $stored
            ) {
                if ($document) {
                    $document->update([
                        'original_name' => $file->getClientOriginalName(),
                        'stored_path' => $stored['path'],
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => auth()->id(),
                        'locked_at' => now(),
                    ]);
                } else {
                    $asset->documents()->create([
                        'document_type' => AssetDocument::TYPE_BILL_INVOICE,
                        'original_name' => $file->getClientOriginalName(),
                        'stored_path' => $stored['path'],
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => auth()->id(),
                        'locked_at' => now(),
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            Storage::disk('private')->delete($stored['path']);
            throw $exception;
        }

        if (
            $oldPath &&
            $oldPath !== $stored['path'] &&
            Storage::disk('private')->exists($oldPath)
        ) {
            Storage::disk('private')->delete($oldPath);
        }

        return back()->with(
            'success',
            'Bill/invoice uploaded or replaced successfully.'
        );
    }

    /**
     * Delete the asset image.
     */
    public function destroyImage(
        Asset $asset
    ): RedirectResponse {
        $this->authorizeDocumentAction(
            AssetDocument::TYPE_ASSET_IMAGE
        );

        $document = $asset->documents()
            ->where(
                'document_type',
                AssetDocument::TYPE_ASSET_IMAGE
            )
            ->first();

        if (! $document) {
            return back()->withErrors([
                'file' => 'No asset image exists for this asset.',
            ]);
        }

        $path = $document->stored_path;
        $document->delete();

        if (Storage::disk('private')->exists($path)) {
            Storage::disk('private')->delete($path);
        }

        return back()->with(
            'success',
            'Asset image deleted successfully.'
        );
    }

    /**
     * View an asset document through an authorized route.
     */
    public function show(
        Asset $asset,
        AssetDocument $document
    ): StreamedResponse {
        abort_unless(
            (int) $document->asset_id === (int) $asset->id,
            404
        );

        $user = auth()->user();

        abort_unless($user, 401);

        if (! $user->isSuperAdmin()) {
            $canManageAssets = $user->canManageModule('assets');
            $canManageBills = $user->canManageModule('bills');

            if (
                $document->document_type ===
                AssetDocument::TYPE_ASSET_IMAGE
            ) {
                abort_unless($canManageAssets, 403);
            } elseif (
                $document->document_type ===
                AssetDocument::TYPE_BILL_INVOICE
            ) {
                abort_unless($canManageBills, 403);
            } else {
                abort(404);
            }
        }

        abort_unless(
            Storage::disk('private')->exists($document->stored_path),
            404
        );

        return Storage::disk('private')->response(
            $document->stored_path,
            $document->original_name,
            [
                'Content-Type' => $document->mime_type,
                'X-Content-Type-Options' => 'nosniff',
                'Content-Disposition' => 'inline; filename="'.
                    addslashes($document->original_name).'"',
            ]
        );
    }

    /**
     * Delete a bill/invoice when the user has permission.
     */
    public function destroyBill(
        Asset $asset
    ): RedirectResponse {
        $this->authorizeDocumentAction(
            AssetDocument::TYPE_BILL_INVOICE
        );

        $document = $asset->documents()
            ->where(
                'document_type',
                AssetDocument::TYPE_BILL_INVOICE
            )
            ->first();

        if (! $document) {
            return back()->withErrors([
                'file' => 'No bill or invoice exists for this asset.',
            ]);
        }

        $path = $document->stored_path;

        DB::transaction(function () use ($document) {
            $document->delete();
        });

        if (
            $path &&
            Storage::disk('private')->exists($path)
        ) {
            Storage::disk('private')->delete($path);
        }

        return back()->with(
            'success',
            'Bill/invoice deleted successfully.'
        );
    }
}
