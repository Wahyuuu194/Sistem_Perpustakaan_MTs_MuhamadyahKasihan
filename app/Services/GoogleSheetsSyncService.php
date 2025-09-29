<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleSheetsSyncService
{
    /**
     * Fetch CSV data from Google Sheets public URL
     */
    public function fetchCsvData(string $url): array
    {
        try {
            // Append cache-buster to ensure we always fetch the latest published CSV
            $cacheBuster = (string) round(microtime(true) * 1000);
            $finalUrl = $url . (str_contains($url, '?') ? '&' : '?') . 'cb=' . $cacheBuster;

            $response = Http::timeout(30)
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ])
                ->retry(2, 200)
                ->get($finalUrl);
            
            if (!$response->successful()) {
                throw new \Exception("Gagal mengambil data dari Google Sheets. Status: " . $response->status());
            }
            
            $csvContent = $response->body();
            
            if (empty($csvContent)) {
                throw new \Exception("Data dari Google Sheets kosong");
            }
            
            return $this->parseCsv($csvContent);
            
        } catch (\Exception $e) {
            // Log error if Laravel is available
            if (class_exists('Illuminate\Support\Facades\Log')) {
                \Illuminate\Support\Facades\Log::error('Google Sheets Sync Error: ' . $e->getMessage());
            }
            throw new \Exception("Error: " . $e->getMessage());
        }
    }
    
    /**
     * Parse CSV content into array
     */
    private function parseCsv(string $csvContent): array
    {
        $lines = explode("\n", trim($csvContent));
        $data = [];
        
        // Skip header row
        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (empty($line)) continue;
            
            // Parse CSV line (handle commas in data)
            $row = str_getcsv($line);
            if (count($row) >= 2) { // Minimal 2 kolom
                $data[] = $row;
            }
        }
        
        return $data;
    }
    
    /**
     * Sync student data from Google Sheets
     */
    public function syncStudents(): array
    {
        $url = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTV2yHxMdFkv1HIQDuKsceg8QhBoGCqYhy1oBkdzgsWa7jgYt8ophyPHSfa5ukpgRUw3h5Pw_T-0JED/pub?gid=0&single=true&output=csv';
        
        $csvData = $this->fetchCsvData($url);
        
        $imported = 0;
        $updated = 0;
        $deactivated = 0;
        $errors = [];
        
        // Collect all student IDs from Google Sheets
        $sheetsStudentIds = [];
        
        foreach ($csvData as $index => $row) {
            try {
                if (count($row) < 3) {
                    $errors[] = "Baris " . ($index + 2) . ": Data tidak lengkap";
                    continue;
                }
                
                $nisn = trim($row[0]);
                $name = trim($row[1]);
                $class = trim($row[2]);
                
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
        
        // Deactivate students that are not in Google Sheets
        $studentsNotInSheets = \App\Models\Member::whereNotIn('member_id', $sheetsStudentIds)
            ->where('status', 'active')
            ->get();
            
        foreach ($studentsNotInSheets as $student) {
            $student->update(['status' => 'inactive']);
            $deactivated++;
        }
        
        return [
            'imported' => $imported,
            'updated' => $updated,
            'deactivated' => $deactivated,
            'errors' => $errors,
            'total_processed' => count($csvData)
        ];
    }
    
    /**
     * Sync teacher data from Google Sheets
     */
    public function syncTeachers(): array
    {
        $url = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTV2yHxMdFkv1HIQDuKsceg8QhBoGCqYhy1oBkdzgsWa7jgYt8ophyPHSfa5ukpgRUw3h5Pw_T-0JED/pub?gid=336865292&single=true&output=csv';
        
        $csvData = $this->fetchCsvData($url);
        
        $imported = 0;
        $updated = 0;
        $deactivated = 0;
        $errors = [];
        
        // Collect all teacher IDs from Google Sheets
        $sheetsTeacherIds = [];
        
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
        
        // Deactivate teachers that are not in Google Sheets
        $teachersNotInSheets = \App\Models\Teacher::whereNotIn('teacher_id', $sheetsTeacherIds)
            ->where('status', 'active')
            ->get();
            
        foreach ($teachersNotInSheets as $teacher) {
            $teacher->update(['status' => 'inactive']);
            $deactivated++;
        }
        
        return [
            'imported' => $imported,
            'updated' => $updated,
            'deactivated' => $deactivated,
            'errors' => $errors,
            'total_processed' => count($csvData)
        ];
    }

    /**
     * Sync books from Google Sheets
     */
    public function syncBooks(): array
    {
        $url = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTV2yHxMdFkv1HIQDuKsceg8QhBoGCqYhy1oBkdzgsWa7jgYt8ophyPHSfa5ukpgRUw3h5Pw_T-0JED/pub?gid=404883502&single=true&output=csv';
        
        $csvData = $this->fetchCsvData($url);
        
        $imported = 0;
        $updated = 0;
        $deactivated = 0;
        $errors = [];
        
        // Collect all book titles from Google Sheets
        $sheetsBookTitles = [];
        
        foreach ($csvData as $index => $row) {
            try {
                if (count($row) < 5) {
                    $errors[] = "Baris " . ($index + 2) . ": Data tidak lengkap (minimal 5 kolom)";
                    continue;
                }
                
                $title = trim($row[0]);
                $author = trim($row[1]);
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
                    $author = null; // Set to null if empty
                }
                
                // Normalize category to match system categories
                $category = $this->normalizeCategory($category);
                
                // Check if book exists - simplified logic
                $book = \App\Models\Book::where('title', $title)->first();
                
                if ($book) {
                    // Update existing book and mark as active (set quantity > 0)
                    $hasChanges = false;
                    $updateData = [];
                    
                    if ($book->publisher !== $publisher) {
                        $updateData['publisher'] = $publisher;
                        $hasChanges = true;
                    }
                    
                    if ($book->quantity != $quantity) {
                        $updateData['quantity'] = $quantity;
                        $updateData['available_quantity'] = $quantity; // Reset available quantity to total quantity
                        $hasChanges = true;
                    }
                    
                    if ($book->category !== $category) {
                        $updateData['category'] = $category;
                        $hasChanges = true;
                    }
                    
                    // Also check for title and author changes
                    if ($book->title !== $title) {
                        $updateData['title'] = $title;
                        $hasChanges = true;
                    }
                    
                    // Handle author comparison (including null values)
                    $currentAuthor = $book->author;
                    if ($currentAuthor !== $author) {
                        $updateData['author'] = $author;
                        $hasChanges = true;
                    }
                    
                    // Mark as active (quantity > 0)
                    if ($book->quantity <= 0) {
                        $updateData['quantity'] = $quantity;
                        $updateData['available_quantity'] = $quantity;
                        $hasChanges = true;
                    }
                    
                    // Only update if there are actual changes
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
        
        // Deactivate books that are not in Google Sheets (set quantity to 0)
        $booksNotInSheets = \App\Models\Book::whereNotIn('title', $sheetsBookTitles)
            ->where('quantity', '>', 0)
            ->get();
            
        foreach ($booksNotInSheets as $book) {
            $book->update([
                'quantity' => 0,
                'available_quantity' => 0,
            ]);
            $deactivated++;
        }
        
        return [
            'imported' => $imported,
            'updated' => $updated,
            'deactivated' => $deactivated,
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
        
        // Map Google Sheets categories to system categories
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
