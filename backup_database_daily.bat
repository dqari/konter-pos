@echo off
set "APP_DIR=%~dp0"
cd /d "%APP_DIR%"
"C:\xampp\php\php.exe" spark backup:database
