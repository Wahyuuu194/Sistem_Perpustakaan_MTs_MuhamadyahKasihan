# API ISBN Lookup - Panduan Cepat

## 🚀 Fitur Utama

API ini memungkinkan Anda untuk:
- ✅ Mencari ISBN buku secara otomatis menggunakan Google Books API
- ✅ Update ISBN untuk multiple books sekaligus
- ✅ Validasi format ISBN
- ✅ Mengelola buku yang belum memiliki ISBN

## 📋 Endpoint Utama

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/api/isbn/search` | Cari ISBN untuk satu buku |
| POST | `/api/isbn/search-multiple` | Cari ISBN untuk multiple books |
| POST | `/api/isbn/validate` | Validasi format ISBN |
| PUT | `/api/isbn/update-book/{id}` | Update ISBN buku tertentu |
| POST | `/api/isbn/bulk-update` | Bulk update ISBN |
| GET | `/api/isbn/books-without-isbn` | Daftar buku tanpa ISBN |

## 🎯 Cara Menggunakan

### 1. Via Web Interface
Akses: `http://localhost:8000/books/isbn-manager`

### 2. Via API (cURL)
```bash
# Cari ISBN untuk satu buku
curl -X POST http://localhost:8000/api/isbn/search \
  -H "Content-Type: application/json" \
  -d '{"title": "Laskar Pelangi", "author": "Andrea Hirata"}'

# Bulk update ISBN
curl -X POST http://localhost:8000/api/isbn/bulk-update \
  -H "Content-Type: application/json" \
  -d '{"book_ids": [1, 2, 3]}'
```

### 3. Via JavaScript
```javascript
// Cari ISBN
fetch('/api/isbn/search', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        title: 'Laskar Pelangi',
        author: 'Andrea Hirata'
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

## 🔧 Instalasi & Setup

1. **Pastikan server Laravel berjalan:**
   ```bash
   php artisan serve
   ```

2. **Test API:**
   ```bash
   php test_isbn_api.php
   ```

3. **Akses web interface:**
   ```
   http://localhost:8000/books/isbn-manager
   ```

## ⚡ Contoh Response

### Sukses
```json
{
    "success": true,
    "message": "ISBN berhasil ditemukan",
    "isbn": "9789793062792",
    "book_info": {
        "title": "Laskar Pelangi",
        "authors": ["Andrea Hirata"],
        "publisher": "Bentang Pustaka",
        "published_date": "2005"
    }
}
```

### Error
```json
{
    "success": false,
    "message": "Buku tidak ditemukan di Google Books",
    "isbn": null,
    "book_info": null
}
```

## 🎨 Web Interface Features

- **Tab 1: Cari ISBN** - Pencarian manual untuk satu atau multiple books
- **Tab 2: Bulk Update** - Update ISBN otomatis untuk buku yang sudah ada
- **Tab 3: Buku Tanpa ISBN** - Daftar dan kelola buku yang belum memiliki ISBN

## ⚠️ Catatan Penting

- API menggunakan Google Books API (gratis, tidak perlu API key)
- Ada delay 200ms antar request untuk menghindari rate limiting
- Maksimal 50 books per bulk operation
- ISBN harus unique di database
- Pastikan koneksi internet tersedia

## 🐛 Troubleshooting

**API tidak berfungsi?**
- Pastikan server Laravel berjalan
- Check koneksi internet
- Lihat log Laravel untuk error details

**ISBN tidak ditemukan?**
- Coba dengan judul yang lebih spesifik
- Pastikan penulis ditulis dengan benar
- Periksa apakah buku ada di Google Books

## 📚 Dokumentasi Lengkap

Lihat file `ISBN_API_DOCUMENTATION.md` untuk dokumentasi lengkap dengan contoh penggunaan yang lebih detail.

