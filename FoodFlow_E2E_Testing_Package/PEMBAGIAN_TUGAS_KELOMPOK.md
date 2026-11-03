# PEMBAGIAN TUGAS PRAKTIKUM E2E (4 ANGGOTA KELOMPOK)
**Mata Kuliah:** Penjaminan Mutu Perangkat Lunak (PMPL) - Pertemuan 9  
**Sistem Uji:** FoodFlow (Manajemen Stock & Gizi MBG dan Usaha Catering)  
**Kelompok:** 1 (SIB-3A)  

---

### 📌 Ringkasan Distribusi Tugas

| Anggota | Peran / Fokus | Bagian & Lab yang Dikerjakan | Berkas Kode Uji | Output Tabel Laporan |
| :--- | :--- | :--- | :--- | :--- |
| **Mahasiswa 1** | *E2E Core & Authentication Lead* | **Bagian 0** (Setup Lingkungan)<br>**Lab 1** (Test E2E Pertama: Headed vs Headless)<br>**Lab 2** (Memperbaiki Locator Rapuh) | `tests/e2e/test_01_login.py`<br>`pytest.ini`<br>`conftest.py` | **Tabel 1** (Struktur Proyek)<br>**Tabel 3** (Waktu Headed vs Headless)<br>**Tabel 4** (Resilient Locators) |
| **Mahasiswa 2** | *Test Stability & Flaky Engineer* | **Lab 3** (Membuang Sleep, Memakai Auto-Waiting)<br>**Lab 7** (Trace, Video, Tangkapan Layar Bug Report) | `tests/e2e/test_03_inventory.py` (Lab 3)<br>`test-results/trace.zip` | **Tabel 5** (Sleep vs Expect)<br>**Tabel 9** (Artefak Trace & Video) |
| **Mahasiswa 3** | *Business Logic & Data Integrity* | **Lab 4** (Assertion Tiga Lapisan BR-05)<br>**Lab 5** (Precondition Lewat API Fixture) | `tests/e2e/test_02_material_requests.py` (Lab 4 & 5)<br>`tests/e2e/conftest.py` | **Tabel 6** (Assertion 3 Lapisan)<br>**Tabel 7** (Precondition API vs UI) |
| **Mahasiswa 4** | *Full Flow & Challenge Evaluator* | **Lab 6** (TC-ORD-003 E2E Penuh BR-02)<br>**Lab 8** (Tantangan Studi Kasus Mandiri)<br>**Penutup** (Refleksi Tiga Kalimat) | `tests/e2e/test_02_material_requests.py` (Lab 6)<br>`tests/e2e/test_03_inventory.py` (Lab 8) | **Tabel 8** (Lembar Kerja Lab 6)<br>**Tabel 10** (Studi Kasus Mandiri Lab 8)<br>**Refleksi Tiga Kalimat** |

---

### 📝 Rincian Tanggung Jawab & Terminal Tiap Anggota

#### **1. Mahasiswa 1 — Autentikasi & Locator Semantik**
- Bertanggung jawab memastikan environment testing dan akun demo siap.
- Menjalankan perbandingan headless vs headed (Lab 1) dan mengukur selisih waktu terhadap unit test PHPUnit.
- Menjalankan uji semantic locator (Lab 2):
  ```bash
  python -m pytest tests/e2e/test_01_login.py -v
  ```

#### **2. Mahasiswa 2 — Anti-Flaky, Auto-Waiting, & Bukti Artefak Trace**
- Membuktikan kelemahan `time.sleep(0.05)` yang memicu race condition (Lab 3) dan kestabilan `expect()` Playwright:
  ```bash
  python -m pytest tests/e2e/test_03_inventory.py -k "test_lab3" -v
  ```
- Menjalankan test gagal untuk menghasilkan `trace.zip` dan merekam bukti tangkapan layar untuk laporan cacat (Lab 7):
  ```bash
  python -m pytest tests/e2e/test_02_material_requests.py -k "test_lab7_soal1_rusak_assertion_hasilkan_artefak" --tracing=retain-on-failure --video=retain-on-failure -v
  python -m playwright show-trace $(ls test-results/*/trace.zip | head -n 1)
  ```

#### **3. Mahasiswa 3 — Integritas Data 3 Lapisan & Precondition API**
- Menjalankan pengujian tiga lapisan (Layar, Data via reload, Efek Lanjutan) pada aturan stok BR-05 (Lab 4):
  ```bash
  python -m pytest tests/e2e/test_02_material_requests.py -k "test_lab4" -v
  ```
- Membuktikan isolasi fixture API vs klik antarmuka yang memicu cascading failure saat login dimatikan (Lab 5):
  ```bash
  python -m pytest tests/e2e/test_02_material_requests.py -k "test_lab5" -v
  ```

#### **4. Mahasiswa 4 — E2E Penuh, Tantangan Mandiri, & Refleksi**
- Menjalankan test pembatalan BR-02 dari dua arah (jalur positif & negatif) di Lab 6:
  ```bash
  python -m pytest tests/e2e/test_02_material_requests.py -k "test_lab6" -v
  ```
- Menjalankan tantangan studi kasus mandiri dinilai (Lab 8) dengan trace aktif:
  ```bash
  python -m pytest tests/e2e/test_03_inventory.py -k "test_lab8" --tracing=on -v
  ```
- Menyusun jawaban checklist pengumpulan dan Refleksi Tiga Kalimat pada akhir dokumen.
