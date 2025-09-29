<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BookCategoryClassifier;

class ClassifyBooksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:classify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Klasifikasikan buku berdasarkan judul ke kategori Fiksi atau Non-Fiksi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai klasifikasi buku...');
        
        $updated = BookCategoryClassifier::updateBookCategories();
        
        $this->info("Berhasil mengupdate {$updated} buku.");
        
        return Command::SUCCESS;
    }
}
