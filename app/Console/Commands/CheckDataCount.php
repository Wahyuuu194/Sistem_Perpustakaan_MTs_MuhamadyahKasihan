<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use App\Services\GoogleSheetsSyncService;

class CheckDataCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:data-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check data count between Google Sheets and database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memeriksa jumlah data...');
        
        // Check database count
        $dbCount = Book::count();
        $this->info("Jumlah buku di database: {$dbCount}");
        
        // Check Google Sheets count
        try {
            $syncService = new GoogleSheetsSyncService();
            $csvData = $syncService->fetchCsvData('https://docs.google.com/spreadsheets/d/e/2PACX-1vTV2yHxMdFkv1HIQDuKsceg8QhBoGCqYhy1oBkdzgsWa7jgYt8ophyPHSfa5ukpgRUw3h5Pw_T-0JED/pub?gid=404883502&single=true&output=csv');
            $sheetsCount = count($csvData);
            $this->info("Jumlah data di Google Sheets: {$sheetsCount}");
            
            $difference = $sheetsCount - $dbCount;
            $this->info("Selisih: {$difference}");
            
            if ($difference > 0) {
                $this->warn("Ada {$difference} data di Google Sheets yang belum ada di database");
            } elseif ($difference < 0) {
                $this->warn("Ada " . abs($difference) . " data di database yang tidak ada di Google Sheets");
            } else {
                $this->info("Data sudah sinkron!");
            }
            
            // Show some examples from database
            $this->info("\nContoh data terakhir di database:");
            $lastBooks = Book::orderBy('id', 'desc')->take(5)->get(['title', 'author', 'publisher']);
            foreach($lastBooks as $book) {
                $author = $book->author ?? 'NULL';
                $this->line("- {$book->title} | {$author} | {$book->publisher}");
            }
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
