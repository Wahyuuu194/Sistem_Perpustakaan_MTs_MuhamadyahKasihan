@extends('layouts.import')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Import Data Murid</h1>
                <p class="text-gray-600 mt-1">Import data murid dari file CSV</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('members.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-center">
                    <i class="fas fa-list mr-2"></i>Daftar Murid
                </a>
            </div>
        </div>

        <!-- Import Instructions -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-500 text-lg"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-semibold text-blue-800 mb-2">Panduan Import Data Murid</h3>
                    <div class="text-sm text-blue-700">
                        <p class="mb-2">Format file CSV yang didukung:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Kolom 1:</strong> NISN (Nomor Induk Siswa Nasional)</li>
                            <li><strong>Kolom 2:</strong> Nama Lengkap</li>
                            <li><strong>Kolom 3:</strong> Kelas (contoh: 7A, 8B, 9C)</li>
                            <li><strong>Kolom 4:</strong> Telepon (opsional)</li>
                            <li><strong>Kolom 5:</strong> Alamat (opsional)</li>
                        </ul>
                        <p class="mt-2 text-xs text-blue-600">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <strong>Catatan:</strong> Pastikan file CSV menggunakan koma (,) sebagai pemisah dan tidak ada header baris pertama
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import Form -->
        <form id="importForm" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- File Upload -->
            <div>
                <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih File CSV
                </label>
                <input type="file" id="csv_file" name="csv_file" accept=".csv" required
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-500 mt-1">
                    Format yang didukung: .csv
                </p>
            </div>

            <!-- Import Options -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Opsi Import</h3>
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="skip_duplicates" value="1" checked
                            class="mr-3 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Lewati murid yang sudah ada (berdasarkan NISN)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="update_existing" value="1"
                            class="mr-3 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Update data murid yang sudah ada</span>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('members.index') }}" class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-medium">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit" id="submitImport" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
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
                        <div id="progress-bar" class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
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
    const importForm = document.getElementById('importForm');
    const progressModal = document.getElementById('progressModal');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const importStatus = document.getElementById('import-status');
    const submitImport = document.getElementById('submitImport');

    importForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('csv_file');
        if (!fileInput.files.length) {
            alert('Pilih file CSV terlebih dahulu');
            return;
        }

        // Show progress modal
        progressModal.classList.remove('hidden');
        
        const formData = new FormData(this);
        submitImport.disabled = true;
        submitImport.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Importing...';

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
                    // Redirect to members index
                    window.location.href = '{{ route("members.index") }}';
                }, 1000);
            }
        }, 200);

        // Submit form
        fetch('{{ route("members.import-csv") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            clearInterval(interval);
            progressBar.style.width = '100%';
            progressText.textContent = '100%';
            
            if (data.success) {
                importStatus.textContent = `Import berhasil! ${data.message}`;
                setTimeout(() => {
                    progressModal.classList.add('hidden');
                    window.location.href = '{{ route("members.index") }}';
                }, 2000);
            } else {
                importStatus.textContent = 'Import gagal: ' + data.message;
                setTimeout(() => {
                    progressModal.classList.add('hidden');
                }, 3000);
            }
        })
        .catch(error => {
            clearInterval(interval);
            console.error('Error:', error);
            importStatus.textContent = 'Terjadi kesalahan saat import data';
            setTimeout(() => {
                progressModal.classList.add('hidden');
            }, 3000);
        })
        .finally(() => {
            submitImport.disabled = false;
            submitImport.innerHTML = '<i class="fas fa-upload mr-2"></i>Import Data';
        });
    });
});
</script>
@endsection
