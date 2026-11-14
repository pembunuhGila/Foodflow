<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'FoodFlow')) — {{ config('app.name', 'FoodFlow') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important
        }
    </style>
</head>

<body class="min-h-screen bg-canvas font-sans antialiased text-ink">
@php
    $user = auth()->user();
    $roleValue = $user->role instanceof \BackedEnum ? $user->role->value : (string) $user->role;
    $roleLabel = ['admin' => 'Admin', 'gudang' => 'Staf Gudang', 'produksi' => 'Staf Produksi', 'owner' => 'Owner'][$roleValue] ?? ucfirst($roleValue);
@endphp

    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

        <x-sidebar />

        {{-- Latar gelap saat sidebar terbuka di layar kecil --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-slate-950/60 backdrop-blur-sm lg:hidden"></div>

        <div class="flex min-w-0 flex-1 flex-col">

            {{-- Topbar Operasional Katering --}}
            <header class="sticky top-0 z-20 flex items-center justify-between gap-4 border-b border-line bg-white/95 px-6 py-3 shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
                <div class="flex items-center gap-3">
                    <button type="button" class="rounded-lg p-2 text-ink hover:bg-slate-100 lg:hidden" @click="sidebarOpen = true" aria-label="Buka menu">
                        <x-icon name="menu" class="h-5 w-5" />
                    </button>
                    
                    {{-- Status Operasional Dapur Katering --}}
                    <div class="hidden items-center gap-3 md:flex">
                        <div class="inline-flex items-center gap-2 rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200/60">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Operasional Katering Aktif</span>
                        </div>
                        <span class="text-slate-200">|</span>
                        <div class="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                            <x-icon name="calendar" class="h-3.5 w-3.5 text-slate-400" />
                            <span>{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    {{-- 1-Click Demo Role Switcher --}}
                    @if (!app()->isProduction())
                        <x-demo-role-switcher />
                    @endif

                    {{-- User Profile Pill (bisa diklik menuju Pengaturan Profil) --}}
                    <a href="{{ route('profile.edit') }}" title="Buka Pengaturan Akun &amp; Profil"
                       class="flex items-center gap-3 pl-2 border-l border-line group">
                        <div class="hidden text-right leading-tight sm:block">
                            <div class="text-sm font-semibold text-ink group-hover:text-primary transition-colors">{{ $user->name }}</div>
                            <div class="text-xs text-muted font-medium">{{ $roleLabel }}</div>
                        </div>
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary font-display text-sm font-bold text-white shadow-sm ring-2 ring-primary/20 group-hover:bg-primary-dark transition">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) }}
                        </div>
                    </a>
                </div>
            </header>

            {{-- Slot header lama tetap didukung --}}
            @isset($header)
            <div class="px-6 pt-6">{{ $header }}</div>
            @endisset

            <main class="flex-1 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    <x-confirm-modal />
    <x-logout-modal />
</body>

</html>
