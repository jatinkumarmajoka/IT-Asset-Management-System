<?php

namespace App\Services\Transfers;

use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\AssetOccupancyHistory;
use App\Models\AssetTransfer;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;

class AssetTransferService
{
    /**
     * Create a pending transfer.
     *
     * Source department and occupant are taken from
     * the asset's current database state.
     */
    public function create(array $data): AssetTransfer
    {
        return DB::transaction(function () use ($data) {
            $asset = Asset::query()
                ->lockForUpdate()
                ->findOrFail($data['asset_id']);

            /*
             * Prevent another active transfer for this asset.
             */
            $activeTransferExists = AssetTransfer::query()
                ->where('asset_id', $asset->id)
                ->whereIn('status', ['pending', 'approved'])
                ->exists();

            if ($activeTransferExists) {
                throw ValidationException::withMessages([
                    'asset_id' =>
                        'This asset already has a pending or approved transfer.',
                ]);
            }

            /*
             * Destination must differ from the current department.
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
             * Verify that the destination occupant belongs
             * to the destination department.
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

            $transfer = AssetTransfer::create([
                'asset_id' => $asset->id,
                'from_department_id' => $asset->department_id,
                'to_department_id' => $data['to_department_id'],
                'from_occupant_id' => $asset->present_occupant_id ?: null,
                'to_occupant_id' => $data['to_occupant_id'] ?? null,
                'transfer_date' => $data['transfer_date']
                    ?? now()->toDateString(),
                'received_date' => null,
                'status' => 'pending',
                'reason' => $data['reason'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'reference_number' => $data['reference_number']
                    ?? $this->generateReferenceNumber(),
                'initiated_by' => Auth::id(),
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
    public function approve(AssetTransfer $transfer): AssetTransfer
    {
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
     * Saves the previous and present assignment snapshots
     * before committing the transfer.
     */
    public function complete(AssetTransfer $transfer): AssetTransfer
    {
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
             * Lock the asset to prevent concurrent changes.
             */
            $asset = Asset::query()
                ->lockForUpdate()
                ->findOrFail($transfer->asset_id);

            /*
             * Verify that the source department has not changed.
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
             * Verify that the source occupant has not changed.
             * Normalize both IDs to avoid integer/string mismatches.
             */
            $currentOccupantId = $asset->present_occupant_id
                ? (int) $asset->present_occupant_id
                : null;

            $transferOccupantId = $transfer->from_occupant_id
                ? (int) $transfer->from_occupant_id
                : null;

            if ($currentOccupantId !== $transferOccupantId) {
                throw ValidationException::withMessages([
                    'transfer' =>
                        'The asset occupant has changed since this transfer was created. Review the transfer before completing it.',
                ]);
            }

            /*
             * Load the OLD assignment before updating the asset.
             */
            $asset->load([
                'department',
                'occupant',
                'presentSubDepartment',
            ]);

            $oldDepartmentId = $asset->department_id;
            $oldDepartmentName = $asset->department?->name;

            $oldSubDepartmentId = $asset->present_sub_department_id;
            $oldSubDepartmentName =
                $asset->presentSubDepartment?->name;

            $oldOccupantName = $asset->occupant?->name
                ?? $asset->present_occupant_name;

            $oldLocation = $asset->present_location
                ?? $asset->location;

            $oldRoomNumber = $asset->present_room_number;

            /*
             * Resolve the destination department and occupant.
             */
            $newDepartment = Department::findOrFail(
                $transfer->to_department_id
            );

            $newOccupant = $transfer->to_occupant_id
                ? Employee::findOrFail($transfer->to_occupant_id)
                : null;

            /*
             * The existing transfer fields do not specify a new
             * location, room, or sub-department. Preserve the current
             * values rather than inventing destination values.
             */
            $newLocation = $asset->present_location
                ?? $asset->location;

            $newRoomNumber = $asset->present_room_number;
            $newSubDepartmentId = $asset->present_sub_department_id;
            $newSubDepartmentName =
                $asset->presentSubDepartment?->name;

            /*
             * Update the asset's current assignment.
             * Preserve the old assignment in the asset fields too.
             */
            $asset->update([
                'previous_department_name' => $oldDepartmentName,
                'previous_location' => $oldLocation,
                'previous_room_number' => $oldRoomNumber,

                'department_id' => $transfer->to_department_id,
                'present_occupant_id' => $transfer->to_occupant_id,
                'present_occupant_name' => $newOccupant?->name,
                'present_location' => $newLocation,
                'present_room_number' => $newRoomNumber,

                'updated_by' => Auth::id(),
            ]);

            /*
             * Record a permanent snapshot of the previous and
             * present assignment for the Occupancy History section.
             */
            AssetOccupancyHistory::create([
                'asset_id' => $asset->id,

                'previous_department_id' => $oldDepartmentId,
                'previous_department_name' => $oldDepartmentName,
                'previous_sub_department_id' => $oldSubDepartmentId,
                'previous_sub_department_name' => $oldSubDepartmentName,
                'previous_occupant_name' => $oldOccupantName,
                'previous_location' => $oldLocation,
                'previous_room_number' => $oldRoomNumber,

                'present_department_id' => $newDepartment->id,
                'present_department_name' => $newDepartment->name,
                'present_sub_department_id' => $newSubDepartmentId,
                'present_sub_department_name' => $newSubDepartmentName,
                'present_occupant_name' => $newOccupant?->name,
                'present_location' => $newLocation,
                'present_room_number' => $newRoomNumber,

                'effective_at' => now(),
                'recorded_by' => Auth::id(),
            ]);

            /*
             * Mark the transfer as completed.
             */
            $transfer->update([
                'status' => 'completed',
                'received_date' => now()->toDateString(),
                'completed_by' => Auth::id(),
            ]);

            /*
             * Preserve the existing general asset audit history.
             */
            AssetHistory::create([
                'asset_id' => $asset->id,
                'action' => 'transferred',

                'old_value' => json_encode([
                    'department_id' => $oldDepartmentId,
                    'present_occupant_id' => $currentOccupantId,
                ]),

                'new_value' => json_encode([
                    'department_id' => $transfer->to_department_id,
                    'present_occupant_id' => $transfer->to_occupant_id,
                ]),

                'performed_by' => Auth::id(),
                'ip_address' => Request::ip(),
                'created_at' => now(),
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
    public function cancel(AssetTransfer $transfer): AssetTransfer
    {
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
            $reference = 'TRF-'
                . now()->format('YmdHis')
                . '-'
                . strtoupper(
                    substr(bin2hex(random_bytes(3)), 0, 6)
                );
        } while (
            AssetTransfer::query()
                ->where('reference_number', $reference)
                ->exists()
        );

        return $reference;
    }
}
