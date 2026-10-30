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
    </form>
</x-guest-layout>