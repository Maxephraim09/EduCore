<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        // Allow Super Admin and Admin roles to bypass granular permission checks
        if (!$user || (!method_exists($user, 'isAdmin') && !method_exists($user, 'isSuperAdmin'))) {
            abort(403, 'You do not have permission to perform this action.');
        }

        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return $next($request);
        }

        abort_unless($user && $user->hasAnyPermission($permissions), 403, 'You do not have permission to perform this action.');

        return $next($request);
    }
}
