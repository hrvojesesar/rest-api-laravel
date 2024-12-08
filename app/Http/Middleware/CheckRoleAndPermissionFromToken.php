<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckRoleAndPermissionFromToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles = null, $permissions = null)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $userRoles = JWTAuth::getPayload()->get('roles'); // Role iz tokena
        $userPermissions = JWTAuth::getPayload()->get('permissions'); // Dozvole iz tokena

        $requiredRoles = $roles ? explode(',', $roles) : [];
        $requiredPermissions = $permissions ? explode(',', $permissions) : [];

        // Provjera rola
        if (!empty($requiredRoles) && !array_intersect($requiredRoles, $userRoles)) {
            return response()->json(['message' => 'Unauthorized: Missing required role.'], 403);
        }

        // Provjera permisija
        if (!empty($requiredPermissions) && !array_intersect($requiredPermissions, $userPermissions)) {
            return response()->json(['message' => 'Unauthorized: Missing required permission.'], 403);
        }

        return $next($request);
    }
}
