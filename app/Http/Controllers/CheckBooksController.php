<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class CheckBooksController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::select('id', 'title', 'category', 'author', 'publisher');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('publisher', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }
        
        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // Sorting
        $sort = $request->get('sort', 'id_asc');
        switch ($sort) {
            case 'id_desc':
                $query->orderBy('id', 'desc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'category_asc':
                $query->orderBy('category', 'asc');
                break;
            case 'category_desc':
                $query->orderBy('category', 'desc');
                break;
            default:
                $query->orderBy('id', 'asc');
                break;
        }
        
        // Get total count before pagination
        $totalBooks = Book::count();
        
        // Paginate results
        $books = $query->paginate(20)->appends($request->query());
        
        // Get all categories for filter dropdown
        $categories = Book::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->filter()
            ->values();
        
        // Get category counts for summary
        $categoryCounts = Book::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->pluck('count', 'category');
        
        return view('books.check-books', compact('books', 'categories', 'categoryCounts', 'totalBooks'));
    }
}
