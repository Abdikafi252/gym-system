@echo off
setlocal ENABLEDELAYEDEXPANSION

echo [INFO] Running backup restore dry-run check...
set DBDUMP=%~dp0..\DATABASE FILE\gymnsb_backup.sql

if not exist "%DBDUMP%" (
  echo [ERROR] Backup SQL file not found: %DBDUMP%
  exit /b 1
)

echo [INFO] Creating temporary database gymnsb_restore_test...
"C:\xampp\mysql\bin\mysql.exe" -u root -e "DROP DATABASE IF EXISTS gymnsb_restore_test; CREATE DATABASE gymnsb_restore_test;"
if errorlevel 1 (
  echo [ERROR] Could not create temporary database.
  exit /b 1
)

echo [INFO] Importing backup into temporary database...
"C:\xampp\mysql\bin\mysql.exe" -u root gymnsb_restore_test < "%DBDUMP%"
if errorlevel 1 (
  echo [ERROR] Restore dry-run failed during import.
  exit /b 1
)

echo [INFO] Verifying key tables...
"C:\xampp\mysql\bin\mysql.exe" -u root -e "USE gymnsb_restore_test; SHOW TABLES LIKE 'members'; SHOW TABLES LIKE 'payment_history'; SHOW TABLES LIKE 'attendance';"

echo [INFO] Cleaning up temporary database...
"C:\xampp\mysql\bin\mysql.exe" -u root -e "DROP DATABASE IF EXISTS gymnsb_restore_test;"

echo [SUCCESS] Restore dry-run check completed.
exit /b 0
