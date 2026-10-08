@extends('layouts.app')

@section('page-title', 'Departments')

@section('content')

<style>
    /* Department index table */

    .departments-table-wrap {

        width: 100%;

        overflow-x: auto;

        overflow-y: hidden;

    }

    .departments-table {

        width: 100%;

        min-width: 0;

        table-layout: fixed;

        margin-bottom: 0 !important;

    }

    .departments-table th,

    .departments-table td {

        vertical-align: middle !important;

        padding: 10px 10px !important;

    }

    .departments-table thead th {

        white-space: nowrap !important;

        word-break: normal !important;

        overflow-wrap: normal !important;

        font-weight: 700;

    }

    .departments-table .col-name {

        width: 30%;

    }

    .departments-table .col-code {

        width: 16%;

    }

    .departments-table .col-assets {

        width: 9%;

        text-align: center;

    }

    .departments-table .col-employees {

        width: 11%;

        text-align: center;

    }

    .departments-table .col-status {

        width: 12%;

        text-align: center;

    }

    .departments-table .col-actions {

        width: 22%;

        text-align: center;

        white-space: nowrap !important;

    }

    .departments-table .department-name {

        font-weight: 600;

        line-height: 1.35;

        overflow-wrap: break-word;

        word-break: normal;

    }

    .departments-table .department-description {

        display: block;

        margin-top: 6px;

        color: #6c757d;

        font-size: .9rem;

        line-height: 1.45;

        overflow-wrap: break-word;

        word-break: normal;

    }

    .departments-table .code-badge {

        display: inline-block;

        max-width: 100%;

        padding: 6px 10px;

        border: 1px solid #dee2e6;

        border-radius: 8px;

        background: #f8f9fa;

        font-weight: 600;

        white-space: nowrap;

        overflow: hidden;

        text-overflow: ellipsis;

        vertical-align: middle;

    }

    .departments-table .count-badge {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        min-width: 38px;

        height: 34px;

        padding: 0 10px;

        border-radius: 8px;

        font-weight: 700;

    }

    .departments-table .actions {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        flex-wrap: nowrap;

        white-space: nowrap;

    }

    .departments-table .actions .btn {


        white-space: nowrap;
        font-size: 0.75rem;
        line-height: 1.2;
        padding: 5px 8px;
    }

    /* Compact status badges. */
    .departments-table .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        line-height: 1.2;
        padding: 5px 8px !important;
        white-space: nowrap;
    }

    .departments-table .status-badge i {
        margin-right: 4px !important;
    }

    @media (max-width: 1100px) {

        .departments-table {

            min-width: 920px;

        }

    }
</style>

{{-- Page Header --}}

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="mb-1">Departments</h2>

        <p class="text-muted mb-0">

            Manage departments and view allocated assets.

        </p>

    </div>

    <a

        href="{{ route('departments.create') }}"

        class="btn btn-primary">

        <i class="bi bi-plus-lg me-1"></i>

        Add Department

    </a>

</div>

{{-- Departments Card --}}

