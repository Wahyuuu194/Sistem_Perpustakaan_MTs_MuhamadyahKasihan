<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\GoogleSheetsSyncService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $query = Book::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Pencarian yang lebih cerdas untuk seri buku
                if (preg_match('/^(.+?)\s+(\d+)$/', $search, $matches)) {
                    $baseTitle = $matches[1];
                    $number = $matches[2];
                    
                    // Cari buku dengan judul yang mengandung base title dan nomor yang tepat
                    $q->where(function($subQ) use ($baseTitle, $number) {
                        $subQ->where('title', 'like', "%{$baseTitle}%")
                             ->where('title', 'like', "%{$number}%");
                    });
                } else {
                    // Pencarian normal
                    $q->where('title', 'like', "%{$search}%");
                }
                
                $q->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('publisher', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%")
                  ->orWhere('rak', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }
        
        $books = $query->get();
        $categories = Book::distinct()->pluck('category')->filter()->values();
        
        return view('books.index', compact('books', 'categories'));
    }

    public function create(): View
    {
        return view('books.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:20|unique:books',
            'publisher' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'kelas' => 'nullable|string|max:50',
            'rak' => 'nullable|string|in:A,B,C,D,E,F,G',
            'quantity' => 'required|integer|min:1',
            'available_quantity' => 'required|integer|min:0',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            $imagePath = $request->file('cover_image')->store('book-covers', 'public');
            $validated['cover_image'] = $imagePath;
        }

        Book::create($validated);

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan!');
    }

    public function show(Book $book): View
    {
        $book->load('borrowings.member');
        return view('books.show', compact('book'));
    }

    public function edit(Book $book): View
    {
        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:20|unique:books,isbn,' . $book->id,
            'publisher' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'kelas' => 'nullable|string|max:50',
            'rak' => 'nullable|string|in:A,B,C,D,E,F,G',
            'quantity' => 'required|integer|min:1',
            'available_quantity' => 'required|integer|min:0',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                \Storage::disk('public')->delete($book->cover_image);
            }
            $imagePath = $request->file('cover_image')->store('book-covers', 'public');
            $validated['cover_image'] = $imagePath;
        }

        $book->update($validated);

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil diupdate!');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil dihapus!');
    }

    /**
     * Sync books from Google Sheets
     */
    public function syncFromGoogleSheets(Request $request)
    {
        // Ensure JSON response
        $request->headers->set('Accept', 'application/json');
        
        try {
            $syncService = app(GoogleSheetsSyncService::class);
            
            if (!$syncService) {
                throw new \Exception('Gagal menginisialisasi Google Sheets Service');
            }
            
            $result = $syncService->syncBooks();
            
            if (!is_array($result)) {
                throw new \Exception('Hasil sync tidak valid');
            }
            
            $message = "Sync buku berhasil! ";
            $message .= "Imported: " . ($result['imported'] ?? 0) . ", ";
            $message .= "Updated: " . ($result['updated'] ?? 0) . ", ";
            $message .= "Deleted: " . ($result['deleted'] ?? 0) . ", ";
            $message .= "Total processed: " . ($result['total_processed'] ?? 0);
            
            if (!empty($result['errors'])) {
                $message .= ". Errors: " . count($result['errors']);
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $result
            ], 200, [
                'Content-Type' => 'application/json'
            ]);
            
        } catch (\Google_Service_Exception $e) {
            $errorMessage = json_decode($e->getMessage(), true);
            $message = $errorMessage['error']['message'] ?? $e->getMessage();
            
            \Log::error('Google Service Error: ' . $message);
            \Log::error('Full error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Sync gagal: ' . $message,
                'error' => $message
            ], 500, [
                'Content-Type' => 'application/json'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Sync Books Error: ' . $e->getMessage());
            \Log::error('Error Class: ' . get_class($e));
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Sync gagal: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'error_class' => get_class($e)
            ], 500, [
                'Content-Type' => 'application/json'
            ]);
        } catch (\Throwable $e) {
            \Log::error('Fatal Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan fatal: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500, [
                'Content-Type' => 'application/json'
            ]);
        }
    }
}
