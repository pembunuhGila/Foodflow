<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Formulir Penyesuaian Stok Fisik (Stock Adjustment)') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Koreksi perbedaan antara saldo sistem dengan hasil stok opname fisik di gudang (FR-26 & BR-03).
                </p>
            </div>
            <div>
                <a href="{{ route('stock-cards.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold uppercase tracking-widest rounded-lg transition shadow-sm">
                    &larr; Kembali ke Buku Kartu Stok
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

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
                $ingredientsMap = $ingredients->keyBy('id')->map(function ($ing) {
                    return [
                        'id' => $ing->id,
                        'name' => $ing->name,
                        'current_stock' => (float) ($ing->current_stock ?? 0),
                        'unit' => $ing->unit,
                    ];
                });
            @endphp

            <div x-data="{
                ingredients: {{ Js::from($ingredientsMap) }},
                selectedId: '{{ old('ingredient_id', $ingredients->first()?->id ?? '') }}',
                physicalCount: '{{ old('physical_count', '') }}',

                get currentIngredient() {
                    return this.ingredients[this.selectedId] || { current_stock: 0, unit: '' };
                },

                get difference() {
                    if (this.physicalCount === '' || isNaN(this.physicalCount)) {
                        return 0;
                    }
                    return (parseFloat(this.physicalCount) - this.currentIngredient.current_stock).toFixed(3);
                }
            }" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-6">

                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg">
                    <p class="text-xs text-amber-900 leading-relaxed">
                        <strong>Aturan Bisnis (BR-03):</strong> Perubahan saldo stok tidak boleh diedit langsung tanpa jejak mutasi. Formulir ini otomatis menerbitkan record transaksi <code>adjustment</code> pada kartu stok dan menyelaraskan saldo fisik gudang.
                    </p>
                </div>

                <form method="POST" action="{{ route('stock-adjustments.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="ingredient_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Pilih Bahan Baku <span class="text-red-500">*</span>
                        </label>
                        <select name="ingredient_id"
                                id="ingredient_id"
                                x-model="selectedId"
                                required
                                class="w-full text-sm rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 font-medium">
                            @foreach ($ingredients as $ing)
                                <option value="{{ $ing->id }}">
                                    {{ $ing->name }} (Saldo Sistem: {{ (float) ($ing->current_stock ?? 0) }} {{ $ing->unit }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Perbandingan Saldo Sistem vs Fisik --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <div>
                            <span class="text-xs text-gray-500">Saldo Tercatat di Sistem:</span>
                            <p class="text-lg font-bold text-gray-800 mt-1">
                                <span x-text="currentIngredient.current_stock"></span>
                                <span class="text-xs font-normal text-gray-500" x-text="currentIngredient.unit"></span>
                            </p>
                        </div>
                        <div>
                            <label for="physical_count" class="block text-xs font-semibold text-gray-700 mb-1">
                                Hasil Hitung Fisik (Opname) <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center space-x-1">
                                <input type="number"
                                       step="0.001"
                                       min="0"
                                       name="physical_count"
                                       id="physical_count"
                                       x-model="physicalCount"
                                       required
                                       placeholder="0.00"
                                       class="w-full text-sm rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 font-bold">
                                <span class="text-xs text-gray-500 font-semibold" x-text="currentIngredient.unit"></span>
                            </div>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Selisih Penyesuaian:</span>
                            <p class="text-lg font-extrabold mt-1"
                               :class="difference > 0 ? 'text-emerald-600' : (difference < 0 ? 'text-rose-600' : 'text-gray-600')">
                                <span x-text="difference > 0 ? `+${difference}` : difference"></span>
                                <span class="text-xs font-normal text-gray-500" x-text="currentIngredient.unit"></span>
                            </p>
                        </div>
                    </div>

                    <div>
                        <label for="adjustment_date" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Tanggal Penyesuaian <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="adjustment_date"
                               id="adjustment_date"
                               value="{{ old('adjustment_date', $today) }}"
                               required
                               class="w-full sm:w-1/2 text-sm rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                    </div>

                    <div>
                        <label for="notes" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Alasan Penyesuaian Stok (Wajib Diisi - FR-26) <span class="text-red-500">*</span>
                        </label>
                        <textarea name="notes"
                                  id="notes"
                                  rows="3"
                                  required
                                  placeholder="Contoh: Susut berat pada sayuran segar, perbedaan timbangan supplier, atau koreksi barang tumpah..."
                                  class="w-full text-sm rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('stock-cards.index') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-lg shadow-sm transition flex items-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Penyesuaian Stok</span>
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</x-app-layout>
