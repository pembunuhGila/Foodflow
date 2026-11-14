<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FoodFlow') }} — Sistem Katering</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important
        }
    </style>
</head>

<body class="font-sans antialiased bg-canvas text-ink">
    <div class="min-h-screen lg:grid lg:grid-cols-2">

        {{-- Panel kiri: branding katering operasional --}}
        <aside class="relative hidden overflow-hidden bg-gradient-to-br from-slate-900 via-blue-950 to-blue-900 p-12 text-white lg:flex lg:flex-col lg:justify-between shadow-2xl">
            <div class="relative flex items-center gap-3">
                <span class="flex h-11 w-14 items-center justify-center rounded-lg bg-white/10 p-2 border border-white/20">
                    <x-brand-logo class="h-full w-full object-contain" />
                </span>
                <div class="leading-tight">
                    <span class="font-display text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                        FoodFlow
                        <span class="rounded bg-blue-500/20 border border-blue-400/30 px-2 py-0.5 text-[11px] font-semibold text-blue-200 uppercase tracking-normal">Katering</span>
                    </span>
                    <span class="text-xs text-blue-200/70">Operasional &amp; Dapur Sentral</span>
                </div>
            </div>

            <div class="relative max-w-lg my-auto py-8">
                <div class="inline-flex items-center gap-2 rounded-md bg-blue-500/10 border border-blue-400/20 px-3 py-1 text-xs font-semibold text-blue-300 uppercase tracking-wider mb-4">
                    <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span>
                    Sistem Operasional Katering
                </div>
                <h2 class="font-display text-3xl font-bold leading-tight tracking-tight text-white sm:text-4xl">
                    Kelola pesanan dapur,
                    <span class="text-blue-300">presisi di setiap porsi.</span>
                </h2>
                <p class="mt-4 text-sm leading-relaxed text-blue-100/75">
                    Platform terpadu untuk mengontrol pesanan katering, kalkulasi komposisi resep, mutasi stok gudang, hingga realisasi masak dapur harian.
                </p>

                <ul class="mt-8 space-y-3">
                    @foreach ([
                    ['cart', 'Pesanan & Komposisi Resep', 'Kalkulasi otomatis kebutuhan bahan baku per porsi menu katering'],
                    ['package', 'Stok Gudang & PO Supplier', 'Peringatan stok minimum dan alur penerimaan barang dari vendor'],
                    ['clock', 'Jadwal & Realisasi Masak', 'Pencatatan hasil olahan dapur dan pemantauan distribusi tepat waktu'],
                    ] as [$icon, $title, $desc])
                    <li class="flex items-center gap-4 rounded-xl border border-white/10 bg-white/5 p-3.5 backdrop-blur-sm">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-500/20 border border-blue-400/20 text-blue-200">
                            <x-icon :name="$icon" class="h-5 w-5" />
                        </span>
                        <div>
                            <div class="text-sm font-semibold text-white">{{ $title }}</div>
                            <div class="text-xs text-blue-100/70">{{ $desc }}</div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="relative flex items-center justify-between border-t border-white/10 pt-5 text-xs text-blue-200/60">
                <span>Khusus staf operasional: Admin, Gudang, Dapur, &amp; Manajemen</span>
                <span>Versi 0.1</span>
            </div>
        </aside>

        {{-- Panel kanan: form --}}
        <main class="relative flex min-h-screen flex-col items-center justify-center bg-canvas px-6 py-10">
            @isset($back)
            <div class="absolute inset-x-0 top-0 border-b border-line bg-white/60 px-8 py-4 text-small">
                {{ $back }}
            </div>
            @endisset

            <div class="w-full max-w-md my-auto pt-6">

                {{-- Logo untuk layar kecil --}}
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <x-brand-logo class="h-10 w-14" />
                    <span class="font-display text-2xl font-bold text-ink">FoodFlow</span>
                    <span class="rounded bg-primary/10 px-2 py-0.5 text-xs font-semibold text-primary">Katering</span>
                </div>

                @isset($intro)
                <div class="mb-6">{{ $intro }}</div>
                @endisset

                <div class="rounded-2xl border border-line bg-white p-7 sm:p-8 shadow-card">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-center text-xs text-muted">
                    Membutuhkan akun staf operasional? <span class="font-medium text-primary">Hubungi Admin Dapur</span>
                    <br>&copy; {{ date('Y') }} FoodFlow Katering
                </p>
            </div>
        </main>
    </div>
</body>

</html>
