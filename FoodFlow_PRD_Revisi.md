# PRD FoodFlow

## Informasi Dokumen

| Keterangan | Isi |
|---|---|
| Versi | 1.1 - Perbaikan konsistensi dari versi 1.0 (lihat Lampiran A) |
| Tanggal | 12 Oktober 2026 |
| Status | Draft untuk ditinjau tim. Butir bertanda **(usulan)** dan Open Questions (bagian 14) menunggu keputusan. |

---

## 1. Ringkasan PRD FoodFlow

FoodFlow adalah aplikasi berbasis web Sistem Informasi Manajemen Stock dan Gizi pada MBG dan Usaha Catering untuk mengelola data bahan baku, pengadaan, penerimaan dan pemeriksaan bahan, pencatatan stok, pengelolaan menu, perencanaan dan pencatatan produksi, penggunaan bahan, informasi gizi, serta monitoring dan pelaporan.

Sistem dirancang generik untuk dapur katering. Fitur dan integrasi khusus MBG/SPPG berada di luar ruang lingkup (bagian 5.2, lihat juga OQ-R09).

## 2. Tujuan

- Membantu pengelolaan data bahan baku dan informasi stok secara terpusat.
- Mendukung pencatatan pengadaan, penerimaan, pemeriksaan, stok masuk, dan stok keluar.
- Menyediakan satu sumber data bersama bagi Admin/Staff Operasional, Staff Gudang/Inventory, Staff Produksi, dan Owner/Manager sehingga operasional antar divisi saling terhubung.
- Menyediakan informasi monitoring produksi, stok, dan gizi, serta fasilitas pencetakan laporan bagi Owner/Manager.

## 3. Pengguna dan Hak Akses

Sistem memiliki empat aktor bisnis/role sesuai diagram use case. Aktor User merupakan generalization dari semua role yang ada di alur proses.

| Role / Aktor | Tanggung jawab utama |
|---|---|
| **Admin / Staff Operasional** | Bertanggung jawab terhadap pengelolaan data administrasi dan pencatatan transaksi operasional. Aktivitas: register akun, mengelola data pengguna dan hak akses, memproses permintaan reset password, mengelola data menu, mengelola data bahan baku, mengelola data supplier, mengelola data pelanggan, mencatat dan mengelola pesanan, membuat pengadaan bahan ke supplier. |
| **Staff Gudang / Inventory** | Bertanggung jawab terhadap pencatatan dan pemantauan persediaan bahan baku. Aktivitas: mengelola data bahan baku, mencatat penerimaan bahan, memeriksa bahan, mencatat stok masuk, mencatat stok keluar, memverifikasi permintaan bahan, monitoring stok. |
| **Staff Produksi** (termasuk fungsi ahli gizi) | Menggunakan informasi pesanan, komposisi menu, dan ketersediaan bahan untuk mendukung perencanaan serta pelaksanaan produksi. Aktivitas: mengelola menu, merencanakan produksi, menentukan jumlah produksi, menentukan jumlah porsi, menentukan kebutuhan bahan, mencatat produksi, mencatat penggunaan bahan, mengajukan permintaan bahan, mengelola data gizi. |
| **Owner / Manager** (satu role) | Bertanggung jawab melakukan pemantauan terhadap operasional dan memperoleh informasi untuk mendukung pengambilan keputusan. Aktivitas: monitoring produksi, monitoring stok, monitoring informasi gizi, mencetak laporan. |

**Keterangan:** K = Kelola (lihat, tambah, ubah, nonaktifkan), L = Lihat, – = Tidak memiliki akses, Ajukan = Mengajukan permintaan, dan Verifikasi = Memeriksa serta memverifikasi permintaan bahan. Baris *Monitoring* bersifat hanya-lihat (L) untuk semua role; pengelolaan datanya berada pada baris fitur masing-masing.

| Fitur | Admin | Gudang | Produksi | Owner |
|---|:---:|:---:|:---:|:---:|
| Pengguna, hak akses, dan reset password | K | – | – | – |
| Bahan baku (master) | K | K | L | L |
| Menu/produk (master) | K | L | K | L |
| Komposisi menu dan data gizi | L | – | K | L |
| Data Supplier | K | L | – | L |
| Data Pelanggan | K | – | – | L |
| Order | K | L | L | L |
| Pengadaan (catatan internal) | K | L | – | L |
| Penerimaan dan pemeriksaan bahan | L | K | – | L |
| Stok bahan (masuk, keluar, adjustment, pengembalian) | L | K | L | L |
| Permintaan bahan | L | Verifikasi | Ajukan | L |
| Rencana produksi dan produksi | L | L | K | L |
| Pencatatan realisasi produksi | L | L | K | L |
| Pencatatan penggunaan bahan aktual | L | L | K | L |
| Monitoring produksi | L | L | L | L |
| Monitoring stok | L | L | L | L |
| Monitoring informasi gizi | L | – | L | L |
| Pencetakan laporan | – | – | – | K |

## 4. Ketentuan Hak Akses

- Setiap akun memiliki satu role yang menentukan hak aksesnya.
- User hanya dapat mengakses menu dan menjalankan aktivitas yang diizinkan untuk role tersebut.
- Hak akses tidak hanya membatasi tindakan mengubah data, tetapi juga membatasi akses untuk melihat data yang berada di luar kewenangan role.
- Pembatasan hak akses harus diterapkan pada sisi server, bukan hanya dengan menyembunyikan menu atau tombol pada antarmuka.
- User yang mencoba membuka halaman atau menjalankan fungsi di luar hak aksesnya akan ditolak oleh sistem.
- Apabila suatu aktivitas terhubung dengan lebih dari satu aktor dalam use case, aktivitas tersebut dapat diakses oleh role-role yang terkait sesuai kewenangannya.
  - Aktivitas Mengelola Data Bahan Baku terhubung dengan Staff Gudang/Inventory dan Admin/Staff Operasional.
  - Aktivitas Mengelola Menu terhubung dengan Staff Produksi dan Admin/Staff Operasional.
