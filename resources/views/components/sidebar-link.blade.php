@props([
    'href',
    'active' => false,
])

@php
    $baseClasses = 'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition duration-150 ease-in-out';
    $activeClasses = 'bg-purple-600 text-white shadow-md shadow-purple-200';
    $inactiveClasses = 'text-gray-600 hover:text-purple-700 hover:bg-purple-100/60';
    $classes = $baseClasses . ' ' . ($active ? $activeClasses : $inactiveClasses);
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
