<?php

namespace App\Http\Controllers\Temp;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Api\ApiResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'password' => 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error("Validation error.", 422, $validator->errors());
        }

        $user = User::where('name', $request->name)->first();

        if (is_null($user) || !Hash::check($request->password, $user->password)) {
            return ApiResponse::error("Invalid credentials.", 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return ApiResponse::success(['token' => $token], "Login successful.");
    }

    public function logout(Request $request) {
        Auth::guard('sanctum')->user()->tokens()->delete();
        return ApiResponse::success([], "Logout successful.");
    }
}
