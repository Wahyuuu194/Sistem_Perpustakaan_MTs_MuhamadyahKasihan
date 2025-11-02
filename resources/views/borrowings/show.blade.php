@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-4 sm:py-6 px-4 sm:px-0">
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Detail Peminjaman</h1>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    @if($borrowing->status === 'borrowed')
                        <form action="{{ route('borrowings.return', $borrowing) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full sm:w-auto bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm sm:text-base" onclick="return confirm('Yakin ingin mengembalikan buku ini?')">
                                <i class="fas fa-undo mr-2"></i>Kembalikan Buku
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('borrowings.index') }}" class="w-full sm:w-auto bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-center text-sm sm:text-base">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4">Informasi Peminjaman</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Status:</span>
                            <span class="px-2 py-1 text-sm font-medium rounded-full ml-2
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
                        
                        <div>
                            <span class="text-sm font-medium text-gray-500">Tanggal Pinjam:</span>
                            <p class="text-lg text-gray-900">{{ $borrowing->borrow_date->format('d/m/Y') }}</p>
                        </div>
                        
                        <div>
                            <span class="text-sm font-medium text-gray-500">Jumlah Buku:</span>
                            <p class="text-lg text-gray-900">
                                <span class="px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">
                                    {{ $borrowing->quantity }} buku
                                </span>
                            </p>
                        </div>
                        
                        <div>
                            <span class="text-sm font-medium text-gray-500">Jatuh Tempo:</span>
                            <p class="text-lg text-gray-900">{{ $borrowing->due_date->format('d/m/Y') }}</p>
                        </div>
                        
                        @if($borrowing->return_date)
                        <div>
                            <span class="text-sm font-medium text-gray-500">Tanggal Kembali:</span>
                            <p class="text-lg text-gray-900">{{ $borrowing->return_date->format('d/m/Y') }}</p>
                        </div>
                        @endif
                        
                        @if($borrowing->due_date->isPast() && $borrowing->status === 'borrowed')
                        <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-red-800 text-sm">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Buku terlambat dikembalikan
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Buku</h3>
                    
                    <div class="p-3 sm:p-4 bg-gray-50 rounded-lg">
                        <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4">
                            @if($borrowing->book->cover_image)
                                <div class="flex-shrink-0 mx-auto sm:mx-0">
                                    <img src="{{ asset('storage/' . $borrowing->book->cover_image) }}" alt="Cover {{ $borrowing->book->title }}" 
                                        class="w-20 h-28 sm:w-24 sm:h-32 object-cover rounded-lg border shadow-sm">
                                </div>
                            @else
                                <div class="flex-shrink-0 mx-auto sm:mx-0">
                                    <div class="w-20 h-28 sm:w-24 sm:h-32 bg-gray-200 rounded-lg border flex items-center justify-center">
                                        <i class="fas fa-book text-gray-400 text-xl sm:text-2xl"></i>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="flex-1 w-full">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <h4 class="font-medium text-gray-900 text-base sm:text-lg break-words">{{ $borrowing->book->title }}</h4>
                                    @if($borrowing->book->kelas)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                            {{ $borrowing->book->kelas }}
                                        </span>
                                    @endif
                                    @if($borrowing->book->rak)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                            Rak {{ $borrowing->book->rak }}
                                        </span>
                                    @endif
                                </div>
                                <div class="space-y-2 text-sm text-gray-600">
                                    <p><strong>Penulis:</strong> {{ $borrowing->book->author }}</p>
                                    <p><strong>ISBN:</strong> {{ $borrowing->book->isbn ?? 'Tidak ada ISBN' }}</p>
                                    @if($borrowing->book->publisher)
                                        <p><strong>Penerbit:</strong> {{ $borrowing->book->publisher }}</p>
                                    @endif
                                    <p><strong>Kategori:</strong> {{ $borrowing->book->category ?? 'Tidak ada kategori' }}</p>
                                    @if($borrowing->book->rak)
                                        <p><strong>Rak:</strong> Rak {{ $borrowing->book->rak }}</p>
                                    @endif
                                    <p><strong>Stok:</strong> {{ $borrowing->book->available_quantity }}/{{ $borrowing->book->quantity }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-4 mt-6">Informasi Anggota</h3>
                    
                    <div class="p-3 sm:p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-medium text-gray-900 text-base sm:text-lg mb-2 break-words">{{ $borrowing->member->name }}</h4>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p><strong>NIS/NISN:</strong> {{ $borrowing->member->member_id }}</p>
                            <p><strong>Kelas:</strong> 
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                    {{ $borrowing->member->kelas ?? 'Belum ditentukan' }}
                                </span>
                            </p>
                            @if($borrowing->member->phone)
                                <p><strong>Telepon:</strong> {{ $borrowing->member->phone }}</p>
                            @endif
                            <p><strong>Status:</strong> 
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if($borrowing->member->status === 'active') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $borrowing->member->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection