<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IsbnController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ISBN API Routes
Route::prefix('isbn')->group(function () {
    // Cari ISBN untuk satu buku
    Route::post('/search', [IsbnController::class, 'searchIsbn']);
    
    // Cari ISBN untuk multiple books
    Route::post('/search-multiple', [IsbnController::class, 'searchMultipleIsbn']);
    
    // Validasi format ISBN
    Route::post('/validate', [IsbnController::class, 'validateIsbn']);
    
    // Update ISBN untuk buku tertentu
    Route::put('/update-book/{bookId}', [IsbnController::class, 'updateBookIsbn']);
    
    // Bulk update ISBN untuk multiple books
    Route::post('/bulk-update', [IsbnController::class, 'bulkUpdateIsbn']);
    
    // Get books yang belum memiliki ISBN
    Route::get('/books-without-isbn', [IsbnController::class, 'getBooksWithoutIsbn']);
});




