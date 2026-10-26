<?php

namespace App\Http\Requests;

use App\Models\Employee;
use App\Models\SubDepartment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkAssetActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user || ! $user->canManageModule('assets')) {
            return false;
        }

        // For assign_employee action, also require employees module permission
        if ($this->input('action') === 'assign_employee' && ! $user->canManageModule('employees')) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'action' => [
                'required',
                'string',
                Rule::in([
                    'status_change',
                    'delete',
                    'export_csv',
                    'assign_employee',
                ]),
            ],

            'asset_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'asset_ids.*' => [
                'required',
                'integer',
                'exists:assets,id',
            ],

            // For status_change action
            'asset_status' => [
                'required_if:action,status_change',
                'nullable',
                'string',
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

            // For assign_employee action
            'present_occupant_id' => [
                'required_if:action,assign_employee',
                'nullable',
                'integer',
                Rule::exists('employees', 'id')->where(function ($query) {
                    $query->where('status', 'active');
                }),
            ],

            'present_sub_department_id' => [
                'nullable',
                'integer',
                'exists:sub_departments,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'Please select an action.',
            'action.in' => 'Invalid action selected.',
            'asset_ids.required' => 'Please select at least one asset.',
            'asset_ids.min' => 'Please select at least one asset.',
            'asset_ids.*.exists' => 'One or more selected assets do not exist.',
            'asset_status.required_if' => 'Please select a status for the status change action.',
            'asset_status.in' => 'Invalid status selected.',
            'present_occupant_id.required_if' => 'Please select an employee for the assign action.',
            'present_occupant_id.exists' => 'Selected employee does not exist.',
            'present_sub_department_id.exists' => 'Selected sub-department does not exist.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->action === 'assign_employee') {
                $employeeId = $this->present_occupant_id;
                $subDeptId = $this->present_sub_department_id;

                if ($employeeId && $subDeptId) {
                    $employee = Employee::find($employeeId);
                    $subDept = SubDepartment::find($subDeptId);

                    if ($employee && $subDept && $employee->department_id !== $subDept->department_id) {
                        $validator->errors()->add(
                            'present_sub_department_id',
                            'Selected sub-department must belong to the employee\'s department.'
                        );
                    }
                }
            }
        });
    }
}
