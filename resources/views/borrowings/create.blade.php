@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-4 sm:py-6 px-4 sm:px-0">
    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Buat Peminjaman Baru</h1>
            <a href="{{ route('borrowings.index') }}" class="text-blue-600 hover:text-blue-800 text-sm sm:text-base">
                <i class="fas fa-arrow-left mr-1 sm:mr-2"></i>Kembali 
            </a>
        </div>

        <form action="{{ route('borrowings.store') }}" method="POST">
            @csrf
            
            <!-- Book Selection Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-book text-blue-600 mr-2"></i>
                    Pilih Buku
                </h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <label for="book_id" class="block text-sm font-medium text-gray-700 mb-2">Buku yang akan dipinjam</label>
                    
                    <!-- Search Input -->
                    <div class="relative mb-3">
                        <input type="text" id="book_search" placeholder="Cari buku berdasarkan judul, pengarang, kategori, atau ISBN..." 
                            class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="absolute right-3 top-2.5 text-gray-400">
                            <i class="fas fa-times cursor-pointer hover:text-gray-600" id="clear_search" style="display: none;"></i>
                        </div>
                    </div>
                    
                    <!-- Search Results -->
                    <div id="search_results" class="mb-3 max-h-48 overflow-y-auto border border-gray-300 rounded-md bg-white shadow-lg" style="display: none;">
                        <div class="p-2 text-sm text-gray-500 border-b">
                            <i class="fas fa-search mr-1"></i>Hasil Pencarian
                        </div>
                        <div id="search_results_list" class="divide-y divide-gray-100">
                            <!-- Search results will be populated here -->
                        </div>
                    </div>
                    
                    <!-- Book Selection Dropdown -->
                    <select name="book_id" id="book_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Buku</option>
                        @foreach($books as $book)
                            <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}
                                data-title="{{ $book->title }}"
                                data-author="{{ $book->author }}"
                                data-isbn="{{ $book->isbn }}"
                                data-kelas="{{ $book->kelas }}"
                                data-rak="{{ $book->rak }}"
                                data-category="{{ $book->category }}"
                                data-available="{{ $book->available_quantity }}"
                                data-search="{{ strtolower($book->title . ' ' . $book->author . ' ' . $book->isbn . ' ' . $book->kelas . ' ' . $book->rak . ' ' . $book->category) }}">
                                {{ $book->title }}@if($book->kelas) [{{ $book->kelas }}] @endif @if($book->rak) [Rak {{ $book->rak }}] @endif - {{ $book->author }} (Stok: {{ $book->available_quantity }})

                            </option>
                        @endforeach
                    </select>
                        <!-- Input jumlah buku yang akan dipinjam -->
                        <div class="mt-4">
                            <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Buku yang Akan Dipinjam</label>
                            <input type="number" name="jumlah" id="jumlah" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="1" value="{{ old('jumlah', 1) }}" required>
                            <div class="flex items-center justify-between mt-1">
                                <small id="jumlah-info" class="text-xs text-gray-500">Maksimal sesuai stok tersedia.</small>
                                <span id="stok-tersedia" class="text-xs font-semibold text-blue-600">Stok tersedia: -</span>
                            </div>
                            @error('jumlah')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    
                    <!-- Selected Book Info -->
                    <div id="selected_book_info" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-md" style="display: none;">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-blue-800 mb-1">Informasi Buku Terpilih</h4>
                                <div class="text-sm text-blue-700">
                                    <p><strong>Judul:</strong> <span id="selected_title"></span></p>
                                    <p><strong>Pengarang:</strong> <span id="selected_author"></span></p>
                                    <p><strong>Kategori:</strong> <span id="selected_category"></span></p>
                                    <p><strong>ISBN:</strong> <span id="selected_isbn"></span></p>
                                    <p><strong>Kelas:</strong> <span id="selected_kelas"></span></p>
                                    <p><strong>Rak:</strong> <span id="selected_rak"></span></p>
                                    <p><strong>Stok Tersedia:</strong> <span id="selected_available" class="font-semibold"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @error('book_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Borrower Selection Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user text-green-600 mr-2"></i>
                    Pilih Peminjam
                </h3>
                
                <!-- Peminjam Type Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Jenis Peminjam</label>
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-6">
                        <label class="flex items-center p-3 border-2 border-green-200 rounded-lg cursor-pointer hover:bg-green-50 transition flex-1">
                            <input type="radio" name="borrower_type" value="student" id="borrower_student" checked
                                class="mr-3 text-green-600 focus:ring-green-500">
                            <div class="flex items-center">
                                <i class="fas fa-users text-green-600 mr-2"></i>
                                <span class="text-sm font-medium text-gray-700">Siswa</span>
                            </div>
                        </label>
                        <label class="flex items-center p-3 border-2 border-blue-200 rounded-lg cursor-pointer hover:bg-blue-50 transition flex-1">
                            <input type="radio" name="borrower_type" value="teacher" id="borrower_teacher"
                                class="mr-3 text-blue-600 focus:ring-blue-500">
                            <div class="flex items-center">
                                <i class="fas fa-chalkboard-teacher text-blue-600 mr-2"></i>
                                <span class="text-sm font-medium text-gray-700">Guru</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Student Section -->
                <div id="student-section" class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-users text-green-600 mr-2"></i>
                        <h4 class="text-sm font-semibold text-green-800">Data Siswa</h4>
                    </div>
                    
                    <!-- Scan QR Code Button for Student -->
                    <div class="mb-4">
                        <div class="bg-white border border-green-200 rounded-lg p-3">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="flex items-center flex-1">
                                    <i class="fas fa-qrcode text-green-600 text-lg mr-3 flex-shrink-0"></i>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-semibold text-green-800 mb-1">Scan QR Code Kartu Akses Siswa</h3>
                                        <p class="text-xs text-green-600">Gunakan kamera untuk scan QR code dari kartu akses siswa</p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="button" id="scanMemberBtn" class="w-full sm:w-auto px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition font-medium text-sm">
                                        <i class="fas fa-camera mr-1"></i>Scan QR
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Student Selection Dropdown -->
                    <div>
                        <label for="member_id" class="block text-sm font-medium text-gray-700 mb-2">Atau pilih siswa secara manual</label>
                        <select name="member_id" id="member_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Pilih Siswa</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}
                                    data-member-id="{{ $member->member_id }}"
                                    data-name="{{ $member->name }}"
                                    data-kelas="{{ $member->kelas }}">
                                    {{ $member->name }} ({{ $member->member_id }}) - {{ $member->kelas }}
                                </option>
                            @endforeach
                        </select>
                        @error('member_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Teacher Section -->
                <div id="teacher-section" style="display: none;" class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-chalkboard-teacher text-blue-600 mr-2"></i>
                        <h4 class="text-sm font-semibold text-blue-800">Data Guru</h4>
                    </div>
                    
                    <!-- Scan QR Code Button for Teacher -->
                    <div class="mb-4">
                        <div class="bg-white border border-blue-200 rounded-lg p-3">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="flex items-center flex-1">
                                    <i class="fas fa-qrcode text-blue-600 text-lg mr-3 flex-shrink-0"></i>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-semibold text-blue-800 mb-1">Scan QR Code Kartu Akses Guru</h3>
                                        <p class="text-xs text-blue-600">Gunakan kamera untuk scan QR code dari kartu akses guru</p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="button" id="scanTeacherBtn" class="w-full sm:w-auto px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium text-sm">
                                        <i class="fas fa-camera mr-1"></i>Scan QR
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Teacher Selection Dropdown -->
                    <div>
                        <label for="teacher_id" class="block text-sm font-medium text-gray-700 mb-2">Atau pilih guru secara manual</label>
                        <select name="teacher_id" id="teacher_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Guru</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}
                                    data-teacher-id="{{ $teacher->teacher_id }}"
                                    data-name="{{ $teacher->name }}"
                                    data-subject="{{ $teacher->subject }}">
                                    {{ $teacher->name }} ({{ $teacher->teacher_id }}) - {{ $teacher->subject ?? 'Tidak ada mata pelajaran' }}
                                </option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Date Selection Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-calendar text-purple-600 mr-2"></i>
                    Periode Peminjaman
                </h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="borrow_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pinjam</label>
                            <input type="date" name="borrow_date" id="borrow_date" value="{{ old('borrow_date', date('Y-m-d')) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                            @error('borrow_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengembalian</label>
                            <input type="date" name="due_date" id="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+7 days'))) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                            @error('due_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Information Section -->
            <div class="mb-8">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-blue-800 mb-2">Informasi Peminjaman</h3>
                            <div class="text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Buku hanya bisa dipinjam jika stok tersedia</li>
                                    <li>Durasi peminjaman default adalah 7 hari</li>
                                    <li>Stok buku akan berkurang otomatis saat dipinjam</li>
                                    <li>Status peminjaman akan otomatis menjadi "Dipinjam"</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('borrowings.index') }}" class="w-full sm:w-auto px-4 sm:px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-medium text-center">
                    <i class="fas fa-times mr-1 sm:mr-2"></i>Batal
                </a>
                <button type="submit" class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-save mr-1 sm:mr-2"></i>Buat Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

