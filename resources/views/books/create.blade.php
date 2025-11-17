@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-4 sm:py-6 px-4 sm:px-0">
    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Tambah Buku Baru</h1>
            <a href="{{ route('books.index') }}" class="text-blue-600 hover:text-blue-800 text-sm sm:text-base">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul Buku</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="author" class="block text-sm font-medium text-gray-700 mb-2">Penulis </label>
                    <input type="text" name="author" id="author" value="{{ old('author') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('author')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="isbn" class="block text-sm font-medium text-gray-700 mb-2">ISBN</label>
                    <input type="text" name="isbn" id="isbn" value="{{ old('isbn') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('isbn')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="publisher" class="block text-sm font-medium text-gray-700 mb-2">Penerbit</label>
                    <input type="text" name="publisher" id="publisher" value="{{ old('publisher') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('publisher')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="category" id="category" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Kategori</option>
                        <option value="Agama & Keagamaan" {{ old('category') == 'Agama & Keagamaan' ? 'selected' : '' }}>📖 Agama & Keagamaan</option>
                        <option value="Pendidikan & Pelajaran" {{ old('category') == 'Pendidikan & Pelajaran' ? 'selected' : '' }}>🎓 Pendidikan & Pelajaran</option>
                        <option value="Referensi & Kamus" {{ old('category') == 'Referensi & Kamus' ? 'selected' : '' }}>📚 Referensi & Kamus</option>
                        <option value="Teknologi & Sains" {{ old('category') == 'Teknologi & Sains' ? 'selected' : '' }}>💻 Teknologi & Sains</option>
                        <option value="Buku Bacaan - Fiksi" {{ old('category') == 'Buku Bacaan - Fiksi' ? 'selected' : '' }}>📖 Buku Bacaan - Fiksi</option>
                        <option value="Buku Bacaan - Non-Fiksi" {{ old('category') == 'Buku Bacaan - Non-Fiksi' ? 'selected' : '' }}>📖 Buku Bacaan - Non-Fiksi</option>
                        <option value="Sejarah & Budaya" {{ old('category') == 'Sejarah & Budaya' ? 'selected' : '' }}>📜 Sejarah & Budaya</option>
                        <option value="Lainnya" {{ old('category') == 'Lainnya' ? 'selected' : '' }}>📋 Lainnya</option>
                    </select>
                    @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="kelas" class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                    <select name="kelas" id="kelas" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Kelas</option>
                        <option value="VII" {{ old('kelas') == 'VII' ? 'selected' : '' }}>VII</option>
                        <option value="VIII" {{ old('kelas') == 'VIII' ? 'selected' : '' }}>VIII</option>
                        <option value="IX" {{ old('kelas') == 'IX' ? 'selected' : '' }}>IX</option>
                    </select>
                    @error('kelas')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="rak" class="block text-sm font-medium text-gray-700 mb-2">Rak</label>
                    <select name="rak" id="rak" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Rak</option>
                        <option value="A" {{ old('rak') == 'A' ? 'selected' : '' }}>Rak A</option>
                        <option value="B" {{ old('rak') == 'B' ? 'selected' : '' }}>Rak B</option>
                        <option value="C" {{ old('rak') == 'C' ? 'selected' : '' }}>Rak C</option>
                        <option value="D" {{ old('rak') == 'D' ? 'selected' : '' }}>Rak D</option>
                        <option value="E" {{ old('rak') == 'E' ? 'selected' : '' }}>Rak E</option>
                        <option value="F" {{ old('rak') == 'F' ? 'selected' : '' }}>Rak F</option>
                        <option value="G" {{ old('rak') == 'G' ? 'selected' : '' }}>Rak G</option>
                    </select>
                    @error('rak')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>


                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Total </label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="available_quantity" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Tersedia </label>
                    <input type="number" name="available_quantity" id="available_quantity" value="{{ old('available_quantity', 1) }}" min="0" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('available_quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-2">Foto Cover Buku</label>
                    <input type="file" name="cover_image" id="cover_image" accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, Maksimal 2MB.</p>
                    <p class="text-xs text-gray-500 mt-1"></p>
                    <p class="text-xs text-red-500 mt-1">Bisa dilewati.</p>
                    @error('cover_image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
                <a href="{{ route('books.index') }}" class="w-full sm:w-auto px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition text-center text-sm sm:text-base">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-sm sm:text-base">
                    <i class="fas fa-save mr-2"></i>Simpan Buku
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
