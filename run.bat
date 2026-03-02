@echo off
cd /d "%~dp0"
echo Starting Laravel at http://localhost:8000
php -S localhost:8000 -t public
pause