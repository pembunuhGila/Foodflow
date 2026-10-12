PRD FoodFlow
Informasi Dokumen
Keterangan
Versi
1.0 - Revisi berdasarkan use case yang telah ditetapkan
Tanggal
9 Oktober 2026
Status
Draft revisi untuk ditinjau dan disepakati tim

1. Ringkasan PRD FoodFlow
FoodFlow adalah aplikasi berbasis web Sistem Informasi Manajemen Stock dan Gizi pada MBG dan Usaha Catering untuk mengelola data bahan baku, pengadaan, penerimaan dan pemeriksaan bahan, pencatatan stok, pengelolaan menu, perencanaan dan pencatatan produksi, penggunaan bahan, informasi gizi, serta monitoring dan pelaporan. 
2. Tujuan
Membantu pengelolaan data bahan baku dan informasi stok secara terpusat.
Mendukung pencatatan pengadaan, penerimaan, pemeriksaan, stok masuk, dan stok keluar.
Membantu mengintegrasikan pengelolaan operasional semua divisi.
Menyediakan informasi monitoring produksi, stok, dan gizi, serta fasilitas pencetakan laporan bagi Owner/Manager.
3. Pengguna dan Hak Akses
Sistem memiliki empat aktor bisnis/role sesuai diagram use case. Aktor User merupakan generalization dari semua role yang ada di alur proses.
Role / Aktor
Tanggung jawab utama
Admin / Staff Operasional
Bertanggung jawab terhadap pengelolaan data administrasi, pencatatan transaksi operasional. Aktivitas: Register akun, mengelola data pengguna dan hak akses, mengelola data menu, mengelola data bahan baku, mengelola data supplier, mengelola data pelanggan, mencatat dan mengelola pesanan, membuat pengadaan bahan ke supplier.
Staff Gudang / Inventory
Bertanggung jawab terhadap pencatatan dan pemantauan persediaan bahan baku. Aktivitas: Mengelola data bahan baku, mencatat penerimaan bahan, memeriksa bahan, mencatat stok masuk, mencatat stok keluar, monitoring stok.
Staff Produksi (termasuk fungsi ahli gizi)
Role ini menggunakan informasi pesanan, komposisi menu, dan ketersediaan bahan untuk mendukung perencanaan serta pelaksanaan produksi. Aktivitas: Mengelola menu, merencanakan produksi, menentukan jumlah produksi, menentukan jumlah porsi, menentukan kebutuhan bahan, mencatat produksi, mencatat penggunaan bahan, mengajukan permintaan, mengelola data gizi.
Owner / Manager (satu role)
Bertanggung jawab melakukan pemantauan terhadap operasional dan memeproleh informasi untuk mendukung pengambilan keputusan. Aktivitas: Monitoring produksi, monitoring stok, monitoring informasi gizi, mencetak laporan.

Keterangan: K = Kelola, L = Lihat, – = Tidak memiliki akses, Ajukan = Mengajukan permintaan, dan Verifikasi = Memeriksa serta memverifikasi permintaan bahan.
Fitur
Admin
Gudang
Produksi
Owner
Pengguna dan hak akses
K
–
–
–
Bahan baku (master)
K
K
L
L
Menu/produk (master)
K
L
K
L
Komposisi menu dan data gizi
L
–
K
L
Data Supplier
K
L
–
L
Data Pelanggan
K
–
–
L
Order
K
L
L
L
Pengadaan (catatan internal)
K
L
–
L
Penerimaan dan pemeriksaan bahan
L
K
–
L
Stok bahan (masuk, keluar, adjustment)
L
K
L
L
Permintaan bahan
–
verifikasi
ajukan
L
Rencana produksi dan produksi
L
L
K
L
Pencatatan realisasi produk
L
L
K
L
Pencatatan penggunaan bahan aktual
L
L
K
L
Monitoring produksi
L
L
K
L
Monitoring stok
L
K
L
L
Monitoring informasi gizi
L
-
K
L
Pencetakan laporan
-
-
-
K

