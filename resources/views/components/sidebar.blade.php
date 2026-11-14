@php
$user = auth()->user();
$role = $user->role instanceof \BackedEnum ? $user->role->value : (string) $user->role;
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
    class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-line bg-white transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:shrink-0 lg:self-start lg:translate-x-0 shadow-[1px_0_2px_rgba(15,23,42,0.02)]"
    :class="{ '!translate-x-0': sidebarOpen }"
    aria-label="Navigasi utama">

    {{-- 1. Logo & Branding Katering --}}
    <div class="flex shrink-0 items-center justify-between border-b border-line/60 px-5 py-4">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary transition group-hover:bg-primary group-hover:text-white">
                <x-brand-logo class="h-6 w-8" />
            </div>
            <div class="leading-tight">
                <div class="font-display text-base font-bold text-ink tracking-tight flex items-center gap-1.5">
                    FoodFlow
                    <span class="rounded bg-primary/10 px-1.5 py-0.5 text-[10px] font-bold text-primary tracking-normal uppercase">Katering</span>
                </div>
                <div class="text-xs text-muted">Operasional &amp; Dapur</div>
            </div>
        </a>
        <button type="button" class="rounded-lg p-1.5 text-muted hover:bg-slate-100 hover:text-ink lg:hidden" @click="sidebarOpen = false" aria-label="Tutup menu">
            <x-icon name="close" class="h-5 w-5" />
        </button>
    </div>

    {{-- 2. Menu Navigasi --}}
    <nav class="min-h-0 flex-1 overflow-y-auto px-3 py-3">
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
                class="flex w-full items-center justify-between rounded-lg px-2.5 py-1.5 text-label uppercase text-muted transition hover:bg-slate-100 hover:text-ink focus:outline-none focus:ring-1 focus:ring-primary/20">
                <span class="font-bold tracking-wider text-[11px]">{{ $group['title'] }}</span>
                <span class="flex h-5 w-5 items-center justify-center rounded text-muted transition-transform" :class="{ 'rotate-180 text-primary': open }">
                    <x-icon name="chevron" class="h-3.5 w-3.5" />
                </span>
            </button>

            <ul x-show="open" x-cloak class="mt-1 space-y-1">
                @foreach ($items as $item)
                <li>
                    @if ($item['resolved'])
                    <a href="{{ route($item['resolved']) }}"
                        @if ($item['active']) aria-current="page" @endif
                        class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition
                                          {{ $item['active']
                                              ? 'bg-primary font-semibold text-white shadow-sm'
                                              : 'text-slate-600 hover:bg-slate-100 hover:text-ink' }}">
                        <x-icon :name="$item['icon']" class="h-4 w-4 shrink-0" />
                        <span class="truncate">{{ $item['label'] }}</span>
                    </a>
                    @else
                    <div class="flex cursor-not-allowed items-center gap-3 rounded-lg px-3 py-2 text-sm text-muted/70 bg-slate-50 border border-dashed border-line"
                        title="Fitur ini belum tersedia">
                        <x-icon :name="$item['icon']" class="h-4 w-4 shrink-0" />
                        <span class="truncate">{{ $item['label'] }}</span>
                        <span class="ml-auto rounded bg-primary/10 px-1.5 py-0.5 text-[10px] font-semibold text-primary">Segera</span>
                    </div>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
        @else
        {{-- Grup Flat / Non-Dropdown --}}
        <div class="pt-3 first:pt-0.5">
            <div class="px-2.5 pb-1 text-label uppercase text-muted tracking-wider text-[11px]">{{ $group['title'] }}</div>

            <ul class="space-y-1">
                @foreach ($items as $item)
                <li>
                    @if ($item['resolved'])
                    <a href="{{ route($item['resolved']) }}"
                        @if ($item['active']) aria-current="page" @endif
                        class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition
                                          {{ $item['active']
                                              ? 'bg-primary font-semibold text-white shadow-sm'
                                              : 'text-slate-600 hover:bg-slate-100 hover:text-ink' }}">
                        <x-icon :name="$item['icon']" class="h-4 w-4 shrink-0" />
                        <span class="truncate">{{ $item['label'] }}</span>
                    </a>
                    @else
                    <div class="flex cursor-not-allowed items-center gap-3 rounded-lg px-3 py-2 text-sm text-muted/70 bg-slate-50 border border-dashed border-line"
                        title="Fitur ini belum tersedia">
                        <x-icon :name="$item['icon']" class="h-4 w-4 shrink-0" />
                        <span class="truncate">{{ $item['label'] }}</span>
                        <span class="ml-auto rounded bg-primary/10 px-1.5 py-0.5 text-[10px] font-semibold text-primary">Segera</span>
                    </div>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
        @endif
        @endforeach
    </nav>

    {{-- 3. Bawah: Pengaturan & Logout --}}
    <div class="shrink-0 space-y-1 border-t border-line bg-slate-50/70 p-3">
        <a href="{{ url('/') }}" target="_blank" rel="noopener"
            class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-white hover:text-ink transition border border-transparent hover:border-line">
            <x-icon name="external" class="h-4 w-4 text-muted" />
            <span>Lihat Website</span>
        </a>

        @if (\Illuminate\Support\Facades\Route::has('profile.edit'))
        <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-white hover:text-ink transition border border-transparent hover:border-line">
            <x-icon name="key" class="h-4 w-4 text-muted" />
            <span>Ganti Password</span>
        </a>
        @endif

        <button type="button" @click="sidebarOpen = false; $dispatch('open-logout')"
            class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-medium text-rose-600 hover:bg-rose-50 transition">
            <x-icon name="logout" class="h-4 w-4 text-rose-500" />
            <span>Keluar Sistem</span>
        </button>
    </div>
</aside>