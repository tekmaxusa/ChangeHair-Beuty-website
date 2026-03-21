# Docker phpMyAdmin connecting to MySQL on XAMPP (default http://localhost:8082)
Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

Write-Host "Ensure XAMPP MySQL is running (port 3306)." -ForegroundColor Yellow
docker compose -f docker-compose.xampp-phpmyadmin.yml up -d

$pmaPort = if ($env:PMA_XAMPP_PORT) { $env:PMA_XAMPP_PORT } else { '8082' }
Write-Host ""
Write-Host "phpMyAdmin (XAMPP DB): http://localhost:$pmaPort" -ForegroundColor Green
Write-Host "Login e.g. salon_user / salon_secret" -ForegroundColor Gray
