@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'type' => 'button'])
@php
    $variants = [
        'primary'   => 'bg-primary text-white shadow-card hover:bg-primary-dark',
        'secondary' => 'bg-primary/10 text-primary border border-primary/20 hover:bg-primary/20',
        'outline'   => 'bg-white text-ink border border-line hover:bg-primary/5',
        'danger'    => 'bg-danger-bg text-danger-text border border-danger-text/20 hover:bg-danger-text hover:text-white',
    ][$variant] ?? 'bg-primary text-white shadow-card hover:bg-primary-dark';

    $sizes = [
        'sm' => 'px-4 py-1.5 text-small',
        'md' => 'px-5 py-2.5 text-body',
    ][$size] ?? 'px-5 py-2.5 text-body';

    $classes = "inline-flex items-center justify-center gap-2 rounded-full font-medium transition focus:outline-none focus:ring-2 focus:ring-primary/30 active:scale-[0.98] disabled:opacity-60 $sizes $variants";
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif