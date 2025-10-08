@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Edit Buku</h1>
            <a href="{{ route('books.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar
            </a>
        </div>

        <form action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul Buku *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $book->title) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="author" class="block text-sm font-medium text-gray-700 mb-2">Penulis *</label>
                    <input type="text" name="author" id="author" value="{{ old('author', $book->author) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('author')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="isbn" class="block text-sm font-medium text-gray-700 mb-2">ISBN</label>
                    <input type="text" name="isbn" id="isbn" value="{{ old('isbn', $book->isbn) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('isbn')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="publisher" class="block text-sm font-medium text-gray-700 mb-2">Penerbit</label>
                    <input type="text" name="publisher" id="publisher" value="{{ old('publisher', $book->publisher) }}"
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
                        <option value="Agama & Keagamaan" {{ old('category', $book->category) == 'Agama & Keagamaan' ? 'selected' : '' }}>📖 Agama & Keagamaan</option>
                        <option value="Pendidikan & Pelajaran" {{ old('category', $book->category) == 'Pendidikan & Pelajaran' ? 'selected' : '' }}>🎓 Pendidikan & Pelajaran</option>
                        <option value="Referensi & Kamus" {{ old('category', $book->category) == 'Referensi & Kamus' ? 'selected' : '' }}>📚 Referensi & Kamus</option>
                        <option value="Teknologi & Sains" {{ old('category', $book->category) == 'Teknologi & Sains' ? 'selected' : '' }}>💻 Teknologi & Sains</option>
                        <option value="Buku Bacaan - Fiksi" {{ old('category', $book->category) == 'Buku Bacaan - Fiksi' ? 'selected' : '' }}>📖 Buku Bacaan - Fiksi</option>
                        <option value="Buku Bacaan - Non-Fiksi" {{ old('category', $book->category) == 'Buku Bacaan - Non-Fiksi' ? 'selected' : '' }}>📖 Buku Bacaan - Non-Fiksi</option>
                        <option value="Sejarah & Budaya" {{ old('category', $book->category) == 'Sejarah & Budaya' ? 'selected' : '' }}>📜 Sejarah & Budaya</option>
                        <option value="Lainnya" {{ old('category', $book->category) == 'Lainnya' ? 'selected' : '' }}>📋 Lainnya</option>
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
                        <option value="VII" {{ old('kelas', $book->kelas) == 'VII' ? 'selected' : '' }}>VII</option>
                        <option value="VIII" {{ old('kelas', $book->kelas) == 'VIII' ? 'selected' : '' }}>VIII</option>
                        <option value="IX" {{ old('kelas', $book->kelas) == 'IX' ? 'selected' : '' }}>IX</option>
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
                        <option value="A" {{ old('rak', $book->rak) == 'A' ? 'selected' : '' }}>Rak A</option>
                        <option value="B" {{ old('rak', $book->rak) == 'B' ? 'selected' : '' }}>Rak B</option>
                        <option value="C" {{ old('rak', $book->rak) == 'C' ? 'selected' : '' }}>Rak C</option>
                        <option value="D" {{ old('rak', $book->rak) == 'D' ? 'selected' : '' }}>Rak D</option>
                        <option value="E" {{ old('rak', $book->rak) == 'E' ? 'selected' : '' }}>Rak E</option>
                        <option value="F" {{ old('rak', $book->rak) == 'F' ? 'selected' : '' }}>Rak F</option>
                        <option value="G" {{ old('rak', $book->rak) == 'G' ? 'selected' : '' }}>Rak G</option>
                    </select>
                    @error('rak')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>


                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Total *</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $book->quantity) }}" min="1" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="available_quantity" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Tersedia *</label>
                    <input type="number" name="available_quantity" id="available_quantity" value="{{ old('available_quantity', $book->available_quantity) }}" min="0" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('available_quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-2">Foto Cover Buku</label>
                    @if($book->cover_image)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Cover Buku" class="w-32 h-40 object-cover rounded-lg border">
                            <p class="text-xs text-gray-500 mt-1">Foto cover saat ini</p>
                        </div>
                    @endif
                    <input type="file" name="cover_image" id="cover_image" accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB. Bisa dilewati.</p>
                    @error('cover_image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('books.index') }}" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Update Buku
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
