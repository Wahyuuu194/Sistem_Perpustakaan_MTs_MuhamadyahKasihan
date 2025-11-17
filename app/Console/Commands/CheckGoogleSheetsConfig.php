<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetsSyncService;
use Illuminate\Support\Facades\Storage;

class CheckGoogleSheetsConfig extends Command
{
    protected $signature = 'sheets:check-config';
    protected $description = 'Check Google Sheets API configuration';

    public function handle()
    {
        $this->info('Memeriksa konfigurasi Google Sheets API...');
        $this->newLine();

        // Check Spreadsheet ID
        $spreadsheetId = env('GOOGLE_SHEETS_SPREADSHEET_ID');
        if (empty($spreadsheetId)) {
            $this->error('❌ GOOGLE_SHEETS_SPREADSHEET_ID belum dikonfigurasi di .env');
        } else {
            $this->info('✅ GOOGLE_SHEETS_SPREADSHEET_ID: ' . $spreadsheetId);
        }

        // Check credentials file
        $credentialsPath = env('GOOGLE_SHEETS_CREDENTIALS_PATH', 'google-credentials.json');
        $fullPath = storage_path('app/' . $credentialsPath);
        
        if (!file_exists($fullPath)) {
            $this->error('❌ File credentials tidak ditemukan di: ' . $fullPath);
        } else {
            $this->info('✅ File credentials ditemukan: ' . $fullPath);
            
            // Try to read and parse credentials
            try {
                $credentials = json_decode(file_get_contents($fullPath), true);
                if (isset($credentials['client_email'])) {
                    $this->info('   Service Account Email: ' . $credentials['client_email']);
                    $this->comment('   ⚠️  Pastikan email ini sudah diberikan akses ke Google Spreadsheet!');
                }
            } catch (\Exception $e) {
                $this->error('❌ File credentials tidak valid JSON: ' . $e->getMessage());
            }
        }

        // Try to connect
        try {
            $this->newLine();
            $this->info('Mencoba koneksi ke Google Sheets API...');
            
            $syncService = new GoogleSheetsSyncService();
            
            // Try to get spreadsheet info
            $service = $syncService->getService();
            $spreadsheet = $service->spreadsheets->get($spreadsheetId);
            
            $this->info('✅ Berhasil terhubung ke Google Sheets!');
            $this->info('   Judul Spreadsheet: ' . $spreadsheet->getProperties()->getTitle());
            
            $sheets = $spreadsheet->getSheets();
            $this->newLine();
            $this->info('Daftar Sheet:');
            foreach ($sheets as $sheet) {
                $props = $sheet->getProperties();
                $this->line('   - ' . $props->getTitle() . ' (GID: ' . $props->getSheetId() . ')');
            }
            
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Gagal terhubung: ' . $e->getMessage());
            $this->comment('   Pastikan:');
            $this->comment('   1. Spreadsheet ID benar');
            $this->comment('   2. Service account sudah diberikan akses ke spreadsheet');
            $this->comment('   3. File credentials valid');
        }
    }
}

