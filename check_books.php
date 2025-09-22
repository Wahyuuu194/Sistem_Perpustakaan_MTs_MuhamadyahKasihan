<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Book;

echo "=== DATA BUKU YANG SUDAH DIIMPORT ===\n\n";

$books = Book::select('id', 'title', 'category', 'author', 'publisher')->get();

foreach ($books as $book) {
    echo "ID: {$book->id}\n";
    echo "Judul: {$book->title}\n";
    echo "Kategori: {$book->category}\n";
    echo "Penulis: {$book->author}\n";
    echo "Penerbit: {$book->publisher}\n";
    echo "---\n";
}

echo "\nTotal buku: " . $books->count() . "\n";

// Cek kategori yang ada
$categories = Book::select('category')->distinct()->pluck('category');
echo "\nKategori yang ada:\n";
foreach ($categories as $category) {
    echo "- {$category}\n";
}
