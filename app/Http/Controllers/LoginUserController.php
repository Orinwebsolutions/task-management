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

        // Create new token with specific abilities
        $token = $user->createToken('auth-token', ['*']);

        return response()->json([
            'message' => 'Login successful',
            // 'user' => $user,
            // 'token_type' => 'Bearer',
            'access_token' => $token->plainTextToken
        ], 200);
    }
}
