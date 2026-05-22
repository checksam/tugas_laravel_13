<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingWebController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [BorrowingWebController::class, 'dashboard'])->name('dashboard');

    Route::resource('books', BookController::class);

    Route::get('/borrowings', [BorrowingWebController::class, 'index'])->name('borrowings.index');
    Route::post('/borrowings', [BorrowingWebController::class, 'store'])->name('borrowings.store');
    Route::get('/borrowings/history', [BorrowingWebController::class, 'history'])->name('borrowings.history');
    Route::post('/borrowings/{borrowing}/return', [BorrowingWebController::class, 'return'])->name('borrowings.return');
});
