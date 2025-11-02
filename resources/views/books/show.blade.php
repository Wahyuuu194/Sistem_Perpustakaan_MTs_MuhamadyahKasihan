@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-6 px-2 sm:px-8">
    <div class="bg-white rounded-lg shadow px-4 py-8 sm:px-10 sm:py-10">
        <div>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Detail Buku</h1>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <a href="{{ route('books.edit', $book) }}" class="w-full sm:w-auto bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition text-center text-sm sm:text-base">
                        <i class="fas fa-edit mr-2"></i>Edit Buku
                    </a>
                    <a href="{{ route('books.index') }}" class="w-full sm:w-auto bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-center text-sm sm:text-base">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-12">
                <div class="md:col-span-1 flex items-center justify-center">
                    @if($book->cover_image)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Cover {{ $book->title }}" 
                                class="w-full max-w-xs mx-auto object-cover rounded-lg border shadow-lg">
                        </div>
                    @else
                        <div class="mb-4">
                            <div class="w-56 h-80 bg-gray-200 rounded-lg border flex items-center justify-center">
                                <i class="fas fa-book text-gray-400 text-6xl"></i>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="md:col-span-2">
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-4">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900 break-words">{{ $book->title }}</h2>
                        @if($book->kelas)
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-purple-100 text-purple-800">
                                {{ $book->kelas }}
                            </span>
                        @endif
                        @if($book->rak)
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">
                                Rak {{ $book->rak }}
                            </span>
                        @endif
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Penulis:</span>
                            <p class="text-lg text-gray-900">{{ $book->author }}</p>
                        </div>
                        
                        <div>
                            <span class="text-sm font-medium text-gray-500">ISBN:</span>
                            <p class="text-lg text-gray-900">{{ $book->isbn ?? 'Tidak ada ISBN' }}</p>
                        </div>
                        
                        @if($book->publisher)
                        <div>
                            <span class="text-sm font-medium text-gray-500">Penerbit:</span>
                            <p class="text-lg text-gray-900">{{ $book->publisher }}</p>
                        </div>
                        @endif
                        
                        <div>
                            <span class="text-sm font-medium text-gray-500">Kategori:</span>
                            <p class="text-lg text-gray-900">{{ $book->category ?? 'Tidak ada kategori' }}</p>
                        </div>
                        
                        @if($book->rak)
                        <div>
                            <span class="text-sm font-medium text-gray-500">Rak:</span>
                            <p class="text-lg text-gray-900">Rak {{ $book->rak }}</p>
                        </div>
                        @endif
                        
                        <div>
                            <span class="text-sm font-medium text-gray-500">Stok:</span>
                            <div class="flex items-center space-x-2">
                                <span class="text-lg font-bold text-gray-900">{{ $book->available_quantity }}/{{ $book->quantity }}</span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if($book->available_quantity > 0) bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $book->available_quantity > 0 ? 'Tersedia' : 'Tidak Tersedia' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Riwayat Peminjaman</h3>
                    
                    @if($book->borrowings->count() > 0)
                        <div class="space-y-3">
                            @foreach($book->borrowings->take(5) as $borrowing)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $borrowing->member->name }}</p>
                                            <p class="text-sm text-gray-600">{{ $borrowing->member->member_id }}</p>
                                            <p class="text-xs text-gray-500">{{ $borrowing->member->kelas ?? 'Kelas belum ditentukan' }}</p>
                                            <p class="text-sm text-gray-500">{{ $borrowing->borrow_date->format('d/m/Y') }} - {{ $borrowing->due_date->format('d/m/Y') }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            @if($borrowing->status === 'borrowed') 
                                                @if($borrowing->due_date->isPast()) bg-red-100 text-red-800
                                                @else bg-blue-100 text-blue-800 @endif
                                            @elseif($borrowing->status === 'returned') bg-green-100 text-green-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            @if($borrowing->status === 'borrowed' && $borrowing->due_date->isPast())
                                                Terlambat
                                            @elseif($borrowing->status === 'borrowed')
                                                Dipinjam
                                            @elseif($borrowing->status === 'returned')
                                                Dikembalikan
                                            @else
                                                {{ $borrowing->status_label }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($book->borrowings->count() > 5)
                            <p class="text-sm text-gray-500 mt-3">Dan {{ $book->borrowings->count() - 5 }} peminjaman lainnya...</p>
                        @endif
                    @else
                        <p class="text-gray-500">Belum ada riwayat peminjaman</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
