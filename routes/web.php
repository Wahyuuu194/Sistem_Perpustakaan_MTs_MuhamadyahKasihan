<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\BookImportController;
use App\Http\Controllers\AuthController;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Import routes (no auth required for testing)
Route::get('/import-books', function() {
    return view('books.import-simple');
})->name('import-books');
Route::post('/import-books', [BookImportController::class, 'importExcel'])->name('import-books');
Route::post('/preview-books', [BookImportController::class, 'previewExcel'])->name('preview-books');

// Check books route
Route::get('/check-books', [App\Http\Controllers\CheckBooksController::class, 'index'])->name('check-books');

// Import routes (no auth required for testing)
Route::get('/members/import', [MemberController::class, 'showImportForm'])->name('members.import');
Route::get('/teachers/import', [TeacherController::class, 'showImportForm'])->name('teachers.import');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('books', BookController::class);
    Route::resource('members', MemberController::class);
    Route::resource('teachers', TeacherController::class);
    Route::resource('borrowings', BorrowingController::class);
    
    Route::patch('borrowings/{borrowing}/return', [BorrowingController::class, 'return'])->name('borrowings.return');
    Route::post('members/import-csv', [MemberController::class, 'importFromCsv'])->name('members.import-csv');
    Route::post('members/check-nisn', [MemberController::class, 'checkNisn'])->name('members.check-nisn');
    Route::post('teachers/import-csv', [TeacherController::class, 'importFromCsv'])->name('teachers.import-csv');
    Route::post('teachers/check-nip', [TeacherController::class, 'checkNip'])->name('teachers.check-nip');
    
    // Book Import Routes
    Route::get('books/import-excel', [BookImportController::class, 'showImportForm'])->name('books.import-excel');
    Route::post('books/import-excel', [BookImportController::class, 'importExcel'])->name('books.import-excel');
    Route::post('books/preview-excel', [BookImportController::class, 'previewExcel'])->name('books.preview-excel');
});