- Role yang tidak memiliki hak terhadap suatu aktivitas tidak otomatis memperoleh akses hanya karena membutuhkan informasi dari aktivitas tersebut.

## 5. Ruang Lingkup

### 5.1 Termasuk dalam Ruang Lingkup

**Autentikasi dan User**

- Login, Logout
- Lupa Password (diproses Admin)
- Register Akun
- Pengelolaan data user dan hak akses.
- Pembatasan akses berdasarkan role.

**Pengelolaan Data Master**

- Pengelolaan data bahan baku.
- Pengelolaan data menu.
- Pengelolaan komposisi bahan pada menu.
- Pengelolaan data supplier.
- Pengelolaan data pelanggan.
- Pengelolaan informasi gizi menu (diinput manual, lihat OQ-R01). Informasi gizi pada bahan baku bersifat opsional dan hanya sebagai referensi.

**Pengelolaan Pesanan**

- Pencatatan pesanan pelanggan.
- Pengelolaan detail pesanan.
- Pencatatan jumlah menu dan informasi pesanan.
- Pengelolaan status pesanan.

**Pengadaan dan Penerimaan Bahan**

- Pencatatan pengadaan bahan kepada supplier, termasuk konfirmasi supplier yang dicatat manual.
- Pencatatan penerimaan bahan.
- Pemeriksaan kondisi bahan yang diterima.
- Pencatatan bahan yang memenuhi atau tidak memenuhi kondisi penerimaan.

**Pengelolaan Stok**

- Pencatatan stok masuk, stok keluar, dan adjustment.
- Perhitungan stok berdasarkan transaksi.
- Monitoring ketersediaan bahan.
- Peringatan stok di bawah batas minimum pada dashboard.

**Perencanaan dan Produksi**

- Perencanaan produksi.
- Penentuan jumlah porsi.
- Perhitungan kebutuhan bahan dan pengecekan kecukupan stok.
- Pengajuan dan verifikasi permintaan bahan.
- Pencatatan realisasi produksi.
- Pencatatan penggunaan bahan aktual.
- Pengembalian sisa bahan ke gudang **(usulan)**.

**Informasi Gizi**

- Pengelolaan data gizi menu.
- Monitoring informasi gizi oleh Owner/Manager.

**Monitoring dan Pelaporan**

- Monitoring produksi.
- Monitoring stok.
- Monitoring informasi gizi.
- Pencetakan laporan oleh Owner/Manager.

### 5.2 Di Luar Ruang Lingkup

- Prediksi permintaan atau kebutuhan bahan berbasis algoritma.
- Fitur khusus MBG/SPPG, sekolah, target porsi MBG, atau integrasi dengan sistem MBG.
- Pencatatan dan analisis food waste sebagai modul tersendiri.
- Pengelolaan beberapa bisnis dalam satu sistem atau multi-tenant.
- Penggunaan satu akun dengan beberapa role.
- Penerapan metode FEFO sebagai fitur khusus, termasuk pelacakan batch dan tanggal kedaluwarsa per stok.
- Integrasi langsung dengan supplier.
- Pembayaran online dan payment gateway.
- Modul akuntansi dan perhitungan Harga Pokok Produksi (HPP).
- Integrasi email atau WhatsApp (notifikasi stok maupun pengiriman password; keduanya dilakukan manual di luar sistem).
- Aplikasi mobile khusus (antarmuka web tetap responsif, lihat KNF-06).
- Distribusi hasil produksi dan pencatatan penerimaan hasil produksi.
- Pengelolaan stok produk jadi secara terpisah, termasuk transaksi stok produk (ditunda, lihat OQ-R02).
- Audit log terperinci.

## 6. Alur Bisnis Utama

1. Admin/Staff Operasional mengelola data pengguna, supplier, pelanggan, bahan baku, dan menu sesuai hak aksesnya.
2. Staff Produksi mengelola menu, komposisi menu, dan data gizi sesuai aktivitas yang terhubung dengan role tersebut.
3. Admin/Staff Operasional mencatat dan mengelola pesanan pelanggan berdasarkan menu, jumlah pesanan, tanggal pemesanan, dan informasi lain yang diperlukan.
4. Staff Produksi menyusun rencana produksi (dapat menghubungkan satu rencana ke beberapa pesanan) dan menentukan jumlah porsi yang akan diproduksi. Berdasarkan jumlah porsi dan komposisi bahan pada menu, sistem menghitung kebutuhan bahan dan membandingkannya dengan stok tersedia sehingga kekurangan bahan dapat diketahui sebelum produksi dilaksanakan.
5. Apabila persediaan bahan tidak mencukupi kebutuhan produksi, Admin/Staff Operasional mencatat pengadaan bahan kepada supplier dan mencatat konfirmasi supplier yang diperoleh di luar sistem.
6. Staff Gudang/Inventory mencatat penerimaan bahan yang datang dari supplier dan memeriksa kondisi bahan tersebut. Bahan dikategorikan sebagai Baik, Rusak, Kedaluwarsa, atau Tidak Sesuai. Hanya bahan berkondisi Baik yang menambah stok.
7. Staff Produksi mengajukan permintaan bahan kepada Staff Gudang/Inventory berdasarkan kebutuhan rencana produksi. Staff Gudang/Inventory memverifikasi permintaan; saat diverifikasi, sistem mencatat pengeluaran bahan (stok OUT).
8. Staff Produksi melaksanakan produksi berdasarkan rencana yang telah dibuat. Setelah produksi berlangsung, Staff Produksi mencatat jumlah produksi aktual dan penggunaan bahan aktual. Data tersebut digunakan untuk membandingkan rencana dengan realisasi produksi. Sisa bahan yang tidak terpakai dikembalikan ke gudang **(usulan)**.
9. Staff Gudang/Inventory memantau kondisi stok secara berkelanjutan, termasuk peringatan stok di bawah minimum pada dashboard.
10. Owner/Manager memantau informasi produksi, stok, dan gizi serta mencetak laporan sesuai hak aksesnya.

