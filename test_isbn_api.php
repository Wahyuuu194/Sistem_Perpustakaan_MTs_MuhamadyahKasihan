<?php

/**
 * Test script untuk ISBN API
 * Jalankan dengan: php test_isbn_api.php
 */

// Base URL API
$baseUrl = 'http://localhost:8000/api/isbn';

// Function untuk membuat HTTP request
function makeRequest($url, $method = 'GET', $data = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    if ($method === 'POST' || $method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

echo "=== TEST ISBN API ===\n\n";

// Test 1: Cari ISBN untuk satu buku
echo "1. Test: Cari ISBN untuk satu buku\n";
echo "-----------------------------------\n";
$testData = [
    'title' => 'Laskar Pelangi',
    'author' => 'Andrea Hirata'
];

$result = makeRequest($baseUrl . '/search', 'POST', $testData);
echo "Status Code: " . $result['code'] . "\n";
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n\n";

// Test 2: Validasi ISBN
echo "2. Test: Validasi ISBN\n";
echo "----------------------\n";
$isbnData = [
    'isbn' => '9789793062792'
];

$result = makeRequest($baseUrl . '/validate', 'POST', $isbnData);
echo "Status Code: " . $result['code'] . "\n";
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n\n";

// Test 3: Cari ISBN untuk multiple books
echo "3. Test: Cari ISBN untuk multiple books\n";
echo "---------------------------------------\n";
$multipleBooks = [
    'books' => [
        [
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata'
        ],
        [
            'title' => 'Sang Pemimpi',
            'author' => 'Andrea Hirata'
        ],
        [
            'title' => 'Bumi Manusia',
            'author' => 'Pramoedya Ananta Toer'
        ]
    ]
];

$result = makeRequest($baseUrl . '/search-multiple', 'POST', $multipleBooks);
echo "Status Code: " . $result['code'] . "\n";
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n\n";

// Test 4: Get books tanpa ISBN
echo "4. Test: Get books tanpa ISBN\n";
echo "-----------------------------\n";
$result = makeRequest($baseUrl . '/books-without-isbn');
echo "Status Code: " . $result['code'] . "\n";
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n\n";

echo "=== TEST SELESAI ===\n";

// Function untuk test dengan data yang berbeda
function testWithDifferentBooks() {
    global $baseUrl;
    
    echo "\n=== TEST DENGAN BUKU LAIN ===\n\n";
    
    $books = [
        [
            'title' => 'Harry Potter and the Philosopher\'s Stone',
            'author' => 'J.K. Rowling'
        ],
        [
            'title' => 'The Great Gatsby',
            'author' => 'F. Scott Fitzgerald'
        ],
        [
            'title' => 'To Kill a Mockingbird',
            'author' => 'Harper Lee'
        ]
    ];
    
    foreach ($books as $index => $book) {
        echo ($index + 1) . ". Testing: " . $book['title'] . " by " . $book['author'] . "\n";
        
        $result = makeRequest($baseUrl . '/search', 'POST', $book);
        
        if ($result['data']['success']) {
            echo "   ✓ ISBN ditemukan: " . $result['data']['isbn'] . "\n";
        } else {
            echo "   ✗ Gagal: " . $result['data']['message'] . "\n";
        }
        echo "\n";
        
        // Delay untuk menghindari rate limiting
        sleep(1);
    }
}

// Uncomment untuk menjalankan test tambahan
// testWithDifferentBooks();

echo "\nCatatan:\n";
echo "- Pastikan server Laravel berjalan di http://localhost:8000\n";
echo "- Pastikan koneksi internet tersedia untuk mengakses Google Books API\n";
echo "- Jika ada error, check log Laravel untuk detail lebih lanjut\n";



