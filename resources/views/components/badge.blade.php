@props(['variant' => 'info'])
@php
    $styles = [
        'success' => 'bg-success-bg text-success-text border border-success-text/20',
        'warning' => 'bg-warning-bg text-warning-text border border-warning-text/20',
        'info'    => 'bg-info-bg text-info-text border border-info-text/20',
        'danger'  => 'bg-danger-bg text-danger-text border border-danger-text/20',
        'neutral' => 'bg-white text-muted border border-line',
    ][$variant] ?? 'bg-info-bg text-info-text border border-info-text/20';
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-label uppercase $styles"]) }}>
    {{ $slot }}
</span>
