<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:40',
            'email' => 'required|email|unique:users|string',
            'password' => 'required|string|min:6'
        ]);

        if ($request->password_confirmation == null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password confirmation is required'
            ], 400);
        }

        if ($request->password !== $request->password_confirmation) {
            return response()->json([
                'message' => 'Password mismatch'
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User registration failed'
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|string',
            'password' => 'required|string'
        ]);

        // Provjera korisnika
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found, please register!'
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid password!'
            ], 401);
        }

        // Pokušaj generiranja JWT tokena
        try {
            // // Generiraj JWT token
            // $token = JWTAuth::attempt($request->only('email', 'password'));

            // Generiraj JWT token s dodatnim podacima
            $token = JWTAuth::claims([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->toArray(), // Dodavanje rola
                'permissions' => $user->permissions()->pluck('name')->toArray(), // Dodavanje dozvola
            ])->attempt($request->only('email', 'password'));

            if (!$token) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid login credentials!'
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Could not create token!',
                'error' => $e->getMessage()
            ], 500);
        }

        // Povrat uspješnog odgovora s tokenom
        return response()->json([
            'status' => 'success',
            'message' => 'User logged in successfully',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }


    public function refreshToken(Request $request)
    {
        $token = JWTAuth::getToken();
        $token = JWTAuth::refresh($token);

        return response()->json([
            'status' => 'success',
            'message' => 'Token refreshed successfully',
            'data' => [
                'token' => $token
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $token = JWTAuth::getToken();
        JWTAuth::invalidate($token);

        return response()->json([
            'status' => 'success',
            'message' => 'User logged out successfully'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|string',
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6',
            'password_confirmation' => 'required|string|min:6'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found!'
            ], 404);
        }

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid password!'
            ], 401);
        }

        if ($request->new_password !== $request->password_confirmation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password mismatch!'
            ], 400);
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset successfully'
        ]);
    }

    public function deleteUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email|string',
            'password' => 'required|string|min:6'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found!'
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid password!'
            ], 401);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Account deleted successfully'
        ]);
    }

    public function getMe(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'status' => 'success',
            'message' => 'Details about you',
            'data' => $user
        ]);
    }

    public function getAllUsers()
    {
        $users = User::all();

        return response()->json([
            'status' => 'success',
            'message' => 'All users fetched successfully',
            'data' => $users
        ]);
    }

    public function getUserById($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found!'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User fetched successfully',
            'data' => $user
        ]);
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:40',
            'email' => 'required|email|string'
        ]);

        $user = User::where('id', $id)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found!'
            ], 404);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }
}
