@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-8">
    <div class="flex justify-between items-center">
        <!-- <h1 class="text-3xl font-bold text-gray-900">Daftar Anggota</h1> -->
        <div class="flex gap-3">
            <button type="button" id="importBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-file-import mr-2"></i>Import CSV
            </button>
            <a href="{{ route('members.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Tambah Anggota
            </a>
        </div>
    </div>

    <!-- Search Form -->
    <div class="bg-white rounded-lg shadow p-4">
        <form action="{{ route('members.index') }}" method="GET" class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Cari anggota berdasarkan nama, NIS/NISN, kelas, telepon, atau alamat..."
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div class="sm:w-32">
                    <select name="kelas" class="block w-full px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ request('kelas') == 'all' || !request('kelas') ? 'selected' : '' }}>Semua Kelas</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas }}" {{ request('kelas') == $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="sm:w-32">
                    <select name="status" class="block w-full px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-search mr-2"></i>Cari
                    </button>
                    @if(request('search') || (request('kelas') && request('kelas') !== 'all') || (request('status') && request('status') !== 'all'))
                        <a href="{{ route('members.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition">
                            <i class="fas fa-times mr-2"></i>Reset
                        </a>
                    @endif
                </div>
            </div>
            
            @if(request('search') || (request('kelas') && request('kelas') !== 'all') || (request('status') && request('status') !== 'all'))
                <div class="text-sm text-gray-600 border-t pt-3">
                    <i class="fas fa-info-circle mr-1"></i>
                    @if(request('search') && request('kelas') && request('kelas') !== 'all' && request('status') && request('status') !== 'all')
                        Menampilkan hasil pencarian untuk: <strong>"{{ request('search') }}"</strong> dalam kelas <strong>"{{ request('kelas') }}"</strong> dengan status <strong>"{{ request('status') === 'active' ? 'Aktif' : 'Nonaktif' }}"</strong>
                    @elseif(request('search') && request('kelas') && request('kelas') !== 'all')
                        Menampilkan hasil pencarian untuk: <strong>"{{ request('search') }}"</strong> dalam kelas <strong>"{{ request('kelas') }}"</strong>
                    @elseif(request('search') && request('status') && request('status') !== 'all')
                        Menampilkan hasil pencarian untuk: <strong>"{{ request('search') }}"</strong> dengan status <strong>"{{ request('status') === 'active' ? 'Aktif' : 'Nonaktif' }}"</strong>
                    @elseif(request('search'))
                        Menampilkan hasil pencarian untuk: <strong>"{{ request('search') }}"</strong>
                    @elseif(request('kelas') && request('kelas') !== 'all')
                        Menampilkan anggota dalam kelas: <strong>"{{ request('kelas') }}"</strong>
                    @elseif(request('status') && request('status') !== 'all')
                        Menampilkan anggota dengan status: <strong>"{{ request('status') === 'active' ? 'Aktif' : 'Nonaktif' }}"</strong>
                    @endif
                    <span class="text-gray-500">({{ $members->count() }} anggota ditemukan)</span>
                </div>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            @if($members->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS/NISN</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($members as $member)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $member->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $member->address }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $member->member_id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                            {{ $member->kelas ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $member->phone }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            @if($member->status === 'active') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $member->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('members.show', $member) }}" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('members.edit', $member) }}" class="text-indigo-600 hover:text-indigo-900">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('members.destroy', $member) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin menghapus anggota ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Belum ada anggota yang ditambahkan</p>
                    <a href="{{ route('members.create') }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        Tambah Anggota Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Import CSV Modal -->
<div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Import Data Siswa</h3>
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
                            Format: NISN, Nama, Kelas (dari Google Sheets)
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

        fetch('{{ route("members.import-csv") }}', {
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
