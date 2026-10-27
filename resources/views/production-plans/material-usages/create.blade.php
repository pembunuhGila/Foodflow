<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Catat Pemakaian Bahan Riil Dapur') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1 font-mono">
                    Rencana Produksi: {{ $plan->plan_code }} &mdash; Tanggal Masak: {{ $plan->plan_date?->format('d/m/Y') }}
                </p>
            </div>
            <div>
                <a href="{{ route('production-plans.show', $plan) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold uppercase tracking-widest rounded-lg transition shadow-sm">
                    &larr; Kembali ke Rencana
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
                        <h3 class="text-sm font-semibold text-red-800">Terdapat kesalahan pada input:</h3>
                    </div>
                    <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-6">

                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                    <p class="text-xs text-blue-900 leading-relaxed">
                        <strong>Panduan Staf Dapur (FR-35):</strong> Bandingkan takaran standar resep (kebutuhan rencana) dengan jumlah bahan baku yang benar-benar habis dipakai saat memasak. Masukkan catatan jika ada bahan tumpah, susut, atau sisa layak simpan.
                    </p>
                </div>

                <form method="POST" action="{{ route('production-plans.material-usages.store', $plan) }}" class="space-y-6">
                    @csrf

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bahan Baku</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Kebutuhan Rencana</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Pemakaian Riil <span class="text-red-500">*</span></th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Satuan</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Catatan Dapur</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse ($items as $index => $item)
                                    <tr class="hover:bg-gray-50/70 transition">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                            {{ $item['ingredient_name'] }}
                                            <input type="hidden" name="items[{{ $index }}][ingredient_id]" value="{{ $item['ingredient_id'] }}">
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600 text-right font-mono">
                                            {{ number_format((float) $item['planned_quantity'], 3, ',', '.') }}
                                            <input type="hidden" name="items[{{ $index }}][planned_quantity]" value="{{ $item['planned_quantity'] }}">
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <input type="number"
                                                   step="0.001"
                                                   min="0"
                                                   name="items[{{ $index }}][actual_quantity]"
                                                   value="{{ old("items.{$index}.actual_quantity", $item['actual_quantity']) }}"
                                                   required
                                                   class="w-32 text-xs rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-right font-semibold">
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $item['unit'] }}
                                            <input type="hidden" name="items[{{ $index }}][unit]" value="{{ $item['unit'] }}">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text"
                                                   name="items[{{ $index }}][notes]"
                                                   value="{{ old("items.{$index}.notes", $item['notes']) }}"
                                                   placeholder="Misal: Tumpah 100gr saat menimbang..."
                                                   class="w-full text-xs rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">
                                            Tidak ada komposisi resep bahan baku yang terdaftar pada menu rencana produksi ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if (!empty($items))
                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                            <a href="{{ route('production-plans.show', $plan) }}"
                               class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                                Batal
                            </a>
                            <button type="submit"
                                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition flex items-center space-x-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Simpan Realisasi Pemakaian Bahan</span>
                            </button>
                        </div>
                    @endif

                </form>

            </div>

        </div>
    </div>
</x-app-layout>
