<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $assetId = $this->route('asset')?->id;

        return [

            /*
            |--------------------------------------------------------------------------
            | SECURITY
            |--------------------------------------------------------------------------
            |
            | Department and Present Occupant can ONLY be changed
            | through the Asset Transfer workflow.
            |
            */

            'department_id' => [
                'prohibited',
            ],

            'present_occupant_id' => [
                'prohibited',
            ],


            /*
            |--------------------------------------------------------------------------
            | Asset Information
            |--------------------------------------------------------------------------
            */

            'asset_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique(
                    'assets',
                    'asset_code'
                )->ignore($assetId),
            ],

            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'item_name' => [
                'required',
                'string',
                'max:255',
            ],

            'make' => [
                'nullable',
                'string',
                'max:150',
            ],

            'model' => [
                'nullable',
                'string',
                'max:150',
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

            'asset_serial_number' => [
                'nullable',
                'string',
                'max:150',
                Rule::unique(
                    'assets',
                    'asset_serial_number'
                )->ignore($assetId),
            ],

            'barcode_number' => [
                'nullable',
                'string',
                'max:150',
                Rule::unique(
                    'assets',
                    'barcode_number'
                )->ignore($assetId),
            ],

            'date_of_issue' => [
                'nullable',
                'date',
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

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'department_id.prohibited' =>
                'The department can only be changed through the Asset Transfer workflow.',

            'present_occupant_id.prohibited' =>
                'The present occupant can only be changed through the Asset Transfer workflow.',
        ];
    }
}