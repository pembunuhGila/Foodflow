<x-app-layout>
    @php
    $inputClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';
    $labelClass = 'mb-1.5 block text-label uppercase text-muted';
    @endphp

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('purchase-orders.index') }}" class="text-muted transition hover:text-primary">Daftar Purchase Order</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Buat Purchase Order</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">Buat Purchase Order Bahan Baku</h1>
            </div>
            <p class="mt-1 text-small text-muted">Buat pesanan pengadaan bahan baku ke vendor. Nomor PO dibuat otomatis oleh sistem.</p>
        </div>

        <a href="{{ route('purchase-orders.index') }}"
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
            <p class="mb-2 font-semibold">Mohon periksa kembali input Anda:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('purchase-orders.store') }}"
            x-data="purchaseOrderForm({
                  availableIngredients: @js($ingredients),
                  oldItems: @js(old('items', [['ingredient_id' => '', 'quantity' => '', 'unit' => '', 'price' => '']]))
              })"
            class="space-y-6">
            @csrf

            <!-- Bagian 1: Informasi PO -->
            <x-card class="w-full sm:p-8">
                <div class="border-b border-line pb-4">
                    <h2 class="text-lg font-semibold text-ink">Informasi Purchase Order</h2>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <span class="{{ $labelClass }}">Nomor PO (Otomatis)</span>
                        <div class="rounded-input border border-line bg-primary/5 px-3 py-2.5 text-body text-muted">{{ $suggestedOrderNumber }}</div>
                        <p class="mt-1 text-small text-muted">Nomor PO dibuat otomatis saat disimpan.</p>
                    </div>

                    <div>
                        <label for="vendor_id" class="{{ $labelClass }}">Vendor / Supplier <span class="text-danger-text">*</span></label>
                        <select name="vendor_id" id="vendor_id" required class="{{ $inputClass }}">
                            <option value="">-- Pilih Vendor --</option>
                            @foreach ($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('vendor_id')" class="mt-1" />
                    </div>

                    <div>
                        <label for="order_date" class="{{ $labelClass }}">Tanggal Pesan <span class="text-danger-text">*</span></label>
                        <input type="date" name="order_date" id="order_date" value="{{ old('order_date', $today) }}" required class="{{ $inputClass }}">
                        <p class="mt-1 text-small text-muted">Tanggal PO dibuat dan dikirim ke vendor.</p>
                        <x-input-error :messages="$errors->get('order_date')" class="mt-1" />
                    </div>

                    <div>
                        <label for="expected_date" class="{{ $labelClass }}">Tanggal Pengiriman Diharapkan <span class="text-danger-text">*</span></label>
                        <input type="date" name="expected_date" id="expected_date" value="{{ old('expected_date') }}" required class="{{ $inputClass }}">
                        <p class="mt-1 text-small text-muted">Tidak boleh sebelum tanggal pesan.</p>
                        <x-input-error :messages="$errors->get('expected_date')" class="mt-1" />
                    </div>

                    <div class="md:col-span-2">
                        <label for="notes" class="{{ $labelClass }}">Catatan / Instruksi Khusus (Opsional)</label>
                        <textarea name="notes" id="notes" rows="2"
                            placeholder="Contoh: Tolong kirim sebelum jam 08.00 pagi, hubungi Pak Budi saat tiba..."
                            class="{{ $inputClass }}">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </x-card>

            <!-- Bagian 2: Rincian bahan -->
            <x-card class="w-full sm:p-8">
                <div class="flex flex-col gap-3 border-b border-line pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-ink">Daftar Bahan Baku yang Dipesan</h2>
                        <p class="mt-0.5 text-small text-muted">Pilih bahan baku, isi jumlah dan perkiraan harga beli. Subtotal dihitung otomatis.</p>
                    </div>
                    <button type="button" @click="addItem()"
                        class="inline-flex shrink-0 items-center gap-2 self-start rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-primary transition hover:bg-primary/5">
                        <x-icon name="plus" class="h-4 w-4" />
                        Tambah Bahan
                    </button>
                </div>

                <div class="mt-6 space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="rounded-input border border-line bg-primary/5 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <span class="text-label uppercase text-muted" x-text="'Bahan #' + (index + 1)"></span>
                                <button type="button" x-show="items.length > 1" @click="removeItem(index)"
                                    class="text-small font-medium text-danger-text transition hover:underline">Hapus</button>
                            </div>

                            <div class="grid grid-cols-1 items-end gap-4 md:grid-cols-12">
                                <div class="md:col-span-4">
                                    <label class="{{ $labelClass }}">Bahan Baku <span class="text-danger-text">*</span></label>
                                    <select :name="'items[' + index + '][ingredient_id]'"
                                        x-model="item.ingredient_id" @change="onIngredientChange(index)" required
                                        class="{{ $inputClass }}">
                                        <option value="">-- Pilih Bahan Baku --</option>
                                        <template x-for="ing in availableIngredients" :key="ing.id">
                                            <option :value="ing.id" :selected="item.ingredient_id == ing.id"
                                                x-text="ing.name + ' (' + ing.unit + ')'"></option>
                                        </template>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="{{ $labelClass }}">Satuan</label>
                                    <input type="text" x-model="item.unit" readonly placeholder="-"
                                        class="{{ $inputClass }} cursor-not-allowed bg-gray-100 text-muted">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="{{ $labelClass }}">Jumlah <span class="text-danger-text">*</span></label>
                                    <input type="number" :name="'items[' + index + '][quantity]'"
                                        x-model="item.quantity"
                                        @input="
                                                item.quantity = item.quantity?.toString().replace(',', '.');
                                                recalcSubtotal(index)"
                                        :min="getMinForUnit(item.unit)"
                                        :step="getStepForUnit(item.unit)"
                                        required class="{{ $inputClass }}">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="{{ $labelClass }}">Harga / Satuan (Rp) <span class="text-danger-text">*</span></label>
                                    <input type="number" :name="'items[' + index + '][price]'"
                                        x-model.number="item.price" @input="recalcSubtotal(index)"
                                        min="0.01" step="0.01" required placeholder="0"
                                        class="{{ $inputClass }}">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="{{ $labelClass }}">Subtotal</label>
                                    <div class="flex h-[2.625rem] items-center justify-end rounded-input border border-line bg-white px-3 text-body font-semibold text-primary"
                                        x-text="formatRupiah(item.subtotal)"></div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Ringkasan -->
                <div class="mt-6 flex flex-wrap items-center gap-x-8 gap-y-3 border-t border-line pt-6">
                    <div>
                        <div class="text-label uppercase text-muted">Total Jenis Bahan</div>
                        <div class="mt-1 text-body font-medium text-ink" x-text="items.filter(i => i.ingredient_id).length + ' bahan'"></div>
                    </div>
                    <div>
                        <div class="text-label uppercase text-muted">Grand Total Estimasi</div>
                        <div class="mt-1 text-2xl font-semibold text-primary" x-text="formatRupiah(grandTotal())"></div>
                    </div>
                </div>
            </x-card>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3">
                <x-button variant="outline" :href="route('purchase-orders.index')">Batal</x-button>
                <x-button type="submit" id="btn-submit-po">Simpan Purchase Order</x-button>
            </div>
        </form>
    </div>

    {{-- Alpine.js Component Script (tidak diubah) --}}
    <script>
        function purchaseOrderForm(config) {
            const defaultItem = {
                ingredient_id: '',
                quantity: '',
                unit: '',
                price: '',
                subtotal: 0
            };

            const rawOldItems = config.oldItems && Array.isArray(config.oldItems) && config.oldItems.length > 0 ?
                config.oldItems :
                [{
                    ...defaultItem
                }];

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
                    this.items.push({
                        ...defaultItem
                    });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                onIngredientChange(index) {
                    const item = this.items[index];
                    const ingredient = this.availableIngredients.find(ing => ing.id == item.ingredient_id);
                    item.unit = ingredient ? ingredient.unit : '';
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
                    return 'Rp ' + num.toLocaleString('id-ID', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    });
                },

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