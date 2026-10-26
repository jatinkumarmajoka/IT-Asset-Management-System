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

    <div class="d-flex gap-2">
        <a
            href="{{ route('assets.create') }}"
            class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Add Asset
        </a>
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

        <form class="row g-2 mb-3">

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

                    <option value="">

                        All Departments

                    </option>

                    @foreach($departments as $d)

                    <option

                        value="{{ $d->id }}"

                        @selected(

                        request('department_id') == $d->id

                        )

                        >

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

                    <option value="">

                        All Categories

                    </option>

                    @foreach($categories as $c)

                    <option

                        value="{{ $c->id }}"

                        @selected(

                        request('category_id') == $c->id

                        )

                        >

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

                    <option value="">

                        All Status

                    </option>

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

                        @selected(

                        request('asset_status') == $s

                        )>

                        {{ $s }}

                    </option>

                    @endforeach

                </select>

            </div>

            {{-- Filter Button --}}

            <div class="col-md-2">

                <button

                    type="submit"

                    class="btn btn-dark w-100">

                    Filter

                </button>

            </div>

        </form>

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
            const action = prompt('Select action:\n1. status_change - Change status\n2. delete - Delete assets\n3. assign_employee - Assign employee\n4. export_csv - Export to CSV\n\nEnter action name:');
            if (!action) return;

            const validActions = ['status_change', 'delete', 'assign_employee', 'export_csv'];
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

            if (action === 'export_csv') {
                const params = new URLSearchParams();
                const form = document.querySelector('form[method="GET"]');
                if (form) {
                    new FormData(form).forEach((v, k) => params.append(k, v));
                }
                params.append('export', 'csv');
                params.append('asset_ids', bulkAssetIdsInput.value);
                window.location.href = '{{ route("assets.export") }}?' + params.toString();
                return;
            }

            document.getElementById('bulkActionForm').submit();
        });

        updateBulkUI();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setupAssetsTopScrollbar();
            setupBulkActions();
        });
    } else {
        setupAssetsTopScrollbar();
        setupBulkActions();
    }
})();
</script>

@endsection

