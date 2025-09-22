@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Import Data Buku dari Excel</h1>
            <a href="{{ route('books.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Buku
            </a>
        </div>

        <!-- Import Instructions -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-500 text-lg"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-semibold text-blue-800 mb-2">Panduan Import Data</h3>
                    <div class="text-sm text-blue-700">
                        <p class="mb-2">Sistem ini mendukung 3 format data CSV:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Data Koleksi Perpustakaan:</strong> No, Jenis Koleksi, Judul, Pengarang, Penerbit, Jumlah</li>
                            <li><strong>Buku Bacaan Madsaka:</strong> No, Judul, Pengarang, Penerbit, Jumlah</li>
                            <li><strong>Buku Paket:</strong> Nama Buku, Jumlah</li>
                        </ul>
                        <p class="mt-2 text-xs text-blue-600">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <strong>Catatan:</strong> Konversi file Excel ke CSV terlebih dahulu dengan "Save As" → "CSV (Comma delimited)"
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import Form -->
        <form action="{{ route('books.import-excel') }}" method="POST" enctype="multipart/form-data" id="importForm">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- File Upload -->
                <div>
                    <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih File CSV
                    </label>
                    <input type="file" id="excel_file" name="excel_file" accept=".csv,.txt" required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-500 mt-1">
                        Format yang didukung: .csv, .txt
                    </p>
                </div>

                <!-- Data Type Selection -->
                <div>
                    <label for="data_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Data
                    </label>
                    <select name="data_type" id="data_type" required
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Jenis Data</option>
                        <option value="koleksi">Data Koleksi Perpustakaan (No, Jenis Koleksi, Judul, Pengarang, Penerbit, Jumlah)</option>
                        <option value="bacaan">Buku Bacaan Madsaka (No, Judul, Pengarang, Penerbit, Jumlah)</option>
                        <option value="paket">Buku Paket (Nama Buku, Jumlah)</option>
                    </select>
                </div>
            </div>


            <!-- Import Options -->
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Opsi Import</h3>
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="skip_duplicates" value="1" checked
                            class="mr-3 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Lewati buku yang sudah ada (berdasarkan ISBN/Judul)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="update_existing" value="1"
                            class="mr-3 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Update data buku yang sudah ada</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="add_quantity" value="1"
                            class="mr-3 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Tambahkan ke jumlah eksemplar yang sudah ada</span>
                    </label>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="mt-6" id="preview-section" style="display: none;">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Preview Data</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div id="preview-content" class="text-sm text-gray-600">
                        <!-- Preview content will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('books.index') }}" class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-medium">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="button" id="previewBtn" class="px-6 py-2 text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition font-medium">
                    <i class="fas fa-eye mr-2"></i>Preview
                </button>
                <button type="submit" id="importBtn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-upload mr-2"></i>Import Data
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Progress Modal -->
<div id="progressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Importing Data</h3>
                </div>
                
                <div class="mb-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>Progress</span>
                        <span id="progress-text">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>

                <div id="import-status" class="text-sm text-gray-600">
                    Memproses file...
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('excel_file');
    const dataTypeSelect = document.getElementById('data_type');
    const previewBtn = document.getElementById('previewBtn');
    const importBtn = document.getElementById('importBtn');
    const importForm = document.getElementById('importForm');
    const progressModal = document.getElementById('progressModal');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const importStatus = document.getElementById('import-status');

    // Handle file selection
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            const fileName = this.files[0].name;
            console.log('File selected:', fileName);
        }
    });

    // Handle data type selection
    dataTypeSelect.addEventListener('change', function() {
        // CSV files don't have sheets, so no need for sheet selection
    });

    // Handle preview button
    previewBtn.addEventListener('click', function() {
        if (!fileInput.files.length || !dataTypeSelect.value) {
            alert('Pilih file dan jenis data terlebih dahulu');
            return;
        }

        // Show preview section
        document.getElementById('preview-section').style.display = 'block';
        document.getElementById('preview-content').innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-spinner fa-spin text-blue-500 text-2xl mb-2"></i>
                <p>Memproses preview data...</p>
            </div>
        `;

        // In a real implementation, you would send the file to the server for preview
        setTimeout(() => {
            document.getElementById('preview-content').innerHTML = `
                <div class="text-green-600">
                    <i class="fas fa-check-circle mr-2"></i>
                    File siap untuk diimport. Format data sesuai dengan jenis yang dipilih.
                </div>
            `;
        }, 2000);
    });

    // Handle form submission
    importForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!fileInput.files.length || !dataTypeSelect.value) {
            alert('Pilih file dan jenis data terlebih dahulu');
            return;
        }

        // Show progress modal
        progressModal.classList.remove('hidden');
        
        // Simulate progress
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 100) progress = 100;
            
            progressBar.style.width = progress + '%';
            progressText.textContent = Math.round(progress) + '%';
            
            if (progress >= 100) {
                clearInterval(interval);
                importStatus.textContent = 'Import selesai!';
                setTimeout(() => {
                    progressModal.classList.add('hidden');
                    // Redirect to books index
                    window.location.href = '{{ route("books.index") }}';
                }, 1000);
            }
        }, 200);

        // In a real implementation, you would submit the form here
        // For now, we'll just simulate the process
    });
});
</script>
@endsection
