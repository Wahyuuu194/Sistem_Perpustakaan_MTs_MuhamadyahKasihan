<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Sistem Perpustakaan MTs Muhamadyah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center py-12">
    <div class="max-w-md w-full space-y-8 p-8">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <!-- Logo dan Header -->
            <div class="text-center mb-8">
                <div class="mx-auto h-16 w-16 rounded-full flex items-center justify-center mb-4">
                    <img src="{{ asset('images/logo-mts.png') }}" alt="Logo MTs Muhamadyah Kasihan" class="h-22 w-22 rounded-full bg-white p-1">
                </div>
                <h2 class="text-3xl font-bold text-gray-900">Daftar Akun Baru</h2>
                <p class="text-gray-600 mt-2">Sistem Perpustakaan MTs Muhamadyah</p>
            </div>

            <!-- Form Register -->
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf
                
                <!-- Nama -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Nama Lengkap
                    </label>
                    <input id="name" 
                           type="text" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           autofocus
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                           placeholder="Masukkan nama lengkap Anda">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                           placeholder="Masukkan email Anda">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                           placeholder="Masukkan password (min. 8 karakter)">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Konfirmasi Password
                    </label>
                    <input id="password_confirmation" 
                           type="password" 
                           name="password_confirmation" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ulangi password Anda">
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-user-plus mr-2"></i>
                        Daftar
                    </button>
                </div>

                <!-- Login Link -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                            Masuk di sini
                        </a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center text-sm text-gray-500">
            <p>&copy; 2024 MTs Muhamadyah. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

