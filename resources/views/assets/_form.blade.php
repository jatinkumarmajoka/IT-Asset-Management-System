@php
$isEdit = isset($asset) && $asset->exists;
$selectedDepartmentId = old('department_id', $asset->department_id ?? '');
@endphp
@csrf
<div class="row g-4">

    {{-- =========================================================
        ROW 1
    ========================================================== --}}

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
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- =========================================================
        ROW 2
    ========================================================== --}}

    {{-- =========================================================
        OCCUPANCY DETAILS
    ========================================================== --}}

    {{-- =========================================================
        PREVIOUS OCCUPANCY: Department | Location | Room Number
    ========================================================== --}}

{{-- Previous Occupant Department: choose from core departments --}}
    <div class="col-md-4">
        <label for="previous_department_name" class="form-label">
            Previous Occupant Department
        </label>
        <select
            name="previous_department_name"
            id="previous_department_name"
            class="form-select @error('previous_department_name') is-invalid @enderror"
            disabled>
            <option value="">Select previous department</option>
            @foreach($departments as $department)
                <option
                    value="{{ $department->name }}"
                    @selected(old('previous_department_name', $asset->previous_department_name ?? '') === $department->name)>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
        @error('previous_department_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    @php
    $occupancyLocations = [
        'Research Lab', 'Equipment Lab', 'Computer Lab', 'Classroom',
        'Lecture Hall', 'Office', 'Store Room', 'Workshop', 'Library', 'Other',
    ];
@endphp

{{-- Previous Occupant Location --}}
    <div class="col-md-4">
        <label for="previous_location" class="form-label">
            Previous Occupant Location
        </label>
        <select
            name="previous_location"
            id="previous_location"
            class="form-select @error('previous_location') is-invalid @enderror"
            disabled>
            <option value="">Select previous location</option>
            @foreach($occupancyLocations as $occupancyLocation)
            <option
                value="{{ $occupancyLocation }}"
                @selected(old('previous_location', $asset->previous_location ?? '') === $occupancyLocation)
                >
                {{ $occupancyLocation }}
            </option>
            @endforeach
        </select>
        @error('previous_location')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

{{-- Previous Room Number --}}
    <div class="col-md-4">
        <label for="previous_room_number" class="form-label">
            Previous Room Number
        </label>
        <input
            type="text"
            name="previous_room_number"
            id="previous_room_number"
            class="form-control @error('previous_room_number') is-invalid @enderror"
            disabled
            value="{{ old('previous_room_number', $asset->previous_room_number ?? '') }}"
            placeholder="e.g. Room 101">
        @error('previous_room_number')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12"><hr class="my-2"></div>

    {{-- =========================================================
        PRESENT ASSIGNMENT: Department | Location | Room Number
    ========================================================== --}}

{{-- Present Occupant Department: transfer-controlled --}}
    <div class="col-md-4">
        <label for="department_id" class="form-label">
            Present Occupant Department <span class="text-danger">*</span>
        </label>
        @if($isEdit)
        <input
            type="text"
            id="department_id"
            class="form-control bg-light"
            value="{{ $asset->department?->name ?? '—' }}"
            readonly>
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
                @selected($selectedDepartmentId == $department->id)
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

{{-- Present Occupant Location --}}
    <div class="col-md-4">
        <label for="present_location" class="form-label">
            Present Occupant Location
        </label>
        <select
            name="present_location"
            id="present_location"
            class="form-select @error('present_location') is-invalid @enderror"
            @disabled($isEdit)>
            <option value="">Select present location</option>
            @foreach($occupancyLocations as $occupancyLocation)
            <option
                value="{{ $occupancyLocation }}"
                @selected(old('present_location', $asset->present_location ?? $asset->location ?? '') === $occupancyLocation)
                >
                {{ $occupancyLocation }}
            </option>
            @endforeach
        </select>
        @error('present_location')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if($isEdit)
            <div class="form-text">Location can only be changed through Asset Transfer.</div>
        @endif
    </div>

    {{-- Present Room Number --}}
    <div class="col-md-4">
        <label for="present_room_number" class="form-label">
            Present Room Number
        </label>
        <input
            type="text"
            name="present_room_number"
            id="present_room_number"
            class="form-control @error('present_room_number') is-invalid @enderror"
            @disabled($isEdit)
            value="{{ old('present_room_number', $asset->present_room_number ?? '') }}"
            placeholder="e.g. Room 205">
        @error('present_room_number')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if($isEdit)
            <div class="form-text">Room number can only be changed through Asset Transfer.</div>
        @endif
    </div>

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

    {{-- Condition --}}
    <div class="col-md-4">
        <label for="condition" class="form-label">
            Condition <span class="text-danger">*</span>
        </label>
        @php
    $conditions = ['New', 'Good', 'Fair', 'Damaged', 'Critical'];
@endphp
        <select
            name="condition"
            id="condition"
            class="form-select @error('condition') is-invalid @enderror"
            required>
            <option value="">Select Condition</option>
            @foreach($conditions as $condition)
            <option
                value="{{ $condition }}"
                @selected(
                old(
                'condition',
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

    {{-- =========================================================
        ROW 4
        Serial Number | Barcode Number | Date of Issue
    ========================================================== --}}

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

    {{-- =========================================================
        ROW 5
        Make | Model | Status | Location
    ========================================================== --}}

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

    {{-- Status --}}
    <div class="col-md-4">
        <label for="asset_status" class="form-label">
            Status <span class="text-danger">*</span>
        </label>
        @php
    $statuses = [
        'Active', 'Assigned', 'Unassigned', 'Missing', 'Damaged',
        'Disposed', 'Return to Store', 'Under Repair', 'Retired',
    ];
@endphp
        <select
            name="asset_status"
            id="asset_status"
            class="form-select @error('asset_status') is-invalid @enderror"
            required>
            <option value="">Select Status</option>
            @foreach($statuses as $status)
            <option
                value="{{ $status }}"
                @selected(
                old(
                'asset_status',
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


    {{-- =========================================================
        ROW 6
        Remarks
    ========================================================== --}}
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

    {{-- =========================================================
        DOCUMENTS (NEW ASSET)
        New assets have no ID yet, so both selected files are submitted
        together with the Create Asset form.
    ========================================================== --}}
    @if(!$isEdit && (($canManageAssets ?? false) || ($canManageBills ?? false)))
        <div class="col-12">
            <div class="card border shadow-sm mt-2">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-1"><i class="bi bi-paperclip me-2"></i>Asset Image & Bill/Invoice</h4>
                    <div class="text-muted">Select files below. They will be uploaded when you click Create Asset.</div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        @if($canManageAssets ?? false)
                            <div class="col-lg-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0"><i class="bi bi-image me-2"></i>Asset Image</h6>
                                        <span class="badge bg-secondary" id="asset_image_status">Not Selected</span>
                                    </div>
                                    <div class="d-flex gap-2 mb-3">
                                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('asset_image').click()">
                                            <i class="bi bi-folder2-open me-1"></i> Select Image
                                        </button>
                                        <button type="button" class="btn btn-primary" id="asset_image_upload_btn" disabled onclick="uploadSingleFile('asset_image')">
                                            <i class="bi bi-cloud-upload me-1"></i> Upload
                                        </button>
                                    </div>
                                    <input
                                        type="file"
                                        name="asset_image"
                                        id="asset_image"
                                        class="form-control @error('asset_image') is-invalid @enderror d-none"
                                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                        onchange="updateFileDisplay(this, 'asset_image')">
                                    <div id="asset_image_preview" class="file-preview d-none mb-3">
                                        <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-file-earmark-image text-primary"></i>
                                                <span id="asset_image_filename" class="fw-medium"></span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFile('asset_image')">
                                                <i class="bi bi-x-lg"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-text">Maximum file size: 10 MB. JPG, JPEG, PNG, or WEBP.</div>
                                    @error('asset_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif

                        @if($canManageBills ?? false)
                            <div class="col-lg-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>Bill / Invoice</h6>
                                        <span class="badge bg-secondary" id="bill_invoice_status">Not Selected</span>
                                    </div>
                                    <div class="d-flex gap-2 mb-3">
                                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('bill_invoice').click()">
                                            <i class="bi bi-folder2-open me-1"></i> Select Bill
                                        </button>
                                        <button type="button" class="btn btn-primary" id="bill_invoice_upload_btn" disabled onclick="uploadSingleFile('bill_invoice')">
                                            <i class="bi bi-cloud-upload me-1"></i> Upload
                                        </button>
                                    </div>
                                    <input
                                        type="file"
                                        name="bill_invoice"
                                        id="bill_invoice"
                                        class="form-control @error('bill_invoice') is-invalid @enderror d-none"
                                        accept=".jpg,.jpeg,.png,.webp,.pdf,image/jpeg,image/png,image/webp,application/pdf"
                                        onchange="updateFileDisplay(this, 'bill_invoice')">
                                    <div id="bill_invoice_preview" class="file-preview d-none mb-3">
                                        <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-file-earmark-text text-primary"></i>
                                                <span id="bill_invoice_filename" class="fw-medium"></span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFile('bill_invoice')">
                                                <i class="bi bi-x-lg"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-text">Maximum file size: 10 MB. PDF or supported image. Bill / Invoice documents are locked after upload.</div>
                                    @error('bill_invoice')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif

isEdit = isset($asset) && $asset->exists;
$selectedDepartmentId = old('department_id', $asset->department_id ?? '');
@endphp
@csrf
<div class="row g-4">

    {{-- =========================================================
        ROW 1
    ========================================================== --}}

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
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- =========================================================
        ROW 2
    ========================================================== --}}

    {{-- =========================================================
        OCCUPANCY DETAILS
    ========================================================== --}}

    {{-- =========================================================
        PREVIOUS OCCUPANCY: Department | Location | Room Number
    ========================================================== --}}

{{-- Previous Occupant Department: choose from core departments --}}
    <div class="col-md-4">
        <label for="previous_department_name" class="form-label">
            Previous Occupant Department
        </label>
        <select
            name="previous_department_name"
            id="previous_department_name"
            class="form-select @error('previous_department_name') is-invalid @enderror"
            disabled>
            <option value="">Select previous department</option>
            @foreach($departments as $department)
                <option
                    value="{{ $department->name }}"
                    @selected(old('previous_department_name', $asset->previous_department_name ?? '') === $department->name)>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
        @error('previous_department_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    @php
    $occupancyLocations = [
        'Research Lab', 'Equipment Lab', 'Computer Lab', 'Classroom',
        'Lecture Hall', 'Office', 'Store Room', 'Workshop', 'Library', 'Other',
    ];
@endphp

{{-- Previous Occupant Location --}}
    <div class="col-md-4">
        <label for="previous_location" class="form-label">
            Previous Occupant Location
        </label>
        <select
            name="previous_location"
            id="previous_location"
            class="form-select @error('previous_location') is-invalid @enderror"
            disabled>
            <option value="">Select previous location</option>
            @foreach($occupancyLocations as $occupancyLocation)
            <option
                value="{{ $occupancyLocation }}"
                @selected(old('previous_location', $asset->previous_location ?? '') === $occupancyLocation)
                >
                {{ $occupancyLocation }}
            </option>
            @endforeach
        </select>
        @error('previous_location')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

{{-- Previous Room Number --}}
    <div class="col-md-4">
        <label for="previous_room_number" class="form-label">
            Previous Room Number
        </label>
        <input
            type="text"
            name="previous_room_number"
            id="previous_room_number"
            class="form-control @error('previous_room_number') is-invalid @enderror"
            disabled
            value="{{ old('previous_room_number', $asset->previous_room_number ?? '') }}"
            placeholder="e.g. Room 101">
        @error('previous_room_number')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12"><hr class="my-2"></div>

    {{-- =========================================================
        PRESENT ASSIGNMENT: Department | Location | Room Number
    ========================================================== --}}

{{-- Present Occupant Department: transfer-controlled --}}
    <div class="col-md-4">
        <label for="department_id" class="form-label">
            Present Occupant Department <span class="text-danger">*</span>
        </label>
        @if($isEdit)
        <input
            type="text"
            id="department_id"
            class="form-control bg-light"
            value="{{ $asset->department?->name ?? '—' }}"
            readonly>
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
                @selected($selectedDepartmentId == $department->id)
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

{{-- Present Occupant Location --}}
    <div class="col-md-4">
        <label for="present_location" class="form-label">
            Present Occupant Location
        </label>
        <select
            name="present_location"
            id="present_location"
            class="form-select @error('present_location') is-invalid @enderror"
            @disabled($isEdit)>
            <option value="">Select present location</option>
            @foreach($occupancyLocations as $occupancyLocation)
            <option
                value="{{ $occupancyLocation }}"
                @selected(old('present_location', $asset->present_location ?? $asset->location ?? '') === $occupancyLocation)
                >
                {{ $occupancyLocation }}
            </option>
            @endforeach
        </select>
        @error('present_location')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if($isEdit)
            <div class="form-text">Location can only be changed through Asset Transfer.</div>
        @endif
    </div>

    {{-- Present Room Number --}}
    <div class="col-md-4">
        <label for="present_room_number" class="form-label">
            Present Room Number
        </label>
        <input
            type="text"
            name="present_room_number"
            id="present_room_number"
            class="form-control @error('present_room_number') is-invalid @enderror"
            @disabled($isEdit)
            value="{{ old('present_room_number', $asset->present_room_number ?? '') }}"
            placeholder="e.g. Room 205">
        @error('present_room_number')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if($isEdit)
            <div class="form-text">Room number can only be changed through Asset Transfer.</div>
        @endif
    </div>

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

    {{-- Condition --}}
    <div class="col-md-4">
        <label for="condition" class="form-label">
            Condition <span class="text-danger">*</span>
        </label>
        @php
    $conditions = ['New', 'Good', 'Fair', 'Damaged', 'Critical'];
@endphp
        <select
            name="condition"
            id="condition"
            class="form-select @error('condition') is-invalid @enderror"
            required>
            <option value="">Select Condition</option>
            @foreach($conditions as $condition)
            <option
                value="{{ $condition }}"
                @selected(
                old(
                'condition',
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

    {{-- =========================================================
        ROW 4
        Serial Number | Barcode Number | Date of Issue
    ========================================================== --}}

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

    {{-- =========================================================
        ROW 5
        Make | Model | Status | Location
    ========================================================== --}}

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

    {{-- Status --}}
    <div class="col-md-4">
        <label for="asset_status" class="form-label">
            Status <span class="text-danger">*</span>
        </label>
        @php
    $statuses = [
        'Active', 'Assigned', 'Unassigned', 'Missing', 'Damaged',
        'Disposed', 'Return to Store', 'Under Repair', 'Retired',
    ];
@endphp
        <select
            name="asset_status"
            id="asset_status"
            class="form-select @error('asset_status') is-invalid @enderror"
            required>
            <option value="">Select Status</option>
            @foreach($statuses as $status)
            <option
                value="{{ $status }}"
                @selected(
                old(
                'asset_status',
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


    {{-- =========================================================
        ROW 6
        Remarks
    ========================================================== --}}
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

    {{-- =========================================================
        DOCUMENTS (NEW ASSET)
        New assets have no ID yet, so both selected files are submitted
        together with the Create Asset form.
    ========================================================== --}}
    @if(!$isEdit && (($canManageAssets ?? false) || ($canManageBills ?? false)))
        <div class="col-12">
            <div class="card border shadow-sm mt-2">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-1"><i class="bi bi-paperclip me-2"></i>Asset Ima

                        
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- =========================================================
        BUTTONS
    ========================================================== --}}
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
     PRESENT LOCATION "OTHER" LABEL BEHAVIOUR
========================================================= --}}

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const locationSelect = document.getElementById('present_location');
    const roomInput = document.getElementById('present_room_number');
    const roomLabel = document.querySelector('label[for="present_room_number"]');
    if (!locationSelect || !roomInput || !roomLabel) {
        return;
    }
    const isEdit = @json($isEdit);
    function updatePresentRoomField() {
        const isOther = locationSelect.value === 'Other';
        roomLabel.textContent = isOther
            ? 'Present Location Name & Number'
            : 'Present Room Number';
        roomInput.placeholder = isOther
            ? 'e.g. Server Room 2, Block A'
            : 'e.g. Room 205';
        // Existing assets remain controlled by the Asset Transfer workflow.
        roomInput.disabled = isEdit;
    }
    locationSelect.addEventListener('change', updatePresentRoomField);
    updatePresentRoomField();
});

    // File upload drag & drop + preview
    function handleDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        e.currentTarget.classList.add('drag-over');
    }

    function handleDragLeave(e) {
        e.preventDefault();
        e.stopPropagation();
        e.currentTarget.classList.remove('drag-over');
    }

    function handleDrop(e, fieldName) {
        e.preventDefault();
        e.stopPropagation();
        e.currentTarget.classList.remove('drag-over');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const input = document.getElementById(fieldName);
            input.files = files;
            updateFileDisplay(input, fieldName);
        }
    }

    function updateFileDisplay(input, fieldName) {
        const file = input.files[0];
        const statusEl = document.getElementById(fieldName + '_status');
        const previewEl = document.getElementById(fieldName + '_preview');
        const filenameEl = document.getElementById(fieldName + '_filename');
        const dropZone = document.querySelector(`[ondrop*="${fieldName}"]`);

        if (file) {
            // Update status badge
            if (statusEl) {
                statusEl.textContent = file.name;
                statusEl.className = 'badge bg-success';
            }

            // Show preview
            if (previewEl) previewEl.classList.remove('d-none');
            const filenameEl = document.getElementById(fieldName + '_filename');
            if (filenameEl) filenameEl.textContent = file.name + ' (' + formatFileSize(file.size) + ')';

            // Hide drag-drop content
            const dragDropContent = document.querySelector(`[ondrop*="${fieldName}"] .drag-drop-content`);
            if (dragDropContent) dragDropContent.style.display = 'none';
        } else {
            // Reset UI
            if (statusEl) {
                statusEl.textContent = 'Not Selected';
                statusEl.className = 'badge bg-secondary';
            }
            if (previewEl) previewEl.classList.add('d-none');
            const dragDropContent = document.querySelector(`[ondrop*="${fieldName}"] .drag-drop-content`);
            if (dragDropContent) dragDropContent.style.display = 'block';
        }
    }

    function clearFile(fieldName) {
        const input = document.getElementById(fieldName);
        input.value = '';
        updateFileDisplay(input, fieldName);
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Initialize drag-drop zones
    document.querySelectorAll('.drag-drop-zone').forEach(zone => {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            zone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            zone.addEventListener(eventName, () => zone.classList.add('drag-over'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            zone.addEventListener(eventName, () => zone.classList.remove('drag-over'), false);
        });

        zone.addEventListener('drop', (e) => {
            const fieldName = zone.getAttribute('ondrop').match(/'([^']+)'/)[1];
            handleDrop(e, fieldName);
        }, false);
    });

    // Also handle click on drop zone to trigger file input
    document.querySelectorAll('.drag-drop-zone').forEach(zone => {
        zone.addEventListener('click', (e) => {
            if (e.target === zone || zone.contains(e.target)) {
                const input = zone.querySelector('input[type="file"]');
                if (input) input.click();
            });
        });
    });

    // Update file display on change (for click-based selection)
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            const fieldName = this.id;
            updateFileDisplay(this, fieldName);
        });
    });
</script>
@endpush
