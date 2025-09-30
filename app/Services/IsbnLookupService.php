<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IsbnLookupService
{
    private $googleBooksApiUrl = 'https://www.googleapis.com/books/v1/volumes';
    
    /**
     * Cari ISBN berdasarkan judul dan penulis buku
     */
    public function searchIsbn(string $title, string $author = null): array
    {
        try {
            $query = $this->buildSearchQuery($title, $author);
            $response = Http::timeout(10)->get($this->googleBooksApiUrl, [
                'q' => $query,
                'maxResults' => 5
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Gagal mengakses Google Books API',
                    'isbn' => null,
                    'book_info' => null
                ];
            }

            $data = $response->json();
            
            if (empty($data['items'])) {
                return [
                    'success' => false,
                    'message' => 'Buku tidak ditemukan di Google Books',
                    'isbn' => null,
                    'book_info' => null
                ];
            }

            // Ambil buku pertama yang paling relevan
            $book = $data['items'][0];
            $volumeInfo = $book['volumeInfo'];
            
            // Cari ISBN dari industryIdentifiers
            $isbn = $this->extractIsbn($volumeInfo);
            
            if (!$isbn) {
                return [
                    'success' => false,
                    'message' => 'ISBN tidak ditemukan untuk buku ini',
                    'isbn' => null,
                    'book_info' => $this->formatBookInfo($volumeInfo)
                ];
            }

            return [
                'success' => true,
                'message' => 'ISBN berhasil ditemukan',
                'isbn' => $isbn,
                'book_info' => $this->formatBookInfo($volumeInfo)
            ];

        } catch (\Exception $e) {
            Log::error('ISBN Lookup Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'isbn' => null,
                'book_info' => null
            ];
        }
    }

    /**
     * Cari ISBN untuk multiple books sekaligus
     */
    public function searchMultipleIsbn(array $books): array
    {
        $results = [];
        
        foreach ($books as $index => $book) {
            $title = $book['title'] ?? '';
            $author = $book['author'] ?? null;
            
            $result = $this->searchIsbn($title, $author);
            $result['index'] = $index;
            $result['input'] = $book;
            
            $results[] = $result;
            
            // Tambahkan delay untuk menghindari rate limiting
            usleep(200000); // 200ms delay
        }
        
        return $results;
    }

    /**
     * Build search query untuk Google Books API
     */
    private function buildSearchQuery(string $title, string $author = null): string
    {
        $query = 'intitle:' . $this->cleanQuery($title);
        
        if ($author) {
            $query .= '+inauthor:' . $this->cleanQuery($author);
        }
        
        return $query;
    }

    /**
     * Clean query string untuk API
     */
    private function cleanQuery(string $query): string
    {
        // Remove special characters dan normalize
        $query = preg_replace('/[^\w\s]/', '', $query);
        $query = preg_replace('/\s+/', ' ', trim($query));
        
        return urlencode($query);
    }

    /**
     * Extract ISBN dari volume info
     */
    private function extractIsbn(array $volumeInfo): ?string
    {
        if (!isset($volumeInfo['industryIdentifiers'])) {
            return null;
        }

        // Prioritas: ISBN_13 > ISBN_10
        $isbn13 = null;
        $isbn10 = null;

        foreach ($volumeInfo['industryIdentifiers'] as $identifier) {
            if ($identifier['type'] === 'ISBN_13') {
                $isbn13 = $identifier['identifier'];
            } elseif ($identifier['type'] === 'ISBN_10') {
                $isbn10 = $identifier['identifier'];
            }
        }

        return $isbn13 ?: $isbn10;
    }

    /**
     * Format book info untuk response
     */
    private function formatBookInfo(array $volumeInfo): array
    {
        return [
            'title' => $volumeInfo['title'] ?? '',
            'authors' => $volumeInfo['authors'] ?? [],
            'publisher' => $volumeInfo['publisher'] ?? '',
            'published_date' => $volumeInfo['publishedDate'] ?? '',
            'description' => $volumeInfo['description'] ?? '',
            'page_count' => $volumeInfo['pageCount'] ?? null,
            'categories' => $volumeInfo['categories'] ?? [],
            'language' => $volumeInfo['language'] ?? '',
            'preview_link' => $volumeInfo['previewLink'] ?? '',
            'thumbnail' => $volumeInfo['imageLinks']['thumbnail'] ?? null
        ];
    }

    /**
     * Validasi format ISBN
     */
    public function validateIsbn(string $isbn): bool
    {
        // Remove hyphens dan spaces
        $isbn = preg_replace('/[-\s]/', '', $isbn);
        
        // Check length
        if (strlen($isbn) !== 10 && strlen($isbn) !== 13) {
            return false;
        }
        
        // Basic format validation
        return ctype_digit($isbn) || (strlen($isbn) === 10 && ctype_digit(substr($isbn, 0, 9)) && (is_numeric($isbn[9]) || $isbn[9] === 'X'));
    }
}



