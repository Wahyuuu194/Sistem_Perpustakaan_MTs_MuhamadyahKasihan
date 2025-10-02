<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            // Drop existing foreign key if present, then alter column
            try {
                $table->dropForeign(['member_id']);
            } catch (\Throwable $e) {
                // ignore if not exists
            }
        });

        Schema::table('borrowings', function (Blueprint $table) {
            $table->unsignedBigInteger('member_id')->nullable()->change();
        });

        Schema::table('borrowings', function (Blueprint $table) {
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->unsignedBigInteger('member_id')->nullable(false)->change();
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
        });
    }
};


