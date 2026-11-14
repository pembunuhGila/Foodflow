<div x-data="{
        open: false,
        loading: false,
        formId: null,
        tone: 'primary',
        eyebrow: '',
        title: '',
        subjectLabel: '',
        subject: '',
        message: '',
        detail: '',
        confirmLabel: 'Ya, Lanjutkan',
        show(d) {
            Object.assign(this, { formId: null, tone: 'primary', eyebrow: '', title: '', subjectLabel: '', subject: '', message: '', detail: '', confirmLabel: 'Ya, Lanjutkan', loading: false }, d || {});
            this.open = true;
            this.$nextTick(() => this.$refs.cancel.focus());
        },
        confirm() {
            const form = document.getElementById(this.formId);
            if (form) { this.loading = true; form.submit(); }
        }
     }"
    @open-confirm.window="show($event.detail)"
    @keydown.escape.window="open = false">

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
        role="dialog" aria-modal="true" aria-labelledby="confirm-title">
        <div class="absolute inset-0 bg-ink/40 backdrop-blur-sm" @click="open = false"></div>

        <div x-show="open" x-transition.opacity.duration.150ms
            class="relative w-full max-w-lg overflow-hidden rounded-2xl bg-white p-6 shadow-dropdown border border-line">

            {{-- Cahaya lembut di pojok --}}
            <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full blur-2xl"
                :class="tone === 'danger' ? 'bg-danger-bg' : 'bg-primary/10'"></div>

            <div class="relative flex items-start gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg"
                    :class="tone === 'danger' ? 'bg-danger-bg text-danger-text' : 'bg-primary/10 text-primary'">
                    <x-icon name="alert" class="h-5 w-5" x-show="tone === 'danger'" />
                    <x-icon name="check" class="h-5 w-5" x-show="tone !== 'danger'" x-cloak />
                </div>
                <div class="min-w-0">
                    <div class="text-[11px] font-bold uppercase tracking-wider" :class="tone === 'danger' ? 'text-danger-text' : 'text-primary'" x-text="eyebrow"></div>
                    <h2 id="confirm-title" class="mt-1 font-display text-xl font-bold text-ink" x-text="title"></h2>
                </div>
            </div>

            <div class="relative mt-4 rounded-lg bg-slate-50 border border-line/70 p-4">
                <p class="text-sm text-ink leading-relaxed" x-text="message"></p>

                <div x-show="subject" x-cloak class="mt-3 rounded-md bg-white border border-line px-3.5 py-2.5">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-muted" x-text="subjectLabel"></div>
                    <div class="font-display text-base font-bold text-ink mt-0.5" x-text="subject"></div>
                </div>

                <p x-show="detail" x-cloak class="mt-2 text-xs text-muted" x-text="detail"></p>
            </div>

            <div class="relative mt-5 flex items-center justify-end gap-2.5">
                <button type="button" x-ref="cancel" @click="open = false"
                    class="rounded-lg border border-line bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="button" @click="confirm()" :disabled="loading"
                    class="rounded-lg px-4 py-2 text-sm font-semibold text-white shadow-sm transition disabled:opacity-60"
                    :class="tone === 'danger' ? 'bg-danger-text hover:bg-red-700' : 'bg-primary hover:bg-primary-dark'"
                    x-text="loading ? 'Memproses...' : confirmLabel"></button>
            </div>
        </div>
    </div>
</div>