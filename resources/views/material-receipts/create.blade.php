<x-app-layout>
    @php
    $inputClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';
    $labelClass = 'mb-1.5 block text-label uppercase text-muted';

    $poDataForJs = $purchaseOrders->mapWithKeys(function ($po) {
    return [
    $po->id => [
    'order_number' => $po->order_number,
    'vendor_name' => $po->vendor?->name ?? '-',
    'items' => $po->details->map(function ($d) {
    return [
    'ingredient_id' => $d->ingredient_id,
    'ingredient_name' => $d->ingredient?->name ?? '-',
    'quantity' => (float) $d->quantity,
    'unit' => $d->unit,
    'condition' => 'baik',
    'notes' => '',
    ];
    })->values()->all(),
    ],
    ];
    });
    @endphp

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('material-receipts.index') }}" class="text-muted transition hover:text-primary">Daftar Penerimaan</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Catat Penerimaan</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">Pencatatan Penerimaan Bahan &amp; Pemeriksaan Fisik</h1>
            </div>
            <p class="mt-1 text-small text-muted">Periksa kondisi bahan yang tiba dari supplier. Hanya bahan berkondisi baik yang menambah stok gudang.</p>
        </div>

        <a href="{{ route('material-receipts.index') }}"
            class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar
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

        <div x-data="{
            purchaseOrders: {{ Js::from($poDataForJs) }},
            ingredients: {{ Js::from($activeIngredients) }},
            selectedPoId: '{{ old('purchase_order_id', $selectedPoId ?? '') }}',
            items: {{ Js::from(old('items', [])) }},

            init() {
                if (this.items.length === 0) {
                    if (this.selectedPoId && this.purchaseOrders[this.selectedPoId]) {
                        this.loadItemsFromPo();
                    } else {
                        this.addItem();
                    }
                }
            },

            loadItemsFromPo() {
                if (!this.selectedPoId || !this.purchaseOrders[this.selectedPoId]) {
                    return;
                }
                const poItems = this.purchaseOrders[this.selectedPoId].items;
                this.items = poItems.map(item => ({
                    ingredient_id: item.ingredient_id,
                    quantity: item.quantity,
                    unit: item.unit,
                    condition: 'baik',
                    notes: ''
                }));
            },

            addItem() {
                this.items.push({
                    ingredient_id: this.ingredients.length > 0 ? this.ingredients[0].id : '',
                    quantity: 1,
                    unit: this.ingredients.length > 0 ? this.ingredients[0].unit : 'kg',
                    condition: 'baik',
                    notes: ''
                });
            },

            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },

            onIngredientChange(index) {
                const selectedId = this.items[index].ingredient_id;
                const found = this.ingredients.find(i => i.id == selectedId);
                if (found) {
                    this.items[index].unit = found.unit;
                }
            }
        }">

            <form method="POST" action="{{ route('material-receipts.store') }}" class="space-y-6">
                @csrf

                <!-- Bagian 1: Informasi penerimaan -->
                <x-card class="w-full sm:p-8">
                    <div class="border-b border-line pb-4">
                        <h2 class="text-lg font-semibold text-ink">Informasi Penerimaan</h2>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="purchase_order_id" class="{{ $labelClass }}">Rujukan Purchase Order (Opsional)</label>
                            <select name="purchase_order_id" id="purchase_order_id"
                                x-model="selectedPoId" @change="loadItemsFromPo()"
                                class="{{ $inputClass }}">
                                <option value="">-- Penerimaan Mandiri / Langsung (Tanpa PO) --</option>
                                @foreach ($purchaseOrders as $po)
                                <option value="{{ $po->id }}">
                                    {{ $po->order_number }} &mdash; {{ $po->vendor?->name }} ({{ $po->status->label() }})
                                </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-small text-muted">Memilih PO akan otomatis memuat daftar bahan yang dipesan.</p>
                        </div>

                        <div>
                            <label for="receipt_date" class="{{ $labelClass }}">Tanggal Penerimaan Fisik <span class="text-danger-text">*</span></label>
                            <input type="date" name="receipt_date" id="receipt_date"
                                value="{{ old('receipt_date', $today) }}" required class="{{ $inputClass }}">
                        </div>

                        <div class="md:col-span-2">
                            <label for="notes" class="{{ $labelClass }}">Catatan Penerimaan / Surat Jalan</label>
                            <input type="text" name="notes" id="notes" value="{{ old('notes') }}"
                                placeholder="Contoh: Surat Jalan No. SJ-8891 dari kurir..."
                                class="{{ $inputClass }}">
                        </div>
                    </div>
                </x-card>

                <!-- Bagian 2: Rincian bahan & pemeriksaan -->
                <x-card class="w-full sm:p-8">
                    <div class="flex flex-col gap-3 border-b border-line pb-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-ink">Daftar Bahan &amp; Hasil Pemeriksaan Fisik</h2>
                            <p class="mt-0.5 text-small text-muted">Pilih kondisi fisik tiap bahan. Hanya bahan berstatus Baik yang menambah stok.</p>
                        </div>
                        <button type="button" @click="addItem()"
                            class="inline-flex shrink-0 items-center gap-2 self-start rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-primary transition hover:bg-primary/5">
                            <x-icon name="plus" class="h-4 w-4" />
                            Tambah Baris Bahan
                        </button>
                    </div>

                    <div class="mt-6 space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="rounded-input border p-4 transition"
                                :class="item.condition === 'baik' ? 'border-line bg-primary/5' : 'border-danger-text/20 bg-danger-bg'">
                                <div class="mb-3 flex items-center justify-between">
                                    <span class="text-label uppercase text-muted" x-text="'Bahan #' + (index + 1)"></span>
                                    <button type="button" x-show="items.length > 1" @click="removeItem(index)"
                                        class="text-small font-medium text-danger-text transition hover:underline">Hapus</button>
                                </div>

                                <div class="grid grid-cols-1 items-end gap-4 md:grid-cols-12">
                                    <div class="md:col-span-4">
                                        <label class="{{ $labelClass }}">Bahan Baku</label>
                                        <select :name="`items[${index}][ingredient_id]`"
                                            x-model="item.ingredient_id" @change="onIngredientChange(index)" required
                                            class="{{ $inputClass }}">
                                            <template x-for="ing in ingredients" :key="ing.id">
                                                <option :value="ing.id" x-text="`${ing.name} (Stok: ${Number(ing.current_stock || 0)} ${ing.unit})`"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="{{ $labelClass }}">Jumlah</label>
                                        <input type="number" step="0.001" min="0.001"
                                            :name="`items[${index}][quantity]`" x-model="item.quantity" required
                                            class="{{ $inputClass }} text-right">
                                    </div>

                                    <div class="md:col-span-1">
                                        <label class="{{ $labelClass }}">Satuan</label>
                                        <input type="text" readonly :name="`items[${index}][unit]`" x-model="item.unit"
                                            class="{{ $inputClass }} cursor-not-allowed bg-gray-100 text-muted">
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="{{ $labelClass }}">Kondisi Fisik</label>
                                        <select :name="`items[${index}][condition]`" x-model="item.condition" required
                                            class="{{ $inputClass }}"
                                            :class="item.condition === 'baik' ? '' : 'font-semibold text-danger-text'">
                                            @foreach ($conditions as $c)
                                            <option value="{{ $c->value }}">{{ $c->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="md:col-span-3">
                                        <label class="{{ $labelClass }}">Keterangan / Catatan</label>
                                        <input type="text" :name="`items[${index}][notes]`" x-model="item.notes"
                                            placeholder="Misal: Kemasan bocor..." class="{{ $inputClass }}">
                                    </div>
                                </div>

                                <p x-show="item.condition !== 'baik'" class="mt-3 text-small font-medium text-danger-text">
                                    Item ini ditolak dan tidak akan menambah saldo stok gudang.
                                </p>
                            </div>
                        </template>
                    </div>
                </x-card>

                <!-- Tombol Aksi -->
                <div class="flex items-center justify-end gap-3">
                    <x-button variant="outline" :href="route('material-receipts.index')">Batal</x-button>
                    <x-button type="submit">Simpan &amp; Masukkan Stok Layak</x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>