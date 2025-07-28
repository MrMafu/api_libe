<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;
use App\Models\SubTopic;

class BookController extends Controller
{
    public function index() {
        // Isi nya ntar
    }

    public function store(Request $request) {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'isbn' => 'required|string|max:13',
            'cover' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'title' => 'required|string|max:255',
            'sub_topic_id' => 'required|integer',
            'price' => 'required|numeric',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'language' => 'required|string|max:50',
            'num_of_pages' => 'required|integer',
            'publication_date' => 'required|date',
        ]);

        $subTopic = SubTopic::find($request->sub_topic_id);
        if (!$subTopic) {
            return response()->json(['message' => 'Subtopic not found'], 404);
        }

        $coverPath = $request->file('cover')->store('covers', 'public');

        $book = new Book();
        $book->isbn = $request->isbn;
        $book->cover = $request->file('cover')->store('covers', 'public');
        $book->title = $request->title;
        $book->sub_topic_id = $request->sub_topic_id;
        $book->author = $request->author;
        $book->publisher = $request->publisher;
        $book->language = $request->language;
        $book->num_of_pages = $request->num_of_pages;
        $book->publication_date = $request->publication_date;
        $book->save();

        return response()->json([
            'message' => 'Book created successfully',
            'data' => $book,
        ], 201);
    }
}