## 7. Kebutuhan Fungsional

> Nomor FR yang sudah ada dipertahankan agar tidak memutus rujukan dari dokumen lain. FR-32 dan FR-36 mengisi nomor yang sebelumnya kosong; FR-41 dan FR-42 adalah tambahan baru.

### Autentikasi dan Pengguna

| ID | Kebutuhan |
|---|---|
| FR-01 - Login | Sistem harus menyediakan fitur Login agar user yang memiliki akun terdaftar dan aktif dapat mengakses sistem sesuai dengan role dan hak aksesnya. |
| FR-02 - Lupa Password | Sistem harus menyediakan fitur Lupa Password agar user dapat mengajukan pemulihan password ketika tidak dapat mengakses akun. Permintaan dicatat dengan status (bagian 9) dan diproses oleh Admin, yang membuat password sementara dan menyampaikannya kepada user di luar sistem. User wajib mengganti password sementara saat login berikutnya. |
| FR-03 - Logout | Sistem harus menyediakan fitur Logout agar user dapat mengakhiri sesi penggunaan sistem secara aman. |
| FR-04 - Register Akun dan Pengelolaan User | Sistem harus memungkinkan Admin/Staff Operasional mendaftarkan akun pengguna, menetapkan satu role, serta mengubah dan menonaktifkan akun. |
| FR-05 - Pengelolaan Sesi dan Hak Akses | Sistem harus mengelola sesi Login dan menerapkan hak akses berdasarkan role user untuk memastikan setiap pengguna hanya dapat mengakses fungsi dan data yang menjadi kewenangannya. |

### Master Data

| ID | Kebutuhan |
|---|---|
| FR-06 - Pengelolaan Data Bahan Baku | Sistem harus mendukung pengelolaan data bahan baku, seperti nama bahan, satuan, stok minimum, dan informasi gizi referensi (opsional). Aktivitas ini dapat diakses oleh Staff Gudang/Inventory dan Admin/Staff Operasional. |
| FR-07 - Pengelolaan Data Menu | Sistem harus memungkinkan Staff Produksi dan Admin/Staff Operasional untuk mengelola data menu sesuai hak akses masing-masing berdasarkan Use Case Diagram. |
| FR-08 - Pengelolaan Komposisi Menu | Sistem harus mendukung pencatatan bahan dan jumlah bahan yang diperlukan untuk menghasilkan satu satuan menu (satu porsi). |
| FR-09 - Pengelolaan Data Supplier | Sistem harus mendukung pengelolaan informasi supplier yang diperlukan untuk pencatatan pengadaan bahan. |
| FR-10 - Pengelolaan Data Pelanggan | Sistem harus mendukung pengelolaan informasi pelanggan yang diperlukan untuk pencatatan pesanan. |
| FR-11 - Pengelolaan Data Gizi | Sistem harus memungkinkan Staff Produksi untuk menginput dan mengelola rekap kandungan gizi (kalori, protein, lemak, karbohidrat per satu porsi) pada saat menambahkan atau mengubah data menu. |

### Order

| ID | Kebutuhan |
|---|---|
| FR-12 - Pencatatan Pesanan | Sistem harus memungkinkan Admin/Staff Operasional mencatat pesanan pelanggan beserta detail menu, jumlah, tanggal, dan informasi yang diperlukan. |
| FR-13 - Pengelolaan Pesanan | Sistem harus memungkinkan Admin/Staff Operasional mengelola informasi pesanan sesuai status dan aturan bisnis yang ditetapkan (bagian 9 dan 10). |
| FR-14 - Pencatatan Detail Pesanan | Sistem harus menyimpan hubungan antara pesanan, menu, jumlah, dan harga transaksi apabila harga digunakan dalam proses operasional. |

### Rencana Produksi

| ID | Kebutuhan |
|---|---|
| FR-15 - Merencanakan Produksi | Sistem harus memungkinkan Staff Produksi membuat rencana produksi berdasarkan kebutuhan operasional. |
| FR-16 - Menentukan Jumlah Porsi | Sistem harus memungkinkan Staff Produksi menentukan jumlah porsi yang direncanakan untuk setiap menu pada rencana produksi. |
| FR-17 - Menentukan Kebutuhan Bahan | Sistem harus menghitung kebutuhan bahan berdasarkan jumlah porsi dan komposisi bahan pada menu. |
| FR-18 - Keterkaitan Pesanan dan Produksi | Sistem harus memungkinkan satu rencana produksi dihubungkan ke satu atau beberapa pesanan (BR-09). Penghubungan bersifat opsional. |
| FR-41 - Pengecekan Kecukupan Stok | Sistem harus membandingkan kebutuhan bahan hasil FR-17 dengan stok tersedia dan menampilkan bahan yang kurang beserta jumlah kekurangannya. |

### Pengadaan (Catatan Internal)

| ID | Kebutuhan |
|---|---|
| FR-19 - Membuat Pengadaan Bahan ke Supplier | Admin mencatat pengadaan: supplier, tanggal, perkiraan tanggal diterima, daftar bahan, jumlah, satuan, harga, subtotal, catatan. |
| FR-20 - Pencatatan Konfirmasi Supplier | Sistem harus dapat mencatat konfirmasi supplier (tanggal dan catatan) yang diperoleh Admin/Staff Operasional melalui komunikasi di luar sistem. |

### Penerimaan dan Pemeriksaan Bahan

