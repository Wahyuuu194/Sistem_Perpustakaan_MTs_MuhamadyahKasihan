<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Tambah kolom available_quantity
            $table->integer('available_quantity')->default(1)->after('quantity');
            
            // Ubah kolom isbn menjadi nullable
            $table->string('isbn')->nullable()->change();
            
            // Hapus kolom yang tidak digunakan
            $table->dropColumn(['description', 'publication_year', 'location']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Hapus kolom available_quantity
            $table->dropColumn('available_quantity');
            
            // Kembalikan kolom isbn menjadi required
            $table->string('isbn')->nullable(false)->change();
            
            // Tambah kembali kolom yang dihapus
            $table->text('description')->nullable();
            $table->integer('publication_year')->nullable();
            $table->string('location')->nullable();
        });
    }
};
