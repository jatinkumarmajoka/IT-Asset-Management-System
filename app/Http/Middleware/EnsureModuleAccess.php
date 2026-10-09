<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleAccess
{
    public function handle(
        Request $request,
        Closure $next,
        string $module
    ): Response {
        $user = $request->user();

        if (!$user || !$user->isApproved()) {
            abort(403, 'Your account is not approved.');
        }

        // Super Admin has full access.
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Staff with this module permission can manage its records.
        $canManage = $user->hasModuleAccess($module);

        // Allow Staff to view module pages without edit permission.
        if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
            $routeName = $request->route()?->getName() ?? '';

            // Creating or editing forms require management permission.
            if (
                str_ends_with($routeName, '.create') ||
                str_ends_with($routeName, '.edit')
            ) {
                abort_unless(
                    $canManage,
                    403,
                    'You have view-only access to this module.'
                );
            }

            return $next($request);
        }

        // Block POST, PUT, PATCH and DELETE without permission.
        abort_unless(
            $canManage,
            403,
            'You have view-only access to this module. Contact your Super Admin for editing permission.'
        );

        return $next($request);
    }
}
