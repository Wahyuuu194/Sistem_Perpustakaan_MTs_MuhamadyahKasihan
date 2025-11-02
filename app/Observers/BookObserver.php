<?php

namespace App\Observers;

use App\Models\Book;
use App\Services\GoogleSheetsSyncService;
use Illuminate\Support\Facades\Log;

class BookObserver
{
    /**
     * Handle the Book "created" event.
     */
    public function created(Book $book): void
    {
        if (env('GOOGLE_SHEETS_AUTO_SYNC', true)) {
            try {
                $syncService = app(GoogleSheetsSyncService::class);
                $syncService->pushBook($book);
                Log::info('Book auto-synced to Google Sheets: ' . $book->title);
            } catch (\Exception $e) {
                Log::error('Failed to auto-sync book to Google Sheets: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Handle the Book "updated" event.
     */
    public function updated(Book $book): void
    {
        if (env('GOOGLE_SHEETS_AUTO_SYNC', true)) {
            try {
                $syncService = app(GoogleSheetsSyncService::class);
                $syncService->pushBook($book);
                Log::info('Book auto-synced to Google Sheets: ' . $book->title);
            } catch (\Exception $e) {
                Log::error('Failed to auto-sync book to Google Sheets: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Handle the Book "deleted" event.
     */
    public function deleted(Book $book): void
    {
        // Optionally handle deletion in Google Sheets
        // For now, we'll leave it as the row might still exist but with quantity 0
    }
}

