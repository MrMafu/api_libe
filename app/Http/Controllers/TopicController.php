<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;

class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::all();

        return response()->json([
            'message' => 'List of topics',
            'data' => $topics,
        ]);
    }

    public function show($id)
    {
        $topic = Topic::findOrFail($id);

        return response()->json([
            'message' => 'Details of topic with ID: ' . $id,
            'data' => $topic,
        ]);
    }


    public function store(Request $request)
    {
         if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $topic = Topic::create($data);

        return response()->json([
            'message' => 'Topic created successfully',
            'data' => $topic,
        ], 201);
    }

    public function update(Request $request, $id)
    {
         if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $topic = Topic::findOrFail($id);
        $topic->update($data);

        return response()->json([
            'message' => 'Topic updated successfully',
            'data' => $topic,
        ]);
    }


    public function destroy($id)
    {
         if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $topic = Topic::findOrFail($id);
        $topic->delete();

        return response()->json([
            'message' => 'Topic with ID: ' . $id . ' deleted successfully',
        ]);
    }
}
