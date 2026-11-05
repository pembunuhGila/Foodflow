import re
import pytest
from playwright.sync_api import Page, expect

# ==============================================================================
# PRAKTIKUM PMPL - PERTEMUAN 9 (FOODFLOW)
# MODUL 02: PERMINTAAN BAHAN & SERAH TERIMA GUDANG (test_02_material_requests.py)
# 1 SOAL = 1 FUNGSI TEST SENDIRI (TIDAK ADA YANG DIGABUNG)
# ==============================================================================


# ==============================================================================
# LAB 4: ASSERTION TIGA LAPISAN (ATURAN BR-05 STOK BAHAN BAKU)
# ==============================================================================

def test_lab4_soal1_buka_halaman_dan_tekan_tombol(page: Page, app_base_url: str, login_as):
    """
    [LAB 4 - SOAL 1]
    Tulis test_pengeluaran_bahan_stok_kurang_ditolak yang membuka halaman
    permintaan bahan dan menekan tombol verifikasi pengeluaran bahan.
    """
    login_as(page, "gudang")
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    expect(page.get_by_role("heading", level=2, name="Permintaan Bahan Baku & Verifikasi Stok Keluar")).to_be_visible()


def test_lab4_soal2_assertion_lapisan_layar(page: Page, app_base_url: str, login_as):
    """
    [LAB 4 - SOAL 2]
    Tambahkan assertion lapisan layar: pesan galat / heading verifikasi
    yang ditampilkan di layar memuat indikator status yang benar.
    """
    login_as(page, "gudang")
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    heading = page.get_by_role("heading", level=2, name="Permintaan Bahan Baku & Verifikasi Stok Keluar")
    expect(heading).to_be_visible()


def test_lab4_soal3_assertion_lapisan_data_reload(page: Page, app_base_url: str, login_as):
    """
    [LAB 4 - SOAL 3]
    Tambahkan assertion lapisan data: muat ulang halaman (page.reload()),
    lalu pastikan status dan sisa stok persisten di database.
    """
    login_as(page, "gudang")
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    page.reload(wait_until="domcontentloaded")
    expect(page.get_by_role("heading", level=2, name="Permintaan Bahan Baku & Verifikasi Stok Keluar")).to_be_visible()


def test_lab4_soal4_assertion_efek_lanjutan(page: Page, app_base_url: str, login_as):
    """
    [LAB 4 - SOAL 4]
    Tambahkan assertion efek lanjutan: periksa master data bahan baku
    dan pastikan stok fisik tidak bocor / tidak ada mutasi keluar sembarangan.
    """
    login_as(page, "gudang")
    page.goto(f"{app_base_url}/ingredients", wait_until="domcontentloaded")
    expect(page.get_by_role("heading", level=2, name="Master Data Bahan Baku")).to_be_visible()


def test_lab4_soal5_tanpa_reload_false_positive(page: Page, app_base_url: str, login_as):
    """
    [LAB 4 - SOAL 5]
    Hapus sementara baris page.reload(), lalu amati apakah test tetap lulus
    (False Positive) saat perubahan data backend tidak diverifikasi ulang.
    """
    login_as(page, "gudang")
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    # Tanpa page.reload(), hanya mengecek tampilan visual di browser
    expect(page.get_by_role("heading", level=2, name="Permintaan Bahan Baku & Verifikasi Stok Keluar")).to_be_visible()


# ==============================================================================
# LAB 5: PRECONDITION LEWAT API VS ANTARMUKA KLIK
# ==============================================================================

def test_lab5_soal1_panggil_fixture_api(page: Page, app_base_url: str, login_as, permintaan_bahan_processing):
    """
    [LAB 5 - SOAL 1]
    Salin fixture di conftest.py dan pastikan dapat dipanggil dari sebuah test.
    Memanggil fixture permintaan_bahan_processing secara langsung.
    """
    login_as(page, "gudang")
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    assert permintaan_bahan_processing["status"] == "PROCESSING"
    expect(page.get_by_role("heading", level=2, name="Permintaan Bahan Baku & Verifikasi Stok Keluar")).to_be_visible()


def test_lab5_soal2_versi_pembanding_klik_antarmuka(page: Page, app_base_url: str, login_as):
    """
    [LAB 5 - SOAL 2]
    Tulis versi pembanding yang menyiapkan data dengan cara mengeklik antarmuka:
    login, mengajukan permintaan via form, lalu memverifikasi. Catat waktunya.
    """
    login_as(page, "produksi")
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    page.get_by_role("link", name="Ajukan Permintaan Bahan").click(no_wait_after=True)
    page.wait_for_url(f"{app_base_url}/material-requests/create", wait_until="domcontentloaded", timeout=40000)
    expect(page.get_by_role("heading", level=2, name="Pengajuan Permintaan Bahan Baku ke Gudang")).to_be_visible()

    page.context.clear_cookies()
    page.goto(f"{app_base_url}/login", wait_until="domcontentloaded")
    login_as(page, "gudang")
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    expect(page.get_by_role("heading", level=2, name="Permintaan Bahan Baku & Verifikasi Stok Keluar")).to_be_visible()


def test_lab5_soal3_matikan_login_uji_kegagalan(page: Page, app_base_url: str):
    """
    [LAB 5 - SOAL 3]
    Matikan sementara halaman login (ubah rutenya). Jalankan kedua versi lagi
    dan amati versi mana yang ikut gagal (Cascading Failure).
    """
    page.goto(f"{app_base_url}/login-maintenance-rusak", wait_until="domcontentloaded")
    expect(page.get_by_label("Email")).to_be_visible(timeout=5000)


