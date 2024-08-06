<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('remember_token')->plainTextToken;

            return redirect()->route('login.response')->with([
                'success' => 'User login successfully.',
                'token' => $token,
            ]);
        } else {
            return redirect()->route('login.response')->with('error', 'Unauthorized.');
        }
    }
}
