UC-01: Login
ID
UC-01
Use Case Name
Login
Importance Level
Tinggi (High)
Primary Actor
User: Owner/Manager, Staff Produksi, Staff Gudang/Inventory, Admin/Staff Operasional
Use Case Type
Detail, Essential
Stakeholder & Interest
User ingin mengakses sistem sesuai hak akses yang dimiliki.
Admin memastikan hanya pengguna yang memiliki akun terdaftar yang dapat mengakses sistem.
Brief Description
User melakukan proses autentikasi menggunakan username dan password yang telah dibuat atau didaftarkan oleh Admin untuk masuk ke dalam sistem.
Trigger
User membuka aplikasi dan memilih menu Login.
Type
External
Normal Flow of Events
User membuka website sistem.
Sistem menampilkan halaman Login.
User memasukkan username dan password.
User menekan tombol Login.
Sistem memverifikasi data akun.
Jika data valid, sistem membuat sesi Login.
Jika tidak wajib mengganti password, sistem menampilkan halaman utama sesuai role user.
Jika wajib mengganti password, sistem mengarahkan user ke halaman Ganti Password.
Alternative Flow
5a. Username atau password salah: sistem menampilkan pesan kesalahan.
5b. Akun tidak aktif: sistem menolak proses login dan menampilkan notifikasi.
5c. User memilih Lupa Password: user memilih menu Lupa Password dan sistem menjalankan UC-02 Lupa Password

UC-02: Lupa Password
ID
UC-02
Use Case Name
Lupa Password
Importance Level
Tinggi (High)
Primary Actor
User: Owner/Manager, Staff Produksi, Staff Gudang/Inventory, Admin/Staff Operasional
Use Case Type
Detail, Essential
Stakeholder & Interest
User ingin mendapatkan kembali akses ke akun ketika lupa password. 
Admin memastikan proses perubahan password dilakukan melalui verifikasi email yang terdaftar. 
Brief Description
Memungkinkan pengguna yang lupa password untuk mengajukan permintaan pemulihan password dengan memasukkan alamat email yang terdaftar pada akun. Sistem meneruskan notifikasi permintaan pemulihan password kepada admin. Admin kemudian membuat password sementara dan mengirimkannya ke alamat email pengguna yang terdaftar. 
Trigger
User lupa password dan memilih menu “Lupa Password?” pada halaman Login. 
Type
External
Normal Flow of Events
User memilih “Lupa Password?” pada halaman Login.
Sistem menampilkan form untuk memasukkan email pemulihan.
User memasukkan email yang telah terdaftar.
Sistem memverifikasi email pengguna.
Sistem mengirimkan permintaan pemulihan password kepada Admin.
Admin menerima dan memeriksa permintaan pemulihan password.
Admin membuat password baru untuk user.
Admin mengirimkan password baru ke email user yang terdaftar.
User menerima email yang berisi password baru.
User kembali ke halaman Login dan memasukkan email serta password baru.
Sistem memverifikasi data login dan memberikan akses kepada user.
Alternative Flow
4a. Email tidak terdaftar: Sistem menampilkan notifikasi bahwa email tidak ditemukan. User kembali ke langkah 3 untuk memasukkan email yang terdaftar.
7a. Admin tidak dapat memproses permintaan: Admin menginformasikan kepada user bahwa permintaan pemulihan password belum dapat diproses.

UC-03: Logout
ID
UC-03 
Use Case Name
Logout
Importance Level
Tinggi (High)
Primary Actor
User: Owner/Manager, Staff Produksi, Staff Gudang/Inventory, Admin/Staff Operasional
Use Case Type
Detail, Essential
Stakeholder & Interest
User ingin keluar dari sistem dengan aman setelah selesai menggunakan website sistem.
Sistem memastikan sesi user berakhir sehingga akun tidak dapat diakses oleh orang lain melalui sesi yang sama. 
Brief Description
User mengakhiri sesi penggunaan sistem dengan memilih menu Logout. Sistem menghapus atau mengakhiri sesi user dan mengarahkan user kembali ke halaman Login. 
Trigger
User memilih menu atau tombol Logout.
Type
External
Normal Flow of Events
User sedang berada di dalam sistem.
User memilih menu Logout.
Sistem meminta konfirmasi Logout.
User mengkonfirmasi Logout.
Sistem mengakhiri sesi user.
 Sistem mengarahkan user ke halaman Login.
Alternative Flow
3a. User membatalkan Logout: Sistem membatalkan proses dan tetap menampilkan halaman yang sedang dibuka.
5a. Gagal mengakhiri sesi: Sistem menampilkan pesan kesalahan dan meminta user mencoba kembali.

