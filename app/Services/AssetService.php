<?php

namespace App\Services;

use App\Models\Asset;
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
                'new_value' => json_encode($asset->only([
                    'asset_code',
                    'item_name',
                    'department_id',
                    'category_id',
                    'barcode_number'
                ])),
                'performed_by' => Auth::id(),
                'ip_address' => Request::ip(),
                'created_at' => now(),
            ]);

            return $asset;
        });
    }

    public function update(
        Asset $asset,
        array $data
    ): Asset {
        return DB::transaction(function () use ($asset, $data) {

            /*
         * These fields are controlled exclusively by the
         * Transfer workflow.
         */
            $protectedFields = [
                'department_id',
                'present_occupant_id',
            ];

            foreach ($protectedFields as $field) {
                if (array_key_exists($field, $data)) {
                    throw ValidationException::withMessages([
                        $field =>
                        'This field can only be changed through the asset transfer workflow.',
                    ]);
                }
            }

            $old = $asset->only(
                array_keys($data)
            );

            $data['updated_by'] =
                Auth::id();

            $asset->update($data);

            $asset->history()->create([

                'action' =>
                'updated',

                'old_value' =>
                json_encode($old),

                'new_value' =>
                json_encode(
                    $asset->only(
                        array_keys($data)
                    )
                ),

                'performed_by' =>
                Auth::id(),

                'ip_address' =>
                Request::ip(),

                'created_at' =>
                now(),
            ]);

            return $asset->fresh();
        });
    }
}
