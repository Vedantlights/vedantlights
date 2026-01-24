@echo off
echo Close Cursor and any Git tools first, then run this script.
echo.
pause
powershell -ExecutionPolicy Bypass -File "%~dp0remove-react-app-git.ps1"
pause
