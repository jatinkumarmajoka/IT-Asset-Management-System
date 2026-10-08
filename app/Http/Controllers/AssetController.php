<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use App\Models\Employee;
use App\Services\AssetService;
use Illuminate\Http\Request;

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
        $query = Asset::query()
            ->with([
                'department',
                'category',
                'occupant',
            ]);


        /*
         * Search
         */
        if ($request->filled('search')) {

            $term = trim(
                $request->string('search')
            );

            $query->where(function ($q) use ($term) {

                $q->where(
                    'asset_code',
                    'like',
                    "%{$term}%"
                )
                ->orWhere(
                    'item_name',
                    'like',
                    "%{$term}%"
                )
                ->orWhere(
                    'make',
                    'like',
                    "%{$term}%"
                )
                ->orWhere(
                    'model',
                    'like',
                    "%{$term}%"
                )
                ->orWhere(
                    'barcode_number',
                    'like',
                    "%{$term}%"
                )
                ->orWhere(
                    'asset_serial_number',
                    'like',
                    "%{$term}%"
                );

            });

        }


        /*
         * Department filter
         */
        $query->when(
            $request->filled('department_id'),
            fn ($q) =>
                $q->where(
                    'department_id',
                    $request->integer('department_id')
                )
        );


        /*
         * Category filter
         */
        $query->when(
            $request->filled('category_id'),
            fn ($q) =>
                $q->where(
                    'category_id',
                    $request->integer('category_id')
                )
        );


        /*
         * Status filter
         */
        $query->when(
            $request->filled('asset_status'),
            fn ($q) =>
                $q->where(
                    'asset_status',
                    $request->string('asset_status')
                )
        );


        /*
         * Pagination
         */
        $assets = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();


        /*
         * Filter dropdown data
         */
        $departments = Department::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();


        $categories = Category::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();


        return view(
            'assets.index',
            compact(
                'assets',
                'departments',
                'categories'
            )
        );
    }


    /**
     * Show create asset form.
     */
    public function create()
    {
        return view('assets.create', [

            'departments' =>
                Department::query()
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->get(),

            'categories' =>
                Category::query()
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->get(),

            'employees' =>
                Employee::query()
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->get(),

        ]);
    }


    /**
     * Store a new asset.
     */
    public function store(StoreAssetRequest $request)
    {
        $this->assetService->create(
            $request->validated()
        );

        return redirect()
            ->route('assets.index')
            ->with(
                'success',
                'Asset created successfully.'
            );
    }


 public function show(Asset $asset)
{
    $asset->load([
    'department',
    'category',
    'occupant',
    'documents',
    'transfers' => function ($query) {
            $query->with([
                'fromDepartment',
                'toDepartment',
                'fromOccupant',
                'toOccupant',
                'initiatedBy',
                'approvedBy',
                'completedBy',
            ])
            ->latest('transfer_date')
            ->latest('id');
        },
    ]);

    /*
     * Latest completed transfer determines
     * the Previous Occupant.
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

    return view(
        'assets.show',
        compact(
            'asset',
            'previousOccupant'
        )
    );
}


   public function edit(Asset $asset)
{
    $asset->load([
        'department',
        'category',
        'occupant',
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
            ->route(
                'assets.show',
                $asset
            )
            ->with(
                'success',
                'Asset updated successfully.'
            );
    }


    /**
     * Delete an asset.
     */
    public function destroy(Asset $asset)
    {
        $asset->history()->create([

            'action' =>
                'deleted',

            'old_value' =>
                json_encode(
                    $asset->toArray()
                ),

            'performed_by' => auth()->user()?->getAuthIdentifier(),

            'ip_address' =>
                request()->ip(),

            'created_at' =>
                now(),

        ]);

        $asset->delete();

        return redirect()
            ->route('assets.index')
            ->with(
                'success',
                'Asset deleted successfully.'
            );
    }
}