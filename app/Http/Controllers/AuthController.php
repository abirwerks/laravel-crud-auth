<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
            'role' => 'in:user,admin'
        ]);

        // Assign role, default to 'user' if not provided
        $role = $request->input('role', 'user');

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'role' => $role 
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

         // Create a wallet for the user
         Wallet::create([
            'user_id' => $user->id,
            'balance' => 0.00, // Default balance
            'currency' => 'USD' // You can change the default currency as needed
        ]);

        $response = [
            'message' => 'User registered',	
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'username password dont match'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        // Store the token in the api_token column
        $user->api_token = $token;
        $user->save();

        $response = [
            'message' => 'Logged in successfully',
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];
    }
}