<!-- QR Scanner Modal -->
<div id="qrModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900" id="qrModalTitle">Scan QR Code</h3>
                    <button type="button" id="closeQrModal" class="text-gray-400 hover:text-gray-600 p-1">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <div id="qr-reader" style="width: 100%"></div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2" id="manualInputLabel">Atau masukkan ID manual:</label>
                    <input type="text" id="manualId" placeholder="Masukkan ID dari kartu akses"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3">
                    <button type="button" id="cancelQrScan" class="w-full sm:w-auto px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition">
                        Batal
                    </button>
                    <button type="button" id="processQrScan" class="w-full sm:w-auto px-4 py-2 text-white bg-green-600 rounded-md hover:bg-green-700 transition">
                        Proses Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include jsQR library -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const borrowerStudent = document.getElementById('borrower_student');
    const borrowerTeacher = document.getElementById('borrower_teacher');
    const studentSection = document.getElementById('student-section');
    const teacherSection = document.getElementById('teacher-section');
    const scanMemberBtn = document.getElementById('scanMemberBtn');
    const scanTeacherBtn = document.getElementById('scanTeacherBtn');
    const qrModal = document.getElementById('qrModal');
    const closeQrModal = document.getElementById('closeQrModal');
    const cancelQrScan = document.getElementById('cancelQrScan');
    const processQrScan = document.getElementById('processQrScan');
    const manualId = document.getElementById('manualId');
    const memberSelect = document.getElementById('member_id');
    const teacherSelect = document.getElementById('teacher_id');
    const qrModalTitle = document.getElementById('qrModalTitle');
    const manualInputLabel = document.getElementById('manualInputLabel');
    
    // Book search elements
    const bookSearch = document.getElementById('book_search');
    const bookSelect = document.getElementById('book_id');
    const clearSearch = document.getElementById('clear_search');
    const selectedBookInfo = document.getElementById('selected_book_info');
    const searchResults = document.getElementById('search_results');
    const searchResultsList = document.getElementById('search_results_list');
    
    let stream = null;
    let video = null;
    let canvas = null;
    let context = null;
    let scanning = false;
    let currentScanType = 'student'; // 'student' or 'teacher'

    // Book search functionality
    bookSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        if (searchTerm.length === 0) {
            searchResults.style.display = 'none';
            clearSearch.style.display = 'none';
            return;
        }
        
        // Show clear button
        clearSearch.style.display = 'block';
        
        // Get all book options
        const options = bookSelect.querySelectorAll('option');
        const matchingBooks = [];
        
        options.forEach(option => {
            if (option.value === '') return;
            
            const searchData = option.getAttribute('data-search') || '';
            if (searchData.includes(searchTerm)) {
                matchingBooks.push({
                    id: option.value,
                    title: option.getAttribute('data-title'),
                    author: option.getAttribute('data-author'),
                    category: option.getAttribute('data-category'),
                    isbn: option.getAttribute('data-isbn'),
                    kelas: option.getAttribute('data-kelas'),
                    available: option.getAttribute('data-available'),
                    text: option.textContent
                });
            }
        });
        
        // Display search results
        if (matchingBooks.length > 0) {
            searchResultsList.innerHTML = '';
            matchingBooks.forEach(book => {
                const resultItem = document.createElement('div');
                resultItem.className = 'p-3 hover:bg-gray-50 cursor-pointer transition';
                resultItem.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">${book.title}${book.kelas ? ` [${book.kelas}]` : ''}</h4>
                            <p class="text-xs text-gray-600">${book.author}</p>
                            <p class="text-xs text-gray-500">Kategori: ${book.category || 'N/A'} | ISBN: ${book.isbn || 'N/A'}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-medium ${parseInt(book.available) > 5 ? 'text-green-600' : parseInt(book.available) > 0 ? 'text-yellow-600' : 'text-red-600'}">
                                Stok: ${book.available}
                            </span>
                        </div>
                    </div>
                `;
                
                resultItem.addEventListener('click', function() {
                    bookSelect.value = book.id;
                    bookSelect.dispatchEvent(new Event('change'));
                    searchResults.style.display = 'none';
                    bookSearch.value = '';
                    clearSearch.style.display = 'none';
                });
                
                searchResultsList.appendChild(resultItem);
            });
            searchResults.style.display = 'block';
        } else {
            searchResultsList.innerHTML = '<div class="p-3 text-sm text-gray-500 text-center">Tidak ada buku yang ditemukan</div>';
            searchResults.style.display = 'block';
        }
    });
    
    // Handle Enter key in search field
    bookSearch.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            
            // If there are search results, select the first one
            const firstResult = searchResultsList.querySelector('.cursor-pointer');
            if (firstResult) {
                firstResult.click();
            }
            
            return false;
        }
    });
    
    // Clear search functionality
    clearSearch.addEventListener('click', function() {
        bookSearch.value = '';
        clearSearch.style.display = 'none';
        searchResults.style.display = 'none';
        bookSearch.focus();
    });
    
    // Auto-focus search when clicking on select
    bookSelect.addEventListener('focus', function() {
        bookSearch.focus();
    });
    
    // Hide search results when clicking outside
    document.addEventListener('click', function(event) {
        if (!bookSearch.contains(event.target) && !searchResults.contains(event.target)) {
            searchResults.style.display = 'none';
        }
    });
    
    // Show selected book info
    bookSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value && selectedOption.value !== '') {
            // Show book info
            document.getElementById('selected_title').textContent = selectedOption.getAttribute('data-title') || 'N/A';
            document.getElementById('selected_author').textContent = selectedOption.getAttribute('data-author') || 'N/A';
            document.getElementById('selected_category').textContent = selectedOption.getAttribute('data-category') || 'N/A';
            document.getElementById('selected_isbn').textContent = selectedOption.getAttribute('data-isbn') || 'N/A';
            document.getElementById('selected_kelas').textContent = selectedOption.getAttribute('data-kelas') || 'Semua Kelas';
            document.getElementById('selected_rak').textContent = selectedOption.getAttribute('data-rak') ? 'Rak ' + selectedOption.getAttribute('data-rak') : 'Tidak ada rak';
            document.getElementById('selected_available').textContent = selectedOption.getAttribute('data-available') || '0';
            
            // Add color coding for stock
            const availableElement = document.getElementById('selected_available');
            const available = parseInt(selectedOption.getAttribute('data-available') || '0');
            if (available > 5) {
                availableElement.className = 'font-semibold text-green-600';
            } else if (available > 0) {
                availableElement.className = 'font-semibold text-yellow-600';
            } else {
                availableElement.className = 'font-semibold text-red-600';
            }
            
            selectedBookInfo.style.display = 'block';

                // Update max jumlah sesuai stok buku
                const jumlahInput = document.getElementById('jumlah');
                const jumlahInfo = document.getElementById('jumlah-info');
            const stokTersedia = document.getElementById('stok-tersedia');
                if (jumlahInput) {
                    jumlahInput.max = available > 0 ? available : 1;
                    if (parseInt(jumlahInput.value) > available) {
                        jumlahInput.value = available;
                    }
                    jumlahInfo.textContent = `Maksimal peminjaman: ${available} buku.`;
                if (stokTersedia) {
                    stokTersedia.textContent = `Stok tersedia: ${available}`;
                    stokTersedia.className = 'text-xs font-semibold ' + (available > 5 ? 'text-green-600' : available > 0 ? 'text-yellow-600' : 'text-red-600');
                }
                }
        } else {
            selectedBookInfo.style.display = 'none';
                // Reset max jumlah
                const jumlahInput = document.getElementById('jumlah');
                const jumlahInfo = document.getElementById('jumlah-info');
            const stokTersedia = document.getElementById('stok-tersedia');
                if (jumlahInput) {
                    jumlahInput.max = 1;
                    jumlahInput.value = 1;
                    jumlahInfo.textContent = 'Maksimal sesuai stok tersedia.';
                if (stokTersedia) {
                    stokTersedia.textContent = 'Stok tersedia: -';
                    stokTersedia.className = 'text-xs font-semibold text-blue-600';
                }
                }
        }
    });
    
    // Initialize book info if there's a pre-selected book
    if (bookSelect.value) {
        bookSelect.dispatchEvent(new Event('change'));
    }

    // Toggle between student and teacher sections
    borrowerStudent.addEventListener('change', function() {
        if (this.checked) {
            studentSection.style.display = 'block';
            teacherSection.style.display = 'none';
            memberSelect.required = true;
            teacherSelect.required = false;
            teacherSelect.value = '';
            
            // Update visual state
            this.closest('label').classList.add('border-green-400', 'bg-green-50');
            this.closest('label').classList.remove('border-green-200');
            borrowerTeacher.closest('label').classList.remove('border-blue-400', 'bg-blue-50');
            borrowerTeacher.closest('label').classList.add('border-blue-200');
        }
    });

    borrowerTeacher.addEventListener('change', function() {
        if (this.checked) {
            studentSection.style.display = 'none';
            teacherSection.style.display = 'block';
            memberSelect.required = false;
            teacherSelect.required = true;
            memberSelect.value = '';
            
            // Update visual state
            this.closest('label').classList.add('border-blue-400', 'bg-blue-50');
            this.closest('label').classList.remove('border-blue-200');
            borrowerStudent.closest('label').classList.remove('border-green-400', 'bg-green-50');
            borrowerStudent.closest('label').classList.add('border-green-200');
        }
    });

    // Student QR scan
    scanMemberBtn.addEventListener('click', function() {
        currentScanType = 'student';
        qrModalTitle.textContent = 'Scan QR Code Siswa';
        manualInputLabel.textContent = 'Atau masukkan NISN manual:';
        manualId.placeholder = 'Masukkan NISN dari kartu akses';
        qrModal.classList.remove('hidden');
        startCamera();
    });

    // Teacher QR scan
    scanTeacherBtn.addEventListener('click', function() {
        currentScanType = 'teacher';
        qrModalTitle.textContent = 'Scan QR Code Guru';
        manualInputLabel.textContent = 'Atau masukkan NIP manual:';
        manualId.placeholder = 'Masukkan NIP dari kartu akses';
        qrModal.classList.remove('hidden');
        startCamera();
    });

    closeQrModal.addEventListener('click', function() {
        closeModal();
    });

    cancelQrScan.addEventListener('click', function() {
        closeModal();
    });

    function closeModal() {
        qrModal.classList.add('hidden');
        stopCamera();
    }

    function startCamera() {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(function(mediaStream) {
                stream = mediaStream;
                video = document.createElement('video');
                video.srcObject = stream;
                video.play();
                
                const qrReader = document.getElementById('qr-reader');
                qrReader.innerHTML = '';
                qrReader.appendChild(video);
                
                video.addEventListener('loadedmetadata', function() {
                    video.style.width = '100%';
                    video.style.height = 'auto';
                });
                
                scanning = true;
                scanQR();
            })
            .catch(function(err) {
                console.error('Error accessing camera:', err);
                alert('Tidak dapat mengakses kamera. Pastikan izin kamera sudah diberikan.');
            });
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        scanning = false;
    }

    function scanQR() {
        if (!scanning) return;
        
        if (video && video.readyState === video.HAVE_ENOUGH_DATA) {
            if (!canvas) {
                canvas = document.createElement('canvas');
                context = canvas.getContext('2d');
            }
            
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);
            
            if (code) {
                processQRData(code.data);
                return;
            }
        }
        
        requestAnimationFrame(scanQR);
    }

    function processQRData(qrData) {
        const id = qrData.trim();
        
        if (currentScanType === 'student') {
            // Check if NISN exists in database
            fetch('/members/check-nisn', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ nisn: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Find and select the member in dropdown
                    const options = memberSelect.querySelectorAll('option[data-member-id]');
                    for (let option of options) {
                        if (option.dataset.memberId === data.member.member_id) {
                            memberSelect.value = option.value;
                            closeModal();
                            alert('Data siswa ditemukan!\nNama: ' + data.member.name + '\nKelas: ' + data.member.kelas);
                            return;
                        }
                    }
                    alert('Data siswa tidak ditemukan di dropdown');
                } else {
                    alert('NISN tidak ditemukan di database: ' + id);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memproses QR Code');
            });
        } else {
            // Check if NIP exists in database
            fetch('/teachers/check-nip', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ nip: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Find and select the teacher in dropdown
                    const options = teacherSelect.querySelectorAll('option[data-teacher-id]');
                    for (let option of options) {
                        if (option.dataset.teacherId === data.teacher.teacher_id) {
                            teacherSelect.value = option.value;
                            closeModal();
                            alert('Data guru ditemukan!\nNama: ' + data.teacher.name + '\nMata Pelajaran: ' + (data.teacher.subject || 'Tidak ada'));
                            return;
                        }
                    }
                    alert('Data guru tidak ditemukan di dropdown');
                } else {
                    alert('NIP tidak ditemukan di database: ' + id);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memproses QR Code');
            });
        }
    }

    // Process manual ID input
    processQrScan.addEventListener('click', function() {
        const id = manualId.value.trim();
        
        if (!id) {
            alert('Masukkan ID dari kartu akses');
            return;
        }

        processQRData(id);
    });

    // Handle Enter key in manual input
    manualId.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            processQrScan.click();
        }
    });
});
</script>

@endsection