4. Ketentuan Hak Akses
Setiap akun memiliki satu role yang menentukan hak aksesnya. 
User hanya dapat mengakses menu dan menjalankan aktivitas yang diizinkan untuk role tersebut. 
Hak akses tidak hanya membatasi tindakan mengubah data, tetapi juga membatasi akses untuk melihat data yang berada di luar kewenangan role. 
Pembatasan hak akses harus diterapkan pada sisi server, bukan hanya dengan menyembunyikan menu atau tombol pada antarmuka. 
User yang mencoba membuka halaman atau menjalankan fungsi di luar hak aksesnya akan ditolak oleh sistem. 
Apabila suatu aktivitas terhubung dengan lebih dari satu aktor dalam use case, aktivitas tersebut dapat diakses oleh role-role yang terkait sesuai kewenangannya. 
Aktivitas Mengelola Data Bahan Baku terhubung dengan Staff Gudang/Inventory dan Admin/Staff Operasional. 
Aktivitas Mengelola Menu terhubung dengan Staff Produksi dan Admin/Staff Operasional. 
Role yang tidak memiliki hak terhadap suatu aktivitas tidak otomatis memperoleh akses hanya karena membutuhkan informasi dari aktivitas tersebut. 
5. Ruang Lingkup
5.1 Termasuk dalam Ruang Lingkup
Autentikasi dan User
Login
Lupa Password
Register Akun
Pengelolaan data user dan hak akses.
Pembatasan akses berdasarkan role.
Pengelolaan Data Master
Pengelolaan data bahan baku.
Pengelolaan data menu.
Pengelolaan komposisi bahan pada menu.
Pengelolaan data supplier.
Pengelolaan data pelanggan.
Pengelolaan informasi gizi bahan.
Pengelolaan Pesanan
Pencatatan pesanan pelanggan.
Pengelolaan detail pesanan.
Pencatatan jumlah menu dan informasi pesanan.
Pengelolaan status pesanan.
Pengadaan dan Penerimaan Bahan
Pencatatan pengadaan bahan kepada supplier.
Pencatatan penerimaan bahan.
Pemeriksaan kondisi bahan yang diterima.
Pencatatan bahan yang memenuhi atau tidak memenuhi kondisi penerimaan.
Pengelolaan Stok
Pencatatan stok masuk.
Pencatatan stok keluar.
Perhitungan stok berdasarkan transaksi.
Monitoring ketersediaan bahan.
Peringatan stok di bawah batas minimum pada dashboard.
Perencanaan dan Produksi
Perencanaan produksi.
Penentuan jumlah porsi.
Perhitungan kebutuhan bahan.
Pengajuan permintaan bahan.
Pencatatan realisasi produksi.
Pencatatan penggunaan bahan aktual.
Informasi Gizi
Pengelolaan data gizi bahan.
Perhitungan kandungan gizi menu berdasarkan komposisi bahan.
Monitoring informasi gizi oleh Owner/Manager.
Monitoring dan Pelaporan
Monitoring produksi.
Monitoring stok.
Monitoring informasi gizi.
Pencetakan laporan oleh Owner/Manager.
5.2 Di Luar Ruang Lingkup 
Prediksi permintaan atau kebutuhan bahan berbasis algoritma.
Fitur khusus MBG/SPPG, sekolah, target porsi MBG, atau integrasi dengan sistem MBG.
Pencatatan dan analisis food waste sebagai modul tersendiri.
Pengelolaan beberapa bisnis dalam satu sistem atau multi-tenant. 
Penggunaan satu akun dengan beberapa role. 
Penerapan metode FEFO sebagai fitur khusus. 
Integrasi langsung dengan supplier. 
Pembayaran online dan payment gateway. 
Modul akuntansi dan perhitungan Harga Pokok Produksi (HPP). 
Integrasi email atau WhatsApp untuk notifikasi stok. 
Aplikasi mobile khusus. 
Distribusi hasil produksi dan pencatatan penerimaan hasil produksi. 
Pengelolaan stok produk jadi secara terpisah. 
Audit log terperinci. 
6. Alur Bisnis Utama
Admin/Staff Operasional mengelola data pengguna, supplier, pelanggan, bahan baku, dan menu sesuai hak aksesnya. 
Staff Produksi mengelola menu dan data gizi sesuai aktivitas yang terhubung dengan role tersebut.
 Admin/Staff Operasional mencatat dan mengelola pesanan pelanggan berdasarkan menu, jumlah pesanan, tanggal pemesanan, dan informasi lain yang diperlukan. 
