<!DOCTYPE html>
<html lang="id" x-data="{ mobileMenuOpen: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 20" x-cloak>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="FoodFlow — Sistem ERP Manajemen Dapur MBG untuk SPPG Kota Malang. Kelola produksi, stok, distribusi, dan laporan dalam satu platform digital terintegrasi.">
    <title>FoodFlow — ERP Dapur MBG Malang 01</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }

        .hero-gradient {
            background: linear-gradient(140deg, #EFF6FF 0%, #EDE9FE 55%, #E0E7FF 100%);
        }
        .dark-section {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 60%, #0C2461 100%);
        }
        .card-hover {
            transition: transform 0.22s cubic-bezier(.4,0,.2,1), box-shadow 0.22s cubic-bezier(.4,0,.2,1);
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -8px rgba(15, 23, 42, 0.14);
        }
        .nav-scrolled {
            box-shadow: 0 1px 20px rgba(0,0,0,0.07);
            backdrop-filter: blur(12px);
            background: rgba(255,255,255,0.96) !important;
        }
        .portal-gradient {
            background: linear-gradient(135deg, #1E3A8A 0%, #1D4ED8 60%, #4F46E5 100%);
        }
        .feature-grid-bg {
            background: radial-gradient(ellipse at top left, #EFF6FF 0%, #F8FAFC 60%);
        }

        /* ── Scroll-reveal ── */
        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.55s cubic-bezier(.4,0,.2,1), transform 0.55s cubic-bezier(.4,0,.2,1);
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* stagger children inside a reveal-group */
        .reveal-group > * { opacity: 0; transform: translateY(24px); transition: opacity 0.5s cubic-bezier(.4,0,.2,1), transform 0.5s cubic-bezier(.4,0,.2,1); }
        .reveal-group.visible > *:nth-child(1) { opacity:1; transform:none; transition-delay: 0.05s; }
        .reveal-group.visible > *:nth-child(2) { opacity:1; transform:none; transition-delay: 0.13s; }
        .reveal-group.visible > *:nth-child(3) { opacity:1; transform:none; transition-delay: 0.21s; }
        .reveal-group.visible > *:nth-child(4) { opacity:1; transform:none; transition-delay: 0.29s; }
        .reveal-group.visible > *:nth-child(5) { opacity:1; transform:none; transition-delay: 0.37s; }
        .reveal-group.visible > *:nth-child(6) { opacity:1; transform:none; transition-delay: 0.45s; }

        /* ── Hero entrance ── */
        @keyframes heroFadeUp {
            from { opacity: 0; transform: translateY(32px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .hero-badge  { animation: heroFadeUp 0.6s cubic-bezier(.4,0,.2,1) 0.1s both; }
        .hero-h1     { animation: heroFadeUp 0.65s cubic-bezier(.4,0,.2,1) 0.22s both; }
        .hero-p      { animation: heroFadeUp 0.65s cubic-bezier(.4,0,.2,1) 0.34s both; }
        .hero-cta    { animation: heroFadeUp 0.65s cubic-bezier(.4,0,.2,1) 0.44s both; }
        .hero-stats  { animation: heroFadeUp 0.65s cubic-bezier(.4,0,.2,1) 0.54s both; }

        /* ── Dashboard card float ── */
        @keyframes floatCard {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-8px); }
        }
        .dashboard-float { animation: floatCard 5s ease-in-out infinite; }

        /* ── Floating badge slide-in ── */
        @keyframes badgeSlideIn {
            from { opacity: 0; transform: translate(-12px, 12px) scale(0.92); }
            to   { opacity: 1; transform: translate(0, 0) scale(1); }
        }
        .floating-badge { animation: badgeSlideIn 0.65s cubic-bezier(.4,0,.2,1) 0.75s both; }

        /* ── Progress bar grow ── */
        @keyframes growBar {
            from { width: 0%; }
            to   { width: 68%; }
        }
        .progress-bar { animation: growBar 1.4s cubic-bezier(.4,0,.2,1) 0.6s both; }

        /* ── Stat counter pulse ── */
        @keyframes statPop {
            0%   { transform: scale(0.8); opacity: 0; }
            70%  { transform: scale(1.06); }
            100% { transform: scale(1); opacity: 1; }
        }
        .stat-pop { opacity: 0; }
        .stat-pop.visible { animation: statPop 0.55s cubic-bezier(.4,0,.2,1) forwards; }
        .stat-pop.visible:nth-child(1) { animation-delay: 0.05s; }
        .stat-pop.visible:nth-child(2) { animation-delay: 0.15s; }
        .stat-pop.visible:nth-child(3) { animation-delay: 0.25s; }
        .stat-pop.visible:nth-child(4) { animation-delay: 0.35s; }

        /* ── Orb blobs drift ── */
        @keyframes driftA {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%       { transform: translate(30px, -20px) scale(1.08); }
        }
        @keyframes driftB {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%       { transform: translate(-20px, 15px) scale(1.05); }
        }
        .orb-a { animation: driftA 12s ease-in-out infinite; }
        .orb-b { animation: driftB 10s ease-in-out infinite; }
    </style>
</head>
<body class="bg-white text-slate-800 font-sans antialiased">

    {{-- ===== TOP BAR ===== --}}
    <div class="bg-slate-900 border-b border-slate-700 py-1.5 px-4 print:hidden">
        <div class="max-w-7xl mx-auto flex items-center justify-between flex-wrap gap-2 text-xs">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 text-slate-400 font-medium">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    Sistem aktif — SPPG Dapur MBG Malang 01
                </span>
                <span class="text-slate-600">•</span>
                <span class="text-slate-500">Kota Malang, Jawa Timur</span>
            </div>
            <div class="flex items-center gap-4 text-slate-500">
                <span class="hidden sm:flex items-center gap-1.5">
                    <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Shift: 03.00 – 15.00 WIB
                </span>
                <span class="text-slate-700 hidden sm:inline">•</span>
                <span class="hidden sm:flex items-center gap-1.5">
                    <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    (0341) 491-234
                </span>
            </div>
        </div>
    </div>

    {{-- ===== NAVBAR ===== --}}
    <nav class="sticky top-0 z-50 bg-white border-b border-slate-100 transition-all duration-300 print:hidden" :class="{ 'nav-scrolled': scrolled }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <a href="#beranda" class="flex items-center gap-3" @click="mobileMenuOpen = false">
                    <div class="w-9 h-9 bg-blue-700 rounded-xl flex items-center justify-center shadow flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <div class="font-display font-bold text-slate-900 text-sm">FoodFlow</div>
                        <div class="text-xs text-slate-500">ERP Dapur MBG Malang 01</div>
                    </div>
                </a>

                <div class="hidden lg:flex items-center gap-6">
                    @foreach([
                        ['#beranda', 'Beranda'],
                        ['#erp',     'Fitur ERP'],
                        ['#modul',   'Modul Sistem'],
                        ['#profil',  'Profil Dapur'],
                        ['#menu',    'Menu Harian'],
                    ] as [$href, $label])
                    <a href="{{ $href }}" class="text-sm font-medium text-slate-600 hover:text-blue-700 transition-colors">{{ $label }}</a>
                    @endforeach
                </div>

                @auth
                <a href="{{ route('dashboard') }}" class="hidden lg:inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Buka Dashboard
                </a>
                @else
                @if(Route::has('login'))
                <a href="{{ route('login') }}" class="hidden lg:inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Login Petugas
                </a>
                @endif
                @endauth

                <button class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100" @click="mobileMenuOpen = !mobileMenuOpen">
                    <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="lg:hidden border-t border-slate-100 bg-white px-4 py-3">
            <div class="flex flex-col gap-1">
                @foreach([['#beranda','Beranda'],['#erp','Fitur ERP'],['#modul','Modul Sistem'],['#profil','Profil Dapur'],['#menu','Menu Harian']] as [$h,$l])
                <a href="{{ $h }}" @click="mobileMenuOpen = false" class="text-sm font-medium text-slate-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-3 py-2.5 transition-colors">{{ $l }}</a>
                @endforeach
                @auth
                <a href="{{ route('dashboard') }}" class="mt-2 inline-flex items-center justify-center gap-2 bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg">Buka Dashboard</a>
                @else
                @if(Route::has('login'))
                <a href="{{ route('login') }}" class="mt-2 inline-flex items-center justify-center gap-2 bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg">Login Petugas</a>
                @endif
                @endauth
            </div>
        </div>
    </nav>

    {{-- ===== HERO ===== --}}
    <section id="beranda" class="hero-gradient min-h-[90vh] flex items-center relative overflow-hidden">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="orb-a absolute -top-40 -right-40 w-[500px] h-[500px] bg-blue-300/20 rounded-full blur-3xl"></div>
            <div class="orb-b absolute bottom-0 -left-24 w-80 h-80 bg-indigo-200/20 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full relative z-10">
            <div class="grid lg:grid-cols-2 gap-14 items-center">

                {{-- Left --}}
                <div>
                    <div class="hero-badge inline-flex items-center gap-2 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full px-4 py-1.5 mb-6 border border-blue-200">
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                        Sistem ERP Terintegrasi untuk SPPG MBG
                    </div>

                    <h1 class="hero-h1 font-display font-bold text-4xl sm:text-5xl text-slate-900 leading-[1.1] mb-5">
                        Satu Platform.<br>
                        <span class="text-blue-700">Kelola Semua</span><br>
                        Operasional Dapur.
                    </h1>

                    <p class="hero-p text-slate-600 text-lg leading-relaxed mb-8 max-w-lg">
                        FoodFlow adalah sistem ERP khusus untuk Sentra Pengolahan Pangan Bergizi (SPPG) —
                        menggantikan spreadsheet dan catatan manual dengan platform digital yang real-time,
                        akurat, dan mudah digunakan oleh seluruh tim dapur.
                    </p>

                    <div class="hero-cta flex flex-wrap gap-3 mb-8">
                        @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm px-6 py-3 rounded-xl transition-colors shadow-lg shadow-blue-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Buka Dashboard Sistem
                        </a>
                        @else
                        @if(Route::has('login'))
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm px-6 py-3 rounded-xl transition-colors shadow-lg shadow-blue-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            Masuk ke Sistem
                        </a>
                        @endif
                        @endauth
                        <a href="#erp" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm px-6 py-3 rounded-xl border border-slate-200 transition-colors">
                            Lihat Fitur ERP
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </a>
                    </div>

                    <div class="hero-stats flex flex-wrap items-center gap-5 pt-5 border-t border-slate-200/60">
                        @foreach([
                            ['4', 'Role Pengguna'],
                            ['18+', 'Modul Fitur'],
                            ['Real-time', 'Dashboard KPI'],
                        ] as [$val, $label])
                        <div>
                            <div class="font-display font-bold text-xl text-slate-800">{{ $val }}</div>
                            <div class="text-xs text-slate-500">{{ $label }}</div>
                        </div>
                        <div class="w-px h-8 bg-slate-200 last:hidden"></div>
                        @endforeach
                    </div>
                </div>

                {{-- Right: Dashboard preview --}}
                <div class="relative dashboard-float">
                    <div class="bg-white rounded-2xl shadow-2xl shadow-blue-100/60 border border-slate-100 overflow-hidden">
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
                        <div class="p-4 bg-slate-50 space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                @foreach([
                                    ['Produksi Hari Ini',  '1.250', '+5% dari kemarin',    'text-blue-700',    'text-green-600'],
                                    ['Sekolah Terlayani',  '12',    'Kota Malang',          'text-green-700',   'text-slate-400'],
                                    ['Stok Bahan Kritis',  '3',     'bahan di bawah min',   'text-red-600',     'text-red-500'],
                                    ['PO Pending',         '2',     'menunggu penerimaan',  'text-amber-600',   'text-amber-500'],
                                ] as [$title, $val, $note, $color, $noteColor])
                                <div class="bg-white rounded-xl p-3 border border-slate-100">
                                    <div class="text-xs text-slate-500 mb-1">{{ $title }}</div>
                                    <div class="text-xl font-bold {{ $color }}">{{ $val }}</div>
                                    <div class="text-xs {{ $noteColor }} mt-1">{{ $note }}</div>
                                </div>
                                @endforeach
                            </div>
                            <div class="bg-white rounded-xl p-3 border border-slate-100">
                                <div class="flex justify-between text-xs text-slate-500 mb-2">
                                    <span>Realisasi Produksi Hari Ini</span>
                                    <span class="font-semibold text-blue-700">847 / 1.250 porsi</span>
                                </div>
                                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-2 bg-blue-600 rounded-full progress-bar" style="width: 68%"></div>
                                </div>
                                <div class="text-xs text-slate-400 mt-1.5">67.8% — estimasi selesai pkl. 12.30 WIB</div>
                            </div>
                            {{-- Mini module list --}}
                            <div class="grid grid-cols-3 gap-2">
                                @foreach([
                                    ['📋', 'Rencana Produksi'],
                                    ['📦', 'Stok Bahan'],
                                    ['📄', 'Laporan PDF'],
                                ] as [$e, $l])
                                <div class="bg-white rounded-lg p-2 border border-slate-100 text-center">
                                    <div class="text-lg mb-0.5">{{ $e }}</div>
                                    <div class="text-xs text-slate-500 leading-tight">{{ $l }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Floating badge --}}
                    <div class="floating-badge absolute -bottom-5 -left-5 bg-white rounded-xl shadow-xl border border-slate-100 p-3 flex items-center gap-3 w-52">
                        <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-800">Stok diperbarui</div>
                            <div class="text-xs text-indigo-600">Otomatis saat bahan keluar</div>
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
                <div class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-full px-4 py-1.5 mb-4 border border-indigo-200">
                    Mengapa FoodFlow?
                </div>
                <h2 class="font-display font-bold text-3xl sm:text-4xl text-slate-900 mb-4">
                    Dari Manual ke Digital —<br class="hidden sm:block">Tanpa Kerumitan
                </h2>
                <p class="text-slate-500 text-base max-w-xl mx-auto">
                    FoodFlow menggantikan proses manual yang rawan kesalahan dengan alur kerja digital
                    yang terstruktur, terlacak, dan transparan — dari dapur hingga laporan ke BGN.
                </p>
            </div>

            {{-- Before / After comparison --}}
            <div class="grid lg:grid-cols-2 gap-6 mb-16 reveal-group">
                {{-- Before --}}
                <div class="bg-red-50 border border-red-100 rounded-2xl p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-9 h-9 bg-red-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <span class="font-semibold text-red-700">Tanpa ERP — Cara Lama</span>
                    </div>
                    <ul class="space-y-3">
                        @foreach([
                            'Stok bahan baku dicatat manual di buku / spreadsheet',
                            'Rencana produksi ditulis di papan tulis — mudah hilang',
                            'Realisasi porsi masak dilaporkan via chat WhatsApp',
                            'Laporan bulanan dibuat manual, rentan salah hitung',
                            'Tidak ada jejak siapa menginput apa dan kapan',
                            'Permintaan bahan dapur disampaikan lisan ke gudang',
                        ] as $item)
                        <li class="flex items-start gap-3 text-sm text-red-700">
                            <svg class="w-4 h-4 text-red-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- After --}}
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="font-semibold text-blue-700">Dengan FoodFlow ERP</span>
                    </div>
                    <ul class="space-y-3">
                        @foreach([
                            'Stok bahan baku terupdate otomatis setiap transaksi masuk/keluar',
                            'Rencana produksi digital dengan kalkulasi kebutuhan bahan otomatis',
                            'Realisasi porsi masak tercatat dengan timestamp dan nama petugas',
                            'Laporan PDF siap cetak — inventaris & produksi format A4 portrait',
                            'Audit trail lengkap — setiap aksi terekam dengan akun pengguna',
                            'Form permintaan bahan digital — langsung ke gudang dengan notifikasi',
                        ] as $item)
                        <li class="flex items-start gap-3 text-sm text-blue-700">
                            <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Role cards --}}
            <div class="mb-4">
                <p class="text-center text-xs font-semibold text-slate-400 uppercase tracking-widest mb-6">4 Peran Pengguna Terintegrasi</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 reveal-group">
                @foreach([
                    ['⚙️', 'Admin / Operasional', 'bg-blue-50 border-blue-100', 'text-blue-700', [
                        'Manajemen pengguna & hak akses',
                        'Input pesanan katering (Catering Orders)',
                        'Kelola Purchase Order pengadaan bahan',
                        'Penerimaan & pemeriksaan bahan baku',
                        'Kelola semua data master (supplier, customer, menu)',
                    ]],
                    ['📦', 'Staff Gudang', 'bg-amber-50 border-amber-100', 'text-amber-700', [
                        'Pantau stok bahan baku real-time',
                        'Verifikasi & proses permintaan bahan dapur',
                        'Kartu mutasi stok (stock card)',
                        'Penyesuaian fisik stok (stock adjustment)',
                        'Lihat laporan stok & riwayat transaksi',
                    ]],
                    ['👨‍🍳', 'Staff Produksi', 'bg-green-50 border-green-100', 'text-green-700', [
                        'Akses rencana produksi harian',
                        'Kalkulasi kebutuhan bahan baku otomatis',
                        'Ajukan permintaan bahan ke gudang',
                        'Catat realisasi porsi masak',
                        'Input pemakaian riil bahan produksi',
                    ]],
                    ['📊', 'Manager / Owner', 'bg-purple-50 border-purple-100', 'text-purple-700', [
                        'Dashboard KPI produksi real-time',
                        'Monitoring live capaian produksi',
                        'Evaluasi & monitoring gizi menu',
                        'Unduh laporan produksi PDF',
                        'Unduh laporan inventaris PDF',
                    ]],
                ] as [$emoji, $role, $style, $titleColor, $items])
                <div class="{{ $style }} border rounded-2xl p-5 card-hover flex flex-col">
                    <div class="text-3xl mb-3">{{ $emoji }}</div>
                    <div class="font-display font-bold text-slate-800 mb-3">{{ $role }}</div>
                    <ul class="space-y-1.5 flex-1">
                        @foreach($items as $item)
                        <li class="flex items-start gap-2 text-xs text-slate-600">
                            <svg class="w-3 h-3 {{ $titleColor }} flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== MODUL SISTEM ===== --}}
    <section id="modul" class="py-20 feature-grid-bg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14 reveal">
                <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full px-4 py-1.5 mb-4 border border-blue-200">
                    Modul Sistem
                </div>
                <h2 class="font-display font-bold text-3xl sm:text-4xl text-slate-900 mb-4">Semua yang Dibutuhkan<br class="hidden sm:block"> Dapur MBG</h2>
                <p class="text-slate-500 text-base max-w-lg mx-auto">
                    Setiap modul saling terhubung — perubahan stok memperbarui laporan,
                    rencana produksi mengkalkulasi kebutuhan bahan secara otomatis.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 reveal-group">
                @foreach([
                    [
                        'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                        'Perencanaan Produksi',
                        'bg-blue-100 text-blue-700',
                        'Buat jadwal produksi harian dengan detail menu, jumlah porsi, dan tanggal masak. Kalkulasi kebutuhan bahan dilakukan secara otomatis dari resep.',
                        ['Jadwal masak harian', 'Kalkulasi bahan otomatis', 'Simulasi kalkulasi interaktif'],
                    ],
                    [
                        'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                        'Manajemen Stok Bahan',
                        'bg-amber-100 text-amber-700',
                        'Pantau stok bahan baku secara real-time. Alert otomatis untuk bahan yang mendekati batas minimum agar tidak terjadi kehabisan saat produksi.',
                        ['Stok real-time', 'Alert batas minimum', 'Kartu mutasi stok'],
                    ],
                    [
                        'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        'Permintaan Bahan Dapur',
                        'bg-green-100 text-green-700',
                        'Staff produksi mengajukan permintaan bahan ke gudang secara digital. Gudang memverifikasi dan mencatat transaksi stok keluar dalam satu alur.',
                        ['Form permintaan digital', 'Verifikasi oleh gudang', 'Transaksi stok otomatis'],
                    ],
                    [
                        'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
                        'Purchase Order Pengadaan',
                        'bg-indigo-100 text-indigo-700',
                        'Kelola seluruh proses pengadaan bahan dari pembuatan PO, penerimaan fisik bahan dari supplier, hingga pemeriksaan kualitas secara terstruktur.',
                        ['Buat & kelola PO', 'Penerimaan bahan supplier', 'Pemeriksaan kualitas fisik'],
                    ],
                    [
                        'M11 3.055A9.001 9.001 0 0120.945 13H11V3.055z M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z',
                        'Dashboard & Monitoring',
                        'bg-purple-100 text-purple-700',
                        'Owner dan admin memantau KPI produksi secara live — realisasi porsi, status distribusi, dan evaluasi gizi menu dalam satu layar.',
                        ['KPI produksi real-time', 'Monitoring live capaian', 'Evaluasi gizi menu'],
                    ],
                    [
                        'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
                        'Laporan PDF Otomatis',
                        'bg-rose-100 text-rose-700',
                        'Cetak laporan inventaris dan laporan produksi langsung dari sistem. Format A4 portrait, header table di setiap halaman, siap untuk tanda tangan pejabat.',
                        ['Laporan inventaris PDF', 'Laporan produksi PDF', 'Format A4 siap cetak & TTD'],
                    ],
                ] as [$icon, $title, $badgeStyle, $desc, $tags])
                <div class="bg-white border border-slate-200 rounded-2xl p-6 card-hover group">
                    <div class="w-11 h-11 {{ $badgeStyle }} rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-slate-800 mb-2">{{ $title }}</h3>
                    <p class="text-sm text-slate-500 leading-relaxed mb-4">{{ $desc }}</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($tags as $tag)
                        <span class="text-xs bg-slate-100 text-slate-600 rounded-full px-2.5 py-1">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Security highlight --}}
            <div class="mt-10 bg-slate-900 rounded-2xl p-6 sm:p-8 reveal">
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach([
                        ['M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'Login Aman', 'Password hashed bcrypt + rate limiting 5x/menit per IP'],
                        ['M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'Kontrol Akses', 'Hak akses berbeda per role — tidak ada akses yang tumpang tindih'],
                        ['M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'Audit Trail', 'Setiap aksi terekam: siapa, apa, dan kapan — tidak bisa dihapus'],
                        ['M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9', 'Google SSO', 'Single Sign-On via Google untuk petugas yang sudah terdaftar admin'],
                    ] as [$icon, $title, $desc])
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 bg-blue-900/60 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white mb-1">{{ $title }}</div>
                            <div class="text-xs text-slate-400 leading-relaxed">{{ $desc }}</div>
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
                <div class="inline-flex items-center gap-2 bg-blue-900/50 text-blue-200 text-xs font-semibold rounded-full px-4 py-1.5 mb-4 border border-blue-700/50">
                    Profil & Statistik Dapur
                </div>
                <h2 class="font-display font-bold text-3xl sm:text-4xl text-white mb-3">SPPG Dapur MBG Malang 01</h2>
                <p class="text-slate-400 text-base max-w-lg mx-auto">
                    Data operasional dapur yang dikelola langsung melalui sistem FoodFlow ERP secara real-time.
                </p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-10 stat-container">
                @foreach([
                    ['M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', '1.250+', 'Porsi / Hari', 'Rata-rata harian', 'bg-blue-900/50 text-blue-400'],
                    ['M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', '12', 'Sekolah', 'Kota Malang', 'bg-green-900/50 text-green-400'],
                    ['M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', '98.4%', 'Suhu Layak', 'Standar ≥60°C (BPOM)', 'bg-orange-900/50 text-orange-400'],
                    ['M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', '4', 'Peran Aktif', 'Admin, Gudang, Produksi, Owner', 'bg-purple-900/50 text-purple-400'],
                ] as [$icon, $val, $label, $sub, $iconStyle])
                <div class="bg-slate-800/60 border border-slate-700/40 rounded-2xl p-5 card-hover text-center backdrop-blur-sm stat-pop">
                    <div class="w-11 h-11 {{ $iconStyle }} rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icon }}"/></svg>
                    </div>
                    <div class="text-3xl font-display font-bold text-white mb-1">{{ $val }}</div>
                    <div class="text-sm font-semibold text-slate-300 mb-0.5">{{ $label }}</div>
                    <div class="text-xs text-slate-500">{{ $sub }}</div>
                </div>
                @endforeach
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 reveal-group">
                @foreach([
                    ['Nama Dapur',         'SPPG Dapur MBG Malang 01',                     'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['Wilayah Kerja',      'Kota Malang, Jawa Timur (Kode: MLG-01)',        'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
                    ['Kapasitas Produksi', '1.500 porsi / hari (kapasitas maksimum)',        'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ['Jam Operasional',    'Senin – Jumat, 03.00 – 15.00 WIB',             'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['Sertifikasi',        'Laik Higiene Sanitasi No. 503/SGS/2024',        'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                    ['Pengelola',          'PT Sentra Dapur Mandiri (Mitra Resmi BGN)',     'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
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

    {{-- ===== MENU HARIAN ===== --}}
    <section id="menu" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 reveal">
                <div class="inline-flex items-center gap-2 bg-green-50 text-green-700 text-xs font-semibold rounded-full px-4 py-1.5 mb-4 border border-green-200">
                    Menu Harian
                </div>
                <h2 class="font-display font-bold text-3xl sm:text-4xl text-slate-900 mb-3">Menu Dikelola Langsung<br class="hidden sm:block"> di FoodFlow</h2>
                <p class="text-slate-500 text-base max-w-lg mx-auto">
                    Data menu harian, komposisi bahan, dan kandungan gizi dikelola dalam modul
                    Master Menu FoodFlow — terhubung langsung ke rencana produksi dan monitoring gizi owner.
                </p>
            </div>

            {{-- 5 kelompok pangan --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-12 reveal-group">
                @foreach([
                    ['🌾', 'Karbohidrat',    'Nasi, Jagung, Kentang',    '±250 kkal'],
                    ['🍗', 'Protein Hewani', 'Ayam, Telur, Ikan, Daging','±120 kkal'],
                    ['🥦', 'Serat Sayur',    'Bayam, Wortel, Buncis',    '±30 kkal'],
                    ['🍊', 'Buah-buahan',    'Jeruk, Pepaya, Pisang',    '±50 kkal'],
                    ['🥛', 'Kalsium',        'Susu UHT, Tahu, Tempe',    '±80 kkal'],
                ] as [$emoji, $label, $example, $kcal])
                <div class="bg-gradient-to-b from-blue-50 to-indigo-50 rounded-2xl p-5 text-center card-hover border border-blue-100">
                    <div class="text-4xl mb-3">{{ $emoji }}</div>
                    <div class="font-semibold text-slate-800 text-sm mb-1">{{ $label }}</div>
                    <div class="text-xs text-slate-500 leading-relaxed mb-2">{{ $example }}</div>
                    <span class="inline-flex items-center bg-blue-100 text-blue-700 text-xs font-semibold rounded-full px-2.5 py-0.5">{{ $kcal }}</span>
                </div>
                @endforeach
            </div>

            {{-- Sample menu cards --}}
            <div class="grid sm:grid-cols-3 gap-5 reveal-group">
                @foreach([
                    ['Senin',   'Paket A', '🍱', '530 kkal', ['Nasi putih (200g)', 'Ayam goreng bumbu (80g)', 'Tumis bayam wortel (100g)', 'Jeruk segar (100g)', 'Susu UHT plain (200ml)'], 'bg-blue-50 border-blue-100', 'bg-blue-100 text-blue-700', 'text-blue-500'],
                    ['Selasa',  'Paket B', '🫕', '545 kkal', ['Nasi putih (200g)', 'Telur semur (1 butir)', 'Tempe goreng (60g)', 'Sayur bening bayam (150g)', 'Pepaya potong (100g)'],    'bg-green-50 border-green-100', 'bg-green-100 text-green-700', 'text-green-500'],
                    ['Rabu',    'Paket C', '🍲', '560 kkal', ['Nasi putih (200g)', 'Ikan goreng tepung (90g)', 'Tahu bacem (60g)', 'Ca kangkung (100g)', 'Pisang ambon (1 buah)'],        'bg-orange-50 border-orange-100', 'bg-orange-100 text-orange-700', 'text-orange-500'],
                ] as [$day, $pack, $emoji, $kcal, $items, $bgBorder, $badge, $check])
                <div class="bg-white rounded-2xl border {{ $bgBorder }} shadow-sm card-hover overflow-hidden">
                    <div class="px-5 py-4 {{ $bgBorder }} flex items-center justify-between">
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

            <p class="text-center text-xs text-slate-400 mt-6">
                Data menu, resep, dan kandungan gizi dikelola di modul <strong class="text-slate-600">Master Menu & Resep Builder</strong> — dapat diperbarui kapan saja oleh Admin atau Staff Produksi.
            </p>
        </div>
    </section>

    {{-- ===== PORTAL / CTA ===== --}}
    <section class="portal-gradient py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 bg-white/10 text-blue-200 text-xs font-semibold rounded-full px-4 py-1.5 mb-6 border border-white/20 reveal">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Portal Petugas FoodFlow
            </div>

            <h2 class="font-display font-bold text-3xl sm:text-4xl text-white mb-4">Mulai Kelola Dapur<br class="hidden sm:block"> Lebih Efisien Hari Ini</h2>
            <p class="text-blue-200 text-base max-w-lg mx-auto mb-10">
                Masuk ke sistem FoodFlow dengan akun yang sudah didaftarkan Admin.
                Tersedia akses Google SSO untuk kemudahan login petugas SPPG MBG.
            </p>

            <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-6 sm:p-8 max-w-md mx-auto reveal">
                <div class="grid grid-cols-2 gap-3 mb-6">
                    @foreach([['⚙️','Admin / Operasional'],['📦','Staff Gudang'],['👨‍🍳','Staff Produksi'],['📊','Manager / Owner']] as [$e,$r])
                    <div class="bg-white/10 rounded-xl px-3 py-2.5 flex items-center gap-2">
                        <span class="text-base">{{ $e }}</span>
                        <span class="font-medium text-xs text-white">{{ $r }}</span>
                    </div>
                    @endforeach
                </div>

                @auth
                <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center gap-2 bg-white text-blue-700 font-bold text-sm px-6 py-3.5 rounded-xl hover:bg-blue-50 transition-colors shadow-lg mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Buka Dashboard Saya
                </a>
                @else
                @if(Route::has('login'))
                <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-2 bg-white text-blue-700 font-bold text-sm px-6 py-3.5 rounded-xl hover:bg-blue-50 transition-colors shadow-lg mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Masuk ke FoodFlow
                </a>
                @if(Route::has('auth.google'))
                <a href="{{ route('auth.google') }}" class="w-full inline-flex items-center justify-center gap-2 border border-white/40 text-white font-semibold text-sm px-6 py-3 rounded-xl hover:bg-white/10 transition-colors">
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

                <p class="text-blue-300 text-xs text-center mt-4">
                    Belum punya akun? Hubungi Admin — <span class="font-semibold">(0341) 491-234</span>
                </p>
            </div>
        </div>
    </section>

    {{-- ===== FOOTER ===== --}}
    <footer class="dark-section border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-10">

                <div class="sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 bg-blue-700 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        </div>
                        <div>
                            <div class="font-display font-bold text-white text-sm">FoodFlow</div>
                            <div class="text-xs text-slate-500">ERP Dapur MBG Malang 01</div>
                        </div>
                    </div>
                    <p class="text-slate-500 text-xs leading-relaxed">
                        Sistem ERP manajemen dapur digital khusus untuk SPPG MBG — mendukung program
                        Makan Bergizi Gratis yang diselenggarakan Badan Gizi Nasional (BGN) RI.
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold text-slate-300 text-sm mb-4">Navigasi</h4>
                    <ul class="space-y-2.5">
                        @foreach([['#beranda','Beranda'],['#erp','Fitur ERP'],['#modul','Modul Sistem'],['#profil','Profil Dapur'],['#menu','Menu Harian']] as [$href,$label])
                        <li><a href="{{ $href }}" class="text-slate-500 hover:text-slate-300 text-sm transition-colors">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-slate-300 text-sm mb-4">Kontak</h4>
                    <ul class="space-y-3">
                        @foreach([
                            ['M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'Jl. Soekarno-Hatta No. 9, Kota Malang 65141'],
                            ['M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', '(0341) 491-234'],
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
                    &copy; {{ date('Y') }} FoodFlow — SPPG Dapur MBG Malang 01. PT Sentra Dapur Mandiri, mitra resmi BGN RI.
                </p>
                <div class="flex items-center gap-4 text-xs text-slate-600">
                    <a href="#" class="hover:text-slate-400 transition-colors">Kebijakan Privasi</a>
                    <span class="text-slate-700">•</span>
                    <a href="#" class="hover:text-slate-400 transition-colors">Syarat & Ketentuan</a>
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
