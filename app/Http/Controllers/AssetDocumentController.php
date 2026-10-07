<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetDocumentController extends Controller
{
    /**
     * Upload an asset document.
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

            'file' => [
                'required',
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,webp,pdf',
            ],
        ]);

        $type = $request
            ->string('document_type')
            ->toString();

        /*
        |--------------------------------------------------------------------------
        | Asset Image
        |--------------------------------------------------------------------------
        |
        | Asset images CAN be replaced/deleted.
        |
        */

        if ($type === AssetDocument::TYPE_ASSET_IMAGE) {

            $existing = $asset->documents()
                ->where(
                    'document_type',
                    AssetDocument::TYPE_ASSET_IMAGE
                )
                ->first();

            if ($existing) {
                return back()->withErrors([
                    'file' =>
                        'An asset image already exists. Use Replace Image instead.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Bill / Invoice
        |--------------------------------------------------------------------------
        |
        | Bill / Invoice is permanently locked after upload.
        |
        */

        if ($type === AssetDocument::TYPE_BILL_INVOICE) {

            $existing = $asset->documents()
                ->where(
                    'document_type',
                    AssetDocument::TYPE_BILL_INVOICE
                )
                ->first();

            if ($existing) {
                return back()->withErrors([
                    'file' =>
                        'This Bill / Invoice has already been uploaded and is locked.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Store file
        |--------------------------------------------------------------------------
        */

        $file = $request->file('file');

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        $filename =
            Str::uuid()->toString() .
            '.' .
            $extension;

        $directory =
            'assets/' .
            $asset->id;

        $storedPath = $file->storeAs(
            $directory,
            $filename,
            'private'
        );


        /*
        |--------------------------------------------------------------------------
        | Create database record
        |--------------------------------------------------------------------------
        */

        $document = $asset->documents()->create([
            'document_type' => $type,
            'original_name' => $file->getClientOriginalName(),
            'stored_path' => $storedPath,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => auth()->id(),

            /*
             * Only Bill / Invoice is locked.
             */
            'locked_at' =>
                $type === AssetDocument::TYPE_BILL_INVOICE
                    ? now()
                    : null,
        ]);


        return back()->with(
            'success',
            $type === AssetDocument::TYPE_ASSET_IMAGE
                ? 'Asset image uploaded successfully.'
                : 'Bill / Invoice uploaded successfully and locked.'
        );
    }


    /**
     * Replace the current asset image.
     */
    public function replaceImage(
        Request $request,
        Asset $asset
    ): RedirectResponse {

        $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,webp',
            ],
        ]);


        $document = $asset->documents()
            ->where(
                'document_type',
                AssetDocument::TYPE_ASSET_IMAGE
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Store new image first
        |--------------------------------------------------------------------------
        */

        $file = $request->file('file');

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        $filename =
            Str::uuid()->toString() .
            '.' .
            $extension;

        $directory =
            'assets/' .
            $asset->id;

        $newPath = $file->storeAs(
            $directory,
            $filename,
            'private'
        );


        /*
        |--------------------------------------------------------------------------
        | If old image exists, delete old physical file
        |--------------------------------------------------------------------------
        */

        if ($document) {

            if (
                Storage::disk('private')
                    ->exists($document->stored_path)
            ) {
                Storage::disk('private')
                    ->delete($document->stored_path);
            }

            $document->update([
                'original_name' =>
                    $file->getClientOriginalName(),

                'stored_path' =>
                    $newPath,

                'mime_type' =>
                    $file->getMimeType(),

                'file_size' =>
                    $file->getSize(),

                'uploaded_by' =>
                    auth()->id(),

                'locked_at' =>
                    null,
            ]);

        } else {

            $document = $asset->documents()->create([
                'document_type' =>
                    AssetDocument::TYPE_ASSET_IMAGE,

                'original_name' =>
                    $file->getClientOriginalName(),

                'stored_path' =>
                    $newPath,

                'mime_type' =>
                    $file->getMimeType(),

                'file_size' =>
                    $file->getSize(),

                'uploaded_by' =>
                    auth()->id(),

                'locked_at' =>
                    null,
            ]);
        }


        return back()->with(
            'success',
            'Asset image replaced successfully.'
        );
    }


    /**
     * Delete the asset image.
     */
    public function destroyImage(
        Asset $asset
    ): RedirectResponse {

        $document = $asset->documents()
            ->where(
                'document_type',
                AssetDocument::TYPE_ASSET_IMAGE
            )
            ->first();


        if (!$document) {

            return back()->withErrors([
                'file' =>
                    'No asset image exists for this asset.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Delete physical file
        |--------------------------------------------------------------------------
        */

        if (
            Storage::disk('private')
                ->exists($document->stored_path)
        ) {
            Storage::disk('private')
                ->delete($document->stored_path);
        }


        /*
        |--------------------------------------------------------------------------
        | Delete database record
        |--------------------------------------------------------------------------
        */

        $document->delete();


        return back()->with(
            'success',
            'Asset image deleted successfully.'
        );
    }


    /**
     * View document.
     */
    public function show(
        Asset $asset,
        AssetDocument $document
    ): StreamedResponse {

        abort_unless(
            $document->asset_id === $asset->id,
            404
        );

        abort_unless(
            Storage::disk('private')
                ->exists($document->stored_path),
            404
        );


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