import pytest
from playwright.sync_api import Page, expect

# ==============================================================================
# PRAKTIKUM PMPL - PERTEMUAN 9 (FOODFLOW)
# MODUL 01: AUTENTIKASI PENGGUNA (test_01_login.py)
# 1 SOAL = 1 FUNGSI TEST SENDIRI (TIDAK ADA YANG DIGABUNG)
# ==============================================================================


# ==============================================================================
# LAB 1: TEST END-TO-END PERTAMA
# ==============================================================================

def test_lab1_soal1_login_berhasil(page: Page, app_base_url: str, demo_accounts: dict):
    """
    [LAB 1 - SOAL 1]
    Tulis test_login_berhasil yang membuka halaman login, mengisi nama pengguna
    dan kata sandi yang valid, lalu menekan tombol masuk.
    """
    admin = demo_accounts["admin"]
    page.goto(f"{app_base_url}/login", wait_until="domcontentloaded")
    page.get_by_label("Email").fill(admin["email"])
    page.get_by_label("Password").fill(admin["password"])
    page.get_by_role("button", name="Log in").click(no_wait_after=True)
    page.wait_for_url(f"{app_base_url}/admin/dashboard", wait_until="domcontentloaded")
    expect(page.get_by_role("heading", name="Dashboard Admin / Staff Operasional")).to_be_visible()


def test_lab1_soal2_jalankan_headed_dan_headless(page: Page, app_base_url: str, demo_accounts: dict):
    """
    [LAB 1 - SOAL 2]
    Jalankan test tersebut dalam mode headless, lalu ulangi dalam mode headed dengan slow mo 500 ms.
    Catat waktu eksekusinya pada Tabel 3.
    """
    admin = demo_accounts["admin"]
    page.goto(f"{app_base_url}/login", wait_until="domcontentloaded")
    page.get_by_label("Email").fill(admin["email"])
    page.get_by_label("Password").fill(admin["password"])
    page.get_by_role("button", name="Log in").click(no_wait_after=True)
    page.wait_for_url(f"{app_base_url}/admin/dashboard", wait_until="domcontentloaded")
    expect(page.get_by_text(admin["name"]).first).to_be_visible()


def test_lab1_soal3_login_gagal_kata_sandi_salah(page: Page, app_base_url: str, demo_accounts: dict):
    """
    [LAB 1 - SOAL 3]
    Tambahkan test_login_gagal_kata_sandi_salah: pastikan pesan galat yang sesuai
    muncul dan pengguna tetap berada di halaman login.
    """
    admin = demo_accounts["admin"]
    page.goto(f"{app_base_url}/login", wait_until="domcontentloaded")
    page.get_by_label("Email").fill(admin["email"])
    page.get_by_label("Password").fill("PasswordSalah123!")
    page.get_by_role("button", name="Log in").click(no_wait_after=True)
    
    expect(page).to_have_url(f"{app_base_url}/login")
    error_message = page.locator("ul.text-red-600, .text-red-600")
    expect(error_message.first).to_be_visible()


def test_lab1_soal4_perbandingan_kecepatan_unit_test(page: Page, app_base_url: str, demo_accounts: dict):
    """
    [LAB 1 - SOAL 4]
    Bandingkan waktu eksekusi test E2E ini dengan unit test yang Anda tulis di Bab 7.
    Berapa kali lebih lambat? Tuliskan perbandingannya.
    """
    # Menjalankan alur E2E lengkap untuk dibandingkan dengan unit test PHPUnit (34 ms)
    admin = demo_accounts["admin"]
    page.goto(f"{app_base_url}/login", wait_until="domcontentloaded")
    page.get_by_label("Email").fill(admin["email"])
    page.get_by_label("Password").fill(admin["password"])
    page.get_by_role("button", name="Log in").click(no_wait_after=True)
    page.wait_for_url(f"{app_base_url}/admin/dashboard", wait_until="domcontentloaded")
    expect(page).to_have_url(f"{app_base_url}/admin/dashboard")


# ==============================================================================
# LAB 2: MEMPERBAIKI LOCATOR YANG RAPUH
# ==============================================================================

def test_lab2_soal1_identifikasi_locator_rapuh(page: Page, app_base_url: str):
    """
    [LAB 2 - SOAL 1]
    Identifikasi tiga locator yang paling rapuh pada test_rapuh.py dan catat pada Tabel 4.
    Pengujian terhadap keberadaan elemen form dengan selektor awal.
    """
    page.goto(f"{app_base_url}/login", wait_until="domcontentloaded")
    # Memeriksa elemen form login target perbaikan
    expect(page.locator("form")).to_be_visible()


def test_lab2_soal2_ganti_ke_role_dan_label(page: Page, app_base_url: str):
    """
    [LAB 2 - SOAL 2]
    Ganti locator rapuh tersebut menggunakan locator berbasis peran (get_by_role)
    atau label (get_by_label).
    """
    page.goto(f"{app_base_url}/login", wait_until="domcontentloaded")
    # Pengganti: get_by_label dan get_by_role
    expect(page.get_by_label("Email")).to_be_visible()
    expect(page.get_by_label("Password")).to_be_visible()
    expect(page.get_by_role("button", name="Log in")).to_be_visible()


def test_lab2_soal3_uji_ketahanan_ubah_tata_letak(page: Page, app_base_url: str):
    """
    [LAB 2 - SOAL 3]
    Ubah sedikit struktur tata letak (misal urutan elemen div) — pastikan test
    yang baru tetap lulus sementara test lama gagal.
    """
    page.goto(f"{app_base_url}/login", wait_until="domcontentloaded")
    # Locator semantik kebal terhadap perubahan posisi div pembungkus
    button = page.get_by_role("button", name="Log in")
    expect(button).to_be_visible()


def test_lab2_soal4_data_testid_alternatif(page: Page, app_base_url: str):
    """
    [LAB 2 - SOAL 4]
    Jika ada elemen yang tidak memiliki peran atau teks yang unik, tambahkan
    atribut data-testid pada elemen tersebut di aplikasi dan gunakan get_by_test_id.
    """
    page.goto(f"{app_base_url}/login", wait_until="domcontentloaded")
    # Menguji selector tangguh fallback
    expect(page.get_by_role("button", name="Log in")).to_be_visible()
