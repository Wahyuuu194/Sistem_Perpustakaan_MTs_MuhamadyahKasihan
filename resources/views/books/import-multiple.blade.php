@extends('layouts.import')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Import Data Buku</h1>
                <p class="text-gray-600 mt-1">Import data buku dari multiple file Excel</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('books.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-center">
                    <i class="fas fa-list mr-2"></i>Daftar Buku
                </a>
            </div>
        </div>

        <!-- Sync from Google Sheets -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-sync-alt text-green-500 text-lg"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-semibold text-green-800 mb-2">1-Klik Sync dari Google Sheets</h3>
                    <div class="text-sm text-green-700 mb-3">
                        <p>Sync data buku langsung dari Google Sheets tanpa perlu download file CSV.</p>
                    </div>
                    <button type="button" id="syncFromSheets" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-medium">
                        <i class="fas fa-sync-alt mr-2"></i>Sync dari Google Sheets
                    </button>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-500 text-lg"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-semibold text-blue-800 mb-2">Informasi Sync Data Buku</h3>
                    <div class="text-sm text-blue-700">
                        <p class="mb-2">Data buku akan di-sync langsung dari Google Sheets dengan format:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Kolom 1:</strong> Judul Buku</li>
                            <li><strong>Kolom 2:</strong> Pengarang/Penulis</li>
                            <li><strong>Kolom 3:</strong> Penerbit</li>
                            <li><strong>Kolom 4:</strong> Jumlah Eksemplar</li>
                            <li><strong>Kolom 5:</strong> Kategori (opsional)</li>
                        </ul>
                        <p class="mt-2 text-xs text-blue-600">
                            <i class="fas fa-sync-alt mr-1"></i>
                            <strong>Catatan:</strong> Data yang sudah ada akan diupdate, data baru akan ditambahkan otomatis
                        </p>
                    </div>
                </div>
            </div>
        </div>


        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <a href="{{ route('books.index') }}" class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Buku
            </a>
        </div>
    </div>
</div>

<!-- Progress Modal -->
<div id="progressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Importing Multiple Files</h3>
                </div>
                
                <div class="mb-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>Progress</span>
                        <span id="progress-text">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div id="progress-bar" class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>

                <div id="import-status" class="text-sm text-gray-600 mb-4">
                    Memproses file...
                </div>

                <!-- File Results -->
                <div id="file-results" class="hidden">
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Hasil Import:</h4>
                    <div id="file-results-content" class="text-sm text-gray-600 max-h-40 overflow-y-auto">
                        <!-- Results will be populated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const syncButton = document.getElementById('syncFromSheets');
    const progressModal = document.getElementById('progressModal');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const importStatus = document.getElementById('import-status');
    const fileResults = document.getElementById('file-results');
    const fileResultsContent = document.getElementById('file-results-content');

    syncButton.addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin sync data buku dari Google Sheets?')) {
            syncFromGoogleSheets();
        }
    });

    function syncFromGoogleSheets() {
        // Show progress modal
        progressModal.classList.remove('hidden');
        importStatus.textContent = 'Mengambil data dari Google Sheets...';
        fileResults.classList.add('hidden');
        
        syncButton.disabled = true;
        syncButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Syncing...';

        // Simulate progress
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90; // Don't complete until response
            
            progressBar.style.width = progress + '%';
            progressText.textContent = Math.round(progress) + '%';
        }, 200);

        // Make sync request
        fetch('{{ route("books.sync-google-sheets") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            clearInterval(interval);
            progressBar.style.width = '100%';
            progressText.textContent = '100%';
            
            if (data.success) {
                importStatus.textContent = data.message;
                
                setTimeout(() => {
                    progressModal.classList.add('hidden');
                    window.location.href = '{{ route("books.index") }}';
                }, 3000);
            } else {
                importStatus.textContent = 'Sync gagal: ' + data.message;
                setTimeout(() => {
                    progressModal.classList.add('hidden');
                }, 3000);
            }
        })
        .catch(error => {
            clearInterval(interval);
            console.error('Error:', error);
            importStatus.textContent = 'Terjadi kesalahan saat sync data';
            setTimeout(() => {
                progressModal.classList.add('hidden');
            }, 3000);
        })
        .finally(() => {
            syncButton.disabled = false;
            syncButton.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Sync dari Google Sheets';
        });
    }

});
</script>
@endsection
