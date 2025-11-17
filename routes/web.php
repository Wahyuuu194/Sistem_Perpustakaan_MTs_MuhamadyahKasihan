<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\BookImportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SyncController;

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
    return view('books.import-multiple');
})->name('import-books');
Route::post('/import-books', [BookImportController::class, 'importExcel'])->name('import-books');
Route::post('/import-books-multiple', [BookImportController::class, 'importMultipleFiles'])->name('import-books-multiple');
Route::post('/preview-books', [BookImportController::class, 'previewExcel'])->name('preview-books');

// Sync routes (no auth required for testing)
Route::post('books/sync-google-sheets', [App\Http\Controllers\BookController::class, 'syncFromGoogleSheets'])
    ->middleware(\App\Http\Middleware\ForceJsonResponse::class)
    ->name('books.sync-google-sheets');

// Check books route
Route::get('/check-books', [App\Http\Controllers\CheckBooksController::class, 'index'])->name('check-books');

// Import routes (no auth required for testing)
Route::get('/members/import', [MemberController::class, 'showImportForm'])->name('members.import');
Route::post('members/sync-google-sheets', [MemberController::class, 'syncFromGoogleSheets'])->name('members.sync-google-sheets');

Route::get('/teachers/import', [TeacherController::class, 'showImportForm'])->name('teachers.import');
Route::post('teachers/sync-google-sheets', [TeacherController::class, 'syncFromGoogleSheets'])->name('teachers.sync-google-sheets');



// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('books', BookController::class);
    Route::resource('members', MemberController::class);
    Route::resource('teachers', TeacherController::class);
    Route::resource('borrowings', BorrowingController::class);
    
    Route::patch('borrowings/{borrowing}/return', [BorrowingController::class, 'return'])->name('borrowings.return');
    Route::post('members/check-nisn', [MemberController::class, 'checkNisn'])->name('members.check-nisn');
    Route::post('teachers/check-nip', [TeacherController::class, 'checkNip'])->name('teachers.check-nip');
    
    // Sync Routes (protected)
    Route::get('/sync', [SyncController::class, 'index'])->name('sync.index');
    
    // Book Import Routes (protected)
    Route::get('books/import-excel', [BookImportController::class, 'showImportForm'])->name('books.import-excel');
    Route::post('books/import-excel', [BookImportController::class, 'importExcel'])->name('books.import-excel');
    Route::post('books/preview-excel', [BookImportController::class, 'previewExcel'])->name('books.preview-excel');
    
});