<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\BorrowingController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('books', BookController::class);
Route::resource('members', MemberController::class);
Route::resource('borrowings', BorrowingController::class);

Route::patch('borrowings/{borrowing}/return', [BorrowingController::class, 'return'])->name('borrowings.return');
Route::post('members/import-csv', [MemberController::class, 'importFromCsv'])->name('members.import-csv');
Route::post('members/check-nisn', [MemberController::class, 'checkNisn'])->name('members.check-nisn');