UC-04: Mengelola Data Bahan Baku 
ID
UC-04
Use Case Name
Mengelola Data Bahan Baku 
Importance Level
Tinggi (High)
Primary Actor
User: Staff Gudang/Inventory, Admin/Staff Operasional 
Use Case Type
Detail, Essential
Stakeholder & Interest
User (Staff Gudang dan Admin) ingin memastikan data bahan baku sesuai dengan kondisi gudang. 
Sistem ingin memastikan data bahan baku tersimpan secara terstruktur. 
Brief Description
User mengelola informasi bahan baku, seperti menambah, melihat, mengubah, dan menghapus data bahan baku. 
Trigger
User memilih menu Data Bahan Baku. 
Type
External
Normal Flow of Events
Aktor membuka menu Data Bahan Baku.
Sistem menampilkan daftar bahan baku.
Aktor memilih tambah, lihat, ubah, atau hapus data.
Aktor memasukkan atau mengubah informasi bahan baku.
Sistem melakukan validasi data.
Sistem menyimpan perubahan.
Sistem menampilkan data bahan baku terbaru. 
Alternative Flow
5a. Data tidak lengkap atau tidak valid, sistem menampilkan pesan kesalahan.
6a. Data gagal disimpan, sistem menampilkan notifikasi kegagalan. 

UC-05: Mencatat Penerimaan Bahan 
ID
UC-05
Use Case Name
Mengelola Data Bahan Baku 
Importance Level
Tinggi (High)
Primary Actor
User: Staff Gudang/Inventory
Use Case Type
Detail, Essential
Stakeholder & Interest
User (Staff Gudang) ingin mencatat setiap bahan yang diterima secara akurat. 
Owner/Manager ingin mengetahui riwayat penerimaan bahan. 
Brief Description
Staff Gudang mencatat bahan baku yang diterima dari supplier ke dalam sistem. 
Trigger
Bahan baku diterima oleh gudang. 
Type
External
Normal Flow of Events
Staff Gudang membuka menu Penerimaan Bahan.
Sistem menampilkan form penerimaan.
Staff memilih data pengadaan terkait jika penerimaan berasal dari purchase order. 
Staff memasukkan bahan, jumlah yang diterima, satuan, dan kondisi bahan. 
Sistem menjalankan UC-05 Memeriksa Bahan. 
Staff menyimpan hasil penerimaan berdasarkan hasil pemeriksaan.
Sistem menyimpan data penerimaan.
Untuk jumlah bahan yang dinyatakan diterima, sistem mencatat transaksi stok masuk pada UC-06.
Sistem menampilkan notifikasi bahwa penerimaan berhasil dicatat.
Alternative Flow
3a. Tidak ada pengadaan terkait: sistem mengizinkan pencatatan penerimaan tanpa purchase order hanya jika aturan operasional mengizinkannya.
4a. Data penerimaan tidak lengkap: sistem meminta Staff melengkapi data.
5a. Bahan tidak sesuai: sistem mencatat kondisi bahan dan memisahkan jumlah yang ditolak dari jumlah yang diterima.
7a. Data gagal disimpan: sistem menampilkan pesan kegagalan dan tidak menambahkan stok.

UC-06: Memeriksa Bahan 
ID
UC-06
Use Case Name
Memeriksa Bahan 
Importance Level
Tinggi (High)
Primary Actor
User: Staff Gudang/Inventory
Use Case Type
Detail, Essential
Stakeholder & Interest
Staff Gudang ingin memastikan bahan sesuai jenis, jumlah, dan kualitas. 
Owner/Manager ingin memastikan bahan yang digunakan memenuhi standar. 
Brief Description
Staff Gudang memeriksa bahan yang diterima sebelum jumlah yang memenuhi syarat dimasukkan ke stok. 
Trigger
Bahan baku diterima oleh gudang. 
Type
External
Normal Flow of Events
Staff Gudang membuka menu Penerimaan Bahan
Sistem menampilkan bahan yang akan diperiksa.
Staff memeriksa jenis dan jumlah bahan.
Staff memeriksa kondisi atau kualitas bahan.
Staff memasukkan hasil pemeriksaan.
Sistem memvalidasi hasil pemeriksaan.
Sistem menyimpan status bahan, yaitu sesuai atau tidak sesuai.
Sistem mengirimkan jumlah yang diterima dan layak ke proses pencatatan stok masuk.
Alternative Flow
4a. Bahan rusak atau tidak sesuai: Staff mencatat jumlah dan kondisi bahan yang ditolak.
6a. Hasil pemeriksaan belum lengkap: sistem meminta Staff melengkapi data.
7a. Hasil pemeriksaan gagal disimpan: sistem menampilkan pesan kesalahan.

UC-07: Mencatat Stok Masuk 
ID
UC-07
Use Case Name
Mencatat Stok Masuk 
Importance Level
Tinggi (High)
Primary Actor
User: Staff Gudang/Inventory
Use Case Type
Detail, Essential
Stakeholder & Interest
Staff Gudang ingin memastikan penambahan stok tercatat. 
Owner/Manager ingin mengetahui jumlah bahan yang tersedia. 
Brief Description
Staff Gudang mencatat penambahan stok bahan baku berdasarkan penerimaan yang telah diperiksa atau transaksi masuk lain yang sah. 
Trigger
Bahan dinyatakan diterima dan layak digunakan. 
Type
External
Normal Flow of Events
Sistem menerima data bahan yang dinyatakan layak dari UC-06.
Sistem menampilkan rincian bahan dan jumlah yang akan masuk.
Staff Gudang memeriksa rincian transaksi.
Sistem memvalidasi bahan dan jumlah.
Sistem menyimpan transaksi masuk pada riwayat transaksi stok bahan.
Sistem menghitung ulang stok berdasarkan transaksi yang tercatat.
Sistem menampilkan stok terbaru.
Alternative Flow
4a. Jika bahan belum terdaftar, sistem menampilkan notifikasi bahwa bahan belum terdaftar dan meminta pengguna menambahkan data bahan melalui menu Kelola Bahan sebelum melanjutkan transaksi. 
4b. Jika jumlah bahan tidak valid, sistem menolak transaksi dan menampilkan pesan kesalahan agar pengguna memperbaiki jumlah bahan. 
5a. Jika transaksi penerimaan bahan sudah pernah dikonfirmasi dan dicatat ke stok, sistem mencegah pencatatan ulang agar jumlah stok tidak bertambah dua kali. 
5b. Penyimpanan gagal: sistem tidak mengubah stok dan menampilkan notifikasi. 