Staff Produksi menyusun rencana produksi dan menentukan jumlah porsi yang akan diproduksi. Berdasarkan jumlah porsi dan komposisi bahan pada menu, sistem menghitung kebutuhan bahan yang diperlukan. Hasil perhitungan digunakan untuk membantu mengetahui kebutuhan bahan sebelum produksi dilaksanakan.
Apabila persediaan bahan tidak mencukupi kebutuhan produksi, Admin/Staff Operasional dapat mencatat pengadaan bahan kepada supplier. 
Staff Gudang/Inventory mencatat penerimaan bahan yang datang dari supplier dan memeriksa kondisi bahan tersebut. Bahan dapat dikategorikan sebagai Baik, Rusak, Kedaluwarsa, atau Tidak Sesuai. 
Staff Produksi mengajukan permintaan bahan kepada Staff Gudang/Inventory berdasarkan kebutuhan produksi. Staff Gudang/Inventory memeriksa permintaan dan mencatat pengeluaran bahan.
 Staff Produksi melaksanakan produksi berdasarkan rencana yang telah dibuat. Setelah produksi berlangsung, Staff Produksi mencatat jumlah produksi aktual dan penggunaan bahan aktual. Data tersebut digunakan untuk membandingkan rencana dengan realisasi produksi. 
Owner/Manager memantau informasi produksi, stok, dan gizi serta mencetak laporan sesuai hak aksesnya. 
Staff Gudang/Inventory melakukan monitoring stok untuk mengetahui kondisi persediaan bahan. 
7. Kebutuhan Fungsional
Autentikasi dan pengguna
ID
Kebutuhan
FR-01- Login
Sistem harus menyediakan fitur Login agar user yang memiliki akun terdaftar dan aktif dapat mengakses sistem sesuai dengan role dan hak aksesnya. 
FR-02 - Lupa Password
Sistem harus menyediakan fitur Lupa Password agar user dapat mengajukan pemulihan password ketika tidak dapat mengakses akun. Proses pemulihan dilakukan melalui permintaan user yang diperiksa dan diproses oleh Admin. 
FR-03 - Logout
Sistem harus menyediakan fitur Logout agar user dapat mengakhiri sesi penggunaan sistem secara aman. 
FR-04 - Register Akun
Sistem harus memungkinkan Admin/Staff Operasional mendaftarkan akun pengguna. 
FR-05 - Pengelolaan Sesi dan Hak Akses
Sistem harus mengelola sesi Login dan menerapkan hak akses berdasarkan role user untuk memastikan setiap pengguna hanya dapat mengakses fungsi dan data yang menjadi kewenangannya. 

Master data
ID
Kebutuhan
FR-06 - Pengelolaan Data Bahan Baku
Sistem harus mendukung pengelolaan data bahan baku, seperti nama bahan, satuan, stok minimum, dan informasi gizi apabila tersedia. Aktivitas ini dapat diakses oleh Staff Gudang/Inventory dan Admin/Staff Operasional.
FR-07 - Pengelolaan Data Menu
Sistem harus memungkinkan Staff Produksi dan Admin/Staff Operasional untuk mengelola data menu sesuai hak akses masing-masing berdasarkan Use Case Diagram. 
FR-08 - Pengelolaan Komposisi Menu
Sistem harus mendukung pencatatan bahan dan jumlah bahan yang diperlukan untuk menghasilkan satu satuan menu. 
FR-09 - Pengelolaan Data Supplier
Sistem harus mendukung pengelolaan informasi supplier yang diperlukan untuk pencatatan pengadaan bahan. 
FR-10 - Pengelolaan Data Pelanggan
Sistem harus mendukung pengelolaan informasi pelanggan yang diperlukan untuk pencatatan pesanan. 
FR-11 - Pengelolaan Data Gizi
Sistem harus memungkinkan Staff Produksi untuk menginput dan mengelola rekap kandungan gizi pada saat menambahkan atau mengubah data menu. 

