@echo off
title FoodNanny Print Agent Setup v3
cd /d "%~dp0"
echo.
echo ========================================
echo   FoodNanny Local Print Agent SETUP v3
echo ========================================
echo.

set "INSTALL=%LOCALAPPDATA%\FoodNanny\LocalPrintAgent"
if not exist "%INSTALL%" mkdir "%INSTALL%"
set "AGENT=%INSTALL%\Start-LocalPrintAgent-v2.ps1"

echo [1/4] Killing old agents on port 1811...
powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "$ErrorActionPreference='SilentlyContinue';" ^
  "1..6 | ForEach-Object {" ^
  "  Get-NetTCPConnection -LocalPort 1811 -EA SilentlyContinue | ForEach-Object { Stop-Process -Id $_.OwningProcess -Force };" ^
  "  Get-CimInstance Win32_Process | Where-Object { $_.Name -match 'powershell|pwsh|python' -and $_.CommandLine -match 'LocalPrintAgent|print_agent|Start-LocalPrintAgent' } | ForEach-Object { if ($_.ProcessId -ne $PID) { Stop-Process -Id $_.ProcessId -Force } };" ^
  "  Start-Sleep -Seconds 1" ^
  "};" ^
  "netsh http delete urlacl url=http://127.0.0.1:1811/ 2>$null | Out-Null;" ^
  "netsh http delete urlacl url=http://+:1811/ 2>$null | Out-Null;" ^
  "Start-Sleep -Seconds 2"

echo [2/4] Downloading fresh agent...
powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "Invoke-WebRequest -UseBasicParsing -Uri 'https://sathyasai.duckdns.org/local-print-agent/Start-LocalPrintAgent-v2.ps1?v=20260809i' -OutFile '%AGENT%'"
if not exist "%AGENT%" (
  echo ERROR: Download failed.
  pause
  exit /b 1
)

set "STARTUP=%APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup"
(
  echo @echo off
  echo powershell -ExecutionPolicy Bypass -WindowStyle Minimized -File "%AGENT%"
) > "%STARTUP%\FoodNanny-LocalPrintAgent.cmd"

echo [3/4] Starting agent v3 (TCP mode - no URL conflict)...
start "FoodNanny Local Print Agent v3" powershell -NoProfile -ExecutionPolicy Bypass -NoExit -File "%AGENT%"

echo [4/4] Waiting for Ready v3...
set OK=0
for /L %%i in (1,1,15) do (
  timeout /t 1 /nobreak >nul
  powershell -NoProfile -ExecutionPolicy Bypass -Command "try { $h=Invoke-RestMethod http://127.0.0.1:1811/health -TimeoutSec 1; if ($h.ok -and [int]$h.version -ge 3) { exit 0 } else { exit 2 } } catch { exit 1 }"
  if not errorlevel 1 (
    set OK=1
    goto :done
  )
)

:done
echo.
if "%OK%"=="1" (
  echo ========================================
  echo   SUCCESS: Agent Ready v3 + USB
  echo ========================================
  echo.
  echo Set Counter thermal Windows name to:
  echo   RP3200 lite bill
  echo.
  echo Then POS: Ctrl+Shift+R and test print.
) else (
  echo ========================================
  echo   FAILED
  echo ========================================
  echo 1. Download Kill-Old-Agents.bat and run it
  echo 2. Or Task Manager - End ALL "Windows PowerShell"
  echo 3. Run this SETUP again
)
echo.
pause
