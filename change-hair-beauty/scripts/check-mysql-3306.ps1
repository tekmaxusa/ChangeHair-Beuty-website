# Check what is listening on TCP port 3306 (common cause of XAMPP "MySQL shutdown unexpectedly")
Set-StrictMode -Version Latest
$ErrorActionPreference = 'Continue'

Write-Host "`n=== Port 3306 (XAMPP MySQL default) ===" -ForegroundColor Cyan
$lines = netstat -ano 2>$null | Select-String ':3306\s'
if (-not $lines) {
    Write-Host "Nothing LISTENING on 3306 (OK for XAMPP MySQL)." -ForegroundColor Green
    exit 0
}

$lines | ForEach-Object { Write-Host $_.Line }

Write-Host "`nPIDs associated with LISTEN on 3306:" -ForegroundColor Yellow
$pids = @()
foreach ($line in $lines) {
    if ($line -match '\sLISTENING\s+(\d+)\s*$') {
        $pids += [int]$Matches[1]
    }
}
$pids = $pids | Select-Object -Unique
foreach ($p in $pids) {
    $pname = (Get-CimInstance Win32_Process -Filter "ProcessId=$p" -ErrorAction SilentlyContinue).Name
    if (-not $pname) { $pname = '(name unavailable — try: tasklist /FI "PID eq ' + $p + '")' }
    Write-Host "  PID $p  ->  $pname"
}

Write-Host "`nIf Docker MySQL is published on 3306, stop it before starting XAMPP MySQL:" -ForegroundColor Yellow
Write-Host "  docker ps --format `"table {{.Names}}\t{{.Ports}}`"" -ForegroundColor Gray
Write-Host "  docker stop <container_name>`n" -ForegroundColor Gray
