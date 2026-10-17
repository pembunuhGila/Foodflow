<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                        FR-14 & UC-07
                    </span>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Buat Pesanan Katering Baru') }}
                    </h2>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Input rincian pemesanan pelanggan, jadwal acara / pengiriman, dan pilih ragam menu makanan sehat.
                </p>
            </div>
            <div>
                <a href="{{ route('catering-orders.index') }}"
                   id="btn-back-to-list"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold uppercase tracking-widest rounded-lg transition shadow-sm">
                    &larr; Kembali ke Daftar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Validation Errors Banner -->
            @if ($errors->any())
                <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm" role="alert">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 me-2 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-semibold text-red-800">Terdapat kesalahan pada input formulir:</h3>
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
                  action="{{ route('catering-orders.store') }}"
                  id="catering-order-form"
                  x-data="cateringOrderForm({
                      availableCustomers: @js($customers),
                      availableProducts: @js($products),
                      oldCustomerId: @js(old('customer_id', '')),
                      oldItems: @js(old('items', [['product_id' => '', 'quantity' => 1, 'unit_price' => '', 'notes' => '']]))
                  })"
                  class="space-y-6">
                @csrf

                <!-- Card 1: Informasi Pelanggan & Jadwal Pemesanan -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 pb-3 gap-2">
                        <h3 class="text-base font-semibold text-gray-900 flex items-center space-x-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Informasi Pelanggan & Jadwal Pesanan</span>
                        </h3>
                        <span class="inline-flex items-center text-xs font-mono font-medium px-2.5 py-1 rounded bg-gray-100 text-gray-700">
                            Estimasi No. Pesanan: <strong class="ml-1 text-emerald-700">{{ $suggestedOrderNumber }}</strong>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Pelanggan -->
                        <div class="md:col-span-1">
                            <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Pilih Pelanggan <span class="text-red-500">*</span>
                            </label>
                            <select name="customer_id"
                                    id="customer_id"
                                    x-model="selectedCustomerId"
                                    required
                                    class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                                <option value="">-- Pilih Pelanggan --</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} {{ $customer->phone ? '('.$customer->phone.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-gray-400 mt-1">Data master pelanggan aktif.</p>
                        </div>

                        <!-- Tanggal Pemesanan -->
                        <div>
                            <label for="order_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Pemesanan <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   name="order_date"
                                   id="order_date"
                                   value="{{ old('order_date', $today) }}"
                                   required
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                            <p class="text-[11px] text-gray-400 mt-1">Tanggal saat pesanan dibuat oleh pelanggan.</p>
                        </div>

                        <!-- Tanggal Pengiriman / Acara -->
                        <div>
                            <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Pengiriman / Acara <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   name="delivery_date"
                                   id="delivery_date"
                                   value="{{ old('delivery_date', $today) }}"
                                   required
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                            <p class="text-[11px] text-gray-400 mt-1">Tanggal makanan dikirim atau disajikan.</p>
                        </div>
                    </div>

                    <!-- Reactive Customer Preview Box -->
                    <template x-if="getSelectedCustomer()">
                        <div class="p-3 bg-emerald-50/60 border border-emerald-200 rounded-lg text-xs space-y-1 text-emerald-950">
                            <div class="flex items-center justify-between font-semibold">
                                <span class="flex items-center space-x-1.5">
                                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span x-text="getSelectedCustomer().name"></span>
                                </span>
                                <span class="text-emerald-700 font-normal" x-text="getSelectedCustomer().phone || 'Tidak ada no telepon'"></span>
                            </div>
                            <p class="text-emerald-800" x-text="'Alamat: ' + (getSelectedCustomer().address || 'Belum ada alamat terdaftar')"></p>
                        </div>
                    </template>

                    <!-- Catatan Pesanan -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                            Catatan Pesanan / Instruksi Khusus Acara (Opsional)
                        </label>
                        <textarea name="notes"
                                  id="notes"
                                  rows="2"
                                  placeholder="Contoh: Pengiriman pukul 10.30 WIB ke Gedung Serbaguna, include sendok & tisu, pisahkan menu vegetarian..."
                                  class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Card 2: Baris Menu Dinamis (Alpine.js) -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 flex items-center space-x-2">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <span>Rincian Menu Makanan & Porsi Katering</span>
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Pilih hidangan, tentukan jumlah porsi. Harga satuan dan kalkulasi subtotal akan terisi secara otomatis.
                            </p>
                        </div>

                        <button type="button"
                                id="btn-add-menu-top"
                                @click="addItem()"
                                class="inline-flex items-center text-xs font-semibold px-3 py-1.5 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-xs">
                            <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Menu Lain
                        </button>
                    </div>

                    <!-- Dynamic Menu Rows Container -->
                    <div class="space-y-4" id="order-items-container">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/60 hover:bg-white hover:border-emerald-300 transition-all space-y-3 shadow-2xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-gray-200 text-gray-700"
                                              x-text="'Baris Menu #' + (index + 1)"></span>
                                        <template x-if="item.product_id">
                                            <span class="text-xs text-emerald-700 font-medium" x-text="getProductDetails(item.product_id)"></span>
                                        </template>
                                    </div>

                                    <button type="button"
                                            x-show="items.length > 1"
                                            @click="removeItem(index)"
                                            :id="'btn-remove-item-' + index"
                                            class="text-xs text-red-600 hover:text-red-800 font-semibold transition flex items-center space-x-1"
                                            title="Hapus baris menu ini">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span>Hapus</span>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                    <!-- 1. Pilih Menu Makanan (Col 5) -->
                                    <div class="md:col-span-5">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                                            Menu Makanan <span class="text-red-500">*</span>
                                        </label>
                                        <select :name="'items[' + index + '][product_id]'"
                                                :id="'item_product_' + index"
                                                x-model="item.product_id"
                                                @change="onProductChange(index)"
                                                required
                                                class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                                            <option value="">-- Pilih Hidangan Makanan --</option>
                                            <template x-for="p in availableProducts" :key="p.id">
                                                <option :value="p.id"
                                                        :selected="item.product_id == p.id"
                                                        x-text="p.name + ' (' + formatRupiah(p.selling_price) + ' / ' + p.unit + ')'">
                                                </option>
                                            </template>
                                        </select>
                                    </div>

                                    <!-- 2. Jumlah Porsi (Col 2) -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                                            Jumlah Porsi <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="number"
                                                   :name="'items[' + index + '][quantity]'"
                                                   :id="'item_quantity_' + index"
                                                   x-model.number="item.quantity"
                                                   min="1"
                                                   step="1"
                                                   required
                                                   placeholder="1"
                                                   class="w-full text-sm rounded-lg border-gray-300 pr-12 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-gray-400 pointer-events-none">
                                                porsi
                                            </span>
                                        </div>
                                    </div>

                                    <!-- 3. Harga Satuan (Col 2) -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                                            Harga Satuan (Rp)
                                        </label>
                                        <div class="relative">
                                            <input type="number"
                                                   :name="'items[' + index + '][unit_price]'"
                                                   :id="'item_unit_price_' + index"
                                                   x-model.number="item.unit_price"
                                                   min="0"
                                                   step="500"
                                                   placeholder="0"
                                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                                        </div>
                                    </div>

                                    <!-- 4. Real-time Subtotal Display (Col 3) -->
                                    <div class="md:col-span-3">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                                            Subtotal Baris
                                        </label>
                                        <div class="h-10 px-3 py-2 bg-emerald-50/80 border border-emerald-200 rounded-lg flex items-center justify-between text-emerald-900">
                                            <span class="text-xs text-emerald-700">Subtotal:</span>
                                            <span class="text-sm font-bold"
                                                  :id="'item_subtotal_' + index"
                                                  x-text="formatRupiah(calculateSubtotal(item))">
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Baris Tambahan: Catatan Khusus Item -->
                                <div class="pt-1">
                                    <input type="text"
                                           :name="'items[' + index + '][notes]'"
                                           :id="'item_notes_' + index"
                                           x-model="item.notes"
                                           placeholder="Catatan khusus menu ini (contoh: 'Kemasan box bento terpisah', 'Pedas sedang', 'Tanpa MSG')..."
                                           class="w-full text-xs rounded-lg border-gray-200 bg-white placeholder-gray-400 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Summary & Kalkulasi Grand Total Real-time -->
                    <div class="p-5 rounded-xl bg-gradient-to-r from-gray-50 to-emerald-50/30 border border-emerald-200/80 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-6">
                            <div>
                                <span class="text-xs text-gray-500 uppercase tracking-wider block">Ragam Menu</span>
                                <span class="text-base font-bold text-gray-800"
                                      id="total-menu-count"
                                      x-text="totalMenuItems() + ' Variasi'"></span>
                            </div>
                            <div class="h-8 w-px bg-gray-300 hidden sm:block"></div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase tracking-wider block">Total Porsi</span>
                                <span class="text-base font-bold text-gray-800"
                                      id="total-portions-count"
                                      x-text="totalPortions() + ' Porsi'"></span>
                            </div>
                            <div class="h-8 w-px bg-gray-300 hidden sm:block"></div>
                            <div>
                                <span class="text-xs text-emerald-800 uppercase font-semibold tracking-wider block">Grand Total Tagihan</span>
                                <span class="text-2xl font-extrabold text-emerald-700"
                                      id="grand-total-amount"
                                      x-text="formatRupiah(calculateGrandTotal())"></span>
                            </div>
                        </div>

                        <button type="button"
                                id="btn-add-menu-bottom"
                                @click="addItem()"
                                class="inline-flex items-center text-xs font-semibold px-3.5 py-2 rounded-lg bg-white text-emerald-700 hover:bg-emerald-50 border border-emerald-300 transition shadow-2xs">
                            <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>+ Tambah Baris Menu</span>
                        </button>
                    </div>
                </div>

                <!-- Action Button Bar -->
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <a href="{{ route('catering-orders.index') }}"
                       id="btn-cancel"
                       class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-2xs">
                        Batal
                    </a>
                    <button type="submit"
                            id="btn-submit-order"
                            class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg font-semibold text-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition shadow-sm flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan & Buat Pesanan Katering</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Alpine.js Component Script -->
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
                    // Auto-fill unit_price for preselected products without price
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
                    this.items.push({
                        product_id: '',
                        quantity: 1,
                        unit_price: '',
                        notes: ''
                    });
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
                    return this.items.reduce((total, item) => {
                        return total + this.calculateSubtotal(item);
                    }, 0);
                },

                totalPortions() {
                    return this.items.reduce((total, item) => {
                        const qty = parseInt(item.quantity, 10) || 0;
                        return total + qty;
                    }, 0);
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
                    return parts.length > 0 ? '• ' + parts.join(' | ') : '';
                },

                formatRupiah(amount) {
                    const num = Number(amount) || 0;
                    return 'Rp ' + Math.round(num).toLocaleString('id-ID');
                }
            };
        }
    </script>
</x-app-layout>
