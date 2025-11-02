# Troubleshooting Error Sync Google Sheets

## Error: "Unexpected token '<', "<!DOCTYPE "... is not valid JSON"

Error ini terjadi karena server mengembalikan HTML error page bukan JSON response.

## Langkah Perbaikan

### 1. Pastikan Konfigurasi di File `.env`

Tambahkan konfigurasi berikut ke file `.env` (di root project):

```env
# Google Sheets API Configuration
GOOGLE_SHEETS_SPREADSHEET_ID=2PACX-1vTV2yHxMdFkv1HIQDuKsceg8QhBoGCqYhy1oBkdzgsWa7jgYt8ophyPHSfa5ukpgRUw3h5Pw_T-0JED
GOOGLE_SHEETS_SHEET_ID_MURID=0
GOOGLE_SHEETS_SHEET_ID_GURU=336865292
GOOGLE_SHEETS_SHEET_ID_BUKU=404883502
GOOGLE_SHEETS_CREDENTIALS_PATH=google-credentials.json
GOOGLE_SHEETS_AUTO_SYNC=true
```

**Catatan Penting:**
- Spreadsheet ID di atas adalah contoh dari URL published. Anda perlu mengambil Spreadsheet ID yang sebenarnya dari Google Spreadsheet Anda.
- Cara mendapatkan Spreadsheet ID:
  1. Buka Google Spreadsheet
  2. Lihat URL: `https://docs.google.com/spreadsheets/d/SPREADSHEET_ID_HERE/edit`
  3. Copy bagian `SPREADSHEET_ID_HERE`

### 2. Pastikan File Credentials Ada

File `storage/app/google-credentials.json` harus ada dan valid.

### 3. Pastikan Service Account Punya Akses

1. Buka file `storage/app/google-credentials.json`
2. Cari field `"client_email"` (contoh: `perpustakan-sheets-service@...`)
3. Buka Google Spreadsheet
4. Klik **Share** (Bagikan)
5. Tambahkan email service account tersebut
6. Berikan akses **Editor**

### 4. Clear Cache

Setelah mengubah `.env`, jalankan command:

```bash
php artisan config:clear
php artisan cache:clear
```

### 5. Check Konfigurasi

Jalankan command untuk mengecek konfigurasi:

```bash
php artisan sheets:check-config
```

Command ini akan menampilkan:
- ✅ Spreadsheet ID yang dikonfigurasi
- ✅ Status file credentials
- ✅ Email service account
- ✅ Daftar sheet yang tersedia
- ✅ Status koneksi ke Google Sheets API

### 6. Cek Log Error

Jika masih error, cek file log:
- `storage/logs/laravel.log`

Error akan tertulis di log dengan detail lengkap.

## Masalah Umum

### Error: "GOOGLE_SHEETS_SPREADSHEET_ID belum dikonfigurasi"
**Solusi:** Tambahkan `GOOGLE_SHEETS_SPREADSHEET_ID` di file `.env`

### Error: "File credentials tidak ditemukan"
**Solusi:** Pastikan file `storage/app/google-credentials.json` ada

### Error: "Requested entity was not found" (404)
**Solusi:** 
1. Pastikan Spreadsheet ID benar
2. Pastikan service account sudah diberikan akses ke spreadsheet
3. Pastikan nama sheet benar (case-sensitive)

### Error: "Permission denied"
**Solusi:** Berikan akses **Editor** (bukan Viewer) ke service account

## Testing

Setelah memperbaiki konfigurasi:
1. Reload halaman browser (Ctrl+F5)
2. Coba sync lagi
3. Jika masih error, cek console browser (F12) dan log Laravel

