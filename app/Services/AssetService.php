<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Support\Facades\DB;

class AssetService
{
    public function create(array $data): Asset
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();

            $asset = Asset::create($data);

            $asset->history()->create([
                'action' => 'created',
                'new_value' => json_encode($asset->only([
                    'asset_code', 'item_name', 'department_id',
                    'category_id', 'barcode_number'
                ])),
                'performed_by' => auth()->id(),
                'ip_address' => request()->ip(),
                'created_at' => now(),
            ]);

            return $asset;
        });
    }

    public function update(Asset $asset, array $data): Asset
    {
        return DB::transaction(function () use ($asset, $data) {
            $old = $asset->only(array_keys($data));

            $data['updated_by'] = auth()->id();
            $asset->update($data);

            $asset->history()->create([
                'action' => 'updated',
                'old_value' => json_encode($old),
                'new_value' => json_encode($asset->only(array_keys($data))),
                'performed_by' => auth()->id(),
                'ip_address' => request()->ip(),
                'created_at' => now(),
            ]);

            return $asset->fresh();
        });
    }
}
