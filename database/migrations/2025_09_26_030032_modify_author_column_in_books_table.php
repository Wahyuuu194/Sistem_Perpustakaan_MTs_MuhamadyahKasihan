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
        // Use raw SQL to modify the author column to be nullable
        DB::statement('ALTER TABLE books MODIFY COLUMN author VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert author column to not nullable
        DB::statement('ALTER TABLE books MODIFY COLUMN author VARCHAR(255) NOT NULL');
    }
};
