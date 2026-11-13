{{-- MODAL TANDA TANGAN CANVAS INTERAKTIF (NO-PRINT) --}}
<div x-show="signModalOpen"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="no-print fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
     style="display: none;">
    
    <div @click.away="signModalOpen = false" 
         class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl border border-line">
        
        <div class="flex items-start justify-between border-b border-line pb-4">
            <div>
                <h3 class="font-display text-lg font-bold text-ink">Bubuhkan Tanda Tangan Digital</h3>
                <p class="text-xs text-muted mt-1">
                    Atas nama: <strong class="text-ink" x-text="activeName"></strong> (<span x-text="activeRole"></span>)
                </p>
            </div>
            <button type="button" @click="signModalOpen = false" class="text-muted hover:text-ink text-2xl font-bold leading-none p-1">&times;</button>
        </div>

        {{-- Canvas Area --}}
        <div class="mt-4">
            <div class="mb-2 flex items-center justify-between text-xs text-muted">
                <span class="flex items-center gap-1.5 font-medium">
                    <svg class="h-3.5 w-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Goreskan tanda tangan pakai mouse, stylus, atau sentuhan jari:
                </span>
                <button type="button" @click="usePresetSignature()" class="font-bold text-primary hover:text-primary-dark transition-colors flex items-center gap-1">
                    <span>⚡ Ttd Otomatis</span>
                </button>
            </div>

            <div class="relative overflow-hidden rounded-xl border-2 border-line bg-canvas">
                <canvas id="signatureCanvas" 
                        class="block h-48 w-full touch-none cursor-crosshair bg-white"
                        @mousedown="startDrawing($event)"
                        @mousemove="draw($event)"
                        @mouseup="stopDrawing()"
                        @mouseleave="stopDrawing()"
                        @touchstart.passive="startDrawing($event)"
                        @touchmove="draw($event)"
                        @touchend="stopDrawing()">
                </canvas>
                {{-- Garis panduan tanda tangan --}}
                <div class="pointer-events-none absolute bottom-9 left-8 right-8 border-b border-dashed border-slate-300">
                    <span class="text-[9px] uppercase tracking-wider text-slate-400">Garis Panduan Tanda Tangan</span>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-5 flex items-center justify-between gap-3 border-t border-line pt-4">
            <button type="button" @click="clearCanvas()" class="inline-flex items-center gap-1.5 rounded-xl border border-line px-3.5 py-2 text-xs font-semibold text-muted hover:bg-slate-50 transition">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Bersihkan Kanvas
            </button>

            <div class="flex items-center gap-2">
                <button type="button" @click="signModalOpen = false" class="rounded-xl px-4 py-2 text-xs font-semibold text-muted hover:text-ink transition">
                    Batal
                </button>
                <button type="button" @click="saveSignature()" class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-5 py-2 text-xs font-semibold text-white shadow-md hover:bg-primary-dark transition">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Terapkan Tanda Tangan
                </button>
            </div>
        </div>

    </div>
</div>

