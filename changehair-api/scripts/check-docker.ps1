# Check Docker services and which URLs to use
Set-StrictMode -Version Latest
$ErrorActionPreference = 'Continue'
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

Write-Host ""
Write-Host "Change Hair & Beauty — Docker check" -ForegroundColor Cyan
Write-Host "Folder: $root"
Write-Host ""

$dockerOk = $false
try {
    docker version 2>$null | Out-Null
    if ($LASTEXITCODE -eq 0) { $dockerOk = $true }
} catch {}

if (-not $dockerOk) {
    Write-Host "Docker CLI / engine: NOT AVAILABLE — open Docker Desktop and try again." -ForegroundColor Red
    exit 1
}

Write-Host "docker compose ps:" -ForegroundColor Yellow
docker compose ps
Write-Host ""
Write-Host "Expected URLs (default ports):" -ForegroundColor Green
Write-Host "  App:        http://localhost:8080"
Write-Host "  Merchant:   http://localhost:8080/admin/login  (dashboard /admin/, bookings, users)"
Write-Host "  status+DB:  http://localhost:8080/status.php"
Write-Host "  phpMyAdmin: http://localhost:8081  (run `docker compose up phpmyadmin -d` if missing from ps)"
Write-Host "  MySQL host: 127.0.0.1  port 3307  (from host tools)"
Write-Host ""
Write-Host "Not working?  docker compose up -d" -ForegroundColor DarkGray
