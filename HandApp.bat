@echo off
start /min "" "C:\Users\ACER\AppData\Local\Python\pythoncore-3.14-64\python.exe" -m http.server 8080 --directory "C:\Users\ACER\Desktop\Prototipo HandApp"
timeout /t 2 /nobreak >nul
start "" "http://localhost:8080/HTML/traductor.html"
exit