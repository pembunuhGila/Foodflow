<x-app-layout>
    <x-slot name="header">
        <div>
            {{-- Breadcrumb Interaktif --}}
            <nav class="flex items-center gap-2 text-xs font-medium text-gray-400 mb-1.5" aria-label="Breadcrumb">
                <a href="{{ route('production-plans.index') }}" class="hover:text-purple-600 transition-colors">Rencana Produksi</a>
                <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-purple-700 font-semibold">Buat Rencana Baru</span>
            </nav>

            {{-- Judul Halaman --}}
            <h2 class="text-2xl font-bold text-gray-900 leading-tight">
                {{ __('Susun Rencana Produksi & Jadwal Masak') }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">
                Tentukan tanggal masak dan target porsi untuk setiap hidangan katering sehat.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm">
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

                <!-- Card 1: Informasi Jadwal & Waktu Pelaksanaan -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-7 space-y-5">
                    <div class="border-b border-gray-100 pb-4 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold shrink-0 border border-purple-100/60 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">1. Informasi Jadwal &amp; Waktu Pelaksanaan</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Tentukan waktu perencanaan dan pelaksanaan masak di dapur katering.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Tanggal Perencanaan -->
                        <div>
                            <label for="plan_date" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                                Tanggal Perencanaan <span class="text-rose-500">*</span>
                            </label>
                            <input type="date"
                                   name="plan_date"
                                   id="plan_date"
                                   value="{{ old('plan_date', $today) }}"
                                   required
                                   class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-800 focus:bg-white focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all">
                            <p class="text-[11px] text-gray-400 mt-1">Tanggal saat dokumen rencana ini dibuat.</p>
                        </div>

                        <!-- Tanggal Jadwal Masak / Produksi -->
                        <div>
                            <label for="production_date" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                                Tanggal Jadwal Masak / Produksi <span class="text-rose-500">*</span>
                            </label>
                            <input type="date"
                                   name="production_date"
                                   id="production_date"
                                   value="{{ old('production_date', $today) }}"
                                   required
                                   class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-800 focus:bg-white focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all">
                            <p class="text-[11px] text-gray-400 mt-1">Tanggal pelaksanaan masak di dapur katering.</p>
                        </div>

                        <!-- Catatan Operasional -->
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                                Catatan Operasional / Instruksi Khusus (Opsional)
                            </label>
                            <textarea name="notes"
                                      id="notes"
                                      rows="2"
                                      placeholder="Contoh: Shift Pagi, Pesanan Katering Kantor Dinas Kesehatan, packing box ramah lingkungan..."
                                      class="w-full bg-gray-50/50 border border-gray-200 rounded-xl p-3.5 text-sm focus:bg-white focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Daftar Menu Hidangan yang Dimasak -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-7 space-y-4">
                    <div class="border-b border-gray-100 pb-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold shrink-0 border border-purple-100/60 shadow-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900">2. Daftar Menu Hidangan yang Dimasak</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Pilih menu makanan katering dan tentukan jumlah porsi yang harus dimasak.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Menu Rows (Container Kartu Soft) -->
                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="bg-gray-50/40 border border-gray-200/80 rounded-2xl p-4.5 mb-3 transition-all relative">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-[11px] font-bold uppercase text-purple-600 bg-purple-50 border border-purple-100 px-2.5 py-0.5 rounded-md" x-text="'Menu #' + (index + 1)"></span>
                                    <button type="button"
                                            x-show="items.length > 1"
                                            @click="removeItem(index)"
                                            class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-all flex items-center gap-1 text-xs font-semibold"
                                            title="Hapus baris menu ini">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span>Hapus</span>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                                    <!-- Pilih Menu Makanan -->
                                    <div class="md:col-span-6">
                                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">
                                            Menu Makanan <span class="text-rose-500">*</span>
                                        </label>
                                        <select :name="'items[' + index + '][product_id]'"
                                                x-model="item.product_id"
                                                required
                                                class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all">
                                            <option value="">-- Pilih Menu Masakan --</option>
                                            <template x-for="p in availableProducts" :key="p.id">
                                                <option :value="p.id"
                                                        :selected="item.product_id == p.id"
                                                        x-text="p.name + (p.recipe_items_count > 0 ? ' (Resep Siap)' : ' (Belum ada resep)')">
                                                </option>
                                            </template>
                                        </select>

                                        <!-- Resep Status Indicator -->
                                        <div class="mt-2 flex items-center space-x-1" x-show="item.product_id">
                                            <template x-if="getProductRecipeCount(item.product_id) > 0">
                                                <span class="inline-flex items-center text-[11px] text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-md font-medium border border-emerald-200/80">
                                                    ✓ Resep Bahan Siap (<span x-text="getProductRecipeCount(item.product_id)"></span> bahan)
                                                </span>
                                            </template>
                                            <template x-if="getProductRecipeCount(item.product_id) === 0">
                                                <span class="inline-flex items-center text-[11px] text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-md font-medium border border-amber-200/80">
                                                    ⚠️ Resep belum diracik (kebutuhan bahan belum dapat dihitung otomatis)
                                                </span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Target Porsi -->
                                    <div class="md:col-span-3">
                                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">
                                            Target Porsi <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="relative rounded-xl shadow-2xs">
                                            <input type="number"
                                                   :name="'items[' + index + '][planned_quantity]'"
                                                   x-model.number="item.planned_quantity"
                                                   min="1"
                                                   step="1"
                                                   required
                                                   placeholder="50"
                                                   class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-2.5 pr-14 text-sm font-semibold text-gray-800 focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all">
                                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-bold text-gray-400 pointer-events-none">
                                                porsi
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Catatan Khusus Menu -->
                                    <div class="md:col-span-3">
                                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">
                                            Catatan Khusus (Opsional)
                                        </label>
                                        <input type="text"
                                               :name="'items[' + index + '][notes]'"
                                               x-model="item.notes"
                                               placeholder="Contoh: Pisah kuah, rendah garam"
                                               class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Card Summary & Tombol Tambah Baris (Satu Banner Ringkas) -->
                    <div class="bg-purple-50/50 border border-purple-100/80 rounded-2xl p-4.5 flex flex-col sm:flex-row items-center justify-between gap-4 mt-4">
                        <div class="flex items-center gap-6">
                            <div>
                                <span class="text-[11px] text-gray-500 uppercase tracking-wider block font-semibold">Total Ragam Menu</span>
                                <span class="text-lg font-bold text-gray-900" x-text="items.filter(i => i.product_id).length + ' Menu'"></span>
                            </div>
                            <div class="h-8 w-px bg-purple-200/80"></div>
                            <div>
                                <span class="text-[11px] text-gray-500 uppercase tracking-wider block font-semibold">Total Target Masak</span>
                                <span class="text-xl font-extrabold text-purple-700" x-text="totalPlannedQuantity() + ' Porsi'"></span>
                            </div>
                        </div>

                        <button type="button"
                                @click="addItem()"
                                class="px-4 py-2.5 border-2 border-dashed border-purple-300 hover:border-purple-600 text-purple-700 hover:bg-purple-50 font-semibold text-xs rounded-xl transition-all flex items-center gap-2 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>+ Tambah Menu Lain</span>
                        </button>
                    </div>
                </div>

                <!-- Footer Action Buttons (Batal & Simpan) -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('production-plans.index') }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl px-6 py-3 transition-all uppercase tracking-wider">
                        BATAL
                    </a>
                    <button type="submit"
                            class="bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white font-semibold text-xs rounded-xl px-6 py-3 shadow-md shadow-purple-200 transition-all flex items-center gap-2 uppercase tracking-wider">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>SIMPAN &amp; JADWALKAN RENCANA PRODUKSI</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Alpine.js Component Script (Kept Identical & Fully Functional) -->
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
