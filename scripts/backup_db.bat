@echo off
setlocal
set DB_NAME=gymnsb
set DB_USER=root
set DB_PASS=
set BACKUP_DIR=C:\xampp\htdocs\Gym-System\DATABASE FILE\backups

if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

for /f %%i in ('powershell -NoProfile -Command "Get-Date -Format yyyyMMdd-HHmmss"') do set TS=%%i
set FILE=%BACKUP_DIR%\gymnsb-%TS%.sql

"C:\xampp\mysql\bin\mysqldump.exe" -u%DB_USER% %DB_NAME% > "%FILE%"
if %ERRORLEVEL% NEQ 0 (
  echo BACKUP FAILED %date% %time%>> "%BACKUP_DIR%\backup-errors.log"
  exit /b 1
)

echo BACKUP OK %date% %time% - %FILE%>> "%BACKUP_DIR%\backup.log"
exit /b 0
