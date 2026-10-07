@php
$isEdit = isset($asset) && $asset->exists;

$selectedDepartmentId = old(
'department_id',
$asset->department_id ?? ''
);

$selectedOccupantId = old(
'present_occupant_id',
$asset->present_occupant_id ?? ''
);
@endphp

@csrf

<div class="row g-3">

    {{-- =========================
        ROW 1
    ========================== --}}

    {{-- Asset Code --}}
    <div class="col-md-4">
        <label for="asset_code" class="form-label">
            Asset Code <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="asset_code"
            id="asset_code"
            class="form-control @error('asset_code') is-invalid @enderror"
            value="{{ old('asset_code', $asset->asset_code ?? '') }}"
            required>

        @error('asset_code')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Item Name --}}
    <div class="col-md-4">
        <label for="item_name" class="form-label">
            Item Name <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="item_name"
            id="item_name"
            class="form-control @error('item_name') is-invalid @enderror"
            value="{{ old('item_name', $asset->item_name ?? '') }}"
            required>

        @error('item_name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Category --}}
    <div class="col-md-4">
        <label for="category_id" class="form-label">
            Category <span class="text-danger">*</span>
        </label>

        <select
            name="category_id"
            id="category_id"
            class="form-select @error('category_id') is-invalid @enderror"
            required>
            <option value="">Select Category</option>

            @foreach($categories as $category)
            <option
                value="{{ $category->id }}"
                @selected(
                old( 'category_id' ,
                $asset->category_id ?? ''
                ) == $category->id
                )
                >
                {{ $category->name }}
            </option>
            @endforeach
        </select>

        @error('category_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- =========================
        ROW 2
    ========================== --}}

    {{-- Department --}}
    <div class="col-md-4">
        <label class="form-label">
            Department <span class="text-danger">*</span>
        </label>

        @if($isEdit)

        {{-- Department cannot be changed from Asset Edit --}}
        <div class="form-control bg-light">
            {{ $asset->department?->name ?? '—' }}
        </div>

        <div class="form-text">
            Department can only be changed through Asset Transfer.
        </div>

        @else

        <select
            name="department_id"
            id="department_id"
            class="form-select @error('department_id') is-invalid @enderror"
            required>
            <option value="">Select Department</option>

            @foreach($departments as $department)
            <option
                value="{{ $department->id }}"
                @selected($selectedDepartmentId==$department->id)
                >
                {{ $department->name }}
            </option>
            @endforeach
        </select>

        @error('department_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @endif
    </div>


    {{-- Present Occupant --}}
    <div class="col-md-4">
        <label class="form-label">
            Present Occupant
        </label>

        @if($isEdit)

        {{-- Occupant cannot be changed from Asset Edit --}}
        <div class="form-control bg-light">
            @if($asset->occupant)
            {{ $asset->occupant->name }}
            @elseif($asset->department)
            {{ $asset->department->name }}
            @else
            —
            @endif
        </div>

        <div class="form-text">
            Occupant can only be changed through Asset Transfer.
        </div>

        @else

        <select
            name="present_occupant_id"
            id="present_occupant_id"
            class="form-select @error('present_occupant_id') is-invalid @enderror">
            <option value="">
                Select department / employee
            </option>

            {{-- Employees are inserted dynamically by JavaScript --}}
        </select>

        @error('present_occupant_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div class="form-text">
            Select the department itself if the exact employee is unknown.
        </div>

        @endif
    </div>


    {{-- Make --}}
    <div class="col-md-4">
        <label for="make" class="form-label">
            Make
        </label>

        <input
            type="text"
            name="make"
            id="make"
            class="form-control @error('make') is-invalid @enderror"
            value="{{ old('make', $asset->make ?? '') }}">

        @error('make')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- =========================
        ROW 3
    ========================== --}}

    {{-- Model --}}
    <div class="col-md-4">
        <label for="model" class="form-label">
            Model
        </label>

        <input
            type="text"
            name="model"
            id="model"
            class="form-control @error('model') is-invalid @enderror"
            value="{{ old('model', $asset->model ?? '') }}">

        @error('model')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Serial Number --}}
    <div class="col-md-4">
        <label for="asset_serial_number" class="form-label">
            Serial Number
        </label>

        <input
            type="text"
            name="asset_serial_number"
            id="asset_serial_number"
            class="form-control @error('asset_serial_number') is-invalid @enderror"
            value="{{ old('asset_serial_number', $asset->asset_serial_number ?? '') }}">

        @error('asset_serial_number')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Barcode --}}
    <div class="col-md-4">
        <label for="barcode_number" class="form-label">
            Barcode Number
        </label>

        <input
            type="text"
            name="barcode_number"
            id="barcode_number"
            class="form-control @error('barcode_number') is-invalid @enderror"
            value="{{ old('barcode_number', $asset->barcode_number ?? '') }}">

        @error('barcode_number')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- =========================
        ROW 4
    ========================== --}}

    {{-- Issued Quantity --}}
    <div class="col-md-4">
        <label for="issued_quantity" class="form-label">
            Issued Quantity <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            name="issued_quantity"
            id="issued_quantity"
            class="form-control @error('issued_quantity') is-invalid @enderror"
            value="{{ old('issued_quantity', $asset->issued_quantity ?? 0) }}"
            min="0"
            required>

        @error('issued_quantity')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Physically Verified --}}
    <div class="col-md-4">
        <label for="physically_verified_quantity" class="form-label">
            Physically Verified <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            name="physically_verified_quantity"
            id="physically_verified_quantity"
            class="form-control @error('physically_verified_quantity') is-invalid @enderror"
            value="{{ old('physically_verified_quantity', $asset->physically_verified_quantity ?? 0) }}"
            min="0"
            required>

        @error('physically_verified_quantity')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Date of Issue --}}
    <div class="col-md-4">
        <label for="date_of_issue" class="form-label">
            Date of Issue
        </label>

        <input
            type="date"
            name="date_of_issue"
            id="date_of_issue"
            class="form-control @error('date_of_issue') is-invalid @enderror"
            value="{{ old(
                'date_of_issue',
                isset($asset->date_of_issue)
                    ? $asset->date_of_issue->format('Y-m-d')
                    : ''
            ) }}">

        @error('date_of_issue')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- =========================
        ROW 5
    ========================== --}}

    {{-- Status --}}
    <div class="col-md-4">
        <label for="asset_status" class="form-label">
            Status <span class="text-danger">*</span>
        </label>

        <select
            name="asset_status"
            id="asset_status"
            class="form-select @error('asset_status') is-invalid @enderror"
            required>
            @php
            $statuses = [
            'Active',
            'Assigned',
            'Unassigned',
            'Missing',
            'Damaged',
            'Disposed',
            'Transferred',
            'Under Repair',
            'Retired',
            ];
            @endphp

            <option value="">Select Status</option>

            @foreach($statuses as $status)
            <option
                value="{{ $status }}"
                @selected(
                old( 'asset_status' ,
                $asset->asset_status ?? ''
                ) === $status
                )
                >
                {{ $status }}
            </option>
            @endforeach
        </select>

        @error('asset_status')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Condition --}}
    <div class="col-md-4">
        <label for="condition" class="form-label">
            Condition <span class="text-danger">*</span>
        </label>

        <select
            name="condition"
            id="condition"
            class="form-select @error('condition') is-invalid @enderror"
            required>
            @php
            $conditions = [
            'New',
            'Good',
            'Fair',
            'Damaged',
            'Critical',
            ];
            @endphp

            <option value="">Select Condition</option>

            @foreach($conditions as $condition)
            <option
                value="{{ $condition }}"
                @selected(
                old( 'condition' ,
                $asset->condition ?? ''
                ) === $condition
                )
                >
                {{ $condition }}
            </option>
            @endforeach
        </select>

        @error('condition')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Location --}}
    <div class="col-md-4">
        <label for="location" class="form-label">
            Location
        </label>

        <input
            type="text"
            name="location"
            id="location"
            class="form-control @error('location') is-invalid @enderror"
            value="{{ old('location', $asset->location ?? '') }}">

        @error('location')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- =========================
        ROW 6
    ========================== --}}

    {{-- Remarks --}}
    <div class="col-12">
        <label for="remarks" class="form-label">
            Remarks
        </label>

        <textarea
            name="remarks"
            id="remarks"
            rows="4"
            class="form-control @error('remarks') is-invalid @enderror">{{ old('remarks', $asset->remarks ?? '') }}</textarea>

        @error('remarks')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- =========================
        BUTTONS
    ========================== --}}

    <div class="col-12">
        <hr class="my-3">

        <div class="d-flex justify-content-end gap-2">

            <a
                href="{{ route('assets.index') }}"
                class="btn btn-outline-secondary">
                Cancel
            </a>

            <button
                type="submit"
                class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>
                {{ $buttonText ?? ($isEdit ? 'Save Changes' : 'Create Asset') }}
            </button>

        </div>
    </div>

