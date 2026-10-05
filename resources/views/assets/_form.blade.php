@csrf

<div class="row g-3">

    {{-- =========================================================
         Asset Code
    ========================================================== --}}
    <div class="col-md-4">
        <label class="form-label">
            Asset Code <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="asset_code"
            class="form-control @error('asset_code') is-invalid @enderror"
            value="{{ old('asset_code', $asset->asset_code ?? '') }}"
            required
        >

        @error('asset_code')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- =========================================================
         Item Name
    ========================================================== --}}
    <div class="col-md-4">
        <label class="form-label">
            Item Name <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="item_name"
            class="form-control @error('item_name') is-invalid @enderror"
            value="{{ old('item_name', $asset->item_name ?? '') }}"
            required
        >

        @error('item_name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- =========================================================
         Department
    ========================================================== --}}
    <div class="col-md-4">
        <label class="form-label">
            Department <span class="text-danger">*</span>
        </label>

        <select
            name="department_id"
            id="department_id"
            class="form-select @error('department_id') is-invalid @enderror"
            required
        >
            <option value="">
                Select Department
            </option>

            @foreach($departments as $department)
                <option
                    value="{{ $department->id }}"
                    data-department-name="{{ $department->name }}"
                    @selected(
                        old(
                            'department_id',
                            $asset->department_id ?? ''
                        ) == $department->id
                    )
                >
                    {{ $department->name }}
                </option>
            @endforeach
        </select>

        @error('department_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- =========================================================
         Category
    ========================================================== --}}
    <div class="col-md-4">
        <label class="form-label">
            Category <span class="text-danger">*</span>
        </label>

        <select
            name="category_id"
            class="form-select @error('category_id') is-invalid @enderror"
            required
        >
            <option value="">
                Select Category
            </option>

            @foreach($categories as $category)
                <option
                    value="{{ $category->id }}"
                    @selected(
                        old(
                            'category_id',
                            $asset->category_id ?? ''
                        ) == $category->id
                    )
                >
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        @error('category_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- =========================================================
         Make
    ========================================================== --}}
    <div class="col-md-4">
        <label class="form-label">
            Make
        </label>

        <input
            type="text"
            name="make"
            class="form-control @error('make') is-invalid @enderror"
            value="{{ old('make', $asset->make ?? '') }}"
        >

        @error('make')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- =========================================================
         Model
    ========================================================== --}}
    <div class="col-md-4">
        <label class="form-label">
            Model
        </label>

        <input
            type="text"
            name="model"
            class="form-control @error('model') is-invalid @enderror"
            value="{{ old('model', $asset->model ?? '') }}"
        >

        @error('model')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- =========================================================
         Issued Quantity
    ========================================================== --}}
    <div class="col-md-3">
        <label class="form-label">
            Issued Quantity <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            min="0"
            name="issued_quantity"
            class="form-control @error('issued_quantity') is-invalid @enderror"
            value="{{ old('issued_quantity', $asset->issued_quantity ?? 1) }}"
            required
        >

        @error('issued_quantity')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- =========================================================
         Physically Verified
    ========================================================== --}}
    <div class="col-md-3">
        <label class="form-label">
            Physically Verified <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            min="0"
            name="physically_verified_quantity"
            class="form-control @error('physically_verified_quantity') is-invalid @enderror"
            value="{{ old(
                'physically_verified_quantity',
                $asset->physically_verified_quantity ?? 0
            ) }}"
            required
        >

        @error('physically_verified_quantity')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- =========================================================
         Present Occupant
         
         IMPORTANT:
         - Department itself is NOT an employee.
         - Department option has empty value.
         - Employee option has employee ID.
         - Therefore department selection results in
           present_occupant_id = NULL.
    ========================================================== --}}
    <div class="col-md-6">

        <label class="form-label">
            Present Occupant
        </label>

        <select
            name="present_occupant_id"
            id="present_occupant_id"
            class="form-select @error('present_occupant_id') is-invalid @enderror"
        >

            {{-- This option is dynamically replaced with
                 the selected department name --}}
            <option
                value=""
                id="departmentOccupantOption"
                data-type="department"
            >
                Select Department First
            </option>

            @foreach($employees as $employee)

                <option
                    value="{{ $employee->id }}"
                    data-department-id="{{ $employee->department_id }}"
                    data-type="employee"
                    @selected(
                        old(
                            'present_occupant_id',
                            $asset->present_occupant_id ?? ''
                        ) == $employee->id
                    )
                >
                    {{ $employee->name }}
                    @if($employee->employee_code)
                        — {{ $employee->employee_code }}
                    @endif
                </option>

            @endforeach

        </select>

        <div
            id="occupantHelp"
            class="form-text"
        >
            Select a department first.
        </div>

        @error('present_occupant_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- =========================================================
         Serial Number
    ========================================================== --}}
    <div class="col-md-4">
        <label class="form-label">
            Serial Number
        </label>

        <input
            type="text"
            name="asset_serial_number"
            class="form-control @error('asset_serial_number') is-invalid @enderror"
            value="{{ old(
                'asset_serial_number',
                $asset->asset_serial_number ?? ''
            ) }}"
        >

        @error('asset_serial_number')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- =========================================================
         Barcode
    ========================================================== --}}
    <div class="col-md-4">
        <label class="form-label">
            Barcode / Sticker No.
        </label>

        <input
            type="text"
            name="barcode_number"
            class="form-control @error('barcode_number') is-invalid @enderror"
            value="{{ old(
                'barcode_number',
                $asset->barcode_number ?? ''
            ) }}"
        >

        @error('barcode_number')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- =========================================================
         Date of Issue
    ========================================================== --}}
    <div class="col-md-4">
        <label class="form-label">
            Date of Issue
        </label>

        <input
            type="date"
            name="date_of_issue"
            class="form-control @error('date_of_issue') is-invalid @enderror"
            value="{{ old(
                'date_of_issue',
                isset($asset) && $asset->date_of_issue
                    ? $asset->date_of_issue->format('Y-m-d')
                    : ''
            ) }}"
        >

        @error('date_of_issue')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- =========================================================
         Status
    ========================================================== --}}
    <div class="col-md-4">
        <label class="form-label">
            Status <span class="text-danger">*</span>
        </label>

        <select
            name="asset_status"
            class="form-select @error('asset_status') is-invalid @enderror"
            required
        >
            @foreach([
                'Active',
                'Assigned',
                'Unassigned',
                'Missing',
                'Damaged',
                'Disposed',
                'Transferred',
                'Under Repair',
                'Retired'
            ] as $status)

                <option
                    value="{{ $status }}"
                    @selected(
                        old(
                            'asset_status',
                            $asset->asset_status ?? 'Active'
                        ) === $status
                    )
                >
                    {{ $status }}
                </option>

            @endforeach
        </select>

        @error('asset_status')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- =========================================================
         Condition
    ========================================================== --}}
    <div class="col-md-4">
        <label class="form-label">
            Condition <span class="text-danger">*</span>
        </label>

        <select
            name="condition"
            class="form-select @error('condition') is-invalid @enderror"
            required
        >
            @foreach([
                'New',
                'Good',
                'Fair',
                'Damaged',
                'Critical'
            ] as $condition)

                <option
                    value="{{ $condition }}"
                    @selected(
                        old(
                            'condition',
                            $asset->condition ?? 'Good'
                        ) === $condition
                    )
                >
                    {{ $condition }}
                </option>

            @endforeach
        </select>

        @error('condition')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- =========================================================
         Location
    ========================================================== --}}
    <div class="col-md-4">
        <label class="form-label">
            Location
        </label>

        <input
            type="text"
            name="location"
            class="form-control @error('location') is-invalid @enderror"
            value="{{ old('location', $asset->location ?? '') }}"
        >

        @error('location')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- =========================================================
         Remarks
    ========================================================== --}}
    <div class="col-12">
        <label class="form-label">
            Remarks
        </label>

        <textarea
            name="remarks"
            class="form-control @error('remarks') is-invalid @enderror"
            rows="3"
        >{{ old('remarks', $asset->remarks ?? '') }}</textarea>

        @error('remarks')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

