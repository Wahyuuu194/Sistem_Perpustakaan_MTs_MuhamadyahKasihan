# Sistem Perpustakaan MTs Muhamadyah

Sistem perpustakaan digital yang dibangun dengan Laravel untuk mengelola buku, anggota, dan peminjaman.

## Fitur Utama

- **Manajemen Buku**: Tambah, edit, hapus, dan lihat detail buku
- **Manajemen Anggota**: Daftar anggota perpustakaan dengan status aktif/nonaktif
- **Sistem Peminjaman**: Peminjaman dan pengembalian buku dengan tracking status
- **Dashboard**: Overview statistik perpustakaan dan aktivitas terbaru
- **Validasi**: Validasi input dan business logic untuk mencegah kesalahan
- **Responsive Design**: Interface yang responsif dengan Tailwind CSS

## Teknologi

- **Backend**: Laravel 12
- **Database**: SQLite (default), bisa diubah ke MySQL/PostgreSQL
- **Frontend**: Blade templates dengan Tailwind CSS
- **Icons**: Font Awesome

## Instalasi

1. Clone repository ini
2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy file environment:
   ```bash
   cp .env.example .env
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Buat database SQLite:
   ```bash
   touch database/database.sqlite
   ```

6. Jalankan migration:
   ```bash
   php artisan migrate
   ```

7. Jalankan server development:
   ```bash
   php artisan serve
   ```

8. Buka browser ke `http://localhost:8000`

## Struktur Database

### Tabel Books
- id, title, author, isbn, description, publisher
- publication_year, quantity, category, location
- timestamps

### Tabel Members
- id, name, email, phone, address, birth_date
- member_id, registration_date, status
- timestamps

### Tabel Borrowings
- id, book_id, member_id, borrow_date, due_date
- return_date, status, notes
- timestamps

## Penggunaan

### Dashboard
- Overview statistik perpustakaan
- Peminjaman terbaru
- Buku terpopuler

### Manajemen Buku
- Tambah buku baru dengan informasi lengkap
- Edit informasi buku
- Lihat detail dan riwayat peminjaman
- Tracking stok tersedia

### Manajemen Anggota
- Daftar anggota perpustakaan
- Status aktif/nonaktif
- Riwayat peminjaman per anggota

### Sistem Peminjaman
- Buat peminjaman baru
- Tracking status (dipinjam/dikembalikan/terlambat)
- Pengembalian buku
- Validasi stok tersedia

## API Endpoints

- `GET /` - Dashboard
- `GET /books` - Daftar buku
- `POST /books` - Tambah buku
- `GET /books/{id}` - Detail buku
- `PUT /books/{id}` - Update buku
- `DELETE /books/{id}` - Hapus buku
- `GET /members` - Daftar anggota
- `POST /members` - Tambah anggota
- `GET /members/{id}` - Detail anggota
- `PUT /members/{id}` - Update anggota
- `DELETE /members/{id}` - Hapus anggota
- `GET /borrowings` - Daftar peminjaman
- `POST /borrowings` - Buat peminjaman
- `GET /borrowings/{id}` - Detail peminjaman
- `PATCH /borrowings/{id}/return` - Kembalikan buku
- `DELETE /borrowings/{id}` - Hapus peminjaman

## Kontribusi

1. Fork repository
2. Buat branch fitur baru
3. Commit perubahan
4. Push ke branch
5. Buat Pull Request

## Lisensi

Proyek ini dibuat untuk MTs Muhamadyah. All rights reserved.