UC-08: Mencatat Stok Keluar 
ID
UC-08
Use Case Name
Mencatat Stok Keluar
Importance Level
Tinggi (High)
Primary Actor
User: Staff Gudang/Inventory
Use Case Type
Detail, Essential
Stakeholder & Interest
Staff Gudang ingin mencatat pengeluaran bahan.
Staff Produksi membutuhkan bahan untuk produksi. Owner/Manager ingin mengetahui sisa stok bahan.  
Brief Description
Staff Gudang mencatat bahan yang benar-benar dikeluarkan dari gudang berdasarkan permintaan bahan yang disetujui. 
Trigger
Terdapat permintaan bahan yang siap dipenuhi. 
Type
External
Normal Flow of Events
Staff Gudang membuka menu Permintaan Bahan.
Sistem menampilkan permintaan yang menunggu pemenuhan.
Staff memilih permintaan yang akan dipenuhi.
Sistem menampilkan bahan dan jumlah yang disetujui.
Staff memasukkan jumlah bahan yang benar-benar dikeluarkan.
Sistem memeriksa ketersediaan stok.
Sistem menyimpan jumlah aktual yang dikeluarkan.
Sistem mencatat transaksi keluar pada riwayat transaksi stok bahan.
Sistem memperbarui informasi stok berdasarkan transaksi.
Sistem menampilkan status pemenuhan permintaan.
Alternative Flow
6a. Stok tidak mencukupi: sistem menolak pengeluaran atau meminta jumlah disesuaikan.
5a. Jumlah yang dikeluarkan berbeda dari jumlah yang disetujui: sistem meminta konfirmasi sesuai aturan operasional.
8a. Pengeluaran sudah pernah dicatat: sistem mencegah pengurangan stok ganda.
7a. Data pengeluaran tidak lengkap: sistem meminta Staff melengkapi data. 

UC-09: Monitoring Stok 
ID
UC-09
Use Case Name
Monitoring Stok
Importance Level
Tinggi (High)
Primary Actor
User: Owner/Manager dan Staff Gudang/Inventory
Use Case Type
Detail, Essential
Stakeholder & Interest
Owner/Manager ingin mengetahui kondisi stok untuk mengambil keputusan. 
Staff Gudang ingin mengetahui jumlah stok bahan secara aktual.   
Brief Description
Sistem menampilkan stok bahan baku berdasarkan riwayat transaksi masuk, keluar, dan penyesuaian stok. 
Trigger
Aktor memilih menu Monitoring Stok. 
Type
External
Normal Flow of Events
Aktor membuka menu Monitoring Stok.
Sistem mengambil data bahan dan riwayat transaksi stok.
Sistem menghitung stok setiap bahan.
Sistem menampilkan daftar bahan dan jumlah stok tersedia.
Sistem menampilkan status stok berdasarkan batas minimum.
Aktor memilih bahan untuk melihat riwayat transaksi.
Alternative Flow
2a. Belum terdapat transaksi stok: sistem menampilkan informasi bahwa stok belum tersedia.
3a. Stok berada di bawah batas minimum: sistem menampilkan peringatan stok rendah.

UC-10: Monitoring Produksi 
ID
UC-10
Use Case Name
Monitoring Produksi
Importance Level
Tinggi (High)
Primary Actor
User: Owner/Manager 
Use Case Type
Detail, Essential
Stakeholder & Interest
Owner/Manager ingin memantau perkembangan dan hasil produksi untuk evaluasi operasional.    
Brief Description
Owner/Manager ingin memantau perkembangan dan hasil produksi untuk evaluasi operasional. 
Trigger
Owner/Manager melihat rencana, status, dan hasil aktual produksi yang tercatat dalam sistem. 
Type
External
Normal Flow of Events
Owner/Manager membuka menu Monitoring Produksi.
Sistem mengambil data rencana dan pelaksanaan produksi.
Sistem menampilkan daftar rencana produksi beserta statusnya.
Owner/Manager memilih data produksi.
Sistem menampilkan menu, jumlah yang direncanakan, jumlah aktual, tanggal, dan status produksi.
Alternative Flow
2a. Belum terdapat data produksi: sistem menampilkan informasi bahwa data belum tersedia.
4a. Data produksi yang dipilih tidak ditemukan: sistem menampilkan pesan kesalahan.

