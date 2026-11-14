<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 rounded-lg border border-danger-text/20 bg-danger-bg px-4 py-2 text-sm font-semibold text-danger-text transition hover:bg-danger-text hover:text-white focus:outline-none focus:ring-2 focus:ring-danger-text/20 active:scale-[0.99] disabled:opacity-60']) }}>
    {{ $slot }}
</button>
