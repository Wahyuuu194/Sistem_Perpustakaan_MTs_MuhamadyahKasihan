@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-8">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Daftar Guru</h1>
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
            <a href="{{ route('teachers.create') }}" class="bg-blue-600 text-white px-3 py-2 sm:px-4 rounded-lg hover:bg-blue-700 transition text-sm sm:text-base text-center">
                <i class="fas fa-plus mr-1 sm:mr-2"></i><span class="hidden sm:inline">Tambah Guru</span><span class="sm:hidden">Tambah</span>
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
                <div class="flex flex-col sm:flex-row gap-2">
                    <button type="submit" class="px-3 py-2 sm:px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-sm sm:text-base">
                        <i class="fas fa-search mr-1 sm:mr-2"></i>Cari
                    </button>
                    @if(request('search') || (request('status') && request('status') !== ''))
                        <a href="{{ route('teachers.index') }}" class="px-3 py-2 sm:px-4 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition text-sm sm:text-base text-center">
                            <i class="fas fa-times mr-1 sm:mr-2"></i>Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Teachers Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 sm:p-6">
            @if($teachers->count() > 0)
                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
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
                            @foreach($teachers as $teacher)
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
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-4">
                    @foreach($teachers as $teacher)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $teacher->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $teacher->teacher_id }}</p>
                                    @if($teacher->phone)
                                        <p class="text-sm text-gray-500">{{ $teacher->phone }}</p>
                                    @endif
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $teacher->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $teacher->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Mata Pelajaran</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $teacher->subject ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Tanggal Daftar</p>
                                    <p class="text-sm text-gray-900">{{ $teacher->registration_date->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('teachers.show', $teacher) }}" class="text-blue-600 hover:text-blue-900 p-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('teachers.edit', $teacher) }}" class="text-indigo-600 hover:text-indigo-900 p-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data guru ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 p-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-chalkboard-teacher text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Tidak ada data guru</p>
                    <a href="{{ route('teachers.create') }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        Tambah Guru Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Pagination -->
    @if($teachers->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $teachers->links() }}
        </div>
    @endif
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Import functionality removed - now available in Administrator menu
});
</script>
@endsection
