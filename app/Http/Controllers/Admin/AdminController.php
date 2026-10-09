<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    /** The modules that can be granted to Staff. */
    private const MODULES = [
        'assets' => 'Assets',
        'departments' => 'Departments',
        'categories' => 'Categories',
        'employees' => 'Employees',
        'imports' => 'Imports',
        'transfers' => 'Transfers',
        'verification' => 'Verification',
        'reports' => 'Reports',
        'bills' => 'Bills',
    ];

    public function index()
    {
        $pendingUsers = User::where('account_status', 'pending')->latest()->get();
        $staff = User::where('role', 'staff')->latest()->get();

        return view('admin.index', [
            'pendingUsers' => $pendingUsers,
            'staff' => $staff,
            'staffCount' => $staff->count(),
            'pendingCount' => $pendingUsers->count(),
            'approvedCount' => User::where('role', 'staff')->where('account_status', 'approved')->count(),
            'modules' => self::MODULES,
        ]);
    }

    /** Create an approved Staff account from the Super Admin panel. */
    public function generateAccount(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(12)],
        ]);

        $permissions = array_fill_keys(array_keys(self::MODULES), false);

        $user = new User();
        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'staff',
            'account_status' => 'approved',
            'permissions' => $permissions,
        ])->save();

        return redirect()
            ->route('admin.index', ['tab' => 'generate'])
            ->with('status', 'Staff account generated and approved successfully. Share the credentials securely with the user.');
    }

    public function approveAccount(User $user)
    {
        abort_unless($user->role === 'staff', 422, 'Only Staff accounts can be approved.');
        abort_unless($user->account_status === 'pending', 422, 'This account is not pending approval.');

        $user->forceFill([
            'account_status' => 'approved',
            'permissions' => $user->permissions ?? [],
        ])->save();

        return redirect()->route('admin.index', ['tab' => 'accounts'])->with('status', 'Staff account approved successfully.');
    }

    public function rejectAccount(User $user)
    {
        abort_unless($user->role === 'staff', 422, 'Only Staff accounts can be rejected.');
        abort_unless($user->account_status === 'pending', 422, 'This account is not pending approval.');

        $user->forceFill(['account_status' => 'rejected'])->save();

        return redirect()->route('admin.index', ['tab' => 'accounts'])->with('status', 'Staff account rejected.');
    }

    public function updatePermissions(Request $request, User $user)
    {
        abort_unless($user->role === 'staff', 422, 'Permissions can only be set for Staff.');

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['required', Rule::in(['0', '1', 0, 1, true, false])],
        ]);

        $submitted = $validated['permissions'] ?? [];
        $permissions = [];
        foreach (self::MODULES as $module => $label) {
            $permissions[$module] = filter_var($submitted[$module] ?? false, FILTER_VALIDATE_BOOLEAN);
        }

        $user->forceFill(['permissions' => $permissions])->save();

        return redirect()->route('admin.index', ['tab' => 'permissions'])->with('status', 'Staff permissions updated successfully.');
    }
}
