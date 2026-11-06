<!DOCTYPE html>
<html lang="id" x-data="{ mobileMenuOpen: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 20" x-cloak>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="FoodFlow — Sistem ERP Manajemen Katering Modern & Dapur Produksi. Kelola pesanan katering, resep menu, stok bahan baku, jadwal masak, dan rekapitulasi laporan dalam satu platform terintegrasi.">
    <title>FoodFlow — ERP Manajemen Bisnis Katering & Dapur Produksi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }

        /* ── Brand Colors & Gradients ── */
        .hero-gradient {
            background: linear-gradient(140deg, #FAF8FF 0%, #F3EFFF 50%, #ECE5FF 100%);
        }
        .dark-section {
            background: linear-gradient(135deg, #0F0E26 0%, #18153B 55%, #1E1B4B 100%);
        }
        .footer-section {
            background: linear-gradient(180deg, #131032 0%, #0A081D 100%);
        }
        .portal-gradient {
            background: linear-gradient(135deg, #3B1C94 0%, #5C34DB 50%, #7C5CE0 100%);
        }
        .feature-grid-bg {
            background: radial-gradient(ellipse at top left, #F6F2FF 0%, #FBFBFE 60%);
        }

        /* ── Card Transitions & Interactive Glow ── */
        .card-hover {
            transition: transform 0.24s cubic-bezier(.4,0,.2,1), box-shadow 0.24s cubic-bezier(.4,0,.2,1), border-color 0.24s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -10px rgba(124, 92, 224, 0.18);
            border-color: rgba(124, 92, 224, 0.45);
        }
        .nav-scrolled {
            box-shadow: 0 4px 20px rgba(124, 92, 224, 0.08);
            backdrop-filter: blur(14px);
            background: rgba(255, 255, 255, 0.96) !important;
        }

        /* ── Scroll-reveal ── */
        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.6s cubic-bezier(.4,0,.2,1), transform 0.6s cubic-bezier(.4,0,.2,1);
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Stagger reveal inside group */
        .reveal-group > * {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.52s cubic-bezier(.4,0,.2,1), transform 0.52s cubic-bezier(.4,0,.2,1);
        }
        .reveal-group.visible > *:nth-child(1) { opacity: 1; transform: none; transition-delay: 0.05s; }
        .reveal-group.visible > *:nth-child(2) { opacity: 1; transform: none; transition-delay: 0.13s; }
        .reveal-group.visible > *:nth-child(3) { opacity: 1; transform: none; transition-delay: 0.21s; }
        .reveal-group.visible > *:nth-child(4) { opacity: 1; transform: none; transition-delay: 0.29s; }
        .reveal-group.visible > *:nth-child(5) { opacity: 1; transform: none; transition-delay: 0.37s; }
        .reveal-group.visible > *:nth-child(6) { opacity: 1; transform: none; transition-delay: 0.45s; }

        /* ── Hero Entrance Keyframes ── */
        @keyframes heroFadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .hero-badge  { animation: heroFadeUp 0.6s cubic-bezier(.4,0,.2,1) 0.1s both; }
        .hero-h1     { animation: heroFadeUp 0.65s cubic-bezier(.4,0,.2,1) 0.22s both; }
        .hero-p      { animation: heroFadeUp 0.65s cubic-bezier(.4,0,.2,1) 0.34s both; }
        .hero-cta    { animation: heroFadeUp 0.65s cubic-bezier(.4,0,.2,1) 0.44s both; }
        .hero-stats  { animation: heroFadeUp 0.65s cubic-bezier(.4,0,.2,1) 0.54s both; }

        /* ── Floating Mockup Animation ── */
        @keyframes floatCard {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-8px); }
        }
        .dashboard-float { animation: floatCard 5.5s ease-in-out infinite; }

        /* ── Progress Bar Growth ── */
        @keyframes growBar {
            from { width: 0%; }
            to   { width: 68%; }
        }
        .progress-bar { animation: growBar 1.4s cubic-bezier(.4,0,.2,1) 0.6s both; }

        /* ── Stat Pop Animation ── */
        @keyframes statPop {
            0%   { transform: scale(0.85); opacity: 0; }
            70%  { transform: scale(1.04); }
            100% { transform: scale(1); opacity: 1; }
        }
        .stat-pop { opacity: 0; }
        .stat-pop.visible { animation: statPop 0.55s cubic-bezier(.4,0,.2,1) forwards; }
        .stat-pop.visible:nth-child(1) { animation-delay: 0.05s; }
        .stat-pop.visible:nth-child(2) { animation-delay: 0.15s; }
        .stat-pop.visible:nth-child(3) { animation-delay: 0.25s; }
        .stat-pop.visible:nth-child(4) { animation-delay: 0.35s; }

        /* ── Ambient Glow Blobs ── */
        @keyframes driftA {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%       { transform: translate(25px, -18px) scale(1.08); }
        }
        @keyframes driftB {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%       { transform: translate(-20px, 16px) scale(1.05); }
        }
        .orb-a { animation: driftA 12s ease-in-out infinite; }
        .orb-b { animation: driftB 10s ease-in-out infinite; }

        /* ── Pulse Badge Animation ── */
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(124, 92, 224, 0.4); }
            50% { box-shadow: 0 0 0 8px rgba(124, 92, 224, 0); }
        }
        .pulse-glow { animation: pulseGlow 2.5s infinite; }
    </style>
</head>
<body class="bg-white text-slate-800 font-sans antialiased">

    {{-- ===== TOP BAR ===== --}}
    <div class="bg-[#120F2E] border-b border-[#2A2356] py-2 px-4 print:hidden">
        <div class="max-w-7xl mx-auto flex items-center justify-between flex-wrap gap-2 text-xs">
            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="inline-flex items-center gap-1.5 text-purple-200 font-medium">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-ping"></span>
                    Sistem Aktif — Dapur Sentral Katering & Produksi
                </span>
                <span class="text-purple-400/40">•</span>
                <span class="text-purple-300/80">Kota Malang, Jawa Timur</span>
            </div>
            <div class="flex items-center gap-4 text-purple-300/70">
                <span class="hidden sm:flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Shift Dapur: 04.00 – 16.00 WIB
                </span>
                <span class="text-purple-400/40 hidden sm:inline">•</span>
                <span class="hidden sm:flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    Hotline Katering: (0341) 491-234
                </span>
            </div>
        </div>
    </div>

    {{-- ===== NAVBAR ===== --}}
    <nav class="sticky top-0 z-50 bg-white border-b border-purple-100 transition-all duration-300 print:hidden" :class="{ 'nav-scrolled': scrolled }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <a href="#beranda" class="flex items-center gap-3 group" @click="mobileMenuOpen = false">
                    <div class="w-10 h-10 bg-[#7C5CE0] rounded-xl flex items-center justify-center shadow-md shadow-purple-500/20 group-hover:scale-105 transition-transform flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <div class="font-display font-bold text-slate-900 text-base tracking-tight flex items-center gap-1.5">
                            FoodFlow
                            <span class="text-[10px] uppercase font-bold bg-purple-100 text-[#7C5CE0] px-1.5 py-0.5 rounded">ERP</span>
                        </div>
                        <div class="text-xs text-slate-500 font-medium">Sistem Katering &amp; Dapur Sentral</div>
                    </div>
                </a>

                <div class="hidden lg:flex items-center gap-7">
                    @foreach([
                        ['#beranda', 'Beranda'],
                        ['#erp',     'Fitur ERP'],
                        ['#modul',   'Modul Sistem'],
                        ['#profil',  'Profil Dapur'],
                        ['#menu',    'Menu & Paket'],
                    ] as [$href, $label])
                    <a href="{{ $href }}" class="text-sm font-medium text-slate-600 hover:text-[#7C5CE0] transition-colors relative py-1 after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-[#7C5CE0] after:scale-x-0 hover:after:scale-x-100 after:transition-transform">{{ $label }}</a>
                    @endforeach
                </div>

                @auth
                <a href="{{ route('dashboard') }}" class="hidden lg:inline-flex items-center gap-2 bg-[#7C5CE0] hover:bg-[#6B46C1] text-white text-sm font-semibold px-4 py-2 rounded-xl transition-all shadow-md shadow-purple-500/25 hover:shadow-purple-500/40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Buka Dashboard
                </a>
                @else
                @if(Route::has('login'))
                <a href="{{ route('login') }}" class="hidden lg:inline-flex items-center gap-2 bg-[#7C5CE0] hover:bg-[#6B46C1] text-white text-sm font-semibold px-4 py-2 rounded-xl transition-all shadow-md shadow-purple-500/25 hover:shadow-purple-500/40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Login Petugas
                </a>
                @endif
                @endauth

                <button class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-purple-50 hover:text-[#7C5CE0] transition-colors" @click="mobileMenuOpen = !mobileMenuOpen">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="lg:hidden border-t border-purple-100 bg-white px-4 py-3 shadow-xl">
            <div class="flex flex-col gap-1">
                @foreach([['#beranda','Beranda'],['#erp','Fitur ERP'],['#modul','Modul Sistem'],['#profil','Profil Dapur'],['#menu','Menu & Paket']] as [$h,$l])
                <a href="{{ $h }}" @click="mobileMenuOpen = false" class="text-sm font-medium text-slate-700 hover:text-[#7C5CE0] hover:bg-purple-50 rounded-lg px-3 py-2.5 transition-colors">{{ $l }}</a>
                @endforeach
                @auth
                <a href="{{ route('dashboard') }}" class="mt-2 inline-flex items-center justify-center gap-2 bg-[#7C5CE0] text-white text-sm font-semibold px-4 py-2.5 rounded-xl">Buka Dashboard</a>
                @else
                @if(Route::has('login'))
                <a href="{{ route('login') }}" class="mt-2 inline-flex items-center justify-center gap-2 bg-[#7C5CE0] text-white text-sm font-semibold px-4 py-2.5 rounded-xl">Login Petugas</a>
                @endif
                @endauth
            </div>
        </div>
    </nav>

    {{-- ===== HERO ===== --}}
    <section id="beranda" class="hero-gradient min-h-[90vh] flex items-center relative overflow-hidden">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="orb-a absolute -top-40 -right-40 w-[550px] h-[550px] bg-purple-300/25 rounded-full blur-3xl"></div>
            <div class="orb-b absolute bottom-0 -left-28 w-96 h-96 bg-indigo-300/25 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full relative z-10">
            <div class="grid lg:grid-cols-2 gap-14 items-center">

                {{-- Left Content --}}
                <div>
                    <div class="hero-badge inline-flex items-center gap-2 bg-purple-100/90 text-[#7C5CE0] text-xs font-semibold rounded-full px-4 py-1.5 mb-6 border border-purple-200 shadow-sm">
                        <span class="w-2 h-2 bg-[#7C5CE0] rounded-full pulse-glow"></span>
                        Platform ERP Manajemen Dapur &amp; Katering Modern
                    </div>

                    <h1 class="hero-h1 font-display font-bold text-4xl sm:text-5xl text-slate-900 leading-[1.12] mb-5">
                        Satu Platform.<br>
                        <span class="text-[#7C5CE0]">Kelola Seluruh</span><br>
                        Bisnis Katering Anda.
                    </h1>

                    <p class="hero-p text-slate-600 text-lg leading-relaxed mb-8 max-w-lg">
                        FoodFlow mendigitalkan operasional bisnis katering dan dapur sentral — dari penerimaan pesanan nasi kotak &amp; prasmanan, kalkulasi resep otomatis, kontrol stok gudang, hingga pencatatan realisasi masak harian tanpa selisih.
                    </p>

                    <div class="hero-cta flex flex-wrap gap-3.5 mb-8">
                        @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-[#7C5CE0] hover:bg-[#6B46C1] text-white font-semibold text-sm px-6 py-3.5 rounded-xl transition-all shadow-lg shadow-purple-500/30 hover:scale-[1.02] active:scale-[0.98]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Buka Dashboard Sistem
                        </a>
                        @else
                        @if(Route::has('login'))
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-[#7C5CE0] hover:bg-[#6B46C1] text-white font-semibold text-sm px-6 py-3.5 rounded-xl transition-all shadow-lg shadow-purple-500/30 hover:scale-[1.02] active:scale-[0.98]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            Masuk ke Sistem
                        </a>
                        @endif
                        @endauth
                        <a href="#erp" class="inline-flex items-center gap-2 bg-white hover:bg-purple-50/60 text-slate-700 font-semibold text-sm px-6 py-3.5 rounded-xl border border-purple-200 transition-colors shadow-sm">
                            Lihat Fitur ERP
                            <svg class="w-4 h-4 text-[#7C5CE0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </a>
                    </div>

                    <div class="hero-stats flex flex-wrap items-center gap-6 pt-5 border-t border-purple-200/70">
                        @foreach([
                            ['4', 'Role Terintegrasi'],
                            ['18+', 'Modul Katering'],
                            ['Real-time', 'Dashboard Operasional'],
                        ] as [$val, $label])
                        <div>
                            <div class="font-display font-bold text-xl text-slate-900">{{ $val }}</div>
                            <div class="text-xs text-slate-500 font-medium">{{ $label }}</div>
                        </div>
                        <div class="w-px h-8 bg-purple-200 last:hidden"></div>
                        @endforeach
                    </div>
                </div>

                {{-- Right: Dashboard preview --}}
                <div class="relative dashboard-float">
                    <div class="bg-white rounded-2xl shadow-2xl shadow-purple-900/10 border border-purple-100 overflow-hidden">
                        <div class="bg-slate-900 px-4 py-2.5 flex items-center gap-2 border-b border-slate-800">
                            <div class="flex gap-1.5">
                                <div class="w-2.5 h-2.5 bg-red-400 rounded-full"></div>
                                <div class="w-2.5 h-2.5 bg-amber-400 rounded-full"></div>
                                <div class="w-2.5 h-2.5 bg-emerald-400 rounded-full"></div>
                            </div>
                            <div class="flex-1 bg-slate-800/80 rounded-md px-3 py-1 text-xs text-purple-200/70 mx-3 truncate">
                                foodflow-app.duckdns.org/dashboard
                            </div>
                        </div>
                        <div class="p-5 bg-[#FAF9FE] space-y-3.5">
                            <div class="grid grid-cols-2 gap-3">
                                @foreach([
                                    ['Pesanan Katering Hari Ini',  '1.250', 'Paket Nasi Kotak & Event', 'text-[#7C5CE0]',    'text-purple-600'],
                                    ['Klien / Mitra Aktif',        '14',    'Sekolah & Instansi',        'text-emerald-700', 'text-slate-500'],
                                    ['Stok Bahan Kritis',          '3',     'Perlu belanja segera',      'text-rose-600',    'text-rose-500'],
                                    ['PO Pengadaan Bahan',         '2',     'Menunggu vendor kirim',    'text-amber-600',   'text-amber-600'],
                                ] as [$title, $val, $note, $color, $noteColor])
                                <div class="bg-white rounded-xl p-3.5 border border-purple-100/70 shadow-sm">
                                    <div class="text-xs text-slate-500 mb-1">{{ $title }}</div>
                                    <div class="text-2xl font-bold {{ $color }}">{{ $val }}</div>
                                    <div class="text-[11px] {{ $noteColor }} mt-1 font-medium">{{ $note }}</div>
                                </div>
                                @endforeach
                            </div>
                            <div class="bg-white rounded-xl p-3.5 border border-purple-100/70 shadow-sm">
                                <div class="flex justify-between text-xs text-slate-500 mb-2">
                                    <span class="font-medium">Progres Masak Dapur Sentral</span>
                                    <span class="font-bold text-[#7C5CE0]">847 / 1.250 porsi</span>
                                </div>
                                <div class="h-2.5 bg-purple-50 rounded-full overflow-hidden">
                                    <div class="h-2.5 bg-[#7C5CE0] rounded-full progress-bar" style="width: 68%"></div>
                                </div>
                                <div class="text-xs text-slate-400 mt-1.5 flex justify-between">
                                    <span>67.8% selesai</span>
                                    <span>Target distribusi pkl. 11.30 WIB</span>
                                </div>
                            </div>
                            {{-- Mini module list --}}
                            <div class="grid grid-cols-3 gap-2">
                                @foreach([
                                    ['📦', 'Katering Order'],
                                    ['🍳', 'Jadwal Produksi'],
                                    ['📄', 'Laporan PDF'],
                                ] as [$e, $l])
                                <div class="bg-white rounded-lg p-2.5 border border-purple-100/60 text-center hover:border-purple-300 transition-colors">
                                    <div class="text-xl mb-0.5">{{ $e }}</div>
                                    <div class="text-xs text-slate-600 font-semibold leading-tight">{{ $l }}</div>
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
    <section id="erp" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14 reveal">
                <div class="inline-flex items-center gap-2 bg-purple-100 text-[#7C5CE0] text-xs font-semibold rounded-full px-4 py-1.5 mb-4 border border-purple-200">
                    Solusi Terintegrasi Bisnis Katering
                </div>
                <h2 class="font-display font-bold text-3xl sm:text-4xl text-slate-900 mb-4">
                    Tinggalkan Cara Manual,<br class="hidden sm:block"> Otomatiskan Bisnis Katering
                </h2>
                <p class="text-slate-500 text-base max-w-2xl mx-auto">
                    Dari buku catatan kertas dan chat WhatsApp yang berantakan, beralih ke alur kerja katering digital yang terstruktur, presisi dalam kalkulasi resep, dan transparan dari gudang hingga laporan keuangan.
                </p>
            </div>

            {{-- Before / After comparison --}}
            <div class="grid lg:grid-cols-2 gap-6 mb-16 reveal-group">
                {{-- Before --}}
                <div class="bg-rose-50/70 border border-rose-200/80 rounded-2xl p-6 sm:p-8 card-hover">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center text-rose-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <span class="font-bold text-rose-800 text-base">Tanpa ERP — Cara Konvensional</span>
                    </div>
                    <ul class="space-y-3.5">
                        @foreach([
                            'Stok bahan baku dicatat di buku tulis — rawan selisih saat belanja ke pasar',
                            'Pesanan katering dicatat lewat chat WA — sering lupa pesanan khusus klien',
                            'Kebutuhan gramasi bahan dihitung manual — rentan over-budget atau kekurangan bahan',
                            'Jadwal masak di papan tulis — koordinasi chef dan gudang sering miskomunikasi',
                            'Laporan rekap bahan dan biaya makan dibuat manual berhari-hari',
                            'Tidak ada bukti audit siapa staf yang mengeluarkan bahan baku dari gudang',
                        ] as $item)
                        <li class="flex items-start gap-3 text-sm text-rose-700">
                            <svg class="w-4 h-4 text-rose-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                            <span>{{ $item }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- After --}}
                <div class="bg-purple-50/80 border border-purple-200/90 rounded-2xl p-6 sm:p-8 card-hover">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center text-[#7C5CE0]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="font-bold text-[#4C2FB5] text-base">Dengan FoodFlow ERP Katering</span>
                    </div>
                    <ul class="space-y-3.5">
                        @foreach([
                            'Stok bahan baku terpotong otomatis dan tersinkronisasi realtime setiap ada mutasi keluar',
                            'Manajemen pesanan katering terpusat dengan rincian paket, porsi, dan jadwal kirim',
                            'Kalkulator resep cerdas — otomatis menghitung kebutuhan kg beras, sayur, & daging per batch',
                            'Jadwal produksi dapur live — seluruh tim dapur melihat instruksi kerja yang sama',
                            'Laporan rekapitulasi produksi & mutasi persediaan siap cetak A4 Portrait kapan saja',
                            'Audit trail lengkap mencatat siapa staf yang menyetujui dan mengambil barang gudang',
                        ] as $item)
                        <li class="flex items-start gap-3 text-sm text-[#4C2FB5]">
                            <svg class="w-4 h-4 text-[#7C5CE0] flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>{{ $item }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Role cards --}}
            <div class="mb-4">
                <p class="text-center text-xs font-semibold text-purple-900/60 uppercase tracking-widest mb-6">4 Peran Pengguna Terhubung dalam Satu Ekosistem</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 reveal-group">
                @foreach([
                    ['⚙️', 'Admin & Kasir Katering', 'bg-blue-50/70 border-blue-100', 'text-blue-700', [
                        'Input pesanan katering (nasi kotak, event, langganan)',
                        'Manajemen data master pelanggan & supplier',
                        'Kelola Purchase Order pengadaan bahan baku',
                        'Approval permohonan reset password staf',
                        'Kontrol akses akun & status pegawai',
                    ]],
                    ['📦', 'Staff Gudang / Logistik', 'bg-amber-50/70 border-amber-100', 'text-amber-700', [
                        'Pantau saldo stok bahan mentah realtime',
                        'Verifikasi pengeluaran bahan untuk dapur masak',
                        'Kartu mutasi persediaan (Stock Card)',
                        'Penyesuaian fisik opname stok berkala',
                        'Pemeriksaan fisik bahan masuk dari supplier',
                    ]],
                    ['👨‍🍳', 'Head Chef & Produksi', 'bg-emerald-50/70 border-emerald-100', 'text-emerald-700', [
                        'Akses jadwal masak harian dapur katering',
                        'Kalkulasi resep & gramasi bahan otomatis',
                        'Ajukan permintaan bahan masak ke gudang',
                        'Catat realisasi porsi matang & suhu sajian',
                        'Input pemakaian riil bahan per batch masak',
                    ]],
                    ['📊', 'Owner & Manajemen', 'bg-purple-50/70 border-purple-100', 'text-[#7C5CE0]', [
                        'Dashboard live omset porsi & status pesanan',
                        'Monitoring capaian produksi dapur realtime',
                        'Evaluasi kepatuhan komposisi menu',
                        'Cetak laporan rekapitulasi produksi PDF',
                        'Cetak laporan mutasi persediaan gudang PDF',
                    ]],
                ] as [$emoji, $role, $style, $titleColor, $items])
                <div class="{{ $style }} border rounded-2xl p-5 card-hover flex flex-col shadow-sm">
                    <div class="text-3xl mb-3">{{ $emoji }}</div>
                    <div class="font-display font-bold text-slate-800 mb-3 text-base">{{ $role }}</div>
                    <ul class="space-y-2 flex-1">
                        @foreach($items as $item)
                        <li class="flex items-start gap-2 text-xs text-slate-600">
                            <svg class="w-3.5 h-3.5 {{ $titleColor }} flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
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
    <section id="modul" class="py-20 feature-grid-bg border-y border-purple-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14 reveal">
                <div class="inline-flex items-center gap-2 bg-purple-100 text-[#7C5CE0] text-xs font-semibold rounded-full px-4 py-1.5 mb-4 border border-purple-200">
                    Modul Bisnis Katering
                </div>
                <h2 class="font-display font-bold text-3xl sm:text-4xl text-slate-900 mb-4">Fitur Lengkap untuk<br class="hidden sm:block"> Operasional Dapur Katering</h2>
                <p class="text-slate-500 text-base max-w-lg mx-auto">
                    Setiap modul dirancang saling terhubung — perubahan stok memperbarui laporan keuangan, rencana masak mengkalkulasi kebutuhan bahan otomatis.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 reveal-group">
                @foreach([
                    [
                        'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                        'Pesanan Katering (Orders)',
                        'bg-purple-100 text-[#7C5CE0]',
                        'Kelola pesanan katering nasi kotak, prasmanan, dan langganan instansi. Lengkap dengan detail menu, jumlah porsi, jadwal kirim, dan kalkulasi total biaya.',
                        ['Pesanan prasmanan & box', 'Kalkulasi otomatis', 'Tracking status kirim'],
                    ],
                    [
                        'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
                        'Resep & Kalkulator Bahan',
                        'bg-indigo-100 text-indigo-700',
                        'Standarisasi formula resep hidangan katering. Masukkan target jumlah porsi, sistem otomatis menghitung kebutuhan kg beras, daging, dan sayuran.',
                        ['Formula resep standar', 'Kalkulasi gramasi presisi', 'Simulasi kebutuhan masak'],
                    ],
                    [
                        'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                        'Stok Gudang & Mutasi',
                        'bg-amber-100 text-amber-700',
                        'Pantau saldo bahan baku secara realtime. Dilengkapi alert otomatis untuk bahan yang mendekati batas minimum agar tidak kehabisan saat jadwal masak tiba.',
                        ['Stok realtime', 'Peringatan stok minimum', 'Kartu mutasi keluar-masuk'],
                    ],
                    [
                        'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
                        'Purchase Order Supplier',
                        'bg-blue-100 text-blue-700',
                        'Kelola alur pengadaan bahan ke vendor terpercaya — dari pembuatan PO, penerimaan fisik bahan mentah, hingga kontrol standar kualitas kesegaran bahan.',
                        ['Kelola PO supplier', 'Penerimaan fisik bahan', 'Inspeksi mutu & kesegaran'],
                    ],
                    [
                        'M11 3.055A9.001 9.001 0 0120.945 13H11V3.055z M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z',
                        'Jadwal & Realisasi Dapur',
                        'bg-emerald-100 text-emerald-700',
                        'Koordinasi jadwal masak harian chef dan staf dapur. Catat realisasi porsi matang, kontrol suhu makanan layak santap, dan pemakaian riil bahan baku.',
                        ['Jadwal masak digital', 'Pencatatan porsi matang', 'Kontrol suhu sajian'],
                    ],
                    [
                        'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
                        'Laporan PDF Siap Cetak',
                        'bg-rose-100 text-rose-700',
                        'Cetak rekapitulasi produksi dan laporan persediaan gudang langsung dalam format resmi A4 Portrait. Header berulang otomatis di tiap halaman, siap ditandatangani.',
                        ['Format resmi A4 Portrait', 'Rekapitulasi produksi', 'Laporan mutasi persediaan'],
                    ],
                ] as [$icon, $title, $badgeStyle, $desc, $tags])
                <div class="bg-white border border-purple-100 rounded-2xl p-6 card-hover group shadow-sm flex flex-col">
                    <div class="w-12 h-12 {{ $badgeStyle }} rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 group-hover:rotate-2 transition-transform shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-slate-800 text-lg mb-2">{{ $title }}</h3>
                    <p class="text-sm text-slate-500 leading-relaxed mb-4 flex-1">{{ $desc }}</p>
                    <div class="flex flex-wrap gap-1.5 pt-2 border-t border-purple-50">
                        @foreach($tags as $tag)
                        <span class="text-xs bg-purple-50 text-[#7C5CE0] font-medium rounded-full px-2.5 py-1">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Security & Performance highlight --}}
            <div class="mt-12 dark-section rounded-2xl p-6 sm:p-8 border border-purple-900/40 shadow-xl reveal">
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach([
                        ['M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'Login & Akses Terproteksi', 'Bcrypt password hashing + proteksi anti-brute force 5x per menit per IP'],
                        ['M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'Hak Akses Per Role', 'Segregasi ketat: staf gudang, chef dapur, kasir, dan pimpinan tidak saling tumpang tindih'],
                        ['M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'Audit Trail Terlacak', 'Setiap mutasi stok dan approval terekam dengan timestamp dan identitas staf'],
                        ['M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9', 'Google OAuth SSO', 'Dukungan login cepat Google SSO khusus akun staf yang telah diverifikasi Admin'],
                    ] as [$icon, $title, $desc])
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 bg-purple-900/60 rounded-xl flex items-center justify-center flex-shrink-0 border border-purple-500/20">
                            <svg class="w-5 h-5 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white mb-1">{{ $title }}</div>
                            <div class="text-xs text-purple-200/60 leading-relaxed">{{ $desc }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ===== PROFIL DAPUR ===== --}}
    <section id="profil" class="dark-section py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 reveal">
                <div class="inline-flex items-center gap-2 bg-purple-900/60 text-purple-200 text-xs font-semibold rounded-full px-4 py-1.5 mb-4 border border-purple-700/60">
                    Kapasitas &amp; Skala Dapur
                </div>
                <h2 class="font-display font-bold text-3xl sm:text-4xl text-white mb-3">Dapur Sentral FoodFlow Katering</h2>
                <p class="text-purple-200/70 text-base max-w-xl mx-auto">
                    Kapasitas produksi harian dan data operasional yang dikelola secara terpusat melalui sistem FoodFlow ERP.
                </p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-10 stat-container">
                @foreach([
                    ['M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', '1.500+', 'Porsi / Hari', 'Rata-rata produksi harian', 'bg-purple-900/60 text-purple-300'],
                    ['M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', '14+', 'Instansi & Mitra', 'Sekolah, korporat & event', 'bg-emerald-900/60 text-emerald-300'],
                    ['M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', '99.2%', 'Ketepatan Waktu', 'Pengiriman tepat sebelum acara', 'bg-amber-900/60 text-amber-300'],
                    ['M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', '4 Role', 'Divisi Terintegrasi', 'Kasir, Gudang, Dapur, Owner', 'bg-indigo-900/60 text-indigo-300'],
                ] as [$icon, $val, $label, $sub, $iconStyle])
                <div class="bg-[#18153A]/90 border border-purple-900/50 rounded-2xl p-5 card-hover text-center backdrop-blur-sm stat-pop shadow-lg">
                    <div class="w-12 h-12 {{ $iconStyle }} rounded-xl flex items-center justify-center mx-auto mb-3.5 border border-purple-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $icon }}"/></svg>
                    </div>
                    <div class="text-3xl font-display font-bold text-white mb-1">{{ $val }}</div>
                    <div class="text-sm font-semibold text-purple-200 mb-0.5">{{ $label }}</div>
                    <div class="text-xs text-purple-300/60">{{ $sub }}</div>
                </div>
                @endforeach
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 reveal-group">
                @foreach([
                    ['Nama Unit Usaha',     'Dapur Sentral FoodFlow Katering Malang',        'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['Wilayah Layanan',      'Kota Malang & Wilayah Sekitarnya',            'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
                    ['Kapasitas Dapur',      'Maksimal 2.000 porsi per hari',              'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ['Jadwal Operasional',   'Setiap Hari, 04.00 – 16.00 WIB',             'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['Standar Sanitasi',     'Sertifikasi Laik Higiene Sanitasi Jasa Boga', 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                    ['Jenis Layanan',        'Nasi Kotak, Prasmanan Event, Katering Instansi', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                ] as [$label, $val, $icon])
                <div class="bg-[#18153A]/60 border border-purple-900/40 rounded-xl p-4 flex items-start gap-3.5">
                    <div class="w-8 h-8 bg-purple-900/60 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5 border border-purple-500/20">
                        <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    </div>
                    <div>
                        <div class="text-xs text-purple-300/60 mb-0.5">{{ $label }}</div>
                        <div class="text-sm font-medium text-white">{{ $val }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== MENU & PAKET KATERING ===== --}}
    <section id="menu" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 reveal">
                <div class="inline-flex items-center gap-2 bg-purple-100 text-[#7C5CE0] text-xs font-semibold rounded-full px-4 py-1.5 mb-4 border border-purple-200">
                    Katalog Hidangan &amp; Paket
                </div>
                <h2 class="font-display font-bold text-3xl sm:text-4xl text-slate-900 mb-3">Menu Dikelola Terpusat<br class="hidden sm:block"> di Master Menu FoodFlow</h2>
                <p class="text-slate-500 text-base max-w-lg mx-auto">
                    Koleksi resep standar katering, takaran porsi, dan harga pokok penjualan (HPP) dikelola langsung di sistem — siap disimulasikan ke jadwal produksi.
                </p>
            </div>

            {{-- 5 Kelompok Makanan Katering --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-12 reveal-group">
                @foreach([
                    ['🍚', 'Karbohidrat Pilihan', 'Nasi Liwet, Nasi Kuning, Kentang', '±250 kkal'],
                    ['🥩', 'Protein Unggulan',    'Ayam Bakar, Rendang, Ikan Dori',    '±180 kkal'],
                    ['🥗', 'Sayuran Segar',       'Capcay, Tumis Buncis, Sayur Asem',  '±40 kkal'],
                    ['🍉', 'Buah & Pencuci Mulut', 'Semangka, Melon, Puding Buah',     '±60 kkal'],
                    ['🧃', 'Minuman Pelengkap',   'Infused Water, Jus Buah, Teh Dingin','±75 kkal'],
                ] as [$emoji, $label, $example, $kcal])
                <div class="bg-gradient-to-b from-purple-50/60 to-white rounded-2xl p-5 text-center card-hover border border-purple-100 shadow-sm">
                    <div class="text-4xl mb-3">{{ $emoji }}</div>
                    <div class="font-semibold text-slate-900 text-sm mb-1">{{ $label }}</div>
                    <div class="text-xs text-slate-500 leading-relaxed mb-3">{{ $example }}</div>
                    <span class="inline-flex items-center bg-purple-100 text-[#7C5CE0] text-xs font-bold rounded-full px-2.5 py-0.5">{{ $kcal }}</span>
                </div>
                @endforeach
            </div>

            {{-- 3 Contoh Paket Katering Populer --}}
            <div class="grid sm:grid-cols-3 gap-6 reveal-group">
                @foreach([
                    [
                        'Paket Reguler', 'Nasi Kotak Harian', '🍱', 'Rp 22.000 / porsi',
                        ['Nasi putih pulen (200g)', 'Ayam goreng laos gurih (85g)', 'Tumis buncis wortel bakso', 'Kerupuk udang renyah', 'Air mineral & buah pisang'],
                        'border-purple-200', 'bg-purple-50 text-[#7C5CE0]', 'text-[#7C5CE0]'
                    ],
                    [
                        'Paket Premium', 'Corporate Buffet / Prasmanan', '🍲', 'Rp 45.000 / porsi',
                        ['Nasi liwet / nasi putih wangi', 'Daging sapi rendang empuk (90g)', 'Kakap asam manis krispi', 'Capcay kuah seafood spesial', 'Puding cokelat vla vanila'],
                        'border-emerald-200', 'bg-emerald-50 text-emerald-700', 'text-emerald-600'
                    ],
                    [
                        'Paket Sehat', 'Healthy Box Gizi Seimbang', '🥗', 'Rp 28.000 / porsi',
                        ['Nasi merah organik (180g)', 'Dada ayam panggang rosemary', 'Tahu bacem kukus rendah lemak', 'Salad sayur saus wijen sangrai', 'Jus jeruk murni tanpa gula'],
                        'border-amber-200', 'bg-amber-50 text-amber-700', 'text-amber-600'
                    ],
                ] as [$type, $title, $emoji, $price, $items, $border, $badge, $iconColor])
                <div class="bg-white rounded-2xl border {{ $border }} shadow-sm card-hover overflow-hidden flex flex-col">
                    <div class="p-5 border-b border-purple-50 bg-purple-50/30 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-semibold text-purple-900/60 uppercase tracking-wide">{{ $type }}</div>
                            <div class="font-display font-bold text-slate-900 text-base">{{ $title }}</div>
                        </div>
                        <div class="text-3xl">{{ $emoji }}</div>
                    </div>
                    <div class="px-5 py-3 bg-[#FAF8FF] border-b border-purple-50 flex items-center justify-between">
                        <span class="text-xs text-slate-500 font-medium">Estimasi Harga</span>
                        <span class="text-xs font-bold {{ $badge }} px-2.5 py-1 rounded-full">{{ $price }}</span>
                    </div>
                    <ul class="p-5 space-y-2.5 flex-1">
                        @foreach($items as $item)
                        <li class="flex items-center gap-2.5 text-xs text-slate-600">
                            <svg class="w-4 h-4 {{ $iconColor }} flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>{{ $item }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>

            <p class="text-center text-xs text-slate-400 mt-8">
                Master resep, bahan baku, dan komposisi gizi dikelola secara fleksibel di modul <strong class="text-[#7C5CE0]">Resep Builder &amp; Master Menu</strong> oleh tim Chef dan Admin Katering.
            </p>
        </div>
    </section>

    {{-- ===== PORTAL / CTA ===== --}}
    <section class="portal-gradient py-20 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-24 -left-24 w-80 h-80 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-purple-950/20 rounded-full blur-2xl"></div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="inline-flex items-center gap-2 bg-white/15 text-white text-xs font-semibold rounded-full px-4 py-1.5 mb-6 border border-white/25 shadow-sm reveal">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Portal Akses Petugas Dapur Katering
            </div>

            <h2 class="font-display font-bold text-3xl sm:text-4xl text-white mb-4">Kelola Bisnis Katering Anda<br class="hidden sm:block"> Lebih Rapi &amp; Terukur Hari Ini</h2>
            <p class="text-purple-100 text-base max-w-lg mx-auto mb-10">
                Masuk ke sistem FoodFlow dengan akun yang sudah didaftarkan Admin. Tersedia Single Sign-On (SSO) Google untuk kemudahan akses seluruh tim.
            </p>

            <div class="bg-white/10 backdrop-blur-md border border-white/25 rounded-2xl p-6 sm:p-8 max-w-md mx-auto shadow-2xl reveal">
                <div class="grid grid-cols-2 gap-2.5 mb-6">
                    @foreach([['⚙️','Admin & Kasir'],['📦','Staf Gudang'],['👨‍🍳','Chef & Produksi'],['📊','Owner / Manager']] as [$e,$r])
                    <div class="bg-white/15 rounded-xl px-3 py-2 flex items-center gap-2 text-left">
                        <span class="text-sm">{{ $e }}</span>
                        <span class="font-medium text-xs text-white">{{ $r }}</span>
                    </div>
                    @endforeach
                </div>

                @auth
                <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center gap-2 bg-white text-[#4C2FB5] font-bold text-sm px-6 py-3.5 rounded-xl hover:bg-purple-50 transition-all shadow-lg hover:scale-[1.02] active:scale-[0.98] mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Buka Dashboard Saya
                </a>
                @else
                @if(Route::has('login'))
                <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-2 bg-white text-[#4C2FB5] font-bold text-sm px-6 py-3.5 rounded-xl hover:bg-purple-50 transition-all shadow-lg hover:scale-[1.02] active:scale-[0.98] mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Masuk ke FoodFlow
                </a>
                @if(Route::has('auth.google'))
                <a href="{{ route('auth.google') }}" class="w-full inline-flex items-center justify-center gap-2.5 border border-white/40 text-white font-semibold text-sm px-6 py-3 rounded-xl hover:bg-white/10 transition-colors">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#93C5FD"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#86EFAC"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FCD34D"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#FCA5A5"/>
                    </svg>
                    Masuk dengan Google SSO
                </a>
                @endif
                @endif
                @endauth

                <p class="text-purple-200/80 text-xs text-center mt-4">
                    Belum punya akun? Hubungi Admin Dapur — <span class="font-semibold text-white">(0341) 491-234</span>
                </p>
            </div>
        </div>
    </section>

    {{-- ===== FOOTER ===== --}}
    <footer class="footer-section border-t border-[#2A2356] text-slate-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-10">

                <div class="sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-[#7C5CE0] rounded-xl flex items-center justify-center shadow-md shadow-purple-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        </div>
                        <div>
                            <div class="font-display font-bold text-white text-base tracking-tight">FoodFlow</div>
                            <div class="text-xs text-purple-300 font-medium">ERP Katering &amp; Dapur Sentral</div>
                        </div>
                    </div>
                    <p class="text-purple-200/60 text-xs leading-relaxed">
                        Sistem ERP manajemen katering modern — mengintegrasikan pesanan katering, resep gizi, logistik bahan baku, dan pengawasan produksi dapur secara presisi dan akuntabel.
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold text-white text-sm mb-4">Navigasi Halaman</h4>
                    <ul class="space-y-2.5">
                        @foreach([['#beranda','Beranda'],['#erp','Fitur ERP'],['#modul','Modul Sistem'],['#profil','Profil Dapur'],['#menu','Menu & Paket']] as [$href,$label])
                        <li><a href="{{ $href }}" class="text-purple-200/60 hover:text-white text-sm transition-colors">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-white text-sm mb-4">Kontak Dapur Sentral</h4>
                    <ul class="space-y-3">
                        @foreach([
                            ['M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'Jl. Soekarno-Hatta No. 9, Kota Malang 65141'],
                            ['M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', '(0341) 491-234'],
                            ['M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'kontak@foodflow-catering.id'],
                        ] as [$icon, $text])
                        <li class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-[#7C5CE0] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                            <span class="text-purple-200/60 text-xs leading-relaxed">{{ $text }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-white text-sm mb-4">Standar &amp; Mutu Usaha</h4>
                    <ul class="space-y-2.5">
                        @foreach([
                            'Sertifikat Laik Higiene Sanitasi Jasa Boga',
                            'Standar Keamanan Pangan Olahan Siap Saji',
                            'Formula Resep Terstandarisasi Chef',
                            'Audit Mutasi Stok Realtime & Transparan',
                            'Manajemen Pesanan Katering Digital',
                            'Perlindungan Privasi Data Pelanggan',
                        ] as $std)
                        <li class="flex items-start gap-2 text-xs text-purple-200/60">
                            <svg class="w-3.5 h-3.5 text-emerald-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>{{ $std }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-6 border-t border-[#2A2356] flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-purple-300/40 text-xs">
                    &copy; {{ date('Y') }} FoodFlow — Sistem ERP Manajemen Katering &amp; Dapur Sentral Malang. All rights reserved.
                </p>
                <div class="flex items-center gap-4 text-xs text-purple-300/40">
                    <a href="#" class="hover:text-purple-200 transition-colors">Kebijakan Privasi</a>
                    <span class="text-purple-500/40">•</span>
                    <a href="#" class="hover:text-purple-200 transition-colors">Syarat &amp; Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // ── Scroll-reveal (reveal & reveal-group) ──────────────────────────
        (function () {
            const io = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.classList.add('visible');
                        io.unobserve(e.target);
                    }
                });
            }, { threshold: 0.12 });

            document.querySelectorAll('.reveal, .reveal-group').forEach(el => io.observe(el));

            // ── Stat-pop (profil KPI cards) ──────────────────────────────
            const statIo = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.querySelectorAll('.stat-pop').forEach(card => card.classList.add('visible'));
                        statIo.unobserve(e.target);
                    }
                });
            }, { threshold: 0.2 });

            document.querySelectorAll('.stat-container').forEach(el => statIo.observe(el));
        })();
    </script>
</body>
</html>
