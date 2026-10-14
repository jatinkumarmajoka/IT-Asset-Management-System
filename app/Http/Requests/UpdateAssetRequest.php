<?php

namespace App\Http\Requests;

use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $assetRoute = $this->route('asset');

        $assetId = $assetRoute instanceof Asset
            ? $assetRoute->id
            : $assetRoute;

        return [
            'asset_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('assets', 'asset_code')
                    ->ignore($assetId),
            ],

            'item_name' => [
                'required',
                'string',
                'max:255',
            ],

            // Assignment changes must use the asset transfer workflow.
            'department_id' => [
                'prohibited',
            ],

            'present_occupant_id' => [
                'prohibited',
            ],

            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],

            'present_sub_department_id' => [
                'nullable',
                'integer',
                Rule::exists('sub_departments', 'id')
                    ->where(
                        fn ($query) => $query->where(
                            'department_id',
                            Asset::whereKey($assetId)
                                ->value('department_id')
                        )
                    ),
            ],

            'present_occupant_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'issued_quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'physically_verified_quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'condition' => [
                'required',
                Rule::in([
                    'New',
                    'Good',
                    'Fair',
                    'Damaged',
                    'Critical',
                ]),
            ],

            'asset_serial_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('assets', 'asset_serial_number')
                    ->ignore($assetId),
            ],

            'barcode_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('assets', 'barcode_number')
                    ->ignore($assetId),
            ],

            'date_of_issue' => [
                'nullable',
                'date',
            ],

            'make' => [
                'nullable',
                'string',
                'max:255',
            ],

            'model' => [
                'nullable',
                'string',
                'max:255',
            ],

            'asset_status' => [
                'required',
                Rule::in([
                    'Active',
                    'Assigned',
                    'Unassigned',
                    'Missing',
                    'Damaged',
                    'Disposed',
                    'Return to Store',
                    'Under Repair',
                    'Retired',
                ]),
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'previous_department_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'previous_location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'previous_room_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'present_location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'present_room_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'asset_code.unique' =>
                'This asset code already exists.',

            'asset_serial_number.unique' =>
                'This serial number is already registered.',

            'barcode_number.unique' =>
                'This barcode is already registered.',

            'department_id.prohibited' =>
                'Department can only be changed through Asset Transfer.',

            'present_occupant_id.prohibited' =>
                'Present occupant can only be changed through Asset Transfer.',

            'present_sub_department_id.exists' =>
                'Select a sub-department belonging to the asset department.',
        ];
    }
}
