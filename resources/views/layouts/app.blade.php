<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Perpustakaan MTs Muhamadyah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('images/logo-mts.png') }}" alt="Logo MTs Muhamadyah Kasihan" class="h-12 w-12 rounded-full bg-white p-1">
                    <h1 class="text-xl font-bold">Perpustakaan MTs Muhamadyah Kasihan</h1>
                </div>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('dashboard') }}" class="hover:text-blue-200 transition">Beranda</a>
                    <a href="{{ route('books.index') }}" class="hover:text-blue-200 transition">Buku</a>
                    <a href="{{ route('members.index') }}" class="hover:text-blue-200 transition">Anggota</a>
                    <a href="{{ route('borrowings.index') }}" class="hover:text-blue-200 transition">Peminjaman</a>
                    
                    <!-- User Dropdown -->
                    <div class="relative group">
                        <button class="flex items-center space-x-2 hover:text-blue-200 transition">
                            <i class="fas fa-user-circle text-xl"></i>
                            <span class="max-w-32 truncate">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-sm"></i>
                        </button>
                        
                        <div class="absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg py-1 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                            <div class="px-4 py-2 text-sm text-gray-700 border-b">
                                <p class="font-medium break-words">{{ Auth::user()->name }}</p>
                                <p class="text-gray-500 break-all text-xs">{{ Auth::user()->email }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1 max-w-7xl mx-auto py-6 px-4">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; 2024 Sistem Perpustakaan MTs Muhamadyah. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
