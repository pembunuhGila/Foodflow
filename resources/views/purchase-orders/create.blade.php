<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Buat Purchase Order Bahan Baku') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Buat pesanan pengadaan bahan baku ke vendor. Nomor PO dibuat otomatis oleh sistem.
                </p>
            </div>
            <div>
                <a href="{{ route('purchase-orders.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold uppercase tracking-widest rounded-lg transition shadow-sm">
                    &larr; Kembali ke Daftar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 me-2 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-semibold text-red-800">Mohon periksa kembali input Anda:</h3>
                            <ul class="mt-1 text-xs text-red-700 list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('purchase-orders.store') }}"
                  x-data="purchaseOrderForm({
                      availableIngredients: @js($ingredients),
                      oldItems: @js(old('items', [['ingredient_id' => '', 'quantity' => '', 'unit' => '', 'price' => '']]))
                  })"
                  class="space-y-6">
                @csrf

                {{-- Card 1: Informasi Header PO --}}
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                    <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-2 flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Informasi Purchase Order</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Nomor PO (read-only, info saja) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor PO (Otomatis)
                            </label>
                            <div class="w-full text-sm rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-gray-500 font-mono">
                                {{ $suggestedOrderNumber }}
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1">Nomor PO digenerate otomatis saat disimpan.</p>
                        </div>

                        {{-- Pilih Vendor --}}
                        <div>
                            <label for="vendor_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Vendor / Supplier <span class="text-red-500">*</span>
                            </label>
                            <select name="vendor_id"
                                    id="vendor_id"
                                    required
                                    class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                                <option value="">-- Pilih Vendor --</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vendor_id')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tanggal Pesan --}}
                        <div>
                            <label for="order_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Pesan <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   name="order_date"
                                   id="order_date"
                                   value="{{ old('order_date', $today) }}"
                                   required
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <p class="text-[11px] text-gray-400 mt-1">Tanggal saat PO ini dibuat dan dikirim ke vendor.</p>
                            @error('order_date')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tanggal Pengiriman yang Diharapkan --}}
                        <div>
                            <label for="expected_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Pengiriman Diharapkan <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   name="expected_date"
                                   id="expected_date"
                                   value="{{ old('expected_date') }}"
                                   required
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <p class="text-[11px] text-gray-400 mt-1">Tanggal yang diharapkan vendor mengirimkan barang. Tidak boleh sebelum tanggal pesan.</p>
                            @error('expected_date')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                            Catatan / Instruksi Khusus (Opsional)
                        </label>
                        <textarea name="notes"
                                  id="notes"
                                  rows="2"
                                  placeholder="Contoh: Tolong kirim sebelum jam 08.00 pagi, hubungi Pak Budi saat tiba..."
                                  class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- Card 2: Rincian Bahan Baku yang Dipesan --}}
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 flex items-center space-x-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <span>Daftar Bahan Baku yang Dipesan</span>
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">Pilih bahan baku, isi jumlah dan perkiraan harga beli. Subtotal dihitung otomatis.</p>
                        </div>

                        <button type="button"
                                @click="addItem()"
                                class="inline-flex items-center text-xs font-semibold px-3 py-1.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition shadow-xs">
                            <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Bahan
                        </button>
                    </div>

                    {{-- Dynamic Item Rows --}}
                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="p-4 rounded-lg border border-gray-200 bg-gray-50/50 hover:bg-white hover:border-blue-300 transition space-y-3">
                                <div class="flex items-start justify-between">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider" x-text="'Bahan #' + (index + 1)"></span>
                                    <button type="button"
                                            x-show="items.length > 1"
                                            @click="removeItem(index)"
                                            class="text-xs text-red-600 hover:text-red-800 font-medium transition"
                                            title="Hapus baris ini">
                                        &times; Hapus Baris
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                                    {{-- Pilih Bahan Baku --}}
                                    <div class="md:col-span-5">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                            Bahan Baku <span class="text-red-500">*</span>
                                        </label>
                                        <select :name="'items[' + index + '][ingredient_id]'"
                                                x-model="item.ingredient_id"
                                                @change="onIngredientChange(index)"
                                                required
                                                class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                                            <option value="">-- Pilih Bahan Baku --</option>
                                            <template x-for="ing in availableIngredients" :key="ing.id">
                                                <option :value="ing.id"
                                                        :selected="item.ingredient_id == ing.id"
                                                        x-text="ing.name + ' (' + ing.unit + ')'">
                                                </option>
                                            </template>
                                        </select>
                                    </div>

                                    {{-- Satuan (read-only, auto-filled) --}}
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                            Satuan
                                        </label>
                                        <input type="text"
                                               
                                               x-model="item.unit"
                                               readonly
                                               placeholder="—"
                                               class="w-full text-sm rounded-lg border-gray-200 bg-gray-100 text-gray-600 cursor-not-allowed">
                                    </div>

                                    {{-- Jumlah --}}
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                            Jumlah <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number"
                                               :name="'items[' + index + '][quantity]'"
                                               x-model="item.quantity"
                                               @input="
                                                    // Replace comma with dot for decimal separator
                                                    item.quantity = item.quantity?.toString().replace(',', '.');
                                                    recalcSubtotal(index)"
                                               :min="getMinForUnit(item.unit)"
                                               :step="getStepForUnit(item.unit)"
                                               required
                                               placeholder=""
                                               class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    </div>

                                    {{-- Harga Satuan --}}
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                            Harga/Satuan (Rp) <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number"
                                               :name="'items[' + index + '][price]'"
                                               x-model.number="item.price"
                                               @input="recalcSubtotal(index)"
                                               min="0.01"
                                               step="0.01"
                                               required
                                               placeholder="0"
                                               class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    </div>

                                    {{-- Subtotal (read-only) --}}
                                    <div class="md:col-span-1">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                            Subtotal
                                        </label>
                                        <div class="text-sm font-semibold text-blue-700 py-2 text-right"
                                             x-text="formatRupiah(item.subtotal)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Ringkasan Grand Total --}}
                    <div class="p-4 rounded-lg bg-blue-50 border border-blue-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center space-x-4">
                            <div>
                                <span class="text-xs text-blue-600 uppercase tracking-wider block font-semibold">Total Jenis Bahan</span>
                                <span class="text-lg font-bold text-gray-800" x-text="items.filter(i => i.ingredient_id).length + ' Bahan'"></span>
                            </div>
                            <div class="h-8 w-px bg-blue-200"></div>
                            <div>
                                <span class="text-xs text-blue-600 uppercase tracking-wider block font-semibold">Grand Total Estimasi</span>
                                <span class="text-xl font-extrabold text-blue-700" x-text="formatRupiah(grandTotal())"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit Button Bar --}}
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <a href="{{ route('purchase-orders.index') }}"
                       class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit"
                            id="btn-submit-po"
                            class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-semibold text-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition shadow-sm flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Purchase Order</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- Alpine.js Component Script --}}
    <script>
        function purchaseOrderForm(config) {
            const defaultItem = { ingredient_id: '', quantity: '', unit: '', price: '', subtotal: 0 };

            // Normalisasi oldItems dari server
            const rawOldItems = config.oldItems && Array.isArray(config.oldItems) && config.oldItems.length > 0
                ? config.oldItems
                : [{ ...defaultItem }];

            const normalizedItems = rawOldItems.map(item => ({
                ingredient_id: item.ingredient_id || '',
                quantity: item.quantity !== undefined ? parseFloat(item.quantity) || '' : '',
                unit: item.unit || '',
                price: item.price !== undefined ? parseFloat(item.price) || '' : '',
                subtotal: (parseFloat(item.quantity) || 0) * (parseFloat(item.price) || 0),
            }));

            return {
                availableIngredients: config.availableIngredients || [],
                items: normalizedItems,

                addItem() {
                    this.items.push({ ...defaultItem });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                onIngredientChange(index) {
                    const item = this.items[index];
                    const ingredient = this.availableIngredients.find(ing => ing.id == item.ingredient_id);
                    if (ingredient) {
                        item.unit = ingredient.unit;
                    } else {
                        item.unit = '';
                    }
                },

                recalcSubtotal(index) {
                    const item = this.items[index];
                    const qty = parseFloat(item.quantity) || 0;
                    const price = parseFloat(item.price) || 0;
                    item.subtotal = qty * price;
                },

                grandTotal() {
                    return this.items.reduce((total, item) => total + (parseFloat(item.subtotal) || 0), 0);
                },

                formatRupiah(value) {
                    const num = parseFloat(value) || 0;
                    return 'Rp ' + num.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                },
                // Helper methods for unit-based quantity attributes
                getMinForUnit(unit) {
                    const decUnits = ['kg', 'g', 'gram', 'l', 'liter', 'ml'];
                    return decUnits.includes(unit?.toLowerCase()) ? 0.1 : 1;
                },
                getStepForUnit(unit) {
                    const decUnits = ['kg', 'g', 'gram', 'l', 'liter', 'ml'];
                    return decUnits.includes(unit?.toLowerCase()) ? 0.1 : 1;
                }
            };
        }
    </script>
</x-app-layout>
