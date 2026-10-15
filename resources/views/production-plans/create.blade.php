<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Susun Rencana Produksi & Jadwal Masak') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Tentukan tanggal masak dan target porsi untuk setiap hidangan katering sehat.
                </p>
            </div>
            <div>
                <a href="{{ route('production-plans.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold uppercase tracking-widest rounded-lg transition shadow-sm">
                    &larr; Kembali ke Daftar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Validation Errors -->
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
                  action="{{ route('production-plans.store') }}"
                  x-data="productionPlanForm({
                      availableProducts: @js($products),
                      oldItems: @js(old('items', [['product_id' => '', 'planned_quantity' => 50, 'notes' => '']]))
                  })"
                  class="space-y-6">
                @csrf

                <!-- Card 1: Informasi Jadwal Produksi -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                    <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-2 flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Informasi Jadwal & Waktu Pelaksanaan</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="plan_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Perencanaan <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   name="plan_date"
                                   id="plan_date"
                                   value="{{ old('plan_date', $today) }}"
                                   required
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                            <p class="text-[11px] text-gray-400 mt-1">Tanggal saat dokumen rencana ini dibuat.</p>
                        </div>

                        <div>
                            <label for="production_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Jadwal Masak / Produksi <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   name="production_date"
                                   id="production_date"
                                   value="{{ old('production_date', $today) }}"
                                   required
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                            <p class="text-[11px] text-gray-400 mt-1">Tanggal pelaksanaan masak di dapur katering.</p>
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                            Catatan Operasional / Instruksi Khusus (Opsional)
                        </label>
                        <textarea name="notes"
                                  id="notes"
                                  rows="2"
                                  placeholder="Contoh: Shift Pagi, Pesanan Katering Kantor Dinas Kesehatan, packing box ramah lingkungan..."
                                  class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Card 2: Rincian Menu & Target Porsi -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 flex items-center space-x-2">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <span>Daftar Menu Hidangan yang Dimasak</span>
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">Pilih menu makanan katering dan tentukan jumlah porsi yang harus dimasak.</p>
                        </div>

                        <button type="button"
                                @click="addItem()"
                                class="inline-flex items-center text-xs font-semibold px-3 py-1.5 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-xs">
                            <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Menu Lain
                        </button>
                    </div>

                    <!-- Dynamic Menu Rows -->
                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="p-4 rounded-lg border border-gray-200 bg-gray-50/50 hover:bg-white hover:border-emerald-300 transition space-y-3">
                                <div class="flex items-start justify-between">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider" x-text="'Menu #' + (index + 1)"></span>
                                    <button type="button"
                                            x-show="items.length > 1"
                                            @click="removeItem(index)"
                                            class="text-xs text-red-600 hover:text-red-800 font-medium transition"
                                            title="Hapus baris ini">
                                        &times; Hapus Baris
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                                    <!-- Pilih Menu Makanan -->
                                    <div class="md:col-span-6">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                            Menu Makanan <span class="text-red-500">*</span>
                                        </label>
                                        <select :name="'items[' + index + '][product_id]'"
                                                x-model="item.product_id"
                                                required
                                                class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                                            <option value="">-- Pilih Menu Masakan --</option>
                                            <template x-for="p in availableProducts" :key="p.id">
                                                <option :value="p.id"
                                                        :selected="item.product_id == p.id"
                                                        x-text="p.name + (p.recipe_items_count > 0 ? ' (Resep Siap)' : ' (Belum ada resep)')">
                                                </option>
                                            </template>
                                        </select>

                                        <!-- Resep Status Indicator -->
                                        <div class="mt-1.5 flex items-center space-x-1" x-show="item.product_id">
                                            <template x-if="getProductRecipeCount(item.product_id) > 0">
                                                <span class="inline-flex items-center text-[11px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-medium border border-emerald-200">
                                                    ✓ Resep Bahan Siap (<span x-text="getProductRecipeCount(item.product_id)"></span> bahan)
                                                </span>
                                            </template>
                                            <template x-if="getProductRecipeCount(item.product_id) === 0">
                                                <span class="inline-flex items-center text-[11px] text-amber-700 bg-amber-50 px-2 py-0.5 rounded font-medium border border-amber-200">
                                                    ⚠️ Resep belum diracik (kebutuhan bahan belum dapat dihitung otomatis)
                                                </span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Target Porsi -->
                                    <div class="md:col-span-3">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                            Target Porsi <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="number"
                                                   :name="'items[' + index + '][planned_quantity]'"
                                                   x-model.number="item.planned_quantity"
                                                   min="1"
                                                   step="1"
                                                   required
                                                   placeholder="50"
                                                   class="w-full text-sm rounded-lg border-gray-300 pr-14 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-gray-400 pointer-events-none">
                                                porsi
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Catatan Khusus Menu -->
                                    <div class="md:col-span-3">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                            Catatan Khusus (Opsional)
                                        </label>
                                        <input type="text"
                                               :name="'items[' + index + '][notes]'"
                                               x-model="item.notes"
                                               placeholder="Contoh: Pisah kuah, rendah garam"
                                               class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Ringkasan Rencana & Tombol Tambah Baris -->
                    <div class="p-4 rounded-lg bg-gray-50 border border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center space-x-4">
                            <div>
                                <span class="text-xs text-gray-500 uppercase tracking-wider block">Total Ragam Menu</span>
                                <span class="text-lg font-bold text-gray-800" x-text="items.filter(i => i.product_id).length + ' Menu'"></span>
                            </div>
                            <div class="h-8 w-px bg-gray-300"></div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase tracking-wider block">Total Target Masak</span>
                                <span class="text-xl font-extrabold text-emerald-700" x-text="totalPlannedQuantity() + ' Porsi'"></span>
                            </div>
                        </div>

                        <button type="button"
                                @click="addItem()"
                                class="inline-flex items-center text-xs font-semibold px-3 py-1.5 rounded-lg bg-white text-emerald-700 hover:bg-emerald-50 border border-emerald-300 transition shadow-2xs">
                            <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>+ Tambah Baris Menu</span>
                        </button>
                    </div>
                </div>

                <!-- Submit Button Bar -->
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <a href="{{ route('production-plans.index') }}"
                       class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg font-semibold text-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition shadow-sm flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan & Jadwalkan Rencana Produksi</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Alpine.js Component Script (Loaded Directly in Body) -->
    <script>
        function productionPlanForm(config) {
            return {
                availableProducts: config.availableProducts || [],
                items: (config.oldItems && Array.isArray(config.oldItems) && config.oldItems.length > 0)
                    ? config.oldItems
                    : [{ product_id: '', planned_quantity: 50, notes: '' }],
                addItem() {
                    this.items.push({
                        product_id: '',
                        planned_quantity: 50,
                        notes: ''
                    });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                getProductRecipeCount(productId) {
                    if (!productId) return 0;
                    const p = this.availableProducts.find(item => item.id == productId);
                    return p ? (p.recipe_items_count || 0) : 0;
                },
                totalPlannedQuantity() {
                    return this.items.reduce((total, item) => {
                        const qty = parseInt(item.planned_quantity, 10);
                        return total + (isNaN(qty) ? 0 : qty);
                    }, 0);
                }
            };
        }
    </script>
</x-app-layout>
