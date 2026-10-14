<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssetTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization is enforced by the transfer controller/policies.
        return true;
    }

    public function rules(): array
    {
        return [
            /* Asset */
            'asset_id' => [
                'required',
                'integer',
                'exists:assets,id',
            ],

            /* Destination Department */
            'to_department_id' => [
                'required',
                'integer',
                'exists:departments,id',
            ],

            /* Destination Occupant must belong to the destination department. */
            'to_occupant_id' => [
                'nullable',
                'integer',
                Rule::exists('employees', 'id')->where(function ($query) {
                    $query->where('department_id', $this->input('to_department_id'));
                }),
            ],

            /* Transfer Date */
            'transfer_date' => [
                'required',
                'date',
            ],

            /* Reason */
            'reason' => [
                'nullable',
                'string',
                'max:500',
            ],

            /* Remarks */
            'remarks' => [
                'nullable',
                'string',
                'max:5000',
            ],

            /* Reference Number */
            'reference_number' => [
                'nullable',
                'string',
                'max:50',
                'unique:asset_transfers,reference_number',
            ],

            /* Transfer Image */
            'transfer_image' => [
                'nullable',
                'file',
                'image',
                'max:10240',
                'mimes:jpg,jpeg,png,webp',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'asset_id.required' => 'Please select an asset.',
            'asset_id.exists' => 'The selected asset does not exist.',
            'to_department_id.required' => 'Please select the destination department.',
            'to_department_id.exists' => 'The selected destination department does not exist.',
            'to_occupant_id.exists' => 'The selected occupant must belong to the destination department.',
            'transfer_date.required' => 'Transfer date is required.',
            'transfer_date.date' => 'Please enter a valid transfer date.',
            'reference_number.unique' => 'This transfer reference number already exists.',
            'transfer_image.file' => 'The transfer image must be a valid file.',
            'transfer_image.image' => 'The transfer image must be a valid image.',
            'transfer_image.max' => 'The transfer image must not be larger than 10 MB.',
            'transfer_image.mimes' => 'The transfer image must be JPG, JPEG, PNG or WEBP.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Do not accept from_department_id or from_occupant_id from the browser.
        // The controller/service must derive the source assignment from the asset's
        // actual current state on the server.
    }
}
