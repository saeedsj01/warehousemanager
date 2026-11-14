$ErrorActionPreference = 'Stop'
Write-Host 'Warehouse Manager - Laravel setup' -ForegroundColor Cyan
if (-not (Get-Command composer -ErrorAction SilentlyContinue)) { throw 'Composer پیدا نشد. ابتدا Composer را نصب و ترمینال را دوباره باز کنید.' }
if (-not (Test-Path '.env')) { Copy-Item '.env.example' '.env' }
if (-not (Test-Path 'database/database.sqlite')) { New-Item -ItemType File -Path 'database/database.sqlite' | Out-Null }
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
Write-Host 'نصب اولیه تمام شد. دامنه پروژه را در Laravel Herd باز کنید.' -ForegroundColor Green
Write-Host 'ورود اولیه: admin / ChangeMe123! (پس از ورود رمز را تغییر دهید)' -ForegroundColor Yellow
