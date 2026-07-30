@echo off
REM ===== Start the Invoice System local server =====
cd /d "%~dp0"

REM Use WAMP's PHP directly so PATH doesn't matter.
set "PHP=C:\wamp\bin\php\php8.3.28\php.exe"
if not exist "%PHP%" set "PHP=php"

echo.
echo   Invoice System sedang berjalan di:  http://127.0.0.1:8000
echo   (Biarkan tetingkap ini terbuka. Tekan Ctrl+C untuk berhenti.)
echo.

start "" http://127.0.0.1:8000
"%PHP%" -S 127.0.0.1:8000 -t public server.php
pause
