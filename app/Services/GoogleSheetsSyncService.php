<?php

namespace App\Services;

use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GoogleSheetsSyncService
{
    private $client;
    private $service;
    private $spreadsheetId;
    private $initialized = false;
    
    public function __construct()
    {
        $this->spreadsheetId = env('GOOGLE_SHEETS_SPREADSHEET_ID');
    }
    
    /**
     * Initialize client (lazy loading)
     */
    private function ensureInitialized()
    {
        if ($this->initialized) {
            return;
        }
        
        if (empty($this->spreadsheetId)) {
            throw new \Exception('GOOGLE_SHEETS_SPREADSHEET_ID belum dikonfigurasi di file .env');
        }
        
        $this->initializeClient();
        $this->initialized = true;
    }
    
    /**
     * Get Google Sheets Service instance (for testing/debugging)
     */
    public function getService(): Google_Service_Sheets
    {
        $this->ensureInitialized();
        return $this->service;
    }
    
    /**
     * Get Spreadsheet ID
     */
    public function getSpreadsheetId(): string
    {
        return $this->spreadsheetId;
    }
    
    /**
     * Initialize Google Client dengan Service Account
     */
    private function initializeClient()
    {
        try {
            $credentialsPath = env('GOOGLE_SHEETS_CREDENTIALS_PATH', 'google-credentials.json');
            
            // Remove 'storage/app/' if included in path
            $credentialsPath = str_replace('storage/app/', '', $credentialsPath);
            $credentialsPath = str_replace('app/', '', $credentialsPath);
            
            $fullPath = storage_path('app/' . $credentialsPath);
            
            if (!file_exists($fullPath)) {
                // Try alternative paths
                $altPath = base_path($credentialsPath);
                if (file_exists($altPath)) {
                    $fullPath = $altPath;
                } else {
                    throw new \Exception('File credentials Google Sheets tidak ditemukan di: ' . $fullPath . ' atau ' . $altPath);
                }
            }
            
            if (!is_readable($fullPath)) {
                throw new \Exception('File credentials tidak bisa dibaca: ' . $fullPath);
            }
            
            $this->client = new Google_Client();
            $this->client->setApplicationName('Perpustakaan MTs Muhamadyah');
            $this->client->setScopes(Google_Service_Sheets::SPREADSHEETS);
            $this->client->setAccessType('offline');
            $this->client->setAuthConfig($fullPath);
            
            $this->service = new Google_Service_Sheets($this->client);
        } catch (\Google_Service_Exception $e) {
            $errorMessage = json_decode($e->getMessage(), true);
            $message = $errorMessage['error']['message'] ?? $e->getMessage();
            Log::error('Google Sheets API Initialization Error: ' . $message);
            throw new \Exception('Gagal menginisialisasi Google Sheets API: ' . $message);
        } catch (\Exception $e) {
            Log::error('Google Sheets API Initialization Error: ' . $e->getMessage());
            Log::error('Credentials path attempted: ' . ($fullPath ?? 'N/A'));
            throw new \Exception('Gagal menginisialisasi Google Sheets API: ' . $e->getMessage());
        }
    }
    
    /**
     * Get sheet name by GID
     */
    private function getSheetNameByGid(int $gid): string
    {
        $this->ensureInitialized();
        
        try {
            $spreadsheet = $this->service->spreadsheets->get($this->spreadsheetId);
            $sheets = $spreadsheet->getSheets();
            
            if (empty($sheets)) {
                throw new \Exception("Spreadsheet tidak memiliki sheet");
            }
            
            foreach ($sheets as $sheet) {
                $sheetId = $sheet->getProperties()->getSheetId();
                $sheetTitle = $sheet->getProperties()->getTitle();
                
                if ($sheetId == $gid) {
                    return $sheetTitle;
                }
            }
            
            // If GID not found, return first sheet name as fallback
            $firstSheet = $sheets[0];
            $firstSheetTitle = $firstSheet->getProperties()->getTitle();
            Log::warning("Sheet dengan GID {$gid} tidak ditemukan, menggunakan sheet pertama: {$firstSheetTitle}");
            return $firstSheetTitle;
        } catch (\Google_Service_Exception $e) {
            $errorMessage = json_decode($e->getMessage(), true);
            $message = $errorMessage['error']['message'] ?? $e->getMessage();
            Log::error('Get Sheet Name Error: ' . $message);
            throw new \Exception('Gagal mendapatkan nama sheet: ' . $message);
        } catch (\Exception $e) {
            Log::error('Get Sheet Name Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan nama sheet: ' . $e->getMessage());
        }
    }
    
    /**
     * Read data from Google Sheets
     */
    public function readData(string $range): array
    {
        $this->ensureInitialized();
        
        try {
            $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $response->getValues();
            
            if (empty($values)) {
                return [];
            }
            
            // Skip header row if exists
            return array_slice($values, 1);
        } catch (\Google_Service_Exception $e) {
            $errorMessage = json_decode($e->getMessage(), true);
            $message = $errorMessage['error']['message'] ?? $e->getMessage();
            Log::error('Google Sheets Read Error: ' . $message);
            throw new \Exception('Gagal membaca data dari Google Sheets: ' . $message);
        } catch (\Exception $e) {
            Log::error('Google Sheets Read Error: ' . $e->getMessage());
            throw new \Exception('Gagal membaca data dari Google Sheets: ' . $e->getMessage());
        }
    }
    
    /**
     * Append row to Google Sheets
     */
    public function appendRow(string $range, array $values): void
    {
        $this->ensureInitialized();
        
        try {
            $body = new Google_Service_Sheets_ValueRange([
                'values' => [$values]
            ]);
            
            $params = [
                'valueInputOption' => 'USER_ENTERED',
                'insertDataOption' => 'INSERT_ROWS'
            ];
            
            $this->service->spreadsheets_values->append(
                $this->spreadsheetId,
                $range,
                $body,
                $params
            );
        } catch (\Exception $e) {
            Log::error('Google Sheets Append Error: ' . $e->getMessage());
            throw new \Exception('Gagal menambahkan data ke Google Sheets: ' . $e->getMessage());
        }
    }
    
    /**
     * Update row in Google Sheets by finding matching value in first column
     */
    public function updateRow(string $range, string $searchValue, array $newValues): bool
    {
        $this->ensureInitialized();
        
        try {
            // Get sheet name from range (e.g., "Murid!A2:Z" -> "Murid")
            $parts = explode('!', $range);
            $sheetName = $parts[0];
            $dataRange = $parts[1] ?? 'A2:Z';
            
            // Read all data including header to find the row
            $fullRange = $sheetName . '!A2:' . $this->getColumnLetter(max(count($newValues), 10));
            $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $fullRange);
            $allData = $response->getValues();
            
            if (empty($allData)) {
                // No data, append instead
                $this->appendRow($range, $newValues);
                return false;
            }
            
            // Find row index
            $rowIndex = null;
            for ($i = 0; $i < count($allData); $i++) {
                if (isset($allData[$i][0]) && trim($allData[$i][0]) === trim($searchValue)) {
                    $rowIndex = $i + 2; // +2 because sheets are 1-indexed and we start from row 2
                    break;
                }
            }
            
            if ($rowIndex === null) {
                // Row not found, append instead
                $this->appendRow($range, $newValues);
                return false;
            }
            
            // Update the row
            $endColumn = $this->getColumnLetter(count($newValues));
            $updateRange = $sheetName . '!A' . $rowIndex . ':' . $endColumn . $rowIndex;
            
            $body = new Google_Service_Sheets_ValueRange([
                'values' => [$newValues]
            ]);
            
            $params = [
                'valueInputOption' => 'USER_ENTERED'
            ];
            
            $this->service->spreadsheets_values->update(
                $this->spreadsheetId,
                $updateRange,
                $body,
                $params
            );
            
            return true;
        } catch (\Exception $e) {
            Log::error('Google Sheets Update Error: ' . $e->getMessage());
            throw new \Exception('Gagal mengupdate data di Google Sheets: ' . $e->getMessage());
        }
    }
    
    /**
     * Get column letter from number (1 = A, 2 = B, etc.)
     */
    private function getColumnLetter(int $num): string
    {
        $letter = '';
        while ($num > 0) {
            $num--;
            $letter = chr(65 + ($num % 26)) . $letter;
            $num = intval($num / 26);
        }
        return $letter;
    }
    
    /**
     * Push Book to Google Sheets
     */
    public function pushBook(\App\Models\Book $book): bool
    {
        try {
            $gid = (int) env('GOOGLE_SHEETS_SHEET_ID_BUKU', 404883502);
            $sheetName = $this->getSheetNameByGid($gid);
            $range = $sheetName . '!A2:E';
            
            $values = [
                $book->title,
                $book->author ?? '',
                $book->publisher ?? '',
                $book->quantity ?? 1,
                $book->category ?? 'Buku Bacaan'
            ];
            
            return $this->updateRow($range, $book->title, $values);
        } catch (\Exception $e) {
            // Try with hardcoded name
            try {
                $range = 'Buku!A2:E';
                $values = [
                    $book->title,
                    $book->author ?? '',
                    $book->publisher ?? '',
                    $book->quantity ?? 1,
                    $book->category ?? 'Buku Bacaan'
                ];
                return $this->updateRow($range, $book->title, $values);
            } catch (\Exception $e2) {
                Log::error('Push Book Error: ' . $e2->getMessage());
                return false;
            }
        }
    }
    
    /**
     * Push Member to Google Sheets
     */
    public function pushMember(\App\Models\Member $member): bool
    {
        try {
            $gid = (int) env('GOOGLE_SHEETS_SHEET_ID_MURID', 0);
            $sheetName = $this->getSheetNameByGid($gid);
            $range = $sheetName . '!A2:C';
            
            $values = [
                $member->member_id,
                $member->name,
                $member->kelas ?? ''
            ];
            
            return $this->updateRow($range, $member->member_id, $values);
        } catch (\Exception $e) {
            // Try with hardcoded name
            try {
                $range = 'Murid!A2:C';
                $values = [
                    $member->member_id,
                    $member->name,
                    $member->kelas ?? ''
                ];
                return $this->updateRow($range, $member->member_id, $values);
            } catch (\Exception $e2) {
                Log::error('Push Member Error: ' . $e2->getMessage());
                return false;
            }
        }
    }
    
    /**
     * Push Teacher to Google Sheets
     */
    public function pushTeacher(\App\Models\Teacher $teacher): bool
    {
        try {
            $gid = (int) env('GOOGLE_SHEETS_SHEET_ID_GURU', 336865292);
            $sheetName = $this->getSheetNameByGid($gid);
            $range = $sheetName . '!A2:B';
            
            $values = [
                $teacher->teacher_id,
                $teacher->name
            ];
            
            return $this->updateRow($range, $teacher->teacher_id, $values);
        } catch (\Exception $e) {
            // Try with hardcoded name
            try {
                $range = 'Guru!A2:B';
                $values = [
                    $teacher->teacher_id,
                    $teacher->name
                ];
                return $this->updateRow($range, $teacher->teacher_id, $values);
            } catch (\Exception $e2) {
                Log::error('Push Teacher Error: ' . $e2->getMessage());
                return false;
            }
        }
    }
    
    /**
     * Sync student data from Google Sheets
     */
    public function syncStudents(): array
    {
        // Increase execution time limit for large syncs
        set_time_limit(300); // 5 minutes
        
        try {
            // Get sheet name by GID
            $gid = (int) env('GOOGLE_SHEETS_SHEET_ID_MURID', 0);
            $sheetName = $this->getSheetNameByGid($gid);
            $range = $sheetName . '!A2:C';
            $csvData = $this->readData($range);
        } catch (\Exception $e) {
            // Fallback to hardcoded name if GID fails
            $range = 'Murid!A2:C';
            try {
                $csvData = $this->readData($range);
            } catch (\Exception $e2) {
                return [
                    'imported' => 0,
                    'updated' => 0,
                    'deactivated' => 0,
                    'errors' => ['Error: ' . $e2->getMessage()],
                    'total_processed' => 0
                ];
            }
        }
        
        $imported = 0;
        $updated = 0;
        $deleted = 0;
        $errors = [];
        
        // Collect all student IDs from Google Sheets
        $sheetsStudentIds = [];
        
        // Disable model events to prevent auto-sync during bulk operations
        \App\Models\Member::withoutEvents(function() use ($csvData, &$imported, &$updated, &$deleted, &$errors, &$sheetsStudentIds) {
            foreach ($csvData as $index => $row) {
                try {
                    if (count($row) < 3) {
                        $errors[] = "Baris " . ($index + 2) . ": Data tidak lengkap";
                        continue;
                    }
                    
                    $nisn = trim($row[0]);
                    $name = trim($row[1]);
                    $class = trim($row[2] ?? '');
                    
                    if (empty($nisn) || empty($name)) {
                        $errors[] = "Baris " . ($index + 2) . ": NISN atau Nama kosong";
                        continue;
                    }
                    
                    // Add to sheets student IDs collection
                    $sheetsStudentIds[] = $nisn;
                    
                    // Check if student exists
                    $student = \App\Models\Member::where('member_id', $nisn)->first();
                    
                    if ($student) {
                        // Update existing student and mark as active
                        $student->update([
                            'name' => $name,
                            'kelas' => $class,
                            'status' => 'active',
                        ]);
                        $updated++;
                    } else {
                        // Create new student
                        \App\Models\Member::create([
                            'member_id' => $nisn,
                            'name' => $name,
                            'kelas' => $class,
                            'status' => 'active',
                            'registration_date' => now(),
                        ]);
                        $imported++;
                    }
                    
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }
        });
        
        // Delete students that are not in Google Sheets (also without events)
        \App\Models\Member::withoutEvents(function() use ($sheetsStudentIds, &$deleted, &$errors) {
            $studentsNotInSheets = \App\Models\Member::whereNotIn('member_id', $sheetsStudentIds)->get();
            
            foreach ($studentsNotInSheets as $student) {
                try {
                    $student->delete();
                    $deleted++;
                } catch (\Exception $e) {
                    $errors[] = "Gagal menghapus murid {$student->member_id}: " . $e->getMessage();
                }
            }
        });
        
        return [
            'imported' => $imported,
            'updated' => $updated,
            'deleted' => $deleted,
            'errors' => $errors,
            'total_processed' => count($csvData)
        ];
    }
    
    /**
     * Sync teacher data from Google Sheets
     */
    public function syncTeachers(): array
    {
        // Increase execution time limit for large syncs
        set_time_limit(300); // 5 minutes
        
        try {
            // Get sheet name by GID
            $gid = (int) env('GOOGLE_SHEETS_SHEET_ID_GURU', 336865292);
            $sheetName = $this->getSheetNameByGid($gid);
            $range = $sheetName . '!A2:B';
            $csvData = $this->readData($range);
        } catch (\Exception $e) {
            // Fallback to hardcoded name if GID fails
            $range = 'Guru!A2:B';
            try {
                $csvData = $this->readData($range);
            } catch (\Exception $e2) {
                return [
                    'imported' => 0,
                    'updated' => 0,
                    'deleted' => 0,
                    'errors' => ['Error: ' . $e2->getMessage()],
                    'total_processed' => 0
                ];
            }
        }
        
        $imported = 0;
        $updated = 0;
        $deleted = 0;
        $errors = [];
        
        // Collect all teacher IDs from Google Sheets
        $sheetsTeacherIds = [];
        
        // Disable model events to prevent auto-sync during bulk operations
        \App\Models\Teacher::withoutEvents(function() use ($csvData, &$imported, &$updated, &$deleted, &$errors, &$sheetsTeacherIds) {
            foreach ($csvData as $index => $row) {
                try {
                    if (count($row) < 2) {
                        $errors[] = "Baris " . ($index + 2) . ": Data tidak lengkap";
                        continue;
                    }
                    
                    $nip = trim($row[0]);
                    $name = trim($row[1]);
                    
                    if (empty($nip) || empty($name)) {
                        $errors[] = "Baris " . ($index + 2) . ": NIP atau Nama kosong";
                        continue;
                    }
                    
                    // Add to sheets teacher IDs collection
                    $sheetsTeacherIds[] = $nip;
                    
                    // Check if teacher exists
                    $teacher = \App\Models\Teacher::where('teacher_id', $nip)->first();
                    
                    if ($teacher) {
                        // Update existing teacher and mark as active
                        $teacher->update([
                            'name' => $name,
                            'status' => 'active',
                        ]);
                        $updated++;
                    } else {
                        // Create new teacher
                        \App\Models\Teacher::create([
                            'teacher_id' => $nip,
                            'name' => $name,
                            'status' => 'active',
                            'registration_date' => now(),
                        ]);
                        $imported++;
                    }
                    
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }
        });
        
        // Delete teachers that are not in Google Sheets (also without events)
        \App\Models\Teacher::withoutEvents(function() use ($sheetsTeacherIds, &$deleted, &$errors) {
            $teachersNotInSheets = \App\Models\Teacher::whereNotIn('teacher_id', $sheetsTeacherIds)->get();
            
            foreach ($teachersNotInSheets as $teacher) {
                try {
                    $teacher->delete();
                    $deleted++;
                } catch (\Exception $e) {
                    $errors[] = "Gagal menghapus guru {$teacher->teacher_id}: " . $e->getMessage();
                }
            }
        });
        
        return [
            'imported' => $imported,
            'updated' => $updated,
            'deleted' => $deleted,
            'errors' => $errors,
            'total_processed' => count($csvData)
        ];
    }

    /**
     * Sync books from Google Sheets
     */
    public function syncBooks(): array
    {
        // Increase execution time limit for large syncs
        set_time_limit(300); // 5 minutes
        
        try {
            // Get sheet name by GID
            $gid = (int) env('GOOGLE_SHEETS_SHEET_ID_BUKU', 404883502);
            $sheetName = $this->getSheetNameByGid($gid);
            $range = $sheetName . '!A2:E';
            $csvData = $this->readData($range);
        } catch (\Exception $e) {
            // Fallback to hardcoded name if GID fails
            $range = 'Buku!A2:E';
            try {
                $csvData = $this->readData($range);
            } catch (\Exception $e2) {
                return [
                    'imported' => 0,
                    'updated' => 0,
                    'deleted' => 0,
                    'errors' => ['Error: ' . $e2->getMessage()],
                    'total_processed' => 0
                ];
            }
        }
        
        $imported = 0;
        $updated = 0;
        $deleted = 0;
        $errors = [];
        
        // Collect all book titles from Google Sheets
        $sheetsBookTitles = [];
        
        // Disable model events to prevent auto-sync during bulk operations
        \App\Models\Book::withoutEvents(function() use ($csvData, &$imported, &$updated, &$deleted, &$errors, &$sheetsBookTitles) {
            foreach ($csvData as $index => $row) {
                try {
                    if (count($row) < 5) {
                        $errors[] = "Baris " . ($index + 2) . ": Data tidak lengkap (minimal 5 kolom)";
                        continue;
                    }
                    
                    $title = trim($row[0]);
                    $author = trim($row[1] ?? '');
                    $publisher = trim($row[2] ?? '');
                    $quantity = is_numeric($row[3] ?? 1) ? (int)$row[3] : 1;
                    $category = trim($row[4] ?? 'Buku Bacaan');
                    
                    if (empty($title)) {
                        $errors[] = "Baris " . ($index + 2) . ": Judul kosong";
                        continue;
                    }
                    
                    // Add to sheets book titles collection
                    $sheetsBookTitles[] = $title;
                    
                    // Author can be empty, but if provided, use it
                    if (empty($author)) {
                        $author = null;
                    }
                    
                    // Normalize category to match system categories
                    $category = $this->normalizeCategory($category);
                    
                    // Check if book exists
                    $book = \App\Models\Book::where('title', $title)->first();
                    
                    if ($book) {
                        // Update existing book and mark as active
                        $hasChanges = false;
                        $updateData = [];
                        
                        if ($book->publisher !== $publisher) {
                            $updateData['publisher'] = $publisher;
                            $hasChanges = true;
                        }
                        
                        if ($book->quantity != $quantity) {
                            $updateData['quantity'] = $quantity;
                            $hasChanges = true;
                        }
                        
                        if ($book->category !== $category) {
                            $updateData['category'] = $category;
                            $hasChanges = true;
                        }
                        
                        if ($book->title !== $title) {
                            $updateData['title'] = $title;
                            $hasChanges = true;
                        }
                        
                        $currentAuthor = $book->author;
                        if ($currentAuthor !== $author) {
                            $updateData['author'] = $author;
                            $hasChanges = true;
                        }
                        
                        if ($book->quantity <= 0) {
                            $updateData['quantity'] = $quantity;
                            $hasChanges = true;
                        }
                        
                        if ($hasChanges) {
                            $book->update($updateData);
                            $updated++;
                        }
                    } else {
                        // Create new book
                        \App\Models\Book::create([
                            'title' => $title,
                            'author' => $author,
                            'publisher' => $publisher,
                            'quantity' => $quantity,
                            'available_quantity' => $quantity,
                            'category' => $category,
                            'isbn' => $this->generateISBN(),
                            'status' => 'available',
                            'registration_date' => now(),
                        ]);
                        $imported++;
                    }
                    
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }
        });
        
        // Delete books that are not in Google Sheets (also without events)
        \App\Models\Book::withoutEvents(function() use ($sheetsBookTitles, &$deleted, &$errors) {
            $booksNotInSheets = \App\Models\Book::whereNotIn('title', $sheetsBookTitles)->get();
            
            foreach ($booksNotInSheets as $book) {
                try {
                    $book->delete();
                    $deleted++;
                } catch (\Exception $e) {
                    $errors[] = "Gagal menghapus buku {$book->title}: " . $e->getMessage();
                }
            }
        });
        
        return [
            'imported' => $imported,
            'updated' => $updated,
            'deleted' => $deleted,
            'errors' => $errors,
            'total_processed' => count($csvData)
        ];
    }
    
    /**
     * Normalize category to match system categories
     */
    private function normalizeCategory(string $category): string
    {
        $category = strtolower(trim($category));
        
        $categoryMap = [
            'non-fiksi' => 'non-fiksi',
            'fiksi' => 'fiksi',
            'buku paket' => 'Buku Paket',
            'buku bacaan' => 'Buku Pelajaran untuk siswa',
            'metode membaca & menulis al-qur\'an' => 'metode membaca & menulis al-Qur\'an',
            'kamus' => 'kamus',
            'ensiklopedia' => 'ensiklopedia',
            'ict (information computer technology)' => 'ICT (Information Computer Technology)',
            'buku guru' => 'Buku Guru',
            'buku pelajaran untuk siswa' => 'Buku Pelajaran untuk siswa',
            'visual ilmu dan pengetahuan populer' => 'Visual Ilmu dan Pengetahuan Populer',
            'ensiklopedia eksperimen sains lengkap' => 'Ensiklopedia Eksperimen Sains Lengkap',
            'terjemahan kitab' => 'Terjemahan Kitab',
            'buku pegangan' => 'Buku Pegangan',
            'edukasi' => 'Edukasi',
        ];
        
        return $categoryMap[$category] ?? 'non-fiksi';
    }

    /**
     * Generate simple ISBN
     */
    private function generateISBN(): string
    {
        return '978-' . rand(100, 999) . '-' . rand(10, 99) . '-' . rand(100000, 999999) . '-' . rand(0, 9);
    }
}
