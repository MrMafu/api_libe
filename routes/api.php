<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'LoginApi']);

Route::middleware('auth:sanctum')->group(function () {

    //Logut route
    Route::post('/logout', [AuthController::class, 'logoutApi']);

    // User routes
    Route::get('/users', [App\Http\Controllers\Api\UserController::class, 'index']);
    Route::post('/users', [App\Http\Controllers\Api\UserController::class, 'store']);
    Route::put('/users/{id}', [App\Http\Controllers\Api\UserController::class, 'update']);
    Route::delete('/users/{id}', [App\Http\Controllers\Api\UserController::class, 'destroy']);

    // Topic routes
    Route::get('/topics', [App\Http\Controllers\Api\Topics::class, 'index']);
    Route::get('/topics/{id}', [App\Http\Controllers\Api\Topics::class, 'show']);
    Route::post('/topics', [App\Http\Controllers\Api\Topics::class, 'store']);
    Route::put('/topics/{id}', [App\Http\Controllers\Api\Topics::class, 'update']);
    Route::delete('/topics/{id}', [App\Http\Controllers\Api\Topics::class, 'destroy']);

    // SubTopic routes
    Route::get('/subtopics', [App\Http\Controllers\Api\SubTopicsController::class, 'index']);
    Route::post('/subtopics', [App\Http\Controllers\Api\SubTopicsController::class, 'store']);
    Route::put('/subtopics/{id}', [App\Http\Controllers\Api\SubTopicsController::class, 'update']);
    Route::delete('/subtopics/{id}', [App\Http\Controllers\Api\SubTopicsController::class, 'destroy']);

    // Book routes
    Route::get('/books', [App\Http\Controllers\Api\BooksController::class, 'index']);
    Route::post('/books', [App\Http\Controllers\Api\BooksController::class, 'store']);
});
