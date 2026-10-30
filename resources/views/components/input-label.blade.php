@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-label uppercase text-muted']) }}>
    {{ $value ?? $slot }}
</label>