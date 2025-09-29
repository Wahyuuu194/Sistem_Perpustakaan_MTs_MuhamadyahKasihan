<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetsSyncService;

class TestSyncBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:sync-books';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sync books from Google Sheets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Google Sheets sync for books...');
        
        try {
            $syncService = new GoogleSheetsSyncService();
            $result = $syncService->syncBooks();
            
            $this->info('Sync completed!');
            $this->info('Imported: ' . $result['imported']);
            $this->info('Updated: ' . $result['updated']);
            $this->info('Total processed: ' . $result['total_processed']);
            
            if (!empty($result['errors'])) {
                $this->error('Errors found:');
                foreach ($result['errors'] as $error) {
                    $this->error('- ' . $error);
                }
            } else {
                $this->info('No errors found!');
            }
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
