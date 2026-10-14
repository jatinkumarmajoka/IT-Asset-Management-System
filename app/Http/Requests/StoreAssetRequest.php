<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Module permissions are checked by the controller/middleware.
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

            'item_name' => [
                'required',
                'string',
                'max:255',
            ],

            'department_id' => [
                'required',
                'integer',
                'exists:departments,id',
            ],

            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],

            'present_occupant_id' => [
                'nullable',
                'integer',
                Rule::exists('employees', 'id')
                    ->where(
                        fn ($query) => $query->where(
                            'department_id',
                            $this->input('department_id')
                        )
                    ),
            ],

            'present_sub_department_id' => [
                'nullable',
                'integer',
                Rule::exists('sub_departments', 'id')
                    ->where(
                        fn ($query) => $query->where(
                            'department_id',
                            $this->input('department_id')
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
                'unique:assets,asset_serial_number',
            ],

            'barcode_number' => [
                'nullable',
                'string',
                'max:255',
                'unique:assets,barcode_number',
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

            // Optional uploads are validated here but are not asset-table columns.
            'asset_image' => [
                'nullable',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],

            'bill_invoice' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:10240',
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

            'present_occupant_id.exists' =>
                'Select an employee belonging to the selected department.',

            'present_sub_department_id.exists' =>
                'Select a sub-department belonging to the selected department.',

            'asset_image.image' =>
                'The asset image must be a valid image file.',

            'asset_image.mimes' =>
                'The asset image must be JPG, JPEG, PNG, or WEBP.',

            'asset_image.max' =>
                'The asset image must not exceed 10 MB.',

            'bill_invoice.file' =>
                'The bill/invoice must be a valid file.',

            'bill_invoice.mimes' =>
                'The bill/invoice must be JPG, JPEG, PNG, WEBP, or PDF.',

            'bill_invoice.max' =>
                'The bill/invoice must not exceed 10 MB.',
        ];
    }
}
