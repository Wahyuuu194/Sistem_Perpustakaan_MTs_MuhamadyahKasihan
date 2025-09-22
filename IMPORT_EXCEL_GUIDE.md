# 📚 Panduan Import Data CSV ke Sistem Perpustakaan

## 🚀 Persiapan Data

Sistem import menggunakan format CSV yang kompatibel dengan semua sistem. Tidak perlu install package tambahan.

## 📋 Format Data yang Didukung

Sistem ini mendukung 3 format data CSV berdasarkan file yang Anda miliki:

### 1. **Data Koleksi Perpustakaan** (`Data Koleksi Perpustakaan.csv`)
**Format Kolom:**
- `No` - Nomor urut
- `Jenis Koleksi` - Kategori buku (Kitab, Tafsir, Kamus, dll.)
- `Judul` - Judul buku
- `Pengarang` - Penulis buku
- `Penerbit` - Penerbit buku
- `Jumlah` - Jumlah eksemplar

**Contoh Data:**
```
1 | Kitab | Al-Qur'anulkarim | | | 57
2 | Tafsir | Tafsir Juz 'Amma for Kids 1 | Muhammad Muslih | Tiga Serangkai | 2
```

**Kategori Otomatis:** Sistem akan menentukan kategori berdasarkan jenis koleksi dan judul:
- **Kitab** - untuk jenis koleksi "Kitab" (Al-Qur'an, dll.)
- **Tafsir** - untuk jenis koleksi "Tafsir"
- **Pendidikan** - untuk jenis koleksi "Metode" atau judul mengandung "Metode"
- **Kamus** - untuk jenis koleksi "Kamus" atau judul mengandung "Kamus"
- **Referensi** - untuk jenis koleksi "ENSIKLOPEDI" atau judul mengandung "Ensiklopedi"
- **Teknologi** - untuk jenis koleksi "ICT" atau judul mengandung "Komputer", "Excel", "Word", "PowerPoint"
- **Buku Guru** - untuk jenis koleksi "Buku Guru"
- **Buku Pelajaran** - untuk jenis koleksi "Buku Pelajaran untuk Siswa"
- **Mata Pelajaran** - untuk buku pelajaran (IPA, IPS, Bahasa Indonesia, Bahasa Inggris, Matematika, PPKn, PJOK, Seni Budaya, Prakarya, Agama, Bahasa Arab, Sejarah, Bahasa Jawa)

### 2. **Buku Bacaan Madsaka** (`Buku Bacaan Madsaka.csv`)
**Format Kolom:**
- `No` - Nomor urut
- `Judul` - Judul buku
- `Pengarang` - Penulis buku
- `Penerbit` - Penerbit buku
- `Jumlah` - Jumlah eksemplar

**Contoh Data:**
```
1 | 100 Hadits Qudsi | M. Fahmi Hadi | CV MEGAH JAYA | 11
2 | 99 Asmaul Husna | Mutaroh Akmal | NAVILA | 1
```

### 3. **Buku Paket** (`Buku Paket.csv`)
**Format Kolom:**
- `Nama Buku` - Judul buku paket
- `Jumlah` - Jumlah eksemplar

**Contoh Data:**
```
Ilmu Pengetahuan Alam SMP/MTs Kelas VII Semester 1 | 104
Ilmu Pengetahuan Sosial SMP/MTs Kelas VII | 94
```

**Kategori Otomatis:** Sistem akan menentukan kategori mata pelajaran berdasarkan judul:
- **IPA** - untuk buku "Ilmu Pengetahuan Alam"
- **IPS** - untuk buku "Ilmu Pengetahuan Sosial" 
- **Bahasa Indonesia** - untuk buku "Bahasa Indonesia"
- **Bahasa Inggris** - untuk buku "Bahasa Inggris" atau "English"
- **Matematika** - untuk buku "Matematika"
- **PPKn** - untuk buku "Pendidikan Pancasila" atau "PPKn"
- **PJOK** - untuk buku "Pendidikan Jasmani", "Olahraga", atau "Kesehatan"
- **Seni Budaya** - untuk buku "Seni Budaya"
- **Prakarya** - untuk buku "Prakarya"
- **Pendidikan Kemuhammadiyahan** - untuk buku "Pendidikan Kemuhammadiyahan"
- **Buku Paket** - untuk buku yang tidak cocok dengan kategori di atas

## 🔧 Cara Menggunakan Fitur Import

### Langkah 1: Konversi Excel ke CSV
1. **Buka file Excel** Anda di Microsoft Excel atau Google Sheets
2. **Save As** → pilih format **"CSV (Comma delimited)"**
3. **Simpan file** dengan nama yang mudah diingat

### Langkah 2: Akses Halaman Import
1. Buka halaman **Daftar Buku**
2. Klik tombol **"Import Excel"** (tombol hijau)
3. Anda akan diarahkan ke halaman import

### Langkah 3: Upload File CSV
1. Klik **"Pilih File CSV"** dan pilih file `.csv` Anda
2. Pilih **"Jenis Data"** sesuai dengan format file Anda:
   - **Data Koleksi Perpustakaan** - untuk file dengan kolom lengkap
   - **Buku Bacaan Madsaka** - untuk file buku bacaan
   - **Buku Paket** - untuk file buku paket

### Langkah 4: Konfigurasi Import
Pilih opsi import yang sesuai:
- ✅ **Lewati buku yang sudah ada** - Buku duplikat akan dilewati
- ⚠️ **Update data buku yang sudah ada** - Data buku yang sudah ada akan diupdate
- ➕ **Tambahkan ke jumlah eksemplar yang sudah ada** - Jumlah akan ditambahkan

### Langkah 5: Preview dan Import
1. Klik **"Preview"** untuk melihat preview data (opsional)
2. Klik **"Import Data"** untuk memulai proses import
3. Tunggu proses selesai dan lihat hasil import

## ⚙️ Fitur-Fitur Import

### 🔍 **Deteksi Duplikat**
Sistem akan mendeteksi buku duplikat berdasarkan:
- ISBN (jika ada)
- Judul + Penulis (jika ISBN tidak ada)

### 📊 **Ekstraksi Informasi Kelas**
Sistem otomatis mengekstrak informasi kelas dari judul buku:
- "Kelas VII" → "VII"
- "Class 9" → "9"
- "IX Kelas" → "IX"

### 🏷️ **Generasi ISBN Otomatis**
Untuk buku yang tidak memiliki ISBN, sistem akan generate ISBN otomatis dengan format:
`978-XXX-XX-XXXXXX-X`

### 📈 **Progress Tracking**
Sistem menampilkan progress bar dan status import secara real-time.

## 🚨 Troubleshooting

### Error: "File Excel kosong"
- Pastikan file Excel memiliki data di sheet yang dipilih
- Periksa apakah ada data di baris ke-3 dan seterusnya (baris 1-2 biasanya header)

### Error: "Format data tidak lengkap"
- Pastikan jumlah kolom sesuai dengan format yang dipilih
- Periksa apakah ada kolom yang kosong

### Error: "Gagal membaca file Excel"
- Pastikan file tidak corrupt
- Coba save ulang file Excel dengan format `.xlsx`
- Pastikan file tidak sedang dibuka di aplikasi lain

### Import Lambat
- File besar (>1000 baris) mungkin memerlukan waktu lebih lama
- Tutup aplikasi lain yang menggunakan memory tinggi
- Pastikan server memiliki memory yang cukup

## 📝 Tips dan Best Practices

### ✅ **Sebelum Import:**
1. **Backup database** terlebih dahulu
2. **Test dengan file kecil** dulu (10-20 baris)
3. **Pastikan format data konsisten**
4. **Hapus baris kosong** di file Excel

### ✅ **Setelah Import:**
1. **Periksa hasil import** di halaman Daftar Buku
2. **Verifikasi jumlah data** yang diimport
3. **Periksa data duplikat** jika ada

### ✅ **Optimasi File Excel:**
1. **Gunakan format .xlsx** (lebih stabil)
2. **Hapus formatting** yang tidak perlu
3. **Pastikan data di sheet pertama** (Sheet1)
4. **Gunakan header yang jelas** di baris pertama

## 🔄 Rollback Import

Jika terjadi kesalahan, Anda bisa:
1. **Hapus data yang salah** melalui halaman Daftar Buku
2. **Import ulang** dengan konfigurasi yang benar
3. **Restore database** dari backup (jika diperlukan)

## 📞 Support

Jika mengalami masalah, periksa:
1. **Log error** di `storage/logs/laravel.log`
2. **Format file Excel** sesuai dengan panduan
3. **Permission file** dan folder storage
4. **Memory limit** PHP (minimal 256MB)

---

**Selamat mengimport data perpustakaan Anda! 📚✨**
