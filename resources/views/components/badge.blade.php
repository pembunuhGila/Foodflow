@props(['variant' => 'info'])
@php
    $styles = [
        'success' => 'bg-success-bg text-success-text',
        'warning' => 'bg-warning-bg text-warning-text',
        'info'    => 'bg-info-bg text-primary',
        'danger'  => 'bg-danger-bg text-danger-text',
        'neutral' => 'bg-white text-muted border border-line',
    ][$variant] ?? 'bg-info-bg text-primary';
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-3 py-1 text-label uppercase $styles"]) }}>
    {{ $slot }}
</span>