<script>
function digitalSignatureApp(defaultSigner1 = 'Penanggung Jawab', defaultSigner2 = 'Rachmad Zaki Setyawan') {
    return {
        signer1Name: defaultSigner1,
        signer2Name: defaultSigner2,
        signModalOpen: false,
        activeKey: '',
        activeName: '',
        activeRole: '',
        isDrawing: false,
        hasDrawn: false,
        signatures: {
            signer1: null,
            signer2: null
        },
        timestamps: {
            signer1: '',
            signer2: ''
        },
        canvas: null,
        ctx: null,

        initSignatures() {
            // Tanda tangan dan timestamp selalu mulai dari kondisi bersih (reset otomatis saat halaman dibuka)
            this.signatures.signer1 = null;
            this.signatures.signer2 = null;
            this.timestamps.signer1 = '';
            this.timestamps.signer2 = '';

            // Bersihkan sisa penyimpanan sesi lama agar tidak menempel ke dokumen baru
            try {
                ['signer1', 'signer2'].forEach(k => {
                    localStorage.removeItem(`foodflow_sig_${k}`);
                    localStorage.removeItem(`foodflow_sig_time_${k}`);
                });
            } catch (err) {}
        },

        resetAll() {
            this.signer1Name = defaultSigner1;
            this.signer2Name = defaultSigner2;
            this.initSignatures();
        },

        openSignModal(key, name, role) {
            this.activeKey = key;
            this.activeName = name;
            this.activeRole = role;
            this.signModalOpen = true;
            this.hasDrawn = false;

            this.$nextTick(() => {
                this.setupCanvas();
            });
        },

        setupCanvas() {
            this.canvas = document.getElementById('signatureCanvas');
            if (!this.canvas) return;

            const rect = this.canvas.getBoundingClientRect();
            this.canvas.width = rect.width * 2;
            this.canvas.height = rect.height * 2;
            this.ctx = this.canvas.getContext('2d');
            this.ctx.scale(2, 2);

            this.ctx.lineWidth = 2.6;
            this.ctx.lineCap = 'round';
            this.ctx.lineJoin = 'round';
            this.ctx.strokeStyle = '#1E1B4B';

            this.clearCanvas();
        },

        getPointerPos(e) {
            const rect = this.canvas.getBoundingClientRect();
            let clientX, clientY;
            if (e.touches && e.touches.length > 0) {
                clientX = e.touches[0].clientX;
                clientY = e.touches[0].clientY;
            } else {
                clientX = e.clientX;
                clientY = e.clientY;
            }
            return {
                x: clientX - rect.left,
                y: clientY - rect.top
            };
        },

        startDrawing(e) {
            this.isDrawing = true;
            const pos = this.getPointerPos(e);
            this.ctx.beginPath();
            this.ctx.moveTo(pos.x, pos.y);
            this.hasDrawn = true;
        },

        draw(e) {
            if (!this.isDrawing) return;
            if (e.cancelable) e.preventDefault();
            const pos = this.getPointerPos(e);
            this.ctx.lineTo(pos.x, pos.y);
            this.ctx.stroke();
        },

        stopDrawing() {
            if (this.isDrawing) {
                this.isDrawing = false;
                this.ctx.closePath();
            }
        },

        clearCanvas() {
            if (!this.ctx || !this.canvas) return;
            const rect = this.canvas.getBoundingClientRect();
            this.ctx.clearRect(0, 0, rect.width, rect.height);
            this.hasDrawn = false;
        },

        usePresetSignature() {
            if (!this.ctx || !this.canvas) return;
            this.clearCanvas();
            const rect = this.canvas.getBoundingClientRect();
            const w = rect.width;
            const h = rect.height;

            // Gambar goresan tanda tangan digital natural dan elegan
            this.ctx.save();
            this.ctx.strokeStyle = '#1E1B4B';
            this.ctx.lineWidth = 2.6;
            this.ctx.beginPath();

            // Huruf kapital awal dengan lengkungan estetik
            this.ctx.moveTo(w * 0.18, h * 0.70);
            this.ctx.bezierCurveTo(w * 0.22, h * 0.20, w * 0.32, h * 0.18, w * 0.35, h * 0.52);
            this.ctx.bezierCurveTo(w * 0.38, h * 0.82, w * 0.26, h * 0.85, w * 0.23, h * 0.62);
            this.ctx.bezierCurveTo(w * 0.20, h * 0.42, w * 0.32, h * 0.40, w * 0.44, h * 0.50);

            // Goresan sambung tengah
            this.ctx.bezierCurveTo(w * 0.50, h * 0.58, w * 0.54, h * 0.38, w * 0.58, h * 0.54);
            this.ctx.bezierCurveTo(w * 0.62, h * 0.64, w * 0.65, h * 0.36, w * 0.70, h * 0.50);
            this.ctx.bezierCurveTo(w * 0.74, h * 0.60, w * 0.78, h * 0.40, w * 0.82, h * 0.52);

            // Garis bawah tanda tangan (underline flourish)
            this.ctx.moveTo(w * 0.22, h * 0.76);
            this.ctx.bezierCurveTo(w * 0.42, h * 0.72, w * 0.65, h * 0.78, w * 0.86, h * 0.68);
            this.ctx.stroke();
            this.ctx.restore();

            this.hasDrawn = true;
        },

        saveSignature() {
            if (!this.hasDrawn) {
                alert('Silakan goreskan tanda tangan pada canvas terlebih dahulu, atau klik "Ttd Otomatis"');
                return;
            }

            const dataUrl = this.canvas.toDataURL('image/png');
            this.signatures[this.activeKey] = dataUrl;

            const now = new Date();
            const dateStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            const timeLabel = `${dateStr} ${timeStr} WIB`;

            this.timestamps[this.activeKey] = timeLabel;

            try {
                localStorage.setItem(`foodflow_sig_${this.activeKey}`, dataUrl);
                localStorage.setItem(`foodflow_sig_time_${this.activeKey}`, timeLabel);
            } catch (err) {
                console.warn('LocalStorage limit exceeded:', err);
            }

            this.signModalOpen = false;
        },

        removeSignature(key) {
            this.signatures[key] = null;
            this.timestamps[key] = '';
            try {
                localStorage.removeItem(`foodflow_sig_${key}`);
                localStorage.removeItem(`foodflow_sig_time_${key}`);
            } catch (err) {}
        }
    };
}
</script>
