@extends('layouts.app')

@section('title', 'New Asset Transfer')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="mb-1">
                <i class="bi bi-arrow-left-right me-2"></i>
                New Asset Transfer
            </h4>

            <div class="text-muted small">
                Transfer an existing asset to another department.
            </div>
        </div>

        <a href="{{ route('transfers.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Transfers
        </a>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-2">
                Please fix the following errors:
            </div>

            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('transfers.store') }}"
        id="transferForm"
        enctype="multipart/form-data"
    >
        @csrf

        <div class="row g-4">

            {{-- LEFT --}}
            <div class="col-lg-8">

                {{-- Asset Information --}}
                <div class="card shadow-sm border-0 mb-4">

                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="bi bi-box-seam me-2"></i>
                            Asset Information
                        </h6>
                    </div>

                    <div class="card-body">

                        {{-- Asset --}}
                        <div class="mb-3">

                            <label for="asset_id"
                                   class="form-label fw-semibold">
                                Asset <span class="text-danger">*</span>
                            </label>

                            <select
                                name="asset_id"
                                id="asset_id"
                                class="form-select @error('asset_id') is-invalid @enderror"
                                required
                            >
                                <option value="">
                                    Select asset
                                </option>

                                @foreach($assets as $asset)
                                    <option
                                        value="{{ $asset->id }}"
                                        data-department-id="{{ $asset->department_id }}"
                                        data-department-name="{{ $asset->department?->name ?? '' }}"
                                        data-occupant-id="{{ $asset->present_occupant_id ?? '' }}"
                                        data-occupant-name="{{ $asset->occupant?->name ?? '' }}"
                                        {{ old('asset_id') == $asset->id ? 'selected' : '' }}
                                    >
                                        {{ $asset->asset_code }}
                                        —
                                        {{ $asset->item_name }}

                                        @if($asset->make || $asset->model)
                                            —
                                            {{ trim(($asset->make ?? '') . ' ' . ($asset->model ?? '')) }}
                                        @endif
                                    </option>
                                @endforeach

                            </select>

                            @error('asset_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        {{-- Current Department --}}
                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Current Department
                            </label>

                            <input
                                type="text"
                                id="current_department_display"
                                class="form-control bg-light"
                                value=""
                                readonly
                            >

                        </div>

                        {{-- Current Occupant --}}
                        <div class="mb-0">

                            <label class="form-label fw-semibold">
                                Current Occupant
                            </label>

                            <input
                                type="text"
                                id="current_occupant_display"
                                class="form-control bg-light"
                                value=""
                                readonly
                            >

                            <div class="form-text">
                                This value is taken from the asset's current database state.
                            </div>

                        </div>

                    </div>

                </div>


                {{-- Destination --}}
                <div class="card shadow-sm border-0 mb-4">

                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="bi bi-sign-turn-right me-2"></i>
                            Destination
                        </h6>
                    </div>

                    <div class="card-body">

                        {{-- Destination Department --}}
                        <div class="mb-3">

                            <label
                                for="to_department_id"
                                class="form-label fw-semibold"
                            >
                                Destination Department
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                name="to_department_id"
                                id="to_department_id"
                                class="form-select @error('to_department_id') is-invalid @enderror"
                                required
                            >
                                <option value="">
                                    Select destination department
                                </option>

                                @foreach($departments as $department)
                                    <option
                                        value="{{ $department->id }}"
                                        {{ old('to_department_id') == $department->id ? 'selected' : '' }}
                                    >
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('to_department_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Destination Occupant --}}
                        <div class="mb-0">

                            <label
                                for="to_occupant_id"
                                class="form-label fw-semibold"
                            >
                                Destination Occupant

                                <span class="text-muted fw-normal">
                                    (optional)
                                </span>
                            </label>

                            <select
                                name="to_occupant_id"
                                id="to_occupant_id"
                                class="form-select @error('to_occupant_id') is-invalid @enderror"
                            >
                                <option value="">
                                    Select destination department first
                                </option>
                            </select>

                            <div class="form-text">
                                If no employee is selected, the destination
                                department itself will be treated as the occupant.
                            </div>

                            @error('to_occupant_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                </div>


                {{-- Transfer Details --}}
                <div class="card shadow-sm border-0 mb-4">

                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="bi bi-file-text me-2"></i>
                            Transfer Details
                        </h6>
                    </div>

                    <div class="card-body">

                        <div class="row g-3">

                            {{-- Date --}}
                            <div class="col-md-6">

                                <label
                                    for="transfer_date"
                                    class="form-label fw-semibold"
                                >
                                    Transfer Date
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="date"
                                    name="transfer_date"
                                    id="transfer_date"
                                    class="form-control @error('transfer_date') is-invalid @enderror"
                                    value="{{ old('transfer_date', now()->format('Y-m-d')) }}"
                                    required
                                >

                                @error('transfer_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- Reference --}}
                            <div class="col-md-6">

                                <label
                                    for="reference_number"
                                    class="form-label fw-semibold"
                                >
                                    Reference Number

                                    <span class="text-muted fw-normal">
                                        (optional)
                                    </span>
                                </label>

                                <input
                                    type="text"
                                    name="reference_number"
                                    id="reference_number"
                                    class="form-control @error('reference_number') is-invalid @enderror"
                                    value="{{ old('reference_number') }}"
                                    placeholder="Auto-generated if blank"
                                >

                                @error('reference_number')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- Reason --}}
                            <div class="col-12">

                                <label
                                    for="reason"
                                    class="form-label fw-semibold"
                                >
                                    Reason
                                </label>

                                <input
                                    type="text"
                                    name="reason"
                                    id="reason"
                                    class="form-control @error('reason') is-invalid @enderror"
                                    value="{{ old('reason') }}"
                                    maxlength="500"
                                    placeholder="Example: Department requirement / installation"
                                >

                                @error('reason')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- Remarks --}}
                            <div class="col-12">

                                <label
                                    for="remarks"
                                    class="form-label fw-semibold"
                                >
                                    Remarks
                                </label>

                                <textarea
                                    name="remarks"
                                    id="remarks"
                                    rows="4"
                                    class="form-control @error('remarks') is-invalid @enderror"
                                    maxlength="5000"
                                    placeholder="Additional transfer information"
                                >{{ old('remarks') }}</textarea>

                                @error('remarks')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- Transfer Image --}}
                            <div class="col-12">

                                <label
                                    for="transfer_image"
                                    class="form-label fw-semibold"
                                >
                                    Transfer Image
                                    <span class="text-muted fw-normal">
                                        (optional)
                                    </span>
                                </label>

                                <input
                                    type="file"
                                    name="transfer_image"
                                    id="transfer_image"
                                    class="form-control @error('transfer_image') is-invalid @enderror"
                                    accept=".jpg,.jpeg,.png,.webp"
                                >

                                <div class="form-text">
                                    Upload a JPG, JPEG, PNG or WEBP image.
                                    Maximum size: 10 MB.
                                </div>

                                @error('transfer_image')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- RIGHT --}}
            <div class="col-lg-4">

                {{-- Transfer Summary --}}
                <div class="card shadow-sm border-0 mb-4">

                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="bi bi-clipboard-check me-2"></i>
                            Transfer Summary
                        </h6>
                    </div>

                    <div class="card-body">

                        <div class="small text-muted mb-2">
                            Asset
                        </div>

                        <div
                            class="fw-semibold mb-3"
                            id="summary_asset"
                        >
                            Not selected
                        </div>


                        <div class="small text-muted mb-2">
                            Current Department
                        </div>

                        <div
                            class="fw-semibold mb-3"
                            id="summary_from"
                        >
                            —
                        </div>


                        <div class="text-center my-3">
                            <i class="bi bi-arrow-down fs-3 text-primary"></i>
                        </div>


                        <div class="small text-muted mb-2">
                            Destination Department
                        </div>

                        <div
                            class="fw-semibold mb-3"
                            id="summary_to"
                        >
                            —
                        </div>

                        <hr>

                        <div class="small text-muted">
                            The asset's department will change only after
                            the transfer is approved and completed.
                        </div>

                    </div>

                </div>


                {{-- Actions --}}
                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <button
                            type="submit"
                            class="btn btn-primary w-100 mb-2"
                        >
                            <i class="bi bi-send me-1"></i>
                            Create Transfer
                        </button>

                        <a
                            href="{{ route('transfers.index') }}"
                            class="btn btn-outline-secondary w-100"
                        >
                            Cancel
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>