| ID | Kebutuhan |
|---|---|
| FR-21 - Mencatat Penerimaan Bahan | Sistem harus memungkinkan Staff Gudang/Inventory mencatat bahan yang diterima berdasarkan pengadaan, termasuk penerimaan sebagian. |
| FR-22 - Memeriksa Bahan | Sistem harus mendukung pencatatan kondisi bahan yang diterima, yaitu Baik, Rusak, Kedaluwarsa, atau Tidak Sesuai. |
| FR-23 - Pembaruan Stok dari Penerimaan | Sistem harus memperbarui stok tersedia (transaksi IN) hanya berdasarkan jumlah bahan berkondisi Baik. Bahan berkondisi Rusak, Kedaluwarsa, atau Tidak Sesuai tidak boleh menambah stok tersedia. |

### Pengelolaan Stok

| ID | Kebutuhan |
|---|---|
| FR-24 - Mencatat Stok | Semua pergerakan stok bahan dicatat sebagai transaksi IN, OUT, atau ADJUSTMENT, lengkap dengan sumber transaksi, tanggal, catatan, dan pencatat. |
| FR-25 - Menghitung Stok | Stok saat ini = total IN - total OUT +/- adjustment. |
| FR-26 - Adjustment Stok | Adjustment dipakai bila stok sistem berbeda dengan kondisi fisik, termasuk bahan yang kedaluwarsa setelah disimpan. Stock opname bukan proses utama. |
| FR-27 - Peringatan Stok Minimum | Dashboard menampilkan peringatan bila stok di bawah stok minimum. Tidak ada notifikasi email atau WhatsApp. |
| FR-28 - Stok Produk Jadi *(ditunda)* | Pencatatan transaksi stok produk jadi **tidak termasuk** pada versi ini karena katering umumnya dibuat sesuai pesanan (lihat OQ-R02). |
| FR-29 - Riwayat Stok | Staff Gudang dapat melihat riwayat stok per bahan. |

### Permintaan Bahan

| ID | Kebutuhan |
|---|---|
| FR-30 - Mengajukan Permintaan | Staff Produksi mengajukan permintaan bahan ke Gudang berdasarkan rencana produksi (tabel `material_requests` dan `material_request_details`, bagian 12). |
| FR-31 - Memverifikasi Permintaan | Staff Gudang memverifikasi permintaan dan menetapkan jumlah yang dikeluarkan. Saat diverifikasi, stok bahan langsung dikurangi (OUT) sebesar jumlah tersebut dengan keterangan, misalnya "dikeluarkan untuk produksi, menunggu pencatatan pemakaian". |
| FR-32 - Menolak Permintaan | Staff Gudang dapat menolak permintaan dengan catatan alasan. Permintaan yang ditolak tidak mengubah stok. Sistem tidak boleh mengeluarkan bahan melebihi stok tersedia (BR-13). |
| FR-42 - Pengembalian Sisa Bahan *(usulan)* | Staff Gudang dapat mencatat pengembalian sisa bahan yang tidak terpakai dari produksi sebagai transaksi IN dengan referensi ke permintaan bahan terkait (BR-12). |

### Produksi dan Penggunaan Bahan

| ID | Kebutuhan |
|---|---|
| FR-33 - Mencatat Produksi | Staff Produksi mencatat realisasi produksi dari rencana produksi: tanggal, status, catatan. |
| FR-34 - Mencatat Jumlah Aktual | Sistem membedakan jumlah rencana dan jumlah aktual per menu. |
| FR-35 - Mencatat Penggunaan Bahan | Pemakaian bahan aktual dicatat per produksi (per menu pada rencana produksi). |

### Informasi Gizi

| ID | Kebutuhan |
|---|---|
| FR-36 - Status Kelengkapan Gizi Menu | Sistem harus menyimpan nilai gizi menu sebagaimana diinput dan menampilkan status Tersedia atau Belum Lengkap berdasarkan kelengkapan empat nilai gizi wajib (bagian 11). |

### Dashboard dan Laporan

| ID | Kebutuhan |
|---|---|
| FR-37 - Monitoring Produksi | Sistem harus menyediakan informasi monitoring produksi bagi Owner/Manager, termasuk perbandingan rencana dan realisasi. |
| FR-38 - Monitoring Stok | Sistem harus menyediakan informasi monitoring stok sesuai hak akses Owner/Manager dan Staff Gudang/Inventory. |
| FR-39 - Monitoring Gizi | Sistem harus menyediakan fitur monitoring informasi gizi bagi Owner/Manager untuk melihat rekap kandungan gizi menu yang tersimpan dalam sistem. |
| FR-40 - Mencetak Laporan | Sistem harus memungkinkan Owner/Manager mencetak laporan berikut dengan filter periode **(usulan, lihat OQ-R07)**: (a) Laporan Produksi: rencana vs realisasi; (b) Laporan Stok: stok saat ini dan riwayat transaksi; (c) Laporan Informasi Gizi Menu. |

## 8. Kebutuhan Non-Fungsional