<div class="card shadow-sm border-0">

    <div class="card-body">

        {{-- Search --}}

        <form

            method="GET"

            action="{{ route('departments.index') }}"

            class="mb-4">

            <div class="input-group">

                <span class="input-group-text bg-white">

                    <i class="bi bi-search"></i>

                </span>

                <input

                    type="text"

                    name="search"

                    class="form-control"

                    placeholder="Search department name or code..."

                    value="{{ request('search') }}">

                <button

                    type="submit"

                    class="btn btn-dark">

                    Search

                </button>

                @if (request('search'))

                <a

                    href="{{ route('departments.index') }}"

                    class="btn btn-outline-secondary">

                    Clear

                </a>

                @endif

            </div>

        </form>

        {{-- Departments Table --}}

        <div class="departments-table-wrap">

            <table class="table table-hover align-middle departments-table">

                <thead class="table-light">

                    <tr>

                        <th class="col-name">Name</th>

                        <th class="col-code">Code</th>

                        <th class="col-assets">Assets</th>

                        <th class="col-employees">Employees</th>

                        <th class="col-status">Status</th>

                        <th class="col-actions">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($departments as $department)

                    <tr>

                        {{-- Name --}}

                        <td class="col-name">

                            <div class="department-name">

                                {{ $department->name }}

                            </div>

                            @if ($department->description)

                            <span class="department-description">

                                {{ \Illuminate\Support\Str::limit($department->description, 60) }}

                            </span>

                            @endif

                        </td>

                        {{-- Code --}}

                        <td class="col-code">

                            <span

                                class="code-badge"

                                title="{{ $department->code }}">

                                {{ $department->code }}

                            </span>

                        </td>

                        {{-- Assets --}}

                        <td class="col-assets">

                            <span class="count-badge bg-primary text-white">

                                {{ $department->assets_count }}

                            </span>

                        </td>

                        {{-- Employees --}}

                        <td class="col-employees">

                            <span class="count-badge bg-secondary text-white">

                                {{ $department->employees_count }}

                            </span>

                        </td>

                        {{-- Status --}}

                        <td class="col-status">

                            @if ($department->status === 'active')

                            <span class="badge text-bg-success status-badge">

                                <i class="bi bi-check-circle me-1"></i>

                                Active

                            </span>

                            @else

                            <span class="badge text-bg-secondary status-badge">

                                <i class="bi bi-dash-circle me-1"></i>

                                Inactive

                            </span>

                            @endif

                        </td>

                        {{-- Actions --}}

                        <td class="col-actions">

                            <div class="actions">

                                <button

                                    type="button"

                                    class="btn btn-outline-primary view-assets-btn"

                                    data-assets-url="{{ route('departments.assets', $department) }}"

                                    data-department-name="{{ $department->name }}">

                                    <i class="bi bi-box-seam me-1"></i>

                                    View Assets

                                </button>

                                <a

                                    href="{{ route('departments.edit', $department) }}"

                                    class="btn btn-outline-secondary">

                                    <i class="bi bi-pencil me-1"></i>

                                    Edit

                                </a>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="6" class="text-center text-muted py-5">

                            <i class="bi bi-building fs-1"></i>

                            <h6 class="mt-3">

                                No departments found

                            </h6>

                            <p class="mb-0">

                                @if (request('search'))

                                No departments match your search.

                                @else

                                No departments have been added yet.

                                @endif

                            </p>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- Pagination --}}

        @if ($departments->hasPages())

        <div class="mt-4">

            {{ $departments->links() }}

        </div>

        @endif

    </div>

</div>

{{-- ========================================================= --}}

{{-- Department Assets Modal --}}

{{-- ========================================================= --}}

