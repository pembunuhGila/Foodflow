<!DOCTYPE html>
<html lang="id" x-data="{ mobileMenuOpen: false, activeSection: 'beranda' }" x-cloak>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SPPG Dapur MBG Kalideres 01 — Sentra Pengolahan Pangan Bergizi untuk program Makan Bergizi Gratis (MBG) di wilayah Kalideres, Jakarta Barat.">
    <title>SPPG Dapur MBG Kalideres 01 — Transparansi Program MBG</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        /* Smooth scroll */
        html { scroll-behavior: smooth; }

        /* Hero gradient */
        .hero-gradient {
            background: linear-gradient(135deg, #EFF6FF 0%, #F0FDF4 50%, #FFF7ED 100%);
        }

        /* Stats section background */
        .stats-bg {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 60%, #0F3460 100%);
        }

        /* Card hover effect */
        .card-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px -8px rgba(30, 27, 75, 0.12);
        }

        /* Number counter animation */
        @keyframes countUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-count { animation: countUp 0.6s ease forwards; }

        /* Navbar scroll shadow */
        .nav-scrolled {
            box-shadow: 0 1px 20px rgba(0,0,0,0.08);
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(10px);
        }

        /* Underline active nav */
        .nav-active {
            color: #1D4ED8;
            position: relative;
        }
        .nav-active::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0; right: 0;
            height: 2px;
            background: #1D4ED8;
            border-radius: 2px;
        }

        /* Food group icon bg */
        .food-icon-bg {
            background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
        }

        /* Map placeholder */
        .map-placeholder {
            background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);
        }

        /* Portal section gradient */
        .portal-gradient {
            background: linear-gradient(135deg, #1E40AF 0%, #1D4ED8 50%, #2563EB 100%);
        }
    </style>
</head>
<body class="bg-white text-slate-800 font-sans antialiased" x-data="{ scrolled: false }" @scroll.window="scrolled = window.scrollY > 20">

    {{-- ========================= TOP ANNOUNCEMENT BAR ========================= --}}
    <div class="bg-blue-50 border-b border-blue-100 py-1.5 px-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between flex-wrap gap-2 text-xs">
            {{-- Left: status + lokasi --}}
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 bg-white border border-blue-200 text-blue-800 font-semibold rounded-full px-3 py-0.5">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    Sentra Dapur Mandiri Aktif
                </span>
                <span class="inline-flex items-center gap-1.5 bg-white border border-blue-100 text-slate-600 rounded-full px-3 py-0.5">
                    <svg class="w-3 h-3 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                    Wilayah Kalideres, Jakarta Barat
                </span>
            </div>
            {{-- Right: shift + hotline --}}
            <div class="flex items-center gap-4 text-slate-500">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-slate-400">Shift:</span> <span class="text-slate-700 font-medium">03.00 – 15.00 WIB</span>
                </span>
                <span class="text-slate-300 hidden sm:inline">•</span>
                <span class="flex items-center gap-1.5 hidden sm:flex">
                    <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span class="text-slate-400">Hotline:</span> <span class="text-slate-700 font-medium">(021) 543-9821</span>
                </span>
            </div>
        </div>
    </div>

    {{-- ========================= NAVBAR ========================= --}}
    <nav
        class="sticky top-0 z-50 bg-white border-b border-slate-100 transition-all duration-300"
        :class="{ 'nav-scrolled': scrolled }"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="#beranda" class="flex items-center gap-3" @click="mobileMenuOpen = false">
                    <div class="w-10 h-10 bg-blue-800 rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <div class="font-display font-bold text-slate-900 text-base tracking-tight">SPPG Dapur MBG</div>
                        <div class="text-xs text-slate-500 font-medium">Kalideres 01 — BGN</div>
                    </div>
                </a>

                {{-- Desktop Nav Links --}}
                <div class="hidden lg:flex items-center gap-6">
                    @foreach([
                        ['href' => '#beranda',    'label' => 'Beranda'],
                        ['href' => '#profil',     'label' => 'Profil & Statistik'],
                        ['href' => '#menu',       'label' => 'Menu Harian'],
                        ['href' => '#distribusi', 'label' => 'Peta Distribusi'],
                        ['href' => '#aduan',      'label' => 'Form Aduan'],
                    ] as $link)
                    <a
                        href="{{ $link['href'] }}"
                        class="text-sm font-medium text-slate-600 hover:text-blue-700 transition-colors relative py-1"
                    >{{ $link['label'] }}</a>
                    @endforeach
                </div>

                {{-- CTA Button --}}
                <div class="hidden lg:flex items-center gap-3">
                    @if(Route::has('login'))
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        Login Petugas
                    </a>
                    @endif
                </div>

                {{-- Mobile Hamburger --}}
                <button
                    class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    :aria-expanded="mobileMenuOpen"
                >
                    <svg class="w-5 h-5" x-show="!mobileMenuOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg class="w-5 h-5" x-show="mobileMenuOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="lg:hidden border-t border-slate-100 bg-white px-4 py-4"
        >
            <div class="flex flex-col gap-1">
                @foreach([
                    ['href' => '#beranda',    'label' => 'Beranda'],
                    ['href' => '#profil',     'label' => 'Profil & Statistik'],
                    ['href' => '#menu',       'label' => 'Menu Harian'],
                    ['href' => '#distribusi', 'label' => 'Peta Distribusi'],
                    ['href' => '#aduan',      'label' => 'Form Aduan'],
                ] as $link)
                <a
                    href="{{ $link['href'] }}"
                    class="text-sm font-medium text-slate-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-3 py-2.5 transition-colors"
                    @click="mobileMenuOpen = false"
                >{{ $link['label'] }}</a>
                @endforeach
                @if(Route::has('login'))
                <a href="{{ route('login') }}" class="mt-2 inline-flex items-center justify-center gap-2 bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Login Petugas
                </a>
                @endif
            </div>
        </div>
    </nav>

    {{-- ========================= HERO SECTION ========================= --}}
    <section id="beranda" class="hero-gradient min-h-[90vh] flex items-center relative overflow-hidden">
        {{-- Background decoration --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-32 -right-32 w-96 h-96 bg-blue-200/30 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-16 -left-16 w-80 h-80 bg-green-200/30 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 right-1/4 w-48 h-48 bg-orange-100/40 rounded-full blur-2xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full relative">
            <div class="grid lg:grid-cols-2 gap-12 items-center">

                {{-- Left: Text Content --}}
                <div>
                    {{-- Badge --}}
                    <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full px-4 py-1.5 mb-6 border border-blue-200">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        Program Makan Bergizi Gratis (MBG) — BGN RI
                    </div>

                    <h1 class="font-display font-bold text-4xl sm:text-5xl lg:text-5xl text-slate-900 leading-tight mb-6">
                        Makan Bergizi,<br>
                        <span class="text-blue-700">Generasi Sehat</span><br>
                        Indonesia
                    </h1>

                    <p class="text-slate-600 text-base sm:text-lg leading-relaxed mb-8 max-w-lg">
                        Sentra Pengolahan Pangan Bergizi Dapur MBG Kalideres 01 memastikan
                        <strong class="text-slate-800">1.250+ porsi makanan bergizi</strong> tersalurkan setiap hari
                        ke 12 sekolah di wilayah Kalideres, Jakarta Barat — dengan standar gizi, kebersihan, dan transparansi tertinggi.
                    </p>

                    <div class="flex flex-wrap gap-3">
                        <a
                            href="#aduan"
                            class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm px-6 py-3 rounded-xl transition-colors shadow-lg shadow-blue-200"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3-3-3z"/></svg>
                            Kirim Laporan / Feedback Makanan
                        </a>
                        <a
                            href="#profil"
                            class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm px-6 py-3 rounded-xl border border-slate-200 transition-colors"
                        >
                            Lihat Profil Dapur
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Right: Dashboard Preview Card --}}
                <div class="relative">
                    {{-- Main Preview --}}
                    <div class="bg-white rounded-2xl shadow-2xl shadow-blue-100/50 border border-slate-100 overflow-hidden">
                        {{-- Fake browser bar --}}
                        <div class="bg-slate-100 px-4 py-3 flex items-center gap-2 border-b border-slate-200">
                            <div class="flex gap-1.5">
                                <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                            </div>
                            <div class="flex-1 bg-white rounded-md px-3 py-1 text-xs text-slate-400 mx-4">
                                foodflow.sppg-kalideres.go.id/dashboard
                            </div>
                        </div>
                        {{-- Dashboard content preview --}}
                        <div class="p-4 bg-slate-50">
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div class="bg-white rounded-xl p-3 border border-slate-100">
                                    <div class="text-xs text-slate-500 mb-1">Produksi Hari Ini</div>
                                    <div class="text-xl font-bold text-blue-700">1.250</div>
                                    <div class="text-xs text-green-600 flex items-center gap-1 mt-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                        +5% dari kemarin
                                    </div>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-slate-100">
                                    <div class="text-xs text-slate-500 mb-1">Sekolah Terlayani</div>
                                    <div class="text-xl font-bold text-green-700">12</div>
                                    <div class="text-xs text-slate-400 mt-1">Kalideres & sekitarnya</div>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-slate-100">
                                    <div class="text-xs text-slate-500 mb-1">Suhu Layak (≥60°C)</div>
                                    <div class="text-xl font-bold text-orange-600">98.4%</div>
                                    <div class="text-xs text-orange-500 mt-1">Standar BPOM ✓</div>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-slate-100">
                                    <div class="text-xs text-slate-500 mb-1">Food Waste</div>
                                    <div class="text-xl font-bold text-emerald-700">↓12%</div>
                                    <div class="text-xs text-emerald-600 mt-1">vs target bulanan</div>
                                </div>
                            </div>
                            {{-- Mini progress bar --}}
                            <div class="bg-white rounded-xl p-3 border border-slate-100">
                                <div class="flex justify-between text-xs text-slate-500 mb-2">
                                    <span>Progress Distribusi Hari Ini</span>
                                    <span class="font-semibold text-blue-700">847 / 1.250 porsi</span>
                                </div>
                                <div class="h-2 bg-slate-100 rounded-full">
                                    <div class="h-2 bg-blue-600 rounded-full" style="width: 68%"></div>
                                </div>
                                <div class="text-xs text-slate-400 mt-1.5">67.8% — estimasi selesai pkl. 12.30 WIB</div>
                            </div>
                        </div>
                    </div>

                    {{-- Floating Delivery Status Card --}}
                    <div class="absolute -bottom-5 -left-5 bg-white rounded-xl shadow-xl border border-slate-100 p-3 flex items-center gap-3 w-56">
                        <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-800">SDN Kalideres 01</div>
                            <div class="text-xs text-green-600">Terkirim • 10.15 WIB</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================= STATS SECTION ========================= --}}
    <section id="profil" class="stats-bg py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-blue-900/50 text-blue-200 text-xs font-semibold rounded-full px-4 py-1.5 mb-4 border border-blue-700/50">
                    Profil & Capaian Kinerja
                </div>
                <h2 class="font-display font-bold text-3xl sm:text-4xl text-white mb-4">
                    Angka yang Berbicara
                </h2>
                <p class="text-slate-400 text-base max-w-xl mx-auto">
                    Data capaian nyata Dapur MBG Kalideres 01 yang kami perbarui setiap hari secara transparan.
                </p>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-12">
                @foreach([
                    [
                        'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
                        'value' => '1.250+',
                        'label' => 'Porsi / Hari',
                        'sub' => 'Rata-rata harian',
                        'color' => 'blue',
                    ],
                    [
                        'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                        'value' => '12',
                        'label' => 'Sekolah',
                        'sub' => 'Wilayah Kalideres',
                        'color' => 'green',
                    ],
                    [
                        'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                        'value' => '98.4%',
                        'label' => 'Suhu Layak',
                        'sub' => 'Standar ≥60°C (BPOM)',
                        'color' => 'orange',
                    ],
                    [
                        'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
                        'value' => '↓12%',
                        'label' => 'Food Waste',
                        'sub' => 'vs target bulanan',
                        'color' => 'emerald',
                    ],
                ] as $stat)
                <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-6 card-hover text-center backdrop-blur-sm">
                    @php
                        $colorMap = [
                            'blue'    => 'bg-blue-900/50 text-blue-400',
                            'green'   => 'bg-green-900/50 text-green-400',
                            'orange'  => 'bg-orange-900/50 text-orange-400',
                            'emerald' => 'bg-emerald-900/50 text-emerald-400',
                        ];
                    @endphp
                    <div class="w-12 h-12 {{ $colorMap[$stat['color']] }} rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $stat['icon'] }}"/>
                        </svg>
                    </div>
                    <div class="text-3xl sm:text-4xl font-display font-bold text-white mb-2">{{ $stat['value'] }}</div>
                    <div class="text-sm font-semibold text-slate-300 mb-1">{{ $stat['label'] }}</div>
                    <div class="text-xs text-slate-500">{{ $stat['sub'] }}</div>
                </div>
                @endforeach
            </div>

            {{-- Profile Info Row --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach([
                    ['label' => 'Nama Dapur', 'value' => 'SPPG Dapur MBG Kalideres 01', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['label' => 'Wilayah Kerja', 'value' => 'Kalideres, Jakarta Barat (Kode: JKT-WB-01)', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
                    ['label' => 'Kapasitas Produksi', 'value' => '1.500 porsi / hari (maks)', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ['label' => 'Jam Operasional', 'value' => 'Senin – Jumat, 03.00 – 15.00 WIB', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label' => 'Sertifikasi', 'value' => 'Laik Higiene Sanitasi No. 503/SGS/2024', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                    ['label' => 'Pengelola', 'value' => 'PT Sentra Dapur Mandiri (Mitra BGN)', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                ] as $info)
                <div class="bg-slate-800/40 border border-slate-700/40 rounded-xl p-4 flex items-start gap-3">
                    <div class="w-8 h-8 bg-blue-900/50 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $info['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 mb-0.5">{{ $info['label'] }}</div>
                        <div class="text-sm font-medium text-slate-200">{{ $info['value'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========================= NUTRITION / MENU SECTION ========================= --}}
    <section id="menu" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-green-50 text-green-700 text-xs font-semibold rounded-full px-4 py-1.5 mb-4 border border-green-200">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    Standar Gizi & Menu Harian
                </div>
                <h2 class="font-display font-bold text-3xl sm:text-4xl text-slate-900 mb-4">
                    5 Kelompok Pangan Seimbang
                </h2>
                <p class="text-slate-500 text-base max-w-xl mx-auto">
                    Setiap porsi MBG memenuhi pedoman gizi seimbang Kemenkes RI — mengandung karbohidrat,
                    protein, serat sayur, buah, dan kalsium yang terukur secara klinis.
                </p>
            </div>

            {{-- Food Groups --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-14">
                @foreach([
                    ['icon' => '🌾', 'label' => 'Karbohidrat',    'example' => 'Nasi putih, Jagung, Kentang',       'kcal' => '±250 kkal'],
                    ['icon' => '🍗', 'label' => 'Protein Hewani', 'example' => 'Ayam, Telur, Ikan, Daging sapi',   'kcal' => '±120 kkal'],
                    ['icon' => '🥦', 'label' => 'Serat Sayur',    'example' => 'Bayam, Wortel, Buncis, Kangkung',  'kcal' => '±30 kkal'],
                    ['icon' => '🍊', 'label' => 'Buah-buahan',    'example' => 'Jeruk, Pepaya, Pisang, Semangka',  'kcal' => '±50 kkal'],
                    ['icon' => '🥛', 'label' => 'Kalsium',        'example' => 'Susu UHT, Tahu sutra, Tempe',      'kcal' => '±80 kkal'],
                ] as $group)
                <div class="food-icon-bg rounded-2xl p-5 text-center card-hover border border-blue-50">
                    <div class="text-4xl mb-3">{{ $group['icon'] }}</div>
                    <div class="font-semibold text-slate-800 text-sm mb-1.5">{{ $group['label'] }}</div>
                    <div class="text-xs text-slate-500 leading-relaxed mb-2">{{ $group['example'] }}</div>
                    <div class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full px-2.5 py-0.5">
                        {{ $group['kcal'] }}
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Menu Cards --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                @foreach([
                    [
                        'day' => 'Senin',
                        'date' => 'Paket A',
                        'icon' => '🍱',
                        'kcal' => '530 kkal',
                        'items' => [
                            'Nasi putih (200g)',
                            'Ayam goreng bumbu (80g)',
                            'Tumis bayam wortel (100g)',
                            'Jeruk segar (100g)',
                            'Susu UHT plain (200ml)',
                        ],
                        'color' => 'blue',
                    ],
                    [
                        'day' => 'Selasa',
                        'date' => 'Paket B',
                        'icon' => '🫕',
                        'kcal' => '545 kkal',
                        'items' => [
                            'Nasi putih (200g)',
                            'Telur semur (1 butir)',
                            'Tempe goreng (60g)',
                            'Sayur bening bayam (150g)',
                            'Pepaya potong (100g)',
                        ],
                        'color' => 'green',
                    ],
                    [
                        'day' => 'Rabu',
                        'date' => 'Paket C',
                        'icon' => '🍲',
                        'kcal' => '560 kkal',
                        'items' => [
                            'Nasi putih (200g)',
                            'Ikan goreng tepung (90g)',
                            'Tahu bacem (60g)',
                            'Ca kangkung (100g)',
                            'Pisang ambon (1 buah)',
                        ],
                        'color' => 'orange',
                    ],
                ] as $menu)
                @php
                    $menuColorMap = [
                        'blue'   => ['bg' => 'bg-blue-50',   'badge' => 'bg-blue-100 text-blue-700',   'icon' => 'text-blue-600',   'border' => 'border-blue-100'],
                        'green'  => ['bg' => 'bg-green-50',  'badge' => 'bg-green-100 text-green-700', 'icon' => 'text-green-600',  'border' => 'border-green-100'],
                        'orange' => ['bg' => 'bg-orange-50', 'badge' => 'bg-orange-100 text-orange-700','icon' => 'text-orange-600','border' => 'border-orange-100'],
                    ];
                    $mc = $menuColorMap[$menu['color']];
                @endphp
                <div class="bg-white rounded-2xl border {{ $mc['border'] }} shadow-sm card-hover overflow-hidden">
                    <div class="{{ $mc['bg'] }} px-5 py-4 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ $menu['day'] }}</div>
                            <div class="font-display font-bold text-slate-800">{{ $menu['date'] }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">{{ $menu['icon'] }}</span>
                            <span class="{{ $mc['badge'] }} text-xs font-semibold rounded-full px-2.5 py-0.5">{{ $menu['kcal'] }}</span>
                        </div>
                    </div>
                    <ul class="p-5 space-y-2">
                        @foreach($menu['items'] as $item)
                        <li class="flex items-center gap-2 text-sm text-slate-600">
                            <svg class="w-3.5 h-3.5 {{ $mc['icon'] }} flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>

            {{-- Food Waste Commitment Card --}}
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl border border-emerald-100 p-6 sm:p-8">
                <div class="grid sm:grid-cols-2 gap-6 items-center">
                    <div>
                        <div class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full px-3 py-1 mb-4">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            Komitmen Zero Waste
                        </div>
                        <h3 class="font-display font-bold text-2xl text-slate-800 mb-3">Program Kurangi Sisa Makanan</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">
                            Kami berkomitmen meminimalkan food waste melalui perencanaan porsi berbasis data,
                            monitoring real-time, dan audit mingguan. Sisa makanan yang tak terhindarkan
                            diolah menjadi kompos atau disalurkan ke mitra sosial.
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach([
                            ['value' => '↓12%', 'label' => 'Food Waste vs Target',    'sub' => 'Oktober 2026'],
                            ['value' => '97%',   'label' => 'Tingkat Konsumsi',         'sub' => 'Rata-rata bulanan'],
                            ['value' => '8x',    'label' => 'Audit Internal/Bulan',     'sub' => 'Setiap Senin & Kamis'],
                            ['value' => '100%',  'label' => 'Daur Ulang Sisa',          'sub' => 'Ke mitra kompos'],
                        ] as $wStat)
                        <div class="bg-white rounded-xl p-3 text-center border border-emerald-100">
                            <div class="text-xl font-display font-bold text-emerald-700">{{ $wStat['value'] }}</div>
                            <div class="text-xs font-medium text-slate-700 mt-0.5">{{ $wStat['label'] }}</div>
                            <div class="text-xs text-slate-400">{{ $wStat['sub'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================= DISTRIBUTION MAP SECTION ========================= --}}
    <section id="distribusi" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full px-4 py-1.5 mb-4 border border-blue-200">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                    Peta Distribusi
                </div>
                <h2 class="font-display font-bold text-3xl sm:text-4xl text-slate-900 mb-4">
                    12 Sekolah, 1 Misi
                </h2>
                <p class="text-slate-500 text-base max-w-xl mx-auto">
                    Jangkauan distribusi Dapur MBG Kalideres 01 mencakup 12 satuan pendidikan
                    dari tingkat SD hingga SMP di wilayah Kalideres dan sekitarnya.
                </p>
            </div>

            <div class="grid lg:grid-cols-5 gap-6">
                {{-- Map --}}
                <div class="lg:col-span-3">
                    <div class="map-placeholder rounded-2xl overflow-hidden h-80 sm:h-96 lg:h-full min-h-80 relative border border-blue-100">
                        {{-- Embedded map or placeholder --}}
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15867.5!2d106.7!3d-6.14!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6a0e3c4c0c0c0c%3A0x0!2sKalideres%2C+Jakarta+Barat!5e0!3m2!1sid!2sid!4v1699999999999!5m2!1sid!2sid"
                            class="w-full h-full border-0"
                            style="filter: hue-rotate(200deg) saturate(0.8) brightness(1.05);"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Peta distribusi Dapur MBG Kalideres"
                        ></iframe>
                        {{-- Map overlay badge --}}
                        <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-sm rounded-xl px-3 py-2 shadow-sm border border-slate-100">
                            <div class="text-xs font-semibold text-slate-700">🗺️ Kalideres, Jakarta Barat</div>
                            <div class="text-xs text-slate-500">Radius distribusi ±5 km</div>
                        </div>
                    </div>
                </div>

                {{-- School List --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden h-full">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="font-semibold text-slate-800">Daftar Sekolah Penerima</h3>
                            <span class="bg-blue-100 text-blue-700 text-xs font-bold rounded-full px-2.5 py-0.5">12 Sekolah</span>
                        </div>
                        <div class="divide-y divide-slate-50 overflow-y-auto max-h-96 lg:max-h-none">
                            @foreach([
                                ['no' => 1,  'name' => 'SDN Kalideres 01',       'kelurahan' => 'Kalideres',     'siswa' => 142, 'status' => 'aktif'],
                                ['no' => 2,  'name' => 'SDN Kalideres 02',       'kelurahan' => 'Kalideres',     'siswa' => 118, 'status' => 'aktif'],
                                ['no' => 3,  'name' => 'SDN Kalideres 03',       'kelurahan' => 'Kalideres',     'siswa' => 95,  'status' => 'aktif'],
                                ['no' => 4,  'name' => 'SDN Pegadungan 01',      'kelurahan' => 'Pegadungan',    'siswa' => 130, 'status' => 'aktif'],
                                ['no' => 5,  'name' => 'SDN Pegadungan 02',      'kelurahan' => 'Pegadungan',    'siswa' => 108, 'status' => 'aktif'],
                                ['no' => 6,  'name' => 'SDN Semanan 01',         'kelurahan' => 'Semanan',       'siswa' => 97,  'status' => 'aktif'],
                                ['no' => 7,  'name' => 'SDN Tegal Alur 01',      'kelurahan' => 'Tegal Alur',    'siswa' => 115, 'status' => 'aktif'],
                                ['no' => 8,  'name' => 'SDN Tegal Alur 02',      'kelurahan' => 'Tegal Alur',    'siswa' => 103, 'status' => 'aktif'],
                                ['no' => 9,  'name' => 'MIN 23 Jakarta Barat',   'kelurahan' => 'Kalideres',     'siswa' => 86,  'status' => 'aktif'],
                                ['no' => 10, 'name' => 'SMPN 165 Jakarta',       'kelurahan' => 'Kalideres',     'siswa' => 134, 'status' => 'aktif'],
                                ['no' => 11, 'name' => 'MTs Al-Hikmah Kalideres','kelurahan' => 'Pegadungan',    'siswa' => 72,  'status' => 'aktif'],
                                ['no' => 12, 'name' => 'SDN Duri Kosambi 01',    'kelurahan' => 'Duri Kosambi',  'siswa' => 50,  'status' => 'persiapan'],
                            ] as $school)
                            <div class="px-5 py-3 flex items-center gap-3 hover:bg-slate-50 transition-colors">
                                <span class="text-xs text-slate-400 font-mono w-5 flex-shrink-0">{{ sprintf('%02d', $school['no']) }}</span>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-slate-800 truncate">{{ $school['name'] }}</div>
                                    <div class="text-xs text-slate-500">Kel. {{ $school['kelurahan'] }} • {{ $school['siswa'] }} siswa</div>
                                </div>
                                <span class="flex-shrink-0 inline-flex items-center gap-1 text-xs rounded-full px-2 py-0.5 font-medium {{ $school['status'] === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $school['status'] === 'aktif' ? 'bg-green-500' : 'bg-amber-500' }}"></span>
                                    {{ ucfirst($school['status']) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                        <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 text-xs text-slate-500 text-center">
                            Total: 1.250 siswa aktif penerima MBG
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================= FEEDBACK / ADUAN SECTION ========================= --}}
    <section id="aduan" class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="text-center mb-10">
                <div class="inline-flex items-center gap-2 bg-orange-50 text-orange-700 text-xs font-semibold rounded-full px-4 py-1.5 mb-4 border border-orange-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3-3-3z"/></svg>
                    Suara Anda Penting
                </div>
                <h2 class="font-display font-bold text-3xl sm:text-4xl text-slate-900 mb-4">
                    Kirim Laporan & Feedback Makanan
                </h2>
                <p class="text-slate-500 text-base max-w-lg mx-auto">
                    Bantu kami meningkatkan kualitas program MBG dengan melaporkan masalah,
                    memberikan saran, atau mengapresiasi pelayanan kami.
                </p>
            </div>

            {{-- Feedback Form --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
                <form class="space-y-5" action="#" method="POST">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Pelapor <span class="text-slate-400">(opsional)</span></label>
                            <input
                                type="text"
                                name="nama_pelapor"
                                placeholder="mis. Orang Tua / Kepala Sekolah"
                                class="w-full rounded-xl border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3.5"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Sekolah</label>
                            <select name="sekolah" class="w-full rounded-xl border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3.5">
                                <option value="">— Pilih Sekolah —</option>
                                @foreach([
                                    'SDN Kalideres 01','SDN Kalideres 02','SDN Kalideres 03',
                                    'SDN Pegadungan 01','SDN Pegadungan 02','SDN Semanan 01',
                                    'SDN Tegal Alur 01','SDN Tegal Alur 02','MIN 23 Jakarta Barat',
                                    'SMPN 165 Jakarta','MTs Al-Hikmah Kalideres','SDN Duri Kosambi 01',
                                ] as $sch)
                                <option value="{{ $sch }}">{{ $sch }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Jenis Laporan</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @foreach([
                                ['value' => 'kualitas', 'label' => '🍽️ Kualitas Makanan'],
                                ['value' => 'ketepatan','label' => '🕐 Ketepatan Waktu'],
                                ['value' => 'kebersihan','label'=> '🧹 Kebersihan'],
                                ['value' => 'apresiasi','label' => '👍 Apresiasi'],
                            ] as $type)
                            <label class="flex items-center justify-center gap-2 border border-slate-200 rounded-xl px-3 py-2.5 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors text-xs font-medium text-slate-700 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700">
                                <input type="radio" name="jenis_laporan" value="{{ $type['value'] }}" class="sr-only">
                                {{ $type['label'] }}
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Isi Laporan / Feedback</label>
                        <textarea
                            name="isi_laporan"
                            rows="4"
                            placeholder="Ceritakan pengalaman Anda atau masalah yang terjadi secara detail..."
                            class="w-full rounded-xl border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3.5 resize-none"
                        ></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between pt-1">
                        <p class="text-xs text-slate-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Data Anda dijaga kerahasiaannya sesuai UU PDP
                        </p>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm px-6 py-2.5 rounded-xl transition-colors shadow-sm w-full sm:w-auto justify-center"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            Kirim Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- ========================= PORTAL / CTA SECTION ========================= --}}
    <section class="portal-gradient py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 bg-blue-800/50 text-blue-200 text-xs font-semibold rounded-full px-4 py-1.5 mb-6 border border-blue-600/50">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Portal Internal Petugas
            </div>

            <h2 class="font-display font-bold text-3xl sm:text-4xl text-white mb-4">
                Masuk Portal Manajemen Dapur
            </h2>
            <p class="text-blue-200 text-base max-w-lg mx-auto mb-10">
                Akses sistem FoodFlow untuk manajemen produksi, stok bahan baku, distribusi,
                dan laporan kinerja harian. Khusus petugas yang terdaftar.
            </p>

            <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-6 sm:p-8 max-w-md mx-auto">
                <div class="grid grid-cols-2 gap-3 mb-6">
                    @foreach([
                        ['role' => 'Admin / Operasional', 'icon' => '⚙️'],
                        ['role' => 'Staff Gudang',         'icon' => '📦'],
                        ['role' => 'Staff Produksi',       'icon' => '👨‍🍳'],
                        ['role' => 'Manager / Owner',      'icon' => '📊'],
                    ] as $r)
                    <div class="bg-white/10 rounded-xl px-3 py-2.5 flex items-center gap-2 text-sm text-white">
                        <span>{{ $r['icon'] }}</span>
                        <span class="font-medium text-xs">{{ $r['role'] }}</span>
                    </div>
                    @endforeach
                </div>

                @if(Route::has('login'))
                <a
                    href="{{ route('login') }}"
                    class="w-full inline-flex items-center justify-center gap-2 bg-white text-blue-700 font-bold text-sm px-6 py-3.5 rounded-xl hover:bg-blue-50 transition-colors shadow-lg"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Masuk Portal Petugas
                </a>
                @if(Route::has('auth.google'))
                <div class="mt-3">
                    <a
                        href="{{ route('auth.google') }}"
                        class="w-full inline-flex items-center justify-center gap-2 bg-transparent border border-white/40 text-white font-semibold text-sm px-6 py-3 rounded-xl hover:bg-white/10 transition-colors"
                    >
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#93C5FD"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#86EFAC"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FCD34D"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#FCA5A5"/>
                        </svg>
                        Masuk dengan Google (SSO MBG)
                    </a>
                </div>
                @endif
                @endif

                <p class="text-blue-300 text-xs text-center mt-4">
                    Akun tidak aktif? Hubungi Admin melalui hotline <span class="font-semibold">(021) 543-9821</span>
                </p>
            </div>
        </div>
    </section>

    {{-- ========================= FOOTER ========================= --}}
    <footer class="stats-bg border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-10">

                {{-- Column 1: Brand --}}
                <div class="sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 bg-blue-700 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-display font-bold text-white text-sm">SPPG Dapur MBG</div>
                            <div class="text-xs text-slate-500">Kalideres 01 — BGN RI</div>
                        </div>
                    </div>
                    <p class="text-slate-500 text-xs leading-relaxed mb-4">
                        Sentra Pengolahan Pangan Bergizi yang berkomitmen menyediakan makanan berkualitas
                        untuk generasi Indonesia yang sehat dan berprestasi.
                    </p>
                    <div class="flex gap-2">
                        @foreach(['M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z', 'M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z', 'M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z M4 6a2 2 0 100-4 2 2 0 000 4z'] as $icon)
                        <a href="#" class="w-8 h-8 bg-slate-800 hover:bg-slate-700 rounded-lg flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="{{ $icon }}"/>
                            </svg>
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- Column 2: Navigasi --}}
                <div>
                    <h4 class="font-semibold text-slate-300 text-sm mb-4">Navigasi</h4>
                    <ul class="space-y-2.5">
                        @foreach([
                            ['href' => '#beranda',    'label' => 'Beranda'],
                            ['href' => '#profil',     'label' => 'Profil & Statistik'],
                            ['href' => '#menu',       'label' => 'Menu Harian'],
                            ['href' => '#distribusi', 'label' => 'Peta Distribusi'],
                            ['href' => '#aduan',      'label' => 'Form Aduan'],
                        ] as $link)
                        <li>
                            <a href="{{ $link['href'] }}" class="text-slate-500 hover:text-slate-300 text-sm transition-colors">{{ $link['label'] }}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Column 3: Kontak --}}
                <div>
                    <h4 class="font-semibold text-slate-300 text-sm mb-4">Kontak</h4>
                    <ul class="space-y-3">
                        @foreach([
                            ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'text' => 'Jl. Kalideres Raya No. 12, Jakarta Barat 11840'],
                            ['icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'text' => '(021) 543-9821 (Hotline Pengaduan)'],
                            ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'text' => 'info@dapur-mbg-kalideres.id'],
                        ] as $contact)
                        <li class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-slate-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $contact['icon'] }}"/>
                            </svg>
                            <span class="text-slate-500 text-xs leading-relaxed">{{ $contact['text'] }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Column 4: Standar Mutu --}}
                <div>
                    <h4 class="font-semibold text-slate-300 text-sm mb-4">Standar Mutu</h4>
                    <ul class="space-y-2.5">
                        @foreach([
                            'Laik Higiene Sanitasi (Dinkes DKI)',
                            'HACCP — Hazard Analysis Critical Control Point',
                            'Pedoman Gizi Seimbang Kemenkes RI',
                            'SNI 01-3747 (Pangan Siap Saji)',
                            'Panduan MBG — Badan Gizi Nasional',
                            'UU PDP (Perlindungan Data Pribadi)',
                        ] as $std)
                        <li class="flex items-start gap-2 text-xs text-slate-500">
                            <svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $std }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Footer bottom --}}
            <div class="mt-12 pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-slate-600 text-xs">
                    &copy; {{ date('Y') }} SPPG Dapur MBG Kalideres 01 — PT Sentra Dapur Mandiri. Mitra resmi Badan Gizi Nasional (BGN) RI.
                </p>
                <div class="flex items-center gap-4 text-xs text-slate-600">
                    <a href="#" class="hover:text-slate-400 transition-colors">Kebijakan Privasi</a>
                    <span class="text-slate-700">•</span>
                    <a href="#" class="hover:text-slate-400 transition-colors">Syarat & Ketentuan</a>
                    <span class="text-slate-700">•</span>
                    <span>Powered by <strong class="text-slate-500">FoodFlow</strong></span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
