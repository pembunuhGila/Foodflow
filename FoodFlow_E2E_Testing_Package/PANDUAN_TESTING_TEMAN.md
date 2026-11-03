# PANDUAN PENGUJIAN OTOMATIS END-TO-END (E2E) - FOODFLOW
**Mata Kuliah:** Penjaminan Mutu Perangkat Lunak (PMPL) - Pertemuan 9  
**Sistem Uji:** FoodFlow (Manajemen Stock & Gizi MBG dan Catering)  
**Kelompok:** 1 (SIB-3A)  

---

## 📁 Struktur Folder Paket Testing

```text
FoodFlow_E2E_Testing_Package/
│
├── pytest.ini                     # Konfigurasi runner pytest & Playwright
├── requirements.txt               # Daftar pustaka Python yang dibutuhkan
├── install_perkakas.bat           # Skrip otomatis 1-klik untuk install library & Chromium
├── jalankan_semua_test.bat        # Skrip otomatis 1-klik untuk menjalankan seluruh test
├── PANDUAN_TESTING_TEMAN.md       # Berkas panduan ini
│
└── tests/
    └── e2e/
        ├── conftest.py            # Konfigurasi global, akun demo, helper login, & fixture API
        ├── test_01_login.py       # Pengujian Lab 1 (Dasar E2E) & Lab 2 (Resilient Locators)
        ├── test_02_material_requests.py # Pengujian Lab 4 (3 Lapisan), Lab 5 (Precondition API),
        │                                 Lab 6 (E2E Penuh), dan Lab 7 (Artefak Trace/Video)
        └── test_03_inventory.py   # Pengujian Lab 3 (Sleep vs Auto-waiting) & Lab 6 (Hak Akses)
```

---

## 🚀 Langkah Cepat Menjalankan Pengujian (Untuk Teman Kelompok)

### Langkah 1: Pastikan Web Server Laravel Berjalan
Buka terminal / Command Prompt di direktori project Laravel FoodFlow, lalu nyalakan server lokal:
```bash
php artisan serve --port=8000
```
*(Pastikan aplikasi bisa dibuka di browser pada alamat `http://127.0.0.1:8000`)*.

---

### Langkah 2: Install Perkakas Pengujian (Hanya Perlu Sekali Saja)
Dobel-klik file **`install_perkakas.bat`**  
*Atau jalankan perintah ini di Git Bash / Terminal:*
```bash
python -m pip install -r requirements.txt
python -m playwright install chromium
```

---

### Langkah 3: Menjalankan Pengujian

#### Cara A: Jalankan Semua Sekaligus
Dobel-klik file **`jalankan_semua_test.bat`**  
*Atau via terminal:*
```bash
python -m pytest tests/e2e -v
```

#### Cara B: Jalankan Spesifik per Lab / per Nomor Soal
Jika ingin menguji satu nomor soal saja untuk keperluan screenshot laporan Word:

- **Lab 1 (Login Berhasil):**
  ```bash
  python -m pytest tests/e2e/test_01_login.py -k "test_lab1_soal1_login_berhasil" -v
  ```

- **Lab 3 (Uji Auto-Waiting vs Sleep):**
  ```bash
  python -m pytest tests/e2e/test_03_inventory.py -k "test_lab3_soal3_auto_waiting_expect" -v
  ```

- **Lab 4 (Assertion Tiga Lapisan BR-05):**
  ```bash
  python -m pytest tests/e2e/test_02_material_requests.py -k "test_lab4_soal3_assertion_lapisan_data_reload" -v
  ```

- **Lab 5 (Precondition Lewat API):**
  ```bash
  python -m pytest tests/e2e/test_02_material_requests.py -k "test_lab5_soal1_panggil_fixture_api" -v
  ```

- **Lab 6 (End-to-End Penuh BR-02 Mode Headed - Browser Terlihat):**
  ```bash
  python -m pytest tests/e2e/test_02_material_requests.py -k "test_lab6_soal4_mode_headed_dan_headless" --headed --slowmo 500 -v
  ```

- **Lab 7 (Melihat Trace Viewer Playwright):**
  ```bash
  # 1. Jalankan test gagal untuk memicu pembuatan trace:
  python -m pytest tests/e2e/test_02_material_requests.py -k "test_lab7_soal1_rusak_assertion_hasilkan_artefak" --tracing=retain-on-failure --video=retain-on-failure -v

  # 2. Buka GUI interaktif trace viewer:
  python -m playwright show-trace $(ls test-results/*/trace.zip | head -n 1)
  ```

---

## 👥 Akun Uji Demo yang Digunakan (Otomatis dari conftest.py)

| Role Akun | Email | Password | Dashboard Tujuan |
| :--- | :--- | :--- | :--- |
| **Admin** | `demo.admin@foodflow.test` | `password123` | `/admin/dashboard` |
| **Staf Gudang** | `demo.gudang@foodflow.test` | `password123` | `/gudang/dashboard` |
| **Staf Produksi** | `demo.produksi@foodflow.test` | `password123` | `/produksi/dashboard` |
| **Owner** | `demo.owner@foodflow.test` | `password123` | `/owner/dashboard` |
| **Akun Nonaktif** | `demo.inactive@foodflow.test`| `password123` | Ditolak masuk (403/Redirect login) |
