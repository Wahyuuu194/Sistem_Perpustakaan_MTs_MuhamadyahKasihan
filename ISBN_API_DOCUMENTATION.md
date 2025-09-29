# API ISBN Lookup - Dokumentasi

API ini memungkinkan Anda untuk mencari ISBN buku secara otomatis menggunakan Google Books API tanpa harus memasukkan satu per satu.

## Base URL
```
http://localhost:8000/api/isbn
```

## Endpoints

### 1. Cari ISBN untuk Satu Buku
**POST** `/api/isbn/search`

Mencari ISBN untuk satu buku berdasarkan judul dan penulis.

#### Request Body:
```json
{
    "title": "Judul Buku",
    "author": "Nama Penulis" // optional
}
```

#### Response:
```json
{
    "success": true,
    "message": "ISBN berhasil ditemukan",
    "isbn": "9781234567890",
    "book_info": {
        "title": "Judul Buku Lengkap",
        "authors": ["Nama Penulis"],
        "publisher": "Nama Penerbit",
        "published_date": "2023",
        "description": "Deskripsi buku...",
        "page_count": 200,
        "categories": ["Fiction"],
        "language": "id",
        "preview_link": "https://books.google.com/...",
        "thumbnail": "https://books.google.com/books/content/..."
    }
}
```

### 2. Cari ISBN untuk Multiple Books
**POST** `/api/isbn/search-multiple`

Mencari ISBN untuk multiple books sekaligus (maksimal 50 buku).

#### Request Body:
```json
{
    "books": [
        {
            "title": "Judul Buku 1",
            "author": "Penulis 1"
        },
        {
            "title": "Judul Buku 2",
            "author": "Penulis 2"
        }
    ]
}
```

#### Response:
```json
{
    "success": true,
    "message": "Proses selesai. Berhasil: 2/2",
    "total_processed": 2,
    "success_count": 2,
    "results": [
        {
            "index": 0,
            "input": {
                "title": "Judul Buku 1",
                "author": "Penulis 1"
            },
            "success": true,
            "message": "ISBN berhasil ditemukan",
            "isbn": "9781234567890",
            "book_info": { ... }
        },
        {
            "index": 1,
            "input": {
                "title": "Judul Buku 2",
                "author": "Penulis 2"
            },
            "success": true,
            "message": "ISBN berhasil ditemukan",
            "isbn": "9780987654321",
            "book_info": { ... }
        }
    ]
}
```

### 3. Validasi Format ISBN
**POST** `/api/isbn/validate`

Memvalidasi format ISBN.

#### Request Body:
```json
{
    "isbn": "9781234567890"
}
```

#### Response:
```json
{
    "success": true,
    "isbn": "9781234567890",
    "is_valid": true,
    "message": "ISBN format valid"
}
```

### 4. Update ISBN untuk Buku Tertentu
**PUT** `/api/isbn/update-book/{bookId}`

Update ISBN untuk buku yang sudah ada di database.

#### Request Body:
```json
{
    "isbn": "9781234567890" // optional, jika tidak disediakan akan dicari otomatis
}
```

#### Response:
```json
{
    "success": true,
    "message": "ISBN berhasil diupdate",
    "book": {
        "id": 1,
        "title": "Judul Buku",
        "author": "Penulis",
        "isbn": "9781234567890",
        ...
    },
    "isbn": "9781234567890"
}
```

### 5. Bulk Update ISBN
**POST** `/api/isbn/bulk-update`

Update ISBN untuk multiple books yang sudah ada di database.

#### Request Body:
```json
{
    "book_ids": [1, 2, 3, 4, 5]
}
```

#### Response:
```json
{
    "success": true,
    "message": "Bulk update selesai. Berhasil: 4/5",
    "total_processed": 5,
    "success_count": 4,
    "results": [
        {
            "book_id": 1,
            "title": "Judul Buku 1",
            "success": true,
            "isbn": "9781234567890",
            "message": "ISBN berhasil diupdate"
        },
        {
            "book_id": 2,
            "title": "Judul Buku 2",
            "success": false,
            "isbn": null,
            "message": "Buku tidak ditemukan di Google Books"
        }
    ]
}
```

### 6. Get Books Tanpa ISBN
**GET** `/api/isbn/books-without-isbn`

Mendapatkan daftar buku yang belum memiliki ISBN.

