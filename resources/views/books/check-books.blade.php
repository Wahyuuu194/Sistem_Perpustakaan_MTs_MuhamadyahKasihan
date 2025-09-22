@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Data Buku yang Sudah Diimport</h1>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('books.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-center">
                    <i class="fas fa-list mr-2"></i>Daftar Buku
                </a>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('check-books') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search Input -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari Buku</label>
                        <div class="relative">
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                placeholder="Cari berdasarkan judul, penulis, atau penerbit..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select name="category" id="category" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort Options -->
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                        <select name="sort" id="sort" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="id_asc" {{ request('sort') == 'id_asc' ? 'selected' : '' }}>ID (A-Z)</option>
                            <option value="id_desc" {{ request('sort') == 'id_desc' ? 'selected' : '' }}>ID (Z-A)</option>
                            <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Judul (A-Z)</option>
                            <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Judul (Z-A)</option>
                            <option value="category_asc" {{ request('sort') == 'category_asc' ? 'selected' : '' }}>Kategori (A-Z)</option>
                            <option value="category_desc" {{ request('sort') == 'category_desc' ? 'selected' : '' }}>Kategori (Z-A)</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-search mr-2"></i>Cari
                    </button>
                    <a href="{{ route('check-books') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition text-center">
                        <i class="fas fa-refresh mr-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Results Summary -->
        @if(request()->hasAny(['search', 'category', 'sort']))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Menampilkan {{ $books->count() }} dari {{ $totalBooks }} buku
                    @if(request('search'))
                        untuk pencarian "<strong>{{ request('search') }}</strong>"
                    @endif
                    @if(request('category'))
                        dalam kategori "<strong>{{ request('category') }}</strong>"
                    @endif
                </p>
            </div>
        @endif

        <!-- Books Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                            ID
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                            Judul
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                            Kategori
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                            Penulis
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                            Penerbit
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($books as $book)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-sm text-gray-900 border-b">
                                <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $book->id }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 border-b">
                                <div class="max-w-xs">
                                    <p class="font-medium text-gray-900 truncate" title="{{ $book->title }}">
                                        {{ $book->title }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 border-b">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($book->category == 'Agama & Keagamaan') bg-purple-100 text-purple-800
                                    @elseif($book->category == 'Pendidikan & Pelajaran') bg-blue-100 text-blue-800
                                    @elseif($book->category == 'Referensi & Kamus') bg-green-100 text-green-800
                                    @elseif($book->category == 'Teknologi & Sains') bg-yellow-100 text-yellow-800
                                    @elseif($book->category == 'Buku Bacaan - Fiksi') bg-pink-100 text-pink-800
                                    @elseif($book->category == 'Buku Bacaan - Non-Fiksi') bg-indigo-100 text-indigo-800
                                    @elseif($book->category == 'Sejarah & Budaya') bg-orange-100 text-orange-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $book->category }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 border-b">
                                <div class="max-w-xs">
                                    <p class="truncate" title="{{ $book->author }}">
                                        {{ $book->author }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 border-b">
                                <div class="max-w-xs">
                                    <p class="truncate" title="{{ $book->publisher }}">
                                        {{ $book->publisher }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 border-b">
                                <div class="flex space-x-2">
                                    <a href="{{ route('books.show', $book->id) }}" 
                                        class="text-blue-600 hover:text-blue-800 transition" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('books.edit', $book->id) }}" 
                                        class="text-green-600 hover:text-green-800 transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-book-open text-4xl text-gray-300 mb-2"></i>
                                    <p class="text-lg font-medium">Tidak ada buku ditemukan</p>
                                    <p class="text-sm">Coba ubah kata kunci pencarian atau filter</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($books->hasPages())
            <div class="mt-6">
                {{ $books->appends(request()->query())->links() }}
            </div>
        @endif

        <!-- Category Summary -->
        <div class="mt-8 bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Kategori</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($categoryCounts as $category => $count)
                    <div class="bg-white rounded-lg p-3 border">
                        <p class="text-sm font-medium text-gray-900">{{ $category }}</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $count }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when category or sort changes
    const categorySelect = document.getElementById('category');
    const sortSelect = document.getElementById('sort');
    
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    // Clear search on Escape key
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                this.form.submit();
            }
        });
    }
});
</script>
@endsection