UC-11: Monitoring Informasi Gizi 
ID
UC-11
Use Case Name
Monitoring Informasi Gizi
Importance Level
Tinggi (High)
Primary Actor
User: Owner/Manager 
Use Case Type
Detail, Essential
Stakeholder & Interest
Owner/Manager ingin memantau kandungan gizi menu untuk evaluasi dan pengawasan. 
Brief Description
Sistem menampilkan perkiraan kandungan gizi menu berdasarkan komposisi bahan dan nilai gizi setiap bahan. 
Trigger
Owner/Manager membuka menu Informasi Gizi. 
Type
External
Normal Flow of Events
Owner/Manager membuka menu Informasi Gizi.
Sistem mengambil data menu dan komposisi bahan.
Sistem mengambil nilai gizi dari setiap bahan.
Sistem menghitung perkiraan kandungan gizi berdasarkan komposisi resep.
Sistem menampilkan informasi gizi menu.
Owner/Manager memilih menu tertentu.
Sistem menampilkan rincian kandungan gizi menu tersebut.
Alternative Flow
2a. Komposisi bahan menu belum tersedia: sistem memberi tahu bahwa informasi gizi belum dapat dihitung.
3a. Data gizi bahan belum lengkap: sistem menampilkan peringatan bahwa hasil perhitungan belum lengkap.

UC-12: Mencetak Laporan 
ID
UC-12
Use Case Name
Mencetak Laporan
Importance Level
Tinggi (High)
Primary Actor
User: Owner/Manager 
Use Case Type
Detail, Essential
Stakeholder & Interest
Owner/Manager membutuhkan laporan sebagai bahan evaluasi, dokumentasi, dan pengambilan keputusan. 
Brief Description
Owner/Manager membuat dan mencetak laporan berdasarkan data operasional yang tersedia. 
Trigger
Owner/Manager memilih menu Laporan. 
Type
External
Normal Flow of Events
Owner/Manager membuka menu Laporan.
Sistem menampilkan pilihan jenis laporan.
Owner/Manager memilih jenis dan periode laporan.
Sistem mengambil data sesuai filter.
Sistem menampilkan pratinjau laporan.
Owner/Manager memilih cetak atau ekspor.
Sistem menghasilkan laporan sesuai pilihan.
Alternative Flow
4a. Data pada periode yang dipilih tidak tersedia: sistem menampilkan pemberitahuan.
7a. Proses pembuatan atau pencetakan laporan gagal: sistem menampilkan pesan kesalahan.

UC-13: Mengelola Menu 
ID
UC-13
Use Case Name
Mengelola Menu
Importance Level
Tinggi (High)
Primary Actor
User: Staff Produksi 
Use Case Type
Detail, Essential
Stakeholder & Interest
Staff Produksi ingin melihat dan mengelola menu yang akan diproduksi. 
Owner/Manager ingin memastikan menu yang digunakan sesuai kebutuhan. 
Brief Description
Staff Produksi mengelola data menu yang digunakan dalam perencanaan dan pelaksanaan produksi. 
Trigger
Staff Produksi memilih menu Mengelola Menu. 
Type
External
Normal Flow of Events
Staff Produksi membuka menu Mengelola Menu.
Sistem menampilkan daftar menu.
Staff memilih tambah, lihat, atau ubah data menu.
Staff memasukkan atau mengubah informasi menu.
Sistem memvalidasi data.
Sistem menyimpan perubahan.
Sistem menampilkan data menu terbaru.
Alternative Flow
5a. Data tidak lengkap atau tidak valid: sistem meminta perbaikan.
6a. Menu sudah digunakan dalam transaksi: sistem menolak penghapusan langsung atau menonaktifkan menu agar riwayat tetap terjaga.

UC-14: Merencanakan Produksi 
ID
UC-14
Use Case Name
Merencanakan Produksi
Importance Level
Tinggi (High)
Primary Actor
User: Staff Produksi 
Use Case Type
Detail, Essential
Stakeholder & Interest
Staff Produksi ingin membuat rencana produksi. Owner/Manager ingin memastikan produksi terencana sesuai kebutuhan. 
Brief Description
Staff Produksi membuat rencana produksi berdasarkan tanggal, menu, dan jumlah porsi yang akan dibuat. 
Trigger
Staff Produksi memilih menu Perencanaan Produksi. 
Type
External
Normal Flow of Events
Staff membuka menu Perencanaan Produksi.
Sistem menampilkan form perencanaan.
Staff memasukkan tanggal produksi dan informasi rencana.
Staff memilih satu atau beberapa menu.
Sistem menjalankan UC-15 Menentukan Jumlah Porsi untuk setiap menu.
Sistem menjalankan UC-16 Menentukan Kebutuhan Bahan.
Sistem menampilkan ringkasan rencana dan kebutuhan bahan.
Staff memeriksa hasil perencanaan.
Staff menyimpan rencana produksi.
Sistem menyimpan data rencana beserta detail menu dan jumlah porsinya.
Alternative Flow
4a. Jika menu yang dipilih tidak tersedia atau berstatus tidak aktif, sistem menampilkan notifikasi dan meminta Staff memilih menu lain yang tersedia.
5a. Jika jumlah porsi yang dimasukkan tidak valid, sistem menampilkan pesan kesalahan dan meminta Staff memperbaiki jumlah porsi.
6a. Jika komposisi bahan untuk menu belum tersedia, sistem menampilkan notifikasi bahwa kebutuhan bahan belum dapat dihitung. Staff harus melengkapi komposisi bahan melalui fitur pengelolaan menu sebelum melanjutkan perencanaan produksi.
9a. Jika data perencanaan produksi belum lengkap, sistem menampilkan notifikasi dan meminta Staff melengkapi data yang diperlukan sebelum menyimpan rencana produksi.