#### Response:
```json
{
    "success": true,
    "count": 10,
    "books": [
        {
            "id": 1,
            "title": "Judul Buku 1",
            "author": "Penulis 1",
            "publisher": "Penerbit 1",
            "publication_year": 2023
        },
        {
            "id": 2,
            "title": "Judul Buku 2",
            "author": "Penulis 2",
            "publisher": "Penerbit 2",
            "publication_year": 2022
        }
    ]
}
```

## Contoh Penggunaan dengan cURL

### 1. Cari ISBN untuk satu buku:
```bash
curl -X POST http://localhost:8000/api/isbn/search \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Laskar Pelangi",
    "author": "Andrea Hirata"
  }'
```

### 2. Cari ISBN untuk multiple books:
```bash
curl -X POST http://localhost:8000/api/isbn/search-multiple \
  -H "Content-Type: application/json" \
  -d '{
    "books": [
      {
        "title": "Laskar Pelangi",
        "author": "Andrea Hirata"
      },
      {
        "title": "Sang Pemimpi",
        "author": "Andrea Hirata"
      }
    ]
  }'
```

### 3. Validasi ISBN:
```bash
curl -X POST http://localhost:8000/api/isbn/validate \
  -H "Content-Type: application/json" \
  -d '{
    "isbn": "9789793062792"
  }'
```

### 4. Update ISBN untuk buku:
```bash
curl -X PUT http://localhost:8000/api/isbn/update-book/1 \
  -H "Content-Type: application/json" \
  -d '{
    "isbn": "9789793062792"
  }'
```

### 5. Bulk update ISBN:
```bash
curl -X POST http://localhost:8000/api/isbn/bulk-update \
  -H "Content-Type: application/json" \
  -d '{
    "book_ids": [1, 2, 3, 4, 5]
  }'
```

### 6. Get books tanpa ISBN:
```bash
curl -X GET http://localhost:8000/api/isbn/books-without-isbn
```

## Contoh Penggunaan dengan JavaScript/Fetch

```javascript
// Cari ISBN untuk satu buku
async function searchIsbn(title, author = null) {
    const response = await fetch('/api/isbn/search', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            title: title,
            author: author
        })
    });
    
    return await response.json();
}

// Cari ISBN untuk multiple books
async function searchMultipleIsbn(books) {
    const response = await fetch('/api/isbn/search-multiple', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            books: books
        })
    });
    
    return await response.json();
}

// Bulk update ISBN
async function bulkUpdateIsbn(bookIds) {
    const response = await fetch('/api/isbn/bulk-update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            book_ids: bookIds
        })
    });
    
    return await response.json();
}

// Contoh penggunaan
searchIsbn('Laskar Pelangi', 'Andrea Hirata')
    .then(result => {
        if (result.success) {
            console.log('ISBN ditemukan:', result.isbn);
            console.log('Info buku:', result.book_info);
        } else {
            console.log('Error:', result.message);
        }
    });
```

## Error Handling

API akan mengembalikan response dengan format:

```json
{
    "success": false,
    "message": "Pesan error",
    "isbn": null,
    "book_info": null
}
```

### Kemungkinan Error Messages:
- "Gagal mengakses Google Books API"
- "Buku tidak ditemukan di Google Books"
- "ISBN tidak ditemukan untuk buku ini"
- "Terjadi kesalahan: [detail error]"
- "Format ISBN tidak valid"
- "Gagal menemukan ISBN: [detail]"

## Rate Limiting

- API menggunakan delay 200ms antara request untuk menghindari rate limiting Google Books API
- Untuk bulk operations, proses akan berjalan secara sequential dengan delay
- Maksimal 50 books per request untuk bulk operations

## Catatan Penting

1. **Google Books API**: API ini menggunakan Google Books API yang gratis dan tidak memerlukan API key
2. **Akurasi**: Hasil pencarian bergantung pada ketersediaan data di Google Books
3. **Delay**: Ada delay 200ms antara request untuk menghindari rate limiting
4. **Validasi**: ISBN yang ditemukan akan divalidasi formatnya sebelum disimpan
5. **Uniqueness**: ISBN harus unique di database (tidak boleh duplikat)

## Troubleshooting

### Jika API tidak berfungsi:
1. Pastikan server Laravel berjalan
2. Pastikan koneksi internet tersedia
3. Check log Laravel untuk error details
4. Pastikan Google Books API dapat diakses

### Jika ISBN tidak ditemukan:
1. Coba dengan judul yang lebih spesifik
2. Pastikan penulis ditulis dengan benar
3. Coba tanpa penulis (hanya judul)
4. Periksa apakah buku ada di Google Books

