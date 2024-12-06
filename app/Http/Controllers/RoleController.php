<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;

class RoleController extends Controller
{
    public function getAllRoles()
    {
        $roles = Role::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Roles fetched successfully',
            'roles' => $roles
        ]);
    }

    public function getRoleById($id)
    {
        $role = Role::where('id', $id)->first();

        if (!$role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Role fetched successfully',
            'role' => $role
        ]);
    }

    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $role = Role::create([
            'name' => $request->name
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Role created successfully',
            'role' => $role
        ]);
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $role = Role::where('id', $id)->first();

        if (!$role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found'
            ], 404);
        }

        $role->update([
            'name' => $request->name
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Role updated successfully',
            'role' => $role
        ]);
    }


    public function deleteRole($id)
    {
        $role = Role::where('id', $id)->first();

        if (!$role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found'
            ], 404);
        }

        $role->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Role deleted successfully'
        ]);
    }

    public function assignRoleToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'role_id' => 'required|integer'
        ]);

        $user = User::where('id', $request->user_id)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $role = Role::where('id', $request->role_id)->first();

        if (!$role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found'
            ], 404);
        }

        $user->roles()->attach($role->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Role assigned successfully',
            'user' => $user,
            'role' => $role
        ]);
    }

    public function revokeRoleFromUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'role_id' => 'required|integer'
        ]);

        $user = User::where('id', $request->user_id)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $role = Role::where('id', $request->role_id)->first();

        if (!$role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found'
            ], 404);
        }

        $user->roles()->detach($role->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Role revoked from user successfully',
            'user' => $user,
            'role' => $role
        ]);
    }
}
