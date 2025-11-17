@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-8 px-4 sm:px-0">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Detail Guru</h1>
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-2">
            <a href="{{ route('teachers.edit', $teacher) }}" class="w-full sm:w-auto text-indigo-600 hover:text-indigo-900 px-3 py-2 border border-indigo-600 rounded-md hover:bg-indigo-50 transition text-center text-sm sm:text-base">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('teachers.index') }}" class="w-full sm:w-auto text-gray-600 hover:text-gray-900 px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition text-center text-sm sm:text-base">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">NIP</label>
                <p class="text-lg font-semibold text-gray-900">{{ $teacher->teacher_id }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Lengkap</label>
                <p class="text-lg font-semibold text-gray-900">{{ $teacher->name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">No. Telepon</label>
                <p class="text-gray-900">{{ $teacher->phone ?? '-' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Mata Pelajaran</label>
                <p class="text-gray-900">{{ $teacher->subject ?? '-' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Lahir</label>
                <p class="text-gray-900">{{ $teacher->birth_date ? $teacher->birth_date->format('d/m/Y') : '-' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Daftar</label>
                <p class="text-gray-900">{{ $teacher->registration_date->format('d/m/Y') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                    {{ $teacher->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $teacher->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                </span>
            </div>

            @if($teacher->address)
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">Alamat</label>
                <p class="text-gray-900">{{ $teacher->address }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Peminjaman Aktif -->
    @if($teacher->borrowings && $teacher->borrowings->count() > 0)
    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
        <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4">Riwayat Peminjaman</h2>
        <div class="overflow-x-auto -mx-4 sm:mx-0">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Pinjam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Kembali</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($teacher->borrowings as $borrowing)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $borrowing->book->title ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $borrowing->borrow_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $borrowing->return_date ? $borrowing->return_date->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $borrowing->status === 'returned' ? 'bg-green-100 text-green-800' : ($borrowing->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $borrowing->status === 'returned' ? 'Dikembalikan' : ($borrowing->status === 'overdue' ? 'Terlambat' : 'Dipinjam') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row justify-end gap-3">
        <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" 
            onsubmit="return confirm('Yakin ingin menghapus data guru ini?')" class="inline w-full sm:w-auto">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition text-sm sm:text-base">
                <i class="fas fa-trash mr-2"></i>Hapus Data Guru
            </button>
        </form>
    </div>
</div>
@endsection

