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
     * Source department and source occupant are always
     * taken from the asset's current database state.
     */
    public function create(array $data): AssetTransfer
    {
        return DB::transaction(function () use ($data) {

            $asset = Asset::query()
                ->lockForUpdate()
                ->findOrFail($data['asset_id']);

            /*
             * Prevent creating another active transfer
             * for the same asset.
             */
            $activeTransferExists = AssetTransfer::query()
                ->where('asset_id', $asset->id)
                ->whereIn('status', [
                    'pending',
                    'approved',
                ])
                ->exists();

            if ($activeTransferExists) {
                throw ValidationException::withMessages([
                    'asset_id' =>
                        'This asset already has a pending or approved transfer.',
                ]);
            }

            /*
             * Destination must be different from
             * the asset's current department.
             */
            if (
                (int) $asset->department_id ===
                (int) $data['to_department_id']
            ) {
                throw ValidationException::withMessages([
                    'to_department_id' =>
                        'The destination department must be different from the current department.',
                ]);
            }

            /*
             * Verify destination occupant belongs
             * to destination department.
             */
            if (!empty($data['to_occupant_id'])) {

                $validOccupant = DB::table('employees')
                    ->where('id', $data['to_occupant_id'])
                    ->where(
                        'department_id',
                        $data['to_department_id']
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
             * Source values are taken directly
             * from the current asset.
             */
            $fromDepartmentId = $asset->department_id;
            $fromOccupantId = $asset->present_occupant_id ?: null;

            $transfer = AssetTransfer::create([
                'asset_id' => $asset->id,

                'from_department_id' =>
                    $fromDepartmentId,

                'to_department_id' =>
                    $data['to_department_id'],

                'from_occupant_id' =>
                    $fromOccupantId,

                'to_occupant_id' =>
                    $data['to_occupant_id'] ?? null,

                'transfer_date' =>
                    $data['transfer_date']
                    ?? now()->toDateString(),

                'received_date' => null,

                'status' => 'pending',

                'reason' =>
                    $data['reason'] ?? null,

                'remarks' =>
                    $data['remarks'] ?? null,

                'reference_number' =>
                    $data['reference_number']
                    ?? $this->generateReferenceNumber(),

                'initiated_by' =>
                    Auth::id(),

                'approved_by' => null,

                'completed_by' => null,
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
                'status' => 'approved',
                'approved_by' => Auth::id(),
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
     * This is the only point where the asset's
     * department and occupant are changed.
     */
    public function complete(
        AssetTransfer $transfer
    ): AssetTransfer {
        return DB::transaction(function () use ($transfer) {

            $transfer = AssetTransfer::query()
                ->lockForUpdate()
                ->findOrFail($transfer->id);

            if ($transfer->status !== 'approved') {
                throw ValidationException::withMessages([
                    'transfer' =>
                        'Only approved transfers can be completed.',
                ]);
            }

            /*
             * Lock the asset so another request cannot
             * modify it simultaneously.
             */
            $asset = Asset::query()
                ->lockForUpdate()
                ->findOrFail($transfer->asset_id);

            /*
             * Verify the asset's current department
             * has not changed since this transfer began.
             */
            if (
                (int) $asset->department_id !==
                (int) $transfer->from_department_id
            ) {
                throw ValidationException::withMessages([
                    'transfer' =>
                        'The asset department has changed since this transfer was created. Review the transfer before completing it.',
                ]);
            }

            /*
             * Verify the current occupant has not changed.
             */
            $currentOccupantId =
                $asset->present_occupant_id ?: null;

            $transferOccupantId =
                $transfer->from_occupant_id ?: null;

            if (
                $currentOccupantId !==
                $transferOccupantId
            ) {
                throw ValidationException::withMessages([
                    'transfer' =>
                        'The asset occupant has changed since this transfer was created. Review the transfer before completing it.',
                ]);
            }

            /*
             * Save old state for immutable history.
             */
            $oldDepartmentId =
                $asset->department_id;

            $oldOccupantId =
                $asset->present_occupant_id;

            /*
             * Update the asset's CURRENT state.
             */
            $asset->update([
                'department_id' =>
                    $transfer->to_department_id,

                'present_occupant_id' =>
                    $transfer->to_occupant_id,

                'updated_by' =>
                    Auth::id(),
            ]);

            /*
             * Mark transfer as completed.
             */
            $transfer->update([
                'status' => 'completed',

                'received_date' =>
                    now()->toDateString(),

                'completed_by' =>
                    Auth::id(),
            ]);

            /*
             * Create permanent asset history.
             */
            AssetHistory::create([
                'asset_id' => $asset->id,

                'action' => 'transferred',

                'old_value' => json_encode([
                    'department_id' =>
                        $oldDepartmentId,

                    'present_occupant_id' =>
                        $oldOccupantId,
                ]),

                'new_value' => json_encode([
                    'department_id' =>
                        $transfer->to_department_id,

                    'present_occupant_id' =>
                        $transfer->to_occupant_id,
                ]),

                'performed_by' =>
                    Auth::id(),

                'ip_address' =>
                    Request::ip(),

                'created_at' =>
                    now(),
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
     * Cancel a pending or approved transfer.
     */
    public function cancel(
        AssetTransfer $transfer
    ): AssetTransfer {
        return DB::transaction(function () use ($transfer) {

            $transfer = AssetTransfer::query()
                ->lockForUpdate()
                ->findOrFail($transfer->id);

            if (!in_array(
                $transfer->status,
                ['pending', 'approved'],
                true
            )) {
                throw ValidationException::withMessages([
                    'transfer' =>
                        'Only pending or approved transfers can be cancelled.',
                ]);
            }

            $transfer->update([
                'status' => 'cancelled',
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
     * Generate a unique transfer reference.
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
                        bin2hex(random_bytes(3)),
                        0,
                        6
                    )
                );
        } while (
            AssetTransfer::query()
                ->where(
                    'reference_number',
                    $reference
                )
                ->exists()
        );

        return $reference;
    }
}