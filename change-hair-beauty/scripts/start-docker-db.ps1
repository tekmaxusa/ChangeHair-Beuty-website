# Start MySQL container only (XAMPP + Docker DB workflow)
Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
# scripts/ → repo root (change-hair-beauty)
$root = Split-Path -Parent $PSScriptRoot
if (-not (Test-Path (Join-Path $root 'docker-compose.yml'))) {
    throw "docker-compose.yml not found at: $root"
}
Set-Location $root
docker compose up db phpmyadmin -d
Write-Host 'MySQL + phpMyAdmin (Docker) are running.'
Write-Host '  From PC/XAMPP PHP: DB_HOST=127.0.0.1  DB_PORT=3307  (see .env.xampp.example)'
Write-Host '  phpMyAdmin: http://127.0.0.1:8081  (login: salon_user / salon_secret or root / DB_ROOT_PASSWORD)'
