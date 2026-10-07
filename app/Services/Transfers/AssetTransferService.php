<?php

namespace App\Services\Transfers;

use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\AssetTransfer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;

class AssetTransferService
{
    /**
     * Create a pending transfer.
     *
     * IMPORTANT:
     * Creating a transfer does NOT change the asset.
     *
     * The current department and current occupant are always
     * read from the database.
     */
    public function create(array $data): AssetTransfer
    {
        return DB::transaction(function () use ($data) {

            /*
             * Lock the asset so another transfer cannot modify
             * it simultaneously.
             */
            $asset = Asset::query()
                ->lockForUpdate()
                ->findOrFail($data['asset_id']);

            $currentDepartmentId = (int) $asset->department_id;
            $currentOccupantId = $asset->present_occupant_id
                ? (int) $asset->present_occupant_id
                : null;

            $destinationDepartmentId =
                (int) $data['to_department_id'];

            $destinationOccupantId =
                !empty($data['to_occupant_id'])
                    ? (int) $data['to_occupant_id']
                    : null;

            /*
             * Prevent transfer to the same department.
             */
            if (
                $currentDepartmentId ===
                $destinationDepartmentId
            ) {
                throw ValidationException::withMessages([
                    'to_department_id' =>
                        'The destination department must be different from the asset\'s current department.',
                ]);
            }

            /*
             * Verify the destination occupant again on the
             * SERVER.
             *
             * FormRequest validation is not enough because
             * business rules must also be enforced here.
             */
            if ($destinationOccupantId !== null) {

                $validOccupant = DB::table('employees')
                    ->where('id', $destinationOccupantId)
                    ->where(
                        'department_id',
                        $destinationDepartmentId
                    )
                    ->exists();

                if (!$validOccupant) {
                    throw ValidationException::withMessages([
                        'to_occupant_id' =>
                            'The selected occupant does not belong to the destination department.',
                    ]);
                }
            }

            /*
             * Create the transfer snapshot.
             *
             * FROM values come ONLY from the database.
             */
            $transfer = AssetTransfer::create([

                'asset_id' =>
                    $asset->id,

                'from_department_id' =>
                    $currentDepartmentId,

                'to_department_id' =>
                    $destinationDepartmentId,

                'from_occupant_id' =>
                    $currentOccupantId,

                'to_occupant_id' =>
                    $destinationOccupantId,

                'transfer_date' =>
                    $data['transfer_date']
                    ?? now()->toDateString(),

                'received_date' =>
                    null,

                'status' =>
                    'pending',

                'reason' =>
                    $data['reason'] ?? null,

                'remarks' =>
                    $data['remarks'] ?? null,

                'reference_number' =>
                    $data['reference_number']
                    ?? $this->generateReferenceNumber(),

                'initiated_by' =>
                    Auth::id(),

                'approved_by' =>
                    null,

                'completed_by' =>
                    null,
            ]);

            return $transfer->load([
                'asset',
                'fromDepartment',
                'toDepartment',
                'fromOccupant',
                'toOccupant',
            ]);
        });
    }


    /**
     * Approve a pending transfer.
     */
    public function approve(
        AssetTransfer $transfer
    ): AssetTransfer {

        return DB::transaction(function () use ($transfer) {

            $transfer = AssetTransfer::query()
                ->lockForUpdate()
                ->findOrFail($transfer->id);

            if ($transfer->status !== 'pending') {
                throw ValidationException::withMessages([
                    'transfer' =>
                        'Only pending transfers can be approved.',
                ]);
            }

            $transfer->update([
                'status' =>
                    'approved',

                'approved_by' =>
                    Auth::id(),
            ]);

            return $transfer->fresh([
                'asset',
                'fromDepartment',
                'toDepartment',
                'fromOccupant',
                'toOccupant',
            ]);
        });
    }


