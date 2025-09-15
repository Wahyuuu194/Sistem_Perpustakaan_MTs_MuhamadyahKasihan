@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <!-- <h1 class="text-2xl font-bold text-gray-900">Buat Peminjaman Baru</h1> -->
            <a href="{{ route('borrowings.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>Kembali 
            </a>
        </div>

        <form action="{{ route('borrowings.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="book_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Buku</label>
                    <select name="book_id" id="book_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Buku</option>
                        @foreach($books as $book)
                            <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                {{ $book->title }}@if($book->kelas) [{{ $book->kelas }}]@endif - {{ $book->author }} (Stok: {{ $book->available_quantity }})
                            </option>
                        @endforeach
                    </select>
                    
                    @error('book_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="member_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Anggota</label>
                    
                    <!-- Scan QR Code Button -->
                    <div class="mb-3">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center flex-1">
                                    <i class="fas fa-qrcode text-green-600 text-xl mr-3"></i>
                                    <div class="flex-1">
                                        <h3 class="text-sm font-semibold text-green-800 mb-1">Scan QR Code Kartu Akses</h3>
                                        <p class="text-xs text-green-600">Gunakan kamera untuk scan QR code dari kartu akses siswa</p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="button" id="scanMemberBtn" class="px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition font-medium text-sm">
                                        <i class="fas fa-camera mr-1"></i>Scan QR
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Manual Selection Dropdown -->
                    <select name="member_id" id="member_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Anggota</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}
                                data-member-id="{{ $member->member_id }}"
                                data-name="{{ $member->name }}"
                                data-kelas="{{ $member->kelas }}">
                                {{ $member->name }} ({{ $member->member_id }}) - {{ $member->kelas }}
                            </option>
                        @endforeach
                    </select>
                    @error('member_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Pinjam </label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div> -->

                <div>
                    <label for="borrow_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pinjam </label>
                    <input type="date" name="borrow_date" id="borrow_date" value="{{ old('borrow_date', date('Y-m-d')) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('borrow_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengembalian </label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+14 days'))) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('due_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Informasi Peminjaman</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Buku hanya bisa dipinjam jika stok tersedia</li>
                                    <!-- <li>Jatuh tempo otomatis 14 hari dari tanggal pinjam</li> -->
                                    <!-- <li>Status peminjaman akan otomatis menjadi "Dipinjam"</li>
                                    <li>Stok buku akan berkurang otomatis saat dipinjam</li> -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('borrowings.index') }}" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Buat Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

<!-- QR Scanner Modal -->
<div id="qrModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Scan QR Code Anggota</h3>
                    <button type="button" id="closeQrModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <div id="qr-reader" style="width: 100%"></div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Atau masukkan NISN manual:</label>
                    <input type="text" id="manualNisn" placeholder="Masukkan NISN dari kartu akses"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelQrScan" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition">
                        Batal
                    </button>
                    <button type="button" id="processQrScan" class="px-4 py-2 text-white bg-green-600 rounded-md hover:bg-green-700 transition">
                        Proses Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include jsQR library -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scanMemberBtn = document.getElementById('scanMemberBtn');
    const qrModal = document.getElementById('qrModal');
    const closeQrModal = document.getElementById('closeQrModal');
    const cancelQrScan = document.getElementById('cancelQrScan');
    const processQrScan = document.getElementById('processQrScan');
    const manualNisn = document.getElementById('manualNisn');
    const memberSelect = document.getElementById('member_id');
    
    let stream = null;
    let video = null;
    let canvas = null;
    let context = null;
    let scanning = false;

    scanMemberBtn.addEventListener('click', function() {
        qrModal.classList.remove('hidden');
        startCamera();
    });

    closeQrModal.addEventListener('click', function() {
        closeModal();
    });

    cancelQrScan.addEventListener('click', function() {
        closeModal();
    });

    function closeModal() {
        qrModal.classList.add('hidden');
        stopCamera();
    }

    function startCamera() {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(function(mediaStream) {
                stream = mediaStream;
                video = document.createElement('video');
                video.srcObject = stream;
                video.play();
                
                const qrReader = document.getElementById('qr-reader');
                qrReader.innerHTML = '';
                qrReader.appendChild(video);
                
                video.addEventListener('loadedmetadata', function() {
                    video.style.width = '100%';
                    video.style.height = 'auto';
                });
                
                scanning = true;
                scanQR();
            })
            .catch(function(err) {
                console.error('Error accessing camera:', err);
                alert('Tidak dapat mengakses kamera. Pastikan izin kamera sudah diberikan.');
            });
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        scanning = false;
    }

    function scanQR() {
        if (!scanning) return;
        
        if (video && video.readyState === video.HAVE_ENOUGH_DATA) {
            if (!canvas) {
                canvas = document.createElement('canvas');
                context = canvas.getContext('2d');
            }
            
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);
            
            if (code) {
                processQRData(code.data);
                return;
            }
        }
        
        requestAnimationFrame(scanQR);
    }

    function processQRData(qrData) {
        const nisn = qrData.trim();
        
        // Check if NISN exists in database
        fetch('/members/check-nisn', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ nisn: nisn })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Find and select the member in dropdown
                const options = memberSelect.querySelectorAll('option[data-member-id]');
                for (let option of options) {
                    if (option.dataset.memberId === data.member.member_id) {
                        memberSelect.value = option.value;
                        closeModal();
                        alert('Data anggota ditemukan!\nNama: ' + data.member.name + '\nKelas: ' + data.member.kelas);
                        return;
                    }
                }
                alert('Data anggota tidak ditemukan di dropdown');
            } else {
                alert('NISN tidak ditemukan di database: ' + nisn);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses QR Code');
        });
    }

    // Process manual NISN input
    processQrScan.addEventListener('click', function() {
        const nisn = manualNisn.value.trim();
        
        if (!nisn) {
            alert('Masukkan NISN dari kartu akses');
            return;
        }

        processQRData(nisn);
    });

    // Handle Enter key in manual input
    manualNisn.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            processQrScan.click();
        }
    });
});
</script>

@endsection
