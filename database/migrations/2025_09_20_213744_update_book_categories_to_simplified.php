<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mapping kategori lama ke kategori baru
        $categoryMapping = [
            // Agama & Keagamaan
            'Agama' => 'Agama & Keagamaan',
            'Kitab' => 'Agama & Keagamaan',
            'Tafsir' => 'Agama & Keagamaan',
            'Pendidikan Kemuhammadiyahan' => 'Agama & Keagamaan',
            'Bahasa Arab' => 'Agama & Keagamaan',
            
            // Pendidikan & Pelajaran
            'Pendidikan' => 'Pendidikan & Pelajaran',
            'Buku Pelajaran' => 'Pendidikan & Pelajaran',
            'Buku Paket' => 'Pendidikan & Pelajaran',
            'Buku Guru' => 'Pendidikan & Pelajaran',
            'Buku siswa' => 'Pendidikan & Pelajaran',
            'Buku pegangan' => 'Pendidikan & Pelajaran',
            'PPKn' => 'Pendidikan & Pelajaran',
            'IPA' => 'Pendidikan & Pelajaran',
            'IPS' => 'Pendidikan & Pelajaran',
            'Bahasa Indonesia' => 'Pendidikan & Pelajaran',
            'Bahasa Inggris' => 'Pendidikan & Pelajaran',
            'Matematika' => 'Pendidikan & Pelajaran',
            'PJOK' => 'Pendidikan & Pelajaran',
            'Seni Budaya' => 'Pendidikan & Pelajaran',
            'Prakarya' => 'Pendidikan & Pelajaran',
            'Bahasa Jawa' => 'Pendidikan & Pelajaran',
            
            // Referensi & Kamus
            'Kamus' => 'Referensi & Kamus',
            'Referensi' => 'Referensi & Kamus',
            'Visual ilmu dan pengetahuan populer' => 'Referensi & Kamus',
            
            // Teknologi & Sains
            'Teknologi' => 'Teknologi & Sains',
            'Sains' => 'Teknologi & Sains',
            
            // Sastra & Fiksi
            'Fiksi' => 'Sastra & Fiksi',
            'Non-Fiksi' => 'Sastra & Fiksi',
            'Buku Bacaan' => 'Sastra & Fiksi',
            'Novel' => 'Sastra & Fiksi',
            'Cerita' => 'Sastra & Fiksi',
            
            // Sejarah & Budaya
            'Sejarah' => 'Sejarah & Budaya',
        ];

        // Update kategori buku yang sudah ada
        foreach ($categoryMapping as $oldCategory => $newCategory) {
            DB::table('books')
                ->where('category', $oldCategory)
                ->update(['category' => $newCategory]);
        }

        // Update kategori yang tidak ada dalam mapping ke 'Lainnya'
        $existingCategories = array_keys($categoryMapping);
        DB::table('books')
            ->whereNotIn('category', $existingCategories)
            ->where('category', '!=', 'Lainnya')
            ->where('category', '!=', 'Agama & Keagamaan')
            ->where('category', '!=', 'Pendidikan & Pelajaran')
            ->where('category', '!=', 'Referensi & Kamus')
            ->where('category', '!=', 'Teknologi & Sains')
            ->where('category', '!=', 'Sastra & Fiksi')
            ->where('category', '!=', 'Sejarah & Budaya')
            ->update(['category' => 'Lainnya']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak ada rollback karena mapping ini bersifat satu arah
        // Jika diperlukan rollback, bisa dibuat mapping terbalik
    }
};
