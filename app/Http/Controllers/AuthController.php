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

         // If the user is a regular user, create a wallet
        if ($role === 'user') {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => 0.00, // Default balance
                'currency' => 'USD' // You can change the default currency as needed
            ]);

            // Update the user's wallet_id after the wallet is created
            $user->update([
                'wallet_id' => $wallet->id
            ]);
        }

        // Load the wallet relationship if it exists
        $user->load('wallet');

        $response = [
            'message' => 'User registered',	
            'user' => $user,
            'wallet' => $user->wallet,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request) {
        $fields = $request->validate([
            'email' => 'required|string|email',
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

        // Load the wallet relationship if it exists
        $user->load('wallet');

        $response = [
            'message' => 'Logged in successfully',
            'user' => $user,
            // 'wallet' => $user->wallet, // Include wallet data
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

    public function updateWallet(Request $request) {
        // Validate the request input
        $fields = $request->validate([
            'balance' => 'required|numeric', // Ensure balance is a number
            'currency' => 'required|string'  // Ensure currency is provided
        ]);
    
        // Get the currently authenticated user
        $user = auth()->user();
    
        // Check if the user has a wallet, create one if it doesn't exist and user is not admin
        if (!$user->wallet && $user->role != 'admin') {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => 0.00, // Default balance
                'currency' => $fields['currency']
            ]);
    
            // Update the user's wallet_id after the wallet is created
            $user->wallet_id = $wallet->id;
            $user->save();
        } elseif ($user->role === 'admin') {
            return response([
                'message' => 'Admins do not have wallets.'
            ], 403);
        } else {
            // Load the existing wallet
            $wallet = $user->wallet;
        }
    
        // Update the wallet balance and currency
        $newBalance = $wallet->balance + $fields['balance'];
    
        // Check if the new balance would be less than 0
        if ($newBalance < 0) {
            return response([
                'message' => 'Insufficient balance.'
            ], 400);
        }
    
        // Update wallet balance and currency
        $wallet->update([
            'balance' => $newBalance,
            'currency' => $fields['currency']
        ]);
    
        return response([
            'message' => 'Wallet updated successfully.',
            'wallet' => $wallet
        ], 200);
    }
    
}
