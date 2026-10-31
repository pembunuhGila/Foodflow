@props(['class' => 'h-10 w-10'])
@php

    $dir = public_path('images');
    $names = is_dir($dir) ? array_map('basename', glob($dir . '/*') ?: []) : [];
    $file = null;
    foreach (['svg', 'png', 'webp', 'jpg', 'jpeg'] as $ext) {
        foreach ($names as $name) {
            if (strtolower($name) === 'logo.' . $ext) {
                $file = $name;
                break 2;
            }
        }
    }
@endphp
@if ($file)
    <img src="{{ asset('images/' . $file) }}" alt="FoodFlow" {{ $attributes->merge(['class' => $class . ' object-contain']) }}>
@else
    <span {{ $attributes->merge(['class' => $class . ' flex items-center justify-center rounded-input bg-primary font-display font-bold text-white']) }}>F</span>
@endif