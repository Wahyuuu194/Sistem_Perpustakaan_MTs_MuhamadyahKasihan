@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-4 sm:py-6 px-4 sm:px-0">
    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 mb-2">Sinkronkan Data</h1>
            <p class="text-sm sm:text-base text-gray-600">Sinkronkan data buku, murid, dan guru langsung dari Google Sheets</p>
        </div>

        <!-- Sync Buku Section -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row items-start gap-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-book text-green-600 text-xl sm:text-2xl"></i>
                </div>
                <div class="flex-1 w-full">
                    <h3 class="text-base sm:text-lg font-semibold text-green-800 mb-2">Sinkronkan Data Buku</h3>
                    <p class="text-xs sm:text-sm text-green-700 mb-4">
                        Sync data buku langsung dari Google Sheets tanpa perlu download file CSV.
                    </p>
                    <button type="button" id="syncBooksBtn" class="w-full sm:w-auto bg-green-600 text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-green-700 transition font-medium text-sm sm:text-base">
                        <i class="fas fa-sync-alt mr-2"></i>Sinkronkan Buku
                    </button>
                    <div class="mt-3 text-xs text-green-600">
                        <p><strong>Format:</strong> Judul | Pengarang | Penerbit | Jumlah Eksemplar | Kategori</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sync Murid Section -->
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row items-start gap-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-users text-orange-600 text-xl sm:text-2xl"></i>
                </div>
                <div class="flex-1 w-full">
                    <h3 class="text-base sm:text-lg font-semibold text-orange-800 mb-2">Sinkronkan Data Murid</h3>
                    <p class="text-xs sm:text-sm text-orange-700 mb-4">
                        Sinkronkan data Murid terbaru dari Google Sheets.
                    </p>
                    <button type="button" id="syncMembersBtn" class="w-full sm:w-auto bg-orange-600 text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-orange-700 transition font-medium text-sm sm:text-base">
                        <i class="fas fa-sync-alt mr-2"></i>Sinkronkan Murid
                    </button>
                    <div class="mt-3 text-xs text-orange-600">
                        <p><strong>Format:</strong> NISN | Nama Lengkap | Kelas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sync Guru Section -->
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row items-start gap-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-chalkboard-teacher text-purple-600 text-xl sm:text-2xl"></i>
                </div>
                <div class="flex-1 w-full">
                    <h3 class="text-base sm:text-lg font-semibold text-purple-800 mb-2">Sinkronkan Data Guru</h3>
                    <p class="text-xs sm:text-sm text-purple-700 mb-4">
                        Sinkronkan data Guru terbaru dari Google Sheets.
                    </p>
                    <button type="button" id="syncTeachersBtn" class="w-full sm:w-auto bg-purple-600 text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-purple-700 transition font-medium text-sm sm:text-base">
                        <i class="fas fa-sync-alt mr-2"></i>Sinkronkan Guru
                    </button>
                    <div class="mt-3 text-xs text-purple-600">
                        <p><strong>Format:</strong> NIP | Nama Lengkap</p>
                    </div>
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
                    <h3 class="text-sm font-semibold text-blue-800 mb-2">Informasi Sinkronisasi</h3>
                    <div class="text-sm text-blue-700">
                        <p class="mb-2">Data akan di-sync langsung dari Google Sheets yang sudah dipublikasikan.</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Data yang sudah ada akan diupdate otomatis</li>
                            <li>Data baru akan ditambahkan otomatis</li>
                            <li>Pastikan Google Sheets sudah dipublikasikan dengan format CSV</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <a href="{{ route('dashboard') }}" class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Progress Modal -->
<div id="progressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Menyinkronkan Data</h3>
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
                    Memproses sinkronisasi...
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const progressModal = document.getElementById('progressModal');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const importStatus = document.getElementById('import-status');

    function showProgress() {
        progressModal.classList.remove('hidden');
        progressBar.style.width = '0%';
        progressText.textContent = '0%';
        importStatus.textContent = 'Memulai sinkronisasi dari Google Sheets...';
    }

    function hideProgress() {
        setTimeout(() => {
            progressModal.classList.add('hidden');
        }, 2000);
    }

    function updateProgress(percent, status) {
        progressBar.style.width = percent + '%';
        progressText.textContent = Math.round(percent) + '%';
        if (status) {
            importStatus.textContent = status;
        }
    }

    function syncData(type, route, buttonId) {
        if (!confirm(`Apakah Anda yakin ingin sinkronkan data ${type} dari Google Sheets? Data yang sudah ada akan diupdate.`)) {
            return;
        }

        showProgress();
        const button = document.getElementById(buttonId);
        const originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyinkronkan...';

        // Simulate progress
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 20;
            if (progress > 90) progress = 90;
            updateProgress(progress, 'Mengambil data dari Google Sheets...');
        }, 300);

        // Make sync request
        fetch(route, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            const contentType = response.headers.get("content-type");
            
            // Check if response is JSON
            if (contentType && contentType.includes("application/json")) {
                return response.json();
            } else {
                // If not JSON, get text to see what we got
                const text = await response.text();
                throw new Error('Server mengembalikan response yang tidak valid. Status: ' + response.status + '. Response: ' + text.substring(0, 200));
            }
        })
        .then(data => {
            clearInterval(interval);
            updateProgress(100, data.message || 'Sinkronisasi selesai');
            
            if (data.success) {
                setTimeout(() => {
                    hideProgress();
                    if (type === 'buku') {
                        window.location.href = '{{ route("books.index") }}';
                    } else if (type === 'murid') {
                        window.location.href = '{{ route("members.index") }}';
                    } else if (type === 'guru') {
                        window.location.href = '{{ route("teachers.index") }}';
                    }
                }, 2000);
            } else {
                updateProgress(100, 'Sync gagal: ' + (data.message || 'Terjadi kesalahan'));
                hideProgress();
            }
        })
        .catch(error => {
            clearInterval(interval);
            console.error('Error:', error);
            updateProgress(100, 'Terjadi kesalahan saat sinkronisasi data');
            hideProgress();
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = originalHtml;
        });
    }

    // Sync Books
    document.getElementById('syncBooksBtn').addEventListener('click', function() {
        syncData('buku', '{{ route("books.sync-google-sheets") }}', 'syncBooksBtn');
    });

    // Sync Members
    document.getElementById('syncMembersBtn').addEventListener('click', function() {
        syncData('murid', '{{ route("members.sync-google-sheets") }}', 'syncMembersBtn');
    });

    // Sync Teachers
    document.getElementById('syncTeachersBtn').addEventListener('click', function() {
        syncData('guru', '{{ route("teachers.sync-google-sheets") }}', 'syncTeachersBtn');
    });
});
</script>
@endsection

