<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssetTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
         return true;
    }

    public function rules(): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | Asset
            |--------------------------------------------------------------------------
            */

            'asset_id' => [
                'required',
                'integer',
                'exists:assets,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Destination Department
            |--------------------------------------------------------------------------
            */

            'to_department_id' => [
                'required',
                'integer',
                'exists:departments,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Destination Occupant
            |--------------------------------------------------------------------------
            |
            | If an employee is selected, that employee MUST belong
            | to the selected destination department.
            |
            | NULL means the department itself is the occupant.
            |
            */

            'to_occupant_id' => [
                'nullable',
                'integer',
                Rule::exists(
                    'employees',
                    'id'
                )->where(function ($query) {
                    $query->where(
                        'department_id',
                        $this->input('to_department_id')
                    );
                }),
            ],

            /*
            |--------------------------------------------------------------------------
            | Transfer Date
            |--------------------------------------------------------------------------
            */

            'transfer_date' => [
                'required',
                'date',
            ],

            /*
            |--------------------------------------------------------------------------
            | Reason
            |--------------------------------------------------------------------------
            */

            'reason' => [
                'nullable',
                'string',
                'max:500',
            ],

            /*
            |--------------------------------------------------------------------------
            | Remarks
            |--------------------------------------------------------------------------
            */

            'remarks' => [
                'nullable',
                'string',
                'max:5000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Reference Number
            |--------------------------------------------------------------------------
            */

            'reference_number' => [
                'nullable',
                'string',
                'max:50',
                'unique:asset_transfers,reference_number',
            ],

            /*
            |--------------------------------------------------------------------------
            | Transfer Image
            |--------------------------------------------------------------------------
            */

            'transfer_image' => [
                'nullable',
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,webp',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'asset_id.required' =>
                'Please select an asset.',

            'asset_id.exists' =>
                'The selected asset does not exist.',

            'to_department_id.required' =>
                'Please select the destination department.',

            'to_department_id.exists' =>
                'The selected destination department does not exist.',

            'to_occupant_id.exists' =>
                'The selected occupant must belong to the destination department.',

            'transfer_date.required' =>
                'Transfer date is required.',

            'transfer_date.date' =>
                'Please enter a valid transfer date.',

            'reference_number.unique' =>
                'This transfer reference number already exists.',

            'transfer_image.file' =>
                'The transfer image must be a valid file.',

            'transfer_image.max' =>
                'The transfer image must not be larger than 10 MB.',

            'transfer_image.mimes' =>
                'The transfer image must be JPG, JPEG, PNG or WEBP.',
        ];
    }

    protected function prepareForValidation(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Security
        |--------------------------------------------------------------------------
        |
        | Do NOT accept these values from the browser:
        |
        | from_department_id
        | from_occupant_id
        |
        | They must always be taken from the asset's
        | actual current state on the server.
        |
        */
    }
}