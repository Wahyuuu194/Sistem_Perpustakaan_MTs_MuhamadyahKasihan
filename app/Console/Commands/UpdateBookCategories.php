<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;

class UpdateBookCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:book-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update book categories to match Google Sheets categories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memperbarui kategori buku...');
        
        // Define the mapping from old categories to new categories
        $categoryMapping = [
            'Non-Fiksi' => 'non-fiksi',
            'Fiksi' => 'fiksi',
            'Buku Bacaan' => 'Buku Pelajaran untuk siswa',
            'Buku Paket' => 'Buku Paket',
            // Add more mappings as needed
        ];
        
        // Get all unique categories in database
        $currentCategories = Book::distinct()->pluck('category')->filter()->values();
        $this->info("Kategori yang ada di database: " . $currentCategories->implode(', '));
        
        // Show current category distribution
        $this->info("\nDistribusi kategori saat ini:");
        foreach ($currentCategories as $category) {
            $count = Book::where('category', $category)->count();
            $this->line("- {$category}: {$count} buku");
        }
        
        // Update categories based on content analysis
        $this->info("\nMemperbarui kategori berdasarkan analisis konten...");
        
        $updatedCount = 0;
        
        // Update based on title patterns
        $books = Book::all();
        foreach ($books as $book) {
            $newCategory = $this->determineCategory($book->title, $book->publisher);
            if ($newCategory && $newCategory !== $book->category) {
                $book->update(['category' => $newCategory]);
                $updatedCount++;
                $this->line("Updated: {$book->title} -> {$newCategory}");
            }
        }
        
        $this->info("\nBerhasil memperbarui {$updatedCount} kategori buku!");
        
        // Show new category distribution
        $this->info("\nDistribusi kategori setelah update:");
        $newCategories = Book::distinct()->pluck('category')->filter()->values();
        foreach ($newCategories as $category) {
            $count = Book::where('category', $category)->count();
            $this->line("- {$category}: {$count} buku");
        }
    }
    
    private function determineCategory($title, $publisher)
    {
        $title = strtolower($title);
        $publisher = strtolower($publisher);
        
        // Fiksi
        if (strpos($title, 'novel') !== false || 
            strpos($title, 'cerita') !== false ||
            strpos($title, 'kisah') !== false) {
            return 'fiksi';
        }
        
        // Buku Paket
        if (strpos($title, 'paket') !== false ||
            strpos($title, 'kelas') !== false ||
            strpos($title, 'smp') !== false ||
            strpos($title, 'mts') !== false) {
            return 'Buku Paket';
        }
        
        // Buku Guru
        if (strpos($title, 'guru') !== false ||
            strpos($title, 'panduan') !== false) {
            return 'Buku Guru';
        }
        
        // Buku Pelajaran untuk siswa
        if (strpos($title, 'pelajaran') !== false ||
            strpos($title, 'materi') !== false ||
            strpos($title, 'pembelajaran') !== false) {
            return 'Buku Pelajaran untuk siswa';
        }
        
        // ICT
        if (strpos($title, 'komputer') !== false ||
            strpos($title, 'teknologi') !== false ||
            strpos($title, 'digital') !== false) {
            return 'ICT (Information Computer Technology)';
        }
        
        // Kamus
        if (strpos($title, 'kamus') !== false ||
            strpos($title, 'kamus') !== false) {
            return 'kamus';
        }
        
        // Ensiklopedia
        if (strpos($title, 'ensiklopedia') !== false ||
            strpos($title, 'referensi') !== false) {
            return 'ensiklopedia';
        }
        
        // Metode membaca & menulis al-Qur'an
        if (strpos($title, 'quran') !== false ||
            strpos($title, 'al-qur') !== false ||
            strpos($title, 'iqra') !== false ||
            strpos($title, 'tajwid') !== false) {
            return 'metode membaca & menulis al-Qur\'an';
        }
        
        // Terjemahan Kitab
        if (strpos($title, 'terjemahan') !== false ||
            strpos($title, 'kitab') !== false) {
            return 'Terjemahan Kitab';
        }
        
        // Edukasi
        if (strpos($title, 'pendidikan') !== false ||
            strpos($title, 'edukasi') !== false) {
            return 'Edukasi';
        }
        
        // Visual Ilmu dan Pengetahuan Populer
        if (strpos($title, 'visual') !== false ||
            strpos($title, 'populer') !== false) {
            return 'Visual Ilmu dan Pengetahuan Populer';
        }
        
        // Default to non-fiksi for religious/educational content
        if (strpos($title, 'islam') !== false ||
            strpos($title, 'hadits') !== false ||
            strpos($title, 'akhlak') !== false ||
            strpos($title, 'fiqih') !== false) {
            return 'non-fiksi';
        }
        
        // Default fallback
        return 'non-fiksi';
    }
}
