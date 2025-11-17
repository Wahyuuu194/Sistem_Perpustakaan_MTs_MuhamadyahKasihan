<?php

namespace App\Observers;

use App\Models\Member;
use App\Services\GoogleSheetsSyncService;
use Illuminate\Support\Facades\Log;

class MemberObserver
{
    /**
     * Handle the Member "created" event.
     */
    public function created(Member $member): void
    {
        if (env('GOOGLE_SHEETS_AUTO_SYNC', true)) {
            try {
                $syncService = app(GoogleSheetsSyncService::class);
                $syncService->pushMember($member);
                Log::info('Member auto-synced to Google Sheets: ' . $member->name);
            } catch (\Exception $e) {
                Log::error('Failed to auto-sync member to Google Sheets: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Handle the Member "updated" event.
     */
    public function updated(Member $member): void
    {
        if (env('GOOGLE_SHEETS_AUTO_SYNC', true)) {
            try {
                $syncService = app(GoogleSheetsSyncService::class);
                $syncService->pushMember($member);
                Log::info('Member auto-synced to Google Sheets: ' . $member->name);
            } catch (\Exception $e) {
                Log::error('Failed to auto-sync member to Google Sheets: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Handle the Member "deleted" event.
     */
    public function deleted(Member $member): void
    {
        // Optionally handle deletion in Google Sheets
    }
}