Order
ID
Kebutuhan
FR-12 - Pencatatan Pesanan
Sistem harus memungkinkan Admin/Staff Operasional mencatat pesanan pelanggan beserta detail menu, jumlah, tanggal, dan informasi yang diperlukan. 
FR-13 - Pengelolaan Pesanan
Sistem harus memungkinkan Admin/Staff Operasional mengelola informasi pesanan sesuai status dan aturan bisnis yang ditetapkan. 
FR-14 - Pencatatan Detail Pesanan
Sistem harus menyimpan hubungan antara pesanan, menu, jumlah, dan harga transaksi apabila harga digunakan dalam proses operasional. 

Rencana produksi
ID
Kebutuhan
FR-15 - Merencanakan Produksi
Sistem harus memungkinkan Staff Produksi membuat rencana produksi berdasarkan kebutuhan operasional. 
FR-16 - Menentukan Jumlah Porsi
Satu rencana dapat dihubungkan ke beberapa order.
FR-17 - Menentukan Kebutuhan Bahan
Sistem harus menghitung kebutuhan bahan berdasarkan jumlah porsi dan komposisi bahan pada menu. 
FR-18 - Keterkaitan Pesanan dan Produksi
Sistem dapat menghubungkan rencana produksi dengan pesanan terkait apabila hubungan tersebut ditetapkan dalam model data dan aturan bisnis. 

Pengadaan (catatan internal)
ID
Kebutuhan
FR-19 - Membuat Pengadaan Bahan ke Supplier
Admin mencatat pengadaan: supplier, tanggal, perkiraan tanggal diterima, daftar bahan, jumlah, satuan, harga, subtotal, catatan.
FR- 20 - Pencatatan Konfirmasi Supplier
Sistem dapat mencatat konfirmasi supplier yang diperoleh Admin/Staff Operasional melalui komunikasi di luar sistem. 

Penerimaan dan Pemeriksaan Bahan
ID
Kebutuhan
FR-21 - Mencatat Penerimaan Bahan
Sistem harus memungkinkan Staff Gudang/Inventory mencatat bahan yang diterima. 
FR-22 - Memeriksa Bahan
Sistem harus mendukung pencatatan kondisi bahan yang diterima, termasuk Baik, Rusak, Kedaluwarsa, atau Tidak Sesuai. 
FR-23 - Pembaruan Stok dari Penerimaan
Sistem harus memperbarui stok tersedia berdasarkan jumlah bahan yang memenuhi aturan penerimaan. Bahan yang tidak memenuhi kondisi penerimaan tidak boleh menambah stok tersedia. 

Pengelolaan Stok
ID
Kebutuhan
FR-24 - Mencatat Stok 
Semua pergerakan stok bahan dicatat sebagai transaksi IN, OUT, atau ADJUSTMENT, lengkap dengan sumber transaksi, tanggal, catatan, dan pencatat.
FR-25
Stok saat ini = total IN - total OUT +/- adjustment.
FR-26
Adjustment dipakai bila stok sistem berbeda dengan kondisi fisik. Stock opname bukan proses utama.
FR-27
Dashboard menampilkan peringatan bila stok di bawah stok minimum. Tidak ada notifikasi email atau WhatsApp.
FR-28
Stok produk jadi dicatat dengan transaksi IN, OUT, ADJUSTMENT. Ini bukan fokus utama karena catering umumnya dibuat sesuai pesanan.
FR-29
Staff Gudang dapat melihat riwayat stok per bahan dan per produk.

