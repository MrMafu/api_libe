<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubTopic;
use Illuminate\Support\Facades\Auth;
use App\Models\Topic;

class SubTopicController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'List of subtopics',
            'data' => SubTopic::all(),
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'topic_id' => 'required|integer',
        ]);

        $topic = Topic::find($request->topic_id);
        if (!$topic) {
            return response()->json(['message' => 'Topic not found'], 404);
        }

        $subTopic = SubTopic::create($request->only(['name', 'topic_id']));

        return response()->json([
            'message' => 'Subtopic created successfully',
            'data' => $subTopic,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $subTopic = SubTopic::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'topic_id' => 'sometimes|required|integer',
        ]);

        if ($request->has('name')) {
            $subTopic->name = $request->name;
        }
        if ($request->has('topic_id')) {
            $topic = Topic::find($request->topic_id);
            if (!$topic) {
                return response()->json(['message' => 'Topic not found'], 404);
            }
            $subTopic->topic_id = $request->topic_id;
        }

        $subTopic->save();

        return response()->json([
            'message' => 'Subtopic updated successfully',
            'data' => $subTopic,
        ]);
    }

    public function destroy($id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $subTopic = SubTopic::findOrFail($id);
        $subTopic->delete();

        return response()->json([
            'message' => 'Subtopic deleted successfully',
        ]);
    }
}