| ID | Kategori | Kebutuhan Non-Fungsional |
|---|---|---|
| KNF-01 | Keamanan | Sistem harus menerapkan autentikasi pengguna melalui halaman login dan membatasi akses fitur berdasarkan hak akses masing-masing peran, dengan pemeriksaan di sisi server. |
| KNF-02 | Integritas Data | Sistem harus memvalidasi data yang dimasukkan pengguna (tipe, rentang, kewajiban isi) untuk mencegah kesalahan input dan menjaga konsistensi data. |
| KNF-03 | Kinerja | Pada kondisi jaringan dan beban normal, halaman biasa harus tampil dalam ≤ 3 detik dan laporan dalam ≤ 10 detik **(usulan target, lihat OQ-R08)**. |
| KNF-04 | Kegunaan | Sistem harus memiliki antarmuka yang sederhana, konsisten, dan mudah dipahami oleh pengguna sesuai dengan perannya. |
| KNF-05 | Kompatibilitas | Sistem harus dapat diakses melalui peramban web modern, seperti Google Chrome dan Microsoft Edge. |
| KNF-06 | Responsivitas | Antarmuka sistem harus menyesuaikan ukuran layar perangkat desktop maupun perangkat seluler agar informasi tetap dapat dibaca dengan baik. |
| KNF-07 | Integritas Data | Sistem harus menjaga konsistensi data persediaan, penggunaan bahan, dan rekap gizi menu agar perubahan data tidak menimbulkan ketidaksesuaian antarbagian. |
| KNF-08 | Keandalan | Sistem harus memberikan informasi yang sesuai dengan data yang tersimpan dan menampilkan pemberitahuan apabila proses penyimpanan data gagal. |
| KNF-09 | Pemeliharaan | Struktur aplikasi harus disusun secara terorganisasi agar memudahkan proses pemeliharaan, pengujian, dan pengembangan sistem. |
| KNF-10 | Akurasi Perhitungan | Sistem harus menyimpan dan menampilkan nilai gizi menu persis seperti yang diinput dengan satuan konsisten (kkal, gram). Perhitungan yang dilakukan sistem (kebutuhan bahan, stok saat ini) harus sesuai rumus pada FR-17 dan FR-25. Jika perhitungan gizi otomatis diterapkan (OQ-R01), hasilnya harus sesuai rumus yang ditetapkan. |
| KNF-11 | Keamanan | Password harus disimpan dalam bentuk hash (bukan teks asli), dan sesi yang tidak aktif harus berakhir otomatis setelah batas waktu yang ditetapkan **(usulan)**. |
| KNF-12 | Keandalan | Data harus dicadangkan secara berkala sesuai kebijakan yang ditetapkan tim **(usulan)**. |

## 9. Status dan Alur

Status bertanda **(usulan)** sebelumnya belum didefinisikan dan perlu disepakati (OQ-R03).

| Entitas | Status | Alur status |
|---|---|---|
| Akun User | Aktif, Tidak Aktif | User melakukan login menggunakan akun yang telah terdaftar. Sistem memvalidasi kredensial dan status akun. Jika valid dan aktif, pengguna diarahkan ke halaman sesuai hak aksesnya. Jika tidak valid atau akun tidak aktif, sistem menampilkan pesan kesalahan. |
| Permintaan Reset Password | Diajukan, Diproses, Selesai, Gagal | User memasukkan email terdaftar melalui fitur Lupa Password (status Diajukan). Admin memeriksa permintaan (Diproses), membuat password sementara, dan menyampaikannya kepada user di luar sistem. Status menjadi Selesai setelah password sementara diberikan, atau Gagal apabila proses tidak dapat diselesaikan (misalnya identitas tidak dapat dipastikan). |
| Bahan Baku | Aktif, Tidak Aktif | Data bahan baku ditambahkan atau diperbarui oleh user yang memiliki hak akses terkait. Data yang tidak lagi digunakan dapat dinonaktifkan tanpa menghapus riwayat transaksi persediaan. |
| Menu | Aktif, Tidak Aktif | Staff Produksi menambahkan atau memperbarui data menu serta informasi kandungan gizi menu. Menu yang tidak lagi digunakan dapat dinonaktifkan, sedangkan informasi menu yang aktif dapat dilihat oleh user sesuai hak aksesnya. |
| Informasi Gizi Menu (diturunkan dari data) | Tersedia, Belum Lengkap | Saat menambahkan atau memperbarui menu, Staff Produksi memasukkan kalori, protein, lemak, dan karbohidrat per porsi. Status Tersedia apabila keempat nilai telah diisi, dan Belum Lengkap apabila masih ada yang kosong. Status dihitung sistem, tidak diinput. |
| Pesanan *(usulan)* | Baru, Dikonfirmasi, Dalam Produksi, Selesai, Dibatalkan | Admin mencatat pesanan (Baru), mengonfirmasi (Dikonfirmasi), status berubah menjadi Dalam Produksi saat dihubungkan ke rencana produksi yang berjalan, dan Selesai setelah produksi untuk pesanan tersebut selesai. Distribusi hasil produksi tidak dicatat (bagian 5.2). Pesanan dapat Dibatalkan sebelum Dalam Produksi. |
| Rencana Produksi *(usulan)* | Draft, Direncanakan, Berjalan, Selesai, Dibatalkan | Staff Produksi menyusun rencana (Draft), menetapkannya (Direncanakan), lalu menjadi Berjalan saat produksi dicatat dan Selesai ketika seluruh produksi pada rencana selesai. |
| Produksi *(usulan)* | Berjalan, Selesai | Staff Produksi mencatat realisasi produksi dan menyelesaikannya setelah jumlah aktual dan penggunaan bahan dicatat. |
| Pengadaan *(usulan)* | Draft, Dipesan, Dikonfirmasi, Diterima Sebagian, Diterima, Dibatalkan | Admin membuat pengadaan (Draft), mencatatnya sebagai Dipesan, lalu mencatat konfirmasi supplier (Dikonfirmasi). Status menjadi Diterima Sebagian atau Diterima berdasarkan penerimaan bahan yang dicatat Gudang. Pengadaan dapat Dibatalkan sebelum ada penerimaan. |
| Permintaan Bahan | Draft, Diajukan, Diverifikasi, Ditolak | Staff Produksi membuat permintaan sebagai Draft, kemudian mengajukannya (Diajukan). Staff Gudang memverifikasi (Diverifikasi, stok OUT tercatat) atau menolak (Ditolak, stok tidak berubah). |
| Persediaan Bahan (diturunkan dari data) | Tersedia, Stok Menipis, Habis | Stok bertambah ketika bahan Baik diterima atau dikembalikan, dan berkurang ketika permintaan bahan diverifikasi. Tersedia: stok ≥ stok minimum. Stok Menipis: 0 < stok < stok minimum. Habis: stok = 0. Kedaluwarsa tidak menjadi status persediaan karena tidak ada pelacakan batch (OQ-R06). |

## 10. Aturan Bisnis

