<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetTransfer;
use App\Models\Department;
use App\Models\OperationalRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AdminController extends Controller
{
    /**
     * Modules that can be granted to Staff.
     */
    private const MODULES = [
        'assets' => 'Assets',
        'departments' => 'Departments',
        'categories' => 'Categories',
        'employees' => 'Employees',
        'imports' => 'Imports',
        'transfers' => 'Transfers',
        'bills' => 'Bills',
    ];

    /**
     * Display the Super Admin panel.
     */
    public function index()
    {
        $staff = User::where('role', 'staff')
            ->with('department')
            ->latest()
            ->get();

        // Existing asset-transfer records and their relationships.
        $assetTransfers = AssetTransfer::with([
            'asset',
            'fromDepartment',
            'toDepartment',
            'initiatedBy',
            'approvedBy',
            'completedBy',
        ])
            ->latest()
            ->get();

        // General operational requests submitted by staff.
        $operationalRequests = OperationalRequest::with([
            'user.department',
            'department',
            'reviewer',
        ])
            ->latest()
            ->get();

        return view('admin.index', [
            'staff' => $staff,

            'staffCount' => $staff->count(),

            'approvedCount' => User::where('role', 'staff')
                ->where('account_status', 'approved')
                ->count(),

            'modules' => self::MODULES,

            'departments' => Department::orderBy('name')->get(),

            'assetTransfers' => $assetTransfers,

            'operationalRequests' => $operationalRequests,
        ]);
    }

    /**
     * Display the Staff account edit form.
     */
    public function editStaff(User $user)
    {
        abort_unless($user->role === 'staff', 404);

        return view('admin.staff-edit', [
            'user' => $user,
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    /**
     * Generate an approved Staff account.
     */
    public function generateAccount(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'department_id' => [
                'required',
                'integer',
                'exists:departments,id',
            ],

            'password' => [
                'required',
                'string',
                'confirmed',
                PasswordRule::min(12),
            ],
        ]);

        $permissions = array_fill_keys(
            array_keys(self::MODULES),
            false
        );

        $user = new User;

        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'department_id' => $validated['department_id'],
            'password' => Hash::make($validated['password']),
            'role' => 'staff',
            'account_status' => 'approved',
            'permissions' => $permissions,
        ])->save();

        return redirect()
            ->route('admin.index', ['tab' => 'generate'])
            ->with('status', 'Staff account generated successfully.');
    }

    /**
     * Update Staff account details and department.
     */
    public function updateStaff(Request $request, User $user)
    {
        abort_unless($user->role === 'staff', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'department_id' => [
                'required',
                'integer',
                'exists:departments,id',
            ],
        ]);

        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'department_id' => $validated['department_id'],
        ])->save();

        return redirect()
            ->route('admin.index', ['tab' => 'staff'])
            ->with(
                'status',
                'Staff details and department updated successfully.'
            );
    }

    /**
     * Send a secure password-reset link to a Staff member.
     *
     * Only Super Admins can access this method through
     * the super.admin route middleware.
     */
    public function sendPasswordResetLink(User $user)
    {
        abort_unless(
            $user->role === 'staff',
            404,
            'Only Staff accounts can receive a password-reset link.'
        );

        abort_unless(
            $user->account_status === 'approved',
            422,
            'Only approved Staff accounts can receive a password-reset link.'
        );

        $status = Password::sendResetLink([
            'email' => $user->email,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()
                ->route('admin.index', ['tab' => 'staff'])
                ->with(
                    'status',
                    'Password-reset link sent to '.$user->email.'.'
                );
        }

        return redirect()
            ->route('admin.index', ['tab' => 'staff'])
            ->withErrors([
                'email' => __($status),
            ]);
    }

    /**
     * Approve a pending Staff account.
     */
    public function approveAccount(User $user)
    {
        abort_unless(
            $user->role === 'staff',
            422,
            'Only Staff accounts can be approved.'
        );

        abort_unless(
            $user->account_status === 'pending',
            422,
            'This account is not pending approval.'
        );

        $user->forceFill([
            'account_status' => 'approved',
            'permissions' => $user->permissions ?? [],
        ])->save();

        return redirect()
            ->route('admin.index', ['tab' => 'accounts'])
            ->with('status', 'Staff account approved successfully.');
    }

    /**
     * Reject a pending Staff account.
     */
    public function rejectAccount(User $user)
    {
        abort_unless(
            $user->role === 'staff',
            422,
            'Only Staff accounts can be rejected.'
        );

        abort_unless(
            $user->account_status === 'pending',
            422,
            'This account is not pending approval.'
        );

        $user->forceFill([
            'account_status' => 'rejected',
        ])->save();

        return redirect()
            ->route('admin.index', ['tab' => 'accounts'])
            ->with('status', 'Staff account rejected.');
    }

    /**
     * Update Staff module permissions (bulk update).
     */
    public function updatePermissions(Request $request, User $user)
    {
        abort_unless(
            $user->role === 'staff',
            422,
            'Permissions can only be set for Staff.'
        );

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],

            'permissions.*' => [
                'required',
                Rule::in(['0', '1', 0, 1, true, false]),
            ],
        ]);

        $submitted = $validated['permissions'] ?? [];
        $permissions = [];

        foreach (self::MODULES as $module => $label) {
            $permissions[$module] = filter_var(
                $submitted[$module] ?? false,
                FILTER_VALIDATE_BOOLEAN
            );
        }

        $user->forceFill([
            'permissions' => $permissions,
        ])->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'All permissions updated successfully.',
                'permissions' => $permissions,
            ]);
        }

        return redirect()
            ->route('admin.index', ['tab' => 'permissions'])
            ->with('status', 'Staff permissions updated successfully.');
    }

    /**
     * Update a single Staff module permission.
     */
    public function updateSinglePermission(Request $request, User $user, string $module)
    {
        abort_unless(
            $user->role === 'staff',
            422,
            'Permissions can only be set for Staff.'
        );

        // Validate the module is a known permission key
        abort_unless(
            array_key_exists($module, self::MODULES),
            422,
            'Invalid permission module.'
        );

        $validated = $request->validate([
            'value' => ['required', 'boolean'],
        ]);

        $permissions = $user->permissions ?? [];
        $permissions[$module] = $validated['value'];

        $user->forceFill([
            'permissions' => $permissions,
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully.',
            'module' => $module,
            'value' => $validated['value'],
        ]);
    }

    /**
     * Update Staff password.
     */
    public function updateStaffPassword(Request $request, User $user)
    {
        abort_unless($user->role === 'staff', 404);

        $validated = $request->validate([
            'password' => ['sometimes', 'nullable', 'string', 'confirmed', PasswordRule::min(12)],
        ]);

        // Only update password if provided
        if ($request->filled('password')) {
            $user->forceFill([
                'password' => Hash::make($validated['password']),
            ])->save();
        }

        return redirect()
            ->route('admin.staff.edit', $user)
            ->with('status', $request->filled('password') ? 'Password updated successfully.' : 'No password change requested.');
    }

    /**
     * Activate a staff account.
     */
    public function activateStaff(User $user)
    {
        abort_unless($user->role === 'staff', 404);

        if ($user->account_status === 'approved') {
            return redirect()
                ->route('admin.staff.edit', $user)
                ->with('status', 'Staff account is already active.');
        }

        $user->forceFill([
            'account_status' => 'approved',
        ])->save();

        return redirect()
            ->route('admin.staff.edit', $user)
            ->with('status', 'Staff account activated successfully.');
    }

    /**
     * Deactivate a staff account.
     */
    public function deactivateStaff(User $user)
    {
        abort_unless($user->role === 'staff', 404);

        if ($user->account_status === 'inactive') {
            return redirect()
                ->route('admin.staff.edit', $user)
                ->with('status', 'Staff account is already inactive.');
        }

        // Prevent deactivating yourself
        if (auth()->id() === $user->id) {
            return redirect()
                ->route('admin.staff.edit', $user)
                ->withErrors(['account_status' => 'You cannot deactivate your own account.']);
        }

        $user->forceFill([
            'account_status' => 'inactive',
        ])->save();

        return redirect()
            ->route('admin.staff.edit', $user)
            ->with('status', 'Staff account deactivated successfully.');
    }

    /**
     * Delete a staff account.
     */
    public function deleteStaff(Request $request, User $user)
    {
        abort_unless($user->role === 'staff', 404);

        // Prevent deleting yourself
        if (auth()->id() === $user->id) {
            return redirect()
                ->route('admin.index', ['tab' => 'staff'])
                ->withErrors(['user' => 'You cannot delete your own account.']);
        }

        // Check for dependent records that would prevent safe deletion
        $blockingRecords = [];

        // Check AssetTransfer records where user is initiator, approver, or completer
        $transferCount = AssetTransfer::where(function ($q) use ($user) {
            $q->where('initiated_by', $user->id)
                ->orWhere('approved_by', $user->id)
                ->orWhere('completed_by', $user->id);
        })->count();

        if ($transferCount > 0) {
            $blockingRecords[] = "{$transferCount} asset transfer record(s)";
        }

        // Check OperationalRequest records
        $requestCount = OperationalRequest::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhere('reviewed_by', $user->id);
        })->count();

        if ($requestCount > 0) {
            $blockingRecords[] = "{$requestCount} operational request record(s)";
        }

        // Check Asset records where user is creator or updater
        $assetCount = Asset::where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
                ->orWhere('updated_by', $user->id);
        })->count();

        if ($assetCount > 0) {
            $blockingRecords[] = "{$assetCount} asset record(s)";
        }

        // Check if user has any permissions assigned (optional check)
        if (! empty($user->permissions)) {
            $blockingRecords[] = 'assigned module permissions';
        }

        if (! empty($blockingRecords)) {
            return redirect()
                ->route('admin.staff.edit', $user)
                ->withErrors([
                    'user' => 'Cannot delete staff account. Dependent records exist: '.implode(', ', $blockingRecords).'. Please reassign or delete those records first.',
                ]);
        }

        // Safe to delete - use database transaction
        DB::transaction(function () use ($user) {
            // Nullify foreign keys before deleting (optional, since we checked for dependencies)
            AssetTransfer::where('initiated_by', $user->id)->update(['initiated_by' => null]);
            AssetTransfer::where('approved_by', $user->id)->update(['approved_by' => null]);
            AssetTransfer::where('completed_by', $user->id)->update(['completed_by' => null]);

            OperationalRequest::where('reviewed_by', $user->id)->update(['reviewed_by' => null]);

            Asset::where('created_by', $user->id)->update(['created_by' => null]);
            Asset::where('updated_by', $user->id)->update(['updated_by' => null]);

            $user->delete();
        });

        return redirect()
            ->route('admin.index', ['tab' => 'staff'])
            ->with('status', 'Staff account deleted successfully.');
    }
}
