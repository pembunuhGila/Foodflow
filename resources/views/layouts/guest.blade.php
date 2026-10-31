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

<body class="font-sans antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-2">

        {{-- Panel kiri: branding (disembunyikan di layar kecil) --}}
        <aside class="relative hidden overflow-hidden bg-gradient-to-br from-ink via-secondary to-primary p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="pointer-events-none absolute -left-24 -top-24 h-80 w-80 rounded-full bg-primary/40 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -right-24 h-96 w-96 rounded-full bg-tertiary/40 blur-3xl"></div>

            <div class="relative flex items-center gap-3">
                <span class="flex h-11 w-16 items-center justify-center rounded-input bg-white p-1.5">
                    <x-brand-logo class="h-full w-full" />
                </span>
                <span class="font-display text-h2 italic">FoodFlow</span>
            </div>

            <div class="relative max-w-lg">
                <h2 class="font-display text-4xl font-semibold leading-tight tracking-tight">
                    Kelola dapur produksi,
                    <span class="bg-gradient-to-r from-warning-bg to-tertiary bg-clip-text text-transparent">lebih rapi dan terukur.</span>
                </h2>
                <p class="mt-4 text-body text-white/75">
                    Pantau pergerakan stok bahan mentah, susun jadwal masak harian, dan pantau kalkulasi nutrisi gizi porsi secara presisi dalam satu dashboard terintegrasi.
                </p>

                <ul class="mt-8 space-y-3">
                    @foreach ([
                    ['check', 'Validasi Email Terdaftar', 'Sistem memverifikasi akun aktif sesuai penugasan shift'],
                    ['users', 'Otorisasi Admin', 'Permohonan reset password diteruskan ke dashboard manajemen'],
                    ['key', 'Password Sementara', 'Kredensial baru diberikan setelah permohonan disetujui admin'],
                    ] as [$icon, $title, $desc])
                    <li class="flex items-center gap-4 rounded-card border border-white/15 bg-white/10 p-4 backdrop-blur">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-input bg-white/15">
                            <x-icon :name="$icon" />
                        </span>
                        <div>
                            <div class="text-body font-semibold">{{ $title }}</div>
                            <div class="text-small text-white/70">{{ $desc }}</div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="relative flex items-center justify-between border-t border-white/15 pt-5 text-small text-white/70">
                <span>Digunakan oleh tim Manajer, Chef, &amp; Gudang</span>
                <span>Versi 0.1</span>
            </div>
        </aside>

        {{-- Panel kanan: form --}}
        <main class="relative flex min-h-screen flex-col items-center justify-center bg-canvas px-6 py-10">
            @isset($back)
            <div class="absolute inset-x-0 top-0 border-b border-line px-8 py-5 text-small">{{ $back }}</div>
            @endisset

            <div class="w-full max-w-md">

                {{-- Logo untuk layar kecil --}}
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <x-brand-logo class="h-10 w-16" />
                    <span class="font-display text-h2 text-ink">FoodFlow</span>
                </div>

                @isset($intro)
                <div class="mb-6">{{ $intro }}</div>
                @endisset

                <div class="rounded-card border border-line bg-white p-8 shadow-card">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-center text-small text-muted">
                    Belum memiliki akses dapur katering? <span class="text-primary">Hubungi Admin</span>
                    <br>&copy; {{ date('Y') }} FoodFlow
                </p>
            </div>
        </main>
    </div>
</body>

</html>
