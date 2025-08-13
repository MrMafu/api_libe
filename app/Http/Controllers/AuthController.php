<?php

namespace App\Http\Controllers;

use App\Http\Api\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string",
            "password" => "required|string|min:5",
        ]);

        if ($validator->fails()) {
            return ApiResponse::error("Validation error.", 422, $validator->errors());
        }

        $user = User::where("name", $request->name)->first();

        if (is_null($user) || !Hash::check($request->password, $user->password)) {
            return ApiResponse::error("Invalid credentials.", 401);
        }

        $token = $user->createToken("api_token")->plainTextToken;

        $data = [
            "user" => new UserResource($user->makeHidden("password")),
            "token" => $token
        ];

        return ApiResponse::success($data, "Succesfully logged in.");
    }

    public function logout()
    {
        Auth::guard("sanctum")->user()->currentAccessToken()->delete();
        $cookie = cookie()->forget("api_token");

        return ApiResponse::success(null, "Successfully logged out.");
    }

    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return ApiResponse::error("Unauthenticated.", 401);
        }

        $data = new UserResource($user->makeHidden("password"));
        return ApiResponse::success($data, "User data retrieved successfully.");
    }
}
