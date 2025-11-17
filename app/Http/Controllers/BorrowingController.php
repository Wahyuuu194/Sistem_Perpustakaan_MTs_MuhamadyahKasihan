<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Teacher;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    public function index(): View
    {
        $borrowings = Borrowing::with(['book', 'member', 'teacher'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('borrowings.index', compact('borrowings'));
    }

    public function create(): View
    {
        $books = Book::all()->filter(function($book) {
            return $book->available_quantity > 0;
        });
        
        $members = Member::where('status', 'active')->get();
        $teachers = Teacher::where('status', 'active')->get();
        return view('borrowings.create', compact('books', 'members', 'teachers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'member_id' => 'nullable|exists:members,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'jumlah' => 'required|integer|min:1',
            'borrow_date' => 'required|date',   
            'due_date' => 'required|date|after:borrow_date',
        ]);

        // Pastikan ada member_id atau teacher_id
        if (!$validated['member_id'] && !$validated['teacher_id']) {
            return back()->withErrors(['member_id' => 'Pilih anggota atau guru untuk peminjaman.']);
        }

        $book = Book::find($validated['book_id']);
        $quantity = $validated['jumlah'];
        
        if ($book->available_quantity < $quantity) {
            return back()->withErrors(['jumlah' => "Stok buku tidak mencukupi. Stok tersedia: {$book->available_quantity} buku."]);
        }

        // Kurangi stok buku sesuai jumlah yang dipinjam
        $book->decrement('available_quantity', $quantity);
        
        // Tambahkan quantity ke data yang akan disimpan
        $validated['quantity'] = $quantity;
        unset($validated['jumlah']); // Hapus field jumlah karena sudah diganti dengan quantity
        
        Borrowing::create($validated);

        return redirect()->route('borrowings.index')
            ->with('success', "Peminjaman berhasil dibuat! {$quantity} buku telah dipinjam.");
    }

    public function edit(Borrowing $borrowing): View
    {
        $borrowing->load(['book', 'member', 'teacher']);
        
        $books = Book::all();
        $members = Member::where('status', 'active')->get();
        $teachers = Teacher::where('status', 'active')->get();
        
        return view('borrowings.edit', compact('borrowing', 'books', 'members', 'teachers'));
    }

    public function update(Request $request, Borrowing $borrowing): RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'member_id' => 'nullable|exists:members,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'jumlah' => 'required|integer|min:1',
            'borrow_date' => 'required|date',   
            'due_date' => 'required|date|after:borrow_date',
        ]);

        // Pastikan ada member_id atau teacher_id
        if (!$validated['member_id'] && !$validated['teacher_id']) {
            return back()->withErrors(['member_id' => 'Pilih anggota atau guru untuk peminjaman.']);
        }

        $book = Book::find($validated['book_id']);
        $quantity = $validated['jumlah'];
        
        // Hitung selisih quantity
        $oldQuantity = $borrowing->quantity;
        $quantityDifference = $quantity - $oldQuantity;
        
        // Jika buku berubah, kembalikan stok buku lama dan kurangi stok buku baru
        if ($borrowing->book_id != $validated['book_id']) {
            // Kembalikan stok buku lama
            $oldBook = Book::find($borrowing->book_id);
            $oldBook->increment('available_quantity', $oldQuantity);
            
            // Kurangi stok buku baru
            if ($book->available_quantity < $quantity) {
                return back()->withErrors(['jumlah' => "Stok buku tidak mencukupi. Stok tersedia: {$book->available_quantity} buku."]);
            }
            $book->decrement('available_quantity', $quantity);
        } else {
            // Jika buku sama, sesuaikan stok berdasarkan selisih quantity
            if ($quantityDifference > 0) {
                // Jumlah bertambah, kurangi stok
                if ($book->available_quantity < $quantityDifference) {
                    return back()->withErrors(['jumlah' => "Stok buku tidak mencukupi. Stok tersedia: {$book->available_quantity} buku."]);
                }
                $book->decrement('available_quantity', $quantityDifference);
            } elseif ($quantityDifference < 0) {
                // Jumlah berkurang, kembalikan stok
                $book->increment('available_quantity', abs($quantityDifference));
            }
        }

        // Tambahkan quantity ke data yang akan disimpan
        $validated['quantity'] = $quantity;
        unset($validated['jumlah']); // Hapus field jumlah karena sudah diganti dengan quantity
        
        $borrowing->update($validated);

        return redirect()->route('borrowings.index')
            ->with('success', "Peminjaman berhasil diperbarui!");
    }

    public function show(Borrowing $borrowing): View
    {
        $borrowing->load(['book', 'member', 'teacher']);
        return view('borrowings.show', compact('borrowing'));
    }

    public function return(Borrowing $borrowing): RedirectResponse
    {
        $borrowing->update([
            'status' => 'returned',
            'return_date' => Carbon::today()
        ]);

        // Kembalikan stok buku sesuai jumlah yang dipinjam
        $borrowing->book->increment('available_quantity', $borrowing->quantity);

        return redirect()->route('borrowings.index')
            ->with('success', "Buku berhasil dikembalikan! {$borrowing->quantity} buku telah dikembalikan.");
    }

    public function destroy(Borrowing $borrowing): RedirectResponse
    {
        if ($borrowing->status === 'borrowed') {
            // Kembalikan stok buku sesuai jumlah yang dipinjam
            $borrowing->book->increment('available_quantity', $borrowing->quantity);
        }
        
        $borrowing->delete();

        return redirect()->route('borrowings.index')
            ->with('success', 'Peminjaman berhasil dihapus!');
    }
}
