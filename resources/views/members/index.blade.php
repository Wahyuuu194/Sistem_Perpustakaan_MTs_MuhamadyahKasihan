@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-8">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Daftar Anggota</h1>
        <a href="{{ route('members.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Tambah Anggota
        </a>
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
@endsection
