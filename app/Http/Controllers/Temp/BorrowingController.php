<?php

namespace App\Http\Controllers\Temp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Api\ApiResponse;
use App\Http\Resources\BorrowingResource;
use App\Http\Resources\BorrowingDetailResource;
use App\Models\BookCopy;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Fine;
use Illuminate\Support\Facades\Log;

class BorrowingController extends Controller
{

    protected function checkAndGenerateFines()
    {
        app(FineController::class)->checkOverdueFines();
    }

    public function index()
    {
        $this->checkAndGenerateFines();

        $borrowings = Borrowing::with(['user', 'borrowingDetails.book', 'borrowingDetails.bookCopy'])->get();
        $data = BorrowingResource::collection($borrowings);
        return ApiResponse::success($data, "Borrowings retrieved successfully.");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info("BorrowingController@store: Start");

        $validator = Validator::make($request->all(), [
            'due' => 'required|date',
            'books' => 'required|array',
            'books.*.book_id' => 'required|integer|exists:books,id',
            'books.*.book_copy_id' => 'required|string|exists:book_copies,id',
        ]);

        if ($validator->fails()) {
            Log::warning("Validation failed", $validator->errors()->toArray());
            return ApiResponse::error("Validation error", 422, $validator->errors());
        }

        $user = Auth::user();
        if (!$user || $user->role !== 'user') {
            Log::warning("Unauthorized user attempt", ['user_id' => optional($user)->id]);
            return ApiResponse::error("Unauthorized", 403);
        }

        $activeBorrowingsCount = Borrowing::where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->count();

        if ($activeBorrowingsCount >= 3) {
            Log::info("User has reached max active borrowings", ['user_id' => $user->id]);
            return ApiResponse::error("You can only borrow up to 3 active books.", 422);
        }

        $bookIds = array_column($request->books, 'book_id');
        if (count($bookIds) !== count(array_unique($bookIds))) {
            Log::info("Duplicate book_id detected", ['user_id' => $user->id, 'book_ids' => $bookIds]);
            return ApiResponse::error("You can only borrow one copy per book title.", 422);
        }

        try {
            DB::beginTransaction();

            $borrowing = Borrowing::create([
                'user_id' => $user->id,
                'due' => $request->due,
                'borrowed_at' => now(),
                'returned_at' => null,
                'status' => 'borrowed',
            ]);

            Log::info("Borrowing record created", ['borrowing_id' => $borrowing->id]);

            foreach ($request->books as $book) {
                Log::info("Checking book copy", $book);

                $copy = BookCopy::find($book['book_copy_id']);

                if (!$copy || $copy->status !== 'available') {
                    Log::warning("Book copy not available", [
                        'book_copy_id' => $book['book_copy_id'],
                        'found' => $copy !== null,
                        'status' => $copy->status ?? null
                    ]);

                    DB::rollBack();
                    return ApiResponse::error("Book copy ID {$book['book_copy_id']} is not available.", 422);
                }

                BorrowingDetail::create([
                    'borrowing_id' => $borrowing->id,
                    'book_id' => $book['book_id'],
                    'book_copy_id' => $book['book_copy_id'],
                    'returned_condition' => null,
                ]);

                $copy->update(['status' => 'borrowed']);
            }

            DB::commit();
            Log::info("Borrowing commit success", ['borrowing_id' => $borrowing->id]);

            $borrowing->load('user', 'borrowingDetails.book', 'borrowingDetails.bookCopy');

            return ApiResponse::success(new BorrowingResource($borrowing), "Borrowing created successfully.", 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Exception during borrowing creation", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return ApiResponse::error("Server error", 500, ['error' => $e->getMessage()]);
        }
    }

    public function returnBooks(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'books' => 'required|array',
            'books.*.book_copy_id' => 'required|exists:book_copies,id',
            'books.*.returned_condition' => 'required|in:good,damaged',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error("Validation error", 422, $validator->errors());
        }

        $borrowing = Borrowing::with('borrowingDetails')->find($id);
        if (!$borrowing || $borrowing->status !== 'borrowed') {
            return ApiResponse::error("Invalid borrowing record", 404);
        }

        try {
            DB::beginTransaction();

            $borrowing->update([
                'status' => 'returned',
                'returned_at' => now(),
            ]);

            foreach ($request->books as $book) {
                $detail = $borrowing->borrowingDetails()
                    ->where('book_copy_id', $book['book_copy_id'])
                    ->first();

                if ($detail) {
                    $detail->update([
                        'returned_condition' => $book['returned_condition'],
                    ]);
                }

                $copy = BookCopy::find($book['book_copy_id']);
                if ($copy) {
                    $copy->update(['status' => 'available']);
                }

                $fine = Fine::where('borrowing_id', $borrowing->id)
                    ->where('book_copy_id', $copy->id)
                    ->where('status', 'unpaid')
                    ->first();

                if($fine) {
                    $fine->update([
                        'status' => 'paid',
                        'paid_at' => now()
                    ]);
                }
            }

            DB::commit();
            
            return ApiResponse::success(null, "Books returned successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error("Server error", 500, ['error' => $e->getMessage()]);
        }
    }
}
