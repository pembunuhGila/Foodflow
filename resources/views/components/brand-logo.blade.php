@props(['class' => 'h-10 w-10'])
@php
    $file = collect(['logo.svg', 'logo.png', 'logo.webp', 'logo.jpg', 'logo.jpeg'])
        ->first(fn ($f) => file_exists(public_path('images/' . $f)));
@endphp
@if ($file)
    <img src="{{ asset('images/' . $file) }}" alt="FoodFlow" {{ $attributes->merge(['class' => $class . ' object-contain']) }}>
@else
    <span {{ $attributes->merge(['class' => $class . ' flex items-center justify-center rounded-input bg-primary font-display font-bold text-white']) }}>F</span>
@endif