- **BR-01:** Satu user hanya punya satu role (satu role per akun).
- **BR-02:** Data tidak dihapus permanen, cukup dinonaktifkan (soft delete).
- **BR-03:** Stok bahan selalu dihitung dari riwayat transaksi, bukan diedit langsung. Koreksi memakai ADJUSTMENT.
- **BR-04:** Hanya bahan berkondisi Baik yang masuk stok tersedia. Bahan berkondisi Rusak, Kedaluwarsa, atau Tidak Sesuai tidak menambah stok (penanganan retur dibahas di OQ-R10).
- **BR-05:** Stok bahan berkurang saat permintaan bahan diverifikasi Gudang, bukan saat pemakaian aktual dicatat.
- **BR-06:** Stok di bawah stok minimum menampilkan peringatan di dashboard saja.
- **BR-07:** Harga hanya disimpan sebagai data transaksi. Tidak ada perhitungan HPP.
- **BR-08:** Supplier tidak punya akun. Konfirmasi supplier dicatat manual oleh internal.
- **BR-09:** Satu rencana produksi boleh melayani beberapa order.
- **BR-10:** Konversi satuan hanya untuk satuan baku yang ditentukan sistem [OQ-14].
- **BR-11:** User hanya dapat menjalankan aktivitas yang menjadi hak role-nya. Hak untuk melihat data juga mengikuti pembatasan tersebut.
- **BR-12 (usulan):** Selisih antara bahan yang dikeluarkan (BR-05) dan pemakaian aktual dikembalikan ke stok melalui transaksi IN berreferensi permintaan bahan terkait (FR-42), atau dikoreksi dengan ADJUSTMENT bila bahan tidak dapat dipakai lagi (OQ-R04).
- **BR-13 (usulan):** Stok bahan tidak boleh bernilai negatif. Jumlah yang diverifikasi Gudang tidak boleh melebihi stok tersedia (OQ-R05).
- **BR-14:** Password tidak pernah ditampilkan kembali oleh sistem. Password sementara dari proses reset wajib diganti user pada login berikutnya.

## 11. Perhitungan Gizi

Fitur informasi gizi digunakan untuk mencatat dan menampilkan rekap kandungan gizi pada setiap menu yang dikelola dalam sistem FoodFlow. Informasi gizi diinput secara manual oleh Staff Produksi ketika menambahkan atau mengubah data menu.

Data informasi gizi yang dicatat per satu porsi menu meliputi (keempatnya wajib agar status menjadi Tersedia):

- Kalori (kkal)
- Protein (gram)
- Lemak (gram)
- Karbohidrat (gram)

Sistem menyimpan informasi gizi tersebut sebagai bagian dari data menu (tabel `products`) sehingga dapat ditampilkan kembali dan dipantau oleh Owner/Manager melalui fitur Monitoring Informasi Gizi.

Kolom gizi pada tabel `ingredients` bersifat opsional dan hanya referensi; sistem tidak menggunakannya untuk menghitung gizi menu pada versi ini. Apakah gizi menu perlu dihitung otomatis dari komposisi bahan menjadi keputusan terbuka (OQ-R01).

## 12. Model Data

Model data sistem FoodFlow dirancang berdasarkan ERD revisi yang mencakup pengelolaan pengguna, data master, pesanan, pengadaan, persediaan bahan baku, serta perencanaan dan pencatatan produksi. Perubahan dari versi 1.0 ditandai pada kolom Catatan.

| Tabel | Isi (field utama) | Fungsi | Catatan |
|---|---|---|---|
| `users` | id, name, email, password (hash), role, status | Menyimpan data akun user dan hak akses. | |
| `password_reset_requests` | id, user_id, email, status, requested_at, processed_by, processed_at, notes | Menyimpan permintaan reset password dan statusnya. | **Baru** |
| `customers` | id, name, phone, email, address, status | Menyimpan data pelanggan. | |
| `suppliers` | id, name, phone, email, address, status | Menyimpan data supplier bahan baku. | Nama tabel jamak; typo `addres` diperbaiki |
| `products` | id, name, description, unit, selling_price, calories, protein, fat, carbohydrates, status | Menyimpan data menu atau produk beserta gizi per porsi. | Kolom gizi **ditambahkan**. Status kelengkapan gizi diturunkan, tidak disimpan |
| `ingredients` | id, name, unit, minimum_stock, calories, protein, fat, carbohydrates, status | Menyimpan data bahan baku, batas minimum stok, dan informasi gizi referensi (opsional). | |
| `product_ingredients` | id, product_id, ingredient_id, quantity_per_unit, unit | Menyimpan komposisi bahan baku untuk setiap produk. | |
| `orders` | id, customer_id, created_by, order_number, order_date, delivery_date, status, notes | Menyimpan data pesanan pelanggan. | |
| `order_details` | id, order_id, product_id, quantity, price, subtotal, notes | Menyimpan rincian produk dalam pesanan. | |
| `production_plans` | id, created_by, plan_number, plan_date, production_date, status, notes | Menyimpan rencana produksi. | `order_id` **dihapus** (diganti tabel penghubung) |
| `production_plan_orders` | id, production_plan_id, order_id | Menghubungkan satu rencana produksi ke beberapa pesanan (BR-09). | **Baru** |
| `production_plan_details` | id, production_plan_id, product_id, planned_quantity | Menyimpan rincian produk dan jumlah yang direncanakan untuk diproduksi. | |
| `productions` | id, production_plan_detail_id, created_by, production_number, production_date, actual_quantity, status, notes | Menyimpan realisasi pelaksanaan produksi. | |
| `production_material_usages` | id, production_id, ingredient_id, created_by, actual_quantity, unit, notes | Menyimpan penggunaan aktual bahan baku per produksi. | `production_plan_id` diganti `production_id`; kolom `roles` diganti `notes` |
| `purchase_orders` | id, supplier_id, created_by, po_number, order_date, expected_date, status, confirmed_date, confirmation_notes, notes | Menyimpan data pengadaan bahan baku kepada supplier beserta konfirmasi supplier. | `order_number` diganti `po_number`; kolom konfirmasi **ditambahkan** (FR-20) |
| `purchase_order_details` | id, purchase_order_id, ingredient_id, quantity, unit, price, subtotal | Menyimpan rincian bahan yang dipesan dalam pengadaan. | |
| `material_receipts` | id, purchase_order_id, received_by, receipt_number, receipt_date, notes | Menyimpan data penerimaan bahan baku. | Typo `recived_by` diperbaiki. Status penerimaan berada di `purchase_orders` |
| `material_receipt_details` | id, material_receipt_id, purchase_order_detail_id, ingredient_id, received_quantity, unit, condition, expiry_date, notes | Menyimpan rincian jumlah dan kondisi bahan yang diterima. | `expiry_date` opsional dan hanya informasi (tanpa FEFO). Typo `recived_quantity` diperbaiki |
| `material_requests` | id, production_plan_id, requested_by, verified_by, request_number, request_date, verified_date, status, notes | Menyimpan permintaan bahan baku dari produksi kepada gudang. | `request_number` dan `verified_date` **ditambahkan** |
| `material_request_details` | id, material_request_id, ingredient_id, request_quantity, approved_quantity, unit, notes | Menyimpan rincian bahan yang diminta dan yang diverifikasi/dikeluarkan. | Kolom `id_material_request_id` diperbaiki menjadi `id`, `material_request_id`. `issued_quantity` dihapus karena verifikasi = pengeluaran (FR-31) |
| `ingredient_stock_transactions` | id, ingredient_id, created_by, transaction_type, quantity, transaction_date, reference_type, reference_id, notes | Mencatat transaksi perubahan stok bahan baku. | `quantity` positif untuk IN/OUT; untuk ADJUSTMENT bertanda (positif menambah, negatif mengurangi) |

