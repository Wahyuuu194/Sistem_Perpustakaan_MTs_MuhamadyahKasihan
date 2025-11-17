<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Data Buku - Sistem Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto py-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Import Data Buku dari CSV</h1>
                <a href="/books" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
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
                        <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-lightbulb mr-1"></i>
                                <strong>Tips:</strong> 
                            </p>
                            <ul class="text-sm text-yellow-800 mt-1 ml-4 list-disc">
                                <li><strong>Buku Bacaan Madsaka:</strong> Sistem otomatis menentukan kategori Fiksi/Non-Fiksi berdasarkan judul</li>
                                <li><strong>Buku Paket:</strong> Sistem otomatis menentukan kategori mata pelajaran (IPA, IPS, Bahasa Indonesia, dll.)</li>
                                <li>Import terpisah untuk hasil yang lebih akurat</li>
                            </ul>
                        </div>
                            <p class="mt-2 text-xs text-blue-600">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                <strong>Catatan:</strong> Konversi file Excel ke CSV terlebih dahulu dengan "Save As" → "CSV (Comma delimited)"
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import Form -->
            <form action="/import-books" method="POST" enctype="multipart/form-data" id="importForm">
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
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Lewati buku yang sudah ada</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="update_existing" value="1"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Update data buku yang sudah ada</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="add_quantity" value="1"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Tambahkan ke jumlah eksemplar yang sudah ada</span>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex flex-col sm:flex-row gap-4">
                    <button type="button" id="previewBtn"
                        class="flex-1 bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition text-center">
                        <i class="fas fa-eye mr-2"></i>Preview Data
                    </button>
                    <button type="submit" id="importBtn"
                        class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition text-center">
                        <i class="fas fa-upload mr-2"></i>Import Data
                    </button>
                </div>
            </form>

            <!-- Preview Section -->
            <div id="preview-section" class="mt-8 hidden">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Preview Data</h3>
                <div id="preview-content" class="bg-gray-50 rounded-lg p-4">
                    <!-- Preview content will be loaded here -->
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
        const previewSection = document.getElementById('preview-section');
        const previewContent = document.getElementById('preview-content');

        // Handle file selection
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const fileName = this.files[0].name;
                console.log('File selected:', fileName);
            }
        });

        // Handle preview button
        previewBtn.addEventListener('click', function() {
            if (!fileInput.files.length || !dataTypeSelect.value) {
                alert('Pilih file dan jenis data terlebih dahulu');
                return;
            }

            const formData = new FormData();
            formData.append('excel_file', fileInput.files[0]);
            formData.append('data_type', dataTypeSelect.value);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch('/preview-books', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    previewContent.innerHTML = '<p>Preview data berhasil dimuat. Total baris: ' + data.total_rows + '</p>';
                    previewSection.classList.remove('hidden');
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat preview data');
            });
        });

        // Handle form submission
        importForm.addEventListener('submit', function(e) {
            if (!fileInput.files.length || !dataTypeSelect.value) {
                e.preventDefault();
                alert('Pilih file dan jenis data terlebih dahulu');
                return;
            }

            importBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            importBtn.disabled = true;
        });
    });
    </script>
</body>
</html>
