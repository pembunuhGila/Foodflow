# 🏛️ FoodFlow Project Governance Log
## Catatan Tata Kelola Proyek, Pengendalian Perubahan, & Transisi Anggota Tim

Dokumen ini merupakan catatan tata kelola resmi (*Governance Log*) proyek perangkat lunak **FoodFlow (ERP Katering & Manajemen Dapur MBG Kota Malang)** sesuai dengan standar rekayasa perangkat lunak dan komitmen dalam *Scope Statement*. Dokumen ini mencatat setiap keputusan struktural, perubahan ruang lingkup (*scope changes*), serta penyesuaian susunan tim pengembang.

---

## 📌 Log Transisi Anggota Tim: GOV-2026-001

| Parameter | Detail Informasi |
| :--- | :--- |
| **ID Log** | `GOV-2026-001` |
| **Tanggal Efektif** | 14 November 2026 |
| **Kategori Perubahan** | *Structural Team Transition & Scope Realignment* |
| **Anggota Terdampak** | **Syahfrizam Ikmal K. (Izam)** |
| **Peran Awal** | Data Analyst / Full-Stack Developer (Divisi Admin Penjualan & Pengadaan Bahan) |
| **Status Perubahan** | **Honorably Departed / Contribution Freeze** (Mengundurkan diri dari perkuliahan) |
| **Penyetuju (Sign-off)** | Rachmad Zaki Setyawan (Project Manager & Tech Lead) |

---

## 1. Latar Belakang & Pernyataan Resmi

Pada pertengahan siklus pengembangan (fase menuju Milestone 3 / Expo Proyek), rekan tim kami, **Syahfrizam Ikmal K. (Izam)**, memutuskan untuk tidak melanjutkan perkuliahan. 

Sesuai dengan etika rekayasa perangkat lunak (*Software Engineering Code of Ethics*) dan prinsip tata kelola tim yang transparan:
1. Seluruh kontribusi kode, arsitektur, dan modul yang telah dikerjakan dan diselesaikan oleh Syafrizam selama masa aktifnya **diakui secara penuh dan dilindungi hak akademisnya**.
2. Rekam jejak kontribusi kode Syafrizam pada repositori Git dibekukan (*Contribution Freeze*) dan tidak akan dihapus atau diklaim oleh anggota lain.
3. Seluruh tanggung jawab pemeliharaan, integrasi lanjutan, dan penyelesaian laporan akhir dialihkan secara resmi kepada anggota tim aktif.

---

## 2. Inventarisasi Kontribusi Selesai (Frozen Contributions)

Seluruh modul di bawah ini telah diselesaikan 100% oleh Syafrizam dan telah berhasil dimerge ke branch utama (`main`):

| No | Modul / Fitur | Branch Asal | Nomor PR / Bukti | Status Pengujian |
| :---: | :--- | :--- | :---: | :---: |
| 1 | **Master Pelanggan Catering (`customers`)**<br>Manajemen data pemesan katering, riwayat kontak, dan alamat. | `feat/customers-crud` | **PR #8** ✅ | 100% Lulus Test |
| 2 | **Skema Database & Model Pesanan (`catering_orders`)**<br>Relasi database pesanan, enum status, dan audit fields. | `feat/orders-migration-model` | **PR #12** ✅ | 100% Lulus Test |
| 3 | **Formulir Input Pesanan Catering**<br>Formulir pemesanan multi-menu interaktif berbasis Alpine.js. | `feat/orders-create-form` | **PR #15** ✅ | 100% Lulus Test |
| 4 | **Daftar & Filter Status Pesanan Catering**<br>Tabel daftar transaksi pesanan katering dengan filter dinamis. | `feat/orders-list-status` | **PR #16** ✅ | 100% Lulus Test |
| 5 | **Detail Pesanan, Invoice & Aksi Pembatalan**<br>Tampilan detail pesanan, kalkulasi total tagihan, dan pembatalan pesanan. | `feat/orders-detail-view` | **PR #17** ✅ | 100% Lulus Test |
| 6 | **Monitoring Evaluasi Gizi & Laporan Owner**<br>Pemantauan kalori, makronutrien porsi katering, dan ekspor laporan PDF. | `feat/owner-nutrition-monitoring-reports` | **PR #18** ✅ | 100% Lulus Test |
| 7 | **Format Cetak Portrait A4 & Seeder Pagination**<br>Standarisasi cetak dokumen dan pengisian dataset uji coba. | `feat/report-portrait-pagination` | **PR #41** ✅<br>*(Co-author)* | 100% Lulus Test |

