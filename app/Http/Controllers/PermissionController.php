<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Role;

class PermissionController extends Controller
{
    public function getAllPermissions()
    {
        $permissions = Permission::all();
        return response()->json(
            [
                'status' => 'success',
                'message' => 'All permissions fetched successfully',
                'data' => $permissions
            ]
        );
    }

    public function getPermissionById($id)
    {
        $permission = Permission::where('id', $id)->first();
        if (!$permission) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Permission not found',
                    'data' => null
                ],
                404
            );
        }
        return response()->json(
            [
                'status' => 'success',
                'message' => 'Permission fetched successfully',
                'data' => $permission
            ]
        );
    }

    public function createPermission(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|string'
            ]
        );

        $permission = Permission::create([
            'name' => $request->name
        ]);

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Permission created successfully',
                'data' => $permission
            ],
            201
        );
    }

    public function updatePermission(Request $request, $id)
    {
        $request->validate(
            [
                'name' => 'required|string'
            ]
        );

        $permission = Permission::where('id', $id)->first();

        if (!$permission) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Permission not found',
                    'data' => null
                ],
                404
            );
        }

        $permission->update([
            'name' => $request->name
        ]);

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Permission updated successfully',
                'data' => $permission
            ]
        );
    }

    public function deletePermission($id)
    {
        $permission = Permission::where('id', $id)->first();

        if (!$permission) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Permission not found'
                ],
                404
            );
        }

        $permission->delete();

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Permission deleted successfully'
            ]
        );
    }

    public function assignPermissionToRole(Request $request)
    {
        $request->validate(
            [
                'role_id' => 'required|integer',
                'permission_id' => 'required|integer'
            ]
        );

        $role = Role::where('id', $request->role_id)->first();
        $permission = Permission::where('id', $request->permission_id)->first();

        if (!$role || !$permission) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Role or permission not found'
                ],
                404
            );
        }

        $role->permissions()->attach($permission->id);

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Permission assigned to role successfully',
                'role' => $role,
                'permission' => $permission
            ]
        );
    }
}
