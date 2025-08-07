<?php

namespace App\Http\Controllers\Temp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubTopic;
use Illuminate\Support\Facades\Auth;
use App\Models\Topic;
use Illuminate\Support\Facades\Validator;
use App\Http\Api\ApiResponse;
use App\Http\Resources\SubTopicResource;

class SubTopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subTopics = SubTopic::with('topic')->get();
        $data = SubTopicResource::collection($subTopics);
        return ApiResponse::success($data, "Subtopics retrieved successfully.");
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'topic_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error("Validation error.", 422, $validator->errors());
        }

        if (!Auth::check() || !in_array(Auth::user()->role, ['librarian', 'admin'])) {
            return ApiResponse::error("Unauthorized", 403);
        }


        $topic = Topic::find($request->topic_id);
        if (!$topic) {
            return ApiResponse::error("Topic not found.", 404);
        }

        $subTopic = SubTopic::create($request->only(['name', 'topic_id']));
        $subTopic->load('topic');

        $data = new SubTopicResource($subTopic);
        return ApiResponse::success($data, "Subtopic created successfully.", 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subTopic = SubTopic::find($id);
        if (!$subTopic) {
            return ApiResponse::error("Subtopic not found.", 404);
        }

        $data = new SubTopicResource($subTopic);
        return ApiResponse::success($data, "Subtopic retrieved successfully.");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'topic_id' => 'required|integer',
        ]);

        if (!Auth::check() || !in_array(Auth::user()->role, ['librarian', 'admin'])) {
            return ApiResponse::error("Unauthorized", 403);
        }


        if ($validator->fails()) {
            return ApiResponse::error("Validation error.", 422, $validator->errors());
        }

        $subTopic = SubTopic::find($id);
        if (!$subTopic) {
            return ApiResponse::error("Subtopic not found.", 404);
        }

        $subTopic->update($request->only(['name', 'topic_id']));
        $subTopic->load('topic');

        $data = new SubTopicResource($subTopic);
        return ApiResponse::success($data, "Subtopic updated successfully.");
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subTopic = SubTopic::find($id);
        if (!$subTopic) {
            return ApiResponse::error("Subtopic not found.", 404);
        }

        if (!Auth::check() || !in_array(Auth::user()->role, ['librarian', 'admin'])) {
            return ApiResponse::error("Unauthorized", 403);
        }


        $subTopic->delete();

        return ApiResponse::success(null, "Subtopic deleted successfully.");
    }
}
