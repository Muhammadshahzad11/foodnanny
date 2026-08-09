@echo off
title Kill FoodNanny Print Agents
echo Closing all FoodNanny / Print Agent PowerShell processes...
powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "$ErrorActionPreference='SilentlyContinue';" ^
  "Get-NetTCPConnection -LocalPort 1811 -EA SilentlyContinue | ForEach-Object { Stop-Process -Id $_.OwningProcess -Force };" ^
  "Get-CimInstance Win32_Process | Where-Object { $_.Name -match 'powershell|pwsh|python' -and $_.CommandLine -match 'LocalPrintAgent|print_agent|Start-LocalPrintAgent|1811' } | ForEach-Object { Stop-Process -Id $_.ProcessId -Force };" ^
  "Get-Process powershell,pwsh -EA SilentlyContinue | Where-Object { $_.MainWindowTitle -match 'Print Agent|FoodNanny|Local Print' } | Stop-Process -Force;" ^
  "netsh http delete urlacl url=http://127.0.0.1:1811/ | Out-Null;" ^
  "netsh http delete urlacl url=http://+:1811/ | Out-Null;" ^
  "Start-Sleep -Seconds 1;" ^
  "Write-Host 'Done. Port 1811 should be free now.' -ForegroundColor Green"
echo.
pause