<div

    class="modal fade"

    id="departmentAssetsModal"

    tabindex="-1"

    aria-labelledby="departmentAssetsModalLabel"

    aria-hidden="true">

    <div

        class="modal-dialog modal-xl modal-dialog-scrollable">

        <div class="modal-content">

            {{-- Modal Header --}}

            <div class="modal-header">

                <div>

                    <h5

                        class="modal-title"

                        id="departmentAssetsModalLabel">

                        Department Assets

                    </h5>

                    <small

                        class="text-muted"

                        id="departmentAssetsModalSubtitle">

                        Loading...

                    </small>

                </div>

                <button

                    type="button"

                    class="btn-close"

                    data-bs-dismiss="modal"

                    aria-label="Close"></button>

            </div>

            {{-- Modal Body --}}

            <div class="modal-body">

                {{-- Loading --}}

                <div

                    id="assetsLoading"

                    class="text-center py-5">

                    <div

                        class="spinner-border text-primary mb-3"

                        role="status">

                        <span class="visually-hidden">

                            Loading...

                        </span>

                    </div>

                    <p class="text-muted mb-0">

                        Loading department assets...

                    </p>

                </div>

                {{-- Error --}}

                <div

                    id="assetsError"

                    class="alert alert-danger d-none"

                    role="alert">

                    <i class="bi bi-exclamation-triangle me-1"></i>

                    <span id="assetsErrorMessage">

                        Unable to load assets.

                    </span>

                </div>

                {{-- Empty --}}

                <div

                    id="assetsEmpty"

                    class="text-center text-muted py-5 d-none">

                    <i class="bi bi-box-seam fs-1"></i>

                    <h6 class="mt-3">

                        No assets allocated

                    </h6>

                    <p class="mb-0">

                        This department currently has no allocated assets.

                    </p>

                </div>

                {{-- Assets Table --}}

                <div

                    id="assetsTableContainer"

                    class="table-responsive d-none">

                    <table

                        class="table table-bordered table-hover align-middle">

                        <thead class="table-light">

                            <tr>

                                <th>Asset Code</th>

                                <th>Item</th>

                                <th>Make</th>

                                <th>Model</th>

                                <th class="text-center">

                                    Issued Qty

                                </th>

                                <th class="text-center">

                                    Verified Qty

                                </th>

                                <th>

                                    Present Occupant

                                </th>

                                <th>

                                    Barcode

                                </th>

                                <th>

                                    Status

                                </th>

                            </tr>

                        </thead>

                        <tbody id="assetsTableBody"></tbody>

                    </table>

                </div>

            </div>

            {{-- Modal Footer --}}

            <div class="modal-footer">

                <button

                    type="button"

                    class="btn btn-secondary"

                    data-bs-dismiss="modal">

                    Close

                </button>

            </div>

        </div>

    </div>

</div>

{{-- ========================================================= --}}

{{-- Department Assets JavaScript --}}

{{-- ========================================================= --}}

