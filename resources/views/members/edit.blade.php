@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-4 sm:py-6 px-4 sm:px-0">
    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Edit Anggota</h1>
            <a href="{{ route('members.index') }}" class="text-blue-600 hover:text-blue-800 text-sm sm:text-base">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <form action="{{ route('members.update', $member) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $member->name) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="member_id" class="block text-sm font-medium text-gray-700 mb-2">NIS/NISN *</label>
                    <input type="text" name="member_id" id="member_id" value="{{ old('member_id', $member->member_id) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('member_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="kelas" class="block text-sm font-medium text-gray-700 mb-2">Kelas *</label>
                    <select name="kelas" id="kelas" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Kelas</option>
                        <option value="7A" {{ old('kelas', $member->kelas) == '7A' ? 'selected' : '' }}>7A</option>
                        <option value="7B" {{ old('kelas', $member->kelas) == '7B' ? 'selected' : '' }}>7B</option>
                        <option value="7C" {{ old('kelas', $member->kelas) == '7C' ? 'selected' : '' }}>7C</option>
                        <option value="8A" {{ old('kelas', $member->kelas) == '8A' ? 'selected' : '' }}>8A</option>
                        <option value="8B" {{ old('kelas', $member->kelas) == '8B' ? 'selected' : '' }}>8B</option>
                        <option value="8C" {{ old('kelas', $member->kelas) == '8C' ? 'selected' : '' }}>8C</option>
                        <option value="9A" {{ old('kelas', $member->kelas) == '9A' ? 'selected' : '' }}>9A</option>
                        <option value="9B" {{ old('kelas', $member->kelas) == '9B' ? 'selected' : '' }}>9B</option>
                        <option value="9C" {{ old('kelas', $member->kelas) == '9C' ? 'selected' : '' }}>9C</option>
                    </select>
                    @error('kelas')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $member->phone) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="registration_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Registrasi *</label>
                    <input type="date" name="registration_date" id="registration_date" value="{{ old('registration_date', $member->registration_date) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('registration_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" id="status" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="active" {{ old('status', $member->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $member->status) == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                <textarea name="address" id="address" rows="3" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address', $member->address) }}</textarea>
                @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
                <a href="{{ route('members.index') }}" class="w-full sm:w-auto px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition text-center text-sm sm:text-base">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-sm sm:text-base">
                    <i class="fas fa-save mr-2"></i>Update Anggota
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
