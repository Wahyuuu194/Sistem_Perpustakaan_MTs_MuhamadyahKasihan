<?php

namespace App\Http\Controllers;

use App\Services\IsbnLookupService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class IsbnController extends Controller
{
    protected $isbnLookupService;

    public function __construct(IsbnLookupService $isbnLookupService)
    {
        $this->isbnLookupService = $isbnLookupService;
    }

    /**
     * Cari ISBN untuk satu buku
     * POST /api/isbn/search
     */
    public function searchIsbn(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255'
        ]);

        $title = $request->input('title');
        $author = $request->input('author');

        $result = $this->isbnLookupService->searchIsbn($title, $author);

        return response()->json($result);
    }

    /**
     * Cari ISBN untuk multiple books sekaligus
     * POST /api/isbn/search-multiple
     */
    public function searchMultipleIsbn(Request $request): JsonResponse
    {
        $request->validate([
            'books' => 'required|array|min:1|max:50',
            'books.*.title' => 'required|string|max:255',
            'books.*.author' => 'nullable|string|max:255'
        ]);

        $books = $request->input('books');
        $results = $this->isbnLookupService->searchMultipleIsbn($books);

        // Hitung statistik
        $successCount = collect($results)->where('success', true)->count();
        $totalCount = count($results);

        return response()->json([
            'success' => true,
            'message' => "Proses selesai. Berhasil: {$successCount}/{$totalCount}",
            'total_processed' => $totalCount,
            'success_count' => $successCount,
            'results' => $results
        ]);
    }

    /**
     * Validasi format ISBN
     * POST /api/isbn/validate
     */
    public function validateIsbn(Request $request): JsonResponse
    {
        $request->validate([
            'isbn' => 'required|string|max:20'
        ]);

        $isbn = $request->input('isbn');
        $isValid = $this->isbnLookupService->validateIsbn($isbn);

        return response()->json([
            'success' => true,
            'isbn' => $isbn,
            'is_valid' => $isValid,
            'message' => $isValid ? 'ISBN format valid' : 'ISBN format tidak valid'
        ]);
    }

    /**
     * Update ISBN untuk buku yang sudah ada di database
     * PUT /api/isbn/update-book/{bookId}
     */
    public function updateBookIsbn(Request $request, $bookId): JsonResponse
    {
        $book = \App\Models\Book::findOrFail($bookId);
        
        $request->validate([
            'isbn' => 'nullable|string|max:20|unique:books,isbn,' . $bookId
        ]);

        $isbn = $request->input('isbn');
        
        // Jika ISBN tidak disediakan, coba cari otomatis
        if (!$isbn) {
            $searchResult = $this->isbnLookupService->searchIsbn($book->title, $book->author);
            
            if ($searchResult['success']) {
                $isbn = $searchResult['isbn'];
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menemukan ISBN: ' . $searchResult['message']
                ], 400);
            }
        }

        // Validasi ISBN
        if (!$this->isbnLookupService->validateIsbn($isbn)) {
            return response()->json([
                'success' => false,
                'message' => 'Format ISBN tidak valid'
            ], 400);
        }

        $book->update(['isbn' => $isbn]);

        return response()->json([
            'success' => true,
            'message' => 'ISBN berhasil diupdate',
            'book' => $book,
            'isbn' => $isbn
        ]);
    }

    /**
     * Bulk update ISBN untuk multiple books
     * POST /api/isbn/bulk-update
     */
    public function bulkUpdateIsbn(Request $request): JsonResponse
    {
        $request->validate([
            'book_ids' => 'required|array|min:1',
            'book_ids.*' => 'integer|exists:books,id'
        ]);

        $bookIds = $request->input('book_ids');
        $books = \App\Models\Book::whereIn('id', $bookIds)->get();
        
        $results = [];
        $successCount = 0;

        foreach ($books as $book) {
            $searchResult = $this->isbnLookupService->searchIsbn($book->title, $book->author);
            
            if ($searchResult['success'] && $this->isbnLookupService->validateIsbn($searchResult['isbn'])) {
                $book->update(['isbn' => $searchResult['isbn']]);
                $successCount++;
                
                $results[] = [
                    'book_id' => $book->id,
                    'title' => $book->title,
                    'success' => true,
                    'isbn' => $searchResult['isbn'],
                    'message' => 'ISBN berhasil diupdate'
                ];
            } else {
                $results[] = [
                    'book_id' => $book->id,
                    'title' => $book->title,
                    'success' => false,
                    'isbn' => null,
                    'message' => $searchResult['message'] ?? 'Gagal menemukan ISBN'
                ];
            }
            
            // Delay untuk menghindari rate limiting
            usleep(200000); // 200ms
        }

        return response()->json([
            'success' => true,
            'message' => "Bulk update selesai. Berhasil: {$successCount}/" . count($books),
            'total_processed' => count($books),
            'success_count' => $successCount,
            'results' => $results
        ]);
    }

    /**
     * Get books yang belum memiliki ISBN
     * GET /api/isbn/books-without-isbn
     */
    public function getBooksWithoutIsbn(): JsonResponse
    {
        $books = \App\Models\Book::whereNull('isbn')
            ->orWhere('isbn', '')
            ->select('id', 'title', 'author', 'publisher', 'publication_year')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $books->count(),
            'books' => $books
        ]);
    }
}

