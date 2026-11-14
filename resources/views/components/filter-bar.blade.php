@props(['action', 'search' => '', 'placeholder' => 'Cari...', 'param' => 'search', 'tabs' => [], 'hidden' => []])
<div class="rounded-card border border-line bg-white p-3.5 shadow-card">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center">

        {{-- Kolom pencarian (tekan Enter untuk mencari) --}}
        <form method="GET" action="{{ $action }}" role="search" class="relative flex-1">
            @foreach ($hidden as $hiddenName => $hiddenValue)
                @if (filled($hiddenValue))
                    <input type="hidden" name="{{ $hiddenName }}" value="{{ $hiddenValue }}">
                @endif
            @endforeach
            <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input type="search" name="{{ $param }}" value="{{ $search }}" placeholder="{{ $placeholder }}"
                   class="h-10 w-full rounded-lg border border-line bg-slate-50 pl-10 pr-4 text-sm text-ink placeholder:text-muted/70 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20">
            <button type="submit" class="sr-only">Cari</button>
        </form>

        {{-- Tab status --}}
        @if (count($tabs))
            <nav class="flex shrink-0 items-center gap-1 rounded-lg bg-slate-100 p-1 border border-line/60" aria-label="Filter status">
                @foreach ($tabs as $tab)
                    <a href="{{ $tab['href'] }}" @if ($tab['active']) aria-current="page" @endif
                       class="whitespace-nowrap rounded-md px-3.5 py-1.5 text-xs font-semibold transition {{ $tab['active'] ? 'bg-white text-primary shadow-sm' : 'text-slate-600 hover:text-ink' }}">
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