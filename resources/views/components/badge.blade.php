@props(['variant' => 'info'])
@php
    $styles = [
        'success' => 'bg-primary/10 text-primary',
        'warning' => 'bg-primary/10 text-primary',
        'info'    => 'bg-primary/10 text-primary',
        'danger'  => 'bg-danger-bg text-danger-text',
        'neutral' => 'bg-white text-muted border border-line',
    ][$variant] ?? 'bg-primary/10 text-primary';
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-3 py-1 text-label uppercase $styles"]) }}>
    {{ $slot }}
</span>