</div>


{{-- =========================================================
    PRESENT OCCUPANT DYNAMIC FILTER
    Only required on CREATE
========================================================= --}}

@if(!$isEdit)

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const departmentSelect =
        document.getElementById('department_id');

    const occupantSelect =
        document.getElementById('present_occupant_id');

    if (!departmentSelect || !occupantSelect) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Employee data
    |--------------------------------------------------------------------------
    |
    | Do not use PHP -> operators here.
    | data_get() keeps this section safe from the formatting issue
    | you currently have in VS Code.
    |
    */

    const employees = [
        @foreach($employees as $employee)
        {
            id: @json(data_get($employee, 'id')),
            name: @json(data_get($employee, 'name')),
            department_id: @json(data_get($employee, 'department_id'))
        },
        @endforeach
    ];

    const oldOccupantId =
        @json(old('present_occupant_id', ''));

    /*
    |--------------------------------------------------------------------------
    | Get department name
    |--------------------------------------------------------------------------
    */

    function getDepartmentName() {

        const selectedOption =
            departmentSelect.options[
                departmentSelect.selectedIndex
            ];

        if (!selectedOption) {
            return '';
        }

        return selectedOption.textContent.trim();
    }

    /*
    |--------------------------------------------------------------------------
    | Build Present Occupant dropdown
    |--------------------------------------------------------------------------
    */

    function rebuildOccupants() {

        const departmentId =
            departmentSelect.value;

        /*
        |--------------------------------------------------------------------------
        | Reset dropdown
        |--------------------------------------------------------------------------
        */

        occupantSelect.innerHTML = '';

        /*
        |--------------------------------------------------------------------------
        | No department selected
        |--------------------------------------------------------------------------
        */

        if (!departmentId) {

            const option =
                document.createElement('option');

            option.value = '';
            option.textContent =
                'Select department first';

            occupantSelect.appendChild(option);

            occupantSelect.disabled = true;

            return;
        }

        occupantSelect.disabled = false;

        /*
        |--------------------------------------------------------------------------
        | Department itself
        |--------------------------------------------------------------------------
        |
        | Empty value means:
        |
        | present_occupant_id = NULL
        |
        | Therefore the department becomes the fallback
        | Present Occupant.
        |
        */

        const departmentOption =
            document.createElement('option');

        departmentOption.value = '';

        departmentOption.textContent =
            getDepartmentName() + ' (Department)';

        occupantSelect.appendChild(
            departmentOption
        );

        /*
        |--------------------------------------------------------------------------
        | Employees belonging to selected department
        |--------------------------------------------------------------------------
        */

        const matchingEmployees =
            employees.filter(function (employee) {

                return String(employee.department_id) ===
                    String(departmentId);

            });

        matchingEmployees.forEach(function (employee) {

            const option =
                document.createElement('option');

            option.value = employee.id;

            option.textContent = employee.name;

            occupantSelect.appendChild(option);

        });

        /*
        |--------------------------------------------------------------------------
        | Restore old selected employee after validation error
        |--------------------------------------------------------------------------
        */

        if (oldOccupantId !== null &&
            oldOccupantId !== '') {

            const employeeOption =
                Array.from(
                    occupantSelect.options
                ).find(function (option) {

                    return String(option.value) ===
                        String(oldOccupantId);

                });

            if (employeeOption) {

                occupantSelect.value =
                    oldOccupantId;

            } else {

                /*
                |--------------------------------------------------------------------------
                | Employee does not belong to selected department.
                | Fall back to department.
                |--------------------------------------------------------------------------
                */

                occupantSelect.value = '';

            }

        } else {

            /*
            |--------------------------------------------------------------------------
            | No employee selected.
            | Department is the Present Occupant.
            |--------------------------------------------------------------------------
            */

            occupantSelect.value = '';

        }
    }

    /*
    |--------------------------------------------------------------------------
    | Department changed
    |--------------------------------------------------------------------------
    */

    departmentSelect.addEventListener(
        'change',
        function () {

            /*
            | When department changes, automatically
            | reset Present Occupant to department.
            */

            rebuildOccupants();

            occupantSelect.value = '';

        }
    );

    /*
    |--------------------------------------------------------------------------
    | Initial page load
    |--------------------------------------------------------------------------
    */

    rebuildOccupants();

});
</script>
@endpush

@endif