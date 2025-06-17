<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();

        // Dapatkan role_id dari user
        $role = DB::table('role_user')->where('user_id', $user->id)->first();
        if (!$role) {
            return response()->json(['message' => 'Forbidden: No role assigned.'], 403);
        }

        // Periksa apakah role tersebut memiliki permission yang dibutuhkan
        $hasPermission = DB::table('permission_role')
            ->join('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->where('permission_role.role_id', $role->role_id)
            ->where('permissions.slug', $permission)
            ->exists();

        if (!$hasPermission) {
            return response()->json(['message' => 'Forbidden: You do not have the required permission.'], 403);
        }

        return $next($request);
    }
}