**Ditunda:** `product_stock_transactions` (id, product_id, created_by, transaction_type, quantity, reference_type, reference_id, notes) tidak termasuk versi ini (OQ-R02).

**Nilai `reference_type` pada transaksi stok:** `material_receipt_detail` (IN), `material_request` (OUT), `material_return` (IN, usulan), `adjustment` (ADJUSTMENT).

## 13. Milestone (dari Project Charter)

| Minggu | Milestone |
|:---:|---|
| 4 | Finalisasi proposal produk |
| 6 | Perencanaan dan perancangan selesai (WBS, ERD, UI/UX, activity diagram) |
| 8 | Milestone 1: prototipe/rancangan utama |
| 9-11 | Pengembangan fitur MVP, integrasi, testing awal |
| 12 | Milestone 2: produk fungsional end-to-end |
| 13-15 | Penyempurnaan dan pengujian |
| 16 | Milestone 3: demo final dan penutupan |

Fitur yang masuk MVP belum ditentukan [OQ-02]. Fase pengembangan hanya 3 minggu (minggu 9-11) untuk sekitar 40 kebutuhan fungsional dan 21 tabel, sehingga penentuan MVP perlu segera dilakukan.

**Usulan prioritas (belum disepakati)**

| Prioritas | Kebutuhan |
|---|---|
| Wajib (MVP) | FR-01 s.d. FR-08 (autentikasi, bahan baku, menu, komposisi); FR-21 s.d. FR-25 (penerimaan dan stok); FR-30 s.d. FR-32 (permintaan bahan); FR-15 s.d. FR-17 dan FR-41 (rencana produksi dan kebutuhan bahan); FR-33 s.d. FR-35 (produksi dan pemakaian aktual); FR-27 dan FR-38 (peringatan dan monitoring stok) |
| Sebaiknya | FR-09, FR-10, FR-12 s.d. FR-14, FR-18 s.d. FR-20 (supplier, pelanggan, pesanan, pengadaan); FR-26, FR-29, FR-42 (adjustment, riwayat, pengembalian); FR-11, FR-36 (data gizi menu); FR-37 |
| Terakhir | FR-39 (monitoring gizi); FR-40 (cetak laporan) |

## 14. Open Questions

OQ-02 dan OQ-14 mempertahankan nomor dari daftar tim. Pertanyaan baru memakai awalan **OQ-R** agar tidak bentrok dengan nomor lain.

| ID | Pertanyaan | Dasar / usulan sementara |
|---|---|---|
| OQ-02 | Fitur apa yang masuk MVP? | Lihat usulan prioritas pada bagian 13. |
| OQ-14 | Satuan baku apa saja dan bagaimana aturan konversinya? | Belum ada; tabel konversi belum ada di model data. |
| OQ-R01 | Gizi menu diinput manual atau dihitung otomatis dari komposisi bahan? | Versi 1.0 saling bertentangan. Baseline sementara: manual per porsi (bagian 11, FR-11). Jika otomatis, perlu basis gizi per bahan (misalnya per 100 g) dan perubahan FR-11, FR-36, KNF-10. |
| OQ-R02 | Apakah stok produk jadi perlu dicatat? | Baseline sementara: ditunda (bertentangan dengan 5.2 jika tetap ada). |
| OQ-R03 | Apakah daftar status usulan pada bagian 9 sudah sesuai (pesanan, rencana produksi, produksi, pengadaan)? | Usulan sementara. |
| OQ-R04 | Bagaimana sisa bahan yang sudah dikeluarkan tetapi tidak terpakai ditangani? | Usulan: dikembalikan lewat transaksi IN (FR-42, BR-12). Tanpa ini stok terlanjur berkurang dan hilang dari catatan. |
| OQ-R05 | Bolehkah stok negatif, atau verifikasi dibatasi sampai stok tersedia? | Usulan: dibatasi (BR-13). |
| OQ-R06 | Bagaimana bahan yang kedaluwarsa setelah disimpan dicatat? | Usulan: ADJUSTMENT (FR-26). Status stok "Kedaluwarsa" dihapus karena tidak ada pelacakan batch/FEFO. |
| OQ-R07 | Laporan apa saja yang dibutuhkan Owner/Manager, dalam format apa, dan apakah bisa diunduh PDF selain dicetak? | Usulan tiga laporan pada FR-40. |
| OQ-R08 | Berapa target kinerja dan jumlah pengguna bersamaan yang disepakati? | Usulan: halaman ≤ 3 detik, laporan ≤ 10 detik. |
| OQ-R09 | Apakah nama "MBG" tetap dipakai pada judul dan ringkasan, mengingat fitur khusus MBG dikecualikan? | Bergantung pada konteks proposal. |
| OQ-R10 | Bagaimana penanganan bahan Rusak/Tidak Sesuai (retur, penggantian, atau hanya dicatat)? | Saat ini hanya dicatat dan tidak menambah stok. |