UC-15: Menentukan Jumlah Porsi 
ID
UC-15
Use Case Name
Menentukan Jumlah Porsi
Importance Level
Tinggi (High)
Primary Actor
User: Staff Produksi 
Use Case Type
Detail, Essential
Stakeholder & Interest
Staff Produksi ingin menentukan jumlah porsi sesuai kebutuhan produksi. 
Brief Description
Staff Produksi memasukkan jumlah porsi untuk setiap menu dalam rencana produksi. 
Trigger
UC-14 Merencanakan Produksi dijalankan. 
Type
External
Normal Flow of Events
Sistem menampilkan menu yang dipilih.
Staff memasukkan jumlah porsi yang direncanakan.
Sistem memvalidasi jumlah porsi.
Sistem menyimpan jumlah porsi sebagai detail rencana produksi.
Sistem meneruskan data menu dan jumlah porsi ke UC-16 Menentukan Kebutuhan Bahan.
Alternative Flow
3a. Jumlah porsi kosong, nol, atau negatif: sistem meminta Staff memasukkan jumlah yang valid.
3b. Jumlah porsi bukan bilangan bulat: sistem meminta Staff memperbaiki jumlah.

UC-16: Menentukan Kebutuhan Bahan 
ID
UC-16
Use Case Name
Menentukan Kebutuhan Bahan
Importance Level
Tinggi (High)
Primary Actor
User: Staff Produksi 
Use Case Type
Detail, Essential
Stakeholder & Interest
Staff Produksi ingin mengetahui kebutuhan bahan untuk rencana produksi. 
Staff Gudang membutuhkan informasi tersebut untuk menyiapkan bahan. 
Brief Description
Sistem menghitung perkiraan kebutuhan bahan berdasarkan komposisi resep dan jumlah porsi yang direncanakan.  
Trigger
UC-15 Menentukan Jumlah Porsi selesai dilakukan. 
Type
External
Normal Flow of Events
Sistem menerima data menu dan jumlah porsi.
Sistem mengambil komposisi bahan dari data menu.
Sistem menghitung kebutuhan setiap bahan berdasarkan resep dan jumlah porsi.
Sistem menggabungkan kebutuhan bahan yang sama dari beberapa menu dalam satu rencana.
Sistem menampilkan rincian kebutuhan bahan.
Sistem membandingkan kebutuhan bahan dengan stok yang tersedia.
Sistem menampilkan informasi bahan yang cukup dan bahan yang kurang.
Sistem menyimpan rencana produksi beserta detail menu dan jumlah porsi.
Alternative Flow
2a. Komposisi bahan belum tersedia: sistem menampilkan pemberitahuan.
3a. Komposisi atau satuan bahan tidak valid: sistem meminta data resep diperbaiki.
6a. Stok tidak mencukupi: sistem menampilkan kekurangan bahan untuk ditindaklanjuti melalui UC-19 Mengajukan Permintaan.

UC-17: Mencatat Produksi 
ID
UC-17
Use Case Name
Mencatat Produksi
Importance Level
Tinggi (High)
Primary Actor
User: Staff Produksi 
Use Case Type
Detail, Essential
Stakeholder & Interest
Staff Produksi ingin mencatat hasil aktual produksi. Owner/Manager ingin mengetahui jumlah produksi yang benar-benar dihasilkan. 
Brief Description
Staff Produksi mencatat hasil pelaksanaan produksi berdasarkan detail rencana produksi.  
Trigger
Kegiatan produksi selesai atau menghasilkan keluaran yang akan dicatat. 
Type
External
Normal Flow of Events
Staff membuka menu Mencatat Produksi.
Sistem menampilkan detail rencana produksi.
Staff memilih menu yang telah diproduksi.
Staff memasukkan jumlah produksi aktual dan informasi pelaksanaan yang diperlukan.
Sistem memvalidasi data produksi.
Sistem menyimpan catatan produksi aktual.
Sistem memperbarui status produksi.
Jika hasil produksi telah dikonfirmasi selesai, sistem mencatat stok produk jadi masuk satu kali.
Sistem menampilkan hasil pencatatan produksi.
Alternative Flow
3a. Tidak terdapat rencana produksi yang sesuai: sistem meminta Staff memilih rencana yang tersedia.
4a. Jumlah produksi aktual berbeda dari rencana: sistem tetap mencatat jumlah aktual dan menampilkan perbedaannya.
5a. Jumlah aktual tidak valid: sistem meminta Staff memperbaiki data.
8a. Hasil produksi sudah pernah diposting ke stok: sistem mencegah penambahan stok ganda.

