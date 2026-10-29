<x-guest-layout>
    <x-slot name="intro">
        <h1 class="text-h1 text-ink">Selamat datang!</h1>
        <p class="mt-1 text-body text-muted">Masukkan kredensial Anda untuk mengakses dashboard dapur produksi.</p>
    </x-slot>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($errors->any())
        <div class="mb-5 flex gap-3 rounded-input border border-danger-text/20 bg-danger-bg p-3 text-small text-danger-text" role="alert">
            <x-icon name="alert" class="mt-0.5 h-4 w-4 shrink-0" />
            <div>
                <div class="font-semibold">Gagal masuk</div>
                <div>{{ $errors->first() }}</div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" x-data="{ show: false }" class="space-y-4">
        @csrf

        {{-- Email (nama field "email" dipertahankan, dibaca LoginRequest) --}}
        <div>
            <label for="email" class="mb-1.5 block text-label uppercase text-muted">Email</label>
            <div class="relative">
                <x-icon name="mail" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" />
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       required autofocus autocomplete="username" placeholder="nama@foodflow.test"
                       class="h-11 w-full rounded-input border bg-white pl-10 pr-4 text-body text-ink placeholder:text-muted/60 focus:ring-2
                              {{ $errors->has('email') ? 'border-danger-text focus:border-danger-text focus:ring-danger-text/20' : 'border-line focus:border-primary focus:ring-primary/20' }}">
            </div>
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="mb-1.5 block text-label uppercase text-muted">Password</label>
            <div class="relative">
                <x-icon name="key" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" />
                <input id="password" name="password" :type="show ? 'text' : 'password'"
                       required autocomplete="current-password" placeholder="••••••••"
                       class="h-11 w-full rounded-input border border-line bg-white pl-10 pr-11 text-body text-ink placeholder:text-muted/60 focus:border-primary focus:ring-2 focus:ring-primary/20">
                <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-primary"
                        :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
                    <x-icon name="eye" class="h-4 w-4" x-show="!show" />
                    <x-icon name="eye-off" class="h-4 w-4" x-show="show" x-cloak />
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 text-small text-muted">
                <input id="remember_me" type="checkbox" name="remember"
                       class="h-4 w-4 rounded border-muted/50 text-primary focus:ring-primary/20">
                Ingat sesi saya
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-small font-medium text-primary hover:text-primary-dark">
                    Lupa Password?
                </a>
            @endif
        </div>

        <button type="submit"
                class="flex h-11 w-full items-center justify-center gap-2 rounded-full bg-primary text-body font-medium text-white shadow-card transition hover:bg-primary-dark active:scale-[0.98]">
            Login
            <x-icon name="arrow" class="h-4 w-4" />
        </button>
    </form>
</x-guest-layout>