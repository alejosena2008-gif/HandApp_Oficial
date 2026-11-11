@echo off
title HandApp - Servidor Django + Mascotas 3D + IA LSC
cd /d "%~dp0src\main\PYTHON"
echo Iniciando HandApp en Django (http://localhost:8081) ...
start "" "http://localhost:8081"
"C:\Users\ACER\AppData\Local\Python\pythoncore-3.12-64\python.exe" manage.py runserver 0.0.0.0:8081
pause
