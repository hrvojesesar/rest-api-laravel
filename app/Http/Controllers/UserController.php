<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

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
}
