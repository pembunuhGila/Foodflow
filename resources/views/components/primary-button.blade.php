@props(['padding' => true])
<div {{ $attributes->merge(['class' => 'rounded-card border border-line bg-white shadow-card' . ($padding ? ' p-6' : '')]) }}>
    {{ $slot }}
</div>