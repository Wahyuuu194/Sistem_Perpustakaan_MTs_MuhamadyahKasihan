<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use App\Services\GoogleSheetsSyncService;

class AnalyzeMissingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analyze:missing-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze missing data between Google Sheets and database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Menganalisis data yang hilang...');
        
        try {
            $syncService = new GoogleSheetsSyncService();
            $csvData = $syncService->fetchCsvData('https://docs.google.com/spreadsheets/d/e/2PACX-1vTV2yHxMdFkv1HIQDuKsceg8QhBoGCqYhy1oBkdzgsWa7jgYt8ophyPHSfa5ukpgRUw3h5Pw_T-0JED/pub?gid=404883502&single=true&output=csv');
            
            $this->info("Total data di Google Sheets: " . count($csvData));
            
            $missingData = [];
            $processedCount = 0;
            
            foreach ($csvData as $index => $row) {
                if (count($row) < 5) {
                    $this->warn("Baris " . ($index + 1) . ": Data tidak lengkap - " . implode(' | ', $row));
                    continue;
                }
                
                $title = trim($row[0]);
                $author = trim($row[1]);
                $publisher = trim($row[2] ?? '');
                
                if (empty($title)) {
                    $this->warn("Baris " . ($index + 1) . ": Judul kosong - " . implode(' | ', $row));
                    continue;
                }
                
                // Check if this book exists in database
                $book = Book::where('title', $title)->first();
                
                if (!$book) {
                    $missingData[] = [
                        'row' => $index + 1,
                        'title' => $title,
                        'author' => $author,
                        'publisher' => $publisher
                    ];
                }
                
                $processedCount++;
            }
            
            $this->info("Data yang diproses: {$processedCount}");
            $this->info("Data yang hilang: " . count($missingData));
            
            if (count($missingData) > 0) {
                $this->warn("\nData yang hilang di database:");
                foreach (array_slice($missingData, 0, 10) as $missing) {
                    $this->line("Baris {$missing['row']}: {$missing['title']} | {$missing['author']} | {$missing['publisher']}");
                }
                
                if (count($missingData) > 10) {
                    $this->line("... dan " . (count($missingData) - 10) . " data lainnya");
                }
            }
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
