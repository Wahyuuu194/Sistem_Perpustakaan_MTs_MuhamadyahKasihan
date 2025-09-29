@extends('layouts.app')

@section('title', 'Manajemen ISBN Buku')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-barcode mr-2 text-blue-600"></i> Manajemen ISBN Buku
            </h3>
        </div>
        <div class="p-6">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button class="tab-button active py-2 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600" data-tab="search">
                        <i class="fas fa-search mr-2"></i> Cari ISBN
                    </button>
                    <button class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="bulk">
                        <i class="fas fa-list mr-2"></i> Bulk Update
                    </button>
                    <button class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="without-isbn">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Buku Tanpa ISBN
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div id="tab-content">
                <!-- Search Tab -->
                <div id="search-tab" class="tab-content-pane active">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <h5 class="text-lg font-medium text-gray-900 mb-4">Cari ISBN untuk Satu Buku</h5>
                            <form id="searchIsbnForm" class="space-y-4">
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700">Judul Buku *</label>
                                    <input type="text" id="title" name="title" required 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="author" class="block text-sm font-medium text-gray-700">Penulis</label>
                                    <input type="text" id="author" name="author" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <i class="fas fa-search mr-2"></i> Cari ISBN
                                </button>
                            </form>
                        </div>
                        <div>
                            <h5 class="text-lg font-medium text-gray-900 mb-4">Hasil Pencarian</h5>
                            <div id="searchResult" class="border border-gray-200 rounded-md p-4 min-h-48">
                                <p class="text-gray-500">Masukkan judul buku untuk mencari ISBN...</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h5 class="text-lg font-medium text-gray-900 mb-4">Cari ISBN untuk Multiple Books</h5>
                        <div class="mb-4">
                            <label for="multipleBooks" class="block text-sm font-medium text-gray-700 mb-2">Daftar Buku (JSON Format)</label>
                            <textarea id="multipleBooks" rows="8" 
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder='[
  {
    "title": "Judul Buku 1",
    "author": "Penulis 1"
  },
  {
    "title": "Judul Buku 2", 
    "author": "Penulis 2"
  }
]'></textarea>
                        </div>
                        <button type="button" id="searchMultipleBtn" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <i class="fas fa-list mr-2"></i> Cari ISBN Multiple
                        </button>
                    </div>

                    <div class="mt-6">
                        <div id="multipleResult" class="border border-gray-200 rounded-md p-4 min-h-48">
                            <p class="text-gray-500">Hasil pencarian multiple books akan muncul di sini...</p>
                        </div>
                    </div>
                </div>

                <!-- Bulk Update Tab -->
                <div id="bulk-tab" class="tab-content-pane hidden">
                    <div>
                        <h5 class="text-lg font-medium text-gray-900 mb-4">Bulk Update ISBN untuk Buku yang Sudah Ada</h5>
                        <p class="text-gray-600 mb-4">Pilih buku-buku yang ingin diupdate ISBN-nya secara otomatis.</p>
                        
                        <div class="mb-4 space-x-2">
                            <button type="button" id="loadBooksBtn" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-sync mr-2"></i> Load Buku Tanpa ISBN
                            </button>
                            <button type="button" id="selectAllBtn" class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 hidden">
                                <i class="fas fa-check-square mr-2"></i> Pilih Semua
                            </button>
                            <button type="button" id="bulkUpdateBtn" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 hidden">
                                <i class="fas fa-magic mr-2"></i> Update ISBN Otomatis
                            </button>
                        </div>

                        <div id="booksList" class="border border-gray-200 rounded-md p-4">
                            <p class="text-gray-500">Klik "Load Buku Tanpa ISBN" untuk memuat daftar buku...</p>
                        </div>
                    </div>
                </div>

                <!-- Books Without ISBN Tab -->
                <div id="without-isbn-tab" class="tab-content-pane hidden">
                    <div>
                        <h5 class="text-lg font-medium text-gray-900 mb-4">Daftar Buku Tanpa ISBN</h5>
                        <button type="button" id="refreshWithoutIsbnBtn" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4">
                            <i class="fas fa-refresh mr-2"></i> Refresh
                        </button>
                        
                        <div id="withoutIsbnList" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penulis</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerbit</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="withoutIsbnTableBody" class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            Loading...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2">Memproses...</h3>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Tab functionality
    $('.tab-button').on('click', function() {
        const tabId = $(this).data('tab');
        
        // Update tab buttons
        $('.tab-button').removeClass('active border-blue-500 text-blue-600').addClass('border-transparent text-gray-500');
        $(this).addClass('active border-blue-500 text-blue-600').removeClass('border-transparent text-gray-500');
        
        // Update tab content
        $('.tab-content-pane').addClass('hidden').removeClass('active');
        $('#' + tabId + '-tab').removeClass('hidden').addClass('active');
        
        // Load data for specific tabs
        if (tabId === 'without-isbn') {
            loadBooksWithoutIsbn();
        }
    });
    
    // Search ISBN for single book
    $('#searchIsbnForm').on('submit', function(e) {
        e.preventDefault();
        
        const title = $('#title').val();
        const author = $('#author').val();
        
        if (!title.trim()) {
            alert('Judul buku harus diisi!');
            return;
        }
        
        showLoading();
        
        $.ajax({
            url: '/api/isbn/search',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                title: title,
                author: author
            }),
            success: function(response) {
                hideLoading();
                displaySearchResult(response);
            },
            error: function(xhr) {
                hideLoading();
                const error = xhr.responseJSON || { message: 'Terjadi kesalahan' };
                displaySearchResult({
                    success: false,
                    message: error.message || 'Terjadi kesalahan'
                });
            }
        });
    });
    
    // Search ISBN for multiple books
    $('#searchMultipleBtn').on('click', function() {
        const booksText = $('#multipleBooks').val();
        
        if (!booksText.trim()) {
            alert('Masukkan daftar buku dalam format JSON!');
            return;
        }
        
        try {
            const books = JSON.parse(booksText);
            
            if (!Array.isArray(books) || books.length === 0) {
                alert('Format JSON tidak valid atau array kosong!');
                return;
            }
            
            if (books.length > 50) {
                alert('Maksimal 50 buku per request!');
                return;
            }
            
            showLoading();
            
            $.ajax({
                url: '/api/isbn/search-multiple',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ books: books }),
                success: function(response) {
                    hideLoading();
                    displayMultipleResult(response);
                },
                error: function(xhr) {
                    hideLoading();
                    const error = xhr.responseJSON || { message: 'Terjadi kesalahan' };
                    displayMultipleResult({
                        success: false,
                        message: error.message || 'Terjadi kesalahan'
                    });
                }
            });
            
        } catch (e) {
            alert('Format JSON tidak valid: ' + e.message);
        }
    });
    
    // Load books without ISBN
    $('#loadBooksBtn').on('click', function() {
        loadBooksWithoutIsbn();
    });
    
    // Select all books
    $('#selectAllBtn').on('click', function() {
        $('.book-checkbox').prop('checked', true);
        updateBulkUpdateButton();
    });
    
    // Bulk update ISBN
    $('#bulkUpdateBtn').on('click', function() {
        const selectedBooks = $('.book-checkbox:checked').map(function() {
            return parseInt($(this).val());
        }).get();
        
        if (selectedBooks.length === 0) {
            alert('Pilih minimal satu buku!');
            return;
        }
        
        if (!confirm(`Update ISBN untuk ${selectedBooks.length} buku?`)) {
            return;
        }
        
        showLoading();
        
        $.ajax({
            url: '/api/isbn/bulk-update',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ book_ids: selectedBooks }),
            success: function(response) {
                hideLoading();
                alert(`Bulk update selesai!\nBerhasil: ${response.success_count}/${response.total_processed}`);
                loadBooksWithoutIsbn(); // Refresh list
            },
            error: function(xhr) {
                hideLoading();
                const error = xhr.responseJSON || { message: 'Terjadi kesalahan' };
                alert('Error: ' + (error.message || 'Terjadi kesalahan'));
            }
        });
    });
    
    // Refresh without ISBN list
    $('#refreshWithoutIsbnBtn').on('click', function() {
        loadBooksWithoutIsbn();
    });
    
    // Update bulk update button state
    $(document).on('change', '.book-checkbox', function() {
        updateBulkUpdateButton();
    });
    
    function showLoading() {
        $('#loadingModal').removeClass('hidden');
    }
    
    function hideLoading() {
        $('#loadingModal').addClass('hidden');
    }
    
    function displaySearchResult(result) {
        let html = '';
        
        if (result.success) {
            html = `
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <h6 class="font-medium"><i class="fas fa-check mr-2"></i> ${result.message}</h6>
                    <p class="mt-1"><strong>ISBN:</strong> <code class="bg-green-200 px-2 py-1 rounded">${result.isbn}</code></p>
                </div>
            `;
            
            if (result.book_info) {
                html += `
                    <div class="bg-white border border-gray-200 rounded-md">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <h6 class="font-medium text-gray-900">Informasi Buku</h6>
                        </div>
                        <div class="px-4 py-3">
                            <p class="text-sm"><strong>Judul:</strong> ${result.book_info.title}</p>
                            <p class="text-sm"><strong>Penulis:</strong> ${result.book_info.authors ? result.book_info.authors.join(', ') : '-'}</p>
                            <p class="text-sm"><strong>Penerbit:</strong> ${result.book_info.publisher || '-'}</p>
                            <p class="text-sm"><strong>Tahun:</strong> ${result.book_info.published_date || '-'}</p>
                            <p class="text-sm"><strong>Halaman:</strong> ${result.book_info.page_count || '-'}</p>
                            ${result.book_info.thumbnail ? `<img src="${result.book_info.thumbnail}" class="mt-2 rounded border" style="max-width: 100px;">` : ''}
                        </div>
                    </div>
                `;
            }
        } else {
            html = `
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <h6 class="font-medium"><i class="fas fa-times mr-2"></i> ${result.message}</h6>
                </div>
            `;
        }
        
        $('#searchResult').html(html);
    }
    
    function displayMultipleResult(result) {
        let html = '';
        
        if (result.success) {
            html = `
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                    <h6 class="font-medium"><i class="fas fa-info mr-2"></i> ${result.message}</h6>
                    <p class="mt-1">Total diproses: ${result.total_processed}, Berhasil: ${result.success_count}</p>
                </div>
            `;
            
            if (result.results) {
                html += '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200">';
                html += '<thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penulis</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ISBN</th></tr></thead><tbody>';
                
                result.results.forEach((item, index) => {
                    const statusClass = item.success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                    const statusIcon = item.success ? 'check' : 'times';
                    const isbn = item.isbn || '-';
                    
                    html += `
                        <tr class="bg-white">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${index + 1}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.input.title}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.input.author || '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap"><span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusClass}"><i class="fas fa-${statusIcon} mr-1"></i> ${item.success ? 'Berhasil' : 'Gagal'}</span></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><code class="bg-gray-100 px-2 py-1 rounded">${isbn}</code></td>
                        </tr>
                    `;
                });
                
                html += '</tbody></table></div>';
            }
        } else {
            html = `
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <h6 class="font-medium"><i class="fas fa-times mr-2"></i> ${result.message}</h6>
                </div>
            `;
        }
        
        $('#multipleResult').html(html);
    }
    
    function loadBooksWithoutIsbn() {
        $.ajax({
            url: '/api/isbn/books-without-isbn',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    displayBooksWithoutIsbn(response.books);
                } else {
                    $('#booksList').html('<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Gagal memuat data buku</div>');
                }
            },
            error: function() {
                $('#booksList').html('<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Terjadi kesalahan saat memuat data</div>');
            }
        });
    }
    
    function displayBooksWithoutIsbn(books) {
        if (books.length === 0) {
            $('#booksList').html('<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">Semua buku sudah memiliki ISBN!</div>');
            $('#selectAllBtn, #bulkUpdateBtn').addClass('hidden');
            return;
        }
        
        let html = `
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-info mr-2"></i> Ditemukan ${books.length} buku tanpa ISBN
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"><input type="checkbox" id="selectAllCheckbox"></th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penulis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penerbit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tahun</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
        `;
        
        books.forEach(book => {
            html += `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap"><input type="checkbox" class="book-checkbox" value="${book.id}"></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${book.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${book.title}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${book.author}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${book.publisher || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${book.publication_year || '-'}</td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        
        $('#booksList').html(html);
        $('#selectAllBtn, #bulkUpdateBtn').removeClass('hidden');
        
        // Select all checkbox functionality
        $('#selectAllCheckbox').on('change', function() {
            $('.book-checkbox').prop('checked', $(this).is(':checked'));
            updateBulkUpdateButton();
        });
    }
    
    function updateBulkUpdateButton() {
        const selectedCount = $('.book-checkbox:checked').length;
        if (selectedCount > 0) {
            $('#bulkUpdateBtn').text(`Update ISBN (${selectedCount} buku)`).removeClass('hidden');
        } else {
            $('#bulkUpdateBtn').addClass('hidden');
        }
    }
});
</script>
@endsection