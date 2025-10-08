@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-8">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Daftar Peminjaman</h1>
        <a href="{{ route('borrowings.create') }}" class="bg-blue-600 text-white px-3 py-2 sm:px-4 rounded-lg hover:bg-blue-700 transition text-sm sm:text-base text-center">
            <i class="fas fa-plus mr-1 sm:mr-2"></i><span class="hidden sm:inline">Buat Peminjaman</span><span class="sm:hidden">Buat</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-4 sm:p-6">
            @if($borrowings->count() > 0)
                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buku</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Anggota</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pinjam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengembalian</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($borrowings as $borrowing)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="text-sm font-medium text-gray-900">{{ $borrowing->book->title }}</div>
                                            @if($borrowing->book->kelas)
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                                    {{ $borrowing->book->kelas }}
                                                </span>
                                            @endif
                                            @if($borrowing->book->rak)
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                    Rak {{ $borrowing->book->rak }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $borrowing->book->author }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($borrowing->member)
                                            <div class="text-sm font-medium text-gray-900">{{ $borrowing->member->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $borrowing->member->member_id }}</div>
                                            <div class="text-xs text-gray-400">{{ $borrowing->member->kelas ?? 'Kelas belum ditentukan' }}</div>
                                        @elseif($borrowing->teacher)
                                            <div class="text-sm font-medium text-gray-900">{{ $borrowing->teacher->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $borrowing->teacher->teacher_id }}</div>
                                            <div class="text-xs text-gray-400">Guru</div>
                                        @else
                                            <div class="text-sm text-gray-500">-</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                            {{ $borrowing->quantity }} buku
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $borrowing->borrow_date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $borrowing->due_date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($borrowing->status === 'borrowed')
                                            @if($borrowing->due_date->isPast())
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                    Terlambat
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                    Dipinjam
                                                </span>
                                            @endif
                                        @elseif($borrowing->status === 'returned')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                Dikembalikan
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                {{ $borrowing->status_label }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('borrowings.show', $borrowing) }}" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($borrowing->status === 'borrowed')
                                                <form action="{{ route('borrowings.return', $borrowing) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-green-600 hover:text-green-900">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('borrowings.destroy', $borrowing) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin menghapus peminjaman ini?')">
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
                    @foreach($borrowings as $borrowing)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">{{ $borrowing->book->title }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $borrowing->book->author }}</p>
                                    @if($borrowing->book->kelas)
                                        <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800 mt-2">
                                            {{ $borrowing->book->kelas }}
                                        </span>
                                    @endif
                                    @if($borrowing->book->rak)
                                        <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 mt-2 ml-2">
                                            Rak {{ $borrowing->book->rak }}
                                        </span>
                                    @endif
                                </div>
                                <div class="ml-3 flex-shrink-0">
                                    @if($borrowing->status === 'borrowed')
                                        @if($borrowing->due_date->isPast())
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                Terlambat
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                Dipinjam
                                            </span>
                                        @endif
                                    @elseif($borrowing->status === 'returned')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            Dikembalikan
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                            {{ $borrowing->status_label }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Peminjam</p>
                                @if($borrowing->member)
                                    <p class="text-sm font-medium text-gray-900">{{ $borrowing->member->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $borrowing->member->member_id }}</p>
                                    <p class="text-xs text-gray-500">{{ $borrowing->member->kelas ?? 'Kelas belum ditentukan' }}</p>
                                @elseif($borrowing->teacher)
                                    <p class="text-sm font-medium text-gray-900">{{ $borrowing->teacher->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $borrowing->teacher->teacher_id }}</p>
                                    <p class="text-xs text-gray-500">Guru</p>
                                @else
                                    <p class="text-sm text-gray-500">-</p>
                                @endif
                                <div class="mt-2">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        {{ $borrowing->quantity }} buku dipinjam
                                    </span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Tanggal Pinjam</p>
                                    <p class="text-sm text-gray-900">{{ $borrowing->borrow_date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Tanggal Kembali</p>
                                    <p class="text-sm text-gray-900">{{ $borrowing->due_date->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('borrowings.show', $borrowing) }}" class="text-blue-600 hover:text-blue-900 p-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($borrowing->status === 'borrowed')
                                    <form action="{{ route('borrowings.return', $borrowing) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-green-600 hover:text-green-900 p-2">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('borrowings.destroy', $borrowing) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 p-2" onclick="return confirm('Yakin ingin menghapus peminjaman ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-book-open text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Belum ada peminjaman yang dibuat</p>
                    <a href="{{ route('borrowings.create') }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        Buat Peminjaman Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
