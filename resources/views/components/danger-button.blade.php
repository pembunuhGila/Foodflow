<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 rounded-full border border-danger-text/20 bg-danger-bg px-5 py-2.5 text-body font-medium text-danger-text transition hover:bg-danger-text hover:text-white focus:outline-none focus:ring-2 focus:ring-danger-text/30 active:scale-[0.98] disabled:opacity-60']) }}>
    {{ $slot }}
</button>