<x-app-layout>
    @php
        $inputClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';
        $labelClass = 'mb-1.5 block text-label uppercase text-muted';
    @endphp

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('catering-orders.index') }}" class="text-muted transition hover:text-primary">Daftar Pesanan</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Buat Pesanan</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">Buat Pesanan Katering Baru</h1>
            </div>
            <p class="mt-1 text-small text-muted">Input pelanggan, jadwal acara / pengiriman, dan pilih ragam menu makanan sehat.</p>
        </div>

        <a href="{{ route('catering-orders.index') }}" id="btn-back-to-list"
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
            <p class="mb-2 font-semibold">Terdapat kesalahan pada input formulir:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('catering-orders.store') }}" id="catering-order-form"
              x-data="cateringOrderForm({
                  availableCustomers: @js($customers),
                  availableProducts: @js($products),
                  oldCustomerId: @js(old('customer_id', '')),
                  oldItems: @js(old('items', [['product_id' => '', 'quantity' => 1, 'unit_price' => '', 'notes' => '']]))
              })"
              class="space-y-6">
            @csrf

            <!-- Bagian 1: Pelanggan & Jadwal -->
            <x-card class="w-full sm:p-8">
                <div class="flex flex-col gap-2 border-b border-line pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-lg font-semibold text-ink">Informasi Pelanggan &amp; Jadwal Pesanan</h2>
                    <span class="text-small text-muted">Estimasi No. Pesanan: <span class="font-medium text-ink">{{ $suggestedOrderNumber }}</span></span>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div>
                        <label for="customer_id" class="{{ $labelClass }}">Pilih Pelanggan <span class="text-danger-text">*</span></label>
                        <select name="customer_id" id="customer_id" x-model="selectedCustomerId" required class="{{ $inputClass }}">
                            <option value="">-- Pilih Pelanggan --</option>
                            @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} {{ $customer->phone ? '(' . $customer->phone . ')' : '' }}
                            </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-small text-muted">Data master pelanggan aktif.</p>
                    </div>

                    <div>
                        <label for="order_date" class="{{ $labelClass }}">Tanggal Pemesanan <span class="text-danger-text">*</span></label>
                        <input type="date" name="order_date" id="order_date" value="{{ old('order_date', $today) }}" required class="{{ $inputClass }}">
                        <p class="mt-1 text-small text-muted">Tanggal pesanan dibuat oleh pelanggan.</p>
                    </div>

                    <div>
                        <label for="delivery_date" class="{{ $labelClass }}">Tanggal Pengiriman / Acara <span class="text-danger-text">*</span></label>
                        <input type="date" name="delivery_date" id="delivery_date" value="{{ old('delivery_date', $today) }}" required class="{{ $inputClass }}">
                        <p class="mt-1 text-small text-muted">Tanggal makanan dikirim atau disajikan.</p>
                    </div>

                    <!-- Pratinjau pelanggan -->
                    <template x-if="getSelectedCustomer()">
                        <div class="rounded-input border border-line bg-primary/5 p-4 md:col-span-3">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <span class="text-body font-medium text-ink" x-text="getSelectedCustomer().name"></span>
                                <span class="text-small text-muted" x-text="getSelectedCustomer().phone || 'Tidak ada no telepon'"></span>
                            </div>
                            <p class="mt-1 text-small text-muted" x-text="'Alamat: ' + (getSelectedCustomer().address || 'Belum ada alamat terdaftar')"></p>
                        </div>
                    </template>

                    <div class="md:col-span-3">
                        <label for="notes" class="{{ $labelClass }}">Catatan Pesanan / Instruksi Khusus Acara (Opsional)</label>
                        <textarea name="notes" id="notes" rows="2"
                                  placeholder="Contoh: Pengiriman pukul 10.30 WIB ke Gedung Serbaguna, include sendok & tisu, pisahkan menu vegetarian..."
                                  class="{{ $inputClass }}">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </x-card>

            <!-- Bagian 2: Rincian Menu -->
            <x-card class="w-full sm:p-8">
                <div class="flex flex-col gap-3 border-b border-line pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-ink">Rincian Menu Makanan &amp; Porsi Katering</h2>
                        <p class="mt-0.5 text-small text-muted">Pilih hidangan dan jumlah porsi. Harga satuan dan subtotal terisi otomatis.</p>
                    </div>
                    <button type="button" id="btn-add-menu-top" @click="addItem()"
                            class="inline-flex shrink-0 items-center gap-2 self-start rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-primary transition hover:bg-primary/5">
                        <x-icon name="plus" class="h-4 w-4" />
                        Tambah Menu Lain
                    </button>
                </div>

                <div class="mt-6 space-y-4" id="order-items-container">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="rounded-input border border-line bg-primary/5 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-label uppercase text-muted" x-text="'Baris Menu #' + (index + 1)"></span>
                                    <template x-if="item.product_id">
                                        <span class="text-small text-muted" x-text="getProductDetails(item.product_id)"></span>
                                    </template>
                                </div>

                                <button type="button" x-show="items.length > 1" @click="removeItem(index)"
                                        :id="'btn-remove-item-' + index"
                                        class="text-small font-medium text-danger-text transition hover:underline">Hapus</button>
                            </div>

                            <div class="grid grid-cols-1 items-end gap-4 md:grid-cols-12">
                                <div class="md:col-span-5">
                                    <label class="{{ $labelClass }}">Menu Makanan <span class="text-danger-text">*</span></label>
                                    <select :name="'items[' + index + '][product_id]'" :id="'item_product_' + index"
                                            x-model="item.product_id" @change="onProductChange(index)" required
                                            class="{{ $inputClass }}">
                                        <option value="">-- Pilih Hidangan Makanan --</option>
                                        <template x-for="p in availableProducts" :key="p.id">
                                            <option :value="p.id" :selected="item.product_id == p.id"
                                                    x-text="p.name + ' (' + formatRupiah(p.selling_price) + ' / ' + p.unit + ')'"></option>
                                        </template>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="{{ $labelClass }}">Jumlah Porsi <span class="text-danger-text">*</span></label>
                                    <input type="number" :name="'items[' + index + '][quantity]'" :id="'item_quantity_' + index"
                                           x-model.number="item.quantity" min="1" step="1" required placeholder="1"
                                           class="{{ $inputClass }}">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="{{ $labelClass }}">Harga Satuan (Rp)</label>
                                    <input type="number" :name="'items[' + index + '][unit_price]'" :id="'item_unit_price_' + index"
                                           x-model.number="item.unit_price" min="0" step="500" placeholder="0"
                                           class="{{ $inputClass }}">
                                </div>

                                <div class="md:col-span-3">
                                    <label class="{{ $labelClass }}">Subtotal Baris</label>
                                    <div class="flex h-[2.625rem] items-center justify-end rounded-input border border-line bg-white px-3 text-body font-semibold text-primary"
                                         :id="'item_subtotal_' + index" x-text="formatRupiah(calculateSubtotal(item))"></div>
                                </div>

                                <div class="md:col-span-12">
                                    <input type="text" :name="'items[' + index + '][notes]'" :id="'item_notes_' + index"
                                           x-model="item.notes"
                                           placeholder="Catatan khusus menu ini (contoh: kemasan box terpisah, pedas sedang, tanpa MSG)..."
                                           class="{{ $inputClass }}">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Ringkasan -->
                <div class="mt-6 flex flex-col gap-4 border-t border-line pt-6 md:flex-row md:items-center md:justify-between">
                    <dl class="flex flex-wrap items-center gap-x-8 gap-y-3">
                        <div>
                            <dt class="text-label uppercase text-muted">Ragam Menu</dt>
                            <dd class="mt-1 text-body font-medium text-ink" id="total-menu-count" x-text="totalMenuItems() + ' variasi'"></dd>
                        </div>
                        <div>
                            <dt class="text-label uppercase text-muted">Total Porsi</dt>
                            <dd class="mt-1 text-body font-medium text-ink" id="total-portions-count" x-text="totalPortions() + ' porsi'"></dd>
                        </div>
                        <div>
                            <dt class="text-label uppercase text-muted">Grand Total Tagihan</dt>
                            <dd class="mt-1 text-2xl font-semibold text-primary" id="grand-total-amount" x-text="formatRupiah(calculateGrandTotal())"></dd>
                        </div>
                    </dl>
                </div>
            </x-card>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3">
                <x-button variant="outline" :href="route('catering-orders.index')" id="btn-cancel">Batal</x-button>
                <x-button type="submit" id="btn-submit-order">Simpan &amp; Buat Pesanan</x-button>
            </div>
        </form>
    </div>

    <!-- Alpine.js Component Script (tidak diubah) -->
    <script>
        function cateringOrderForm(config) {
            return {
                availableCustomers: config.availableCustomers || [],
                availableProducts: config.availableProducts || [],
                selectedCustomerId: config.oldCustomerId || '',
                items: (config.oldItems && Array.isArray(config.oldItems) && config.oldItems.length > 0)
                    ? config.oldItems.map(item => ({
                        product_id: item.product_id || '',
                        quantity: item.quantity ? parseInt(item.quantity, 10) : 1,
                        unit_price: item.unit_price !== undefined && item.unit_price !== '' ? Number(item.unit_price) : '',
                        notes: item.notes || ''
                    }))
                    : [{ product_id: '', quantity: 1, unit_price: '', notes: '' }],

                init() {
                    this.items.forEach((item, index) => {
                        if (item.product_id && (item.unit_price === '' || item.unit_price === undefined || item.unit_price === null)) {
                            this.onProductChange(index);
                        }
                    });
                },

                onProductChange(index) {
                    const item = this.items[index];
                    if (!item || !item.product_id) {
                        item.unit_price = '';
                        return;
                    }
                    const product = this.availableProducts.find(p => p.id == item.product_id);
                    if (product) {
                        item.unit_price = parseFloat(product.selling_price) || 0;
                    }
                },

                addItem() {
                    this.items.push({ product_id: '', quantity: 1, unit_price: '', notes: '' });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                calculateSubtotal(item) {
                    const qty = parseInt(item.quantity, 10) || 0;
                    const price = parseFloat(item.unit_price) || 0;
                    return qty * price;
                },

                calculateGrandTotal() {
                    return this.items.reduce((total, item) => total + this.calculateSubtotal(item), 0);
                },

                totalPortions() {
                    return this.items.reduce((total, item) => total + (parseInt(item.quantity, 10) || 0), 0);
                },

                totalMenuItems() {
                    return this.items.filter(item => item.product_id).length;
                },

                getSelectedCustomer() {
                    if (!this.selectedCustomerId) return null;
                    return this.availableCustomers.find(c => c.id == this.selectedCustomerId) || null;
                },

                getProductDetails(productId) {
                    if (!productId) return '';
                    const p = this.availableProducts.find(prod => prod.id == productId);
                    if (!p) return '';
                    const parts = [];
                    if (p.unit) parts.push(p.unit);
                    if (p.calories) parts.push(p.calories + ' kkal');
                    return parts.length > 0 ? parts.join(' | ') : '';
                },

                formatRupiah(amount) {
                    const num = Number(amount) || 0;
                    return 'Rp ' + Math.round(num).toLocaleString('id-ID');
                }
            };
        }
    </script>
</x-app-layout>