<script>
    console.log('Department JavaScript loaded');

    document.addEventListener('DOMContentLoaded', function() {

                console.log('Department DOM loaded');

                const modalElement = document.getElementById(

                    'departmentAssetsModal'

                );

                const modal = new bootstrap.Modal(

                    modalElement

                );

                const loading = document.getElementById(

                    'assetsLoading'

                );

                const error = document.getElementById(

                    'assetsError'

                );

                const errorMessage = document.getElementById(

                    'assetsErrorMessage'

                );

                const empty = document.getElementById(

                    'assetsEmpty'

                );

                const tableContainer = document.getElementById(

                    'assetsTableContainer'

                );

                const tableBody = document.getElementById(

                    'assetsTableBody'

                );

                const modalTitle = document.getElementById(

                    'departmentAssetsModalLabel'

                );

                const modalSubtitle = document.getElementById(

                    'departmentAssetsModalSubtitle'

                );

                function escapeHtml(value) {

                    if (

                        value === null ||

                        value === undefined

                    ) {

                        return '';

                    }

                    return String(value)

                        .replace(/&/g, '&amp;')

                        .replace(/</g, '&lt;')

                        .replace(/>/g, '&gt;')

                        .replace(/"/g, '&quot;')

                        .replace(/'/g, '&#039;');

                }

                document

                    .querySelectorAll('.view-assets-btn')

                    .forEach(function(button) {

                            button.addEventListener(

                                    'click',

                                    async function() {

                                            console.log(

                                                'View Assets clicked'

                                            );

                                            const assetsUrl =

                                                this.dataset.assetsUrl;

                                            const departmentName =

                                                this.dataset.departmentName;

                                            console.log(

                                                'Assets URL:',

                                                assetsUrl

                                            );

                                            /*

                                            |--------------------------------------------------------------------------

                                            | Reset

                                            |--------------------------------------------------------------------------

                                            */

                                            loading.classList.remove(

                                                'd-none'

                                            );

                                            error.classList.add(

                                                'd-none'

                                            );

                                            empty.classList.add(

                                                'd-none'

                                            );

                                            tableContainer.classList.add(

                                                'd-none'

                                            );

                                            tableBody.innerHTML = '';

                                            /*

                                            |--------------------------------------------------------------------------

                                            | Modal Title

                                            |--------------------------------------------------------------------------

                                            */

                                            modalTitle.textContent =

                                                departmentName + ' - Assets';

                                            modalSubtitle.textContent =

                                                'Loading allocated assets...';

                                            /*

                                            |--------------------------------------------------------------------------

                                            | Open Modal

                                            |--------------------------------------------------------------------------

                                            */

                                            modal.show();

                                            /*

                                            |--------------------------------------------------------------------------

                                            | Fetch Assets

                                            |--------------------------------------------------------------------------

                                            */

                                            try {

                                                const response =

                                                    await fetch(

                                                        assetsUrl,

                                                        {

                                                            method: 'GET',

                                                            headers: {

                                                                'Accept':

                                                                    'application/json',

                                                                'X-Requested-With':

                                                                    'XMLHttpRequest'

                                                            }

                                                        }

                                                    );

                                                console.log(

                                                    'Response status:',

                                                    response.status

                                                );

                                                if (!response.ok) {

                                                    throw new Error(

                                                        'HTTP ' +

                                                        response.status

                                                    );

                                                }

                                                const data =

                                                    await response.json();

                                                console.log(

                                                    'API response:',

                                                    data

                                                );

                                                loading.classList.add(

                                                    'd-none'

                                                );

                                                /*

                                                |--------------------------------------------------------------------------

                                                | No Assets

                                                |--------------------------------------------------------------------------

                                                */

                                                if (

                                                    !data.assets ||

                                                    data.assets.length === 0

                                                ) {

                                                    empty.classList.remove(

                                                        'd-none'

                                                    );

                                                    modalSubtitle.textContent =

                                                        'No assets allocated to this department.';

                                                    return;

                                                }

                                                /*

                                                |--------------------------------------------------------------------------

                                                | Render Assets

                                                |--------------------------------------------------------------------------

                                                */

                                                modalSubtitle.textContent =

                                                    data.assets.length +

                                                    ' asset(s) allocated';

                                                tableBody.innerHTML =

                                                    data.assets

                                                    .map(function(asset) {

                                                            return\ `

                                                <tr>

                                                    <td>

                                                        <strong>

                                                            ${escapeHtml(

                                                                asset.asset_code

                                                            )}

                                                        </strong>

                                                    </td>

                                                    <td>

                                                        ${escapeHtml(

                                                            asset.item_name

                                                        )}

                                                    </td>

                                                    <td>

                                                        ${escapeHtml(

                                                            asset.make || '-'

                                                        )}

                                                    </td>

                                                    <td>

                                                        ${escapeHtml(

                                                            asset.model || '-'

                                                        )}

                                                    </td>

                                                    <td>

                                                        ${escapeHtml(

                                                            asset.issued_quantity ?? 0

                                                        )}

                                                    </td>

                                                    <td>

                                                        ${escapeHtml(

                                                            asset.physically_verified_quantity ?? 0

                                                        )}

                                                    </td>

                                                    <td>

                                                        ${escapeHtml(

                                                            asset.occupant ||

                                                            'Unassigned'

                                                        )}

                                                    </td>

                                                    <td>

                                                        ${escapeHtml(

                                                            asset.barcode_number ||

                                                            '-'

                                                        )}

                                                    </td>

                                                    <td>

                                                        ${escapeHtml(

                                                            asset.status ||

                                                            '-'

                                                        )}

                                                    </td>

                                                </tr>

                                            \`;

                                        })

                                        .join('');

                                tableContainer.classList.remove(

                                    'd-none'

                                );

                            }

                            catch (exception) {

                                console.error(

                                    'Department assets error:',

                                    exception

                                );

                                loading.classList.add(

                                    'd-none'

                                );

                                error.classList.remove(

                                    'd-none'

                                );

                                errorMessage.textContent =

                                    'Unable to load department assets.';

                            }

                        }

                    );

                });

        });
</script>

@endsection