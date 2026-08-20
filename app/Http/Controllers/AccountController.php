<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return User::with('role')->select(['id', 'nama_lengkap', 'email', 'role_id'])->get();
    }

    public function loginForm()
    {
        return \Inertia\Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        Log::info('Login attempt', ['email' => $credentials['email'], 'ip' => $request->ip()]);

        if (!Auth::attempt($credentials)) {
            Log::warning('Login failed: invalid credentials', ['email' => $credentials['email'], 'ip' => $request->ip()]);
            return back()->withErrors([
                'email' => 'Email atau Password Salah.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        Log::info('Login successful', ['user' => Auth::user()->email, 'role' => Auth::user()->role->nama_role ?? 'N/A', 'ip' => $request->ip()]);

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
