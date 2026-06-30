<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (! Auth::check() || ! Auth::user()->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You do not have the required permission.',
                    'permission' => $permission,
                ], 403);
            }

            abort(403, 'Missing permission: ' . $permission);
        }

        return $next($request);
    }
}
