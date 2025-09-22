<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Book;

echo "=== TEST PENCARIAN CERDAS ===\n\n";

// Simulasi logika pencarian yang baru
function smartSearch($search) {
    $query = Book::query();
    
    if (preg_match('/^(.+?)\s+(\d+)$/', $search, $matches)) {
        $baseTitle = $matches[1];
        $number = $matches[2];
        
        echo "Pencarian seri: Base='{$baseTitle}', Number='{$number}'\n";
        
        $books = $query->where(function($q) use ($baseTitle, $number) {
            $q->where('title', 'like', "%{$baseTitle}%")
              ->where('title', 'like', "%{$number}%");
        })->get();
    } else {
        echo "Pencarian normal: '{$search}'\n";
        
        $books = $query->where('title', 'like', "%{$search}%")->get();
    }
    
    return $books;
}

// Test berbagai pencarian
$searchTerms = [
    'Ensiklopedi Anak Nasional 1',
    'Ensiklopedi Anak Nasional 2',
    'Ensiklopedi Anak Nasional 10',
    'Ensiklopedi Anak Nasional',
    'Ensiklopedi'
];

foreach ($searchTerms as $term) {
    echo "=== Pencarian: '{$term}' ===\n";
    $books = smartSearch($term);
    echo "Hasil: " . $books->count() . " buku\n";
    
    foreach ($books as $book) {
        echo "- {$book->title}\n";
    }
    echo "\n";
}

