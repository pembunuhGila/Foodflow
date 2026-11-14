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
    class="fixed inset-y-0 left-0 z-40 flex w-[260px] -translate-x-full flex-col border-r border-line bg-white/60 backdrop-blur-md transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:shrink-0 lg:self-start lg:translate-x-0"
    :class="{ '!translate-x-0': sidebarOpen }"
    aria-label="Navigasi utama">

    {{-- 1. Logo --}}
    <div class="flex shrink-0 items-center justify-between px-5 pb-3 pt-6">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <x-brand-logo class="h-10 w-14" />
            <div class="leading-tight">
                <div class="font-display text-h3 text-ink">FoodFlow</div>
                <div class="text-small text-muted">Versi 0.1</div>
            </div>
        </a>
        <button type="button" class="rounded-full p-1 text-muted hover:bg-primary/10 lg:hidden" @click="sidebarOpen = false" aria-label="Tutup menu">
            <x-icon name="close" />
        </button>
    </div>

    {{-- 2. Menu (hanya bagian ini yang di-scroll) --}}
    <nav class="min-h-0 flex-1 overflow-y-auto px-3 pb-3">
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
        {{-- Grup dengan Accordion Dropdown (untuk sidebar role dengan isi >= 6) --}}
        <div x-data="{ open: {{ $groupActive ? 'true' : 'false' }} }" class="pt-4 first:pt-1">
            <button type="button" @click="open = !open" data-sidebar-toggle="true"
                class="flex w-full items-center justify-between rounded-full px-3 py-1.5 text-label uppercase text-muted transition hover:bg-primary/5 hover:text-ink focus:outline-none focus:ring-1 focus:ring-primary/20">
                <span class="font-bold tracking-wider">{{ $group['title'] }}</span>
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-primary/5 text-muted transition-transform" :class="{ 'rotate-180 bg-primary/10 text-primary': open }">
                    <x-icon name="chevron" class="h-3.5 w-3.5" />
                </span>
            </button>

            <ul x-show="open" x-cloak class="space-y-0.5">
                @foreach ($items as $item)
                <li>
                    @if ($item['resolved'])
                    <a href="{{ route($item['resolved']) }}"
                        @if ($item['active']) aria-current="page" @endif
                        class="flex items-center gap-3 rounded-full px-3 py-2.5 text-body transition
                                          {{ $item['active']
                                              ? 'bg-primary font-medium text-white shadow-card'
                                              : 'text-ink hover:bg-primary/10' }}">
                        <x-icon :name="$item['icon']" />
                        <span class="truncate">{{ $item['label'] }}</span>
                    </a>
                    @else
                    <div class="flex cursor-not-allowed items-center gap-3 rounded-full px-3 py-2.5 text-body text-muted/70"
                        title="Fitur ini belum tersedia">
                        <x-icon :name="$item['icon']" />
                        <span class="truncate">{{ $item['label'] }}</span>
                        <span class="ml-auto rounded-full bg-primary/10 px-2 py-0.5 text-label normal-case tracking-normal text-primary">Segera</span>
                    </div>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
        @else
        {{-- Grup Flat / Non-Dropdown (jika isi sidebar role kurang dari 6) --}}
        <div class="pt-4 first:pt-1">
            <div class="px-3 pb-1.5 text-label uppercase text-muted">{{ $group['title'] }}</div>

            <ul class="space-y-0.5">
                @foreach ($items as $item)
                <li>
                    @if ($item['resolved'])
                    <a href="{{ route($item['resolved']) }}"
                        @if ($item['active']) aria-current="page" @endif
                        class="flex items-center gap-3 rounded-full px-3 py-2.5 text-body transition
                                          {{ $item['active']
                                              ? 'bg-primary font-medium text-white shadow-card'
                                              : 'text-ink hover:bg-primary/10' }}">
                        <x-icon :name="$item['icon']" />
                        <span class="truncate">{{ $item['label'] }}</span>
                    </a>
                    @else
                    <div class="flex cursor-not-allowed items-center gap-3 rounded-full px-3 py-2.5 text-body text-muted/70"
                        title="Fitur ini belum tersedia">
                        <x-icon :name="$item['icon']" />
                        <span class="truncate">{{ $item['label'] }}</span>
                        <span class="ml-auto rounded-full bg-primary/10 px-2 py-0.5 text-label normal-case tracking-normal text-primary">Segera</span>
                    </div>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
        @endif
        @endforeach
    </nav>

    {{-- 3. Bawah: selalu terlihat, tidak ikut di-scroll --}}
    <div class="shrink-0 space-y-0.5 border-t border-line px-3 py-3">
        <a href="{{ url('/') }}" target="_blank" rel="noopener"
            class="flex items-center gap-3 rounded-full px-3 py-2 text-body text-ink hover:bg-primary/10">
            <x-icon name="external" />
            <span>Lihat Website</span>
        </a>

        @if (\Illuminate\Support\Facades\Route::has('profile.edit'))
        <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-3 rounded-full px-3 py-2 text-body text-ink hover:bg-primary/10">
            <x-icon name="key" />
            <span>Ganti Password</span>
        </a>
        @endif

        <button type="button" @click="sidebarOpen = false; $dispatch('open-logout')"
            class="flex w-full items-center gap-3 rounded-full bg-transparent px-3 py-2 text-left font-sans text-body text-danger-text hover:bg-danger-bg">
            <x-icon name="logout" />
            <span>Keluar</span>
        </button>
    </div>
</aside>
