<?php

namespace App\Http\Controllers;

use App\Http\Api\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $topics = Topic::all();
        $data = TopicResource::collection($topics);

        return ApiResponse::success($data, "Topics retrieved successfully.");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255",
        ]);

        if ($validator->fails()) {
            return ApiResponse::error("Validation error.", 422, $validator->errors());
        }

        if (!Auth::check() || !in_array(Auth::user()->role, ["librarian", "admin"])) {
            return ApiResponse::error("Unauthorized", 403);
        }

        $topic = Topic::create([
            "name" => $request->name,
        ]);

        $data = new TopicResource($topic);
        return ApiResponse::success($data, "Topic created successfully.", 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $topic = Topic::find($id);
        if (!$topic) {
            return ApiResponse::error("Topic not found.", 404);
        }

        $topic = new TopicResource($topic);
        return ApiResponse::success($topic, "Topic retrieved successfully.");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255",
        ]);

        if ($validator->fails()) {
            return ApiResponse::error("Validation error.", 422, $validator->errors());
        }

        if (!Auth::check() || !in_array(Auth::user()->role, ["librarian", "admin"])) {
            return ApiResponse::error("Unauthorized", 403);
        }

        $topic = Topic::find($id);
        if (!$topic) {
            return ApiResponse::error("Topic not found.", 404);
        }

        $topic->update([
            "name" => $request->name,
        ]);

        return ApiResponse::success(new TopicResource($topic), "Topic updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $topic = Topic::find($id);
        if (!$topic) {
            return ApiResponse::error("Topic not found.", 404);
        };

        if (!Auth::check() || !in_array(Auth::user()->role, ["librarian", "admin"])) {
            return ApiResponse::error("Unauthorized", 403);
        }

        $topic->delete();
        return ApiResponse::success([], "Topic deleted successfully.");
    }
}
