<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Pencatatan Penerimaan Bahan & Pemeriksaan Fisik') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Periksa kondisi bahan yang tiba dari supplier (BR-04: Hanya bahan 'Baik' yang menambah stok gudang).
                </p>
            </div>
            <div>
                <a href="{{ route('material-receipts.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold uppercase tracking-widest rounded-lg transition shadow-sm">
                    &larr; Kembali ke Daftar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
                    <div class="flex items-center mb-1">
                        <svg class="w-5 h-5 text-red-500 me-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <h3 class="text-sm font-semibold text-red-800">Terdapat kesalahan pada isian formulir:</h3>
                    </div>
                    <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $poDataForJs = $purchaseOrders->mapWithKeys(function ($po) {
                    return [
                        $po->id => [
                            'order_number' => $po->order_number,
                            'vendor_name' => $po->vendor?->name ?? '—',
                            'items' => $po->details->map(function ($d) {
                                return [
                                    'ingredient_id' => $d->ingredient_id,
                                    'ingredient_name' => $d->ingredient?->name ?? '—',
                                    'quantity' => (float) $d->quantity,
                                    'unit' => $d->unit,
                                    'condition' => 'baik',
                                    'notes' => '',
                                ];
                            })->values()->all(),
                        ]
                    ];
                });
            @endphp

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
            }" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-6">

                <form method="POST" action="{{ route('material-receipts.store') }}" class="space-y-6">
                    @csrf

                    {{-- Informasi Penerimaan --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4 border-b border-gray-100">
                        <div>
                            <label for="purchase_order_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                Rujukan Purchase Order (Opsional)
                            </label>
                            <select name="purchase_order_id"
                                    id="purchase_order_id"
                                    x-model="selectedPoId"
                                    @change="loadItemsFromPo()"
                                    class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">-- Penerimaan Mandiri / Langsung (Tanpa PO) --</option>
                                @foreach ($purchaseOrders as $po)
                                    <option value="{{ $po->id }}">
                                        {{ $po->order_number }} &mdash; {{ $po->vendor?->name }} ({{ $po->status->label() }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-gray-400 mt-1">Memilih PO akan otomatis memuat daftar bahan yang dipesan.</p>
                        </div>

                        <div>
                            <label for="receipt_date" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                Tanggal Penerimaan Fisik <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   name="receipt_date"
                                   id="receipt_date"
                                   value="{{ old('receipt_date', $today) }}"
                                   required
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="notes" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                Catatan Penerimaan / Surat Jalan
                            </label>
                            <input type="text"
                                   name="notes"
                                   id="notes"
                                   value="{{ old('notes') }}"
                                   placeholder="Contoh: Surat Jalan No. SJ-8891 dari kurir..."
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                    </div>

                    {{-- Form Rincian Bahan & Pemeriksaan Fisik --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-sm font-bold text-gray-800">Daftar Bahan & Hasil Pemeriksaan Fisik (UC-06)</h3>
                                <p class="text-xs text-gray-500">Pilih kondisi fisik tiap bahan: hanya bahan berstatus <strong>Baik</strong> yang menambah stok (BR-04).</p>
                            </div>
                            <button type="button"
                                    @click="addItem()"
                                    class="inline-flex items-center px-3 py-1.5 bg-emerald-50 border border-emerald-300 text-emerald-700 text-xs font-semibold rounded-lg hover:bg-emerald-100 transition">
                                <svg class="w-3.5 h-3.5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Baris Bahan
                            </button>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="p-4 rounded-xl border transition"
                                     :class="item.condition === 'baik' ? 'bg-emerald-50/40 border-emerald-200' : 'bg-red-50/40 border-red-200'">
                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-start">

                                        {{-- Bahan --}}
                                        <div class="sm:col-span-4">
                                            <label class="block text-[11px] font-semibold text-gray-600 mb-1">Bahan Baku</label>
                                            <select :name="`items[${index}][ingredient_id]`"
                                                    x-model="item.ingredient_id"
                                                    @change="onIngredientChange(index)"
                                                    required
                                                    class="w-full text-xs rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                                                <template x-for="ing in ingredients" :key="ing.id">
                                                    <option :value="ing.id" x-text="`${ing.name} (Stok: ${Number(ing.current_stock || 0)} ${ing.unit})`"></option>
                                                </template>
                                            </select>
                                        </div>

                                        {{-- Jumlah Diterima --}}
                                        <div class="sm:col-span-2">
                                            <label class="block text-[11px] font-semibold text-gray-600 mb-1">Jumlah</label>
                                            <input type="number"
                                                   step="0.001"
                                                   min="0.001"
                                                   :name="`items[${index}][quantity]`"
                                                   x-model="item.quantity"
                                                   required
                                                   class="w-full text-xs rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-right">
                                        </div>

                                        {{-- Satuan --}}
                                        <div class="sm:col-span-1">
                                            <label class="block text-[11px] font-semibold text-gray-600 mb-1">Satuan</label>
                                            <input type="text"
                                                   readonly
                                                   :name="`items[${index}][unit]`"
                                                   x-model="item.unit"
                                                   class="w-full text-xs rounded-lg bg-gray-50 border-gray-300 text-gray-600">
                                        </div>

                                        {{-- Kondisi Fisik (BR-04) --}}
                                        <div class="sm:col-span-2">
                                            <label class="block text-[11px] font-semibold text-gray-600 mb-1">Kondisi Fisik</label>
                                            <select :name="`items[${index}][condition]`"
                                                    x-model="item.condition"
                                                    required
                                                    class="w-full text-xs rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 font-medium"
                                                    :class="item.condition === 'baik' ? 'text-emerald-700' : 'text-red-700 font-bold'">
                                                @foreach ($conditions as $c)
                                                    <option value="{{ $c->value }}">{{ $c->label() }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Catatan Kerusakan / Keterangan --}}
                                        <div class="sm:col-span-2">
                                            <label class="block text-[11px] font-semibold text-gray-600 mb-1">Keterangan / Catatan</label>
                                            <input type="text"
                                                   :name="`items[${index}][notes]`"
                                                   x-model="item.notes"
                                                   placeholder="Misal: Kemasan bocor..."
                                                   class="w-full text-xs rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                                        </div>

                                        {{-- Hapus Baris --}}
                                        <div class="sm:col-span-1 flex items-end justify-center pt-5">
                                            <button type="button"
                                                    @click="removeItem(index)"
                                                    :disabled="items.length === 1"
                                                    class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-gray-100 transition disabled:opacity-30">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>

                                    </div>

                                    {{-- Warning Alert jika bukan 'baik' --}}
                                    <div x-show="item.condition !== 'baik'" class="mt-2 text-[11px] text-red-600 flex items-center space-x-1 font-medium">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        <span>Item ini ditolak dan TIDAK akan menambahkan saldo stok gudang (BR-04).</span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('material-receipts.index') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition flex items-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan & Masukkan Stok Layak</span>
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</x-app-layout>
