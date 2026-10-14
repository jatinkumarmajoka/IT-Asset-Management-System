<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetTransferRequest;
use App\Models\Asset;
use App\Models\AssetTransfer;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Services\Transfers\AssetTransferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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
        $user = Auth::user();

        abort_unless($user, 401);

        $query = AssetTransfer::query()
            ->with([
                'asset',
                'fromDepartment',
                'toDepartment',
                'fromOccupant',
                'toOccupant',
                'initiatedBy',
                'approvedBy',
                'completedBy',
                'documents',
            ])
            ->latest('id');

        // Staff can see requests they submitted. Super Admin can see all.
        if (!$user->isSuperAdmin()) {
            $query->where('initiated_by', $user->id);
        }

        $transfers = $query->paginate(20);

        return view('transfers.index', compact('transfers'));
    }

    /**
     * Show transfer creation form.
     */
    public function create(): View
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_unless(
            $user->isSuperAdmin() || $user->canManageModule('transfers'),
            403,
            'You do not have permission to create transfer requests.'
        );

        $assets = Asset::query()
            ->with(['department', 'occupant'])
            ->orderBy('asset_code')
            ->get();

        $departments = Department::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $employees = Employee::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $employeeData = $employees
            ->map(static function (Employee $employee): array {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'department_id' => $employee->department_id,
                ];
            })
            ->values();

        return view(
            'transfers.create',
            compact(
                'assets',
                'departments',
                'employees',
                'employeeData'
            )
        );
    }

    /**
     * Create a pending transfer request.
     */
    public function store(
        StoreAssetTransferRequest $request
    ): RedirectResponse {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_unless(
            $user->isSuperAdmin() || $user->canManageModule('transfers'),
            403,
            'You do not have permission to create transfer requests.'
        );

        // Keep the validated upload separate from the transfer database fields.
        // transfer_image belongs to asset_transfer_documents, not asset_transfers.
        $validated = $request->validated();
        unset($validated['transfer_image']);

        $transfer = $this->transferService->create($validated);

        // The document controller handles private storage and document records.
        if ($request->hasFile('transfer_image')) {
            app(AssetTransferDocumentController::class)->store(
                $request,
                $transfer
            );
        }

        return redirect()
            ->route('transfers.show', $transfer)
            ->with('success', 'Transfer request created successfully.');
    }

    /**
     * Display transfer details and workflow progress.
     */
    public function show(AssetTransfer $transfer): View
    {
        $this->authorizeTransferVisibility($transfer);

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
            'documents',
        ]);

        return view('transfers.show', compact('transfer'));
    }

    /**
     * Approve a pending transfer. Only the Super Admin may approve.
     */
    public function approve(AssetTransfer $transfer): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $this->transferService->approve($transfer);

        return redirect()
            ->route('transfers.show', $transfer)
            ->with('success', 'Transfer approved successfully.');
    }

    /**
     * Complete an approved transfer. Only the Super Admin may complete.
     */
    public function complete(AssetTransfer $transfer): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $this->transferService->complete($transfer);

        return redirect()
            ->route('transfers.show', $transfer)
            ->with(
                'success',
                'Transfer completed. Asset department and occupant updated successfully.'
            );
    }

    /**
     * Cancel a transfer request.
     *
     * Staff may cancel their own request while it is pending.
     * The Super Admin may cancel any transfer allowed by the service.
     */
    public function cancel(AssetTransfer $transfer): RedirectResponse
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_unless(
            $user->isSuperAdmin()
                || (int) $transfer->initiated_by === (int) $user->id,
            403,
            'You cannot cancel another user’s transfer request.'
        );

        $this->transferService->cancel($transfer);

        return redirect()
            ->route('transfers.show', $transfer)
            ->with('success', 'Transfer cancelled successfully.');
    }

    /**
     * Ensure staff can only view their own requests.
     */
    private function authorizeTransferVisibility(
        AssetTransfer $transfer
    ): void {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_unless(
            $user->isSuperAdmin()
                || (int) $transfer->initiated_by === (int) $user->id,
            403,
            'You cannot view another user’s transfer request.'
        );
    }

    /**
     * Ensure the current user is an authenticated Super Admin.
     */
    private function authorizeSuperAdmin(): void
    {
        $user = Auth::user();

        abort_unless(
            $user instanceof User && $user->isSuperAdmin(),
            403,
            'Only the Super Admin can perform this action.'
        );
    }
}
