<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Fine;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FineController extends Controller
{
    public function checkOverdueFines()
    {
        $today = Carbon::now();

        $borrowings = Borrowing::where("status", "borrowed")
            ->where("due", "<", $today)
            ->with(["borrowingDetails.bookCopy"])
            ->get();

        foreach ($borrowings as $borrowing) {
            $daysLate = Carbon::parse($borrowing->due)->diffInDays(Carbon::now());

            if ($daysLate <= 0) {
                continue;
            }

            foreach ($borrowing->borrowingDetails as $detail) {
                $bookCopyId = $detail->book_copy_id;

                $existingFine = Fine::where("borrowing_id", $borrowing->id)
                    ->where("book_copy_id", $bookCopyId)
                    ->first();

                $amount = $daysLate * 5000;

                if (!$existingFine) {
                    Fine::create([
                        "user_id"      => $borrowing->user_id,
                        "borrowing_id" => $borrowing->id,
                        "book_copy_id" => $bookCopyId,
                        "amount"       => $amount,
                        "issued_at"    => $today,
                        "paid_at"      => now(),
                        "status"       => "unpaid",
                        "reason"       => "Late return ({$daysLate} days)",
                    ]);

                    Log::info("Fine created", [
                        "borrowing_id" => $borrowing->id,
                        "book_copy_id" => $bookCopyId,
                        "days_late"    => $daysLate,
                        "amount"       => $amount
                    ]);
                } else {
                    if ($existingFine->amount != $amount) {
                        $existingFine->update([
                            "amount"    => $amount,
                            "issued_at" => $today,
                            "reason"    => "Late return ({$daysLate} days)",
                        ]);

                        Log::info("Fine updated", [
                            "borrowing_id"   => $borrowing->id,
                            "book_copy_id"   => $bookCopyId,
                            "updated_amount" => $amount,
                        ]);
                    }
                }
            }
        }
    }
}
