# Guide + start Docker phpMyAdmin to browse XAMPP MySQL in the browser (:8082)
Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

Write-Host ""
Write-Host "=== Database on XAMPP, view in Docker ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "1) Start MySQL in XAMPP Control Panel." -ForegroundColor Yellow
Write-Host "2) In XAMPP phpMyAdmin (as root): Import file:" -ForegroundColor Yellow
Write-Host "   $root\sql\xampp_complete_setup.sql" -ForegroundColor Gray
Write-Host "3) Starting Docker phpMyAdmin (port 8082)..." -ForegroundColor Yellow
Write-Host ""

& docker compose -f docker-compose.xampp-phpmyadmin.yml up -d

$pmaPort = if ($env:PMA_XAMPP_PORT) { $env:PMA_XAMPP_PORT } else { '8082' }
Write-Host ""
Write-Host "Open in browser: http://localhost:$pmaPort" -ForegroundColor Green
Write-Host "Login: salon_user / salon_secret" -ForegroundColor Green
Write-Host "Docs: docs\XAMPP-DOCKER-DATABASE.md" -ForegroundColor Gray
Write-Host ""
