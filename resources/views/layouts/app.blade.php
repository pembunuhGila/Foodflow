<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FoodFlow') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important
        }
    </style>
</head>

<body class="min-h-screen font-sans antialiased">
    @php
    $user = auth()->user();
    $roleValue = $user->role instanceof \BackedEnum ? $user->role->value : (string) $user->role;
    $roleLabel = ['admin' => 'Admin', 'gudang' => 'Staf Gudang', 'produksi' => 'Staf Produksi', 'owner' => 'Owner'][$roleValue] ?? ucfirst($roleValue);

    // Jumlah notifikasi belum dibaca. Isi dari backend nanti; 0 = titik merah tidak tampil.
    $unreadNotifications = 0;
    @endphp

    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

        <x-sidebar />

        {{-- Latar gelap saat sidebar terbuka di layar kecil --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-ink/40 lg:hidden"></div>

        <div class="flex min-w-0 flex-1 flex-col">

            {{-- Topbar --}}
            <header class="sticky top-0 z-20 flex items-center justify-between gap-4 border-b border-line bg-white/80 px-6 py-3 backdrop-blur">
                <div class="flex items-center gap-3">
                    <button type="button" class="rounded-full p-2 text-ink hover:bg-primary/10 lg:hidden" @click="sidebarOpen = true" aria-label="Buka menu">
                        <x-icon name="menu" class="h-6 w-6" />
                    </button>
                    <div class="hidden items-center gap-2 text-body text-ink md:flex">
                        <x-icon name="calendar" class="h-4 w-4 text-muted" />
                        <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    {{-- Pencarian: mengirim ?search= ke halaman yang sedang dibuka --}}
                    <form method="GET" action="{{ url()->current() }}" role="search"
                        x-data
                        @keydown.window="if (($event.metaKey || $event.ctrlKey) && $event.key.toLowerCase() === 'k') { $event.preventDefault(); $refs.search.focus() }"
                        class="hidden items-center gap-2 rounded-full border border-line bg-white px-4 py-2 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 sm:flex">
                        <x-icon name="search" class="h-4 w-4 text-muted" />
                        <input x-ref="search" type="search" name="search" value="{{ request('search') }}"
                            placeholder="Cari data, bahan, resep..."
                            class="w-48 border-0 bg-transparent p-0 text-body text-ink placeholder:text-muted focus:ring-0 lg:w-64">
                        <kbd class="rounded-full bg-primary/10 px-2 py-0.5 text-label normal-case tracking-normal text-muted">Ctrl K</kbd>
                    </form>

                    {{-- Notifikasi --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                        <button type="button" @click="open = !open"
                            class="relative flex h-10 w-10 items-center justify-center rounded-full text-ink hover:bg-primary/10"
                            aria-label="Notifikasi">
                            <x-icon name="bell" class="h-5 w-5" />
                            @if ($unreadNotifications > 0)
                            <span class="absolute right-2.5 top-2.5 h-2 w-2 rounded-full bg-primary ring-2 ring-white"></span>
                            @endif
                        </button>
                        <div x-show="open" x-cloak x-transition.opacity
                            class="absolute right-0 mt-2 w-72 rounded-card border border-line bg-white p-4 shadow-card">
                            <div class="text-h3 font-display text-ink">Notifikasi</div>
                            <p class="mt-3 text-small text-muted">Belum ada notifikasi.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pl-1">
                        <div class="hidden text-right leading-tight sm:block">
                            <div class="text-body font-medium text-ink">{{ $user->name }}</div>
                            <div class="text-small text-muted">{{ $roleLabel }}</div>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary font-display text-body font-semibold text-white">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) }}
                        </div>
                    </div>
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

    <x-logout-modal />
</body>

</html>