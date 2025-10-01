<?php

/**
 * Contoh Penggunaan ISBN API
 * 
 * File ini berisi contoh-contoh praktis penggunaan API ISBN
 * untuk sistem perpustakaan MTs Muhamadyah
 */

// ========================================
// CONTOH 1: Cari ISBN untuk Satu Buku
// ========================================

function cariIsbnSatuBuku($judul, $penulis = null) {
    $url = 'http://localhost:8000/api/isbn/search';
    
    $data = [
        'title' => $judul,
        'author' => $penulis
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status_code' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// Contoh penggunaan
echo "=== CONTOH 1: Cari ISBN untuk Satu Buku ===\n";
$result = cariIsbnSatuBuku('Laskar Pelangi', 'Andrea Hirata');
echo "Status: " . $result['status_code'] . "\n";
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n\n";

// ========================================
// CONTOH 2: Cari ISBN untuk Multiple Books
// ========================================

function cariIsbnMultipleBuku($daftarBuku) {
    $url = 'http://localhost:8000/api/isbn/search-multiple';
    
    $data = [
        'books' => $daftarBuku
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status_code' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// Contoh penggunaan
echo "=== CONTOH 2: Cari ISBN untuk Multiple Books ===\n";
$daftarBuku = [
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
];

$result = cariIsbnMultipleBuku($daftarBuku);
echo "Status: " . $result['status_code'] . "\n";
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n\n";

// ========================================
// CONTOH 3: Bulk Update ISBN
// ========================================

function bulkUpdateIsbn($idsBuku) {
    $url = 'http://localhost:8000/api/isbn/bulk-update';
    
    $data = [
        'book_ids' => $idsBuku
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status_code' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// Contoh penggunaan
echo "=== CONTOH 3: Bulk Update ISBN ===\n";
$idsBuku = [1, 2, 3, 4, 5]; // ID buku yang ingin diupdate
$result = bulkUpdateIsbn($idsBuku);
echo "Status: " . $result['status_code'] . "\n";
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n\n";

// ========================================
// CONTOH 4: Get Books Tanpa ISBN
// ========================================

function getBukuTanpaIsbn() {
    $url = 'http://localhost:8000/api/isbn/books-without-isbn';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status_code' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// Contoh penggunaan
echo "=== CONTOH 4: Get Books Tanpa ISBN ===\n";
$result = getBukuTanpaIsbn();
echo "Status: " . $result['status_code'] . "\n";
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n\n";

// ========================================
// CONTOH 5: Validasi ISBN
// ========================================

function validasiIsbn($isbn) {
    $url = 'http://localhost:8000/api/isbn/validate';
    
    $data = [
        'isbn' => $isbn
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status_code' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// Contoh penggunaan
echo "=== CONTOH 5: Validasi ISBN ===\n";
$isbn = '9789793062792'; // ISBN Laskar Pelangi
$result = validasiIsbn($isbn);
echo "Status: " . $result['status_code'] . "\n";
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n\n";

// ========================================
// CONTOH 6: Update ISBN untuk Buku Tertentu
// ========================================

function updateIsbnBuku($idBuku, $isbn = null) {
    $url = "http://localhost:8000/api/isbn/update-book/{$idBuku}";
    
    $data = [];
    if ($isbn) {
        $data['isbn'] = $isbn;
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status_code' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// Contoh penggunaan
echo "=== CONTOH 6: Update ISBN untuk Buku Tertentu ===\n";
$idBuku = 1; // ID buku yang ingin diupdate
$result = updateIsbnBuku($idBuku); // Tanpa ISBN, akan dicari otomatis
echo "Status: " . $result['status_code'] . "\n";
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n\n";

// ========================================
// CONTOH 7: Workflow Lengkap
// ========================================

function workflowLengkap() {
    echo "=== CONTOH 7: Workflow Lengkap ===\n";
    
    // 1. Cek buku yang belum memiliki ISBN
    echo "1. Mengecek buku yang belum memiliki ISBN...\n";
    $bukuTanpaIsbn = getBukuTanpaIsbn();
    
    if ($bukuTanpaIsbn['data']['success'] && $bukuTanpaIsbn['data']['count'] > 0) {
        echo "   Ditemukan {$bukuTanpaIsbn['data']['count']} buku tanpa ISBN\n";
        
        // 2. Ambil beberapa ID buku untuk diupdate
        $idsBuku = array_slice(array_column($bukuTanpaIsbn['data']['books'], 'id'), 0, 3);
        echo "   Mengupdate ISBN untuk buku ID: " . implode(', ', $idsBuku) . "\n";
        
        // 3. Bulk update ISBN
        $resultUpdate = bulkUpdateIsbn($idsBuku);
        
        if ($resultUpdate['data']['success']) {
            echo "   Berhasil mengupdate {$resultUpdate['data']['success_count']} dari {$resultUpdate['data']['total_processed']} buku\n";
        } else {
            echo "   Gagal mengupdate ISBN\n";
        }
    } else {
        echo "   Semua buku sudah memiliki ISBN\n";
    }
    
    echo "\n";
}

// Jalankan workflow lengkap
workflowLengkap();

// ========================================
// CONTOH 8: Pencarian Buku Populer Indonesia
// ========================================

function cariBukuPopulerIndonesia() {
    echo "=== CONTOH 8: Pencarian Buku Populer Indonesia ===\n";
    
    $bukuPopuler = [
        ['title' => 'Laskar Pelangi', 'author' => 'Andrea Hirata'],
        ['title' => 'Sang Pemimpi', 'author' => 'Andrea Hirata'],
        ['title' => 'Bumi Manusia', 'author' => 'Pramoedya Ananta Toer'],
        ['title' => 'Anak Semua Bangsa', 'author' => 'Pramoedya Ananta Toer'],
        ['title' => 'Ayat-Ayat Cinta', 'author' => 'Habiburrahman El Shirazy'],
        ['title' => 'Dilan 1990', 'author' => 'Pidi Baiq'],
        ['title' => 'Perahu Kertas', 'author' => 'Dewi Lestari'],
        ['title' => 'Supernova', 'author' => 'Dewi Lestari']
    ];
    
    echo "Mencari ISBN untuk " . count($bukuPopuler) . " buku populer Indonesia...\n";
    
    $result = cariIsbnMultipleBuku($bukuPopuler);
    
    if ($result['data']['success']) {
        echo "Berhasil menemukan ISBN untuk {$result['data']['success_count']} dari {$result['data']['total_processed']} buku\n";
        
        // Tampilkan hasil
        foreach ($result['data']['results'] as $index => $item) {
            $status = $item['success'] ? '✓' : '✗';
            $isbn = $item['isbn'] ?: 'Tidak ditemukan';
            echo "   {$status} {$item['input']['title']} - {$isbn}\n";
        }
    } else {
        echo "Gagal mencari ISBN: " . $result['data']['message'] . "\n";
    }
    
    echo "\n";
}

// Jalankan pencarian buku populer
cariBukuPopulerIndonesia();

echo "=== SELESAI ===\n";
echo "Catatan:\n";
echo "- Pastikan server Laravel berjalan di http://localhost:8000\n";
echo "- Pastikan koneksi internet tersedia untuk mengakses Google Books API\n";
echo "- Untuk penggunaan production, tambahkan error handling yang lebih robust\n";
echo "- Pertimbangkan untuk menambahkan rate limiting dan caching\n";




