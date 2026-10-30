<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Peracik Komposisi Resep (Recipe Builder)') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Tentukan takaran bahan baku mentah yang diperlukan untuk memproduksi 1 {{ $product->unit ?? 'porsi' }} menu <strong>{{ $product->name }}</strong> (FR-08).
                </p>
            </div>
            <a href="{{ route('products.show', $product) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold uppercase tracking-widest rounded-md transition shadow-sm">
                Kembali ke Detail Menu
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="recipeBuilder({
        availableIngredients: @js($availableIngredients),
        initialItems: @js(old('items', $initialItems))
    })">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Error Alert -->
            @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm shadow-sm">
                    <div class="font-semibold mb-1">Terdapat kesalahan pada komposisi resep:</div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Info Singkat Menu Hidangan -->
            <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-white p-6 rounded-lg border border-emerald-200 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-2xl shadow-sm">
                        🍲
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Menu Catering</span>
                        <h3 class="text-xl font-bold text-gray-900">{{ $product->name }}</h3>
                        <p class="text-xs text-gray-600 mt-0.5">
                            Takaran bahan di bawah ini dihitung untuk <strong>1 {{ $product->unit ?? 'porsi' }}</strong> hidangan matang.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-lg border border-emerald-100 shadow-xs">
                    <div class="text-right">
                        <span class="text-[10px] text-gray-400 uppercase font-semibold block">Energi Kalori</span>
                        <span class="text-sm font-bold text-amber-600">🔥 {{ number_format($product->calories, 0) }} kkal</span>
                    </div>
                    <div class="h-6 w-px bg-gray-200"></div>
                    <div class="text-right">
                        <span class="text-[10px] text-gray-400 uppercase font-semibold block">Harga Menu</span>
                        <span class="text-sm font-bold text-gray-900">{{ $product->formattedPrice() }}</span>
                    </div>
                </div>
            </div>

            <!-- Formulir Peracik Resep Dinamis -->
            <form method="POST" action="{{ route('products.recipe.update', $product) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-white p-6 sm:p-8 rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-100 pb-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <span>📋</span> Komposisi Bahan Baku Mentah
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">
                                Tambahkan daftar bahan baku beserta takaran presisi untuk menghasilkan satu porsi hidangan.
                            </p>
                        </div>
                        <div class="text-xs font-medium text-gray-600 bg-gray-100 px-3 py-1.5 rounded-full">
                            Total: <span class="font-bold text-emerald-600" x-text="items.length"></span> jenis bahan
                        </div>
                    </div>

                    <!-- Empty State -->
                    <template x-if="items.length === 0">
                        <div class="p-8 text-center border-2 border-dashed border-gray-200 rounded-lg">
                            <span class="text-3xl block mb-2">🧂</span>
                            <p class="text-sm font-semibold text-gray-700">Belum ada bahan baku dalam resep ini</p>
                            <p class="text-xs text-gray-500 mt-1">Klik tombol di bawah untuk mulai menambahkan bahan baku masakan.</p>
                        </div>
                    </template>

                    <!-- Dynamic Table Rows -->
                    <template x-if="items.length > 0">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="w-12 px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase">#</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bahan Baku Mentah</th>
                                        <th scope="col" class="w-48 px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Takaran (per 1 {{ $product->unit ?? 'porsi' }})</th>
                                        <th scope="col" class="w-36 px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Satuan Takaran</th>
                                        <th scope="col" class="w-16 px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr class="hover:bg-gray-50 transition">
                                            <!-- Index -->
                                            <td class="px-3 py-3 text-center text-xs font-bold text-gray-400" x-text="index + 1"></td>

                                            <!-- Bahan Baku Select -->
                                            <td class="px-4 py-3">
                                                <select :name="`items[${index}][ingredient_id]`" x-model="item.ingredient_id" @change="onIngredientChange(index)" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                                                    <option value="">-- Pilih Bahan Baku --</option>
                                                    <template x-for="ing in availableIngredients" :key="ing.id">
                                                        <option :value="ing.id" :selected="ing.id == item.ingredient_id" x-text="`${ing.name} (Satuan baku: ${ing.unit})`"></option>
                                                    </template>
                                                </select>
                                            </td>

                                            <!-- Jumlah Takaran -->
                                            <td class="px-4 py-3">
                                                <div class="relative rounded-md shadow-sm">
                                                    <input type="number" step="0.001" min="0.001" :name="`items[${index}][quantity_per_unit]`" x-model="item.quantity_per_unit" placeholder="Misal: 150" class="w-full text-sm font-semibold border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                                                </div>
                                            </td>

                                            <!-- Satuan Takaran -->
                                            <td class="px-4 py-3">
                                                <input type="text" :name="`items[${index}][unit]`" x-model="item.unit" placeholder="gram, kg, ml..." class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                                            </td>

                                            <!-- Hapus Baris -->
                                            <td class="px-3 py-3 text-center">
                                                <button type="button" @click="removeItem(index)" class="text-gray-400 hover:text-red-600 transition p-1 rounded hover:bg-red-50" title="Hapus bahan dari resep">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>

                    <!-- Tombol Tambah Baris -->
                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                        <button type="button" @click="addItem()" class="inline-flex items-center px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-md text-xs font-semibold uppercase tracking-wider transition shadow-2xs">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah Bahan Baku
                        </button>
                        <span class="text-xs text-gray-400">Pastikan takaran bahan baku akurat untuk kebutuhan kalkulator bahan produksi.</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('products.show', $product) }}" class="px-5 py-2.5 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition shadow-sm">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition shadow-sm">
                        Simpan Komposisi Resep
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Alpine.js Component Script -->
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
