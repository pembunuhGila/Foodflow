@props(['variant' => 'info'])
@php
    $styles = [
        'success' => 'bg-success-bg text-success-text',
        'warning' => 'bg-warning-bg text-warning-text',
        'danger'  => 'bg-danger-bg text-danger-text',
        'info'    => 'bg-info-bg text-info-text',
        'neutral' => 'bg-gray-100 text-muted',
    ][$variant] ?? 'bg-info-bg text-info-text';
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-3 py-1 text-label uppercase $styles"]) }}>
    {{ $slot }}
</span>