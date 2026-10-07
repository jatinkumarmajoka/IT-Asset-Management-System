<?php

namespace App\Http\Requests;

use App\Models\Asset;
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
             * The asset is the only source of truth for the
             * current department and current occupant.
             */
            'asset_id' => [
                'required',
                'integer',
                'exists:assets,id',
            ],

            /*
             * Destination department.
             */
            'to_department_id' => [
                'required',
                'integer',
                'exists:departments,id',
            ],

            /*
             * Destination occupant is optional.
             *
             * If null:
             * the destination department itself is treated
             * as the occupant.
             */
            'to_occupant_id' => [
                'nullable',
                'integer',
                Rule::exists('employees', 'id')
                    ->where(function ($query) {
                        $query->where(
                            'department_id',
                            $this->input('to_department_id')
                        );
                    }),
            ],

            'transfer_date' => [
                'required',
                'date',
            ],

            'reason' => [
                'nullable',
                'string',
                'max:500',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'reference_number' => [
                'nullable',
                'string',
                'max:50',
                'unique:asset_transfers,reference_number',
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
        ];
    }

    /**
     * Never trust the current department or current occupant
     * coming from the browser.
     *
     * The Transfer Service will read both directly from
     * the locked asset record.
     */
    protected function prepareForValidation(): void
    {
        /*
         * Intentionally do NOT accept:
         *
         * from_department_id
         * from_occupant_id
         *
         * from the request.
         *
         * They are determined server-side.
         */
    }
}