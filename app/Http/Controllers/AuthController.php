<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $credentials['email'] = strtolower(trim($credentials['email']));
        $remember = $request->boolean('remember');

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials are incorrect.',
            ]);
        }

        if (! $user->isApproved()) {
            throw ValidationException::withMessages([
                'email' => 'Your account is pending approval or is not active.',
            ]);
        }

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Unable to sign you in. Please try again.',
            ]);
        }

        $request->session()->regenerate();

        $request->user()->forceFill([
            'last_activity_at' => now(),
        ])->save();

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        $name = trim($data['name']);
        $email = strtolower(trim($data['email']));

        if (User::whereRaw('LOWER(email) = ?', [$email])->exists()) {
            throw ValidationException::withMessages([
                'email' => 'This email address is already registered.',
            ]);
        }

        User::forceCreate([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($data['password']),
            'role' => 'staff',
            'account_status' => 'pending',
            'permissions' => [],
            'last_activity_at' => null,
        ]);

        return redirect()
            ->route('login')
            ->with(
                'status',
                'Registration submitted successfully. Please wait for Super Admin approval.'
            );
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('status', 'You have been logged out successfully.');
    }
}
