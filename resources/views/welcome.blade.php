<!DOCTYPE html>
<html lang="id" x-data="{ mobileMenuOpen: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 20" x-cloak>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SPPG Dapur MBG Malang 01 — Sentra Pengolahan Pangan Bergizi untuk program Makan Bergizi Gratis (MBG) di Kota Malang, Jawa Timur.">
    <title>SPPG Dapur MBG Malang 01 — Transparansi Program MBG</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }

        .hero-gradient {
            background: linear-gradient(140deg, #EFF6FF 0%, #F0FDF4 55%, #FEFCE8 100%);
        }
        .stats-bg {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 60%, #0C2461 100%);
        }
        .card-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px -8px rgba(15, 23, 42, 0.14);
        }
        .nav-scrolled {
            box-shadow: 0 1px 20px rgba(0,0,0,0.07);
            backdrop-filter: blur(12px);
            background: rgba(255,255,255,0.96) !important;
        }
        .food-icon-bg {
            background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
        }
        .map-placeholder {
            background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);
        }
        .portal-gradient {
            background: linear-gradient(135deg, #1E3A8A 0%, #1D4ED8 60%, #2563EB 100%);
        }
        /* "Cara Pakai" step connector line */
        .step-line::before {
            content: '';
            position: absolute;
            top: 20px;
            left: calc(50% + 28px);
            right: calc(-50% + 28px);
            height: 2px;
            background: linear-gradient(to right, #BFDBFE, #93C5FD);
        }
    </style>
</head>
<body class="bg-white text-slate-800 font-sans antialiased">

    {{-- ===== TOP ANNOUNCEMENT BAR ===== --}}
    <div class="bg-blue-50 border-b border-blue-100 py-1.5 px-4 print:hidden">
        <div class="max-w-7xl mx-auto flex items-center justify-between flex-wrap gap-2 text-xs">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 bg-white border border-blue-200 text-blue-800 font-semibold rounded-full px-3 py-0.5">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    Sentra Dapur Aktif
                </span>
                <span class="inline-flex items-center gap-1.5 bg-white border border-blue-100 text-slate-600 rounded-full px-3 py-0.5">
                    <svg class="w-3 h-3 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                    Kota Malang, Jawa Timur
                </span>
            </div>
            <div class="flex items-center gap-4 text-slate-500">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-slate-400">Shift:</span>
                    <span class="text-slate-700 font-medium">03.00 – 15.00 WIB</span>
                </span>
                <span class="text-slate-300 hidden sm:inline">•</span>
                <span class="hidden sm:flex items-center gap-1.5">
                    <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span class="text-slate-400">Hotline:</span>
                    <span class="text-slate-700 font-medium">(0341) 491-234</span>
                </span>
            </div>
        </div>
    </div>

    {{-- ===== NAVBAR ===== --}}
    <nav
        class="sticky top-0 z-50 bg-white border-b border-slate-100 transition-all duration-300 print:hidden"
        :class="{ 'nav-scrolled': scrolled }"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <a href="#beranda" class="flex items-center gap-3" @click="mobileMenuOpen = false">
                    <div class="w-10 h-10 bg-blue-800 rounded-xl flex items-center justify-center shadow flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <div class="font-display font-bold text-slate-900 text-sm tracking-tight">SPPG Dapur MBG</div>
                        <div class="text-xs text-slate-500 font-medium">Malang 01 — BGN</div>
                    </div>
                </a>

                <div class="hidden lg:flex items-center gap-7">
                    @foreach([
                        ['#beranda',    'Beranda'],
                        ['#profil',     'Profil & Statistik'],
                        ['#menu',       'Menu Harian'],
                        ['#distribusi', 'Peta Distribusi'],
                        ['#gunakan',    'Gunakan Sistem'],
                    ] as [$href, $label])
                    <a href="{{ $href }}" class="text-sm font-medium text-slate-600 hover:text-blue-700 transition-colors">{{ $label }}</a>
                    @endforeach
                </div>

                @if(Route::has('login'))
                <a href="{{ route('login') }}" class="hidden lg:inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Login Petugas
                </a>
                @endif

                <button class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100" @click="mobileMenuOpen = !mobileMenuOpen">
                    <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1"
            class="lg:hidden border-t border-slate-100 bg-white px-4 py-3"
        >
            <div class="flex flex-col gap-1">
                @foreach([
                    ['#beranda',    'Beranda'],
                    ['#profil',     'Profil & Statistik'],
                    ['#menu',       'Menu Harian'],
                    ['#distribusi', 'Peta Distribusi'],
                    ['#gunakan',    'Gunakan Sistem'],
                ] as [$href, $label])
                <a href="{{ $href }}" @click="mobileMenuOpen = false" class="text-sm font-medium text-slate-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-3 py-2.5 transition-colors">{{ $label }}</a>
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

    {{-- ===== HERO ===== --}}
    <section id="beranda" class="hero-gradient min-h-[88vh] flex items-center relative overflow-hidden">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-blue-200/25 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-96 h-96 bg-green-200/25 rounded-full blur-3xl"></div>
            <div class="absolute top-1/3 right-1/3 w-56 h-56 bg-amber-100/30 rounded-full blur-2xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full relative z-10">
            <div class="grid lg:grid-cols-2 gap-14 items-center">

                {{-- Left --}}
                <div>
                    <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full px-4 py-1.5 mb-6 border border-blue-200">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        Program Makan Bergizi Gratis (MBG) — Badan Gizi Nasional RI
                    </div>

                    <h1 class="font-display font-bold text-4xl sm:text-5xl text-slate-900 leading-[1.1] mb-5">
                        Makan Bergizi,<br>
                        <span class="text-blue-700">Generasi Sehat</span><br>
                        Indonesia
                    </h1>

                    <p class="text-slate-600 text-lg leading-relaxed mb-8 max-w-lg">
                        Dapur MBG Malang 01 menyalurkan <strong class="text-slate-800">1.250+ porsi makanan bergizi</strong>
                        setiap hari ke 12 sekolah di Kota Malang — terkelola secara digital, terukur, dan transparan
                        untuk seluruh pemangku kepentingan.
                    </p>

                    <div class="flex flex-wrap gap-3">
                        <a href="#gunakan" class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm px-6 py-3 rounded-xl transition-colors shadow-lg shadow-blue-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Gunakan Sistem FoodFlow
                        </a>
                        <a href="#profil" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm px-6 py-3 rounded-xl border border-slate-200 transition-colors">
                            Lihat Profil Dapur
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </a>
                    </div>

                    {{-- Trust badges --}}
                    <div class="flex flex-wrap items-center gap-4 mt-8 pt-6 border-t border-slate-200/80">
                        @foreach(['Laik Higiene Sanitasi', 'HACCP Certified', 'Data Real-time'] as $badge)
                        <span class="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                            <svg class="w-3.5 h-3.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $badge }}
                        </span>
                        @endforeach
                    </div>
                </div>

                {{-- Right: Dashboard preview --}}
                <div class="relative">
                    <div class="bg-white rounded-2xl shadow-2xl shadow-blue-100/60 border border-slate-100 overflow-hidden">
                        {{-- Browser bar --}}
                        <div class="bg-slate-100 px-4 py-2.5 flex items-center gap-2 border-b border-slate-200">
                            <div class="flex gap-1.5">
                                <div class="w-2.5 h-2.5 bg-red-400 rounded-full"></div>
                                <div class="w-2.5 h-2.5 bg-yellow-400 rounded-full"></div>
                                <div class="w-2.5 h-2.5 bg-green-400 rounded-full"></div>
                            </div>
                            <div class="flex-1 bg-white rounded-md px-3 py-1 text-xs text-slate-400 mx-3">
                                foodflow.sppg-malang.go.id/dashboard
                            </div>
                        </div>
                        {{-- Dashboard content --}}
                        <div class="p-4 bg-slate-50 space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                @foreach([
                                    ['Produksi Hari Ini', '1.250', 'porsi', 'text-blue-700', '+5% dari kemarin', 'text-green-600'],
                                    ['Sekolah Terlayani', '12', 'sekolah', 'text-green-700', 'Kota Malang', 'text-slate-400'],
                                    ['Suhu Layak ≥60°C', '98.4%', 'standar BPOM', 'text-orange-600', 'Standar terpenuhi ✓', 'text-orange-500'],
                                    ['Food Waste', '↓12%', 'vs target', 'text-emerald-700', 'vs target bulanan', 'text-emerald-600'],
                                ] as [$title, $val, $sub, $color, $note, $noteColor])
                                <div class="bg-white rounded-xl p-3 border border-slate-100">
                                    <div class="text-xs text-slate-500 mb-1">{{ $title }}</div>
                                    <div class="text-xl font-bold {{ $color }}">{{ $val }}</div>
                                    <div class="text-xs {{ $noteColor }} mt-1">{{ $note }}</div>
                                </div>
                                @endforeach
                            </div>
                            <div class="bg-white rounded-xl p-3 border border-slate-100">
                                <div class="flex justify-between text-xs text-slate-500 mb-2">
                                    <span>Progress Distribusi Hari Ini</span>
                                    <span class="font-semibold text-blue-700">847 / 1.250 porsi</span>
                                </div>
                                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-2 bg-blue-600 rounded-full transition-all" style="width: 68%"></div>
                                </div>
                                <div class="text-xs text-slate-400 mt-1.5">67.8% — estimasi selesai pkl. 12.30 WIB</div>
                            </div>
                        </div>
                    </div>

                    {{-- Floating card --}}
                    <div class="absolute -bottom-5 -left-5 bg-white rounded-xl shadow-xl border border-slate-100 p-3 flex items-center gap-3 w-52">
                        <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-800">SDN Merjosari 01</div>
                            <div class="text-xs text-green-600">Terkirim • 10.15 WIB</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== STATS / PROFIL ===== --}}
    <section id="profil" class="stats-bg py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-blue-900/50 text-blue-200 text-xs font-semibold rounded-full px-4 py-1.5 mb-4 border border-blue-700/50">
                    Profil & Capaian Kinerja
                </div>
                <h2 class="font-display font-bold text-3xl sm:text-4xl text-white mb-3">Angka yang Berbicara</h2>
                <p class="text-slate-400 text-base max-w-lg mx-auto">
                    Data capaian nyata Dapur MBG Malang 01 — diperbarui setiap hari secara transparan untuk publik.
                </p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-12">
                @foreach([
                    ['M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', '1.250+', 'Porsi / Hari', 'Rata-rata harian', 'blue'],
                    ['M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', '12', 'Sekolah', 'Kota Malang', 'green'],
                    ['M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', '98.4%', 'Suhu Layak', 'Standar ≥60°C (BPOM)', 'orange'],
                    ['M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', '↓12%', 'Food Waste', 'vs target bulanan', 'emerald'],
                ] as [$icon, $val, $label, $sub, $color])
                @php
                    $cm = ['blue' => 'bg-blue-900/50 text-blue-400', 'green' => 'bg-green-900/50 text-green-400', 'orange' => 'bg-orange-900/50 text-orange-400', 'emerald' => 'bg-emerald-900/50 text-emerald-400'];
                @endphp
                <div class="bg-slate-800/60 border border-slate-700/40 rounded-2xl p-5 card-hover text-center backdrop-blur-sm">
                    <div class="w-11 h-11 {{ $cm[$color] }} rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icon }}"/></svg>
                    </div>
                    <div class="text-3xl font-display font-bold text-white mb-1">{{ $val }}</div>
                    <div class="text-sm font-semibold text-slate-300 mb-0.5">{{ $label }}</div>
                    <div class="text-xs text-slate-500">{{ $sub }}</div>
                </div>
                @endforeach
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach([
                    ['Nama Dapur',         'SPPG Dapur MBG Malang 01',                           'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['Wilayah Kerja',      'Kota Malang, Jawa Timur (Kode: MLG-01)',              'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
                    ['Kapasitas Produksi', '1.500 porsi / hari (kapasitas maksimum)',              'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ['Jam Operasional',    'Senin – Jumat, 03.00 – 15.00 WIB',                   'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['Sertifikasi',        'Laik Higiene Sanitasi No. 503/SGS/2024',              'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                    ['Pengelola',          'PT Sentra Dapur Mandiri (Mitra Resmi BGN)',           'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                ] as [$label, $val, $icon])
                <div class="bg-slate-800/40 border border-slate-700/40 rounded-xl p-4 flex items-start gap-3">
                    <div class="w-8 h-8 bg-blue-900/50 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 mb-0.5">{{ $label }}</div>
                        <div class="text-sm font-medium text-slate-200">{{ $val }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== MENU / NUTRITION ===== --}}
    <section id="menu" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-green-50 text-green-700 text-xs font-semibold rounded-full px-4 py-1.5 mb-4 border border-green-200">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    Standar Gizi & Menu Harian
                </div>
                <h2 class="font-display font-bold text-3xl sm:text-4xl text-slate-900 mb-3">5 Kelompok Pangan Seimbang</h2>
                <p class="text-slate-500 text-base max-w-xl mx-auto">
                    Setiap porsi MBG memenuhi pedoman gizi seimbang Kemenkes RI — mengandung karbohidrat,
                    protein, serat sayur, buah, dan kalsium yang terukur secara klinis.
                </p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-14">
                @foreach([
                    ['🌾', 'Karbohidrat',    'Nasi, Jagung, Kentang',       '±250 kkal'],
                    ['🍗', 'Protein Hewani', 'Ayam, Telur, Ikan, Daging',   '±120 kkal'],
                    ['🥦', 'Serat Sayur',    'Bayam, Wortel, Buncis',       '±30 kkal'],
                    ['🍊', 'Buah-buahan',    'Jeruk, Pepaya, Pisang',       '±50 kkal'],
                    ['🥛', 'Kalsium',        'Susu UHT, Tahu, Tempe',       '±80 kkal'],
                ] as [$emoji, $label, $example, $kcal])
                <div class="food-icon-bg rounded-2xl p-5 text-center card-hover border border-blue-50">
                    <div class="text-4xl mb-3">{{ $emoji }}</div>
                    <div class="font-semibold text-slate-800 text-sm mb-1">{{ $label }}</div>
                    <div class="text-xs text-slate-500 leading-relaxed mb-2">{{ $example }}</div>
                    <span class="inline-flex items-center bg-blue-100 text-blue-700 text-xs font-semibold rounded-full px-2.5 py-0.5">{{ $kcal }}</span>
                </div>
                @endforeach
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                @foreach([
                    ['Senin', 'Paket A', '🍱', '530 kkal', ['Nasi putih (200g)', 'Ayam goreng bumbu (80g)', 'Tumis bayam wortel (100g)', 'Jeruk segar (100g)', 'Susu UHT plain (200ml)'], 'blue', 'bg-blue-50 border-blue-100', 'bg-blue-100 text-blue-700', 'text-blue-600'],
                    ['Selasa', 'Paket B', '🫕', '545 kkal', ['Nasi putih (200g)', 'Telur semur (1 butir)', 'Tempe goreng (60g)', 'Sayur bening bayam (150g)', 'Pepaya potong (100g)'], 'green', 'bg-green-50 border-green-100', 'bg-green-100 text-green-700', 'text-green-600'],
                    ['Rabu', 'Paket C', '🍲', '560 kkal', ['Nasi putih (200g)', 'Ikan goreng tepung (90g)', 'Tahu bacem (60g)', 'Ca kangkung (100g)', 'Pisang ambon (1 buah)'], 'orange', 'bg-orange-50 border-orange-100', 'bg-orange-100 text-orange-700', 'text-orange-600'],
                ] as [$day, $pack, $emoji, $kcal, $items, $_, $bgBorder, $badge, $check])
                <div class="bg-white rounded-2xl border {{ $bgBorder }} shadow-sm card-hover overflow-hidden">
                    <div class="{{ $bgBorder }} px-5 py-4 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ $day }}</div>
                            <div class="font-display font-bold text-slate-800">{{ $pack }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">{{ $emoji }}</span>
                            <span class="{{ $badge }} text-xs font-semibold rounded-full px-2.5 py-0.5">{{ $kcal }}</span>
                        </div>
                    </div>
                    <ul class="p-5 space-y-2">
                        @foreach($items as $item)
                        <li class="flex items-center gap-2 text-sm text-slate-600">
                            <svg class="w-3.5 h-3.5 {{ $check }} flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>

            {{-- Food Waste Commitment --}}
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
                            monitoring real-time, dan audit mingguan. Sisa makanan diolah menjadi kompos
                            atau disalurkan ke mitra sosial terpercaya.
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach([
                            ['↓12%', 'Food Waste vs Target', 'Oktober 2026'],
                            ['97%',  'Tingkat Konsumsi',     'Rata-rata bulanan'],
                            ['8x',   'Audit Internal/Bulan', 'Setiap Senin & Kamis'],
                            ['100%', 'Daur Ulang Sisa',      'Ke mitra kompos'],
                        ] as [$v, $l, $s])
                        <div class="bg-white rounded-xl p-3 text-center border border-emerald-100">
                            <div class="text-xl font-display font-bold text-emerald-700">{{ $v }}</div>
                            <div class="text-xs font-medium text-slate-700 mt-0.5">{{ $l }}</div>
                            <div class="text-xs text-slate-400">{{ $s }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== DISTRIBUTION MAP ===== --}}
    <section id="distribusi" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full px-4 py-1.5 mb-4 border border-blue-200">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                    Peta Distribusi
                </div>
                <h2 class="font-display font-bold text-3xl sm:text-4xl text-slate-900 mb-3">12 Sekolah, 1 Misi</h2>
                <p class="text-slate-500 text-base max-w-lg mx-auto">
                    Jangkauan distribusi Dapur MBG Malang 01 mencakup 12 satuan pendidikan dari tingkat SD
                    hingga SMP di wilayah Lowokwaru dan sekitarnya, Kota Malang.
                </p>
            </div>

            <div class="grid lg:grid-cols-5 gap-6">
                <div class="lg:col-span-3">
                    <div class="map-placeholder rounded-2xl overflow-hidden h-80 sm:h-96 lg:h-full min-h-80 relative border border-blue-100">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31671.8!2d112.6141!3d-7.9666!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd629aef0e1c931%3A0x81615e2c9ef54c24!2sLowokwaru%2C+Kota+Malang%2C+Jawa+Timur!5e0!3m2!1sid!2sid!4v1699999999999!5m2!1sid!2sid"
                            class="w-full h-full border-0"
                            style="filter: hue-rotate(200deg) saturate(0.8) brightness(1.05);"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Peta distribusi Dapur MBG Malang"
                        ></iframe>
                        <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-sm rounded-xl px-3 py-2 shadow border border-slate-100">
                            <div class="text-xs font-semibold text-slate-700">🗺️ Lowokwaru, Kota Malang</div>
                            <div class="text-xs text-slate-500">Radius distribusi ±5 km</div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden h-full">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="font-semibold text-slate-800">Daftar Sekolah Penerima</h3>
                            <span class="bg-blue-100 text-blue-700 text-xs font-bold rounded-full px-2.5 py-0.5">12 Sekolah</span>
                        </div>
                        <div class="divide-y divide-slate-50 overflow-y-auto max-h-96 lg:max-h-none">
                            @foreach([
                                [1,  'SDN Merjosari 01',       'Merjosari',    138, 'aktif'],
                                [2,  'SDN Merjosari 02',       'Merjosari',    122, 'aktif'],
                                [3,  'SDN Dinoyo 01',          'Dinoyo',       105, 'aktif'],
                                [4,  'SDN Dinoyo 02',          'Dinoyo',        97, 'aktif'],
                                [5,  'SDN Sumbersari 01',      'Sumbersari',   143, 'aktif'],
                                [6,  'SDN Sumbersari 02',      'Sumbersari',   118, 'aktif'],
                                [7,  'SDN Ketawanggede 01',    'Ketawanggede', 101, 'aktif'],
                                [8,  'SDN Lowokwaru 01',       'Lowokwaru',    126, 'aktif'],
                                [9,  'MIN 1 Kota Malang',      'Merjosari',     89, 'aktif'],
                                [10, 'SMPN 10 Malang',         'Lowokwaru',    134, 'aktif'],
                                [11, 'MTs Surya Buana Malang', 'Lowokwaru',     77, 'aktif'],
                                [12, 'SDN Tlogomas 01',        'Tlogomas',      50, 'persiapan'],
                            ] as [$no, $name, $kel, $siswa, $status])
                            <div class="px-5 py-3 flex items-center gap-3 hover:bg-slate-50 transition-colors">
                                <span class="text-xs text-slate-400 font-mono w-5 flex-shrink-0">{{ sprintf('%02d', $no) }}</span>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-slate-800 truncate">{{ $name }}</div>
                                    <div class="text-xs text-slate-500">Kel. {{ $kel }} • {{ $siswa }} siswa</div>
                                </div>
                                <span class="flex-shrink-0 inline-flex items-center gap-1 text-xs rounded-full px-2 py-0.5 font-medium {{ $status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $status === 'aktif' ? 'bg-green-500' : 'bg-amber-500' }}"></span>
                                    {{ ucfirst($status) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                        <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 text-xs text-slate-500 text-center">
                            Total: 1.200 siswa aktif penerima MBG
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== GUNAKAN SISTEM (pengganti form aduan) ===== --}}
    <section id="gunakan" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full px-4 py-1.5 mb-4 border border-blue-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                    Platform Digital Terintegrasi
                </div>
                <h2 class="font-display font-bold text-3xl sm:text-4xl text-slate-900 mb-4">
                    Kelola MBG Lebih Cerdas<br class="hidden sm:block">
                    dengan FoodFlow
                </h2>
                <p class="text-slate-500 text-base max-w-xl mx-auto">
                    FoodFlow adalah sistem manajemen dapur digital yang dirancang khusus untuk SPPG MBG —
                    dari perencanaan produksi, stok bahan baku, distribusi, hingga laporan kinerja harian.
                    Semua dalam satu platform yang mudah digunakan.
                </p>
            </div>

            {{-- How it works: 3 Steps --}}
            <div class="grid sm:grid-cols-3 gap-6 mb-16 relative">
                {{-- connector line desktop --}}
                <div class="hidden sm:block absolute top-8 left-1/3 right-1/3 h-0.5 bg-gradient-to-r from-blue-200 to-blue-200 via-blue-300 z-0"></div>

                @foreach([
                    ['01', 'Daftar & Login', 'Admin mendaftarkan akun petugas sesuai role. Login aman dengan password atau Google SSO (MBG Single Sign-On).', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'bg-blue-600'],
                    ['02', 'Kelola Operasional', 'Rencanakan produksi, pantau stok bahan baku real-time, catat realisasi porsi, dan kelola distribusi ke sekolah dengan mudah.', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'bg-indigo-600'],
                    ['03', 'Pantau & Laporan', 'Owner dan admin memantau KPI produksi, evaluasi gizi, dan mengunduh laporan PDF harian/bulanan langsung dari dashboard.', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'bg-purple-600'],
                ] as [$step, $title, $desc, $icon, $bg])
                <div class="relative z-10 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 {{ $bg }} rounded-2xl flex items-center justify-center mb-5 shadow-lg group-hover:scale-105 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-400 tracking-widest mb-2">LANGKAH {{ $step }}</span>
                    <h3 class="font-display font-bold text-lg text-slate-800 mb-3">{{ $title }}</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">{{ $desc }}</p>
                </div>
                @endforeach
            </div>

            {{-- Role cards --}}
            <div class="mb-14">
                <p class="text-center text-xs font-semibold text-slate-400 uppercase tracking-widest mb-6">Tersedia untuk 4 peran pengguna</p>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach([
                        ['⚙️', 'Admin / Operasional', 'Kelola pengguna, pesanan katering, PO pengadaan, penerimaan bahan, dan semua data master.', 'bg-blue-50 border-blue-100 hover:border-blue-300'],
                        ['📦', 'Staff Gudang',         'Kelola stok bahan baku, verifikasi permintaan bahan dapur, dan pantau kartu mutasi stok.', 'bg-amber-50 border-amber-100 hover:border-amber-300'],
                        ['👨‍🍳', 'Staff Produksi',      'Akses rencana produksi, catat realisasi masak, dan hitung kebutuhan bahan secara otomatis.', 'bg-green-50 border-green-100 hover:border-green-300'],
                        ['📊', 'Manager / Owner',      'Pantau KPI produksi, evaluasi gizi menu, dan unduh laporan kinerja harian/bulanan dalam PDF.', 'bg-purple-50 border-purple-100 hover:border-purple-300'],
                    ] as [$emoji, $role, $desc, $style])
                    <div class="{{ $style }} border rounded-2xl p-5 card-hover transition-colors">
                        <div class="text-3xl mb-3">{{ $emoji }}</div>
                        <div class="font-semibold text-slate-800 text-sm mb-2">{{ $role }}</div>
                        <p class="text-xs text-slate-500 leading-relaxed">{{ $desc }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Feature highlights --}}
            <div class="bg-slate-50 rounded-2xl p-6 sm:p-8 border border-slate-200">
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach([
                        ['M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'Login Aman', 'Password terenkripsi bcrypt + Google SSO + rate limiting 5x/menit'],
                        ['M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'Dashboard Real-time', 'Pantau KPI produksi, stok, dan distribusi secara langsung tanpa refresh'],
                        ['M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'Laporan PDF Otomatis', 'Cetak laporan inventaris & produksi format A4 portrait siap tanda tangan'],
                        ['M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'Multi-Role Akses', 'Hak akses berbeda per peran — admin, gudang, produksi, dan owner'],
                        ['M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z', 'Kalkulasi Otomatis', 'Hitung kebutuhan bahan baku per rencana produksi secara instan'],
                        ['M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z', 'Monitoring Gizi', 'Evaluasi kandungan gizi menu dan rekap nutrisi mingguan/bulanan'],
                    ] as [$icon, $title, $desc])
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4.5 h-4.5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                        </div>
                        <div>
                            <div class="font-semibold text-slate-800 text-sm mb-1">{{ $title }}</div>
                            <div class="text-xs text-slate-500 leading-relaxed">{{ $desc }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ===== PORTAL / CTA ===== --}}
    <section class="portal-gradient py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 bg-blue-800/50 text-blue-200 text-xs font-semibold rounded-full px-4 py-1.5 mb-6 border border-blue-600/50">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Portal Internal Petugas
            </div>

            <h2 class="font-display font-bold text-3xl sm:text-4xl text-white mb-4">Siap Mulai Menggunakan<br class="hidden sm:block"> FoodFlow?</h2>
            <p class="text-blue-200 text-base max-w-lg mx-auto mb-10">
                Masuk ke portal manajemen dapur digital Anda. Kelola produksi, stok, distribusi,
                dan laporan — semua dari satu tempat yang aman dan mudah diakses.
            </p>

            <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-6 sm:p-8 max-w-md mx-auto">
                <div class="grid grid-cols-2 gap-3 mb-6">
                    @foreach([['⚙️','Admin / Operasional'],['📦','Staff Gudang'],['👨‍🍳','Staff Produksi'],['📊','Manager / Owner']] as [$e,$r])
                    <div class="bg-white/10 rounded-xl px-3 py-2.5 flex items-center gap-2 text-sm text-white">
                        <span>{{ $e }}</span>
                        <span class="font-medium text-xs">{{ $r }}</span>
                    </div>
                    @endforeach
                </div>

                @if(Route::has('login'))
                <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-2 bg-white text-blue-700 font-bold text-sm px-6 py-3.5 rounded-xl hover:bg-blue-50 transition-colors shadow-lg mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Masuk Portal Petugas
                </a>
                @if(Route::has('auth.google'))
                <a href="{{ route('auth.google') }}" class="w-full inline-flex items-center justify-center gap-2 bg-transparent border border-white/40 text-white font-semibold text-sm px-6 py-3 rounded-xl hover:bg-white/10 transition-colors">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#93C5FD"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#86EFAC"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FCD34D"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#FCA5A5"/>
                    </svg>
                    Masuk dengan Google (SSO MBG)
                </a>
                @endif
                @endif

                <p class="text-blue-300 text-xs text-center mt-4">
                    Belum punya akun? Hubungi Admin via hotline <span class="font-semibold">(0341) 491-234</span>
                </p>
            </div>
        </div>
    </section>

    {{-- ===== FOOTER ===== --}}
    <footer class="stats-bg border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-10">

                <div class="sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 bg-blue-700 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        </div>
                        <div>
                            <div class="font-display font-bold text-white text-sm">SPPG Dapur MBG</div>
                            <div class="text-xs text-slate-500">Malang 01 — BGN RI</div>
                        </div>
                    </div>
                    <p class="text-slate-500 text-xs leading-relaxed mb-4">
                        Sentra Pengolahan Pangan Bergizi yang berkomitmen menyediakan makanan berkualitas
                        untuk generasi Indonesia yang sehat dan berprestasi.
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold text-slate-300 text-sm mb-4">Navigasi</h4>
                    <ul class="space-y-2.5">
                        @foreach([['#beranda','Beranda'],['#profil','Profil & Statistik'],['#menu','Menu Harian'],['#distribusi','Peta Distribusi'],['#gunakan','Gunakan Sistem']] as [$href,$label])
                        <li><a href="{{ $href }}" class="text-slate-500 hover:text-slate-300 text-sm transition-colors">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-slate-300 text-sm mb-4">Kontak</h4>
                    <ul class="space-y-3">
                        @foreach([
                            ['M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'Jl. Soekarno-Hatta No. 9, Kota Malang 65141'],
                            ['M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', '(0341) 491-234 (Hotline Pengaduan)'],
                            ['M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'info@dapur-mbg-malang.id'],
                        ] as [$icon, $text])
                        <li class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-slate-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                            <span class="text-slate-500 text-xs leading-relaxed">{{ $text }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-slate-300 text-sm mb-4">Standar Mutu</h4>
                    <ul class="space-y-2.5">
                        @foreach([
                            'Laik Higiene Sanitasi (Dinkes Kota Malang)',
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

            <div class="mt-12 pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-slate-600 text-xs">
                    &copy; {{ date('Y') }} SPPG Dapur MBG Malang 01 — PT Sentra Dapur Mandiri. Mitra resmi Badan Gizi Nasional (BGN) RI.
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
