<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use App\Models\Employee;
use App\Models\SubDepartment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class AssetsImport implements SkipsOnFailure, ToModel, WithBatchInserts, WithChunkReading, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $department = Department::where('name', $row['department'])->first();
        $category = Category::where('name', $row['category'])->first();
        $employee = ! empty($row['present_occupant_name'])
            ? Employee::where('name', $row['present_occupant_name'])->first()
            : null;
        $subDepartment = ! empty($row['sub_department'])
            ? SubDepartment::where('name', $row['sub_department'])->first()
            : null;

        // Validate required relationships exist
        if (! $department) {
            throw ValidationException::withMessages([
                'department' => "Department '{$row['department']}' not found",
            ]);
        }

        if (! $category) {
            throw ValidationException::withMessages([
                'category' => "Category '{$row['category']}' not found",
            ]);
        }

        // Check if employee belongs to department
        if ($employee && $employee->department_id !== $department->id) {
            throw ValidationException::withMessages([
                'present_occupant_name' => "Employee '{$row['present_occupant_name']}' does not belong to department '{$row['department']}'",
            ]);
        }

        // Check if sub-department belongs to department
        if ($subDepartment && $subDepartment->department_id !== $department->id) {
            throw ValidationException::withMessages([
                'sub_department' => "Sub-department '{$row['sub_department']}' does not belong to department '{$row['department']}'",
            ]);
        }

        return new Asset([
            'asset_code' => $row['asset_code'],
            'department_id' => $department->id,
            'category_id' => $category->id,
            'item_name' => $row['item_name'],
            'make' => $row['make'] ?? null,
            'model' => $row['model'] ?? null,
            'issued_quantity' => $row['issued_quantity'] ?? 1,
            'physically_verified_quantity' => $row['physically_verified_quantity'] ?? 0,
            'present_occupant_id' => $employee?->id,
            'present_occupant_name' => $row['present_occupant_name'] ?? null,
            'asset_serial_number' => $row['asset_serial_number'] ?? null,
            'barcode_number' => $row['barcode_number'] ?? null,
            'date_of_issue' => $row['date_of_issue'] ? Carbon::parse($row['date_of_issue'])->format('Y-m-d') : null,
            'asset_status' => $row['asset_status'] ?? 'Active',
            'condition' => $row['condition'] ?? 'Good',
            'location' => $row['location'] ?? null,
            'present_location' => $row['present_location'] ?? null,
            'present_room_number' => $row['present_room_number'] ?? null,
            'previous_department_name' => $row['previous_department_name'] ?? null,
            'previous_location' => $row['previous_location'] ?? null,
            'previous_room_number' => $row['previous_room_number'] ?? null,
            'present_sub_department_id' => $subDepartment?->id,
            'remarks' => $row['remarks'] ?? null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'asset_code' => 'required|string|max:100|unique:assets,asset_code',
            'department' => 'required|string',
            'category' => 'required|string',
            'item_name' => 'required|string|max:255',
            'make' => 'nullable|string|max:150',
            'model' => 'nullable|string|max:150',
            'issued_quantity' => 'nullable|integer|min:0',
            'physically_verified_quantity' => 'nullable|integer|min:0',
            'present_occupant_name' => 'nullable|string|max:255',
            'asset_serial_number' => 'nullable|string|max:150|unique:assets,asset_serial_number',
            'barcode_number' => 'nullable|string|max:150|unique:assets,barcode_number',
            'date_of_issue' => 'nullable|date',
            'asset_status' => 'nullable|string|in:Active,Assigned,Unassigned,Missing,Damaged,Disposed,Return to Store,Under Repair,Retired',
            'condition' => 'nullable|string|in:New,Good,Fair,Damaged,Critical',
            'location' => 'nullable|string|max:255',
            'present_location' => 'nullable|string|max:255',
            'present_room_number' => 'nullable|string|max:100',
            'previous_department_name' => 'nullable|string|max:255',
            'previous_location' => 'nullable|string|max:255',
            'previous_room_number' => 'nullable|string|max:100',
            'sub_department' => 'nullable|string',
            'remarks' => 'nullable|string',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function onFailure(Failure ...$failures)
    {
        // Log failures
        foreach ($failures as $failure) {
            \Log::error('Asset import failed', [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ]);
        }
    }
}
