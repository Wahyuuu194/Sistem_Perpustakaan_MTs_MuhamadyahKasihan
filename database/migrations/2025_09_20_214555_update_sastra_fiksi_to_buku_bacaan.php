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
        // Update kategori Sastra & Fiksi menjadi Buku Bacaan - Fiksi
        DB::table('books')
            ->where('category', 'Sastra & Fiksi')
            ->update(['category' => 'Buku Bacaan - Fiksi']);

        // Update kategori Fiksi menjadi Buku Bacaan - Fiksi
        DB::table('books')
            ->where('category', 'Fiksi')
            ->update(['category' => 'Buku Bacaan - Fiksi']);

        // Update kategori Non-Fiksi menjadi Buku Bacaan - Non-Fiksi
        DB::table('books')
            ->where('category', 'Non-Fiksi')
            ->update(['category' => 'Buku Bacaan - Non-Fiksi']);

        // Update kategori Buku Bacaan menjadi Buku Bacaan - Non-Fiksi
        DB::table('books')
            ->where('category', 'Buku Bacaan')
            ->update(['category' => 'Buku Bacaan - Non-Fiksi']);

        // Update kategori Novel menjadi Buku Bacaan - Fiksi
        DB::table('books')
            ->where('category', 'Novel')
            ->update(['category' => 'Buku Bacaan - Fiksi']);

        // Update kategori Cerita menjadi Buku Bacaan - Fiksi
        DB::table('books')
            ->where('category', 'Cerita')
            ->update(['category' => 'Buku Bacaan - Fiksi']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback ke kategori sebelumnya
        DB::table('books')
            ->where('category', 'Buku Bacaan - Fiksi')
            ->update(['category' => 'Sastra & Fiksi']);

        DB::table('books')
            ->where('category', 'Buku Bacaan - Non-Fiksi')
            ->update(['category' => 'Non-Fiksi']);
    }
};
