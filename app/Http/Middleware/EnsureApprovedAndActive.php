<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureApprovedAndActive
{
    private const INACTIVITY_LIMIT_SECONDS = 172800; // 48 hours

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Only approved accounts may use the application.
        if (! $user->isApproved()) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account is pending approval or is not active.',
            ]);
        }

        // Check the last recorded activity.
        if (
            $user->last_activity_at === null ||
            $user->last_activity_at->diffInSeconds(now()) >= self::INACTIVITY_LIMIT_SECONDS
        ) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Your session expired. Please log in again.',
            ]);
        }

        // Refresh activity for the next request.
        $user->forceFill([
            'last_activity_at' => now(),
        ])->save();

        return $next($request);
    }
}
