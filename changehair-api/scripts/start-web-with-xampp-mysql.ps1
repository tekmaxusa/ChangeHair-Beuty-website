# Docker web (PHP) + XAMPP MySQL — does not start a MySQL container
Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

Write-Host "Ensure XAMPP MySQL is running on port 3306." -ForegroundColor Yellow
Write-Host "If not set up yet: import sql/xampp_complete_setup.sql (or xampp_db_user.sql then schema.sql) in phpMyAdmin."
Write-Host ""

docker compose -f docker-compose.yml -f docker-compose.xampp-mysql.yml up -d --build --no-deps web

Write-Host ""
Write-Host "App: http://localhost:8080" -ForegroundColor Green
Write-Host "(Do not run Docker MySQL on 3307 at the same time if 3306 is reserved for XAMPP only.)"
