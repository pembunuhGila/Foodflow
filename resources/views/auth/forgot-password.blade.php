<x-guest-layout>
    <x-slot name="back">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-muted hover:text-primary">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Kembali ke Login
        </a>
    </x-slot>

    @php $hasError = $errors->has('email'); @endphp

    {{-- Ikon + judul (berubah saat error) --}}
    <div class="flex h-11 w-11 items-center justify-center rounded-input {{ $hasError ? 'bg-danger-bg text-danger-text' : 'bg-info-bg text-primary' }}">
        <x-icon :name="$hasError ? 'alert-circle' : 'lock'" class="h-5 w-5" />
    </div>

    <h1 class="mt-5 font-display text-h2 text-ink">{{ $hasError ? 'Email Tidak Ditemukan' : 'Lupa Password?' }}</h1>
    <p class="mt-1 text-body text-muted">
        {{ $hasError ? 'Email belum terdaftar, gunakan Email yang terdaftar.' : 'Masukkan email terdaftar. Permintaan akan diteruskan ke Admin.' }}
    </p>

    @if (session('status'))
    <div class="mt-4 rounded-input border border-success-text/20 bg-success-bg p-3 text-small text-success-text" role="status">
        {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-5 space-y-4">
        @csrf

        <div>
            <label for="email" class="mb-1.5 block text-label uppercase text-muted">Email Akun Pegawai</label>
            <div class="relative">
                <x-icon name="mail" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" />
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                    required autofocus placeholder="nama@foodflow.test"
                    class="h-11 w-full rounded-input border bg-white pl-10 pr-10 text-body text-ink placeholder:text-muted/60 focus:ring-2
                              {{ $hasError ? 'border-danger-text focus:border-danger-text focus:ring-danger-text/20' : 'border-line focus:border-primary focus:ring-primary/20' }}">
                @if ($hasError)
                <x-icon name="alert-circle" class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-danger-text" />
                @endif
            </div>

            @if ($hasError)
            <p class="mt-1.5 flex items-center gap-1.5 text-small text-danger-text">
                <x-icon name="alert-circle" class="h-3.5 w-3.5" />
                {{ $errors->first('email') }}
            </p>
            @else
            <p class="mt-2 flex items-start gap-1.5 text-small text-muted">
                <x-icon name="info" class="mt-0.5 h-3.5 w-3.5 shrink-0" />
                Pastikan email terdaftar pada departemen Dapur, Gudang, atau Manajerial.
            </p>
            @endif
        </div>

        <button type="submit"
            class="flex h-11 w-full items-center justify-center gap-2 rounded-full bg-primary text-body font-medium text-white shadow-card transition hover:bg-primary-dark active:scale-[0.98]">
            Kirim Permintaan
            <x-icon name="send" class="h-4 w-4" />
        </button>

        {{-- Alternatif Masuk Cepat Google jika lupa password --}}
        <div class="relative my-4 flex items-center justify-center">
            <div class="w-full border-t border-line"></div>
            <span class="absolute bg-white px-3 text-tiny uppercase tracking-wider text-muted">atau masuk instan</span>
        </div>

        <a href="{{ route('auth.google') }}"
           class="flex h-11 w-full items-center justify-center gap-2.5 rounded-full border border-line bg-white text-body font-medium text-ink shadow-sm transition hover:bg-surface active:scale-[0.98]">
            <svg class="h-4 w-4" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
            </svg>
            Masuk dengan Akun Google
        </a>
    </form>
</x-guest-layout>