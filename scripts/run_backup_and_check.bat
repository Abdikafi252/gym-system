@echo off
cd /d C:\xampp\htdocs\Gym-System
call scripts\backup_db.bat
php api\backup_health_check.php
