@php
$user = auth()->user();
$role = $user->role instanceof \BackedEnum ? $user->role->value : (string) $user->role;
$roleLabel = ['admin' => 'Admin', 'gudang' => 'Staf Gudang', 'produksi' => 'Staf Produksi', 'owner' => 'Owner'][$role] ?? ucfirst($role);
$groups = config('menu.' . $role, []);

// Hitung total item menu navigasi pada sidebar role ini
$totalMenuItems = collect($groups)->sum(fn ($g) => count($g['items'] ?? []));

// Logika: jika isi menu pada sidebar role 6 ke bawah (5-6 menu termasuk dashboard), tampilkan flat tanpa dropdown.
// Hanya role dengan menu lebih dari 6 (seperti Admin dengan 13 menu) yang menggunakan accordion dropdown.
$isDropdownRole = $totalMenuItems > 6;

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
    class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-slate-800 bg-[#0F172A] text-slate-200 transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:shrink-0 lg:self-start lg:translate-x-0 shadow-2xl"
    :class="{ '!translate-x-0': sidebarOpen }"
    aria-label="Navigasi utama">

    {{-- 1. Logo & Branding Katering --}}
    <div class="flex shrink-0 items-center justify-between border-b border-slate-800/80 px-5 py-4 bg-[#0B132B]/50">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-600/20 border border-blue-500/30 text-blue-400 transition group-hover:bg-blue-600 group-hover:text-white shadow-sm">
                <x-brand-logo class="h-6 w-8" />
            </div>
            <div class="leading-tight">
                <div class="font-display text-base font-bold text-white tracking-tight flex items-center gap-1.5">
                    FoodFlow
                    <span class="rounded bg-blue-500/20 border border-blue-400/30 px-1.5 py-0.2 text-[10px] font-bold text-blue-300 tracking-normal uppercase">Katering</span>
                </div>
                <div class="text-xs text-slate-400">Operasional &amp; Dapur</div>
            </div>
        </a>
        <button type="button" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-800 hover:text-white lg:hidden" @click="sidebarOpen = false" aria-label="Tutup menu">
            <x-icon name="close" class="h-5 w-5" />
        </button>
    </div>

    {{-- 2. Menu Navigasi --}}
    <nav class="min-h-0 flex-1 overflow-y-auto px-3 py-3 space-y-1">
        @foreach ($groups as $group)
        @php
        $items = collect($group['items'])->map(function ($item) use ($resolve, $isActive) {
            $item['resolved'] = $resolve($item['route']);
            $item['active'] = $isActive($item['resolved']);
            return $item;
        });
        $groupActive = $items->contains('active', true);
        
        // Hanya buat dropdown jika role memiliki menu >= 6 dan grup bukan 'Utama'
        $isDropdown = $isDropdownRole && ($group['title'] !== 'Utama');
        @endphp

        @if ($isDropdown)
        {{-- Grup dengan Accordion Dropdown --}}
        <div x-data="{ open: {{ $groupActive ? 'true' : 'false' }} }" class="pt-3 first:pt-0.5">
            <button type="button" @click="open = !open" data-sidebar-toggle="true"
                class="flex w-full items-center justify-between rounded-lg px-2.5 py-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-400 transition hover:bg-slate-800/60 hover:text-slate-200 focus:outline-none">
                <span>{{ $group['title'] }}</span>
                <span class="flex h-5 w-5 items-center justify-center rounded text-slate-500 transition-transform" :class="{ 'rotate-180 text-blue-400': open }">
                    <x-icon name="chevron" class="h-3.5 w-3.5" />
                </span>
            </button>

            <ul x-show="open" x-cloak class="mt-1 space-y-0.5">
                @foreach ($items as $item)
                <li>
                    @if ($item['resolved'])
                    <a href="{{ route($item['resolved']) }}"
                        @if ($item['active']) aria-current="page" @endif
                        class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition
                                          {{ $item['active']
                                              ? 'bg-blue-600 font-semibold text-white shadow-md shadow-blue-950/60 ring-1 ring-blue-400/30'
                                              : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <x-icon :name="$item['icon']" class="h-4 w-4 shrink-0 {{ $item['active'] ? 'text-white' : 'text-slate-400' }}" />
                        <span class="truncate">{{ $item['label'] }}</span>
                    </a>
                    @else
                    <div class="flex cursor-not-allowed items-center gap-3 rounded-lg px-3 py-2 text-sm text-slate-500 bg-slate-900/60 border border-slate-800/60"
                        title="Fitur ini belum tersedia">
                        <x-icon :name="$item['icon']" class="h-4 w-4 shrink-0 text-slate-600" />
                        <span class="truncate">{{ $item['label'] }}</span>
                        <span class="ml-auto rounded bg-slate-800 px-1.5 py-0.5 text-[10px] font-semibold text-slate-400">Segera</span>
                    </div>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
        @else
        {{-- Grup Flat / Non-Dropdown --}}
        <div class="pt-3 first:pt-0.5">
            <div class="px-2.5 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ $group['title'] }}</div>

            <ul class="space-y-0.5">
                @foreach ($items as $item)
                <li>
                    @if ($item['resolved'])
                    <a href="{{ route($item['resolved']) }}"
                        @if ($item['active']) aria-current="page" @endif
                        class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition
                                          {{ $item['active']
                                              ? 'bg-blue-600 font-semibold text-white shadow-md shadow-blue-950/60 ring-1 ring-blue-400/30'
                                              : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <x-icon :name="$item['icon']" class="h-4 w-4 shrink-0 {{ $item['active'] ? 'text-white' : 'text-slate-400' }}" />
                        <span class="truncate">{{ $item['label'] }}</span>
                    </a>
                    @else
                    <div class="flex cursor-not-allowed items-center gap-3 rounded-lg px-3 py-2 text-sm text-slate-500 bg-slate-900/60 border border-slate-800/60"
                        title="Fitur ini belum tersedia">
                        <x-icon :name="$item['icon']" class="h-4 w-4 shrink-0 text-slate-600" />
                        <span class="truncate">{{ $item['label'] }}</span>
                        <span class="ml-auto rounded bg-slate-800 px-1.5 py-0.5 text-[10px] font-semibold text-slate-400">Segera</span>
                    </div>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
        @endif
        @endforeach
    </nav>

    {{-- 3. Bawah: Kartu Profil Pengguna Terintegrasi --}}
    <div class="shrink-0 border-t border-slate-800 bg-[#0B132B]/80 p-3">
        <div class="flex items-center justify-between gap-2 rounded-xl bg-slate-900/90 border border-slate-800/90 p-2.5 shadow-inner">
            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-600 font-display text-xs font-bold text-white shadow-sm ring-1 ring-blue-400/40">
                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1 leading-tight">
                    <div class="text-xs font-bold text-white truncate" title="{{ $user->name }}">{{ $user->name }}</div>
                    <div class="text-[10px] text-slate-400 truncate flex items-center gap-1 mt-0.5">
                        <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        <span class="truncate">{{ $roleLabel }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-0.5 shrink-0">
                @if (\Illuminate\Support\Facades\Route::has('profile.edit'))
                <a href="{{ route('profile.edit') }}" title="Pengaturan Akun &amp; Profil"
                   class="flex h-7 w-7 items-center justify-center rounded-md text-slate-400 hover:text-white hover:bg-slate-800 transition">
                    <x-icon name="settings" class="h-3.5 w-3.5" />
                </a>
                @endif

                <button type="button" @click="sidebarOpen = false; $dispatch('open-logout')" title="Keluar Sistem"
                        class="flex h-7 w-7 items-center justify-center rounded-md text-rose-400 hover:text-rose-300 hover:bg-rose-950/40 transition">
                    <x-icon name="logout" class="h-3.5 w-3.5" />
                </button>
            </div>
        </div>
    </div>
</aside>