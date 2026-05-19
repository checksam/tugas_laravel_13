<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'prefers.json'])->group(function () {
    // Book Routes

    Route::apiResource('books', BookController::class)->only(['index', 'show']);
    Route::get('books/category/{category}', [BookController::class, 'getByCategory']);
    Route::get('books/available', [BookController::class, 'getAvailable']);

    // Book Admin Routes
    Route::middleware('admin')->group(function () {
        Route::apiResource('books', BookController::class)->only(['store', 'update', 'destroy']);
    });

    // Borrowing Routes
    Route::post('borrowings/borrow', [BorrowingController::class, 'borrow']);
    Route::post('borrowings/{borrowing}/return', [BorrowingController::class, 'return']);
    Route::get('borrowings/my-history', [BorrowingController::class, 'myHistory']);
    Route::get('borrowings/my-active', [BorrowingController::class, 'myActiveBorrowings']);

    // Borrowing Admin Routes
    Route::middleware('admin')->group(function () {
        Route::apiResource('borrowings', BorrowingController::class)->only(['index']);
        Route::get('borrowings/user/{userId}', [BorrowingController::class, 'userHistory']);
        Route::get('borrowings/overdue', [BorrowingController::class, 'getOverdue']);
        Route::get('borrowings/statistics', [BorrowingController::class, 'getStatistics']);
    });
});
