@props(['disabled' => false])
<select @disabled($disabled) {{ $attributes->merge(['class' => 'h-11 w-full rounded-input border-line bg-white text-body text-ink shadow-none focus:border-primary focus:ring-2 focus:ring-primary/20 disabled:bg-primary/5']) }}>
    {{ $slot }}
</select>