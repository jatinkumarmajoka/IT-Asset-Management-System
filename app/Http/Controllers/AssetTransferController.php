<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetTransferRequest;
use App\Models\Asset;
use App\Models\AssetTransfer;
use App\Models\Department;
use App\Models\Employee;
use App\Services\Transfers\AssetTransferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssetTransferController extends Controller
{
    public function __construct(
        private AssetTransferService $transferService
    ) {
    }

    /**
     * Display transfer history.
     */
    public function index(): View
    {
        $transfers = AssetTransfer::query()
            ->with([
                'asset',
                'fromDepartment',
                'toDepartment',
                'fromOccupant',
                'toOccupant',
                'initiatedBy',
                'approvedBy',
                'completedBy',
            ])
            ->latest('id')
            ->paginate(20);

        return view('transfers.index', compact('transfers'));
    }

    /**
     * Show transfer form.
     */
    public function create(): View
{
    $assets = Asset::query()
        ->with([
            'department',
            'occupant',
        ])
        ->orderBy('asset_code')
        ->get();

    $departments = Department::query()
        ->orderBy('name')
        ->get();

    $employees = Employee::query()
        ->where('status', 'active')
        ->orderBy('name')
        ->get();

    $employeeData = $employees->map(function ($employee) {
        return [
            'id' => $employee->id,
            'name' => $employee->name,
            'department_id' => $employee->department_id,
        ];
    })->values();

    return view('transfers.create', compact(
        'assets',
        'departments',
        'employees',
        'employeeData'
    ));
}

    /**
     * Create a pending transfer.
     */
    public function store(
        StoreAssetTransferRequest $request
    ): RedirectResponse {
        $transfer = $this->transferService->create(
            $request->validated()
        );

        return redirect()
            ->route('transfers.show', $transfer)
            ->with(
                'success',
                'Transfer request created successfully.'
            );
    }

    /**
     * Display transfer details.
     */
    public function show(
        AssetTransfer $transfer
    ): View {
        $transfer->load([
            'asset.department',
            'asset.category',
            'asset.occupant',
            'fromDepartment',
            'toDepartment',
            'fromOccupant',
            'toOccupant',
            'initiatedBy',
            'approvedBy',
            'completedBy',
        ]);

        return view(
            'transfers.show',
            compact('transfer')
        );
    }

    /**
     * Approve pending transfer.
     */
    public function approve(
        AssetTransfer $transfer
    ): RedirectResponse {
        $this->transferService->approve($transfer);

        return redirect()
            ->route('transfers.show', $transfer)
            ->with(
                'success',
                'Transfer approved successfully.'
            );
    }

    /**
     * Complete approved transfer.
     *
     * This is the point where the asset's
     * current department is actually changed.
     */
    public function complete(
        AssetTransfer $transfer
    ): RedirectResponse {
        $this->transferService->complete($transfer);

        return redirect()
            ->route('transfers.show', $transfer)
            ->with(
                'success',
                'Transfer completed. Asset department updated successfully.'
            );
    }

    /**
     * Cancel pending/approved transfer.
     */
    public function cancel(
        AssetTransfer $transfer
    ): RedirectResponse {
        $this->transferService->cancel($transfer);

        return redirect()
            ->route('transfers.show', $transfer)
            ->with(
                'success',
                'Transfer cancelled successfully.'
            );
    }
}