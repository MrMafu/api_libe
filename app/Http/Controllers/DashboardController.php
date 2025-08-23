<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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

        $now = Carbon::now();
        // Last 7 days (daily)
        $last7Days = [];
        // Iterate from oldest to newest (6 days ago -> today)
        for ($i = 0; $i < 7; $i++) {
            $date = (clone $now)->subDays(6 - $i);
            $start = (clone $date)->startOfDay();
            $end = (clone $date)->endOfDay();
            
            $count = Borrowing::whereBetween('borrowed_at', [
                $start->toDateTimeString(),
                $end->toDateTimeString()
                ])->where("status", "borrowed")->count();
                
                $last7Days[] = [
                    'date'  => $date->toDateString(),
                    'label' => $date->format('M j'),
                    'count' => $count,
                ];
            }
            
            // Last 30 days (daily)
            $last30Days = [];
            // Iterate from oldest to newest (29 days ago -> today)
            for ($i = 0; $i < 30; $i++) {
                $date = (clone $now)->subDays(29 - $i);
                $start = (clone $date)->startOfDay();
                $end = (clone $date)->endOfDay();
                
                $count = Borrowing::whereBetween('borrowed_at', [
                    $start->toDateTimeString(),
                $end->toDateTimeString()
                ])->where("status", "borrowed")->count();
                
                $last30Days[] = [
                    'date'  => $date->toDateString(),
                    'label' => $date->format('M j'),
                    'count' => $count,
                ];
            }
            
            $data = [
                "books_count" => $booksCount,
                "active_borrowings_count" => $activeBorrowingsCount,
                "pending_fines_count" => $pendingFinesCount,
                "last_7_days" => $last7Days,
                "last_30_days" => $last30Days,
            ];
            
            return ApiResponse::success($data, "Stats retrieved successfully.");
        }
        
        // $weeksBack = 4;
        // $endOfThisWeek = (clone $now)->endOfWeek();
        // $startOfPeriod = (clone $endOfThisWeek)->subWeeks($weeksBack - 1)->startOfWeek();

        // $weeklyActivity = [];

        // for ($i = 0; $i < $weeksBack; $i++) {
        //     $start = (clone $startOfPeriod)->addWeeks($i)->startOfWeek();
        //     $end = (clone $start)->endOfWeek();

        //     $count = Borrowing::whereBetween('borrowed_at', [
        //         $start->toDateTimeString(),
        //         $end->toDateTimeString()
        //     ])->where("status", "borrowed")->count();

        //     $weeklyActivity[] = [
        //         'start' => $start->toDateString(),
        //         'end'   => $end->toDateString(),
        //         'label' => $start->format('M j') . ' - ' . $end->format('M j'),
        //         'count' => $count,
        //     ];
        // }
}
