<x-guest-layout>
    <x-slot name="back">
        <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-primary transition-colors">
            <x-icon name="arrow-left" class="h-4 w-4" />
            <span>Kembali ke Halaman Depan</span>
        </a>
    </x-slot>

    <x-slot name="intro">
        <div class="mb-2 inline-flex items-center gap-1.5 rounded-md bg-primary/10 px-2.5 py-0.5 text-[11px] font-bold text-primary uppercase">
            <span>Portal Petugas Katering</span>
        </div>
        <h1 class="text-2xl font-bold font-display text-ink tracking-tight">Masuk ke Sistem</h1>
        <p class="mt-1 text-sm text-muted">Akses dashboard operasional pesanan dan dapur katering.</p>
    </x-slot>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($errors->any())
        <div class="mb-5 flex gap-3 rounded-lg border border-danger-text/20 bg-danger-bg p-3.5 text-xs text-danger-text" role="alert">
            <x-icon name="alert" class="mt-0.5 h-4 w-4 shrink-0" />
            <div>
                <div class="font-bold">Gagal masuk</div>
                <div>{{ $errors->first() }}</div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" x-data="{ show: false }" class="space-y-4">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600">Alamat Email</label>
            <div class="relative">
                <x-icon name="mail" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input id="email" type="email" name="email" value="{{ old('email') }}" x-ref="emailInput"
                       required autofocus autocomplete="username" placeholder="nama@foodflow.test"
                       class="h-11 w-full rounded-lg border bg-white pl-10 pr-4 text-sm text-ink placeholder:text-muted/60 focus:ring-2
                              {{ $errors->has('email') ? 'border-danger-text focus:border-danger-text focus:ring-danger-text/20' : 'border-line focus:border-primary focus:ring-primary/20' }}">
            </div>
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600">Kata Sandi</label>
            <div class="relative">
                <x-icon name="key" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input id="password" name="password" :type="show ? 'text' : 'password'" x-ref="passwordInput"
                       required autocomplete="current-password" placeholder="••••••••"
                       class="h-11 w-full rounded-lg border border-line bg-white pl-10 pr-11 text-sm text-ink placeholder:text-muted/60 focus:border-primary focus:ring-2 focus:ring-primary/20">
                <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                        :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                    <x-icon name="eye" class="h-4 w-4" x-show="!show" />
                    <x-icon name="eye-off" class="h-4 w-4" x-show="show" x-cloak />
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center gap-2 text-xs text-muted">
                <input id="remember_me" type="checkbox" name="remember"
                       class="h-4 w-4 rounded border-line text-primary focus:ring-primary/20">
                Ingat sesi saya
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-primary hover:text-primary-dark">
                    Lupa Password?
                </a>
            @endif
        </div>

        <button type="submit"
                x-data="{ loading: false }"
                @click="if ($el.form && $el.form.checkValidity()) { setTimeout(() => { loading = true }, 50) }"
                :disabled="loading"
                :class="{ 'opacity-75 cursor-wait pointer-events-none': loading }"
                class="flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-primary text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark active:scale-[0.99]">
            <svg x-show="loading" x-cloak class="h-4 w-4 animate-spin shrink-0 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span x-show="!loading">Masuk ke Sistem</span>
            <span x-show="loading" x-cloak>Memproses Masuk...</span>
            <x-icon x-show="!loading" name="arrow" class="h-4 w-4" />
        </button>

        {{-- Divider Pemisah Socialite --}}
        <div class="relative my-4 flex items-center justify-center">
            <div class="w-full border-t border-line"></div>
            <span class="absolute bg-white px-3 text-[10px] font-bold uppercase tracking-wider text-muted">atau</span>
        </div>

        {{-- Tombol Google Socialite SSO --}}
        <a href="{{ route('auth.google') }}"
           class="flex h-11 w-full items-center justify-center gap-2.5 rounded-lg border border-line bg-white text-sm font-medium text-ink shadow-sm transition hover:bg-slate-50 active:scale-[0.99]">
            <svg class="h-4 w-4" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
            </svg>
            <span>Masuk dengan Akun Google</span>
        </a>
    </form>
</x-guest-layout>
