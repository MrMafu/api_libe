<?php

namespace App\Http\Controllers;

use App\Http\Api\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name"     => "required|string|exists:users,name",
            "password" => "required|string",
        ]);

        if ($validator->fails()) {
            return ApiResponse::error("Validation error.", 422, $validator->errors());
        }

        $user = User::where("name", $request->name)->first();
        if (!Hash::check($request->password, $user->password)) {
            return ApiResponse::error("Incorrect name or password.", 401);
        }

        Auth::login($user);
        $token = $user->createToken("api-token")->plainTextToken;

        $data = [
            "user"  => new UserResource($user->makeHidden("password")),
            "token" => $token,
        ];

        return ApiResponse::success($data, "Successfully logged in.");
    }

    public function logout(Request $request)
    {
        $user = Auth::guard("sanctum")->user();
        if ($user) {
            $request->user()->currentAccessToken()->delete();
        }

        return ApiResponse::success(null, "Successfully logged out.");
    }
}
