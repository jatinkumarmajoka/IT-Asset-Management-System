<?php

namespace App\Http\Controllers;

use App\Models\AssetTransfer;
use App\Models\AssetTransferDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetTransferDocumentController extends Controller
{
    /**
     * Upload a transfer image.
     */
    public function store(
        Request $request,
        AssetTransfer $transfer
    ): RedirectResponse {
        $request->validate([
            'transfer_image' => [
                'required',
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,webp',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate transfer image
        |--------------------------------------------------------------------------
        */

        $existing = $transfer->documents()
            ->where(
                'document_type',
                AssetTransferDocument::TYPE_TRANSFER_IMAGE
            )
            ->first();

        if ($existing) {
            return back()->withErrors([
                'transfer_image' =>
                    'A transfer image already exists for this transfer.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Get Uploaded File
        |--------------------------------------------------------------------------
        */

        $file = $request->file('transfer_image');

        /*
        |--------------------------------------------------------------------------
        | Generate Secure Filename
        |--------------------------------------------------------------------------
        */

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        $filename =
            Str::uuid()->toString() .
            '.' .
            $extension;

        /*
        |--------------------------------------------------------------------------
        | Private Storage
        |--------------------------------------------------------------------------
        */

        $directory =
            'asset-transfers/' .
            $transfer->id;

        $storedPath = $file->storeAs(
            $directory,
            $filename,
            'private'
        );

        /*
        |--------------------------------------------------------------------------
        | Database Record
        |--------------------------------------------------------------------------
        */

        $transfer->documents()->create([
            'document_type' =>
                AssetTransferDocument::TYPE_TRANSFER_IMAGE,

            'original_name' =>
                $file->getClientOriginalName(),

            'stored_path' =>
                $storedPath,

            'mime_type' =>
                $file->getMimeType(),

            'file_size' =>
                $file->getSize(),

            'uploaded_by' =>
                auth()->id(),
        ]);

        return back()->with(
            'success',
            'Transfer image uploaded successfully.'
        );
    }

    /**
     * View a transfer image.
     */
    public function show(
        AssetTransfer $transfer,
        AssetTransferDocument $document
    ): StreamedResponse {
        /*
        |--------------------------------------------------------------------------
        | Security: Document must belong to transfer
        |--------------------------------------------------------------------------
        */

        abort_unless(
            (int) $document->asset_transfer_id ===
                (int) $transfer->id,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | Only Transfer Images
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $document->document_type ===
                AssetTransferDocument::TYPE_TRANSFER_IMAGE,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | Verify Private File Exists
        |--------------------------------------------------------------------------
        */

        abort_unless(
            Storage::disk('private')->exists(
                $document->stored_path
            ),
            404
        );

        /*
        |--------------------------------------------------------------------------
        | Stream Private File
        |--------------------------------------------------------------------------
        */

        return Storage::disk('private')->response(
            $document->stored_path,
            $document->original_name,
            [
                'Content-Type' =>
                    $document->mime_type,

                'Content-Disposition' =>
                    'inline; filename="' .
                    addslashes(
                        $document->original_name
                    ) .
                    '"',
            ]
        );
    }
}