# Panduan Sistem Authentication - Perpustakaan MTs Muhamadyah

## Overview
Sistem authentication telah berhasil diintegrasikan ke dalam sistem perpustakaan MTs Muhamadyah. Sistem ini memungkinkan pengguna untuk login, register, dan logout dengan aman.

## Fitur Authentication

### 1. Login
- **URL**: `/login`
- **Method**: GET (form) / POST (submit)
- **Fitur**:
  - Validasi email dan password
  - Remember me functionality
  - Redirect otomatis setelah login berhasil
  - Pesan error dalam bahasa Indonesia

### 2. Register
- **URL**: `/register`
- **Method**: GET (form) / POST (submit)
- **Fitur**:
  - Validasi nama, email, dan password
  - Konfirmasi password
  - Auto-login setelah registrasi berhasil
  - Validasi email unik

### 3. Logout
- **URL**: `/logout`
- **Method**: POST
- **Fitur**:
  - Session invalidation
  - Redirect ke halaman login
  - CSRF protection

## User Default

### Administrator
- **Email**: admin@mtsmuhamadyah.sch.id
- **Password**: admin123
- **Role**: Administrator

### Petugas Perpustakaan
- **Email**: petugas@mtsmuhamadyah.sch.id
- **Password**: petugas123
- **Role**: Petugas

## Keamanan

### Middleware Protection
- Semua route perpustakaan dilindungi dengan middleware `auth`
- Route login/register hanya bisa diakses oleh guest
- CSRF protection pada semua form

### Password Security
- Password di-hash menggunakan Laravel Hash
- Minimum 8 karakter untuk password
- Konfirmasi password wajib

## Struktur File

### Controllers
- `app/Http/Controllers/AuthController.php` - Controller untuk authentication

### Views
- `resources/views/auth/login.blade.php` - Form login
- `resources/views/auth/register.blade.php` - Form register
- `resources/views/layouts/app.blade.php` - Layout dengan user dropdown

### Routes
- `routes/web.php` - Route authentication dan protected routes

### Seeders
- `database/seeders/AdminUserSeeder.php` - Seeder untuk user default

### Language
- `resources/lang/id/auth.php` - Pesan error dalam bahasa Indonesia

## Cara Menggunakan

### 1. Akses Sistem
1. Buka browser dan akses `http://localhost:8000`
2. Sistem akan redirect ke halaman login
3. Masukkan email dan password yang valid

### 2. Login sebagai Admin
```
Email: admin@mtsmuhamadyah.sch.id
Password: admin123
```

### 3. Login sebagai Petugas
```
Email: petugas@mtsmuhamadyah.sch.id
Password: petugas123
```

### 4. Register User Baru
1. Klik "Daftar di sini" di halaman login
2. Isi form registrasi
3. Sistem akan auto-login setelah registrasi

### 5. Logout
1. Klik dropdown user di navbar
2. Klik "Logout"

## Troubleshooting

### Error "Email atau password yang Anda masukkan salah"
- Pastikan email dan password benar
- Cek apakah user sudah terdaftar di database

### Error "Email sudah digunakan"
- Gunakan email yang berbeda
- Atau login dengan akun yang sudah ada

### Redirect Loop
- Pastikan middleware auth sudah dikonfigurasi dengan benar
- Cek route configuration

## Development Notes

### Menambah User Baru
```bash
php artisan tinker
User::create([
    'name' => 'Nama User',
    'email' => 'email@example.com',
    'password' => Hash::make('password123')
]);
```

### Reset Password User
```bash
php artisan tinker
$user = User::where('email', 'email@example.com')->first();
$user->password = Hash::make('passwordbaru');
$user->save();
```

### Menjalankan Seeder
```bash
php artisan db:seed --class=AdminUserSeeder
```

## Next Steps

1. **Role Management**: Implementasi role-based access control
2. **Password Reset**: Fitur reset password via email
3. **Profile Management**: Edit profile user
4. **Activity Log**: Log aktivitas user
5. **Two-Factor Authentication**: Keamanan tambahan

## Support

Untuk pertanyaan atau masalah terkait sistem authentication, silakan hubungi tim development.

