<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Teacher;
use App\Models\Borrowing;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBooks = Book::count();
        $totalMembers = Member::count();
        $totalTeachers = Teacher::count();
        $totalBorrowed = Borrowing::where('status', 'borrowed')->count();
        $overdueBooks = Borrowing::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->count();

        $recentBorrowings = Borrowing::with(['book', 'member', 'teacher'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $popularBooks = Book::withCount('borrowings')
            ->orderBy('borrowings_count', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalBooks',
            'totalMembers',
            'totalTeachers', 
            'totalBorrowed',
            'overdueBooks',
            'recentBorrowings',
            'popularBooks'
        ));
    }
}