---

## 3. Matriks Pengalihan Tanggung Jawab (Handover Matrix)

Untuk menjamin kelangsungan aplikasi hingga presentasi akhir dan demo produk (Milestone 3), tanggung jawab operasional dan teknis dialihkan sebagai berikut:

```
[Syafrizam Ikmal K.] (Mantan PIC Penjualan & Data Analyst)
       │
       ├───► Modul Siklus Pesanan & Pelanggan ──────► [Rachmad Zaki Setyawan] (Lead & Fullstack)
       │
       ├───► Modul Monitoring Gizi & Laporan Owner ─► [Syafrida Ade Aulia Hakim] (Produksi & Gizi)
       │
       ├───► Penjaminan Kualitas (QA / Test Cases) ─► [Mutiara Inayah M.] (System Analyst & QA)
       │
       └───► Desain Antarmuka & Responsiveness ─────► [Yola Nur Aida] (UI/UX & Frontend)
```

### Rincian Pembagian Tugas Baru:
1. **Rachmad Zaki Setyawan (Project Manager & Tech Lead)**:
   - Memelihara backend modul Pesanan Katering (`CateringOrderController`, Service, dan Form Request).
   - Menangani integrasi REST API Sanctum untuk transaksi pesanan.
   - Mengawal arsitektur sistem dan kesiapan deployment server cloud.
2. **Syafrida Ade Aulia Hakim (Backend & Ahli Dapur)**:
   - Menghubungkan pesanan katering dengan modul perencanaan masak dapur (*Production Planning*).
   - Memastikan formula perhitungan kebutuhan gizi selaras dengan standar porsi makan katering.
3. **Mutiara Inayah M. (System Analyst & QA)**:
   - Mengambil alih pengujian fungsional (*Automated Tests & Manual Verification*) untuk alur pesanan pelanggan hingga invoice.
   - Menyusun laporan hasil pengujian dan matriks penelusuran kebutuhan (*Traceability Matrix*).
4. **Yola Nur Aida (UI/UX Designer & Frontend)**:
   - Memastikan antarmuka halaman pesanan katering dan formulir pemesanan selaras dengan tema biru katering (*Catering Blue Design System*).
   - Menyiapkan aset grafis presentasi dan materi expo (poster A1 & brosur A5).

---

## 4. Dampak Ruang Lingkup, Waktu, & Anggaran (Scope, Schedule, & Budget Impact)

Berdasarkan komitmen pada dokumen *Scope Statement* dan database *Budget Plan*:

### A. Ruang Lingkup (Scope)
- **Tidak ada pemangkasan fitur (Zero Scope Reduction)**: Seluruh 14 fitur inti FoodFlow tetap dipertahankan dan diselesaikan 100%, karena seluruh fitur yang berada di bawah tanggung jawab Syafrizam sudah selesai dikerjakan sebelum pengunduran dirinya.

### B. Alokasi Jam Kerja SDM (Schedule / Non-Cash Resources)
- Nilai SDM Syafrizam (240 jam kerja) pada iterasi awal telah terserap optimal untuk perancangan fondasi pesanan dan evaluasi nutrisi.
- Jam kerja yang tersisa untuk fase penutupan (Minggu 13 s/d 16: Pengujian Usability, Dokumentasi Akhir, dan Persiapan Expo) didistribusikan secara proporsional kepada 4 anggota tim aktif.

### C. Anggaran Tunai Proyek (Budget Realignment)
- Total biaya tunai proyek: **Rp2.973.300** (termasuk kontingensi 10% sebesar Rp270.300).
- Penyesuaian beban biaya tunai per anggota aktif:
  - *Estimasi Awal (÷ 5 anggota)*: Rp594.660 / orang.
  - *Opsi Realokasi (÷ 4 anggota aktif)*: Rp743.325 / orang, atau memanfaatkan sisa cadangan dana kontingensi proyek untuk menutupi selisih operasional tanpa memberatkan anggota.

