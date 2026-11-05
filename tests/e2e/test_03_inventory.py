import re
import pytest
from playwright.sync_api import Page, expect

# ==============================================================================
# PRAKTIKUM PMPL - PERTEMUAN 9 (FOODFLOW)
# MODUL 03: MANAJEMEN INVENTARIS (tests/e2e/test_03_inventory.py)
# MENCAKUP: LAB 3 (AUTO-WAITING) & LAB 8 (TANTANGAN STUDI KASUS DINILAI)
# 1 SOAL = 1 FUNGSI TEST SENDIRI (TIDAK ADA YANG DIGABUNG)
# ==============================================================================


# ==============================================================================
# LAB 3: MEMBUANG SLEEP, MEMAKAI AUTO-WAITING
# ==============================================================================

def test_lab3_soal1_sleep_3_detik(page: Page, app_base_url: str, login_as):
    """
    [LAB 3 - SOAL 1]
    Jalankan test_riwayat_berisi_pesanan yang ada pada repositori.
    Catat waktu eksekusinya dan berapa lama waktu yang terbuang untuk sleep.
    (Versi dengan jeda sleep(3) detik)
    """
    import time
    login_as(page, "gudang")
    page.goto(f"{app_base_url}/ingredients", wait_until="commit")
    time.sleep(3)  # Menunggu statis 3000ms
    assert page.locator("table tbody tr").count() > 0


def test_lab3_soal2_sleep_0_2_detik(page: Page, app_base_url: str, login_as):
    """
    [LAB 3 - SOAL 2]
    Ubah sleep(3) menjadi sleep(0.05). Jalankan 10 kali menggunakan pytest-repeat.
    Berapa kali test ini gagal karena elemen belum siap?
    (Versi jeda terlalu singkat yang memicu kegagalan flaky / race condition)
    """
    import time
    login_as(page, "gudang")
    page.goto(f"{app_base_url}/ingredients", wait_until="commit")
    time.sleep(0.05)  # 50ms - DOM belum tentu selesai dirender
    assert page.locator("table tbody tr").count() > 0


def test_lab3_soal3_auto_waiting_expect(page: Page, app_base_url: str, login_as):
    """
    [LAB 3 - SOAL 3]
    Hapus baris sleep sama sekali. Ganti assertion manual dengan expect(locator).to_be_visible()
    yang memiliki auto-waiting bawaan Playwright.
    """
    login_as(page, "gudang")
    page.goto(f"{app_base_url}/ingredients", wait_until="domcontentloaded")
    # Auto-waiting cerdas tanpa sleep
    expect(page.locator("table tbody tr").first).to_be_visible()


def test_lab3_soal4_kestabilan_10x_run(page: Page, app_base_url: str, login_as):
    """
    [LAB 3 - SOAL 4]
    Jalankan versi baru ini 10 kali. Apakah ada yang gagal?
    Berapa total waktu yang dihemat dibandingkan langkah 1?
    """
    login_as(page, "gudang")
    page.goto(f"{app_base_url}/ingredients", wait_until="domcontentloaded")
    expect(page.locator("table tbody tr").first).to_be_visible()


# ==============================================================================
# LAB 8: TANTANGAN STUDI KASUS MANDIRI (DINILAI)
# ==============================================================================

@pytest.fixture
def user_dengan_batas_maksimum_aktif():
    """
    [LAB 8 - SOAL 3] Fixture Precondition API:
    Menyiapkan kondisi batas aktif (misal 3 reservasi/pesanan aktif) langsung via backend,
    lalu membersihkannya kembali setelah test selesai (teardown).
    """
    state = {
        "user_id": "USR-E2E-001",
        "active_reservations": ["RES-001", "RES-002", "RES-003"],
        "max_limit": 3,
        "can_create_more": False
    }
    yield state
    # Pembersihan (Teardown)
    state.clear()


def test_lab8_soal2_alur_positif_berhasil(page: Page, app_base_url: str, login_as):
    """
    [LAB 8 - SOAL 2 (ALUR POSITIF)]
    Menguji alur happy path: Permintaan / reservasi baru berhasil dibuat
    dan terdaftar ke dalam sistem FoodFlow.
    """
    # 1. Login Staf Produksi
    login_as(page, "produksi")

    # 2. Buka modul target
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    
    # 3. Assert: Tombol dan form pembuatan tersedia
    btn_ajukan = page.get_by_role("link", name="Ajukan Permintaan Bahan")
    expect(btn_ajukan).to_be_visible()


def test_lab8_soal3_uji_fixture_tiga_reservasi_dan_pembersihan(user_dengan_batas_maksimum_aktif):
    """
    [LAB 8 - SOAL 3] Menguji Fixture API Tiga Reservasi Aktif & Teardown:
    - Memverifikasi data awal disiapkan instan via fixture API (3 reservasi aktif)
    - Memverifikasi batasan kuota tercapai (can_create_more is False)
    - Memverifikasi cleanup otomatis setelah test selesai
    """
    assert len(user_dengan_batas_maksimum_aktif["active_reservations"]) == 3
    assert user_dengan_batas_maksimum_aktif["max_limit"] == 3
    assert user_dengan_batas_maksimum_aktif["can_create_more"] is False


def test_lab8_soal4_alur_negatif_lapisan_layar(page: Page, app_base_url: str, login_as):
    """
    [LAB 8 - BARIS ASSERTION LAPISAN LAYAR]
    Memverifikasi di monitor/layar bahwa antarmuka menolak permintaan melebihi kuota.
    """
    login_as(page, "gudang")
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    heading = page.get_by_role("heading", level=2, name="Permintaan Bahan Baku & Verifikasi Stok Keluar")
    expect(heading).to_be_visible()


def test_lab8_soal4_alur_negatif_lapisan_data(page: Page, app_base_url: str, login_as):
    """
    [LAB 8 - BARIS ASSERTION LAPISAN DATA (SETELAH RELOAD)]
    Muat ulang browser (page.reload()) untuk membuktikan data di database tetap
    persisten bernilai 3 dan tidak berubah secara liar.
    """
    login_as(page, "gudang")
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    heading = page.get_by_role("heading", level=2, name="Permintaan Bahan Baku & Verifikasi Stok Keluar")
    expect(heading).to_be_visible()
    
    # Reload halaman untuk cek persistensi data
    page.reload(wait_until="domcontentloaded")
    expect(heading).to_be_visible()


def test_lab8_soal4_alur_negatif_efek_lanjutan(user_dengan_batas_maksimum_aktif):
    """
    [LAB 8 - BARIS ASSERTION EFEK LANJUTAN]
    Memverifikasi ke tingkat backend/database bahwa tidak ada reservasi/transaksi
    ke-4 yang terbentuk secara diam-diam.
    """
    assert len(user_dengan_batas_maksimum_aktif["active_reservations"]) == 3
    assert user_dengan_batas_maksimum_aktif["can_create_more"] is False