Permintaan bahan
ID
Kebutuhan
FR-30 - Mengajukan Permintaan
Staff Produksi mengajukan permintaan bahan ke Gudang [Usulan] (tabel baru, bagian 10).
FR-31 
Staff Gudang memverifikasi permintaan. Saat diverifikasi, stok bahan langsung dikurangi (OUT) dengan keterangan, misalnya 'dikeluarkan untuk produksi, menunggu pencatatan pemakaian'.

Produksi dan Penggunaan Bahan
ID
Kebutuhan
FR-33 - Mencatat Produksi
Staff Produksi mencatat realisasi produksi dari rencana produksi: tanggal, status, catatan.
FR-34 - Mencatat Jumlah Aktual
Sistem membedakan jumlah rencana dan jumlah aktual per menu.
FR-35 - Mencatat Penggunaan Bahan
Pemakaian bahan aktual dicatat per produksi melalui detail produksi.

Informasi Gizi
Dashboard dan laporan
ID
Kebutuhan
FR-37 - Monitoring Produksi
Sistem harus menyediakan informasi monitoring produksi bagi Owner/Manager. 
FR-38 - Monitoring Stok
Sistem harus menyediakan informasi monitoring stok sesuai hak akses Owner/Manager dan Staff Gudang/Inventory. 
FR-39 - Monitoring Gizi
Sistem harus menyediakan fitur monitoring informasi gizi bagi Owner/Manager untuk melihat rekap kandungan gizi menu yang tersimpan dalam sistem. 
FR-40 Mencetak Laporan
Sistem harus memungkinkan Owner/Manager mencetak laporan yang disediakan oleh sistem. 

7. Kebutuhan Non-Fungsional
ID
Kategori
Kebutuhan Non-Fungsional
KNF-01
Keamanan
Sistem harus menerapkan autentikasi pengguna melalui halaman login dan membatasi akses fitur berdasarkan hak akses masing-masing peran. 
KNF-02
Keamanan
Sistem harus memvalidasi data yang dimasukkan pengguna untuk mencegah kesalahan input dan menjaga konsistensi data. 
KNF-03
Kinerja
Sistem harus menampilkan halaman, data persediaan, dan laporan dalam waktu yang wajar pada kondisi jaringan dan beban penggunaan normal. 
KNF-04
Kegunaan
Sistem harus memiliki antarmuka yang sederhana, konsisten, dan mudah dipahami oleh pengguna sesuai dengan perannya. 
KNF-05
Kompatibilitas
Sistem harus dapat diakses melalui peramban web modern, seperti Google Chrome dan Microsoft Edge. 
KNF-06
Responsivitas
Antarmuka sistem harus menyesuaikan ukuran layar perangkat desktop maupun perangkat seluler agar informasi tetap dapat dibaca dengan baik. 
KNF-07
Integritas Data
Sistem harus menjaga konsistensi data persediaan, penggunaan bahan, dan rekap gizi menu agar perubahan data tidak menimbulkan ketidaksesuaian antarbagian. 
KNF-08
Keandalan
Sistem harus memberikan informasi yang sesuai dengan data yang tersimpan dan menampilkan pemberitahuan apabila proses penyimpanan data gagal. 
KNF-09
Pemeliharaan
Struktur aplikasi harus disusun secara terorganisasi agar memudahkan proses pemeliharaan, pengujian, dan pengembangan sistem. 
KNF-10
Akurasi Perhitungan
Sistem harus menghasilkan perhitungan rekap gizi serta aturan perhitungan yang telah ditetapkan. 


