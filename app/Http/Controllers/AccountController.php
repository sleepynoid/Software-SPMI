<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use function Laravel\Prompts\error;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return User::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(Request $request)
    {
        $input = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'jenis_user' => 'required|in:Dosen,Tenaga Kependidikan',
        ]);

        User::create([
            'nama_lengkap' => $input['nama_lengkap'],
            'email'        => $input['email'],
            'role_id'      => $input['role_id'],
            'jenis_user'   => $input['jenis_user'],
            'password'     => bcrypt($input['password'])
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil. Silahkan login!');
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

        Log::info('Login attempt for: ' . $credentials['email']);

        if (!Auth::attempt($credentials)) {
            Log::warning('Login failed for: ' . $credentials['email']);
            return back()->withErrors([
                'email' => 'Email atau Password Salah.',
            ])->onlyInput('email');
        }

        Log::info('Login success for: ' . $credentials['email'] . '. Redirecting to dashboard.');
        $request->session()->regenerate();

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
