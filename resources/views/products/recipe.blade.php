<x-app-layout>
    <x-slot name="header">
        <div>
            {{-- Breadcrumb Interaktif --}}
            <nav class="flex items-center gap-2 text-xs font-medium text-gray-400 mb-1.5" aria-label="Breadcrumb">
                <a href="{{ route('products.index') }}" class="hover:text-purple-600 transition-colors">Daftar Menu</a>
                <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('products.show', $product) }}" class="hover:text-purple-600 transition-colors">Detail Menu</a>
                <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-purple-700 font-semibold">Peracik Resep</span>
            </nav>

            {{-- Judul Halaman --}}
            <h2 class="font-bold text-xl sm:text-2xl text-gray-900 leading-tight">
                {{ __('Peracik Komposisi Resep (Recipe Builder)') }}
            </h2>
            <p class="text-xs text-gray-500 mt-1">
                Tentukan takaran bahan baku mentah yang diperlukan untuk memproduksi 1 {{ $product->unit ?? 'porsi' }} menu <strong>{{ $product->name }}</strong>.
            </p>
        </div>
    </x-slot>

    <div class="py-8" x-data="recipeBuilder({
        availableIngredients: @js($availableIngredients),
        initialItems: @js(old('items', $initialItems))
    })">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Error Alert -->
            @if ($errors->any())
                <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-r-xl shadow-sm">
                    <div class="font-semibold text-sm mb-1">Terdapat kesalahan pada formulir resep:</div>
                    <ul class="list-disc list-inside space-y-0.5 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Card Ringkasan Menu Catering (Top Info Banner) -->
            <div class="bg-white rounded-2xl border border-purple-100 shadow-sm p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center shrink-0 border border-purple-100/60 shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">MENU CATERING</span>
                        <h3 class="text-xl font-bold text-gray-900 tracking-tight">{{ $product->name }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Takaran bahan di bawah ini dihitung untuk 1 {{ $product->unit ?? 'porsi' }} hidangan matang.
                        </p>
                    </div>
                </div>

                {{-- Quick Stats Badges --}}
                <div class="flex items-center gap-2.5 shrink-0">
                    <span class="bg-amber-50 text-amber-700 border border-amber-100 px-3 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-1.5 shadow-2xs">
                        <span>🔥</span>
                        <span>{{ number_format((float) ($product->calories ?? 0), 0) }} kkal</span>
                    </span>
                    <span class="bg-purple-50 text-purple-700 border border-purple-100 px-3 py-1.5 rounded-xl text-xs font-semibold shadow-2xs">
                        {{ $product->formattedPrice() }} / {{ $product->unit ?? 'porsi' }}
                    </span>
                </div>
            </div>

            <!-- Card Utama Form Komposisi Resep -->
            <form method="POST" action="{{ route('products.recipe.update', $product) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-7">
                    {{-- Header Form --}}
                    <div class="border-b border-gray-100 pb-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Komposisi Bahan Baku Mentah</h3>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Tambahkan daftar bahan baku beserta takaran presisi untuk menghasilkan satu porsi hidangan.
                            </p>
                        </div>
                        <div class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-semibold shrink-0 self-start sm:self-center">
                            Total: <span class="font-bold text-purple-700" x-text="items.length"></span> jenis bahan
                        </div>
                    </div>

                    <!-- Empty State -->
                    <template x-if="items.length === 0">
                        <div class="p-10 text-center border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/40">
                            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-gray-800">Belum ada bahan baku dalam resep ini</p>
                            <p class="text-xs text-gray-500 mt-1">Klik tombol "+ Tambah Bahan Baku" di bawah untuk mulai menyusun resep masakan.</p>
                        </div>
                    </template>

                    <!-- Dynamic Table Rows -->
                    <template x-if="items.length > 0">
                        <div class="overflow-x-auto rounded-xl border border-gray-200/80">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50/70">
                                    <tr>
                                        <th scope="col" class="w-12 px-3 py-3 text-center text-xs uppercase text-gray-400 font-bold tracking-wider">#</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs uppercase text-gray-400 font-bold tracking-wider">Bahan Baku Mentah</th>
                                        <th scope="col" class="w-48 px-4 py-3 text-center text-xs uppercase text-gray-400 font-bold tracking-wider">Takaran (per 1 {{ $product->unit ?? 'porsi' }})</th>
                                        <th scope="col" class="w-36 px-4 py-3 text-center text-xs uppercase text-gray-400 font-bold tracking-wider">Satuan Takaran</th>
                                        <th scope="col" class="w-16 px-3 py-3 text-center text-xs uppercase text-gray-400 font-bold tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr class="hover:bg-purple-50/20 transition">
                                            <!-- Index -->
                                            <td class="px-3 py-3.5 text-center text-xs font-bold text-gray-400" x-text="index + 1"></td>

                                            <!-- Bahan Baku Select -->
                                            <td class="px-4 py-3.5">
                                                <select :name="`items[${index}][ingredient_id]`" x-model="item.ingredient_id" @change="onIngredientChange(index)" class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:bg-white focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all" required>
                                                    <option value="">-- Pilih Bahan Baku Mentah --</option>
                                                    <template x-for="ing in availableIngredients" :key="ing.id">
                                                        <option :value="ing.id" :selected="ing.id == item.ingredient_id" x-text="`${ing.name} (Satuan gudang: ${ing.unit})`"></option>
                                                    </template>
                                                </select>
                                            </td>

                                            <!-- Jumlah Takaran Input -->
                                            <td class="px-4 py-3.5">
                                                <input type="number" step="0.001" min="0.001" :name="`items[${index}][quantity_per_unit]`" x-model="item.quantity_per_unit" placeholder="Misal: 150" class="w-full text-center font-bold text-purple-700 bg-gray-50/50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:bg-white focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all" required>
                                            </td>

                                            <!-- Satuan Takaran Input -->
                                            <td class="px-4 py-3.5">
                                                <input type="text" :name="`items[${index}][unit]`" x-model="item.unit" placeholder="gram, kg, ml..." class="w-full bg-gray-100/70 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-semibold text-gray-600 text-center focus:bg-white focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all" required>
                                            </td>

                                            <!-- Hapus Baris Button -->
                                            <td class="px-3 py-3.5 text-center">
                                                <button type="button" @click="removeItem(index)" class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-all" title="Hapus bahan dari resep">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>

                    <!-- Tombol Tambah Baris & Catatan -->
                    <div class="mt-5 pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <button type="button" @click="addItem()" class="px-4 py-2.5 border-2 border-dashed border-purple-300 hover:border-purple-600 text-purple-700 hover:bg-purple-50 font-semibold text-xs rounded-xl transition-all flex items-center gap-2 self-start sm:self-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>+ TAMBAH BAHAN BAKU</span>
                        </button>
                        <span class="text-xs text-gray-400">💡 Pastikan takaran bahan baku akurat untuk kebutuhan kalkulator bahan produksi.</span>
                    </div>
                </div>

                <!-- Footer Action Buttons (Batal & Simpan) -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('products.show', $product) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl px-6 py-3 transition-all uppercase tracking-wider">
                        BATAL
                    </a>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white font-semibold text-xs rounded-xl px-6 py-3 shadow-md shadow-purple-200 transition-all uppercase tracking-wider">
                        SIMPAN KOMPOSISI RESEP
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Alpine.js Component Script (Kept Identical & Intact) -->
    <script>
        function recipeBuilder(config) {
            return {
                availableIngredients: config.availableIngredients || [],
                items: (config.initialItems && config.initialItems.length > 0)
                    ? config.initialItems
                    : [],
                addItem() {
                    this.items.push({
                        ingredient_id: '',
                        quantity_per_unit: '',
                        unit: 'gram'
                    });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                },
                onIngredientChange(index) {
                    const selectedId = this.items[index].ingredient_id;
                    const found = this.availableIngredients.find(ing => ing.id == selectedId);
                    if (found && (!this.items[index].unit || this.items[index].unit === 'gram')) {
                        this.items[index].unit = found.unit || 'gram';
                    }
                }
            };
        }
    </script>
</x-app-layout>
