# Setup Database MySQL untuk Sistem Perpustakaan

## Langkah 1: Buka MySQL Workbench

1. Buka aplikasi **MySQL Workbench** yang sudah terinstall
2. Klik pada connection **task_rpl** yang sudah ada (atau buat connection baru jika belum ada)

## Langkah 2: Jalankan Script SQL

1. Buka file `database/library_system.sql` di text editor
2. Copy seluruh isi file tersebut
3. Di MySQL Workbench, buat query baru (Ctrl+T)
4. Paste script SQL tersebut
5. Jalankan script dengan klik tombol ⚡ (Execute)

## Langkah 3: Verifikasi Database

Setelah script berhasil dijalankan, akan muncul output:
```
table_name    total_records
Books         10
Members       5
Borrowings    15
Users         1
```

## Langkah 4: Konfigurasi Laravel

1. **Edit file `.env`** (atau buat file baru jika belum ada):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=library_system
DB_USERNAME=root
DB_PASSWORD=
```

2. **Generate App Key**:
```bash
php artisan key:generate
```

3. **Clear cache**:
```bash
php artisan config:clear
php artisan cache:clear
```

## Langkah 5: Jalankan Aplikasi

```bash
php artisan serve
```

## Struktur Database

### Tabel `books`
- `id` - Primary Key
- `title` - Judul buku
- `author` - Penulis
- `isbn` - ISBN buku (unique)
- `description` - Deskripsi buku
- `publisher` - Penerbit
- `publication_year` - Tahun terbit
- `quantity` - Jumlah stok
- `category` - Kategori buku
- `location` - Lokasi di rak

### Tabel `members`
- `id` - Primary Key
- `name` - Nama anggota
- `email` - Email (unique)
- `phone` - Nomor telepon
- `address` - Alamat
- `birth_date` - Tanggal lahir
- `member_id` - ID anggota (unique)
- `registration_date` - Tanggal registrasi
- `status` - Status aktif/nonaktif

### Tabel `borrowings`
- `id` - Primary Key
- `book_id` - Foreign key ke books
- `member_id` - Foreign key ke members
- `borrow_date` - Tanggal pinjam
- `due_date` - Tanggal jatuh tempo
- `return_date` - Tanggal kembali
- `status` - Status peminjaman
- `notes` - Catatan

## Data Sample yang Tersedia

- **10 buku** dengan berbagai kategori (Novel, Pendidikan, Referensi, Sejarah)
- **5 anggota** perpustakaan dengan data lengkap
- **15 peminjaman** untuk testing sistem
- **1 user admin** untuk login (jika diperlukan)

## Troubleshooting

### Error "Access denied for user 'root'@'localhost'"
- Pastikan MySQL service berjalan
- Cek password root MySQL
- Update file `.env` dengan password yang benar

### Error "Database doesn't exist"
- Jalankan script SQL terlebih dahulu
- Pastikan nama database `library_system` sudah dibuat

### Error "Table doesn't exist"
- Jalankan script SQL lengkap
- Pastikan semua tabel sudah terbuat

## Koneksi Database

**Host:** 127.0.0.1 (localhost)  
**Port:** 3306  
**Database:** library_system  
**Username:** root  
**Password:** (kosong/default)
