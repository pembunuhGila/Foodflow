@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'type' => 'button', 'loadingText' => 'Memproses...'])
@php
    $variants = [
        'primary'   => 'bg-primary text-white shadow-sm hover:bg-primary-dark',
        'secondary' => 'bg-primary/10 text-primary border border-primary/20 hover:bg-primary/15',
        'outline'   => 'bg-white text-ink border border-line hover:bg-slate-50',
        'danger'    => 'bg-danger-bg text-danger-text border border-danger-text/20 hover:bg-danger-text hover:text-white',
    ][$variant] ?? 'bg-primary text-white shadow-sm hover:bg-primary-dark';

    $sizes = [
        'sm' => 'px-3.5 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
    ][$size] ?? 'px-4 py-2 text-sm';

    $classes = "inline-flex items-center justify-center gap-2 rounded-lg font-medium transition focus:outline-none focus:ring-2 focus:ring-primary/20 active:scale-[0.99] disabled:opacity-60 $sizes $variants";
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@elseif ($type === 'submit')
    <button
        type="submit"
        x-data="{ submitting: false }"
        @click="if ($el.form && $el.form.checkValidity()) { setTimeout(() => { submitting = true }, 50) }"
        :disabled="submitting"
        :class="{ 'cursor-wait opacity-75 pointer-events-none': submitting }"
        {{ $attributes->merge(['class' => $classes]) }}
    >
        <svg x-show="submitting" x-cloak class="h-4 w-4 animate-spin shrink-0 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <span x-show="!submitting" class="inline-flex items-center gap-2">{{ $slot }}</span>
        <span x-show="submitting" x-cloak>{{ $loadingText }}</span>
    </button>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