---

## Lampiran A. Ringkasan Perubahan dari Versi 1.0

| No | Temuan pada versi 1.0 | Perubahan di versi 1.1 |
|---|---|---|
| 1 | Dua bagian bernomor "7"; FR-30 merujuk "bagian 10" yang keliru | Penomoran bagian menjadi 1 s.d. 14 dan rujukan diperbaiki ke bagian 12 |
| 2 | Penanda "[Usulan]" tersisa pada FR-30 | Dihapus; fitur permintaan bahan sudah ada di alur, matriks, BR, dan model data |
| 3 | FR-32 dan FR-36 hilang; FR-25 s.d. FR-29 dan FR-31 tanpa judul; bagian Informasi Gizi kosong | FR-32 dan FR-36 diisi, judul dilengkapi; FR-41 dan FR-42 ditambahkan |
| 4 | FR-16 berisi kalimat tentang banyak order | FR-16 diganti isi sebenarnya; kalimat lama dipindah ke FR-18 dan BR-09 |
| 5 | Gizi: ruang lingkup menyiratkan dihitung, bagian 10 menyatakan manual; `products` tanpa kolom gizi | Baseline manual per porsi; kolom gizi ditambahkan ke `products`; jadi OQ-R01 |
| 6 | Stok produk jadi dikecualikan (5.2) tetapi ada FR-28, FR-29, dan tabel stok produk | Ditunda; FR-28 ditandai ditunda; tabel dikeluarkan dari model (OQ-R02) |
| 7 | Matriks: Monitoring bernilai K; Admin tanpa akses Permintaan bahan | Semua baris Monitoring menjadi L; Admin menjadi L pada Permintaan bahan |
| 8 | Lupa Password mengirim password via email padahal integrasi email dikecualikan; tidak ada tabel reset | Password sementara disampaikan manual di luar sistem dan wajib diganti; tabel `password_reset_requests` ditambahkan |
| 9 | `production_plans.order_id` tunggal padahal satu rencana bisa melayani banyak order | Diganti tabel penghubung `production_plan_orders` |
| 10 | `production_material_usages` mengacu rencana produksi, bukan produksi; kolom `roles` janggal | Diganti `production_id`; `roles` menjadi `notes` |
| 11 | Status "Kedaluwarsa" tanpa field tanggal kedaluwarsa | Status dihapus dari persediaan; `expiry_date` opsional pada detail penerimaan (OQ-R06) |
| 12 | `approved_quantity` dan `issued_quantity` padahal verifikasi dan pengeluaran satu langkah | `issued_quantity` dihapus; status "Disetujui" menjadi "Diverifikasi" agar seragam dengan FR-31 |
| 13 | Status "Penerimaan Bahan" menyebut "setelah permintaan disetujui" dan tidak ada kolom statusnya | Status dipindah ke Pengadaan (Diterima Sebagian, Diterima); kolom konfirmasi ditambahkan |
| 14 | BR-04 menyebut "ditolak/rusak", bukan kategori kondisi yang ada | BR-04 dan FR-23 disebut jelas: hanya kondisi Baik yang menambah stok |
| 15 | Status pesanan, rencana produksi, produksi, dan pengadaan tidak didefinisikan | Diusulkan di bagian 9 (OQ-R03) |
| 16 | Sisa bahan setelah verifikasi tidak punya mekanisme kembali; stok negatif tidak diatur | BR-12, BR-13, FR-42 (usulan; OQ-R04, OQ-R05) |
| 17 | Tidak ada pengecekan kebutuhan vs stok padahal alur bergantung padanya | FR-41 ditambahkan |
| 18 | "Laporan" tidak didefinisikan | Tiga laporan diusulkan pada FR-40 (OQ-R07) |
| 19 | KNF-03 tidak terukur; KNF-10 tidak lengkap; KNF-02 salah kategori; tidak ada hashing/sesi/backup | KNF-03 diberi target usulan; KNF-10 ditulis ulang; KNF-02 menjadi Integritas Data; KNF-11 dan KNF-12 ditambahkan |
| 20 | Typo field dan nama tabel tidak konsisten (`addres`, `recived`, `supplier`, `id_material_request_id`, `order_number` ganda) | Diperbaiki; `supplier` menjadi `suppliers`, `purchase_orders.order_number` menjadi `po_number` |
| 21 | `ingredient_stock_transactions` tidak menjelaskan tanda untuk ADJUSTMENT | Ditetapkan: ADJUSTMENT bertanda |
| 22 | Istilah tidak seragam ("Bahan Makanan", "Tim Produksi") | Diseragamkan menjadi "Bahan Baku" dan "Staff Produksi" |
| 23 | Urutan alur: monitoring Owner sebelum monitoring Gudang; tidak ada langkah cek stok | Urutan diperbaiki dan langkah cek kecukupan stok ditambahkan |
| 24 | MVP belum ditentukan dengan jendela pengembangan hanya 3 minggu | Catatan risiko dan usulan prioritas MVP ditambahkan (OQ-02) |
