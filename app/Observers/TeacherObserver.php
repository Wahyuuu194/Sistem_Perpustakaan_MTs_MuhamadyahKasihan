<?php

namespace App\Observers;

use App\Models\Teacher;
use App\Services\GoogleSheetsSyncService;
use Illuminate\Support\Facades\Log;

class TeacherObserver
{
    /**
     * Handle the Teacher "created" event.
     */
    public function created(Teacher $teacher): void
    {
        if (env('GOOGLE_SHEETS_AUTO_SYNC', true)) {
            try {
                $syncService = app(GoogleSheetsSyncService::class);
                $syncService->pushTeacher($teacher);
                Log::info('Teacher auto-synced to Google Sheets: ' . $teacher->name);
            } catch (\Exception $e) {
                Log::error('Failed to auto-sync teacher to Google Sheets: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Handle the Teacher "updated" event.
     */
    public function updated(Teacher $teacher): void
    {
        if (env('GOOGLE_SHEETS_AUTO_SYNC', true)) {
            try {
                $syncService = app(GoogleSheetsSyncService::class);
                $syncService->pushTeacher($teacher);
                Log::info('Teacher auto-synced to Google Sheets: ' . $teacher->name);
            } catch (\Exception $e) {
                Log::error('Failed to auto-sync teacher to Google Sheets: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Handle the Teacher "deleted" event.
     */
    public function deleted(Teacher $teacher): void
    {
        // Optionally handle deletion in Google Sheets
    }
}

