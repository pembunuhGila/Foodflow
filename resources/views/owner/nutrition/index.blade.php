<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Monitoring & Evaluasi Kepatuhan Gizi Menu') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Pemantauan kandungan nilai nutrisi hidangan katering MBG (Kalori, Makronutrisi & Serat Pangan - UC-11).
                </p>
            </div>
            <div>
                <a href="{{ route('owner.reports.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold uppercase tracking-widest rounded-lg transition shadow-sm">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Pusat Cetak Laporan Eksekutif &rarr;
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Stat Cards Nutrisi Rata-rata Menu --}}
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-medium text-gray-500">Rata-rata Energi</span>
                    <p class="text-xl font-extrabold text-amber-600 mt-1">{{ $avgCalories }} <span class="text-xs font-normal text-gray-400">kkal</span></p>
                    <span class="text-[10px] text-gray-400">Target MBG: 500-750 kkal</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-medium text-gray-500">Rata-rata Protein</span>
                    <p class="text-xl font-extrabold text-blue-600 mt-1">{{ $avgProtein }} <span class="text-xs font-normal text-gray-400">g</span></p>
                    <span class="text-[10px] text-gray-400">Target: 20-30 gram</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-medium text-gray-500">Rata-rata Lemak</span>
                    <p class="text-xl font-extrabold text-rose-600 mt-1">{{ $avgFat }} <span class="text-xs font-normal text-gray-400">g</span></p>
                    <span class="text-[10px] text-gray-400">Target: 15-25 gram</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-medium text-gray-500">Rata-rata Karbohidrat</span>
                    <p class="text-xl font-extrabold text-emerald-600 mt-1">{{ $avgCarbs }} <span class="text-xs font-normal text-gray-400">g</span></p>
                    <span class="text-[10px] text-gray-400">Target: 60-90 gram</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-medium text-gray-500">Rata-rata Serat</span>
                    <p class="text-xl font-extrabold text-teal-600 mt-1">{{ $avgFiber }} <span class="text-xs font-normal text-gray-400">g</span></p>
                    <span class="text-[10px] text-gray-400">Target: min 5 gram</span>
                </div>
            </div>

            {{-- Table Menu & Profil Gizi --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Daftar Menu Hidangan Aktif & Evaluasi Keseimbangan Gizi</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Memastikan hidangan katering sehat memenuhi proporsi gizi seimbang harian.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Menu Hidangan</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Energi (kkal)</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Protein (g)</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Lemak (g)</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Karbo (g)</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Serat (g)</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Kelengkapan Resep</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($products as $index => $product)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-4 py-3 text-sm text-gray-400">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 text-sm font-bold text-gray-900">
                                        {{ $product->name }}
                                        <span class="block text-[11px] font-normal text-gray-500">{{ $product->description ?: 'Menu hidangan reguler' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-amber-700 text-right font-mono">
                                        {{ number_format((float) ($product->calories ?? 0), 1, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-blue-700 text-right font-mono">
                                        {{ number_format((float) ($product->protein ?? 0), 1, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-rose-700 text-right font-mono">
                                        {{ number_format((float) ($product->fat ?? 0), 1, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-emerald-700 text-right font-mono">
                                        {{ number_format((float) ($product->carbohydrates ?? 0), 1, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-teal-700 text-right font-mono">
                                        {{ number_format((float) ($product->dietary_fiber ?? 0), 1, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($product->recipe_items_count > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                Lengkap ({{ $product->recipe_items_count }} Bahan)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                Belum Ada Resep
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-400">
                                        Belum ada menu makanan yang terdaftar di sistem.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