8. Status dan Alur
Entitas
Status
Alur status
Akun User
Aktif, Tidak Aktif 
User melakukan login menggunakan akun yang telah terdaftar. Sistem memvalidasi kredensial dan status akun. Jika valid dan aktif, pengguna diarahkan ke halaman sesuai hak aksesnya. Jika tidak valid atau akun tidak aktif, sistem menampilkan pesan kesalahan. 
Permintaan Reset Password / Lupa Password
Diajukan, Diproses, Selesai, Gagal 
User memasukkan email terdaftar melalui fitur Lupa Password. Sistem memproses permintaan reset password untuk ditindaklanjuti oleh Admin. Admin membuat password baru dan mengirimkannya ke email pengguna. Status menjadi Selesai setelah proses berhasil atau Gagal apabila proses tidak dapat diselesaikan. 
Data Bahan Makanan 
Aktif, Tidak Aktif 
Data bahan makanan ditambahkan atau diperbarui oleh user yang memiliki hak akses terkait. Data yang tidak lagi digunakan dapat dinonaktifkan tanpa harus menghapus riwayat transaksi persediaan. 
Permintaan Bahan 
Draft, Diajukan, Disetujui, Ditolak 
Permintaan bahan dibuat dan disimpan sebagai Draft, kemudian diajukan untuk diperiksa oleh pihak yang berwenang. Permintaan selanjutnya disetujui atau ditolak sesuai hasil pemeriksaan. 
Penerimaan Bahan 
Menunggu Penerimaan, Diterima, Penerimaan Sebagian 
Setelah permintaan disetujui dan bahan datang, petugas mencatat jumlah bahan yang diterima. Sistem memperbarui status penerimaan dan menyesuaikan stok berdasarkan jumlah aktual yang diterima. 
Persediaan Bahan 
Tersedia, Stok Menipis, Habis, Kedaluwarsa 
Stok bertambah ketika bahan diterima dan berkurang ketika bahan digunakan dalam proses operasional yang tercakup dalam sistem. Status persediaan ditentukan berdasarkan jumlah stok, batas minimum, dan tanggal kedaluwarsa bahan. 
Data Menu 
Aktif, Tidak Aktif
Tim Produksi menambahkan atau memperbarui data menu serta memasukkan informasi kandungan gizi menu. Menu yang tidak lagi digunakan dapat dinonaktifkan, sedangkan informasi menu yang aktif dapat dilihat oleh user sesuai hak aksesnya. 
Informasi Gizi Menu 
Tersedia, Belum Lengkap 
Saat menambahkan atau memperbarui menu, Tim Produksi memasukkan rekap kandungan gizi, seperti kalori, protein, lemak, dan karbohidrat. Informasi berstatus Tersedia apabila data gizi yang diwajibkan telah dilengkapi dan Belum Lengkap apabila masih ada data yang belum diisi. 


9. Aturan Bisnis
BR-01: Satu user hanya punya satu role.(satu role per akun)
BR-02: Data tidak dihapus permanen, cukup dinonaktifkan (soft delete).
 BR-03: Stok bahan selalu dihitung dari riwayat transaksi, bukan diedit langsung. Koreksi memakai ADJUSTMENT.
