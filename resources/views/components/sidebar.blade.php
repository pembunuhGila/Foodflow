@php
$user = auth()->user();
$role = $user->role instanceof \BackedEnum ? $user->role->value : (string) $user->role;
$groups = config('menu.' . $role, []);

$resolve = function ($candidates) {
foreach ((array) $candidates as $name) {
if (\Illuminate\Support\Facades\Route::has($name)) {
return $name;
}
}
return null;
};

$isActive = function (?string $name) {
if (! $name) {
return false;
}
if (! str_contains($name, '.')) {
return request()->routeIs($name);
}
return request()->routeIs(\Illuminate\Support\Str::beforeLast($name, '.') . '.*');
};
@endphp

<aside
    class="fixed inset-y-0 left-0 z-40 flex w-[260px] -translate-x-full flex-col border-r border-line bg-white/60 backdrop-blur-md transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:shrink-0 lg:self-start lg:translate-x-0"
    :class="{ '!translate-x-0': sidebarOpen }"
    aria-label="Navigasi utama">

    {{-- Logo --}}
    <div class="flex items-center justify-between px-5 pb-4 pt-6">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <x-brand-logo class="h-10 w-14" />
            <div class="leading-tight">
                <div class="font-display text-h3 text-ink">FoodFlow</div>
                <div class="text-small text-muted">Versi 0.1</div>
            </div>
        </a>
    </div>

    {{-- Navigation Menu List --}}
    <div class="flex-1 overflow-y-auto px-4 py-2 space-y-6">
        {{-- UTAMA --}}
        <div>
            <h3 class="text-xs font-semibold text-gray-400 tracking-wider uppercase mb-2 px-3">UTAMA</h3>
            <ul class="space-y-1">
                <li>
                    <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard') || request()->routeIs('*.dashboard')">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </x-sidebar-link>
                </li>
            </ul>
        </div>

        {{-- DAPUR / PRODUKSI --}}
        <div>
            <h3 class="text-xs font-semibold text-gray-400 tracking-wider uppercase mb-2 px-3">DAPUR / PRODUKSI</h3>
            <ul class="space-y-1">
                <li>
                    <x-sidebar-link href="{{ route('products.index') }}" :active="request()->routeIs('products.*')">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span>Menu Makanan</span>
                    </x-sidebar-link>
                </li>
                <li>
                    <x-sidebar-link href="{{ route('production-plans.index') }}" :active="request()->routeIs('production-plans.*')">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span>Rencana Produksi</span>
                    </x-sidebar-link>
                </li>
                <li>
                    <x-sidebar-link href="{{ route('productions.index') }}" :active="request()->routeIs('productions.*')">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343a8 8 0 010 11.314z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                        </svg>
                        <span>Realisasi Masak</span>
                    </x-sidebar-link>
                </li>
            </ul>
        </div>

        {{-- PERSEDIAAN & BAHAN --}}
        <div>
            <h3 class="text-xs font-semibold text-gray-400 tracking-wider uppercase mb-2 px-3">PERSEDIAAN & BAHAN</h3>
            <ul class="space-y-1">
                <li>
                    <x-sidebar-link href="{{ route('ingredients.index') }}" :active="request()->routeIs('ingredients.*')">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span>Bahan Baku</span>
                    </x-sidebar-link>
                </li>
                <li>
                    <x-sidebar-link href="{{ route('material-requests.index') }}" :active="request()->routeIs('material-requests.*')">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Permintaan Bahan</span>
                    </x-sidebar-link>
                </li>
            </ul>
        </div>

    {{-- Bawah: ganti password + keluar --}}
    <div class="shrink-0 space-y-0.5 border-t border-line px-3 py-4">
        <a href="{{ url('/') }}" target="_blank" rel="noopener"
            class="flex items-center gap-3 rounded-full px-3 py-2.5 text-body text-ink hover:bg-primary/10">
            <x-icon name="external" />
            <span>Lihat Website</span>
        </a>

        @if (\Illuminate\Support\Facades\Route::has('profile.edit'))
        <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-3 rounded-full px-3 py-2.5 text-body text-ink hover:bg-primary/10">
            <x-icon name="key" />
            <span>Ganti Password</span>
        </a>
        @endif

        <button type="button" @click="sidebarOpen = false; $dispatch('open-logout')"
            class="flex w-full items-center gap-3 rounded-full bg-transparent px-3 py-2.5 text-left font-sans text-body text-danger-text hover:bg-danger-bg">
            <x-icon name="logout" />
            <span>Keluar</span>
        </button>
    </div>
</aside>
