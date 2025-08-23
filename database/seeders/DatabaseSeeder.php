<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Member;
use App\Models\Borrowing;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Sample Books
        $books = [
            [
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'isbn' => '9789793062792',
                'publisher' => 'Bentang Pustaka',
                'quantity' => 3,
                'category' => 'Fiksi',
                'available_quantity' => 3
            ],
            [
                'title' => 'Bumi Manusia',
                'author' => 'Pramoedya Ananta Toer',
                'isbn' => '9789799731235',
                'publisher' => 'Lentera Dipantara',
                'quantity' => 2,
                'category' => 'Fiksi',
                'available_quantity' => 2
            ],
            [
                'title' => 'Matematika Dasar',
                'author' => 'Dr. Suharto',
                'isbn' => '9786021234567',
                'publisher' => 'Erlangga',
                'quantity' => 5,
                'category' => 'Pendidikan',
                'available_quantity' => 5
            ]
        ];

        foreach ($books as $bookData) {
            Book::create($bookData);
        }

        // Sample Members
        $members = [
            [
                'name' => 'Ahmad Fadillah',
                'email' => 'ahmad@example.com',
                'phone' => '081234567890',
                'address' => 'Jl. Sudirman No. 123, Jakarta',
                'birth_date' => '1995-03-15',
                'member_id' => 'M001',
                'registration_date' => '2024-01-01',
                'status' => 'active',
                'kelas' => '8A'
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@example.com',
                'phone' => '081234567891',
                'address' => 'Jl. Thamrin No. 456, Jakarta',
                'birth_date' => '1998-07-22',
                'member_id' => 'M002',
                'registration_date' => '2024-01-02',
                'status' => 'active',
                'kelas' => '9B'
            ]
        ];

        foreach ($members as $memberData) {
            Member::create($memberData);
        }

        // Sample Borrowings
        $borrowings = [
            [
                'book_id' => 1,
                'member_id' => 1,
                'borrow_date' => '2024-01-15',
                'due_date' => '2024-01-22',
                'status' => 'borrowed',
                'created_at' => '2024-01-15 10:00:00',
                'updated_at' => '2024-01-15 10:00:00'
            ],
            [
                'book_id' => 2,
                'member_id' => 2,
                'borrow_date' => '2024-01-10',
                'due_date' => '2024-01-17',
                'status' => 'returned',
                'return_date' => '2024-01-16',
                'created_at' => '2024-01-10 14:00:00',
                'updated_at' => '2024-01-16 09:00:00'
            ]
        ];

        foreach ($borrowings as $borrowingData) {
            Borrowing::create($borrowingData);
        }
    }
}
