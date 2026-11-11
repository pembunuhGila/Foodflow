<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="FoodFlow — Sistem ERP Manajemen Katering Modern & Dapur Produksi. Kelola pesanan katering, resep menu, stok bahan baku, jadwal masak, dan rekapitulasi laporan dalam satu platform terintegrasi.">
    <title>FoodFlow — ERP Manajemen Bisnis Katering & Dapur Produksi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        html {
            scroll-behavior: smooth;
        }

        /* Munculkan perlahan saat di-scroll */
        .reveal,
        .reveal-group>* {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity .55s ease, transform .55s ease;
        }

        .reveal.visible,
        .reveal-group.visible>* {
            opacity: 1;
            transform: none;
        }

        .reveal-group.visible>*:nth-child(2) {
            transition-delay: .08s;
        }

        .reveal-group.visible>*:nth-child(3) {
            transition-delay: .16s;
        }

        .reveal-group.visible>*:nth-child(4) {
            transition-delay: .24s;
        }

        .reveal-group.visible>*:nth-child(5) {
            transition-delay: .32s;
        }

        .reveal-group.visible>*:nth-child(6) {
            transition-delay: .40s;
        }

        @keyframes floatCard {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }


        .float-card {
            animation: floatCard 5.5s ease-in-out infinite;
        }

        @media (prefers-reduced-motion: reduce) {

            .reveal,
            .reveal-group>* {
                opacity: 1;
                transform: none;
                transition: none;
            }

            .float-card {
                animation: none;
            }
        }
    </style>
</head>

