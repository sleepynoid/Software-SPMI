<?php

namespace App\Http\Controllers;

use App\Models\User;
use \Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $validator = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            // 'c_password' => 'required|same:password',
            'role' => 'required'
        ]);

        $input = $request->all();

        User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'role' => $input['role'],
            'password' => bcrypt($input['password'])
        ]);
        $user = User::create($input);

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
                'message' => 'User ' . $user['name'] . ' Successfuly Login'
            ]);
        } else {
            return response()->json([
                'success' => 'false',
                'message' => 'User and Password Wrong'
            ]);
            ;
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
}
