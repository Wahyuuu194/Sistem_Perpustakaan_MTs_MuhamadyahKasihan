<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetsSyncService;

class CheckSheetsStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:sheets-structure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Google Sheets data structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memeriksa struktur data Google Sheets...');
        
        try {
            $syncService = new GoogleSheetsSyncService();
            $csvData = $syncService->fetchCsvData('https://docs.google.com/spreadsheets/d/e/2PACX-1vTV2yHxMdFkv1HIQDuKsceg8QhBoGCqYhy1oBkdzgsWa7jgYt8ophyPHSfa5ukpgRUw3h5Pw_T-0JED/pub?gid=404883502&single=true&output=csv');
            
            $this->info('Jumlah kolom di baris pertama: ' . count($csvData[0]));
            $this->info('Header: ' . implode(' | ', $csvData[0]));
            $this->info('Contoh data baris 1: ' . implode(' | ', $csvData[1]));
            $this->info('Contoh data baris 2: ' . implode(' | ', $csvData[2]));
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
