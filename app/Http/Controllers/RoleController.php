<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

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
}
