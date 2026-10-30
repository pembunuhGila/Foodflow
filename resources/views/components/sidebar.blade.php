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
        <button type="button" class="rounded-full p-1 text-muted hover:bg-primary/10 lg:hidden" @click="sidebarOpen = false" aria-label="Tutup menu">
            <x-icon name="close" />
        </button>
    </div>

    {{-- Menu --}}
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 pb-4">
        @foreach ($groups as $group)
        @php
        $items = collect($group['items'])->map(function ($item) use ($resolve, $isActive) {
        $item['resolved'] = $resolve($item['route']);
        $item['active'] = $isActive($item['resolved']);
        return $item;
        });
        $groupActive = $items->contains('active', true);
        $fixed = $group['title'] === 'Utama';
        @endphp

        <div x-data="{ open: {{ $groupActive || $fixed ? 'true' : 'false' }} }" class="pt-3">
            @if ($fixed)
            <div class="px-3 pb-1 text-label uppercase text-muted">{{ $group['title'] }}</div>
            @else
            <button type="button" @click="open = !open"
                class="flex w-full items-center justify-between rounded-full px-3 pb-1 text-label uppercase text-muted hover:text-ink">
                <span>{{ $group['title'] }}</span>
                <x-icon name="chevron" class="h-4 w-4 transition-transform" ::class="{ 'rotate-180': open }" />
            </button>
            @endif

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
        @endforeach
    </nav>

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