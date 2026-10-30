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
            class="relative w-full max-w-lg overflow-hidden rounded-3xl bg-white p-7 shadow-card">

            {{-- Cahaya lembut di pojok --}}
            <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full blur-2xl"
                :class="tone === 'danger' ? 'bg-danger-bg' : 'bg-primary/10'"></div>

            <div class="relative flex items-start gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-input"
                    :class="tone === 'danger' ? 'bg-danger-bg text-danger-text' : 'bg-primary/10 text-primary'">
                    <x-icon name="alert" class="h-6 w-6" x-show="tone === 'danger'" />
                    <x-icon name="check" class="h-6 w-6" x-show="tone !== 'danger'" x-cloak />
                </div>
                <div class="min-w-0">
                    <div class="text-label uppercase" :class="tone === 'danger' ? 'text-danger-text' : 'text-primary'" x-text="eyebrow"></div>
                    <h2 id="confirm-title" class="mt-1 font-display text-h2 text-ink" x-text="title"></h2>
                </div>
            </div>

            <div class="relative mt-5 rounded-input bg-primary/5 p-4">
                <p class="text-body text-ink" x-text="message"></p>

                <div x-show="subject" x-cloak class="mt-3 rounded-input bg-white px-4 py-3">
                    <div class="text-label uppercase text-muted" x-text="subjectLabel"></div>
                    <div class="font-display text-h3 text-ink" x-text="subject"></div>
                </div>

                <p x-show="detail" x-cloak class="mt-3 text-small text-muted" x-text="detail"></p>
            </div>

            <div class="relative mt-6 flex items-center justify-end gap-3">
                <button type="button" x-ref="cancel" @click="open = false"
                    class="rounded-full px-5 py-2.5 text-body font-medium text-muted transition hover:text-ink focus:outline-none focus:ring-2 focus:ring-primary/30">
                    Batal
                </button>
                <button type="button" @click="confirm()" :disabled="loading"
                    class="rounded-full px-5 py-2.5 text-body font-medium text-white shadow-card transition focus:outline-none focus:ring-2 focus:ring-primary/30 disabled:opacity-60"
                    :class="tone === 'danger' ? 'bg-danger-text hover:opacity-90' : 'bg-primary hover:bg-primary-dark'"
                    x-text="loading ? 'Memproses...' : confirmLabel"></button>
            </div>
        </div>
    </div>
</div>