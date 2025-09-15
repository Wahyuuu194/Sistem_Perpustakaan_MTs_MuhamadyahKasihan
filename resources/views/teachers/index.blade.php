@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-8">
    <div class="flex justify-between items-center">
        <div class="flex gap-3">
            <button type="button" id="importBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-file-import mr-2"></i>Import CSV
            </button>
            <a href="{{ route('teachers.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Tambah Guru
            </a>
        </div>
    </div>

    <!-- Search Form -->
    <div class="bg-white rounded-lg shadow p-4">
        <form action="{{ route('teachers.index') }}" method="GET" class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Cari berdasarkan nama atau NIP..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-search mr-1"></i>Cari
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Teachers Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Daftar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($teachers as $teacher)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $teacher->teacher_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $teacher->name }}</div>
                                @if($teacher->phone)
                                    <div class="text-sm text-gray-500">{{ $teacher->phone }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $teacher->subject ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $teacher->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $teacher->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $teacher->registration_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('teachers.show', $teacher) }}" 
                                        class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('teachers.edit', $teacher) }}" 
                                        class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" 
                                        class="inline" onsubmit="return confirm('Yakin ingin menghapus data guru ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada data guru
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($teachers->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $teachers->links() }}
        </div>
    @endif
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Import Data Guru dari CSV</h3>
                    <button type="button" id="closeImportModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="importForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih File CSV
                        </label>
                        <input type="file" id="csv_file" name="csv_file" accept=".csv" required
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-500 mt-1">
                            Format: NIP, Nama, Mata Pelajaran, Telepon (dari Google Sheets)
                        </p>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancelImport" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                            Batal
                        </button>
                        <button type="submit" id="submitImport" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                            <i class="fas fa-upload mr-1"></i>Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const importBtn = document.getElementById('importBtn');
    const importModal = document.getElementById('importModal');
    const closeImportModal = document.getElementById('closeImportModal');
    const cancelImport = document.getElementById('cancelImport');
    const importForm = document.getElementById('importForm');
    const submitImport = document.getElementById('submitImport');

    importBtn.addEventListener('click', function() {
        importModal.classList.remove('hidden');
    });

    closeImportModal.addEventListener('click', function() {
        importModal.classList.add('hidden');
    });

    cancelImport.addEventListener('click', function() {
        importModal.classList.add('hidden');
    });

    importForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        submitImport.disabled = true;
        submitImport.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Importing...';

        fetch('{{ route("teachers.import-csv") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Import berhasil!\n${data.message}`);
                location.reload();
            } else {
                alert('Import gagal: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat import data');
        })
        .finally(() => {
            submitImport.disabled = false;
            submitImport.innerHTML = '<i class="fas fa-upload mr-1"></i>Import';
        });
    });
});
</script>
@endsection
