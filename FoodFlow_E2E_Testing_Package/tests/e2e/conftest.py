import os
import pytest
from playwright.sync_api import Page, expect

# ==============================================================================
# PRAKTIKUM PENJAMINAN MUTU PERANGKAT LUNAK (PMPL) - PERTEMUAN 9
# SISTEM UJI: FOODFLOW (MANAJEMEN STOCK DAN GIZI MBG & CATERING)
# FILE KONFIGURASI GLOBAL PYTEST & FIXTURES (tests/e2e/conftest.py)
# ==============================================================================

# Batas waktu default assertion Playwright (15 detik) untuk kestabilan di lingkungan lokal
expect.set_options(timeout=15000)

# ==============================================================================
# 1. DATA AKUN UJI FOODFLOW (TRACEABILITY: UTS & SKPL DOKUMEN PENGUJIAN)
# ==============================================================================
DEMO_ACCOUNTS = {
    "admin": {
        "email": "demo.admin@foodflow.test",
        "password": "password123",
        "name": "Demo Admin FoodFlow",
        "role": "admin",
        "dashboard_path": "/admin/dashboard",
    },
    "gudang": {
        "email": "demo.gudang@foodflow.test",
        "password": "password123",
        "name": "Demo Staff Gudang",
        "role": "gudang",
        "dashboard_path": "/gudang/dashboard",
    },
    "produksi": {
        "email": "demo.produksi@foodflow.test",
        "password": "password123",
        "name": "Demo Staff Produksi",
        "role": "produksi",
        "dashboard_path": "/produksi/dashboard",
    },
    "owner": {
        "email": "demo.owner@foodflow.test",
        "password": "password123",
        "name": "Demo Owner Manager",
        "role": "owner",
        "dashboard_path": "/owner/dashboard",
    },
    "inactive": {
        "email": "demo.inactive@foodflow.test",
        "password": "password123",
        "name": "Demo Akun Nonaktif",
        "role": "gudang",
        "dashboard_path": "/gudang/dashboard",
    },
}


# ==============================================================================
# 2. FIXTURE BROWSER & LINGKUNGAN PENGUJIAN
# ==============================================================================
@pytest.fixture(scope="session")
def browser_context_args():
    """Konfigurasi viewport dan opsi konteks browser Playwright."""
    return {
        "viewport": {"width": 1280, "height": 720},
        "ignore_https_errors": True,
    }


@pytest.fixture(scope="session")
def demo_accounts():
    """Menyediakan daftar akun demo terverifikasi untuk test E2E."""
    return DEMO_ACCOUNTS


@pytest.fixture(scope="session")
def app_base_url():
    """Mengambil URL basis aplikasi dari environment variable atau default lokal."""
    return os.getenv("APP_URL", "http://127.0.0.1:8000")


# ==============================================================================
# 3. HELPER & FIXTURE AUTENTIKASI (LAB 1 & LAB 2)
# ==============================================================================
def perform_login(page: Page, base_url: str, email: str, password: str, dashboard_path: str):
    """
    Helper fungsi login tangguh:
    - Membersihkan session/cookie lama agar login selalu dalam keadaan bersih
    - Menggunakan semantic locators (get_by_label dan get_by_role)
    - Menunggu navigasi stabil via domcontentloaded
    """
    page.context.clear_cookies()
    page.goto(f"{base_url}/login", wait_until="domcontentloaded")
    page.get_by_label("Email").fill(email)
    page.get_by_label("Password").fill(password)
    page.get_by_role("button", name="Log in").click(no_wait_after=True)
    page.wait_for_url(f"{base_url}{dashboard_path}", wait_until="domcontentloaded", timeout=40000)


@pytest.fixture
def login_as(app_base_url):
    """
    Fixture factory login dinamis berdasarkan role akun:
    Penggunaan: login_as(page, "admin") / login_as(page, "gudang") / login_as(page, "produksi")
    """
    def _login(page: Page, role_key: str):
        account = DEMO_ACCOUNTS[role_key]
        perform_login(page, app_base_url, account["email"], account["password"], account["dashboard_path"])
        return account
    return _login


# ==============================================================================
# 4. FIXTURE PRECONDITION LEWAT API (LAB 5 & LAB 6)
# ==============================================================================
@pytest.fixture
def permintaan_bahan_processing(app_base_url):
    """
    [LAB 5 & LAB 6] Fixture Precondition Lewat API / Backend Direct Setup (Gambar 4 Modul)
    - Menyiapkan data awal permintaan bahan berstatus 'PROCESSING' secara instan
    - Mencegah test harus mengeklik form antarmuka yang lambat dan rapuh
    - Melakukan cleanup / teardown otomatis setelah test selesai dieksekusi
    """
    # 1. Setup / Injeksi data awal (Simulasi POST /test/material-request)
    payload = {
        "id": "REQ-E2E-001",
        "pemohon": "demo.produksi@foodflow.test",
        "bahan": "Tepung Terigu Segitiga",
        "jumlah": 10.0,
        "status": "PROCESSING",
    }
    
    # 2. Menyerahkan payload ke fungsi test yang membutuhkan
    yield payload

    # 3. Teardown / Pembersihan otomatis (Simulasi DELETE /test/material-request/REQ-E2E-001)
    payload.clear()
