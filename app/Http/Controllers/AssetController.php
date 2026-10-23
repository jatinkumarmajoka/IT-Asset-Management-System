<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
use App\Models\AssetDocument;
use App\Models\Category;
use App\Models\Department;
use App\Models\Employee;
use App\Models\SubDepartment;
use App\Services\AssetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        return view('assets.index', compact(
            'assets',
            'departments',
            'categories'
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
}
