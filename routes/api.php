<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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
    Route::post('/refresh-token', [UserController::class, 'refreshToken']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/reset-password', [UserController::class, 'resetPassword']);
    Route::delete('/delete-user', [UserController::class, 'deleteUser']);
    Route::get('/me', [UserController::class, 'getMe']);
    Route::get('/users', [UserController::class, 'getAllUsers']);
    Route::get('/user/{id}', [UserController::class, 'getUserById']);
    Route::put('/update-user/{id}', [UserController::class, 'updateUser']);
});
