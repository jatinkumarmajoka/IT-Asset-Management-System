<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkAssetActionRequest;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Imports\AssetsImport;
use App\Models\Asset;
use App\Models\AssetDocument;
use App\Models\Category;
use App\Models\Department;
use App\Models\Employee;
use App\Models\SubDepartment;
use App\Services\AssetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class AssetController extends Controller
{
    public function __construct(
        private AssetService $assetService
    ) {}

    /**
     * Display all assets.
     */
    public function index(Request $request)
    {
        $query = Asset::query()->with([
            'department',
            'category',
            'occupant',
            'presentSubDepartment',
        ]);

        if ($request->filled('search')) {
            $term = trim((string) $request->input('search'));

            $query->where(function ($q) use ($term) {
                $q->where('asset_code', 'like', "%{$term}%")
                    ->orWhere('item_name', 'like', "%{$term}%")
                    ->orWhere('make', 'like', "%{$term}%")
                    ->orWhere('model', 'like', "%{$term}%")
                    ->orWhere('barcode_number', 'like', "%{$term}%")
                    ->orWhere('asset_serial_number', 'like', "%{$term}%")
                    ->orWhere('present_occupant_name', 'like', "%{$term}%");
            });
        }

        $query->when(
            $request->filled('department_id'),
            fn ($q) => $q->where(
                'department_id',
                $request->integer('department_id')
            )
        );

        $query->when(
            $request->filled('category_id'),
            fn ($q) => $q->where(
                'category_id',
                $request->integer('category_id')
            )
        );

        $query->when(
            $request->filled('asset_status'),
            fn ($q) => $q->where(
                'asset_status',
                $request->input('asset_status')
            )
        );

        $query->when(
            $request->filled('present_sub_department_id'),
            fn ($q) => $q->where(
                'present_sub_department_id',
                $request->integer('present_sub_department_id')
            )
        );

        // Advanced filters
        $query->when(
            $request->filled('condition'),
            fn ($q) => $q->where('condition', $request->input('condition'))
        );

        $query->when(
            $request->filled('assigned_only'),
            fn ($q) => $q->whereNotNull('present_occupant_id')
        );

        $query->when(
            $request->filled('unassigned_only'),
            fn ($q) => $q->whereNull('present_occupant_id')
        );

        $query->when(
            $request->filled('has_image'),
            fn ($q) => $q->whereHas('documents', fn ($dq) => $dq->where('document_type', AssetDocument::TYPE_ASSET_IMAGE))
        );

        $query->when(
            $request->filled('has_bill'),
            fn ($q) => $q->whereHas('documents', fn ($dq) => $dq->where('document_type', AssetDocument::TYPE_BILL_INVOICE))
        );

        $query->when(
            $request->filled('verified'),
            fn ($q) => $q->whereRaw('physically_verified_quantity = issued_quantity')
        );

        $query->when(
            $request->filled('unverified'),
            fn ($q) => $q->whereRaw('physically_verified_quantity < issued_quantity')
        );

        $query->when(
            $request->filled('date_from'),
            fn ($q) => $q->whereDate('date_of_issue', '>=', $request->input('date_from'))
        );

        $query->when(
            $request->filled('date_to'),
            fn ($q) => $q->whereDate('date_of_issue', '<=', $request->input('date_to'))
        );

        $query->when(
            $request->filled('qty_min'),
            fn ($q) => $q->where('issued_quantity', '>=', $request->integer('qty_min'))
        );

        $query->when(
            $request->filled('qty_max'),
            fn ($q) => $q->where('issued_quantity', '<=', $request->integer('qty_max'))
        );

        $query->when(
            $request->filled('verified_qty_min'),
            fn ($q) => $q->where('physically_verified_quantity', '>=', $request->integer('verified_qty_min'))
        );

        $query->when(
            $request->filled('verified_qty_max'),
            fn ($q) => $q->where('physically_verified_quantity', '<=', $request->integer('verified_qty_max'))
        );

        $query->when(
            $request->filled('employee_id'),
            fn ($q) => $q->where('present_occupant_id', $request->integer('employee_id'))
        );

        $assets = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $departments = Department::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $categories = Category::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $employees = Employee::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $subDepartments = SubDepartment::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $conditions = ['New', 'Good', 'Fair', 'Damaged', 'Critical'];

        // Get filter presets for current user
        $filterPresets = $request->user()
            ? $request->user()->filterPresets()->where('module', 'assets')->get()
            : collect();

        return view('assets.index', compact(
            'assets',
            'departments',
            'categories',
            'employees',
            'subDepartments',
            'conditions',
            'filterPresets'
        ));
    }

    /**
     * Show create asset form.
     */
    public function create()
    {
        $user = request()->user();

        $canManageAssets = $user
            && ($user->isSuperAdmin() || $user->canManageModule('assets'));

        $canManageBills = $user
            && ($user->isSuperAdmin() || $user->canManageModule('bills'));

        return view('assets.create', [
            'departments' => Department::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),

            'categories' => Category::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),

            'employees' => Employee::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),

            'subDepartments' => SubDepartment::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),

            'canManageAssets' => $canManageAssets,
            'canManageBills' => $canManageBills,
        ]);
    }

    /**
     * Store a new asset.
     */
    public function store(StoreAssetRequest $request)
    {
        // StoreAssetRequest validates both optional uploads before this method runs.
        // These permission checks prevent uploads through a forged form submission.
        $user = $request->user();

        abort_unless($user, 401);

        if ($request->hasFile('asset_image')) {
            abort_unless(
                $user->isSuperAdmin() || $user->canManageModule('assets'),
                403,
                'You do not have permission to upload an asset image.'
            );
        }

        if ($request->hasFile('bill_invoice')) {
            abort_unless(
                $user->isSuperAdmin() || $user->canManageModule('bills'),
                403,
                'You do not have permission to upload a bill or invoice.'
            );
        }

        $validated = $request->validated();

        // Uploaded files are document records, not columns on the assets table.
        // Never pass UploadedFile objects into AssetService::create().
        unset($validated['asset_image'], $validated['bill_invoice']);

        /*
         * AssetService creates the asset and records its existing history.
         * Look it up by the unique asset code so this also works if the
         * service does not return the created model.
         */
        $this->assetService->create($validated);

        $asset = Asset::query()
            ->where('asset_code', $validated['asset_code'])
            ->firstOrFail();

        $uploadedPaths = [];

        try {
            foreach ([
                'asset_image' => AssetDocument::TYPE_ASSET_IMAGE,
                'bill_invoice' => AssetDocument::TYPE_BILL_INVOICE,
            ] as $inputName => $documentType) {
                if (! $request->hasFile($inputName)) {
                    continue;
                }

                $file = $request->file($inputName);
                $extension = strtolower($file->getClientOriginalExtension());
                $filename = Str::uuid()->toString().'.'.$extension;

                $storedPath = $file->storeAs(
                    'assets/'.$asset->id,
                    $filename,
                    'private'
                );

                if (! $storedPath) {
                    throw new \RuntimeException(
                        'The uploaded document could not be stored.'
                    );
                }

                $uploadedPaths[] = $storedPath;

                $asset->documents()->create([
                    'document_type' => $documentType,
                    'original_name' => $file->getClientOriginalName(),
                    'stored_path' => $storedPath,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => $user->getAuthIdentifier(),
                    'locked_at' => $documentType === AssetDocument::TYPE_BILL_INVOICE
                        ? now()
                        : null,
                ]);
            }
        } catch (\Throwable $exception) {
            /*
             * Remove newly stored files if saving their document records fails.
             */
            foreach ($uploadedPaths as $path) {
                Storage::disk('private')->delete($path);
            }

            throw $exception;
        }

        return redirect()
            ->route('assets.show', $asset)
            ->with('success', 'Asset created successfully.');
    }

    /**
     * Display a specific asset.
     */
    public function show(Asset $asset)
    {
        $asset->load([
            'department',
            'category',
            'occupant',
            'presentSubDepartment',
            'documents',
            'occupancyHistories' => function ($query) {
                $query->with([
                    'previousDepartment',
                    'previousSubDepartment',
                    'presentDepartment',
                    'presentSubDepartment',
                    'recorder',
                ])->latest('effective_at')->latest('id');
            },
            'transfers' => function ($query) {
                $query->with([
                    'fromDepartment',
                    'toDepartment',
                    'fromOccupant',
                    'toOccupant',
                    'initiatedBy',
                    'approvedBy',
                    'completedBy',
                ])->latest('transfer_date')->latest('id');
            },
        ]);

        /*
         * Preserve the existing transfer-based previous occupant.
         */
        $lastCompletedTransfer = $asset->transfers
            ->filter(fn ($transfer) => $transfer->status === 'completed')
            ->first();

        $previousOccupant = null;

        if ($lastCompletedTransfer) {
            $previousOccupant =
                $lastCompletedTransfer->fromOccupant?->name
                ?? $lastCompletedTransfer->fromDepartment?->name;
        }

        return view('assets.show', compact(
            'asset',
            'previousOccupant'
        ));
    }

    /**
     * Show the edit asset form.
     */
    public function edit(Asset $asset)
    {
        $asset->load([
            'department',
            'category',
            'occupant',
            'presentSubDepartment',
            'documents',
            'transfers' => function ($query) {
                $query->where('status', 'completed')
                    ->with([
                        'fromDepartment',
                        'fromOccupant',
                    ])
                    ->latest('transfer_date')
                    ->latest('id');
            },
        ]);

        $lastCompletedTransfer = $asset->transfers->first();

        $previousOccupant = null;

        if ($lastCompletedTransfer) {
            $previousOccupant =
                $lastCompletedTransfer->fromOccupant?->name
                ?? $lastCompletedTransfer->fromDepartment?->name;
        }

        $user = request()->user();

        $canManageAssets = $user
            && ($user->isSuperAdmin() || $user->canManageModule('assets'));

        $canManageBills = $user
            && ($user->isSuperAdmin() || $user->canManageModule('bills'));

        return view('assets.edit', [
            'asset' => $asset,
            'previousOccupant' => $previousOccupant,

            'departments' => Department::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),

            'categories' => Category::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),

            'employees' => Employee::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),

            'subDepartments' => SubDepartment::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),

            'canManageAssets' => $canManageAssets,
            'canManageBills' => $canManageBills,
        ]);
    }

    /**
     * Update an existing asset.
     */
    public function update(
        UpdateAssetRequest $request,
        Asset $asset
    ) {
        $this->assetService->update(
            $asset,
            $request->validated()
        );

        return redirect()
            ->route('assets.show', $asset)
            ->with('success', 'Asset updated successfully.');
    }

    /**
     * Delete an asset.
     */
    public function destroy(Asset $asset)
    {
        $asset->history()->create([
            'action' => 'deleted',
            'old_value' => json_encode($asset->toArray()),
            'performed_by' => auth()->user()?->getAuthIdentifier(),
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);

        $asset->delete();

        return redirect()
            ->route('assets.index')
            ->with('success', 'Asset deleted successfully.');
    }

    /**
     * Perform bulk actions on selected assets.
     */
    public function bulkAction(BulkAssetActionRequest $request)
    {
        $action = $request->input('action');
        $assetIds = $request->input('asset_ids');
        $user = $request->user();

        $assets = Asset::whereIn('id', $assetIds)
            ->with(['department', 'occupant'])
            ->get();

        if ($assets->count() !== count($assetIds)) {
            return back()->withErrors([
                'asset_ids' => 'One or more selected assets could not be found.',
            ]);
        }

        $processed = 0;
        $failed = 0;
        $errors = [];

        try {
            DB::transaction(function () use (
                $action,
                $assets,
                $request,
                $user,
                &$processed,
                &$failed,
                &$errors
            ) {
                foreach ($assets as $asset) {
                    try {
                        switch ($action) {
                            case 'status_change':
                                $this->bulkStatusChange($asset, $request, $user);
                                break;

                            case 'delete':
                                $this->bulkDelete($asset, $user);
                                break;

                            case 'assign_employee':
                                $this->bulkAssignEmployee($asset, $request, $user);
                                break;

                            default:
                                throw new \InvalidArgumentException("Unknown action: {$action}");
                        }
                        $processed++;
                    } catch (\Throwable $e) {
                        $failed++;
                        $errors[] = "Asset {$asset->asset_code}: {$e->getMessage()}";
                        \Log::error('Bulk action failed', [
                            'asset_id' => $asset->id,
                            'asset_code' => $asset->asset_code,
                            'action' => $action,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            });
        } catch (\Throwable $e) {
            return back()->withErrors([
                'bulk_action' => 'Bulk action failed: '.$e->getMessage(),
            ]);
        }

        // Build response message
        $actionLabels = [
            'status_change' => 'Status updated',
            'delete' => 'Deleted',
            'assign_employee' => 'Employee assigned',
        ];

        $label = $actionLabels[$action] ?? 'Processed';

        if ($failed === 0) {
            return redirect()
                ->route('assets.index')
                ->with('success', "{$label} {$processed} asset(s) successfully.");
        }

        $message = "{$label} {$processed} asset(s). {$failed} failed.";
        if (! empty($errors)) {
            $message .= ' Errors: '.implode('; ', array_slice($errors, 0, 3));
            if (count($errors) > 3) {
                $message .= ' (and '.(count($errors) - 3).' more)';
            }
        }

        return redirect()
            ->route('assets.index')
            ->with('warning', $message);
    }

    /**
     * Bulk status change.
     */
    private function bulkStatusChange(Asset $asset, BulkAssetActionRequest $request, $user): void
    {
        $newStatus = $request->input('asset_status');

        if ($asset->asset_status === $newStatus) {
            return; // No change needed
        }

        $oldStatus = $asset->asset_status;

        $asset->update([
            'asset_status' => $newStatus,
            'updated_by' => $user->id,
        ]);

        // Create history record
        $asset->history()->create([
            'action' => 'status_changed',
            'old_value' => json_encode(['asset_status' => $oldStatus]),
            'new_value' => json_encode(['asset_status' => $newStatus]),
            'performed_by' => $user->id,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }

    /**
     * Bulk delete (soft delete).
     */
    private function bulkDelete(Asset $asset, $user): void
    {
        $asset->history()->create([
            'action' => 'deleted',
            'old_value' => json_encode($asset->toArray()),
            'performed_by' => $user->id,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);

        $asset->delete();
    }

    /**
     * Bulk assign employee.
     */
    private function bulkAssignEmployee(Asset $asset, BulkAssetActionRequest $request, $user): void
    {
        $employeeId = $request->input('present_occupant_id');
        $subDeptId = $request->input('present_sub_department_id');

        $employee = $employeeId ? Employee::find($employeeId) : null;
        $subDept = $subDeptId ? SubDepartment::find($subDeptId) : null;

        $asset->update([
            'present_occupant_id' => $employeeId,
            'present_occupant_name' => $employee?->name,
            'present_sub_department_id' => $subDeptId,
            'updated_by' => $user->id,
        ]);

        // Create history record
        $asset->history()->create([
            'action' => 'occupant_changed',
            'old_value' => json_encode([
                'present_occupant_id' => $asset->getOriginal('present_occupant_id'),
                'present_occupant_name' => $asset->getOriginal('present_occupant_name'),
                'present_sub_department_id' => $asset->getOriginal('present_sub_department_id'),
            ]),
            'new_value' => json_encode([
                'present_occupant_id' => $employeeId,
                'present_occupant_name' => $employee?->name,
                'present_sub_department_id' => $subDeptId,
            ]),
            'performed_by' => $user->id,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }

    /**
     * Store a filter preset for the current user.
     */
    public function storeFilterPreset(Request $request)
    {
        $request->validate([
            'preset_name' => ['required', 'string', 'max:100'],
            'module' => ['required', 'string', 'max:50'],
            'filters' => ['nullable', 'array'],
        ]);

        // If setting as default, unset other defaults
        $request->user()->filterPresets()
            ->where('module', $request->input('module'))
            ->where('is_default', true)
            ->update(['is_default' => false]);

        $preset = $request->user()->filterPresets()->create([
            'module' => $request->input('module'),
            'name' => $request->input('preset_name'),
            'filters' => $request->input('filters', []),
            'is_default' => true,
        ]);

        return response()->json([
            'success' => true,
            'preset' => $preset,
        ]);
    }

    /**
     * Export assets to Excel.
     */
    public function export(Request $request)
    {
        $filters = $request->only([
            'search', 'department_id', 'category_id', 'asset_status',
            'present_sub_department_id', 'condition', 'assigned_only',
            'unassigned_only', 'has_image', 'has_bill', 'verified',
            'unverified', 'date_from', 'date_to', 'qty_min', 'qty_max',
            'verified_qty_min', 'verified_qty_max', 'employee_id',
        ]);

        $export = new AssetsExport($filters);
        $filename = 'assets-export-'.now()->format('Y-m-d-His').'.xlsx';

        return Excel::download($export, $filename);
    }

    /**
     * Download Excel template for asset import.
     */
    public function downloadTemplate()
    {
        $export = new AssetsTemplateExport;
        $filename = 'assets-import-template-'.now()->format('Y-m-d').'.xlsx';

        return Excel::download($export, $filename);
    }

    /**
     * Import assets from Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:20480'],
        ]);

        $import = new AssetsImport;

        try {
            Excel::import($import, $request->file('file'));

            $failures = $import->failures();
            $importedCount = $import->getRowCount() - count($failures);

            if (empty($failures)) {
                return redirect()
                    ->route('assets.index')
                    ->with('success', "Successfully imported {$importedCount} assets.");
            }

            return redirect()
                ->route('assets.index')
                ->with('warning', "Imported {$importedCount} assets. {$failures->count()} rows failed.")
                ->with('import_failures', $failures);

        } catch (\Throwable $e) {
            \Log::error('Asset import failed', ['error' => $e->getMessage()]);

            return back()->withErrors([
                'file' => 'Import failed: '.$e->getMessage(),
            ]);
        }
    }
}