    /**
     * Complete an approved transfer.
     *
     * This is the ONLY place where the transfer system
     * changes the current department and current occupant.
     */
    public function complete(
        AssetTransfer $transfer
    ): AssetTransfer {

        return DB::transaction(function () use ($transfer) {

            /*
             * Lock transfer.
             */
            $transfer = AssetTransfer::query()
                ->lockForUpdate()
                ->findOrFail($transfer->id);

            /*
             * Only approved transfers can be completed.
             */
            if ($transfer->status !== 'approved') {
                throw ValidationException::withMessages([
                    'transfer' =>
                        'Only approved transfers can be completed.',
                ]);
            }

            /*
             * Lock the asset.
             */
            $asset = Asset::query()
                ->lockForUpdate()
                ->findOrFail($transfer->asset_id);

            /*
             * Read the ACTUAL current state from the database.
             */
            $actualDepartmentId =
                (int) $asset->department_id;

            $actualOccupantId =
                $asset->present_occupant_id
                    ? (int) $asset->present_occupant_id
                    : null;

            /*
             * The asset must still be where it was when the
             * transfer was created.
             */
            if (
                $actualDepartmentId !==
                (int) $transfer->from_department_id
            ) {
                throw ValidationException::withMessages([
                    'transfer' =>
                        'The asset department has changed since this transfer was created. The transfer cannot be completed.',
                ]);
            }

            /*
             * Also verify the occupant has not changed.
             *
             * This protects the historical FROM occupant.
             */
            if (
                $actualOccupantId !==
                (
                    $transfer->from_occupant_id
                        ? (int) $transfer->from_occupant_id
                        : null
                )
            ) {
                throw ValidationException::withMessages([
                    'transfer' =>
                        'The asset occupant has changed since this transfer was created. The transfer cannot be completed.',
                ]);
            }

            /*
             * Verify destination occupant one final time.
             */
            if ($transfer->to_occupant_id !== null) {

                $validOccupant = DB::table('employees')
                    ->where(
                        'id',
                        $transfer->to_occupant_id
                    )
                    ->where(
                        'department_id',
                        $transfer->to_department_id
                    )
                    ->exists();

                if (!$validOccupant) {
                    throw ValidationException::withMessages([
                        'transfer' =>
                            'The destination occupant no longer belongs to the destination department.',
                    ]);
                }
            }

            /*
             * Save immutable OLD state BEFORE changing asset.
             */
            $oldState = [
                'department_id' =>
                    $actualDepartmentId,

                'present_occupant_id' =>
                    $actualOccupantId,
            ];

            /*
             * New state comes from the already-approved transfer
             * record, NOT from the browser.
             */
            $newState = [
                'department_id' =>
                    (int) $transfer->to_department_id,

                'present_occupant_id' =>
                    $transfer->to_occupant_id
                        ? (int) $transfer->to_occupant_id
                        : null,
            ];

            /*
             * Update current asset state.
             */
            $asset->update([
    'department_id' =>
        $newState['department_id'],

    'present_occupant_id' =>
        $newState['present_occupant_id'],

    'updated_by' =>
        Auth::id(),
]);

            /*
             * Mark transfer completed.
             */
            $transfer->update([
                'status' =>
                    'completed',

                'received_date' =>
                    now()->toDateString(),

                'completed_by' =>
                    Auth::id(),
            ]);

            /*
             * Create permanent asset history.
             *
             * This record is NEVER used to calculate or overwrite
             * another historical record.
             */
            AssetHistory::create([

                'asset_id' =>
                    $asset->id,

                'action' =>
                    'transferred',

                'old_value' =>
                    json_encode(
                        $oldState,
                        JSON_THROW_ON_ERROR
                    ),

                'new_value' =>
                    json_encode(
                        $newState,
                        JSON_THROW_ON_ERROR
                    ),

                'performed_by' =>
                    Auth::id(),

                'ip_address' =>
                    Request::ip(),

                'created_at' =>
                    now(),
            ]);

            return $transfer->fresh([
                'asset.department',
                'asset.occupant',
                'fromDepartment',
                'toDepartment',
                'fromOccupant',
                'toOccupant',
            ]);
        });
    }


    /**
     * Cancel a pending or approved transfer.
     *
     * Cancellation does NOT modify the asset.
     */
    public function cancel(
        AssetTransfer $transfer
    ): AssetTransfer {

        return DB::transaction(function () use ($transfer) {

            $transfer = AssetTransfer::query()
                ->lockForUpdate()
                ->findOrFail($transfer->id);

            if (
                !in_array(
                    $transfer->status,
                    ['pending', 'approved'],
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    'transfer' =>
                        'Only pending or approved transfers can be cancelled.',
                ]);
            }

            $transfer->update([
                'status' =>
                    'cancelled',
            ]);

            return $transfer->fresh([
                'asset',
                'fromDepartment',
                'toDepartment',
                'fromOccupant',
                'toOccupant',
            ]);
        });
    }


    /**
     * Generate unique transfer reference.
     */
    private function generateReferenceNumber(): string
    {
        do {

            $reference =
                'TRF-' .
                now()->format('YmdHis') .
                '-' .
                strtoupper(
                    substr(
                        bin2hex(
                            random_bytes(3)
                        ),
                        0,
                        6
                    )
                );

        } while (
            AssetTransfer::where(
                'reference_number',
                $reference
            )->exists()
        );

        return $reference;
    }
}