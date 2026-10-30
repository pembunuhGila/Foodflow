@php
    $user = auth()->user();
    $roleValue = $user->role instanceof \BackedEnum ? $user->role->value : (string) $user->role;
    $roleLabel = ['admin' => 'Admin', 'gudang' => 'Staf Gudang', 'produksi' => 'Staf Produksi', 'owner' => 'Owner'][$roleValue] ?? ucfirst($roleValue);
    $initials = collect(preg_split('/\s+/', trim($user->name)))->take(2)
        ->map(fn ($w) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($w, 0, 1)))->join('');
@endphp

<div x-data="{
        open: false,
        loading: false,
        error: false,
        async logout() {
            this.loading = true;
            this.error = false;
            try {
                const res = await fetch('{{ route('logout') }}', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'X-Requested-With': 'XMLHttpRequest' },
                });
                // 419 = sesi sudah berakhir, artinya memang sudah keluar
                if (res.ok || res.status === 419) { window.location.href = '{{ url('/') }}'; return; }
                throw new Error('logout gagal');
            } catch (e) {
                this.open = false;
                this.error = true;
                this.loading = false;
            }
        }
     }"
     @open-logout.window="open = true; error = false; $nextTick(() => $refs.cancel.focus())"
     @keydown.escape.window="open = false">

    {{-- Popup konfirmasi --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         role="dialog" aria-modal="true" aria-labelledby="logout-title">
        <div class="absolute inset-0 bg-ink/40 backdrop-blur-sm" @click="open = false"></div>

        <div x-show="open" x-transition.opacity.duration.150ms
             class="relative w-full max-w-md rounded-3xl bg-white p-8 text-center shadow-card">
            <button type="button" @click="open = false" aria-label="Tutup"
                    class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-primary/10 text-ink hover:bg-primary/20">
                <x-icon name="close" class="h-4 w-4" />
            </button>

            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-info-bg text-primary">
                <x-icon name="logout" class="h-7 w-7" />
            </div>

            <h2 id="logout-title" class="mt-5 font-display text-h2 text-ink">Keluar dari sistem?</h2>
            <p class="mt-2 text-body text-muted">
                Sesi Anda akan diakhiri. Pastikan semua pencatatan hari ini sudah disimpan.
            </p>

            <div class="mt-5 flex items-center gap-3 rounded-input bg-primary/5 p-3 text-left">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary font-display text-body font-semibold text-white">
                    {{ $initials }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="truncate text-body font-semibold text-ink">{{ $user->name }}</span>
                        <x-badge variant="info" class="!px-2 !py-0.5 normal-case tracking-normal">{{ $roleLabel }}</x-badge>
                    </div>
                    <div class="truncate text-small text-muted">{{ $user->email }}</div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-3">
                <button type="button" x-ref="cancel" @click="open = false"
                        class="h-11 rounded-full bg-primary/10 text-body font-medium text-ink hover:bg-primary/20 focus:outline-none focus:ring-2 focus:ring-primary/30">
                    Batal
                </button>
                <button type="button" @click="logout()" :disabled="loading"
                        class="flex h-11 items-center justify-center gap-2 rounded-full bg-primary text-body font-medium text-white shadow-card hover:bg-primary-dark disabled:opacity-60">
                    <x-icon name="power" class="h-4 w-4" />
                    <span x-text="loading ? 'Memproses...' : 'Ya, Logout'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Notifikasi gagal --}}
    <div x-show="error" x-cloak x-transition
         class="fixed left-1/2 top-6 z-50 flex w-[calc(100%-2rem)] max-w-lg -translate-x-1/2 items-center gap-3 rounded-full bg-white px-4 py-3 shadow-card ring-1 ring-line"
         role="alert">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-danger-bg text-danger-text">
            <x-icon name="alert" class="h-4 w-4" />
        </span>
        <span class="flex-1 text-body font-medium text-danger-text">Gagal mengakhiri sesi. Coba lagi.</span>
        <button type="button" @click="open = true; error = false; logout()"
                class="text-body font-medium text-primary underline hover:text-primary-dark">Coba Lagi</button>
        <button type="button" @click="error = false" aria-label="Tutup" class="text-muted hover:text-ink">
            <x-icon name="close" class="h-4 w-4" />
        </button>
    </div>
</div>