BR-04: Bahan ditolak/rusak tidak masuk stok tersedia.
BR-05: Stok bahan berkurang saat permintaan bahan diverifikasi Gudang, bukan saat pemakaian aktual dicatat.
BR-06: Stok di bawah stok minimum menampilkan peringatan di dashboard saja.
BR-07: Harga hanya disimpan sebagai data transaksi. Tidak ada perhitungan HPP.
BR-08: Supplier tidak punya akun. Konfirmasi supplier dicatat manual oleh internal.
BR-09: Satu rencana produksi boleh melayani beberapa order.
BR-10: Konversi satuan hanya untuk satuan baku yang ditentukan sistem [OQ-14].
BR-11: User hanya dapat menjalankan aktivitas yang menjadi hak role-nya. Hak untuk melihat data juga mengikuti pembatasan tersebut.
10. Perhitungan Gizi
Fitur informasi gizi digunakan untuk mencatat dan menampilkan rekap kandungan gizi pada setiap menu yang dikelola dalam sistem FoodFlow. Informasi gizi diinput secara manual oleh Staff Produksi ketika menambahkan atau mengubah data menu.
Data informasi gizi yang dapat dicatat meliputi:
Kalori (kkal)
Protein (gram)
Lemak (gram)
Karbohidrat (gram)
Sistem menyimpan informasi gizi tersebut sebagai bagian dari data menu sehingga dapat ditampilkan kembali dan dipantau oleh Owner/Manager melalui fitur Monitoring Informasi Gizi.
11. Model Data
Model data sistem FoodFlow dirancang berdasarkan ERD revisi yang mencakup pengelolaan pengguna, data master, pesanan, pengadaan, persediaan bahan baku, serta perencanaan dan pencatatan produksi.
Tabel
Isi (field utama)
Fungsi
users
id, name, email, password, role, status
Menyimpan data akun user dan hak akses.
customers
id, name, phone, email, address, status
Menyimpan data pelanggan.
supplier
id, name, phone, email, addres, status
Menyimpan data supplier bahan baku.
products
id, name, description, unit, selling_price, status
Menyimpan data menu atau produk.
ingredients
id, name, unit, minimum_stock, calories, protein, fat, carbohydrates, status
Menyimpan data bahan baku batas minimum stok, dan informasi gizi.
product_ingredients
id, product_id, ingredient_id, quantity_per_unit, unit
Menyimpan komposisi bahanbaku untuk setiap produk.
orders
id, customer_id, created_by, order_number, order_date, delivery_date, status, notes
Menyimpan data pesanan pelanggan.
order_details
id, order_id, product_id, quantity, price, subtotal, notes
Menyimpan rincian produk dalam pesanan
production_plans
id, order_id, created_by, plan_number, plan_date, production_date, status, notes
Menyimpan rencana produksi
production_plan_details
id, production_plan_id, product_id, planned_quantity
Menyimpan rincian produk dan jumlah yang direncanakan untuk diproduksi.
productions
id, production_plan_detail_id, created_by, production_number, production_date, actual_quantity, status, notes
Menyimpan realisasi pelaksanaan produksi.
production_material_usages
id, production_plan_id, ingredient_id, created_by, actual_quantity, unit, roles
Menyimpan penggunaan aktual bahan baku dalam produksi.
purchase_orders
id, supplier_id, created_by, order_number, order_date, expected_date, status, notes
Menyimpan data pengadaan bahan baku kepada supplier.
purchase_order_details
id, purchase_order_id, ingredient_id, quantity, unit, price, subtotal
Menyimpan rincian bahan yang dipesan dalam pengadaan.
material_receipts
id, purchase_order_id, recived_by, receipt_number, receipt_date, notes
Menyimpan data penerimaan bahan baku.
material_receipt_details
id, material_receipt_id, purchase_order_detail_id, ingredient_id, recived_quantity, unit, condition, notes
Menyimpan rincian jumlah dan kondisi bahan yang diterima.
material_requests
id, production_plan_id, requested_by, verified_by, request_date, status, notes
Menyimpan permintaan bahan baku dari produksi kepada gudang.
material_request_details
id_material_request_id, ingredient_id, request_quantity, approved_quantity, issued_quantity, unit, notes
Menyimpan rincian bahan yang diminta, disetujui, dan dikeluarkan.
ingredient_stock_transactions
id, ingredient_id, created_by, transaction_type, quantity, transaction_date, reference_type, reference_id, notes
Mencatat transaksi perubahan stok bahan baku
product_stock_transactions
id, product_id, created_by, transaction_type, quantity, reference_type, reference_id, notes
Mencatat transaksi perubahan stok produk

12. Milestone (dari Project Charter)
Minggu
Milestone
4
Finalisasi proposal produk
6
Perencanaan dan perancangan selesai (WBS, ERD, UI/UX, activity diagram)
8
Milestone 1: prototipe/rancangan utama
9-11
Pengembangan fitur MVP, integrasi, testing awal
12
Milestone 2: produk fungsional end-to-end
13-15
Penyempurnaan dan pengujian
16
Milestone 3: demo final dan penutupan

Fitur yang masuk MVP belum ditentukan [OQ-02].

