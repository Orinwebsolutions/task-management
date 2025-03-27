<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginUserController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();
        // Revoke all existing tokens
        $user->tokens()->delete();

        // Create new token with explicit abilities and expiration
        $token = $user->createToken('auth-token', ['*'], now()->addDay());

        return response()->json([
            'message' => 'Login successful',
            'token' => $token->plainTextToken
        ], 200);
    }
}
