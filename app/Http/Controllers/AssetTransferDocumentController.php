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
                'image',
                'max:10240',
                'mimes:jpg,jpeg,png,webp',
            ],
        ]);

        $user = $request->user();
        abort_unless($user, 401);

        // Transfer requests are restricted to their owner and Super Admin.
        abort_unless(
            $user->isSuperAdmin()
                || (int) $transfer->initiated_by === (int) $user->id,
            403,
            'You cannot upload an image to another user’s transfer request.'
        );

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

        $file = $request->file('transfer_image');

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        $filename = Str::uuid()->toString() . '.' . $extension;
        $directory = 'asset-transfers/' . $transfer->id;

        $storedPath = $file->storeAs(
            $directory,
            $filename,
            'private'
        );

        if (!$storedPath) {
            abort(500, 'The transfer image could not be stored.');
        }

        try {
            $transfer->documents()->create([
                'document_type' => AssetTransferDocument::TYPE_TRANSFER_IMAGE,
                'original_name' => $file->getClientOriginalName(),
                'stored_path' => $storedPath,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => $user->id,
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('private')->delete($storedPath);
            throw $exception;
        }

        return back()->with(
            'success',
            'Transfer image uploaded successfully.'
        );
    }

    /**
     * View a transfer image through an authorized route.
     */
    public function show(
        AssetTransfer $transfer,
        AssetTransferDocument $document
    ): StreamedResponse {
        abort_unless(
            (int) $document->asset_transfer_id === (int) $transfer->id,
            404
        );

        abort_unless(
            $document->document_type ===
                AssetTransferDocument::TYPE_TRANSFER_IMAGE,
            404
        );

        $user = request()->user();
        abort_unless($user, 401);

        abort_unless(
            $user->isSuperAdmin()
                || (int) $transfer->initiated_by === (int) $user->id,
            403,
            'You cannot view another user’s transfer image.'
        );

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
                'Content-Disposition' => 'inline; filename="' .
                    addslashes($document->original_name) . '"',
            ]
        );
    }
}
