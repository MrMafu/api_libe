<?php

use App\Http\Api\ApiResponse;
use App\Http\Controllers\{
    AuthController,
    UserController,
    BookController,
    TopicController,
    SubTopicController,
};
use App\Http\Controllers\Temp\UserController as TempUserController;

use Illuminate\Support\Facades\Route;

// Public Routes
Route::post("login", [AuthController::class, "login"]);
Route::post("logout", [AuthController::class, "logout"])->middleware("auth:sanctum");

Route::middleware("auth:sanctum")->group(function() {
    // 
});

Route::prefix("admin")->middleware("auth:sanctum", "admin-and-librarian-only")->group(function() {
    Route::apiResources([
        "users" => TempUserController::class,
    ]);

    // User routes
    // Route::get("/users", [UserController::class, "index"]);
    // Route::post("/users", [UserController::class, "store"]);
    // Route::put("/users/{id}", [UserController::class, "update"]);
    // Route::delete("/users/{id}", [UserController::class, "destroy"]);
    
    // Topic routes
    Route::get("/topics", [TopicController::class, "index"]);
    Route::get("/topics/{id}", [TopicController::class, "show"]);
    Route::post("/topics", [TopicController::class, "store"]);
    Route::put("/topics/{id}", [TopicController::class, "update"]);
    Route::delete("/topics/{id}", [TopicController::class, "destroy"]);
    
    // SubTopic routes
    Route::get("/subtopics", [SubTopicController::class, "index"]);
    Route::post("/subtopics", [SubTopicController::class, "store"]);
    Route::put("/subtopics/{id}", [SubTopicController::class, "update"]);
    Route::delete("/subtopics/{id}", [SubTopicController::class, "destroy"]);
    
    // Book routes
    Route::get("/books", [BookController::class, "index"]);
    Route::post("/books", [BookController::class, "store"]);
});

// Unauthenticated response
Route::get("fallback", function () {
    return ApiResponse::error("Unauthorized.", 401);
})->name("login");