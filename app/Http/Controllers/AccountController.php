<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use function Laravel\Prompts\error;

class AccountController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        //
        return User::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(Request $request): JsonResponse {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
        ]);

        if ($validator->fails()) {
            return error($validator->errors());

//            return response()->json([
//                'success' => false,
//                'errors' => $validator->errors()
//            ], 422);
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
            'message' => 'User ' . $input['name'] . ' created successfuly'
        ]);
    }

    public function login(Request $request): JsonResponse {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('token')->plainTextToken;
            $success['name'] = $user->name;

            return response()->json([
                'success' => 'true',
                'token' => $success['token'],
                'userRole' => $user['role'],
                'name' => $user['name'],
                'message' => 'User ' . $user['name'] . ' Successfuly Login'
            ]);
        } else {
            return error("gagal login");
        }
    }

    public function logout(Request $request): JsonResponse {
        $currentUser = Auth::user();
        $login_status = Auth::check();
        if (!$login_status) {
            return $this->sendError('error User not found', $login_status);
        }
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => 'true',
            'message' => 'User ' . $currentUser['name'] . ' Successfuly Logout'
        ]);
    }

    public function listUser(): JsonResponse {
        return response()->json(User::all());
    }
}
