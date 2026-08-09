# One-click setup: stop OLD agent, install v2 to Startup, start v2 (USB + network).
# Right-click → Run with PowerShell  OR:
#   powershell -ExecutionPolicy Bypass -File .\Setup-LocalPrintAgent-v2.ps1

$ErrorActionPreference = 'Continue'
$ListenPort = 1811

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  FoodNanny Local Print Agent — SETUP v2" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

function Stop-ListenersOnPort {
    param([int]$Port)
    Write-Host "Stopping anything on port $Port ..." -ForegroundColor Yellow
    try {
        $conns = Get-NetTCPConnection -LocalPort $Port -ErrorAction SilentlyContinue
        foreach ($c in $conns) {
            if ($c.OwningProcess -and $c.OwningProcess -gt 0) {
                Write-Host "  Killing PID $($c.OwningProcess)"
                Stop-Process -Id $c.OwningProcess -Force -ErrorAction SilentlyContinue
            }
        }
    } catch {}

    try {
        $procs = Get-CimInstance Win32_Process -ErrorAction SilentlyContinue |
            Where-Object {
                $_.Name -match '^(powershell|pwsh|python|pythonw)\.exe$' -and
                $_.CommandLine -and
                ($_.CommandLine -match 'LocalPrintAgent|print_agent|Start-LocalPrintAgent|1811')
            }
        foreach ($p in $procs) {
            if ($p.ProcessId -ne $PID) {
                Write-Host "  Killing $($p.Name) PID $($p.ProcessId)"
                Stop-Process -Id $p.ProcessId -Force -ErrorAction SilentlyContinue
            }
        }
    } catch {}

    Start-Sleep -Seconds 1
}

Stop-ListenersOnPort -Port $ListenPort

# Prefer agent script next to this setup file
$here = Split-Path -Parent $MyInvocation.MyCommand.Path
$srcAgent = Join-Path $here 'Start-LocalPrintAgent-v2.ps1'
if (-not (Test-Path $srcAgent)) {
    $srcAgent = Join-Path $here 'Start-LocalPrintAgent.ps1'
}
if (-not (Test-Path $srcAgent)) {
    Write-Host "ERROR: Start-LocalPrintAgent-v2.ps1 not found next to this setup script." -ForegroundColor Red
    Write-Host "Download both files from the setup page into the same folder, then run Setup again." -ForegroundColor Red
    Read-Host "Press Enter to close"
    exit 1
}

$installDir = Join-Path $env:LOCALAPPDATA 'FoodNanny\LocalPrintAgent'
New-Item -ItemType Directory -Force -Path $installDir | Out-Null
$destAgent = Join-Path $installDir 'Start-LocalPrintAgent-v2.ps1'
Copy-Item -Force -Path $srcAgent -Destination $destAgent
Write-Host "Installed agent to: $destAgent" -ForegroundColor Green

# Replace Startup shortcut so login uses v2 (not old Downloads copy)
$startup = [Environment]::GetFolderPath('Startup')
$startupCmd = Join-Path $startup 'FoodNanny-LocalPrintAgent.cmd'
"@echo off`r`npowershell -ExecutionPolicy Bypass -WindowStyle Minimized -File `"$destAgent`"`r`n" |
    Set-Content -Path $startupCmd -Encoding ASCII
Write-Host "Startup updated: $startupCmd" -ForegroundColor Green

Write-Host ""
Write-Host "Starting Local Print Agent v2 ..." -ForegroundColor Cyan
Write-Host "Keep the next window open. It must say: Local Print Agent v2" -ForegroundColor Yellow
Write-Host ""

Start-Process -FilePath "powershell.exe" -ArgumentList @(
    '-ExecutionPolicy', 'Bypass',
    '-NoExit',
    '-File', $destAgent
)

Start-Sleep -Seconds 2
try {
    $health = Invoke-RestMethod -Uri "http://127.0.0.1:$ListenPort/health" -TimeoutSec 3
    if ($health.ok -and [int]$health.version -ge 2 -and ($health.features -contains 'windows')) {
        Write-Host "SUCCESS: Agent Ready v2 + USB" -ForegroundColor Green
        Write-Host "Go back to the browser → Check Agent → should say Ready v2 + USB" -ForegroundColor Green
        Write-Host "Then open POS (Ctrl+Shift+R) and test Confirm & Print." -ForegroundColor Green
    } else {
        Write-Host "Agent started but health is not v2+USB yet. Check the green agent window." -ForegroundColor Yellow
        Write-Host ($health | ConvertTo-Json -Compress)
    }
} catch {
    Write-Host "Could not confirm health yet. Look at the agent window — it must say v2." -ForegroundColor Yellow
}

Write-Host ""
Read-Host "Press Enter to close this setup window"
