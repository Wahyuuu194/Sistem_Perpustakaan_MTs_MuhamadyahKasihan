@echo off
echo ========================================
echo Setup Database Perpustakaan MTs Muhamadyah
echo ========================================
echo.

echo 1. Membuat file .env...
if not exist .env (
    copy .env.example .env
    echo File .env berhasil dibuat
) else (
    echo File .env sudah ada
)

echo.
echo 2. Generate App Key...
php artisan key:generate

echo.
echo 3. Clear cache...
php artisan config:clear
php artisan cache:clear

echo.
echo ========================================
echo Setup selesai!
echo ========================================
echo.
echo Langkah selanjutnya:
echo 1. Buka MySQL Workbench
echo 2. Jalankan script database/library_system.sql
echo 3. Jalankan: php artisan serve
echo.
pause
