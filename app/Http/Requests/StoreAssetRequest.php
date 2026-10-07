<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_code' => [
                'required',
                'string',
                'max:100',
                'unique:assets,asset_code',
            ],

            'department_id' => [
                'required',
                'exists:departments,id',
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

            /*
             * IMPORTANT:
             *
             * If an occupant is selected, that employee MUST
             * belong to the selected department.
             */
            'present_occupant_id' => [
                'nullable',
                Rule::exists('employees', 'id')
                    ->where(function ($query) {
                        $query->where(
                            'department_id',
                            $this->input('department_id')
                        );
                    }),
            ],

            'asset_serial_number' => [
                'nullable',
                'string',
                'max:150',
                'unique:assets,asset_serial_number',
            ],

            'barcode_number' => [
                'nullable',
                'string',
                'max:150',
                'unique:assets,barcode_number',
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
            'present_occupant_id.exists' =>
                'The selected occupant must belong to the selected department.',
        ];
    }
}