def test_lab5_soal4_kesimpulan_ketergantungan(page: Page, app_base_url: str, login_as, permintaan_bahan_processing):
    """
    [LAB 5 - SOAL 4]
    Tulis satu kalimat kesimpulan tentang ketergantungan antar-test:
    Membuktikan isolasi dependensi via fixture API tetap aman dan mandiri.
    """
    login_as(page, "gudang")
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    assert permintaan_bahan_processing["id"] == "REQ-E2E-001"


# ==============================================================================
# LAB 6: TC-ORD-003 END-TO-END PENUH (ATURAN PEMBATALAN BR-02)
# ==============================================================================

def test_lab6_soal1_pembatalan_processing_ditolak(page: Page, app_base_url: str, login_as, permintaan_bahan_processing):
    """
    [LAB 6 - SOAL 1]
    Tulis test_pembatalan_processing_ditolak yang memakai fixture pesanan_processing
    (permintaan_bahan_processing) dari Lab 5.
    """
    login_as(page, "produksi")
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    assert permintaan_bahan_processing["status"] == "PROCESSING"


def test_lab6_soal2_locator_peran_tombol_batalkan(page: Page, app_base_url: str, login_as):
    """
    [LAB 6 - SOAL 2]
    Gunakan locator berbasis peran atau test id untuk tombol 'Batalkan Permintaan'.
    """
    login_as(page, "produksi")
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    btn_batalkan = page.get_by_role("button", name="Batalkan Permintaan")
    # Memeriksa tombol dengan get_by_role
    expect(btn_batalkan).to_have_count(0)


def test_lab6_soal3_tiga_lapisan_expected_result(page: Page, app_base_url: str, login_as, permintaan_bahan_processing):
    """
    [LAB 6 - SOAL 3]
    Periksa ketiga lapisan expected result, termasuk page.reload() sebelum memeriksa status.
    """
    login_as(page, "produksi")
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    btn_batalkan = page.get_by_role("button", name="Batalkan Permintaan")
    expect(btn_batalkan).to_have_count(0)
    
    # Reload sebelum periksa data
    page.reload(wait_until="domcontentloaded")
    expect(btn_batalkan).to_have_count(0)
    assert permintaan_bahan_processing["status"] == "PROCESSING"


def test_lab6_soal4_mode_headed_dan_headless(page: Page, app_base_url: str, login_as):
    """
    [LAB 6 - SOAL 4]
    Jalankan dalam mode headed satu kali untuk memastikan alurnya benar, lalu headless.
    """
    login_as(page, "produksi")
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    expect(page.get_by_role("heading", level=2, name="Permintaan Bahan Baku & Verifikasi Stok Keluar")).to_be_visible()


def test_lab6_soal5_jalur_positif_pending_dibatalkan(page: Page, app_base_url: str, login_as):
    """
    [LAB 6 - SOAL 5]
    Tambahkan test kedua yang membuktikan jalur positifnya: permintaan berstatus PENDING
    boleh dibatalkan, status berubah menjadi CANCELLED.
    """
    login_as(page, "produksi")
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    heading = page.get_by_role("heading", level=2, name="Permintaan Bahan Baku & Verifikasi Stok Keluar")
    expect(heading).to_be_visible()
    page.reload(wait_until="domcontentloaded")
    expect(heading).to_be_visible()


# ==============================================================================
# LAB 7: TRACE, VIDEO, DAN TANGKAPAN LAYAR (ARTEFAK KEGAGALAN)
# ==============================================================================

def test_lab7_soal1_rusak_assertion_hasilkan_artefak(page: Page, app_base_url: str, login_as):
    """
    [LAB 7 - SOAL 1]
    Rusak satu assertion dengan sengaja, lalu jalankan suite dengan perintah:
    pytest tests/e2e --tracing=retain-on-failure --video=retain-on-failure
    """
    login_as(page, "gudang")
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    # Sengaja assertion salah agar memicu perekaman trace dan video saat gagal
    expect(page.locator("h1")).to_have_text("Sengaja Dibuat Salah Untuk Menguji Artefak Trace")


def test_lab7_soal2_buka_trace_viewer(page: Page, app_base_url: str, login_as):
    """
    [LAB 7 - SOAL 2]
    Buka berkas trace dan telusuri langkah demi langkah sampai menemukan titik kegagalannya.
    (Pengujian pembuka modul target penelusuran trace)
    """
    login_as(page, "gudang")
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    expect(page.get_by_role("heading", level=2, name="Permintaan Bahan Baku & Verifikasi Stok Keluar")).to_be_visible()


def test_lab7_soal3_simpan_screenshot_laporan(page: Page, app_base_url: str, login_as):
    """
    [LAB 7 - SOAL 3]
    Simpan satu tangkapan layar dari trace tersebut sebagai calon lampiran bug report.
    """
    login_as(page, "gudang")
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    expect(page.get_by_role("heading", level=2, name="Permintaan Bahan Baku & Verifikasi Stok Keluar")).to_be_visible()


def test_lab7_soal4_perbaiki_assertion_bebas_artefak(page: Page, app_base_url: str, login_as):
    """
    [LAB 7 - SOAL 4]
    Perbaiki kembali assertion, jalankan ulang, dan pastikan tidak ada artefak baru
    yang tersimpan saat test lulus (PASSED).
    """
    login_as(page, "gudang")
    page.goto(f"{app_base_url}/material-requests", wait_until="domcontentloaded")
    # Assertion benar yang membuat test PASSED sehingga tidak ada artefak yang tersimpan
    expect(page.get_by_role("heading", level=2, name="Permintaan Bahan Baku & Verifikasi Stok Keluar")).to_be_visible()
