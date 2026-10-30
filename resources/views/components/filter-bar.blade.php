@props(['action', 'search' => '', 'placeholder' => 'Cari...', 'param' => 'search', 'tabs' => [], 'hidden' => []])
<div class="rounded-card border border-line bg-white p-4 shadow-card">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center">

        {{-- Kolom pencarian (tekan Enter untuk mencari) --}}
        <form method="GET" action="{{ $action }}" role="search" class="relative flex-1">
            @foreach ($hidden as $hiddenName => $hiddenValue)
                @if (filled($hiddenValue))
                    <input type="hidden" name="{{ $hiddenName }}" value="{{ $hiddenValue }}">
                @endif
            @endforeach
            <x-icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-primary" />
            <input type="search" name="{{ $param }}" value="{{ $search }}" placeholder="{{ $placeholder }}"
                   class="h-12 w-full rounded-full border-0 bg-primary/5 pl-12 pr-4 text-body text-ink placeholder:text-muted focus:bg-white focus:ring-2 focus:ring-primary/30">
            <button type="submit" class="sr-only">Cari</button>
        </form>

        {{-- Tab status --}}
        @if (count($tabs))
            <nav class="flex shrink-0 items-center gap-1 rounded-full bg-primary/5 p-1" aria-label="Filter status">
                @foreach ($tabs as $tab)
                    <a href="{{ $tab['href'] }}" @if ($tab['active']) aria-current="page" @endif
                       class="whitespace-nowrap rounded-full px-5 py-2 text-body font-medium transition {{ $tab['active'] ? 'bg-primary text-white shadow-card' : 'text-ink hover:bg-primary/10' }}">
                        {{ $tab['label'] }}@if (isset($tab['count'])) ({{ $tab['count'] }})@endif
                    </a>
                @endforeach
            </nav>
        @endif
    </div>

    @if ($slot->isNotEmpty())
        <div class="mt-3">{{ $slot }}</div>
    @endif
</div>