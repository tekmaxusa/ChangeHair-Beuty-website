# I-start ang MySQL container lang (XAMPP + Docker DB)
Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
# scripts/ → repo root (change-hair-beauty)
$root = Split-Path -Parent $PSScriptRoot
if (-not (Test-Path (Join-Path $root 'docker-compose.yml'))) {
    throw "Hindi mahanap ang docker-compose.yml sa: $root"
}
Set-Location $root
docker compose up db -d
Write-Host 'MySQL (Docker) ay tumatakbo. DB_HOST=127.0.0.1 DB_PORT=3307 sa .env (gamitin ang .env.xampp.example bilang base).'
