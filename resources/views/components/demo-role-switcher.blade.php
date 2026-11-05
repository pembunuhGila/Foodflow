@php
    $currentUser = auth()->user();
    $currentRole = $currentUser?->role instanceof \BackedEnum ? $currentUser->role->value : (string) $currentUser?->role;

    $profiles = [
        [
            'role' => 'admin',
            'name' => 'Admin FoodFlow',
            'email' => 'admin@foodflow.test',
            'title' => 'Administrator',
            'desc' => 'Kelola akun pegawai, master data, dan kontrol sistem',
            'badge' => 'bg-purple-100 text-purple-700 border-purple-200',
            'icon' => 'shield',
        ],
        [
            'role' => 'gudang',
            'name' => 'Staff Gudang',
            'email' => 'gudang@foodflow.test',
            'title' => 'Staf Gudang & Logistik',
            'desc' => 'Kelola stok bahan, PO supplier, dan approval bahan',
            'badge' => 'bg-blue-100 text-blue-700 border-blue-200',
            'icon' => 'package',
        ],
        [
            'role' => 'produksi',
            'name' => 'Staff Produksi',
            'email' => 'produksi@foodflow.test',
            'title' => 'Staf Dapur & Produksi',
            'desc' => 'Rencana masak harian, lembar resep, dan realisasi porsi',
            'badge' => 'bg-amber-100 text-amber-700 border-amber-200',
            'icon' => 'clipboard',
        ],
        [
            'role' => 'owner',
            'name' => 'Owner Manager',
            'email' => 'owner@foodflow.test',
            'title' => 'Owner / Direktur',
            'desc' => 'Dashboard eksekutif, monitoring gizi, dan cetak laporan resmi',
            'badge' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'icon' => 'bar',
        ],
    ];

    $currentProfile = collect($profiles)->firstWhere('role', $currentRole) ?? $profiles[0];
@endphp

{{-- 1. Topbar Quick Dropdown --}}
<div class="relative print:hidden" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
    <button type="button" @click="open = !open"
            title="Klik untuk beralih profil peran demo (Admin, Gudang, Produksi, Owner)"
            class="inline-flex items-center gap-2 rounded-full border border-line bg-white/90 px-3.5 py-1.5 text-small font-medium text-ink shadow-sm transition hover:border-primary hover:bg-primary/5 active:scale-95">
        <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="text-tiny font-bold uppercase tracking-wider text-primary">Demo Role:</span>
        <span class="font-semibold text-ink">{{ $currentProfile['title'] }}</span>
        <svg class="h-3.5 w-3.5 text-muted transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Dropdown Menu Pemilihan 4 Profil --}}
    <div x-show="open" x-cloak x-transition.origin.top.right
         class="absolute right-0 mt-2 w-80 rounded-card border border-line bg-white p-3 shadow-card z-50">
        <div class="mb-2 border-b border-line px-2 pb-2">
            <div class="flex items-center justify-between">
                <span class="text-tiny font-bold uppercase tracking-wider text-muted">Beralih Cepat Profil Demo</span>
                <span class="rounded bg-primary/10 px-1.5 py-0.5 text-tiny font-medium text-primary">Live Demo</span>
            </div>
            <p class="mt-0.5 text-tiny text-muted">Ganti peran tanpa perlu login &amp; logout berulang.</p>
        </div>

        <div class="space-y-1.5">
            @foreach ($profiles as $p)
                @php $isActive = $p['role'] === $currentRole; @endphp
                <a href="{{ route('demo.switch-role', ['role' => $p['role']]) }}"
                   class="group flex items-start gap-3 rounded-input p-2.5 transition {{ $isActive ? 'bg-primary/10 border border-primary/20' : 'hover:bg-canvas' }}">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $isActive ? 'bg-primary text-white' : 'bg-primary/10 text-primary' }}">
                        <x-icon :name="$p['icon']" class="h-4 w-4" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-1">
                            <span class="text-small font-semibold text-ink group-hover:text-primary">{{ $p['title'] }}</span>
                            @if ($isActive)
                                <span class="text-tiny font-bold text-primary">Aktif</span>
                            @endif
                        </div>
                        <p class="text-tiny text-muted line-clamp-1">{{ $p['desc'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- 2. Floating Quick Bar (Pojok Bawah Kanan Layar untuk Kenyamanan Presentasi) --}}
<div class="fixed bottom-5 right-5 z-40 flex items-center gap-1.5 rounded-full border border-line/80 bg-white/95 p-1.5 shadow-lg backdrop-blur-md transition-all hover:shadow-xl print:hidden"
     x-data="{ expanded: true }">
    <div class="flex items-center gap-1 pl-2 pr-1 text-tiny font-bold uppercase tracking-wider text-muted">
        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
        <span class="hidden sm:inline">Role:</span>
    </div>

    @foreach ($profiles as $p)
        @php $isActive = $p['role'] === $currentRole; @endphp
        <a href="{{ route('demo.switch-role', ['role' => $p['role']]) }}"
           title="Beralih ke {{ $p['title'] }} ({{ $p['email'] }})"
           class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-tiny font-semibold transition active:scale-95 {{ $isActive ? 'bg-primary text-white shadow-sm' : 'text-ink hover:bg-primary/10 hover:text-primary' }}">
            <x-icon :name="$p['icon']" class="h-3.5 w-3.5" />
            <span>{{ ucfirst($p['role']) }}</span>
        </a>
    @endforeach
</div>