UC-18: Mencatat Penggunaan Bahan
ID
UC-18
Use Case Name
Mencatat Penggunaan Bahan
Importance Level
Tinggi (High)
Primary Actor
User: Staff Produksi 
Use Case Type
Detail, Essential
Stakeholder & Interest
Staff Produksi ingin mencatat penggunaan aktual bahan. 
Staff Gudang ingin mengetahui pemakaian bahan untuk evaluasi stok. 
Brief Description
Staff Produksi mencatat jumlah bahan yang benar-benar digunakan dalam suatu kegiatan produksi. 
Trigger
Kegiatan produksi telah menggunakan bahan dan jumlah aktualnya perlu dicatat. 
Type
External
Normal Flow of Events
Staff membuka menu Penggunaan Bahan.
Sistem menampilkan kegiatan produksi yang telah dicatat.
Staff memilih catatan produksi yang sesuai.
Staff memasukkan bahan dan jumlah aktual yang digunakan.
Sistem memvalidasi bahan dan jumlah.
Sistem menyimpan catatan penggunaan bahan.
Sistem menampilkan ringkasan penggunaan bahan untuk dibandingkan dengan kebutuhan rencana.
Alternative Flow
4a. Bahan atau jumlah penggunaan belum lengkap: sistem meminta Staff melengkapi data.
5a. Jumlah penggunaan tidak valid: sistem menolak pencatatan.
5b. Jumlah penggunaan berbeda dari perkiraan: sistem menyimpan jumlah aktual dan menampilkan selisihnya.

UC-19: Mengajukan Permintaan Bahan
ID
UC-19
Use Case Name
Mengajukan Permintaan Bahan
Importance Level
Tinggi (High)
Primary Actor
User: Staff Produksi 
Use Case Type
Detail, Essential
Stakeholder & Interest
Staff Produksi ingin mengajukan kebutuhan bahan. Staff Gudang ingin mengetahui dan memenuhi permintaan yang disetujui. 
Brief Description
Staff Produksi mengajukan permintaan bahan untuk kebutuhan rencana produksi. 
Trigger
Staff Produksi membutuhkan bahan untuk kegiatan produksi. 
Type
External
Normal Flow of Events
Staff membuka menu Permintaan Bahan.
Sistem menampilkan form permintaan.
Staff memilih rencana produksi terkait.
Staff memilih bahan yang dibutuhkan.
Staff memasukkan jumlah dan satuan bahan.
Staff mengirimkan permintaan.
Sistem memvalidasi data permintaan.
Sistem menyimpan permintaan dengan status menunggu verifikasi.
Sistem menampilkan ringkasan dan status permintaan.
Alternative Flow
3a. Rencana produksi belum tersedia: sistem meminta Staff memilih atau membuat rencana terlebih dahulu.
4a. Bahan belum terdaftar: sistem meminta Staff memilih bahan yang tersedia.
7a. Data tidak lengkap atau jumlah tidak valid: sistem meminta perbaikan.
8a. Permintaan gagal disimpan: sistem menampilkan notifikasi kegagalan.

UC-20: Mengelola Data Gizi
ID
UC-20
Use Case Name
Mengelola Data Gizi
Importance Level
Tinggi (High)
Primary Actor
User: Staff Produksi 
Use Case Type
Detail, Essential
Stakeholder & Interest
Staff Produksi ingin memasukkan dan memperbarui data gizi menu. 
Owner/Manager membutuhkan data gizi untuk memantau kandungan gizi menu.  
Brief Description
Memungkinkan Staff Produksi memasukkan, melihat, dan memperbarui informasi kandungan gizi pada menu secara manual. 
Trigger
Staff Produksi membuka menu Pengelolaan Data Gizi. 
Type
External
Normal Flow of Events
Staff Produksi membuka menu Pengelolaan Data Gizi.
Sistem menampilkan daftar menu yang tersedia.
Staff Produksi memilih menu yang akan dikelola data gizinya.
Sistem menampilkan formulir data gizi menu.
Staff Produksi memasukkan atau memperbarui data gizi, seperti kalori, protein, lemak, dan karbohidrat.
Staff Produksi menyimpan data gizi.
Sistem memvalidasi kelengkapan dan format data.
Sistem menyimpan data gizi.
Sistem menampilkan notifikasi bahwa data gizi berhasil disimpan.
Alternative Flow
5a. Jika data gizi belum lengkap, sistem menampilkan notifikasi dan meminta Staff Produksi melengkapi data.
7a. Jika format atau nilai data gizi tidak valid, sistem menampilkan pesan kesalahan dan meminta Staff Produksi memperbaiki data.
8a. Jika penyimpanan gagal, sistem menampilkan notifikasi bahwa data gizi gagal disimpan.

UC-21: Register Akun 
ID
UC-21
Use Case Name
Register Akun
Importance Level
Tinggi (High)
Primary Actor
User: Admin/ Staff Operasional
Use Case Type
Detail, Essential
Stakeholder & Interest
Admin ingin membuat akun bagi pengguna yang berhak menggunakan sistem dan menentukan hak aksesnya.
User membutuhkan akun agar dapat login dan menggunakan fitur sesuai role yang diberikan.  
Brief Description
Admin membuat akun baru dengan memasukkan informasi pengguna dan menentukan role sesuai tugas dan tanggung jawabnya.
Trigger
Admin memilih menu Register Akun. 
Type
External
Normal Flow of Events
Admin membuka menu Register Akun.
Sistem menampilkan formulir pembuatan akun.
Admin memasukkan data pengguna, seperti nama, email, dan password.
Admin menentukan role pengguna, seperti Manager, Staff Produksi, atau Staff Gudang.
Admin mengirimkan formulir pembuatan akun.
Sistem memvalidasi kelengkapan dan format data yang dimasukkan.
Sistem memeriksa apakah email sudah terdaftar.
Sistem menyimpan data akun dan role pengguna.
Sistem menampilkan notifikasi bahwa akun berhasil dibuat.
Alternative Flow
3a. Jika data pengguna belum lengkap, sistem menampilkan notifikasi dan meminta Admin melengkapi data.
6a. Jika format data tidak valid, sistem menampilkan pesan kesalahan dan meminta Admin memperbaiki data.
7a. Jika email sudah terdaftar, sistem menampilkan notifikasi bahwa email telah digunakan dan meminta Admin memasukkan email lain.
8a. Jika penyimpanan gagal, sistem menampilkan notifikasi bahwa akun gagal dibuat.