</div>


{{-- =============================================================
     Buttons
============================================================== --}}
<div class="mt-4">

    <button
        type="submit"
        class="btn btn-primary"
    >
        <i class="bi bi-check-lg me-1"></i>
        {{ $buttonText }}
    </button>

    <a
        href="{{ route('assets.index') }}"
        class="btn btn-light"
    >
        Cancel
    </a>

</div>


{{-- =============================================================
     Department → Present Occupant Filtering
     
     Rules:
     
     1. Department is always required.
     2. Department itself appears as the first occupant option.
     3. Department option has value="".
     4. Therefore selecting the department stores
        present_occupant_id = NULL.
     5. Employees are filtered according to department_id.
     6. Employees from other departments cannot be selected.
     7. Changing department automatically selects the new
        department as Present Occupant.
============================================================== --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const departmentSelect =
        document.getElementById('department_id');

    const occupantSelect =
        document.getElementById('present_occupant_id');

    const departmentOccupantOption =
        document.getElementById('departmentOccupantOption');

    const occupantHelp =
        document.getElementById('occupantHelp');


    if (
        !departmentSelect ||
        !occupantSelect ||
        !departmentOccupantOption
    ) {
        return;
    }


    /*
     * Store the initial employee selection.
     *
     * This is important for EDIT mode.
     *
     * If an existing asset already has an employee assigned,
     * we want to keep that employee selected when the page
     * initially loads.
     */
    const initialOccupantValue =
        occupantSelect.value;


    /*
     * Get the selected department name.
     */
    function getDepartmentName() {

        const selectedOption =
            departmentSelect.options[
                departmentSelect.selectedIndex
            ];

        if (!selectedOption) {
            return '';
        }

        return (
            selectedOption.dataset.departmentName ||
            selectedOption.textContent.trim()
        );
    }


    /*
     * Update the department option.
     */
    function updateDepartmentOccupantOption() {

        const departmentId =
            departmentSelect.value;

        const departmentName =
            getDepartmentName();


        if (!departmentId) {

            departmentOccupantOption.textContent =
                'Select Department First';

            departmentOccupantOption.disabled = false;

            return;
        }


        departmentOccupantOption.textContent =
            departmentName;


        /*
         * The department option always represents
         * "department itself" and therefore has no
         * employee ID.
         */
        departmentOccupantOption.value = '';

        departmentOccupantOption.disabled = false;
    }


    /*
     * Filter employees according to selected department.
     */
    function filterEmployees(resetToDepartment = false) {

        const departmentId =
            departmentSelect.value;

        let matchingEmployees = 0;


        updateDepartmentOccupantOption();


        /*
         * No department selected.
         */
        if (!departmentId) {

            Array.from(
                occupantSelect.options
            ).forEach(function (option) {

                if (
                    option.dataset.type === 'employee'
                ) {
                    option.hidden = true;
                    option.disabled = true;
                }

            });


            occupantSelect.value = '';

            occupantHelp.textContent =
                'Select a department first.';

            return;
        }


        /*
         * Filter employee options.
         */
        Array.from(
            occupantSelect.options
        ).forEach(function (option) {

            /*
             * Department option.
             */
            if (
                option.dataset.type === 'department'
            ) {

                option.hidden = false;
                option.disabled = false;

                return;
            }


            /*
             * Employee option.
             */
            if (
                option.dataset.type === 'employee'
            ) {

                const employeeDepartmentId =
                    option.dataset.departmentId;

                const matches =
                    employeeDepartmentId === departmentId;


                option.hidden = !matches;
                option.disabled = !matches;


                if (matches) {
                    matchingEmployees++;
                }

            }

        });


        /*
         * If department was changed by the user,
         * select the department itself.
         */
        if (resetToDepartment) {

            occupantSelect.value = '';

        }


        /*
         * Initial page load / edit mode.
         *
         * If an existing employee belongs to this
         * department, preserve that employee.
         *
         * Otherwise use the department option.
         */
        if (!resetToDepartment) {

            const currentValue =
                occupantSelect.value;


            if (currentValue !== '') {

                const selectedEmployee =
                    Array.from(
                        occupantSelect.options
                    ).find(function (option) {

                        return (
                            option.value === currentValue &&
                            option.dataset.type === 'employee' &&
                            option.dataset.departmentId === departmentId &&
                            !option.disabled
                        );

                    });


                if (!selectedEmployee) {

                    occupantSelect.value = '';

                }

            }

        }


        /*
         * Help text.
         */
        if (matchingEmployees === 0) {

            occupantHelp.textContent =
                departmentName() +
                ' is selected as the Present Occupant. ' +
                'No active employees are currently assigned to this department.';

        } else {

            occupantHelp.textContent =
                'Select the department itself if the employee is unknown, ' +
                'or select an employee belonging to this department.';
        }

    }


    /*
     * Helper to get the current department name.
     */
    function departmentName() {

        return getDepartmentName() || 'The department';
    }


    /*
     * Department changed.
     *
     * Whenever the department changes, automatically
     * select the department itself.
     */
    departmentSelect.addEventListener(
        'change',
        function () {

            filterEmployees(true);

        }
    );


    /*
     * Initial page load.
     *
     * Preserve an existing employee during EDIT mode.
     */
    filterEmployees(false);


    /*
     * If there is no existing employee selected,
     * explicitly select the department option.
     */
    if (
        initialOccupantValue === '' &&
        departmentSelect.value !== ''
    ) {

        occupantSelect.value = '';

    }

});

</script>