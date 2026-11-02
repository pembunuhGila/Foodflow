<x-app-layout>
    @php
        $inputClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';
        $labelClass = 'mb-1.5 block text-label uppercase text-muted';

        $ingredientsMap = $ingredients->keyBy('id')->map(function ($ing) {
            return [
                'id' => $ing->id,
                'name' => $ing->name,
                'current_stock' => (float) ($ing->current_stock ?? 0),
                'unit' => $ing->unit,
            ];
        });
    @endphp

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('stock-cards.index') }}" class="text-muted transition hover:text-primary">Kartu Stok</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Penyesuaian Stok</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">Penyesuaian Stok Fisik</h1>
            </div>
            <p class="mt-1 text-small text-muted">Koreksi perbedaan antara saldo sistem dengan hasil stok opname fisik di gudang.</p>
        </div>

        <a href="{{ route('stock-cards.index') }}"
            class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Kartu Stok
        </a>
    </div>

    <div class="space-y-6">

        @if ($errors->any())
        <div class="rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text" role="alert">
            <p class="mb-2 font-semibold">Terdapat kesalahan pada isian formulir:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <x-card class="w-full sm:p-8"
                x-data="{
                    ingredients: {{ Js::from($ingredientsMap) }},
                    selectedId: '{{ old('ingredient_id', $ingredients->first()?->id ?? '') }}',
                    physicalCount: '{{ old('physical_count', '') }}',

                    get currentIngredient() {
                        return this.ingredients[this.selectedId] || { current_stock: 0, unit: '' };
                    },

                    get difference() {
                        if (this.physicalCount === '' || isNaN(this.physicalCount)) {
                            return 0;
                        }
                        return (parseFloat(this.physicalCount) - this.currentIngredient.current_stock).toFixed(3);
                    }
                }">

            <div class="rounded-input border border-line bg-primary/5 p-4 text-small text-muted">
                <span class="font-semibold text-ink">Aturan bisnis:</span>
                Perubahan saldo stok tidak boleh diedit langsung tanpa jejak mutasi. Formulir ini otomatis menerbitkan transaksi <code>adjustment</code> pada kartu stok dan menyelaraskan saldo fisik gudang.
            </div>

            <form method="POST" action="{{ route('stock-adjustments.store') }}" class="mt-6 space-y-6">
                @csrf

                <div>
                    <label for="ingredient_id" class="{{ $labelClass }}">Pilih Bahan Baku <span class="text-danger-text">*</span></label>
                    <select name="ingredient_id" id="ingredient_id" x-model="selectedId" required class="{{ $inputClass }}">
                        @foreach ($ingredients as $ing)
                        <option value="{{ $ing->id }}">
                            {{ $ing->name }} (Saldo Sistem: {{ (float) ($ing->current_stock ?? 0) }} {{ $ing->unit }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Perbandingan saldo sistem vs fisik --}}
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="rounded-input border border-line bg-primary/5 p-4">
                        <div class="text-label uppercase text-muted">Saldo Tercatat di Sistem</div>
                        <p class="mt-1.5 text-body font-medium text-ink">
                            <span x-text="currentIngredient.current_stock"></span>
                            <span class="text-small font-normal text-muted" x-text="currentIngredient.unit"></span>
                        </p>
                    </div>

                    <div class="rounded-input border border-line bg-primary/5 p-4">
                        <label for="physical_count" class="block text-label uppercase text-muted">
                            Hasil Hitung Fisik <span class="text-danger-text">*</span>
                        </label>
                        <div class="mt-1.5 flex items-center gap-2">
                            <input type="number" step="0.001" min="0" name="physical_count" id="physical_count"
                                   x-model="physicalCount" required placeholder="0.00" class="{{ $inputClass }}">
                            <span class="shrink-0 text-small font-medium text-muted" x-text="currentIngredient.unit"></span>
                        </div>
                    </div>

                    <div class="rounded-input border border-line bg-primary/5 p-4">
                        <div class="text-label uppercase text-muted">Selisih Penyesuaian</div>
                        <p class="mt-1.5 text-body font-semibold"
                           :class="difference > 0 ? 'text-primary' : (difference < 0 ? 'text-danger-text' : 'text-muted')">
                            <span x-text="difference > 0 ? `+${difference}` : difference"></span>
                            <span class="text-small font-normal text-muted" x-text="currentIngredient.unit"></span>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="adjustment_date" class="{{ $labelClass }}">Tanggal Penyesuaian <span class="text-danger-text">*</span></label>
                        <input type="date" name="adjustment_date" id="adjustment_date"
                               value="{{ old('adjustment_date', $today) }}" required class="{{ $inputClass }}">
                    </div>
                </div>

                <div>
                    <label for="notes" class="{{ $labelClass }}">Alasan Penyesuaian Stok (Wajib Diisi) <span class="text-danger-text">*</span></label>
                    <textarea name="notes" id="notes" rows="3" required
                              placeholder="Contoh: Susut berat pada sayuran segar, perbedaan timbangan supplier, atau koreksi barang tumpah..."
                              class="{{ $inputClass }}">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-line pt-6">
                    <x-button variant="outline" :href="route('stock-cards.index')">Batal</x-button>
                    <x-button type="submit">Simpan Penyesuaian Stok</x-button>
                </div>
            </form>
        </x-card>

    </div>
</x-app-layout>