<body class="bg-white font-sans text-ink antialiased"
    x-data="{ mobileMenuOpen: false, scrolled: false }"
    @scroll.window="scrolled = window.scrollY > 20">

    @php
    $nav = [['#beranda', 'Beranda'], ['#erp', 'Fitur ERP'], ['#modul', 'Modul Sistem'], ['#profil', 'Profil Dapur'], ['#menu', 'Menu & Paket']];

    $before = [
    'Stok bahan baku dicatat di buku tulis — rawan selisih saat belanja ke pasar',
    'Pesanan katering dicatat lewat chat WA — sering lupa pesanan khusus klien',
    'Kebutuhan gramasi bahan dihitung manual — rentan over-budget atau kekurangan bahan',
    'Jadwal masak di papan tulis — koordinasi chef dan gudang sering miskomunikasi',
    'Laporan rekap bahan dan biaya makan dibuat manual berhari-hari',
    'Tidak ada bukti audit siapa staf yang mengeluarkan bahan baku dari gudang',
    ];
    $after = [
    'Stok bahan baku terpotong otomatis dan tersinkronisasi realtime setiap ada mutasi keluar',
    'Manajemen pesanan katering terpusat dengan rincian paket, porsi, dan jadwal kirim',
    'Kalkulator resep cerdas — otomatis menghitung kebutuhan kg beras, sayur, & daging per batch',
    'Jadwal produksi dapur live — seluruh tim dapur melihat instruksi kerja yang sama',
    'Laporan rekapitulasi produksi & mutasi persediaan siap cetak A4 Portrait kapan saja',
    'Audit trail lengkap mencatat siapa staf yang menyetujui dan mengambil barang gudang',
    ];

    $roles = [
    ['cart', 'Admin & Kasir Katering', [
    'Input pesanan katering (nasi kotak, event, langganan)',
    'Manajemen data master pelanggan & supplier',
    'Kelola Purchase Order pengadaan bahan baku',
    'Approval permohonan reset password staf',
    'Kontrol akses akun & status pegawai',
    ]],
    ['package', 'Staff Gudang / Logistik', [
    'Pantau saldo stok bahan mentah realtime',
    'Verifikasi pengeluaran bahan untuk dapur masak',
    'Kartu mutasi persediaan (Stock Card)',
    'Penyesuaian fisik opname stok berkala',
    'Pemeriksaan fisik bahan masuk dari supplier',
    ]],
    ['book', 'Head Chef & Produksi', [
    'Akses jadwal masak harian dapur katering',
    'Kalkulasi resep & gramasi bahan otomatis',
    'Ajukan permintaan bahan masak ke gudang',
    'Catat realisasi porsi matang & suhu sajian',
    'Input pemakaian riil bahan per batch masak',
    ]],
    ['activity', 'Owner & Manajemen', [
    'Dashboard live omset porsi & status pesanan',
    'Monitoring capaian produksi dapur realtime',
    'Evaluasi kepatuhan komposisi menu',
    'Cetak laporan rekapitulasi produksi PDF',
    'Cetak laporan mutasi persediaan gudang PDF',
    ]],
    ];

    $modules = [
    ['cart', 'Pesanan Katering (Orders)', 'Kelola pesanan katering nasi kotak, prasmanan, dan langganan instansi. Lengkap dengan detail menu, jumlah porsi, jadwal kirim, dan kalkulasi total biaya.', ['Pesanan prasmanan & box', 'Kalkulasi otomatis', 'Tracking status kirim']],
    ['book', 'Resep & Kalkulator Bahan', 'Standarisasi formula resep hidangan katering. Masukkan target jumlah porsi, sistem otomatis menghitung kebutuhan kg beras, daging, dan sayuran.', ['Formula resep standar', 'Kalkulasi gramasi presisi', 'Simulasi kebutuhan masak']],
    ['package', 'Stok Gudang & Mutasi', 'Pantau saldo bahan baku secara realtime. Dilengkapi alert otomatis untuk bahan yang mendekati batas minimum agar tidak kehabisan saat jadwal masak tiba.', ['Stok realtime', 'Peringatan stok minimum', 'Kartu mutasi keluar-masuk']],
    ['truck', 'Purchase Order Supplier', 'Kelola alur pengadaan bahan ke vendor terpercaya — dari pembuatan PO, penerimaan fisik bahan mentah, hingga kontrol standar kualitas kesegaran bahan.', ['Kelola PO supplier', 'Penerimaan fisik bahan', 'Inspeksi mutu & kesegaran']],
    ['clock', 'Jadwal & Realisasi Dapur', 'Koordinasi jadwal masak harian chef dan staf dapur. Catat realisasi porsi matang, kontrol suhu makanan layak santap, dan pemakaian riil bahan baku.', ['Jadwal masak digital', 'Pencatatan porsi matang', 'Kontrol suhu sajian']],
    ['printer', 'Laporan PDF Siap Cetak', 'Cetak rekapitulasi produksi dan laporan persediaan gudang langsung dalam format resmi A4 Portrait. Header berulang otomatis di tiap halaman, siap ditandatangani.', ['Format resmi A4 Portrait', 'Rekapitulasi produksi', 'Laporan mutasi persediaan']],
    ];

    $security = [
    ['lock', 'Login & Akses Terproteksi', 'Bcrypt password hashing + proteksi anti-brute force 5x per menit per IP'],
    ['users', 'Hak Akses Per Role', 'Segregasi ketat: staf gudang, chef dapur, kasir, dan pimpinan tidak saling tumpang tindih'],
    ['clipboard', 'Audit Trail Terlacak', 'Setiap mutasi stok dan approval terekam dengan timestamp dan identitas staf'],
    ['key', 'Google OAuth SSO', 'Dukungan login cepat Google SSO khusus akun staf yang telah diverifikasi Admin'],
    ];

    $stats = [
    ['package', '1.500+', 'Porsi / Hari', 'Rata-rata produksi harian'],
    ['users', '14+', 'Instansi & Mitra', 'Sekolah, korporat & event'],
    ['check', '99.2%', 'Ketepatan Waktu', 'Pengiriman tepat sebelum acara'],
    ['grid', '4 Role', 'Divisi Terintegrasi', 'Kasir, Gudang, Dapur, Owner'],
    ];

    $profile = [
    ['Nama Unit Usaha', 'Dapur Sentral FoodFlow Katering Malang'],
    ['Wilayah Layanan', 'Kota Malang & Wilayah Sekitarnya'],
    ['Kapasitas Dapur', 'Maksimal 2.000 porsi per hari'],
    ['Jadwal Operasional', 'Setiap Hari, 04.00 – 16.00 WIB'],
    ['Standar Sanitasi', 'Sertifikasi Laik Higiene Sanitasi Jasa Boga'],
    ['Jenis Layanan', 'Nasi Kotak, Prasmanan Event, Katering Instansi'],
    ];

    $groups = [
    ['Karbohidrat Pilihan', 'Nasi Liwet, Nasi Kuning, Kentang', '±250 kkal'],
    ['Protein Unggulan', 'Ayam Bakar, Rendang, Ikan Dori', '±180 kkal'],
    ['Sayuran Segar', 'Capcay, Tumis Buncis, Sayur Asem', '±40 kkal'],
    ['Buah & Pencuci Mulut', 'Semangka, Melon, Puding Buah', '±60 kkal'],
    ['Minuman Pelengkap', 'Infused Water, Jus Buah, Teh Dingin', '±75 kkal'],
    ];

    $packages = [
    ['Paket Reguler', 'Nasi Kotak Harian', 'Rp 22.000 / porsi', ['Nasi putih pulen (200g)', 'Ayam goreng laos gurih (85g)', 'Tumis buncis wortel bakso', 'Kerupuk udang renyah', 'Air mineral & buah pisang']],
    ['Paket Premium', 'Corporate Buffet / Prasmanan', 'Rp 45.000 / porsi', ['Nasi liwet / nasi putih wangi', 'Daging sapi rendang empuk (90g)', 'Kakap asam manis krispi', 'Capcay kuah seafood spesial', 'Puding cokelat vla vanila']],
    ['Paket Sehat', 'Healthy Box Gizi Seimbang', 'Rp 28.000 / porsi', ['Nasi merah organik (180g)', 'Dada ayam panggang rosemary', 'Tahu bacem kukus rendah lemak', 'Salad sayur saus wijen sangrai', 'Jus jeruk murni tanpa gula']],
    ];

    $heroStats = [['4', 'Role Terintegrasi'], ['18+', 'Modul Katering'], ['Real-time', 'Dashboard Operasional']];

    $btnPrimary = 'inline-flex items-center justify-center gap-2 rounded-full bg-primary px-6 py-3 text-body font-medium text-white shadow-card transition hover:bg-primary-dark active:scale-[0.98]';
    $btnLight = 'inline-flex items-center justify-center gap-2 rounded-full bg-white px-6 py-3 text-body font-semibold text-secondary shadow-card transition hover:bg-primary/5 active:scale-[0.98]';
    $sectionTag = 'mb-4 inline-flex items-center rounded-full bg-primary/10 px-4 py-1.5 text-label uppercase text-primary';
    @endphp

    {{-- ===== TOP BAR ===== --}}
    <div class="bg-ink px-4 py-2 print:hidden">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-2 text-small">
            <div class="flex flex-wrap items-center gap-2.5 text-white/80">
                <span class="inline-flex items-center gap-1.5 font-medium">
                    <span class="h-2 w-2 animate-ping rounded-full bg-tertiary"></span>
                    Sistem Aktif — Dapur Sentral Katering & Produksi
                </span>
                <span class="text-white/30">•</span>
                <span class="text-white/60">Kota Malang, Jawa Timur</span>
            </div>
            <div class="hidden items-center gap-4 text-white/60 sm:flex">
                <span>Shift Dapur: 04.00 – 16.00 WIB</span>
                <span class="text-white/30">•</span>
                <span>Hotline Katering: (0341) 491-234</span>
            </div>
        </div>
    </div>

    {{-- ===== NAVBAR ===== --}}
    <nav class="sticky top-0 z-50 border-b border-line transition-all duration-300 print:hidden"
        :class="scrolled ? 'bg-white/95 shadow-card backdrop-blur-md' : 'bg-white'">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">

                <a href="#beranda" class="flex items-center gap-3" @click="mobileMenuOpen = false">
                    <x-brand-logo class="h-10 w-14" />
                    <div class="leading-tight">
                        <div class="font-display text-h3 text-ink">FoodFlow</div>
                        <div class="text-small text-muted">Sistem Katering &amp; Dapur Sentral</div>
                    </div>
                </a>

                <div class="hidden items-center gap-1 lg:flex">
                    @foreach ($nav as [$href, $label])
                    <a href="{{ $href }}" class="rounded-full px-4 py-2 text-body font-medium text-ink transition hover:bg-primary/10 hover:text-primary">{{ $label }}</a>
                    @endforeach
                </div>

                <div class="hidden lg:block">
                    @auth
                    <x-button :href="route('dashboard')"><x-icon name="grid" class="h-4 w-4" /> Buka Dashboard</x-button>
                    @else
                    @if (Route::has('login'))
                    <x-button :href="route('login')"><x-icon name="arrow" class="h-4 w-4" /> Login Petugas</x-button>
                    @endif
                    @endauth
                </div>

                <button type="button" class="rounded-full p-2 text-ink transition hover:bg-primary/10 lg:hidden" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Menu">
                    <x-icon name="menu" class="h-6 w-6" x-show="!mobileMenuOpen" />
                    <x-icon name="close" class="h-6 w-6" x-show="mobileMenuOpen" x-cloak />
                </button>
            </div>
        </div>

        {{-- Menu mobile --}}
        <div x-show="mobileMenuOpen" x-cloak x-transition.opacity
            class="border-t border-line bg-white px-4 py-3 shadow-card lg:hidden">
            <div class="flex flex-col gap-1">
                @foreach ($nav as [$href, $label])
                <a href="{{ $href }}" @click="mobileMenuOpen = false" class="rounded-full px-4 py-2.5 text-body font-medium text-ink transition hover:bg-primary/10">{{ $label }}</a>
                @endforeach
                @auth
                <a href="{{ route('dashboard') }}" class="{{ $btnPrimary }} mt-2">Buka Dashboard</a>
                @else
                @if (Route::has('login'))
                <a href="{{ route('login') }}" class="{{ $btnPrimary }} mt-2">Login Petugas</a>
                @endif
                @endauth
            </div>
        </div>
    </nav>

    {{-- ===== HERO ===== --}}
    <section id="beranda" class="relative flex min-h-[88vh] items-center overflow-hidden bg-gradient-to-br from-wash-from via-wash-via to-wash-to">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -right-40 -top-40 h-[520px] w-[520px] rounded-full bg-primary/20 blur-3xl"></div>
            <div class="absolute -left-28 bottom-0 h-96 w-96 rounded-full bg-tertiary/40 blur-3xl"></div>
        </div>

        <div class="relative z-10 mx-auto w-full max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="grid items-center gap-14 lg:grid-cols-2">

                <div>
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-primary/20 bg-white/80 px-4 py-1.5 text-label uppercase text-primary shadow-card">
                        <span class="h-2 w-2 rounded-full bg-primary"></span>
                        Platform ERP Manajemen Dapur &amp; Katering Modern
                    </div>

                    <h1 class="mb-5 font-display text-4xl font-bold leading-[1.12] tracking-tight text-ink sm:text-5xl">
                        Satu Platform.<br>
                        <span class="text-primary">Kelola Seluruh</span><br>
                        Bisnis Katering Anda.
                    </h1>

                    <p class="mb-8 max-w-lg text-lg leading-relaxed text-muted">
                        FoodFlow mendigitalkan operasional bisnis katering dan dapur sentral — dari penerimaan pesanan nasi kotak &amp; prasmanan, kalkulasi resep otomatis, kontrol stok gudang, hingga pencatatan realisasi masak harian tanpa selisih.
                    </p>

                    <div class="mb-8 flex flex-wrap gap-3">
                        @auth
                        <a href="{{ route('dashboard') }}" class="{{ $btnPrimary }}"><x-icon name="grid" class="h-4 w-4" /> Buka Dashboard Sistem</a>
                        @else
                        @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="{{ $btnPrimary }}"><x-icon name="arrow" class="h-4 w-4" /> Masuk ke Sistem</a>
                        @endif
                        @endauth
                        <a href="#erp" class="inline-flex items-center justify-center gap-2 rounded-full border border-line bg-white px-6 py-3 text-body font-medium text-ink shadow-card transition hover:bg-primary/5">
                            Lihat Fitur ERP
                            <x-icon name="chevron" class="h-4 w-4 text-primary" />
                        </a>
                    </div>

                    <div class="flex flex-wrap items-center gap-6 border-t border-primary/20 pt-5">
                        @foreach ($heroStats as [$val, $label])
                        <div>
                            <div class="font-display text-h2 text-ink">{{ $val }}</div>
                            <div class="text-small text-muted">{{ $label }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Pratinjau dashboard --}}
                <div class="float-card relative">
                    <div class="overflow-hidden rounded-card border border-line bg-white shadow-card">
                        <div class="flex items-center gap-2 border-b border-line bg-primary/5 px-4 py-2.5">
                            <div class="flex gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-muted/30"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-muted/30"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-muted/30"></span>
                            </div>
                            <div class="mx-3 flex-1 truncate rounded-full bg-white px-3 py-1 text-small text-muted">foodflow-app.duckdns.org/dashboard</div>
                        </div>
                        <div class="space-y-3.5 bg-canvas p-5">
                            <div class="grid grid-cols-2 gap-3">
                                @foreach ([
                                ['Pesanan Katering Hari Ini', '1.250', 'Paket Nasi Kotak & Event'],
                                ['Klien / Mitra Aktif', '14', 'Sekolah & Instansi'],
                                ['Stok Bahan Kritis', '3', 'Perlu belanja segera'],
                                ['PO Pengadaan Bahan', '2', 'Menunggu vendor kirim'],
                                ] as [$title, $val, $note])
                                <div class="rounded-input border border-line bg-white p-3.5">
                                    <div class="text-small text-muted">{{ $title }}</div>
                                    <div class="font-display text-h1 text-ink">{{ $val }}</div>
                                    <div class="mt-0.5 text-small font-medium text-primary">{{ $note }}</div>
                                </div>
                                @endforeach
                            </div>
                            <div class="rounded-input border border-line bg-white p-3.5">
                                <div class="mb-2 flex justify-between text-small text-muted">
                                    <span class="font-medium">Progres Masak Dapur Sentral</span>
                                    <span class="font-semibold text-primary">847 / 1.250 porsi</span>
                                </div>
                                <x-progress-bar :value="68" :max="100" />
                                <div class="mt-1.5 flex justify-between text-small text-muted">
                                    <span>67.8% selesai</span>
                                    <span>Target distribusi pkl. 11.30 WIB</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach ([['cart', 'Katering Order'], ['clock', 'Jadwal Produksi'], ['printer', 'Laporan PDF']] as [$ic, $lb])
                                <div class="rounded-input border border-line bg-white p-2.5 text-center">
                                    <x-icon :name="$ic" class="mx-auto mb-1 h-5 w-5 text-primary" />
                                    <div class="text-small font-medium leading-tight text-ink">{{ $lb }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ===== ERP VALUE PROPOSITION ===== --}}
    <section id="erp" class="bg-white py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="reveal mb-14 text-center">
                <div class="{{ $sectionTag }}">Solusi Terintegrasi Bisnis Katering</div>
                <h2 class="mb-4 font-display text-3xl font-bold text-ink sm:text-4xl">
                    Tinggalkan Cara Manual,<br class="hidden sm:block"> Otomatiskan Bisnis Katering
                </h2>
                <p class="mx-auto max-w-2xl text-body text-muted">
                    Dari buku catatan kertas dan chat WhatsApp yang berantakan, beralih ke alur kerja katering digital yang terstruktur, presisi dalam kalkulasi resep, dan transparan dari gudang hingga laporan keuangan.
                </p>
            </div>

            <div class="reveal-group mb-16 grid gap-6 lg:grid-cols-2">
                <div class="rounded-card border border-line bg-canvas p-6 sm:p-8">
                    <div class="mb-6 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-input bg-white text-muted"><x-icon name="close" /></span>
                        <span class="font-display text-h3 text-muted">Tanpa ERP — Cara Konvensional</span>
                    </div>
                    <ul class="space-y-3.5">
                        @foreach ($before as $item)
                        <li class="flex items-start gap-3 text-body text-muted">
                            <x-icon name="close" class="mt-0.5 h-4 w-4 shrink-0 text-muted/60" />
                            <span>{{ $item }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="rounded-card border border-primary/20 bg-primary/5 p-6 sm:p-8">
                    <div class="mb-6 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-input bg-primary text-white"><x-icon name="check" /></span>
                        <span class="font-display text-h3 text-secondary">Dengan FoodFlow ERP Katering</span>
                    </div>
                    <ul class="space-y-3.5">
                        @foreach ($after as $item)
                        <li class="flex items-start gap-3 text-body text-ink">
                            <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                            <span>{{ $item }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <p class="mb-6 text-center text-label uppercase text-muted">4 Peran Pengguna Terhubung dalam Satu Ekosistem</p>
            <div class="reveal-group grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($roles as [$icon, $role, $items])
                <div class="flex flex-col rounded-card border border-line bg-white p-5 shadow-card transition hover:-translate-y-1 hover:border-primary/40">
                    <span class="mb-3 flex h-11 w-11 items-center justify-center rounded-input bg-info-bg text-primary"><x-icon :name="$icon" class="h-6 w-6" /></span>
                    <div class="mb-3 font-display text-h3 text-ink">{{ $role }}</div>
                    <ul class="flex-1 space-y-2">
                        @foreach ($items as $item)
                        <li class="flex items-start gap-2 text-small text-muted">
                            <x-icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-primary" />
                            <span>{{ $item }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== MODUL SISTEM ===== --}}
    <section id="modul" class="border-y border-line bg-canvas py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="reveal mb-14 text-center">
                <div class="{{ $sectionTag }}">Modul Bisnis Katering</div>
                <h2 class="mb-4 font-display text-3xl font-bold text-ink sm:text-4xl">Fitur Lengkap untuk<br class="hidden sm:block"> Operasional Dapur Katering</h2>
                <p class="mx-auto max-w-lg text-body text-muted">
                    Setiap modul dirancang saling terhubung — perubahan stok memperbarui laporan keuangan, rencana masak mengkalkulasi kebutuhan bahan otomatis.
                </p>
            </div>

            <div class="reveal-group grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($modules as [$icon, $title, $desc, $tags])
                <div class="group flex flex-col rounded-card border border-line bg-white p-6 shadow-card transition hover:-translate-y-1 hover:border-primary/40">
                    <span class="mb-4 flex h-12 w-12 items-center justify-center rounded-input bg-info-bg text-primary transition group-hover:scale-110">
                        <x-icon :name="$icon" class="h-6 w-6" />
                    </span>
                    <h3 class="mb-2 font-display text-h3 text-ink">{{ $title }}</h3>
                    <p class="mb-4 flex-1 text-body leading-relaxed text-muted">{{ $desc }}</p>
                    <div class="flex flex-wrap gap-1.5 border-t border-line pt-3">
                        @foreach ($tags as $tag)
                        <span class="rounded-full bg-primary/10 px-2.5 py-1 text-small font-medium text-primary">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Keamanan --}}
            <div class="reveal mt-12 rounded-card bg-gradient-to-br from-ink to-secondary p-6 shadow-card sm:p-8">
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($security as [$icon, $title, $desc])
                    <div class="flex items-start gap-3.5">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-input bg-white/10 text-white"><x-icon :name="$icon" /></span>
                        <div>
                            <div class="mb-1 text-body font-semibold text-white">{{ $title }}</div>
                            <div class="text-small leading-relaxed text-white/60">{{ $desc }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ===== PROFIL DAPUR ===== --}}
    <section id="profil" class="bg-gradient-to-br from-ink via-ink to-secondary py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="reveal mb-12 text-center">
                <div class="mb-4 inline-flex items-center rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-label uppercase text-white">Kapasitas &amp; Skala Dapur</div>
                <h2 class="mb-3 font-display text-3xl font-bold text-white sm:text-4xl">Dapur Sentral FoodFlow Katering</h2>
                <p class="mx-auto max-w-xl text-body text-white/70">
                    Kapasitas produksi harian dan data operasional yang dikelola secara terpusat melalui sistem FoodFlow ERP.
                </p>
            </div>

            <div class="reveal-group mb-10 grid grid-cols-2 gap-4 sm:gap-5 lg:grid-cols-4">
                @foreach ($stats as [$icon, $val, $label, $sub])
                <div class="rounded-card border border-white/15 bg-white/10 p-5 text-center backdrop-blur-sm">
                    <span class="mx-auto mb-3.5 flex h-12 w-12 items-center justify-center rounded-input bg-white/10 text-white"><x-icon :name="$icon" class="h-6 w-6" /></span>
                    <div class="mb-1 font-display text-3xl font-bold text-white">{{ $val }}</div>
                    <div class="mb-0.5 text-body font-semibold text-white">{{ $label }}</div>
                    <div class="text-small text-white/60">{{ $sub }}</div>
                </div>
                @endforeach
            </div>

            <div class="reveal-group grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($profile as [$label, $val])
                <div class="rounded-input border border-white/15 bg-white/5 p-4">
                    <div class="mb-0.5 text-label uppercase text-white/50">{{ $label }}</div>
                    <div class="text-body font-medium text-white">{{ $val }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== MENU & PAKET ===== --}}
    <section id="menu" class="bg-white py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="reveal mb-12 text-center">
                <div class="{{ $sectionTag }}">Katalog Hidangan &amp; Paket</div>
                <h2 class="mb-3 font-display text-3xl font-bold text-ink sm:text-4xl">Menu Dikelola Terpusat<br class="hidden sm:block"> di Master Menu FoodFlow</h2>
                <p class="mx-auto max-w-lg text-body text-muted">
                    Koleksi resep standar katering, takaran porsi, dan harga pokok penjualan (HPP) dikelola langsung di sistem — siap disimulasikan ke jadwal produksi.
                </p>
            </div>

            <div class="reveal-group mb-12 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                @foreach ($groups as [$label, $example, $kcal])
                <div class="rounded-card border border-line bg-canvas p-5 text-center transition hover:-translate-y-1 hover:border-primary/40">
                    <div class="mb-1 text-body font-semibold text-ink">{{ $label }}</div>
                    <div class="mb-3 text-small leading-relaxed text-muted">{{ $example }}</div>
                    <span class="inline-flex items-center rounded-full bg-primary/10 px-2.5 py-0.5 text-small font-semibold text-primary">{{ $kcal }}</span>
                </div>
                @endforeach
            </div>

            <div class="reveal-group grid gap-6 sm:grid-cols-3">
                @foreach ($packages as [$type, $title, $price, $items])
                <div class="flex flex-col overflow-hidden rounded-card border border-line bg-white shadow-card transition hover:-translate-y-1 hover:border-primary/40">
                    <div class="border-b border-line bg-primary/5 p-5">
                        <div class="text-label uppercase text-primary">{{ $type }}</div>
                        <div class="font-display text-h3 text-ink">{{ $title }}</div>
                    </div>
                    <div class="flex items-center justify-between border-b border-line px-5 py-3">
                        <span class="text-small font-medium text-muted">Estimasi Harga</span>
                        <span class="rounded-full bg-primary px-3 py-1 text-small font-semibold text-white">{{ $price }}</span>
                    </div>
                    <ul class="flex-1 space-y-2.5 p-5">
                        @foreach ($items as $item)
                        <li class="flex items-center gap-2.5 text-small text-muted">
                            <x-icon name="check" class="h-4 w-4 shrink-0 text-primary" />
                            <span>{{ $item }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>

            <p class="mt-8 text-center text-small text-muted">
                Master resep, bahan baku, dan komposisi gizi dikelola secara fleksibel di modul <strong class="text-primary">Resep Builder &amp; Master Menu</strong> oleh tim Chef dan Admin Katering.
            </p>
        </div>
    </section>

    {{-- ===== PORTAL / CTA ===== --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-ink via-secondary to-primary py-20">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-24 -top-24 h-80 w-80 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute -bottom-24 -right-24 h-80 w-80 rounded-full bg-tertiary/20 blur-2xl"></div>
        </div>

        <div class="relative z-10 mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
            <div class="reveal mb-6 inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/15 px-4 py-1.5 text-label uppercase text-white">
                <x-icon name="lock" class="h-3.5 w-3.5" />
                Portal Akses Petugas Dapur Katering
            </div>

            <h2 class="mb-4 font-display text-3xl font-bold text-white sm:text-4xl">Kelola Bisnis Katering Anda<br class="hidden sm:block"> Lebih Rapi &amp; Terukur Hari Ini</h2>
            <p class="mx-auto mb-10 max-w-lg text-body text-white/80">
                Masuk ke sistem FoodFlow dengan akun yang sudah didaftarkan Admin. Tersedia Single Sign-On (SSO) Google untuk kemudahan akses seluruh tim.
            </p>

            <div class="reveal mx-auto max-w-md rounded-card border border-white/25 bg-white/10 p-6 shadow-card backdrop-blur-md sm:p-8">
                <div class="mb-6 grid grid-cols-2 gap-2.5">
                    @foreach ([['cart', 'Admin & Kasir'], ['package', 'Staf Gudang'], ['book', 'Chef & Produksi'], ['activity', 'Owner / Manager']] as [$ic, $r])
                    <div class="flex items-center gap-2 rounded-input bg-white/15 px-3 py-2 text-left">
                        <x-icon :name="$ic" class="h-4 w-4 text-white" />
                        <span class="text-small font-medium text-white">{{ $r }}</span>
                    </div>
                    @endforeach
                </div>

                @auth
                <a href="{{ route('dashboard') }}" class="{{ $btnLight }} mb-3 w-full"><x-icon name="grid" class="h-5 w-5" /> Buka Dashboard Saya</a>
                @else
                @if (Route::has('login'))
                <a href="{{ route('login') }}" class="{{ $btnLight }} mb-3 w-full"><x-icon name="arrow" class="h-5 w-5" /> Masuk ke FoodFlow</a>
                @if (Route::has('auth.google'))
                <a href="{{ route('auth.google') }}" class="inline-flex w-full items-center justify-center gap-2.5 rounded-full border border-white/40 px-6 py-3 text-body font-medium text-white transition hover:bg-white/10">
                    <x-icon name="key" class="h-4 w-4" />
                    Masuk dengan Google SSO
                </a>
                @endif
                @endif
                @endauth

                <p class="mt-4 text-center text-small text-white/80">
                    Belum punya akun? Hubungi Admin Dapur — <span class="font-semibold text-white">(0341) 491-234</span>
                </p>
            </div>
        </div>
    </section>

    {{-- ===== FOOTER ===== --}}
    <footer class="bg-ink text-white/60">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">

                <div class="sm:col-span-2 lg:col-span-1">
                    <div class="mb-4 flex items-center gap-3">
                        <span class="flex h-10 w-14 items-center justify-center rounded-input bg-white p-1.5">
                            <x-brand-logo class="h-full w-full" />
                        </span>
                        <div>
                            <div class="font-display text-h3 text-white">FoodFlow</div>
                            <div class="text-small text-white/60">ERP Katering &amp; Dapur Sentral</div>
                        </div>
                    </div>
                    <p class="text-small leading-relaxed">
                        Sistem ERP manajemen katering modern — mengintegrasikan pesanan katering, resep gizi, logistik bahan baku, dan pengawasan produksi dapur secara presisi dan akuntabel.
                    </p>
                </div>

                <div>
                    <h4 class="mb-4 text-body font-semibold text-white">Navigasi Halaman</h4>
                    <ul class="space-y-2.5">
                        @foreach ($nav as [$href, $label])
                        <li><a href="{{ $href }}" class="text-body transition hover:text-white">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h4 class="mb-4 text-body font-semibold text-white">Kontak Dapur Sentral</h4>
                    <ul class="space-y-3 text-small leading-relaxed">
                        <li>Jl. Soekarno-Hatta No. 9, Kota Malang 65141</li>
                        <li>(0341) 491-234</li>
                        <li>kontak@foodflow-catering.id</li>
                    </ul>
                </div>

                <div>
                    <h4 class="mb-4 text-body font-semibold text-white">Standar &amp; Mutu Usaha</h4>
                    <ul class="space-y-2.5">
                        @foreach ([
                        'Sertifikat Laik Higiene Sanitasi Jasa Boga',
                        'Standar Keamanan Pangan Olahan Siap Saji',
                        'Formula Resep Terstandarisasi Chef',
                        'Audit Mutasi Stok Realtime & Transparan',
                        'Manajemen Pesanan Katering Digital',
                        'Perlindungan Privasi Data Pelanggan',
                        ] as $std)
                        <li class="flex items-start gap-2 text-small">
                            <x-icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-tertiary" />
                            <span>{{ $std }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-6 sm:flex-row">
                <p class="text-small text-white/40">
                    &copy; {{ date('Y') }} FoodFlow — Sistem ERP Manajemen Katering &amp; Dapur Sentral Malang. All rights reserved.
                </p>
                <div class="flex items-center gap-4 text-small text-white/40">
                    <a href="#" class="transition hover:text-white">Kebijakan Privasi</a>
                    <span>•</span>
                    <a href="#" class="transition hover:text-white">Syarat &amp; Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Munculkan elemen saat masuk layar
        (function() {
            const io = new IntersectionObserver((entries) => {
                entries.forEach((e) => {
                    if (e.isIntersecting) {
                        e.target.classList.add('visible');
                        io.unobserve(e.target);
                    }
                });
            }, {
                threshold: 0.12
            });
            document.querySelectorAll('.reveal, .reveal-group').forEach((el) => io.observe(el));
        })();
    </script>
</body>

</html>