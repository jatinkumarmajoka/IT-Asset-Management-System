@extends('layouts.app')

@section('page-title', 'Assets')

@section('content')

<style>

    /* Assets table: stable column sizing so headers never overlap. */

    .assets-table {

        width: 100%;

        min-width: 1120px; /* Keeps all ten columns readable; wrapper scrolls on small screens. */

        table-layout: fixed;

    }

    .assets-table thead th {

        white-space: nowrap;

        vertical-align: middle;

        overflow: hidden;

        text-overflow: ellipsis;

        padding: 14px 12px;

    }

    .assets-table tbody td {

        vertical-align: middle;

        overflow-wrap: anywhere;

        word-break: break-word;

        padding: 14px 12px;

    }

    .assets-table th:nth-child(1),

    .assets-table td:nth-child(1) {

        width: 70px;

    }

    .assets-table th:nth-child(2),

    .assets-table td:nth-child(2) {

        width: 190px;

    }

    .assets-table th:nth-child(3),

    .assets-table td:nth-child(3) {

        width: 145px;

    }

    .assets-table th:nth-child(4),

    .assets-table td:nth-child(4) {

        width: 175px;

    }

    .assets-table th:nth-child(5),

    .assets-table td:nth-child(5) {

        width: 100px;

    }

    .assets-table th:nth-child(6),

    .assets-table td:nth-child(6) {

        width: 100px;

    }

    .assets-table th:nth-child(7),

    .assets-table td:nth-child(7) {

        width: 175px;

    }

    .assets-table th:nth-child(8),

    .assets-table td:nth-child(8) {

        width: 115px;

    }

    .assets-table th:nth-child(9),

    .assets-table td:nth-child(9) {

        width: 95px;

    }

    .assets-table th:nth-child(10),

    .assets-table td:nth-child(10) {

        width: 85px;

    }


    @media (max-width: 1200px) {

        .assets-table {

            min-width: 1120px;

        }

    }


    /* Compact controls; keep the table horizontally scrollable instead of clipping the page. */
    /* Always-visible horizontal scrollbar positioned ABOVE the table. */
    .assets-top-scroll {
        display: block;
        width: 100%;
        height: 18px;
        overflow-x: auto;
        overflow-y: hidden;
        margin: 0 0 8px;
        background: #e2e8f0;
        border-radius: 8px;
        scrollbar-width: auto;
        scrollbar-color: #64748b #e2e8f0;
        -webkit-overflow-scrolling: touch;
    }
    .assets-top-scroll-inner { height: 1px; }
    .assets-top-scroll::-webkit-scrollbar { height: 14px; }
    .assets-top-scroll::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 8px; }
    .assets-top-scroll::-webkit-scrollbar-thumb { background: #64748b; border-radius: 8px; border: 2px solid #e2e8f0; }

    .assets-table-wrap {
        display: block;
        width: 100%;
        max-width: 100%;
        overflow-x: scroll !important;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: auto;
        scrollbar-color: #64748b #e2e8f0;
        overscroll-behavior-x: contain;
        border-bottom: 8px solid #e2e8f0;
        box-sizing: border-box;
    }
    .assets-table-wrap > .assets-table {
        width: 1120px !important;
        min-width: 1120px !important;
        max-width: none !important;
        table-layout: fixed !important;
    }
    .assets-table-wrap::-webkit-scrollbar { height: 14px; }
    .assets-table-wrap::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 8px; }
    .assets-table-wrap::-webkit-scrollbar-thumb { background: #64748b; border-radius: 8px; border: 2px solid #e2e8f0; }
    .assets-table-wrap::-webkit-scrollbar-thumb:hover { background: #475569; }
    .assets-table .badge { font-size: .72rem; padding: .35em .5em; }
    .assets-table .btn { padding: .25rem .4rem; font-size: .75rem; }
    .assets-table td:last-child, .assets-table th:last-child { text-align: center; }
    @media (max-width: 576px) {
        .assets-table thead th, .assets-table tbody td { padding: 9px 8px; }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">

    <div>

        <h2>Assets</h2>

        <p class="text-muted mb-0">

            Departmental stock register

        </p>

    </div>

    <div class="d-flex gap-2 flex-wrap">
        <a
            href="{{ route('assets.create') }}"
            class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Add Asset
        </a>

        <a
            href="{{ route('assets.export') }}"
            class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i>
            Export Excel
        </a>

        <a
            href="{{ route('assets.template') }}"
            class="btn btn-outline-info">
            <i class="bi bi-download me-1"></i>
            Template
        </a>

        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-upload me-1"></i> Import
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <form method="POST" action="{{ route('assets.import') }}" enctype="multipart/form-data" class="p-3" style="width: 300px;">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Select Excel/CSV File</label>
                            <input type="file" name="file" class="form-control form-control-sm" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">Max 20MB. Use the template for correct format.</div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Import</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Bulk Action Bar (hidden by default, shown when checkboxes selected) -->
<div id="bulkActionBar" class="card shadow-sm table-card mb-3 d-none">
    <div class="card-body py-2">
        <form method="POST" action="{{ route('assets.bulk-action') }}" id="bulkActionForm">
            @csrf
            <input type="hidden" name="action" id="bulkActionInput">
            <div id="assetIdsContainer"></div>

            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <span class="fw-semibold" id="selectedCount">0</span> asset(s) selected
                </div>

                <div class="col-auto">
                    <select name="asset_status" id="bulkStatusSelect" class="form-select form-select-sm d-none" style="width: auto;">
                        <option value="">Select Status</option>
                        <option value="Active">Active</option>
                        <option value="Assigned">Assigned</option>
                        <option value="Unassigned">Unassigned</option>
                        <option value="Missing">Missing</option>
                        <option value="Damaged">Damaged</option>
                        <option value="Disposed">Disposed</option>
                        <option value="Return to Store">Return to Store</option>
                        <option value="Under Repair">Under Repair</option>
                        <option value="Retired">Retired</option>
                    </select>

                    <select name="present_occupant_id" id="bulkEmployeeSelect" class="form-select form-select-sm d-none" style="width: auto;">
                        <option value="">Select Employee</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->employee_code }}) - {{ $emp->department?->name }}</option>
                        @endforeach
                    </select>

                    <select name="present_sub_department_id" id="bulkSubDeptSelect" class="form-select form-select-sm d-none" style="width: auto;">
                        <option value="">Select Sub-Department (optional)</option>
                        @foreach($subDepartments as $sd)
                            <option value="{{ $sd->id }}">{{ $sd->name }} ({{ $sd->department?->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-auto">
                    <button type="button" class="btn btn-primary btn-sm" id="applyBulkAction" disabled>
                        <i class="bi bi-check-lg me-1"></i> Apply
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="clearBulkSelection">
                        <i class="bi bi-x-lg me-1"></i> Clear
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm table-card">

    <div class="card-body">

        {{-- =====================================================

                Filters

                ====================================================== --}}

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0"><i class="bi bi-funnel me-1"></i> Filters</h6>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="toggleAdvancedFilters" title="Toggle advanced filters">
                        <i class="bi bi-sliders me-1"></i> Advanced
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bookmark-star me-1"></i> Presets
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if($filterPresets->isNotEmpty())
                                @foreach($filterPresets as $preset)
                                    <li>
                                        <a class="dropdown-item load-preset" href="#" data-preset='{{ json_encode($preset->filters) }}' data-name="{{ $preset->name }}">
                                            <i class="bi bi-bookmark me-2"></i> {{ $preset->name }}
                                            @if($preset->is_default) <span class="badge bg-primary ms-1">Default</span> @endif
                                        </a>
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" id="saveCurrentPreset"><i class="bi bi-plus-circle me-2"></i> Save current as preset</a></li>
                                <li><a class="dropdown-item text-danger" href="#" id="clearAllFilters"><i class="bi bi-x-circle me-2"></i> Clear all filters</a></li>
                            @else
                                <li><a class="dropdown-item" href="#" id="saveFirstPreset"><i class="bi bi-plus-circle me-2"></i> Save current as preset</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Basic Filters --}}
            <form class="row g-2 mb-3" id="basicFiltersForm">

                {{-- Search --}}
                <div class="col-md-4">
                    <input
                        name="search"
                        class="form-control"
                        placeholder="Search asset, barcode, serial..."
                        value="{{ request('search') }}">
                </div>

                {{-- Department --}}
                <div class="col-md-2">
                    <select
                        name="department_id"
                        class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $d)
                        <option
                            value="{{ $d->id }}"
                            @selected(request('department_id') == $d->id)>
                            {{ $d->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Category --}}
                <div class="col-md-2">
                    <select
                        name="category_id"
                        class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $c)
                        <option
                            value="{{ $c->id }}"
                            @selected(request('category_id') == $c->id)>
                            {{ $c->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="col-md-2">
                    <select
                        name="asset_status"
                        class="form-select">
                        <option value="">All Status</option>
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
                        ] as $s)
                        <option
                            value="{{ $s }}"
                            @selected(request('asset_status') == $s)>
                            {{ $s }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Button --}}
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>

            </form>

            {{-- Advanced Filters (Collapsible) --}}
            <div class="card border-0 bg-light rounded p-3 mb-3" id="advancedFiltersPanel" style="display: none;">
                <form class="row g-2" id="advancedFiltersForm">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="department_id" value="{{ request('department_id') }}">
                    <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                    <input type="hidden" name="asset_status" value="{{ request('asset_status') }}">

                    {{-- Sub Department --}}
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Sub Department</label>
                        <select name="present_sub_department_id" class="form-select">
                            <option value="">All Sub Departments</option>
                            @foreach($subDepartments as $sd)
                            <option value="{{ $sd->id }}" @selected(request('present_sub_department_id') == $sd->id)>{{ $sd->name }} ({{ $sd->department?->name }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Condition --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Condition</label>
                        <select name="condition" class="form-select">
                            <option value="">All Conditions</option>
                            @foreach($conditions as $c)
                            <option value="{{ $c }}" @selected(request('condition') == $c)>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Employee --}}
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Employee</label>
                        <select name="employee_id" class="form-select">
                            <option value="">All Employees</option>
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->name }} ({{ $emp->employee_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date Range --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>

                    {{-- Quantity Range --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Qty Min</label>
                        <input type="number" name="qty_min" class="form-control" min="0" value="{{ request('qty_min') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Qty Max</label>
                        <input type="number" name="qty_max" class="form-control" min="0" value="{{ request('qty_max') }}">
                    </div>

                    {{-- Verified Qty Range --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Verified Min</label>
                        <input type="number" name="verified_qty_min" class="form-control" min="0" value="{{ request('verified_qty_min') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Verified Max</label>
                        <input type="number" name="verified_qty_max" class="form-control" min="0" value="{{ request('verified_qty_max') }}">
                    </div>

                    {{-- Quick Filters --}}
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Quick Filters</label>
                        <div class="d-flex flex-wrap gap-2">
                            <input type="checkbox" name="assigned_only" value="1" id="filter_assigned" @checked(request('assigned_only')) class="form-check-input" style="margin-top: 0.5rem;">
                            <label for="filter_assigned" class="form-check-label small ms-1">Assigned Only</label>

                            <input type="checkbox" name="unassigned_only" value="1" id="filter_unassigned" @checked(request('unassigned_only')) class="form-check-input" style="margin-top: 0.5rem;">
                            <label for="filter_unassigned" class="form-check-label small ms-1">Unassigned Only</label>

                            <input type="checkbox" name="has_image" value="1" id="filter_image" @checked(request('has_image')) class="form-check-input" style="margin-top: 0.5rem;">
                            <label for="filter_image" class="form-check-label small ms-1">Has Image</label>

                            <input type="checkbox" name="has_bill" value="1" id="filter_bill" @checked(request('has_bill')) class="form-check-input" style="margin-top: 0.5rem;">
                            <label for="filter_bill" class="form-check-label small ms-1">Has Bill</label>

                            <input type="checkbox" name="verified" value="1" id="filter_verified" @checked(request('verified')) class="form-check-input" style="margin-top: 0.5rem;">
                            <label for="filter_verified" class="form-check-label small ms-1">Verified</label>

                            <input type="checkbox" name="unverified" value="1" id="filter_unverified" @checked(request('unverified')) class="form-check-input" style="margin-top: 0.5rem;">
                            <label for="filter_unverified" class="form-check-label small ms-1">Unverified</label>
                        </div>
                    </div>

                    {{-- Submit & Clear --}}
                    <div class="col-md-12 mt-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel me-1"></i> Apply Advanced Filters
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="clearAdvancedFilters">
                                <i class="bi bi-x-lg me-1"></i> Clear Advanced
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        {{-- =====================================================

        Assets Table

        ====================================================== --}}

        <div class="assets-top-scroll" id="assetsTopScroll" aria-label="Scroll table horizontally">
            <div class="assets-top-scroll-inner" id="assetsTopScrollInner"></div>
        </div>
        <div class="table-responsive assets-table-wrap" id="assetsTableScroll">

            <table class="table table-hover align-middle assets-table">

                <thead>

                    <tr>

                        <th style="width: 50px; text-align: center;">
                            <input type="checkbox" id="selectAllAssets" class="form-check-input" title="Select all on this page">
                        </th>

                        <th>S.No.</th>

                        <th>

                            Asset

                        </th>

                        <th>

                            Make & Model

                        </th>

                        <th>

                            Department

                        </th>

                        <th>

                            Qty Issued

                        </th>

                        <th>

                            Verified

                        </th>

                        <th>

                            Occupant

                        </th>

                        <th>

                            Barcode

                        </th>

                        <th>

                            Status

                        </th>

                        <th>

                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($assets as $asset)

                    <tr data-asset-id="{{ $asset->id }}">

                        {{-- Checkbox --}}
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input asset-checkbox" value="{{ $asset->id }}" data-asset-code="{{ $asset->asset_code }}">
                        </td>

                        {{-- S.No. --}}

                        <td>

                            {{ $assets->firstItem() + $loop->index }}

                        </td>

                        {{-- Asset --}}

                        <td>

                            <a

                                class="fw-semibold text-decoration-none"

                                href="{{ route('assets.show', $asset) }}">

                                {{ $asset->item_name }}

                            </a>

                            <br>

                            <small class="text-muted">

                                {{ $asset->asset_code }}

                            </small>

                        </td>

                        {{-- Make & Model --}}

                        <td>

                            {{ trim(

                        ($asset->make ?? '') .

                        ' ' .

                        ($asset->model ?? '')

                        ) ?: '—' }}

                        </td>

                        {{-- Department --}}

                        <td>

                            {{ $asset->department?->name ?? '—' }}

                        </td>

                        {{-- Issued Quantity --}}

                        <td>

                            {{ $asset->issued_quantity }}

                        </td>

                        {{-- Verified Quantity --}}

                        <td>

                            {{ $asset->physically_verified_quantity }}

                        </td>

                        {{-- =================================================

                    Present Occupant

                    If employee exists:

                    show employee

                    If employee does not exist:

                    show department

                    IMPORTANT:

                    The department is NOT stored as an employee.

                    present_occupant_id remains NULL.

                    ================================================== --}}

                        <td>

                            @if($asset->occupant)

                            <span class="fw-semibold">

                                {{ $asset->occupant->name }}

                            </span>

                            @if($asset->occupant->employee_code)

                            <br>

                            <small class="text-muted">

                                {{ $asset->occupant->employee_code }}

                            </small>

                            @endif

                            @elseif($asset->department)

                            <span class="fw-semibold">

                                {{ $asset->department->name }}

                            </span>

                            <br>

                            <small class="text-muted">

                                Department

                            </small>

                            @else

                            —

                            @endif

                        </td>

                        {{-- Barcode --}}

                        <td>

                            <span class="text-truncate" title="{{ $asset->barcode_number ?? '—' }}">{{ $asset->barcode_number ?? '—' }}</span>

                        </td>

                        {{-- Status --}}

                        <td>

                            <span class="badge text-bg-light">

                                {{ $asset->asset_status }}

                            </span>

                        </td>

                        {{-- Edit --}}

                        <td>

                            <a

                                href="{{ route('assets.edit', $asset) }}"

                                class="btn btn-sm btn-outline-secondary"

                                title="Edit Asset">

                                <i class="bi bi-pencil"></i>

                            </a>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td

                            colspan="10"

                            class="text-center py-5 text-muted">

                            No assets found.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- Pagination --}}

        {{ $assets->links() }}

    </div>

</div>


<script>
(function () {
    function setupAssetsTopScrollbar() {
        const top = document.getElementById('assetsTopScroll');
        const inner = document.getElementById('assetsTopScrollInner');
        const tableWrap = document.getElementById('assetsTableScroll');
        const table = tableWrap ? tableWrap.querySelector('.assets-table') : null;
        if (!top || !inner || !tableWrap || !table) return;

        const updateWidth = () => {
            inner.style.width = Math.max(table.scrollWidth, tableWrap.clientWidth) + 'px';
            top.scrollLeft = tableWrap.scrollLeft;
        };
        let syncing = false;
        top.addEventListener('scroll', () => {
            if (syncing) return;
            syncing = true;
            tableWrap.scrollLeft = top.scrollLeft;
            syncing = false;
        }, { passive: true });
        tableWrap.addEventListener('scroll', () => {
            if (syncing) return;
            syncing = true;
            top.scrollLeft = tableWrap.scrollLeft;
            syncing = false;
        }, { passive: true });
        updateWidth();
        window.addEventListener('resize', updateWidth);
        if (window.ResizeObserver) new ResizeObserver(updateWidth).observe(table);
    }

    function setupBulkActions() {
        const selectAll = document.getElementById('selectAllAssets');
        const checkboxes = document.querySelectorAll('.asset-checkbox');
        const bulkActionBar = document.getElementById('bulkActionBar');
        const selectedCount = document.getElementById('selectedCount');
        const applyBtn = document.getElementById('applyBulkAction');
        const clearBtn = document.getElementById('clearBulkSelection');
        const bulkActionInput = document.getElementById('bulkActionInput');
        const assetIdsContainer = document.getElementById('assetIdsContainer');
        const bulkStatusSelect = document.getElementById('bulkStatusSelect');
        const bulkEmployeeSelect = document.getElementById('bulkEmployeeSelect');
        const bulkSubDeptSelect = document.getElementById('bulkSubDeptSelect');

        if (!selectAll || !bulkActionBar) return;

        const actionSelects = {
            status_change: [bulkStatusSelect],
            assign_employee: [bulkEmployeeSelect, bulkSubDeptSelect],
            delete: [],
            export_csv: [],
        };

        function updateBulkUI() {
            const checked = document.querySelectorAll('.asset-checkbox:checked');
            const count = checked.length;

            if (count > 0) {
                bulkActionBar.classList.remove('d-none');
                bulkActionBar.classList.add('d-block');
                applyBtn.disabled = false;
            } else {
                bulkActionBar.classList.add('d-none');
                bulkActionBar.classList.remove('d-block');
                applyBtn.disabled = true;
            }

            selectedCount.textContent = count;

            // Build hidden inputs for asset_ids[]
            assetIdsContainer.innerHTML = '';
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'asset_ids[]';
                input.value = cb.value;
                assetIdsContainer.appendChild(input);
            });

            selectAll.checked = count > 0 && count === checkboxes.length;
            selectAll.indeterminate = count > 0 && count < checkboxes.length;
        }

        function showSelectsForAction(action) {
            Object.values(actionSelects).flat().forEach(s => {
                if (s) s.classList.add('d-none');
            });
            if (actionSelects[action]) {
                actionSelects[action].forEach(s => {
                    if (s) s.classList.remove('d-none');
                });
            }
        }

        selectAll.addEventListener('change', () => {
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateBulkUI();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                updateBulkUI();
            });
        });

        clearBtn.addEventListener('click', () => {
            selectAll.checked = false;
            checkboxes.forEach(cb => cb.checked = false);
            updateBulkUI();
        });

        applyBtn.addEventListener('click', () => {
            const action = prompt('Select action:\n1. status_change - Change status\n2. delete - Delete assets\n3. assign_employee - Assign employee\n\nEnter action name:');
            if (!action) return;

            const validActions = ['status_change', 'delete', 'assign_employee'];
            if (!validActions.includes(action)) {
                alert('Invalid action: ' + action);
                return;
            }

            if (action === 'status_change') {
                const status = bulkStatusSelect.value;
                if (!status) {
                    alert('Please select a status');
                    return;
                }
            }
            if (action === 'assign_employee') {
                const emp = bulkEmployeeSelect.value;
                if (!emp) {
                    alert('Please select an employee');
                    return;
                }
            }

            bulkActionInput.value = action;
            showSelectsForAction(action);

            document.getElementById('bulkActionForm').submit();
        });

        updateBulkUI();
    }

    // Advanced Filters
    function setupAdvancedFilters() {
        const toggleBtn = document.getElementById('toggleAdvancedFilters');
        const panel = document.getElementById('advancedFiltersPanel');
        const clearBtn = document.getElementById('clearAdvancedFilters');
        const basicForm = document.getElementById('basicFiltersForm');
        const advancedForm = document.getElementById('advancedFiltersForm');
        const savePresetBtn = document.getElementById('saveCurrentPreset') || document.getElementById('saveFirstPreset');
        const clearAllBtn = document.getElementById('clearAllFilters');
        const loadPresetLinks = document.querySelectorAll('.load-preset');

        if (!toggleBtn || !panel) return;

        toggleBtn.addEventListener('click', () => {
            const isVisible = panel.style.display !== 'none';
            panel.style.display = isVisible ? 'none' : 'block';
            toggleBtn.innerHTML = isVisible
                ? '<i class="bi bi-sliders me-1"></i> Advanced'
                : '<i class="bi bi-sliders me-1"></i> Advanced <span class="badge bg-success ms-1">Open</span>';
        });

        clearBtn?.addEventListener('click', () => {
            advancedForm.querySelectorAll('input, select').forEach(el => {
                if (el.type === 'checkbox') el.checked = false;
                else if (el.type === 'hidden') return;
                else el.value = '';
            });
            advancedForm.submit();
        });

        // Save preset
        savePresetBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            const name = prompt('Enter preset name:');
            if (!name) return;

            const params = new URLSearchParams();
            [...basicForm.querySelectorAll('input, select'), ...advancedForm.querySelectorAll('input, select')].forEach(el => {
                if (el.type === 'checkbox') {
                    if (el.checked) params.append(el.name, el.value);
                } else if (el.type !== 'submit' && el.name) {
                    params.append(el.name, el.value);
                }
            });
            params.append('preset_name', name);
            params.append('module', 'assets');

            fetch('{{ route("filter-presets.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: params.toString()
            }).then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Preset saved!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to save'));
                }
            });
        });

        // Clear all filters
        clearAllBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            if (confirm('Clear all filters?')) {
                window.location.href = '{{ route("assets.index") }}';
            }
        });

        // Load preset
        loadPresetLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const filters = JSON.parse(link.dataset.preset);
                Object.entries(filters).forEach(([key, value]) => {
                    const basicEl = basicForm.querySelector(`[name="${key}"]`);
                    const advancedEl = advancedForm.querySelector(`[name="${key}"]`);
                    if (basicEl) basicEl.value = value;
                    if (advancedEl) {
                        if (advancedEl.type === 'checkbox') advancedEl.checked = value;
                        else advancedEl.value = value;
                    }
                });
                basicForm.submit();
            });
        });

        // Sync hidden fields
        const syncFields = ['search', 'department_id', 'category_id', 'asset_status'];
        basicForm.addEventListener('submit', () => {
            syncFields.forEach(name => {
                const basicEl = basicForm.querySelector(`[name="${name}"]`);
                const advancedEl = advancedForm.querySelector(`[name="${name}"]`);
                if (basicEl && advancedEl) advancedEl.value = basicEl.value;
            });
        });
    }

    // Drag & Drop for file uploads
    function setupDragDrop() {
        const dropZones = document.querySelectorAll('.drag-drop-zone');
        dropZones.forEach(zone => {
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
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles(files, zone);
            }, false);

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            function handleFiles(files, zone) {
                [...files].forEach(file => {
                    if (!file.type.match('image.*') && !file.type.match('application/pdf')) {
                        alert('Only images and PDFs allowed');
                        return;
                    }
                    if (file.size > 10 * 1024 * 1024) {
                        alert('File too large (max 10MB)');
                        return;
                    }

                    const input = zone.querySelector('input[type="file"]');
                    if (input) {
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        input.files = dt.files;
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            }
        }
    }

    // Table row drag-and-drop for reordering (if needed for custom ordering)
    function setupTableDragDrop() {
        const table = document.querySelector('.assets-table tbody');
        if (!table) return;

        let draggedRow = null;

        table.querySelectorAll('tr[data-asset-id]').forEach(row => {
            row.draggable = true;

            row.addEventListener('dragstart', (e) => {
                draggedRow = row;
                row.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', row.dataset.assetId);
            });

            row.addEventListener('dragend', () => {
                row.classList.remove('dragging');
                draggedRow = null;
            });

            row.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                const afterElement = getDragAfterElement(table, e.clientY);
                if (afterElement == null) {
                    table.appendChild(draggedRow);
                } else {
                    table.insertBefore(draggedRow, afterElement);
                }
            });

            row.addEventListener('drop', async (e) => {
                e.preventDefault();
                const targetId = row.dataset.assetId;
                const sourceId = draggedRow.dataset.assetId;
                if (sourceId !== targetId) {
                    // Could implement reorder API call here
                    console.log('Reorder:', sourceId, '->', targetId);
                }
            });
        });

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('tr[data-asset-id]:not(.dragging)')];
            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setupAssetsTopScrollbar();
            setupBulkActions();
            setupAdvancedFilters();
            setupDragDrop();
            setupTableDragDrop();
        });
    } else {
        setupAssetsTopScrollbar();
        setupBulkActions();
        setupAdvancedFilters();
        setupDragDrop();
        setupTableDragDrop();
    }
})();
</script>

@endsection

