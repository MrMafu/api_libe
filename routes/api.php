<?php

use App\Http\Api\ApiResponse;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\GenerateOverdueFines;
use App\Http\Controllers\Temp\{
    AuthController,
    TopicController,
    SubTopicController,
    BookController,
    BookCopyController,
    BorrowingController,
    FineController
};

use App\Http\Controllers\Temp\UserController as TempUserController;

use Illuminate\Support\Facades\Route;

Artisan::command('fines:generate', function () {
    $this->call(GenerateOverdueFines::class);
});

// Public Routes
Route::post("login", [AuthController::class, "login"]);
Route::post("logout", [AuthController::class, "logout"])->middleware("auth:sanctum");
Route::get("books", [BookController::class, "index"]);
Route::get("bookcopies", [BookCopyController::class, "index"]);
Route::get("topics", [TopicController::class, "index"]);
Route::get("subtopics", [SubTopicController::class, "index"]);
Route::get("borrowings", [BorrowingController::class, "index"]);
Route::post('/fines/generate', [FineController::class, 'generateFines']);

Route::middleware("auth:sanctum")->group(function () {
    Route::apiResource("borrowings", BorrowingController::class);
    Route::post('/borrowings/{id}/return', [BorrowingController::class, 'returnBooks']);
});


Route::prefix("admin")->middleware("auth:sanctum", "admin-and-librarian-only")->group(function () {
    Route::apiResources([
        "users" => TempUserController::class,
        "topics" => TopicController::class,
        "subtopics" => SubTopicController::class,
        "books" => BookController::class,
        "bookcopies" => BookCopyController::class,
    ]);
});


// Unauthenticated response
Route::get("fallback", function () {
    return ApiResponse::error("Unauthorized.", 401);
})->name("login");
