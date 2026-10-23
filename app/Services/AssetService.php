<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetOccupancyHistory;
use App\Models\Department;
use App\Models\SubDepartment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;

class AssetService
{
    public function create(array $data): Asset
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();

            $asset = Asset::create($data);

            $asset->history()->create([
                'action' => 'created',
                'new_value' => json_encode(
                    $asset->only([
                        'asset_code',
                        'item_name',
                        'department_id',
                        'category_id',
                        'barcode_number',
                        'previous_department_name',
                        'previous_location',
                        'previous_room_number',
                        'present_location',
                        'present_room_number',
                    ])
                ),
                'performed_by' => Auth::id(),
                'ip_address' => Request::ip(),
                'created_at' => now(),
            ]);

            $this->recordOccupancy($asset, [], $asset->getAttributes());

            return $asset;
        });
    }

    public function update(Asset $asset, array $data): Asset
    {
        return DB::transaction(function () use ($asset, $data) {
            foreach (['department_id', 'present_occupant_id'] as $field) {
                if (array_key_exists($field, $data)) {
                    throw ValidationException::withMessages([
                        $field => 'This field can only be changed through the asset transfer workflow.',
                    ]);
                }
            }

            $asset->refresh();

            $oldAttributes = $asset->getAttributes();
            $oldValues = $asset->only(array_keys($data));

            $occupancyFields = [
                'previous_department_name',
                'previous_location',
                'previous_room_number',
                'present_location',
                'present_room_number',
                'present_sub_department_id',
                'present_occupant_name',
                'location',
            ];

            $occupancyChanged = false;

            foreach ($occupancyFields as $field) {
                if (
                    array_key_exists($field, $data)
                    && (string) ($asset->{$field} ?? '')
                        !== (string) ($data[$field] ?? '')
                ) {
                    $occupancyChanged = true;
                    break;
                }
            }

            $data['updated_by'] = Auth::id();

            $asset->update($data);
            $asset->refresh();

            $asset->history()->create([
                'action' => 'updated',
                'old_value' => json_encode($oldValues),
                'new_value' => json_encode(
                    $asset->only(array_keys($data))
                ),
                'performed_by' => Auth::id(),
                'ip_address' => Request::ip(),
                'created_at' => now(),
            ]);

            if ($occupancyChanged) {
                $this->recordOccupancy(
                    $asset,
                    $oldAttributes,
                    $asset->getAttributes()
                );
            }

            return $asset;
        });
    }

    private function recordOccupancy(
        Asset $asset,
        array $old,
        array $current
    ): void {
        $previousDepartmentId = $old['department_id'] ?? null;
        $presentDepartmentId = $current['department_id'] ?? null;

        $previousSubDepartmentId =
            $old['present_sub_department_id'] ?? null;

        $presentSubDepartmentId =
            $current['present_sub_department_id'] ?? null;

        $previousDepartmentName =
            $old['previous_department_name'] ?? null;

        if (empty($previousDepartmentName) && $previousDepartmentId) {
            $previousDepartmentName =
                Department::find($previousDepartmentId)?->name;
        }

        $presentDepartmentName = $presentDepartmentId
            ? Department::find($presentDepartmentId)?->name
            : null;

        $previousSubDepartmentName = $previousSubDepartmentId
            ? SubDepartment::find($previousSubDepartmentId)?->name
            : null;

        $presentSubDepartmentName = $presentSubDepartmentId
            ? SubDepartment::find($presentSubDepartmentId)?->name
            : null;

        $previousOccupantName =
            $old['present_occupant_name'] ?? null;

        $presentOccupantName =
            $current['present_occupant_name'] ?? null;

        if (
            empty($previousOccupantName)
            && ! empty($old['present_occupant_id'])
        ) {
            $previousOccupantName = $asset->occupant()
                ->getRelated()
                ->newQuery()
                ->whereKey($old['present_occupant_id'])
                ->value('name');
        }

        if (
            empty($presentOccupantName)
            && ! empty($current['present_occupant_id'])
        ) {
            $presentOccupantName = $asset->occupant()
                ->getRelated()
                ->newQuery()
                ->whereKey($current['present_occupant_id'])
                ->value('name');
        }

        AssetOccupancyHistory::create([
            'asset_id' => $asset->id,

            'previous_department_id' => $previousDepartmentId,
            'previous_department_name' => $previousDepartmentName,
            'previous_sub_department_id' => $previousSubDepartmentId,
            'previous_sub_department_name' => $previousSubDepartmentName,
            'previous_occupant_name' => $previousOccupantName,
            'previous_location' => $old['previous_location']
                ?? $old['present_location']
                ?? $old['location']
                ?? null,
            'previous_room_number' => $old['previous_room_number']
                ?? $old['present_room_number']
                ?? null,

            'present_department_id' => $presentDepartmentId,
            'present_department_name' => $presentDepartmentName,
            'present_sub_department_id' => $presentSubDepartmentId,
            'present_sub_department_name' => $presentSubDepartmentName,
            'present_occupant_name' => $presentOccupantName,
            'present_location' => $current['present_location']
                ?? $current['location']
                ?? null,
            'present_room_number' => $current['present_room_number'] ?? null,

            'effective_at' => now(),
            'recorded_by' => Auth::id(),
        ]);
    }
}