UC-22: Mengelola Data Pengguna dan Hak Akses
ID
UC-22
Use Case Name
Mengelola Data Pengguna dan Hak Akses 
Importance Level
Tinggi (High)
Primary Actor
User: Admin/ Staff Operasional
Use Case Type
Detail, Essential
Stakeholder & Interest
Admin ingin mengelola data akun dan role pengguna. 
User ingin memperoleh hak akses sesuai tanggung jawabnya.  
Brief Description
Admin melihat dan mengubah data user, mengatur role, mengaktifkan atau menonaktifkan akun, serta mengatur status wajib mengganti password jika fitur tersebut diterapkan. 
Trigger
Admin membuka menu Data Pengguna. 
Type
External
Normal Flow of Events
Admin membuka menu Data Pengguna.
Sistem menampilkan daftar pengguna.
Admin memilih pengguna.
Sistem menampilkan detail akun.
Admin mengubah informasi pengguna, role, atau status akun.
Sistem memvalidasi perubahan.
Sistem menyimpan perubahan.
Sistem menerapkan hak akses sesuai role.
Sistem menampilkan data pengguna terbaru.
Alternative Flow
5a. Admin menentukan password awal akun pengguna.
5b. Sistem menyimpan akun pengguna beserta password yang telah dienkripsi. 
5c. Admin menyampaikan informasi login kepada pengguna melalui cara yang aman. 
6a. Email sudah digunakan oleh akun lain: sistem menolak perubahan.
6b. Data tidak valid: sistem meminta Admin memperbaiki data.
7a. Akun dinonaktifkan: sistem menyimpan status tidak aktif sehingga akun tidak dapat digunakan untuk login.

UC-23: Mengelola Data Menu
ID
UC-23
Use Case Name
Mengelola Data Menu
Importance Level
Tinggi (High)
Primary Actor
User: Admin/ Staff Operasional
Use Case Type
Detail, Essential
Stakeholder & Interest
Admin ingin menjaga data menu tetap benar. 
Staff Produksi membutuhkan data menu untuk perencanaan dan pelaksanaan produksi.   
Brief Description
Admin mengelola data master menu yang digunakan dalam kegiatan operasional.  
Trigger
Admin membuka menu Data Menu.
Type
External
Normal Flow of Events
Admin membuka menu Data Menu.
Sistem menampilkan daftar menu.
Admin memilih tambah, lihat, atau ubah data menu.
Admin memasukkan informasi menu.
Sistem memvalidasi data.
Sistem menyimpan perubahan.
Sistem menampilkan data menu terbaru.
Alternative Flow
5a. Data menu tidak lengkap atau tidak valid: sistem meminta perbaikan.
6a. Menu telah digunakan dalam transaksi: sistem menolak penghapusan langsung atau menonaktifkan menu agar riwayat tetap terjaga.

UC-24: Mengelola Data Bahan Baku
ID
UC-24
Use Case Name
Mengelola Data Bahan Baku
Importance Level
Tinggi (High)
Primary Actor
User: Admin/ Staff Operasional
Use Case Type
Detail, Essential
Stakeholder & Interest
Admin ingin menjaga data dasar bahan baku agar dapat digunakan oleh Staff Gudang dan Staff Produksi.   
Brief Description
Admin mengelola data master bahan baku, termasuk nama, kategori, satuan, dan batas minimum stok
Trigger
Admin membuka menu Data Bahan Baku.
Type
External
Normal Flow of Events
Admin membuka menu Data Bahan Baku.
Sistem menampilkan daftar bahan baku.
Admin memilih tambah, lihat, atau ubah data.
Admin memasukkan informasi bahan baku.
Sistem memvalidasi data.
Sistem menyimpan perubahan.
Sistem menampilkan data terbaru.
Alternative Flow
5a. Data tidak lengkap atau tidak valid: sistem menampilkan pesan kesalahan.
6a. Bahan telah digunakan dalam transaksi: sistem menolak penghapusan langsung atau menonaktifkan bahan agar riwayat transaksi tetap terjaga.

