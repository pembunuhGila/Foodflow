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
