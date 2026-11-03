@extends('layouts.app')

@section('page-title', 'Assets')

@section('content')

<style>
    /* Assets table: stable column sizing so headers never overlap. */
    .assets-table {
        width: 100%;
        min-width: 1300px; /* Keeps all columns readable; wrapper scrolls on small screens. */
        table-layout: fixed;
    }

    .assets-table thead th {
        white-space: nowrap;
        vertical-align: middle;
        padding: 12px 10px;
        font-weight: 600;
        font-size: 0.85rem;
        /* NO overflow:hidden or text-overflow:ellipsis - headings must be fully visible */
    }

    .assets-table tbody td {
        vertical-align: middle;
        overflow-wrap: anywhere;
        word-break: break-word;
        padding: 12px 10px;
    }

    /* Column widths - using percentages for fluidity with min-width fallbacks */
    .assets-table th:nth-child(1),
    .assets-table td:nth-child(1) { /* Checkbox */
        width: 4%;
        min-width: 50px;
        text-align: center;
    }

    .assets-table th:nth-child(2),
    .assets-table td:nth-child(2) { /* S.No. */
        width: 4%;
        min-width: 65px;
    }

    .assets-table th:nth-child(3),
    .assets-table td:nth-child(3) { /* Asset */
        width: 22%;
        min-width: 240px;
    }

    .assets-table th:nth-child(4),
    .assets-table td:nth-child(4) { /* Make & Model */
        width: 16%;
        min-width: 180px;
    }

    .assets-table th:nth-child(5),
    .assets-table td:nth-child(5) { /* Department */
        width: 16%;
        min-width: 180px;
        white-space: nowrap;
    }

    .assets-table th:nth-child(6),
    .assets-table td:nth-child(6) { /* Qty Issued */
        width: 6%;
        min-width: 95px;
        text-align: center;
    }

    .assets-table th:nth-child(7),
    .assets-table td:nth-child(7) { /* Verified */
        width: 6%;
        min-width: 95px;
        text-align: center;
    }

    .assets-table th:nth-child(8),
    .assets-table td:nth-child(8) { /* Occupant */
        width: 16%;
        min-width: 180px;
    }

    .assets-table th:nth-child(9),
    .assets-table td:nth-child(9) { /* Barcode */
        width: 10%;
        min-width: 130px;
    }

    .assets-table th:nth-child(10),
    .assets-table td:nth-child(10) { /* Status */
        width: 10%;
        min-width: 120px;
        text-align: center;
    }

    .assets-table th:nth-child(11),
    .assets-table td:nth-child(11) { /* Actions */
        width: 4%;
        min-width: 50px;
        text-align: center;
    }

    @media (max-width: 1400px) {
        .assets-table { min-width: 1200px; }
    }
    @media (max-width: 1200px) {
        .assets-table { min-width: 1100px; }
    }
    @media (max-width: 992px) {
        .assets-table { min-width: 1000px; }
    }

    /* Compact controls; keep the table horizontally scrollable instead of clipping the page. */

    .assets-table-wrap {
        display: block;
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: auto;
        scrollbar-color: #64748b #e2e8f0;
        overscroll-behavior-x: contain;
        border-bottom: 8px solid #e2e8f0;
        box-sizing: border-box;
    }
    .assets-table-wrap > .assets-table {
        width: 100%;
        min-width: 1300px;
        max-width: none;
        table-layout: fixed;
    }
    .assets-table-wrap::-webkit-scrollbar { height: 14px; }
    .assets-table-wrap::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 8px; }
    .assets-table-wrap::-webkit-scrollbar-thumb { background: #64748b; border-radius: 8px; border: 2px solid #e2e8f0; }
    .assets-table-wrap::-webkit-scrollbar-thumb:hover { background: #475569; }

    .assets-table .badge { font-size: .75rem; padding: .35em .5em; }
    .assets-table .btn { padding: .25rem .4rem; font-size: .75rem; }
    .assets-table td:last-child, .assets-table th:last-child { text-align: center; }

    @media (max-width: 576px) {
        .assets-table thead th, .assets-table tbody td { padding: 9px 6px; }
        .assets-table { min-width: 900px; }
        .assets-table-wrap > .assets-table { min-width: 900px; }
    }

    /* Filter form improvements */
    .filter-form .form-control,
    .filter-form .form-select {
        height: calc(1.5em + 0.75rem + 2px);
    }

    @media (max-width: 767.98px) {
        .filter-form .row > [class*="col-"] {
            flex: 0 0 100%;
            max-width: 100%;
        }
        .filter-form .col-md-4 { order: 1; }
        .filter-form .col-md-2 { order: 2; }
        .filter-form .col-md-2:last-of-type { order: 3; } /* Filter button last */
    }

    /* Ensure page header stays within content bounds */
    .page-header {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3 page-header">
    <div class="min-w-0">
        <h2 class="mb-1">Assets</h2>
        <p class="text-muted mb-0">Departmental stock register</p>
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

        <div class="mb-4">
            <h6 class="mb-3"><i class="bi bi-funnel me-1"></i> Filters</h6>

            {{-- Basic Filters --}}
            <form class="row g-3 align-items-end filter-form" id="basicFiltersForm">
                {{-- Search --}}
                <div class="col-12 col-md-5 col-lg-4 col-xl-3">
                    <label class="form-label visually-hidden" for="searchInput">Search</label>
                    <input
                        id="searchInput"
                        name="search"
                        class="form-control"
                        placeholder="Search asset, barcode, serial..."
                        value="{{ request('search') }}">
                </div>

                {{-- Department --}}
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label visually-hidden" for="deptFilter">Department</label>
                    <select
                        id="deptFilter"
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
                <div class="col-6 col-md-2 col-lg-2">
                    <label class="form-label visually-hidden" for="catFilter">Category</label>
                    <select
                        id="catFilter"
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
                <div class="col-12 col-md-3 col-lg-2">
                    <label class="form-label visually-hidden" for="statusFilter">Status</label>
                    <select
                        id="statusFilter"
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
                <div class="col-12 col-md-auto">
                    <button type="submit" class="btn btn-dark w-100 w-md-auto">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        {{-- =====================================================
        Assets Table
        ====================================================== --}}

        <div class="table-responsive assets-table-wrap" id="assetsTableScroll">
            <table class="table table-hover align-middle assets-table">
                <thead>
                    <tr>
                        <th style="width: 4%; min-width: 50px; text-align: center;">
                            <input type="checkbox" id="selectAllAssets" class="form-check-input" title="Select all on this page">
                        </th>
                        <th style="width: 4%; min-width: 55px;">S.No.</th>
                        <th style="width: 20%; min-width: 220px;">Asset</th>
                        <th style="width: 16%; min-width: 180px;">Make & Model</th>
                        <th style="width: 16%; min-width: 180px;">Department</th>
                        <th style="width: 6%; min-width: 85px; text-align: center;">Qty Issued</th>
                        <th style="width: 6%; min-width: 85px; text-align: center;">Verified</th>
                        <th style="width: 16%; min-width: 180px;">Occupant</th>
                        <th style="width: 10%; min-width: 120px;">Barcode</th>
                        <th style="width: 10%; min-width: 110px; text-align: center;">Status</th>
                        <th style="width: 4%; min-width: 50px; text-align: center;"></th>
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
                        <td>{{ $assets->firstItem() + $loop->index }}</td>

                        {{-- Asset --}}
                        <td>
                            <a
                                class="fw-semibold text-decoration-none text-truncate d-block"
                                style="max-width: 100%;"
                                href="{{ route('assets.show', $asset) }}">
                                {{ $asset->item_name }}
                            </a>
                            <small class="text-muted text-truncate d-block" style="max-width: 100%;">{{ $asset->asset_code }}</small>
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
                        <td class="text-nowrap">{{ $asset->department?->name ?? '—' }}</td>

                        {{-- Issued Quantity --}}
                        <td class="text-center">{{ $asset->issued_quantity }}</td>

                        {{-- Verified Quantity --}}
                        <td class="text-center">{{ $asset->physically_verified_quantity }}</td>

                        {{-- Occupant --}}
                        <td>
                            @if($asset->occupant)
                            <span class="fw-semibold text-truncate d-block" style="max-width: 100%;">
                                {{ $asset->occupant->name }}
                            </span>
                            @if($asset->occupant->employee_code)
                            <small class="text-muted text-truncate d-block" style="max-width: 100%;">
                                {{ $asset->occupant->employee_code }}
                            </small>
                            @endif
                            @elseif($asset->department)
                            <span class="fw-semibold text-truncate d-block" style="max-width: 100%;">
                                {{ $asset->department->name }}
                            </span>
                            <small class="text-muted text-truncate d-block" style="max-width: 100%;">Department</small>
                            @else
                            —
                            @endif
                        </td>

                        {{-- Barcode --}}
                        <td>
                            <span class="text-truncate d-block" style="max-width: 100%;" title="{{ $asset->barcode_number ?? '—' }}">{{ $asset->barcode_number ?? '—' }}</span>
                        </td>

                        {{-- Status --}}
                        <td class="text-center">
                            <span class="badge text-bg-light">{{ $asset->asset_status }}</span>
                        </td>

                        {{-- Edit --}}
                        <td class="text-center">
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
                        <td colspan="11" class="text-center py-5 text-muted">No assets found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table
        </div>

        {{-- Pagination --}}
        {{ $assets->links() }}

    </div>
</div>

<script>
(function () {
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
            setupBulkActions();
            setupDragDrop();
            setupTableDragDrop();
        });
    } else {
        setupBulkActions();
        setupDragDrop();
        setupTableDragDrop();
    }
})();
</script>

@endsection