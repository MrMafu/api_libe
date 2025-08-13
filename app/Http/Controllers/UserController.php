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

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        $data = UserResource::collection($users);

        return ApiResponse::success($data, "Users retrieved successfully.");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name"     => "required|string|unique:users,name",
            "password" => "required|string|min:5",
            "role"     => "required|in:user,librarian,admin",
        ]);

        if ($validator->fails()) {
            return ApiResponse::error("Validation error.", 422, $validator->errors());
        }

        $user = Auth::user();
        if (!$user || $user->role !== "admin") {
            return ApiResponse::error("Forbidden.", 403);
        }

        $user = User::create([
            "name"     => $request->name,
            "password" => Hash::make($request->password),
            "role"     => $request->role,
        ]);

        $data = new UserResource($user);
        return ApiResponse::success($data, "User created successfully.", 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return ApiResponse::error("User not found", 404);
        }

        $data = new UserResource($user);
        return ApiResponse::success($data, "User details retrieved.");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return ApiResponse::error("User not found.", 404);
        }

        if (!Auth::check() || Auth::user()->role !== "admin") {
            return ApiResponse::error("Unauthorized", 403);
        }

        $validator = Validator::make($request->all(), [
            "name"     => "sometimes|required|string|unique:users,name,{$user->id}",
            "password" => "sometimes|nullable|string|min:5",
            "role"     => "sometimes|required|in:user,librarian,admin",
        ]);

        if ($validator->fails()) {
            return ApiResponse::error("Validation error.", 422, $validator->errors());
        }

        $userData = $validator->validated();
        if (!empty($userData["password"])) {
            $userData["password"] = Hash::make($userData["password"]);
        } else {
            unset($userData["password"]);
        }

        $user->update($userData);
        $data = new UserResource($user);
        return ApiResponse::success($data, "User updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return ApiResponse::error("User not found", 404);
        }

        if (!Auth::check() || Auth::user()->role !== "admin") {
            return ApiResponse::error("Unauthorized", 403);
        }

        $user->delete();
        return ApiResponse::noContent();
    }
}
