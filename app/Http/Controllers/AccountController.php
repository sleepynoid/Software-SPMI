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
    public function register(Request $request): JsonResponse
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
        ]);

        if ($validator->fails()) {
            // return error($validator->errors());
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $input = $request->all();

        User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'role' => $input['role'],
            'password' => bcrypt($input['password'])
        ]);

        return response()->json([
            'success' => 'true',
            'message' => 'Registrasi berhasil. Silahkan login!'
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $kiey = bin2hex(random_bytes(8));
        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken('authToken')->plainTextToken;

        // Pastikan domain cookie sesuai dengan frontend dan backend
        $cookie = cookie(
            'auth_token',
            $token,
            60 * 24,
            '/',
            null,
            request()->secure(),
            true,
            false,
            'lax'
        );

        return response()->json([
            'success' => true,
            'idk' => $kiey,
            'userRole' => $user->role,
            'name' => $user->name,
            'message' => "User {$user->name} successfully logged in"
        ])->withCookie($cookie);
    }
    public function logout(Request $request): JsonResponse
    {
        // Hapus session Laravel
        Auth::guard('web')->logout();

        // Hapus semua sesi yang tersimpan
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout'
        ]);
    }

    public function listUser(): JsonResponse
    {
        return response()->json(User::all());
    }

    public function editUserRole(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer',
            'new_role' => 'required|string|in:Pelaksanaan,Evaluasi,SuperUser,Pengendalian,Peningkatan,Admin', // adjust roles as needed
        ]);

        $user = User::find($request->user_id);
        $user->role = $request->new_role;
        $user->save();

        return response()->json([
            'message' => 'User role updated successfully',
            'user' => $user
        ], 200);
    }

    public function getUserHistory()
    {

    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'new_password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::find($request->user_id);
            $user->password = bcrypt($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password'
            ], 500);
        }
    }
}