---

## 5. Protokol Kemanan & Repositori Git

1. **Branch Git**: Seluruh branch lokal milik Syafrizam yang telah dimerge (`feat/orders-*`) dinyatakan berstatus *Archived*.
2. **Akses Repositori**: Tidak ada kredensial sensitif atau token production yang terdampak karena otentikasi deployment dikelola terpusat melalui GitHub Secrets tim.
3. **Integritas Commit**: Penulis commit (*Git Author*) atas nama Syafrizam tetap dipertahankan pada riwayat log git tanpa rebase atau modifikasi metadata.

---

## 6. Persetujuan & Tanda Tangan Tata Kelola

| Peran | Nama Anggota | Status Persetujuan |
| :--- | :--- | :---: |
| **Project Manager & Tech Lead** | Rachmad Zaki Setyawan | Disetujui (Approved) ✅ |
| **System Analyst & QA** | Mutiara Inayah M. | Disetujui (Approved) ✅ |
| **UI/UX Designer & Frontend** | Yola Nur Aida | Disetujui (Approved) ✅ |
| **Backend Developer** | Syafrida Ade Aulia Hakim | Disetujui (Approved) ✅ |

*Catatan Tata Kelola ini berlaku efektif sejak tanggal ditetapkan dan menjadi lampiran resmi Laporan Akhir Tugas Proyek FoodFlow.*


---

## 📌 Log Pengendalian Perubahan & Riwayat Tugas Commit Branch: GOV-2026-002

| Parameter | Detail Informasi |
| :--- | :--- |
| **ID Log** | `GOV-2026-002` |
| **Periode Efektif** | 30 Oktober 2026 s/d 15 November 2026 |
| **Kategori Tata Kelola** | *Change Control, Feature Evolution, & Git Branch Task Management* |
| **Ruang Lingkup** | Seluruh Modul (UI/UX, Backend, Database, E2E Testing, CI/CD, Deployment, dan Governance) |
| **Penyetuju (Sign-off)** | Rachmad Zaki Setyawan (Project Manager & Tech Lead) |

---

### 1. Latar Belakang & Rasional Perubahan (Change Request Rationale)

Mulai tanggal **30 Oktober 2026**, proyek FoodFlow memasuki fase stabilisasi dan peningkatan mutu intensif menjelang penyelesaian akhir sistem. Berdasarkan hasil evaluasi kelayakan sistem, audit pengalaman pengguna (*UX audit*), pengujian otomatis (*automated testing*), dan komitmen rekayasa perangkat lunak, tim menyepakati serangkaian *Change Requests* (CR) dan revisi tugas cabang Git (*branch task realignment*).

Tujuan tata kelola perubahan ini adalah:
1. **Peningkatan Kualitas Visual & Ergonomi (30 Okt – 02 Nov):** Menyelaraskan seluruh antarmuka ke *Design System* katering yang bersih, profesional, dan responsif.
2. **Penjaminan Mutu Otomatis (03 – 05 Nov):** Mengintegrasikan *suite* pengujian otomatis Playwright E2E untuk memvalidasi alur pengguna dari browser.
3. **Penguatan Fitur & Keamanan (06 – 11 Nov):** Menerapkan Socialite OAuth2 SSO, pelaporan cetak formal, dan mitigasi celah otorisasi *role*.
4. **Otomasi Operasional & CI/CD (13 Nov):** Mengimplementasikan alur deployment otomatis GitHub Actions ke AWS EC2 dan standardisasi terminologi akuntansi persediaan.
5. **Skalabilitas Enterprise & Fitur Unggulan (14 – 15 Nov):** Menerapkan *SoftDeletes*, antrean asinkron (*Queue Worker*), REST API Sanctum, *Blue Catering Theme*, *Digital Signature*, Kalender Dapur visual, dan Analisis Varians Biaya.

---

### 2. Tabel Matriks Pengendalian Perubahan & Log Komit Git Branch (Mulai 30 Oktober 2026)

