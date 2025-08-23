<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Perpustakaan MTs Muhamadyah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-book-open text-2xl"></i>
                    <h1 class="text-xl font-bold">Perpustakaan MTs Muhamadyah</h1>
                </div>
                <div class="hidden md:flex space-x-6">
                    <a href="{{ route('dashboard') }}" class="hover:text-blue-200 transition">Dashboard</a>
                    <a href="{{ route('books.index') }}" class="hover:text-blue-200 transition">Buku</a>
                    <a href="{{ route('members.index') }}" class="hover:text-blue-200 transition">Anggota</a>
                    <a href="{{ route('borrowings.index') }}" class="hover:text-blue-200 transition">Peminjaman</a>
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
