@echo off
title Jalankan Pengujian E2E FoodFlow
echo ========================================================
echo  MENJALANKAN SELURUH TEST E2E FOODFLOW (PERTEMUAN 9)
echo ========================================================
echo.
echo Pastikan server Laravel sedang berjalan (php artisan serve pada port 8000)
echo.
python -m pytest tests/e2e -v
echo.
echo ========================================================
echo  PENGUJIAN SELESAI!
echo ========================================================
pause
