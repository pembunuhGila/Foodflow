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
    @endphp

    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

        <x-sidebar />

        {{-- Latar gelap saat sidebar terbuka di layar kecil --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-ink/40 lg:hidden"></div>

        <div class="flex min-w-0 flex-1 flex-col">

            {{-- Topbar --}}
            <header class="flex items-center justify-between gap-4 border-b border-line bg-white/80 px-6 py-3 backdrop-blur">
                <div class="flex items-center gap-3">
                    <button type="button" class="rounded-full p-2 text-ink hover:bg-primary/10 lg:hidden" @click="sidebarOpen = true" aria-label="Buka menu">
                        <x-icon name="menu" class="h-6 w-6" />
                    </button>
                    <div class="flex items-center gap-2 text-body text-ink">
                        <x-icon name="calendar" class="h-4 w-4 text-muted" />
                        <span class="hidden sm:inline">{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-right leading-tight">
                        <div class="text-body font-medium text-ink">{{ $user->name }}</div>
                        <div class="text-small text-muted">{{ $roleLabel }}</div>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 font-display text-h3 text-primary">
                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) }}
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
</body>

</html>