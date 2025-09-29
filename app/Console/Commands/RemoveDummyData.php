<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use App\Services\GoogleSheetsSyncService;

class RemoveDummyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:dummy-data {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove dummy data that exists in database but not in Google Sheets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mencari data dummy yang akan dihapus...');
        
        try {
            $syncService = new GoogleSheetsSyncService();
            $csvData = $syncService->fetchCsvData('https://docs.google.com/spreadsheets/d/e/2PACX-1vTV2yHxMdFkv1HIQDuKsceg8QhBoGCqYhy1oBkdzgsWa7jgYt8ophyPHSfa5ukpgRUw3h5Pw_T-0JED/pub?gid=404883502&single=true&output=csv');
            
            // Get all titles from Google Sheets
            $sheetsTitles = [];
            foreach ($csvData as $row) {
                if (count($row) >= 1 && !empty(trim($row[0]))) {
                    $sheetsTitles[] = trim($row[0]);
                }
            }
            
            $this->info("Jumlah data di Google Sheets: " . count($sheetsTitles));
            
            // Find books in database that are not in Google Sheets
            $dummyBooks = Book::whereNotIn('title', $sheetsTitles)->get();
            
            $this->info("Data dummy yang ditemukan: " . $dummyBooks->count());
            
            if ($dummyBooks->count() > 0) {
                $this->warn("\nData yang akan dihapus:");
                foreach ($dummyBooks as $book) {
                    $author = $book->author ?? 'NULL';
                    $this->line("- {$book->title} | {$author} | {$book->publisher}");
                }
                
                if ($this->option('dry-run')) {
                    $this->info("\n[DRY RUN] Data di atas akan dihapus jika command dijalankan tanpa --dry-run");
                } else {
                    $deletedCount = 0;
                    foreach ($dummyBooks as $book) {
                        $book->delete();
                        $deletedCount++;
                    }
                    $this->info("Berhasil menghapus {$deletedCount} data dummy!");
                }
            } else {
                $this->info("Tidak ada data dummy yang ditemukan!");
            }
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