UC-25: Mengelola Data Supplier
ID
UC-25
Use Case Name
Mengelola Data Supplier
Importance Level
Tinggi (High)
Primary Actor
User: Admin/ Staff Operasional
Use Case Type
Detail, Essential
Stakeholder & Interest
Admin ingin memastikan data supplier tersimpan dengan benar untuk mendukung pengadaan bahan. 
Brief Description
Admin mengelola data supplier yang menjadi sumber pengadaan bahan baku. 
Trigger
Admin membuka menu Data Supplier. 
Type
External
Normal Flow of Events
Admin membuka menu Data Supplier.
Sistem menampilkan daftar supplier.
Admin memilih tambah, lihat, atau ubah data supplier.
Admin memasukkan informasi supplier.
Sistem memvalidasi data.
Sistem menyimpan perubahan.
Sistem menampilkan data supplier terbaru.
Alternative Flow
5a. Data supplier tidak lengkap atau tidak valid: sistem meminta perbaikan.
6a. Supplier telah digunakan dalam transaksi pengadaan: sistem menolak penghapusan langsung atau menonaktifkan supplier.

UC-26: Mengelola Data Pelanggan 
ID
UC-26
Use Case Name
Mengelola Data Pelanggan
Importance Level
Tinggi (High)
Primary Actor
User: Admin/ Staff Operasional
Use Case Type
Detail, Essential
Stakeholder & Interest
Admin ingin menjaga data pelanggan untuk mendukung pencatatan dan pengelolaan pesanan. 
Brief Description
Admin mengelola informasi pelanggan yang berkaitan dengan transaksi pemesanan. 
Trigger
Admin membuka menu Data Pelanggan. 
Type
External
Normal Flow of Events
Admin membuka menu Data Pelanggan.
Sistem menampilkan daftar pelanggan.
Admin memilih tambah, lihat, atau ubah data pelanggan.
Admin memasukkan informasi pelanggan.
Sistem memvalidasi data.
Sistem menyimpan perubahan.
Sistem menampilkan data pelanggan terbaru.
Alternative Flow
5a. Data pelanggan tidak lengkap atau tidak valid: sistem meminta perbaikan.
6a. Pelanggan telah memiliki transaksi: sistem menolak penghapusan langsung atau mempertahankan data untuk menjaga riwayat transaksi.

UC-27: Mencatat dan Mengelola Pesanan 
ID
UC-27
Use Case Name
Mencatat dan Mengelola Pesanan
Importance Level
Tinggi (High)
Primary Actor
User: Admin/ Staff Operasional
Use Case Type
Detail, Essential
Stakeholder & Interest
Admin ingin mencatat dan mengelola pesanan secara terstruktur. 
Owner/Manager membutuhkan informasi pesanan untuk memantau operasional. 
Brief Description
Admin mencatat, melihat, dan mengubah pesanan pelanggan beserta menu dan jumlah yang dipesan. 
Trigger
Terdapat pesanan baru atau perubahan pesanan. 
Type
External
Normal Flow of Events
Admin membuka menu Pesanan.
Sistem menampilkan daftar pesanan.
Admin memilih untuk membuat atau mengelola pesanan.
Admin memilih pelanggan.
Admin memasukkan tanggal pesanan, tanggal kebutuhan, dan informasi lainnya.
Admin memilih satu atau beberapa menu beserta jumlahnya.
Sistem menghitung subtotal setiap menu dan total pesanan.
Sistem memvalidasi data pesanan.
Sistem menyimpan pesanan dan detailnya.
Sistem menampilkan ringkasan serta status pesanan.
Alternative Flow
4a. Pelanggan belum tersedia: Admin harus menambahkan data pelanggan terlebih dahulu.
6a. Menu tidak tersedia atau tidak aktif: sistem meminta Admin memilih menu yang tersedia.
8a. Data pesanan tidak lengkap atau jumlah tidak valid: sistem meminta perbaikan.
9a. Pesanan gagal disimpan: sistem menampilkan pesan kesalahan.

UC-28: Membuat Pengadaan Bahan ke Supplier 
ID
UC-27
Use Case Name
Membuat Pengadaan Bahan ke Supplier
Importance Level
Tinggi (High)
Primary Actor
User: Admin/ Staff Operasional
Use Case Type
Detail, Essential
Stakeholder & Interest
Admin ingin membuat pengadaan bahan sesuai kebutuhan. 
Staff Gudang membutuhkan bahan yang tersedia. Owner/Manager ingin memantau pengadaan.  
Brief Description
Admin membuat dan mengelola pesanan pengadaan bahan baku kepada supplier. 
Trigger
Terdapat kebutuhan bahan yang harus dibeli. 
Type
External
Normal Flow of Events
Admin membuka menu Pengadaan Bahan.
Sistem menampilkan daftar dan form pengadaan.
Admin memilih supplier.
Admin memilih bahan yang akan dipesan.
Admin memasukkan jumlah, satuan, dan harga bahan.
Sistem menghitung subtotal setiap bahan dan total pengadaan.
Admin memeriksa dan mengonfirmasi pengadaan.
Sistem memvalidasi data.
Sistem menyimpan data pengadaan beserta detailnya.
Sistem menampilkan nomor dan status pengadaan.
Alternative Flow
3a. Supplier belum tersedia: Admin harus menambahkan data supplier terlebih dahulu.
4a. Bahan belum tersedia: Admin harus menambahkan data bahan terlebih dahulu.
8a. Data pengadaan tidak lengkap atau jumlah tidak valid: sistem meminta perbaikan.
9a. Pengadaan gagal disimpan: sistem menampilkan pesan kesalahan.

