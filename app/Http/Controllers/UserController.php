<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

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
            // Generiraj JWT token
            $token = JWTAuth::attempt($request->only('email', 'password'));


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
}
