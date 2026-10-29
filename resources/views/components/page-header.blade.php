@props(['eyebrow' => null, 'title', 'subtitle' => null])
<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
    <div class="min-w-0">
        @if ($eyebrow)
            <div class="text-label uppercase text-primary">{{ $eyebrow }}</div>
        @endif
        <h1 class="text-h1 text-ink">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1 max-w-2xl text-body text-muted">{{ $subtitle }}</p>
        @endif
    </div>
    @if (isset($actions) && $actions->isNotEmpty())
        <div class="flex items-center gap-3">{{ $actions }}</div>
    @endif
</div>