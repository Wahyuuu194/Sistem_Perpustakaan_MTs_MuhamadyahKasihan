Write-Host "========================================" -ForegroundColor Green
Write-Host "Setup Database Perpustakaan MTs Muhamadyah" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

Write-Host "1. Membuat file .env..." -ForegroundColor Yellow
if (-not (Test-Path ".env")) {
    if (Test-Path ".env.example") {
        Copy-Item ".env.example" ".env"
        Write-Host "File .env berhasil dibuat" -ForegroundColor Green
    } else {
        Write-Host "File .env.example tidak ditemukan" -ForegroundColor Red
        Write-Host "Buat file .env manual dengan konfigurasi database" -ForegroundColor Yellow
    }
} else {
    Write-Host "File .env sudah ada" -ForegroundColor Green
}

Write-Host ""
Write-Host "2. Generate App Key..." -ForegroundColor Yellow
php artisan key:generate

Write-Host ""
Write-Host "3. Clear cache..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "Setup selesai!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Langkah selanjutnya:" -ForegroundColor Cyan
Write-Host "1. Buka MySQL Workbench" -ForegroundColor White
Write-Host "2. Jalankan script database/library_system.sql" -ForegroundColor White
Write-Host "3. Jalankan: php artisan serve" -ForegroundColor White
Write-Host ""
Write-Host "Tekan Enter untuk melanjutkan..." -ForegroundColor Yellow
Read-Host
