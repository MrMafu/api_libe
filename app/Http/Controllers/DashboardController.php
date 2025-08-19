<?php

namespace App\Http\Controllers;

use App\Http\Api\ApiResponse;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Fine;

class DashboardController extends Controller
{
    public function index()
    {
        $booksCount = Book::count();
        $activeBorrowingsCount = Borrowing::where("status", "borrowed")->count();
        $pendingFinesCount = Fine::where("status", "unpaid")->count();
        
        $data = [
            "books_count" => $booksCount,
            "active_borrowings_count" => $activeBorrowingsCount,
            "pending_fines_count" => $pendingFinesCount,
        ];

        return ApiResponse::success($data, "Stats retrieved successfully.");
    }
}
