<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\CategoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
// Route::post('/refresh-token', [UserController::class, 'refreshToken']);
// Route::post('/logout', [UserController::class, 'logout']);
// Route::post('/reset-password', [UserController::class, 'resetPassword']);
// Route::delete('/delete-user', [UserController::class, 'deleteUser']);
// Route::get('/me', [UserController::class, 'getMe']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    // Users
    Route::post('/refresh-token', [UserController::class, 'refreshToken']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/reset-password', [UserController::class, 'resetPassword']);
    Route::delete('/delete-user', [UserController::class, 'deleteUser']);
    Route::get('/me', [UserController::class, 'getMe']);
    Route::get('/users', [UserController::class, 'getAllUsers']);
    Route::get('/user/{id}', [UserController::class, 'getUserById']);
    Route::put('/update-user/{id}', [UserController::class, 'updateUser']);


    // Roles
    Route::get('/roles', [RoleController::class, 'getAllRoles']);
    Route::get('/role/{id}', [RoleController::class, 'getRoleById']);
    Route::post('/create-role', [RoleController::class, 'createRole']);
    Route::put('/update-role/{id}', [RoleController::class, 'updateRole']);
    Route::delete('/delete-role/{id}', [RoleController::class, 'deleteRole']);


    // Permissions
    Route::get('/permissions', [PermissionController::class, 'getAllPermissions']);
    Route::get('/permission/{id}', [PermissionController::class, 'getPermissionById']);
    Route::post('/create-permission', [PermissionController::class, 'createPermission']);
    Route::put('/update-permission/{id}', [PermissionController::class, 'updatePermission']);
    Route::delete('/delete-permission/{id}', [PermissionController::class, 'deletePermission']);

    // Assigning and revoking roles and permissions
    Route::post('/assign-role-to-user', [RoleController::class, 'assignRoleToUser']);
    Route::post('/assign-permission-to-role', [PermissionController::class, 'assignPermissionToRole']);
    Route::delete('/revoke-role-from-user', [RoleController::class, 'revokeRoleFromUser']);
    Route::delete('/revoke-permission-from-role', [PermissionController::class, 'revokePermissionFromRole']);

    // Region
    // Route::get('/regions', [RegionController::class, 'getAllRegions']);
    // Route::get('/region/{id}', [RegionController::class, 'getRegionById']);
    // Route::post('/create-region', [RegionController::class, 'createRegion']);
    // Route::put('/update-region/{id}', [RegionController::class, 'updateRegion']);
    // Route::delete('/delete-region/{id}', [RegionController::class, 'deleteRegion']);
});


Route::middleware(['auth:api', 'check.token.role.permission:superAdmin,admin,region-read'])->group(function () {
    Route::get('/regions', [RegionController::class, 'getAllRegions']);
});
Route::middleware(['auth:api', 'check.token.role.permission:admin,region-read'])->group(function () {
    Route::get('/region/{id}', [RegionController::class, 'getRegionById']);
});
Route::middleware(['auth:api', 'check.token.role.permission:superAdmin,region-create'])->group(function () {
    Route::post('/create-region', [RegionController::class, 'createRegion']);
});
Route::middleware(['auth:api', 'check.token.role.permission:superAdmin,region-update'])->group(function () {
    Route::put('/update-region/{id}', [RegionController::class, 'updateRegion']);
});
Route::middleware(['auth:api', 'check.token.role.permission:superAdmin,region-delete'])->group(function () {
    Route::delete('/delete-region/{id}', [RegionController::class, 'deleteRegion']);
});



Route::middleware(['auth:api', 'check.token.role.permission:superAdmin,admin,category-read'])->group(function () {
    Route::get('/categories', [CategoryController::class, 'getAllCategories']);
});
Route::middleware(['auth:api', 'check.token.role.permission:superAdmin,admin,category-read'])->group(function () {
    Route::get('/category/{id}', [CategoryController::class, 'getCategoryById']);
});
Route::middleware(['auth:api', 'check.token.role.permission:superAdmin,admin,category-read'])->group(function () {
    Route::post('/create-category', [CategoryController::class, 'createCategory']);
});
Route::middleware(['auth:api', 'check.token.role.permission:superAdmin,admin,category-read'])->group(function () {
    Route::put('/update-category/{id}', [CategoryController::class, 'updateCategory']);
});
Route::middleware(['auth:api', 'check.token.role.permission:superAdmin,admin,category-read'])->group(function () {
    Route::delete('/delete-category/{id}', [CategoryController::class, 'deleteCategory']);
});
