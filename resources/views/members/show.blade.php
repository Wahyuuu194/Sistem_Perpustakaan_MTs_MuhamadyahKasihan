@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Detail Anggota</h1>
        <div class="flex space-x-3">
            <a href="{{ route('members.edit', $member) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('members.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $member->name }}</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <span class="text-sm font-medium text-gray-500">ID Anggota:</span>
                            <p class="text-lg text-gray-900">{{ $member->member_id }}</p>
                        </div>
                        
                        <div>
                            <span class="text-sm font-medium text-gray-500">Email:</span>
                            <p class="text-lg text-gray-900">{{ $member->email }}</p>
                        </div>
                        
                        @if($member->phone)
                        <div>
                            <span class="text-sm font-medium text-gray-500">Telepon:</span>
                            <p class="text-lg text-gray-900">{{ $member->phone }}</p>
                        </div>
                        @endif
                        
                        @if($member->birth_date)
                        <div>
                            <span class="text-sm font-medium text-gray-500">Tanggal Lahir:</span>
                            <p class="text-lg text-gray-900">{{ $member->birth_date->format('d/m/Y') }}</p>
                        </div>
                        @endif
                        
                        <div>
                            <span class="text-sm font-medium text-gray-500">Status:</span>
                            <span class="px-2 py-1 text-sm font-medium rounded-full 
                                @if($member->status === 'active') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($member->status) }}
                            </span>
                        </div>
                        
                        <div>
                            <span class="text-sm font-medium text-gray-500">Tanggal Registrasi:</span>
                            <p class="text-lg text-gray-900">{{ $member->registration_date->format('d/m/Y') }}</p>
                        </div>
                        
                        @if($member->address)
                        <div>
                            <span class="text-sm font-medium text-gray-500">Alamat:</span>
                            <p class="text-gray-900 mt-2">{{ $member->address }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Riwayat Peminjaman</h3>
                    
                    @if($member->borrowings->count() > 0)
                        <div class="space-y-3">
                            @foreach($member->borrowings->take(5) as $borrowing)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $borrowing->book->title }}</p>
                                            <p class="text-sm text-gray-600">{{ $borrowing->book->author }}</p>
                                            <p class="text-sm text-gray-500">{{ $borrowing->borrow_date->format('d/m/Y') }} - {{ $borrowing->due_date->format('d/m/Y') }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            @if($borrowing->status === 'borrowed') 
                                                @if($borrowing->is_overdue) bg-red-100 text-red-800
                                                @else bg-blue-100 text-blue-800 @endif
                                            @elseif($borrowing->status === 'returned') bg-green-100 text-green-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            @if($borrowing->status === 'borrowed' && $borrowing->is_overdue)
                                                Terlambat
                                            @else
                                                {{ ucfirst($borrowing->status) }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($member->borrowings->count() > 5)
                            <p class="text-sm text-gray-500 mt-3">Dan {{ $member->borrowings->count() - 5 }} peminjaman lainnya...</p>
                        @endif
                        
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <strong>Total Peminjaman:</strong> {{ $member->borrowings->count() }} buku
                            </p>
                            <p class="text-sm text-blue-800">
                                <strong>Sedang Dipinjam:</strong> {{ $member->active_borrowings->count() }} buku
                            </p>
                        </div>
                    @else
                        <p class="text-gray-500">Belum ada riwayat peminjaman</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
