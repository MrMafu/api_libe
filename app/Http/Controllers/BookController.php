<?php

namespace App\Http\Controllers;

use App\Http\Api\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::with("subTopic.topic")->get();
        $data = BookResource::collection($books);

        return ApiResponse::success($data, "Books retrieved successfully.");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "sub_topic_id"     => "required|integer|exists:sub_topics,id",
            "isbn"             => "required|string|max:13|unique:books,isbn",
            "cover"            => "required|image|max:2048",
            "title"            => "required|string|max:255",
            "language"         => "required|string|max:50",
            "num_of_pages"     => "required|integer|min:1",
            "author"           => "required|string|max:255",
            "publisher"        => "required|string|max:255",
            "publication_date" => "required|date",
            "price"            => "required|numeric|min:0",
        ]);

        if ($validator->fails()) {
            return ApiResponse::error("Validation error.", 422, $validator->errors());
        }
        if (!Auth::check() || !in_array(Auth::user()->role, ["librarian", "admin"])) {
            return ApiResponse::error("Unauthorized", 403);
        }

        $data = $request->only([
            "sub_topic_id",
            "isbn",
            "title",
            "language",
            "num_of_pages",
            "author",
            "publisher",
            "publication_date",
            "price"
        ]);

        if (!isset($data["price"]) || $data["price"] === "") {
            $data["price"] = 0;
        }

        if ($request->hasFile("cover")) {
            $coverFile = $request->file("cover");
            $path = $coverFile->store("covers", "public");
            $data["cover"] = $path;
        } else {
            $data["cover"] = null;
        }

        $book = Book::create($data);
        $book->load("subTopic.topic");

        return ApiResponse::success(new BookResource($book), "Book created successfully.", 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $book = Book::with("subTopic.topic")->find($id);
        if (!$book) {
            return ApiResponse::error("Book not found.", 404);
        }

        $data = new BookResource($book);
        return ApiResponse::success($data, "Book details retrieved successfully.");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            "sub_topic_id"     => "sometimes|required|integer|exists:sub_topics,id",
            "isbn"             => "sometimes|required|string|max:13|unique:books,isbn," . $id,
            "cover"            => "sometimes|image|max:2048",
            "title"            => "sometimes|required|string|max:255",
            "language"         => "sometimes|required|string|max:50",
            "num_of_pages"     => "sometimes|required|integer|min:1",
            "author"           => "sometimes|required|string|max:255",
            "publisher"        => "sometimes|required|string|max:255",
            "publication_date" => "sometimes|required|date",
            "price"            => "sometimes|required|numeric|min:0",
        ]);

        if ($validator->fails()) {
            return ApiResponse::error("Validation error.", 422, $validator->errors());
        }

        if (!Auth::check() || !in_array(Auth::user()->role, ["librarian", "admin"])) {
            return ApiResponse::error("Unauthorized", 403);
        }

        $book = Book::find($id);
        if (!$book) {
            return ApiResponse::error("Book not found.", 404);
        }

        $book->update($request->only([
            "sub_topic_id",
            "isbn",
            "title",
            "language",
            "num_of_pages",
            "author",
            "publisher",
            "publication_date",
            "price"
        ]));
        
        if ($request->hasFile("cover")) {
            $coverFile = $request->file("cover");
            $path = $coverFile->store("covers", "public");
            $book->cover = $path;
        }
        
        $book->save();
        $book->load("subTopic.topic");

        $data = new BookResource($book);
        return ApiResponse::success($data, "Book updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);
        if (!$book) {
            return ApiResponse::error("Book not found.", 404);
        }

        if (!Auth::check() || !in_array(Auth::user()->role, ["librarian", "admin"])) {
            return ApiResponse::error("Unauthorized", 403);
        }


        $book->delete();
        return ApiResponse::success(null, "Book deleted successfully.");
    }
}