{{-- Dynamic Transfer Form JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const assetSelect =
        document.getElementById('asset_id');

    const currentDepartmentDisplay =
        document.getElementById('current_department_display');

    const currentOccupantDisplay =
        document.getElementById('current_occupant_display');

    const destinationDepartment =
        document.getElementById('to_department_id');

    const destinationOccupant =
        document.getElementById('to_occupant_id');

    const summaryAsset =
        document.getElementById('summary_asset');

    const summaryFrom =
        document.getElementById('summary_from');

    const summaryTo =
        document.getElementById('summary_to');

    const employees =
        @json($employeeData);


    /*
    |--------------------------------------------------------------------------
    | Update Asset Information
    |--------------------------------------------------------------------------
    */

    function updateAssetInformation() {

        const option =
            assetSelect.options[
                assetSelect.selectedIndex
            ];

        if (!option || !option.value) {

            currentDepartmentDisplay.value = '';
            currentOccupantDisplay.value = '';

            summaryAsset.textContent =
                'Not selected';

            summaryFrom.textContent =
                '—';

            return;
        }

        const departmentId =
            option.dataset.departmentId || '';

        const departmentName =
            option.dataset.departmentName || '';

        const occupantName =
            option.dataset.occupantName || '';

        currentDepartmentDisplay.value =
            departmentName;

        /*
         * If there is no employee occupant,
         * the department itself is the occupant.
         */
        currentOccupantDisplay.value =
            occupantName || departmentName;

        summaryAsset.textContent =
            option.textContent.trim();

        summaryFrom.textContent =
            departmentName || '—';


        /*
        |--------------------------------------------------------------------------
        | Prevent transferring to same department
        |--------------------------------------------------------------------------
        */

        Array.from(
            destinationDepartment.options
        ).forEach(function (item) {

            if (!item.value) {
                return;
            }

            item.disabled =
                item.value === String(departmentId);
        });


        /*
         * If the currently selected destination is now
         * disabled, clear it.
         */
        if (
            destinationDepartment.value ===
            String(departmentId)
        ) {
            destinationDepartment.value = '';
        }

        updateDestinationOccupants();
    }


    /*
    |--------------------------------------------------------------------------
    | Update Destination Occupants
    |--------------------------------------------------------------------------
    */

    function updateDestinationOccupants() {

        const departmentId =
            destinationDepartment.value;

        const oldOccupant =
            @json(old('to_occupant_id'));

        destinationOccupant.innerHTML = '';

        const defaultOption =
            document.createElement('option');

        defaultOption.value = '';

        defaultOption.textContent =
            departmentId
                ? 'Department itself — no employee selected'
                : 'Select destination department first';

        destinationOccupant.appendChild(
            defaultOption
        );

        if (!departmentId) {

            summaryTo.textContent =
                '—';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Employees belonging to selected department
        |--------------------------------------------------------------------------
        */

        const matchingEmployees =
            employees.filter(function (employee) {

                return String(
                    employee.department_id
                ) === String(departmentId);

            });


        matchingEmployees.forEach(function (employee) {

            const option =
                document.createElement('option');

            option.value =
                employee.id;

            option.textContent =
                employee.name;

            if (
                oldOccupant &&
                String(oldOccupant) ===
                String(employee.id)
            ) {
                option.selected = true;
            }

            destinationOccupant.appendChild(
                option
            );
        });


        /*
        |--------------------------------------------------------------------------
        | Destination Summary
        |--------------------------------------------------------------------------
        */

        const selectedDepartment =
            destinationDepartment.options[
                destinationDepartment.selectedIndex
            ];

        summaryTo.textContent =
            selectedDepartment
                ? selectedDepartment.textContent.trim()
                : '—';
    }


    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    assetSelect.addEventListener(
        'change',
        updateAssetInformation
    );

    destinationDepartment.addEventListener(
        'change',
        updateDestinationOccupants
    );


    /*
    |--------------------------------------------------------------------------
    | Initial State
    |--------------------------------------------------------------------------
    */

    updateAssetInformation();

});
</script>

@endsection