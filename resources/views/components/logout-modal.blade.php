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
             class="relative w-full max-w-md rounded-2xl border border-line bg-white p-6 sm:p-7 text-center shadow-dropdown">
            <button type="button" @click="open = false" aria-label="Tutup"
                    class="absolute right-3.5 top-3.5 flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">
                <x-icon name="close" class="h-4 w-4" />
            </button>

            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-rose-50 border border-rose-100 text-rose-600">
                <x-icon name="logout" class="h-6 w-6" />
            </div>

            <h2 id="logout-title" class="mt-4 font-display text-xl font-bold text-ink">Keluar dari Sistem?</h2>
            <p class="mt-1.5 text-xs text-muted leading-relaxed">
                Sesi akses Anda akan diakhiri. Pastikan semua pencatatan operasional katering hari ini telah disimpan dengan baik.
            </p>

            <div class="mt-4 flex items-center gap-3 rounded-lg border border-line bg-slate-50 p-3 text-left">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-600 font-display text-xs font-bold text-white shadow-sm">
                    {{ $initials }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <span class="truncate text-xs font-bold text-ink">{{ $user->name }}</span>
                        <x-badge variant="info" class="!px-1.5 !py-0.2 text-[10px]">{{ $roleLabel }}</x-badge>
                    </div>
                    <div class="truncate text-xs text-muted mt-0.5">{{ $user->email }}</div>
                </div>
            </div>

            <div class="mt-5 grid grid-cols-2 gap-2.5">
                <button type="button" x-ref="cancel" @click="open = false"
                        class="rounded-lg border border-line bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="button" @click="logout()" :disabled="loading"
                        class="flex items-center justify-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-rose-700 transition disabled:opacity-60">
                    <x-icon name="power" class="h-3.5 w-3.5" />
                    <span x-text="loading ? 'Memproses...' : 'Ya, Keluar'"></span>
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