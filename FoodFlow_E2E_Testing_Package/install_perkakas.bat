@echo off
title Install Perkakas Testing FoodFlow E2E
echo ========================================================
echo  MENGINSTALL PERKAKAS E2E PLAYWRIGHT UNTUK FOODFLOW
echo ========================================================
echo.
python -m pip install -r requirements.txt
echo.
echo Mengunduh browser Chromium Playwright...
python -m playwright install chromium
echo.
echo ========================================================
echo  SELESAI! PERKAKAS TESTING SIAP DIGUNAKAN.
echo ========================================================
pause
