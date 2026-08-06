@echo off
REM Cost to Cost Foods — POS Silent Print
REM Edit the URL below if your domain differs.
set "POS_URL=https://sathyasai.duckdns.org/admin/pos?silent_print=1"

set "CHROME="
if exist "%ProgramFiles%\Google\Chrome\Application\chrome.exe" set "CHROME=%ProgramFiles%\Google\Chrome\Application\chrome.exe"
if exist "%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe" set "CHROME=%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe"
if exist "%LocalAppData%\Google\Chrome\Application\chrome.exe" set "CHROME=%LocalAppData%\Google\Chrome\Application\chrome.exe"

if "%CHROME%"=="" (
  echo Chrome not found. Install Google Chrome first.
  pause
  exit /b 1
)

start "" "%CHROME%" --kiosk-printing --app="%POS_URL%"
