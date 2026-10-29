@props(['label', 'value', 'unit' => null, 'icon' => 'package', 'tone' => 'primary', 'note' => null])
@php
    $iconTone = [
        'primary' => 'bg-info-bg text-primary',
        'warning' => 'bg-warning-bg text-warning-text',
        'success' => 'bg-success-bg text-success-text',
        'danger'  => 'bg-danger-bg text-danger-text',
    ][$tone] ?? 'bg-info-bg text-primary';
@endphp
<div class="rounded-card border border-line bg-white p-5 shadow-card">
    <div class="flex items-start justify-between gap-3">
        <div>
            <div class="text-label uppercase text-muted">{{ $label }}</div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="font-display text-h1 text-ink">{{ $value }}</span>
                @if ($unit)<span class="text-body text-muted">{{ $unit }}</span>@endif
            </div>
        </div>
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-input {{ $iconTone }}">
            <x-icon :name="$icon" class="h-6 w-6" />
        </div>
    </div>
    @if ($note)
        <div class="mt-3"><x-badge :variant="$tone === 'primary' ? 'info' : $tone">{{ $note }}</x-badge></div>
    @endif
</div>