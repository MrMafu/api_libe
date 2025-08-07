<?php

namespace App\Http\Controllers\Temp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Api\ApiResponse;
use App\Models\BookCopy;
use App\Http\Resources\BookCopyResource;
use Illuminate\Support\Facades\Auth;

class BookCopyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookCopies = BookCopy::with('book')->get();
        $data = BookCopyResource::collection($bookCopies);
        return ApiResponse::success($data, "Book copies retrieved successfully.");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_id' => 'required|integer',
            'copy_code' => 'required|string',
            'condition' => 'required|string|in:good,damaged',
            'status' => 'required|string|in:available,borrowed,lost',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error("Validation error.", 422, $validator->errors());
        }

        if (!Auth::check() || !in_array(Auth::user()->role, ['librarian', 'admin'])) {
            return ApiResponse::error("Unauthorized", 403);
        }


        $data = $request->only(['book_id', 'copy_code', 'condition', 'status']);
        $bookCopy = BookCopy::create($data);
        $bookCopy->load('book');
        $data = new BookCopyResource($bookCopy);
        return ApiResponse::success($data, "Book copy created successfully.", 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bookCopy = BookCopy::with('book')->find($id);
        if (!$bookCopy) {
            return ApiResponse::error("Book copy not found.", 404);
        }

        $data = new BookCopyResource($bookCopy);
        return ApiResponse::success($data, "Book copy retrieved successfully.");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'copy_code' => 'sometimes|required|string',
            'condition' => 'sometimes|required|string|in:good,damaged',
            'status' => 'sometimes|required|string|in:available,borrowed,lost',
        ]);

        if (!Auth::check() || !in_array(Auth::user()->role, ['librarian', 'admin'])) {
            return ApiResponse::error("Unauthorized", 403);
        }


        if ($validator->fails()) {
            return ApiResponse::error("Validation error.", 422, $validator->errors());
        }

        $bookCopy = BookCopy::find($id);
        if (!$bookCopy) {
            return ApiResponse::error("Book copy not found.", 404);
        }
        $bookCopy->update($request->only(['copy_code', 'condition', 'status']));
        $bookCopy->load('book');
        $data = new BookCopyResource($bookCopy);
        return ApiResponse::success($data, "Book copy updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bookCopy = BookCopy::find($id);
        if (!$bookCopy) {
            return ApiResponse::error("Book copy not found.", 404);
        }

        if (!Auth::check() || !in_array(Auth::user()->role, ['librarian', 'admin'])) {
            return ApiResponse::error("Unauthorized", 403);
        }

        $bookCopy->delete();
        return ApiResponse::success(null, "Book copy deleted successfully.");
    }
}
