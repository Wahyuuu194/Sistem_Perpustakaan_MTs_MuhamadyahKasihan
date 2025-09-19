@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-8">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Daftar Buku</h1>
        <a href="{{ route('books.create') }}" class="bg-blue-600 text-white px-3 py-2 sm:px-4 rounded-lg hover:bg-blue-700 transition text-sm sm:text-base text-center">
            <i class="fas fa-plus mr-1 sm:mr-2"></i><span class="hidden sm:inline">Tambah Buku</span><span class="sm:hidden">Tambah</span>
        </a>
    </div>

    <!-- Search Form -->
    <div class="bg-white rounded-lg shadow p-4">
        <form action="{{ route('books.index') }}" method="GET" class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Cari buku berdasarkan judul, penulis, penerbit, kategori, kelas, atau ISBN..."
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div class="sm:w-48">
                    <select name="category" class="block w-full px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ request('category') == 'all' || !request('category') ? 'selected' : '' }}>Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-2">
                    <button type="submit" class="bg-blue-600 text-white px-3 py-2 sm:px-4 rounded-md hover:bg-blue-700 transition text-sm sm:text-base">
                        <i class="fas fa-search mr-1 sm:mr-2"></i>Cari
                    </button>
                    @if(request('search') || (request('category') && request('category') !== 'all'))
                        <a href="{{ route('books.index') }}" class="bg-gray-500 text-white px-3 py-2 sm:px-4 rounded-md hover:bg-gray-600 transition text-sm sm:text-base text-center">
                            <i class="fas fa-times mr-1 sm:mr-2"></i>Reset
                        </a>
                    @endif
                </div>
            </div>
            
            @if(request('search') || (request('category') && request('category') !== 'all'))
                <div class="text-sm text-gray-600 border-t pt-3">
                    <i class="fas fa-info-circle mr-1"></i>
                    @if(request('search') && request('category') && request('category') !== 'all')
                        Menampilkan hasil pencarian untuk: <strong>"{{ request('search') }}"</strong> dalam kategori <strong>"{{ request('category') }}"</strong>
                    @elseif(request('search'))
                        Menampilkan hasil pencarian untuk: <strong>"{{ request('search') }}"</strong>
                    @elseif(request('category') && request('category') !== 'all')
                        Menampilkan buku dalam kategori: <strong>"{{ request('category') }}"</strong>
                    @endif
                    <span class="text-gray-500">({{ $books->count() }} buku ditemukan)</span>
                </div>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-4 sm:p-6">
            @if($books->count() > 0)
                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penulis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ISBN</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($books as $book)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-3">
                                            @if($book->cover_image)
                                                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Cover {{ $book->title }}" 
                                                    class="w-12 h-16 object-cover rounded border shadow-sm">
                                            @else
                                                <div class="w-12 h-16 bg-gray-200 rounded border flex items-center justify-center">
                                                    <i class="fas fa-book text-gray-400"></i>
                                                </div>
                                            @endif
                                            
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <div class="text-sm font-medium text-gray-900">{{ $book->title }}</div>
                                                    @if($book->kelas)
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                                            {{ $book->kelas }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $book->publisher }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $book->author }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $book->isbn }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $book->category ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            @if($book->available_quantity > 0) bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $book->available_quantity }}/{{ $book->quantity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('books.show', $book) }}" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('books.edit', $book) }}" class="text-indigo-600 hover:text-indigo-900">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('books.destroy', $book) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin menghapus buku ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-4">
                    @foreach($books as $book)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-start space-x-3 mb-3">
                                @if($book->cover_image)
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Cover {{ $book->title }}" 
                                        class="w-16 h-20 object-cover rounded border shadow-sm flex-shrink-0">
                                @else
                                    <div class="w-16 h-20 bg-gray-200 rounded border flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-book text-gray-400 text-lg"></i>
                                    </div>
                                @endif
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">{{ $book->title }}</h3>
                                            <p class="text-sm text-gray-600 mt-1">{{ $book->author }}</p>
                                            <p class="text-sm text-gray-500">{{ $book->publisher }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            @if($book->available_quantity > 0) bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif ml-2 flex-shrink-0">
                                            {{ $book->available_quantity }}/{{ $book->quantity }}
                                        </span>
                                    </div>
                                    
                                    @if($book->kelas)
                                        <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800 mt-2">
                                            {{ $book->kelas }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">ISBN</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $book->isbn }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Kategori</p>
                                    <p class="text-sm text-gray-900">{{ $book->category ?? '-' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('books.show', $book) }}" class="text-blue-600 hover:text-blue-900 p-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('books.edit', $book) }}" class="text-indigo-600 hover:text-indigo-900 p-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('books.destroy', $book) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 p-2" onclick="return confirm('Yakin ingin menghapus buku ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-book text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Belum ada buku yang ditambahkan</p>
                    <a href="{{ route('books.create') }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        Tambah Buku Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