Tabel berikut mencatat seluruh rekam jejak revisi tugas, branch asal, komit representatif, dan dampak tata kelola sistem sejak 30 Oktober 2026:

| No | Tanggal | ID / Hash Commit | Branch Git Asal | Pengembang (PIC) | Modul / Komponen | Deskripsi Revisi / Perubahan Tugas | Dampak Tata Kelola & Sistem | Status Merge |
| :-: | :---: | :---: | :---: | :---: | :---: | :--- | :--- | :-: |
| **1** | 30/10/2026 | `d758e56`<br>`f7a91b2`<br>`a889b12` | `revisi-ui`<br>`ui/design-system` | Syafrida Ade A.<br>Yola Nur Aida | Dashboard & Rencana Produksi | Modernisasi dashboard produksi dengan Chart.js tema pastel, pembersihan tombol redundan, dan koneksi modal hapus native. | Meningkatkan keterbacaan data metrik masak bagi chef dan admin. | Merged via PR #38 ✅ |
| **2** | 30/10/2026 | `c24f2ce` | `revisi-ui` | Syafrida Ade A. | Database Migration | Penambahan relasi foreign key `product_id` pada tabel `productions`. | Menjamin integritas referensi langsung jadwal produksi ke menu katering. | Merged to `main` ✅ |
| **3** | 30/10/2026 | `02f5663`<br>`1a940c1` | `revisi-ui` | Syafrida Ade A. | Rencana Produksi | Pembaruan UI lembar kerja bahan baku (*ingredients workbook*) dan perbaikan *breadcrumbs* interaktif. | Mempermudah staf dapur mencetak resep kerja tanpa terpotong format kertas. | Merged to `main` ✅ |
| **4** | 30/10/2026 | `e4def61`<br>`9ffd25a` | `revisi-ui` | Syafrida Ade A. | Master Data | Perapian tampilan tabel dan kartu detail pelanggan serta pemasok (*suppliers*). | Standarisasi kartu kontak dan riwayat kemitraan logistik. | Merged to `main` ✅ |
| **5** | 31/10/2026 | `a4ba0ba`<br>`971d684` | `ui/design-system` | Yola Nur Aida | Master Data Global | Redesain antarmuka pemasok, pelanggan, menu masakan & gizi, dan bahan baku sesuai *Design System*. | Konsistensi palet warna, tipografi, dan margin di seluruh modul master. | Merged to `main` ✅ |
| **6** | 02/11/2026 | `d9f7ca7`<br>`b5babca` | `ui/design-system` | Yola Nur Aida | Akun & Pengguna | Perbaikan UI halaman kelola pengguna (*User Management*) dan form ubah kata sandi. | Meningkatkan kenyamanan visual manajemen hak akses akun. | Merged to `main` ✅ |
| **7** | 02/11/2026 | `1097cf1`<br>`82b9385` | `ui/design-system` | Yola Nur Aida | Pesanan & Resep | Perbaikan tampilan kartu catering-orders, lembar resep bahan, dan dashboard nutrisi gizi. | Menyederhanakan peninjauan pesanan katering dan kalkulasi kalori makanan. | Merged to `main` ✅ |
| **8** | 02/11/2026 | `0e3ba9e`<br>`f26884e` | `ui/design-system` | Yola Nur Aida | Owner & Pengadaan | Perbaikan UI modul Owner, cetak inventori, purchase-order, dan kartu mutasi stok (*stock cards*). | Laporan eksekutif dapat dibaca dan dicetak secara estetis oleh owner. | Merged to `main` ✅ |
| **9** | 03/11/2026 | `2b24e6b`<br>`4372c0f` | `testing` | Rachmad Zaki S. | QA Automation Suite | Implementasi 30 test cases otomasi E2E Playwright (Lab 1–7) mencakup Login, Locator, Auto-wait, dan Pembatalan. | Membangun fondasi regresi otomatis berbasis browser Chromium. | Merged via PR #39 ✅ |
| **10** | 06/11/2026 | `c879e72`<br>`22cb797` | `feat/socialite-auth` | Rachmad Zaki S. | Autentikasi | Integrasi Google Socialite OAuth2 SSO dan validasi keamanan kompleksitas password. | Memberikan alternatif login modern 1-klik yang aman bagi pengguna. | Merged via PR #40 ✅ |
| **11** | 06/11/2026 | `1dc52fc`<br>`0fd318a` | `feat/report-portrait` | Rachmad Zaki S. | Laporan Cetak | Standarisasi format cetak laporan portrait A4 dengan paginasi rapi dan data seeder demo. | Dokumen resmi siap cetak fisik dan bebas dari *overflow* margin kertas. | Merged via PR #41 ✅ |
| **12** | 06/11/2026 | `580f00c`<br>`fad0a20` | `feat/demo-role-switcher` | Rachmad Zaki S. | Utilitas Pengujian | Implementasi tombol jalan pintas demo switcher 4 peran untuk mempermudah evaluasi dosen/penguji. | Mempercepat verifikasi fungsionalitas multi-role saat sesi presentasi. | Merged via PR #42 ✅ |
| **13** | 06/11/2026 | `0ef5c41`<br>`2b4b9f9` | `feat/landing-page` | Rachmad Zaki S. | Landing Page & Auth | Pengembangan landing page modern bertema ERP katering terintegrasi tombol navigasi login. | Memperkuat *branding* FoodFlow sebagai aplikasi enterprise katering. | Merged via PR #43 & #44 ✅ |
| **14** | 06/11/2026 | `635278b`<br>`84197ee` | `docs/materi` | Rachmad Zaki S. | Dokumentasi | Penyusunan panduan integrasi fitur materi dan setup lengkap aplikasi di direktori `docs/system/`. | Menyediakan dokumentasi acuan terpusat bagi seluruh anggota tim. | Merged via PR #45 ✅ |
| **15** | 10/11/2026 | `1db2a2f`<br>`c81eda9` | `fix/security-hardening` | Rachmad Zaki S. | Keamanan Sistem | Hardening keamanan aplikasi: pengamanan route sensitif, sanitasi parameter, dan pencegahan bypass peran. | Melindungi endpoint internal dari potensi eskalasi hak akses (*privilege escalation*). | Merged to `main` ✅ |
| **16** | 11/11/2026 | `c996a0c`<br>`fc0652f` | `fix/tests` | Rachmad Zaki S. | Unit & Feature Testing | Sinkronisasi assertions test suite dengan perubahan UI dan perbaikan toleransi response DOM. | Seluruh automated test berjalan hijau tanpa flakiness. | Merged to `main` ✅ |
| **17** | 11/11/2026 | `3f218ed`<br>`d0d0f04` | `feat/sidebar-nav` | Rachmad Zaki S. | Navigasi Layout | Transformasi navigasi sidebar menjadi mode flat (tanpa accordion) untuk role dengan 5–6 menu. | Mengurangi jumlah klik pengguna (*user friction*) saat mengakses menu kerja. | Merged to `main` ✅ |
| **18** | 11/11/2026 | `2a40087`<br>`67bb9b6` | `feat/worksheet-sig` | Rachmad Zaki S.<br>Syafrida Ade A. | Lembar Dapur | Penambahan kolom input manual nama penandatangan lembar kerja dapur dan cetak resep. | Memungkinkan penunjukan kepala koki (*head chef*) yang bertugas secara dinamis. | Merged to `main` ✅ |
| **19** | 11/11/2026 | `71a5a6a`<br>`08ca97a` | `feat/custom-units` | Rachmad Zaki S. | Master Bahan Baku | Penambahan opsi input satuan unit kustom manual dan dropdown bertingkat pada master bahan baku. | Mendukung fleksibilitas unit bahan bumbu dapur (misal: sachet, ikat, butir). | Merged to `main` ✅ |
| **20** | 11/11/2026 | `29c3631` | `feat/localization` | Rachmad Zaki S. | UX & Validasi | Kustomisasi pesan error validasi formulir dalam Bahasa Indonesia yang ramah pengguna. | Menggantikan pesan bawaan bahasa Inggris menjadi instruksi perbaikan yang jelas. | Merged to `main` ✅ |
| **21** | 13/11/2026 | `cb471cc`<br>`829f95c` | `feat/setup-ci-cd` | Rachmad Zaki S. | DevOps & CI/CD | Konfigurasi GitHub Actions CI/CD pipeline untuk auto-build dan auto-deploy ke AWS EC2 via SSH. | Menjamin kode branch `main` langsung terdistribusi live secara kontinu (*zero downtime*). | Merged via PR #46 ✅ |
| **22** | 13/11/2026 | `d85d732`<br>`2fa46f8` | `feat/remove-notif` | Rachmad Zaki S. | UI/UX Topbar | Penghapusan icon dropdown lonceng notifikasi statis pada topbar header. | Menghilangkan elemen visual palsu (*non-functional UI slop*) yang membingungkan pengguna. | Merged via PR #47 ✅ |
| **23** | 13/11/2026 | `9b4f38b`<br>`4901afd` | `feat/revisi-stok` | Rachmad Zaki S. | Inventori Gudang | Standardisasi terminologi: mengganti seluruh kata "Saldo" menjadi "Stok" pada laporan gudang. | Menyelaraskan istilah persediaan dengan kaidah tata kelola gudang katering nyata. | Merged via PR #48 ✅ |
| **24** | 13/11/2026 | `858bd6b` | `fix/report-signature` | Rachmad Zaki S. | Pelaporan | Pembersihan state tanda tangan otomatis saat modal dibuka dan penambahan tombol reset manual. | Menghindari tertinggalnya goresan tanda tangan dokumen sebelumnya pada dokumen baru. | Merged to `main` ✅ |
| **25** | 14/11/2026 | `b1ee63e` | `feat/soft-deletes` | Rachmad Zaki S. | Database Arsitektur | Penerapan trait `SoftDeletes` dan kolom `deleted_at` pada seluruh 20 model Eloquent FoodFlow. | Mencegah kehilangan data permanen (*accidental data loss*) dan mendukung audit pemulihan data. | Merged to `main` ✅ |
| **26** | 14/11/2026 | `1026dd9` | `feat/ui-feedback` | Rachmad Zaki S. | Komponen Frontend | Implementasi status loading interaktif dan proteksi *double-submit* pada seluruh tombol form. | Mencegah pengiriman data ganda saat pengguna mengalami koneksi internet lambat. | Merged to `main` ✅ |
| **27** | 14/11/2026 | `c5f1fad` | `feat/queue-export` | Rachmad Zaki S. | Background Jobs | Integrasi background queue worker untuk ekspor berkas Excel/PDF dan notifikasi selesai. | Menghilangkan *request timeout* pada browser saat mengekspor data dalam volume besar. | Merged to `main` ✅ |
| **28** | 14/11/2026 | `8c61d69` | `refactor/ui-ux` | Rachmad Zaki S. | Aksesibilitas | Penyempurnaan semantik warna badge status, bahasa ramah awam, dan pembesaran target sentuh tabel. | Memenuhi standar aksesibilitas antarmuka (*WCAG 2.1 touch target minimum 44px*). | Merged to `main` ✅ |
| **29** | 14/11/2026 | `45c5072` | `fix/concurrency` | Rachmad Zaki S. | Keamanan & Transaksi | Mitigasi konkurensi stok opname via database lock dan perbaikan relasi produksi. | Menjamin konsistensi saldo fisik saat dua staf gudang mengakses data bersamaan. | Merged to `main` ✅ |
| **30** | 14/11/2026 | `2b85736` | `feat/p9-csv-worker` | Rachmad Zaki S. | Integrasi P9 | Implementasi import CSV bahan baku bersifat idempoten dengan validasi background worker. | Mendukung pembaruan massal ribuan katalog bahan baku suplier tanpa duplikasi data. | Merged to `main` ✅ |
| **31** | 14/11/2026 | `f55cf3c` | `feat/p10-rest-sanctum` | Rachmad Zaki S. | Integrasi P10 | Implementasi REST API Sanctum, dukungan `Idempotency-Key`, dan spesifikasi OpenAPI Swagger YAML. | Membuka kapabilitas integrasi FoodFlow dengan aplikasi mobile atau sistem pihak ketiga. | Merged to `main` ✅ |
| **32** | 14/11/2026 | `ca7c767`<br>`e452c89` | `refactor/blue-theme` | Rachmad Zaki S.<br>Yola Nur Aida | Visual Theme | Perombakan identitas visual ke **Blue Catering Theme** (`#1E40AF` & `#3B82F6`) di seluruh aplikasi. | Memberikan kesan visual terpercaya, elegan, dan profesional khas katering korporat. | Merged to `main` ✅ |
| **33** | 15/11/2026 | `61df822` | `refactor/login-ui` | Rachmad Zaki S. | Antarmuka Publik | Perbaikan presisi form login dan peniadaan total tombol jalan pintas demo pada server live. | Menghilangkan kebocoran informasi kredensial demo pada deployment production. | Merged to `main` ✅ |
| **34** | 15/11/2026 | `889c5d4`<br>`1175c23` | `refactor/ui-controls` | Rachmad Zaki S. | Standarisasi UI | Standarisasi ukuran tombol modal, form profil, tata letak kartu akun, dan sidebar navy. | Menghadirkan proporsi antarmuka yang rapi, simetris, dan konsisten di semua resolusi. | Merged to `main` ✅ |
| **35** | 15/11/2026 | `e98bf2d` | `feat/signature-gate` | Rachmad Zaki S. | Kepatuhan Otorisasi | Validasi wajib input nama dan goresan tanda tangan digital sebelum izin ekspor dokumen resmi. | Menjamin keabsahan hukum dokumen PO dan lembar kerja dapur sebelum diterbitkan. | Merged to `main` ✅ |
| **36** | 15/11/2026 | `7252413` | `feat/kitchen-calendar` | Rachmad Zaki S. | Perencanaan Dapur | Peluncuran kalender visual interaktif jadwal produksi masak dapur (`/production-plans/calendar`). | Memudahkan kepala koki memantau jadwal masak harian dan mingguan secara visual. | Merged to `main` ✅ |
| **37** | 15/11/2026 | `ee995e8` | `feat/cost-variance` | Rachmad Zaki S. | Analisis Keuangan | Peluncuran tabel Analisis Varians Biaya Bahan Dapur (Estimasi Resep vs Realisasi Aktual PO). | Memberikan visibilitas selisih biaya dan margin laba katering secara transparan. | Merged to `main` ✅ |
| **38** | 15/11/2026 | `0742528` | `feat/seeders` | Rachmad Zaki S. | Seeder Database | Penyusunan data seeder komprehensif katalog produk, resep gizi, suplier, dan PO operasional. | Memastikan data demo live siap dipresentasikan tanpa perlu pengisian manual berulang. | Merged to `main` ✅ |
| **39** | 15/11/2026 | `c83aebd` | `docs/guides` | Rachmad Zaki S. | Dokumentasi Teknis | Penyusunan panduan Git, manajemen dependensi, dan klarifikasi versi Laravel 13. | Menjamin standar operasional pemeliharaan repositori tim terdokumentasi rapi. | Merged to `main` ✅ |
| **40** | 15/11/2026 | `1aaaf6e` | `feat/landing-update` | Yola Nur Aida | Halaman Utama | Pembaruan landing page katering, penyesuaian hero section, dan integrasi palet logo baru. | Menyempurnakan kesan pertama (*first impression*) pengguna saat pertama membuka web. | Merged to `main` ✅ |
| **41** | 15/11/2026 | `c04b92b` | `feat/p9-memory-streaming` | Rachmad Zaki S. | Optimasi Memori O(1) & Queue | Implementasi streaming CSV via `lazyById(100)`, import bahan baku batch chunking 100 baris dalam transaksi atomik `DB::transaction()`, queued job resilience (`$tries=3`, `$backoff=[10,60]`), Artisan command `benchmark:memory`, dan dokumen riset pengukuran memori. | Menjamin aplikasi kebal terhadap kehabisan memori PHP (*O(1) flat 27.8 MB*) saat memproses ekspor-impor dataset puluhan ribu baris. | Merged to `main` ✅ |
| **42** | 16/11/2026 | `06317f4` | `feat/p10-api-resource-orders` | Rachmad Zaki S. | REST API Sanctum & Resources | Standarisasi respons API via `CateringOrderResource` & `CateringOrderDetailResource` bebas N+1 (`whenLoaded`), penambahan endpoint `GET /catering-orders` (paginasi & query filter) dan `GET /catering-orders/{id}`, validasi ganda `Idempotency-Key` pada `POST`, pembaruan OpenAPI 3.0, serta penambahan unit test komprehensif. | Menjamin pemisahan kontrak API dari database, mencegah kebocoran data sensitif, dan membuka integrasi sistem kasir eksternal/mobile secara aman. | Merged to `main` ✅ |

