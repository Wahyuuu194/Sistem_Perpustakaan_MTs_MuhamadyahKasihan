@extends('layouts.app')

@section('content')
<div class="space-y-8 pb-8">
    <!-- Header Section -->
    <div class="flex justify-between items-center">
        <!-- <h1 class="text-3xl font-bold text-gray-900">Beranda</h1> -->
        <!-- <div class="flex space-x-3">
            <a href="{{ route('books.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Tambah Buku
            </a>
            <a href="{{ route('members.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-user-plus mr-2"></i>Tambah Anggota
            </a>
            <a href="{{ route('borrowings.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-handshake mr-2"></i>Peminjaman Baru
            </a>
        </div> -->
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-book text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Buku</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalBooks }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Anggota</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalMembers }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-handshake text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Sedang Dipinjam</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalBorrowed }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Terlambat</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $overdueBooks }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Borrowings -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Peminjaman Terbaru</h3>
            </div>
            <div class="p-6">
                @if($recentBorrowings->count() > 0)
                    <div class="space-y-2  max-h-64 overflow-y-auto">
                        @foreach($recentBorrowings as $borrowing)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    @if($borrowing->book->cover_image)
                                        <img src="{{ asset('storage/' . $borrowing->book->cover_image) }}" alt="Cover {{ $borrowing->book->title }}" 
                                            class="w-12 h-16 object-cover rounded border shadow-sm">
                                    @else
                                        <div class="w-12 h-16 bg-gray-200 rounded border flex items-center justify-center">
                                            <i class="fas fa-book text-gray-400"></i>
                                        </div>
                                    @endif
                                    
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="font-medium text-gray-900">{{ $borrowing->book->title }}</p>
                                            @if($borrowing->book->kelas)
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                                    {{ $borrowing->book->kelas }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600">{{ $borrowing->member->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $borrowing->member->student_id ?? 'N/A' }} - {{ $borrowing->member->kelas ?? 'Kelas belum ditentukan' }}</p>
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if($borrowing->status === 'borrowed') bg-blue-100 text-blue-800
                                    @elseif($borrowing->status === 'returned') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $borrowing->status_label }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">Belum ada peminjaman</p>
                @endif
            </div>
        </div>

        <!-- Popular Books -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Buku Terpopuler</h3>
            </div>
            <div class="p-6">
                @if($popularBooks->count() > 0)
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($popularBooks as $book)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="font-medium text-gray-900">{{ $book->title }}</p>
                                        @if($book->kelas)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                                {{ $book->kelas }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $book->author }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                    {{ $book->borrowings_count }}x dipinjam
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">Belum ada data peminjaman</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('books.index') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                <i class="fas fa-book text-blue-600 text-xl mr-3"></i>
                <div>
                    <p class="font-medium text-blue-900">Kelola Buku</p>
                    <p class="text-sm text-blue-600">Lihat dan edit daftar buku</p>
                </div>
            </a>
            <a href="{{ route('members.index') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                <i class="fas fa-users text-green-600 text-xl mr-3"></i>
                <div>
                    <p class="font-medium text-green-900">Kelola Anggota</p>
                    <p class="text-sm text-green-600">Lihat dan edit data anggota</p>
                </div>
            </a>
            <a href="{{ route('borrowings.index') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                <i class="fas fa-handshake text-purple-600 text-xl mr-3"></i>
                <div>
                    <p class="font-medium text-purple-900">Kelola Peminjaman</p>
                    <p class="text-sm text-purple-600">Lihat status peminjaman</p>
                </div>
            </a>
        </div>
    </div>
    </div>
</div>
@endsection
