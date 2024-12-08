<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Models\Role;

class CheckRoleAndPermissionFromToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$params)
    {
        // Parsiranje parametara
        $rolesAndPermissions = implode(',', $params);
        $parts = explode(',', $rolesAndPermissions);

        $requiredRoles = [];
        $requiredPermissions = [];

        foreach ($parts as $part) {
            if (Role::where('name', $part)->exists()) {
                $requiredRoles[] = $part;
            } else {
                $requiredPermissions[] = $part;
            }
        }

        $user = JWTAuth::parseToken()->authenticate();
        $userRoles = JWTAuth::getPayload()->get('roles');
        $userPermissions = array_unique(JWTAuth::getPayload()->get('permissions')); // Ukloni duplikate

        if (!empty($requiredRoles) && empty(array_intersect($requiredRoles, $userRoles))) {
            return response()->json(['message' => 'Unauthorized: Invalid role.'], 403);
        }

        // Provera dozvola
        if (!empty($requiredPermissions) && empty(array_intersect($requiredPermissions, $userPermissions))) {
            return response()->json(['message' => 'Unauthorized: Missing permission.'], 403);
        }

        return $next($request);
    }
}