---

### 3. Rekapitulasi Alokasi & Perubahan Tugas per Anggota Tim (Post 30 Oktober)

| Anggota Tim | Peran Utama | Fokus Kontribusi & Revisi Tugas (Sprint 30 Okt – 15 Nov) | Status Komit |
| :--- | :--- | :--- | :---: |
| **Rachmad Zaki Setyawan** | Project Manager, Tech Lead & DevOps | Arsitektur Enterprise (SoftDeletes, Queue, REST Sanctum), CI/CD GitHub Actions ke AWS EC2, Digital Signature Gate, Kalender Dapur, Cost Variance Analytics, QA Automation E2E Playwright, dan Master Seeders. | Aktif & Terintegrasi ke `main` ✅ |
| **Yola Nur Aida** | UI/UX Designer & Frontend Engineer | Standarisasi Design System di 15+ halaman, pembaruan antarmuka Master Data & Akun, Landing Page modern, perbaikan kartu profil, dan penyesuaian logo katering. | Aktif & Terintegrasi ke `main` ✅ |
| **Syafrida Ade Aulia Hakim** | Backend & Database Developer | Redesain modul Rencana Produksi, penambahan relasi database `product_id`, perapian kartu pelanggan/supplier, dan input dinamis lembar kerja dapur. | Aktif & Terintegrasi ke `main` ✅ |
| **Mutiara Inayah M.** | System Analyst & QA Engineer | Audit konsistensi alur inventaris gudang, verifikasi alur persetujuan Purchase Order, dan pengujian modul operasional. | Aktif & Terintegrasi ke `main` ✅ |
| **Syahfrizam Ikmal K.** | Ex-Developer (Frozen) | Telah menyelesaikan fondasi modul pesanan catering dan purchase order sebelum tanggal transisi; seluruh riwayat komit tetap diakui (Status: *Contribution Freeze* sesuai GOV-2026-001). | Archived & Dibekukan ✅ |

---

### 4. Validasi Integritas Sistem & Status Deployment Live

1. **Integritas Repositori Git**:
   - Seluruh branch pengembangan fitur telah berhasil dimerge ke branch `main`.
   - Tidak ada konflik penggabungan (*merge conflict*) yang tertinggal.
2. **Kesiapan Server Cloud Production**:
   - Server AWS EC2 berjalan aktif di URL publik: `https://foodflow-app.duckdns.org`.
   - Menggunakan Nginx Web Server, PHP 8.4-FPM, dan SSL Let's Encrypt terenkripsi aman.
3. **Kepatuhan CI/CD Pipeline**:
   - Setiap komit baru yang didorong ke `main` secara otomatis memicu skrip deployment GitHub Actions `.github/workflows/deploy.yml` tanpa intervensi manual.

---

### 5. Persetujuan & Tanda Tangan Tata Kelola Perubahan

| Peran | Nama Anggota | Tanda Tangan Persetujuan | Tanggal |
| :--- | :--- | :---: | :---: |
| **Project Manager & Tech Lead** | Rachmad Zaki Setyawan | *Rachmad Zaki S.* ✅ | 15 November 2026 |
| **System Analyst & QA** | Mutiara Inayah M. | *Mutiara Inayah M.* ✅ | 15 November 2026 |
| **UI/UX Designer & Frontend** | Yola Nur Aida | *Yola Nur Aida* ✅ | 15 November 2026 |
| **Backend Developer** | Syafrida Ade Aulia Hakim | *Syafrida Ade A.* ✅ | 15 November 2026 |

*Log Pengendalian Perubahan ini resmi dicatatkan ke dalam repositori dokumentasi sistem FoodFlow sebagai bukti transparansi rekayasa perangkat lunak.*
