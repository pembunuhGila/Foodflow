<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('production-plans.index') }}" class="text-gray-400 hover:text-gray-600 transition text-sm">
                        &larr; Kembali ke Daftar
                    </a>
                </div>
                <div class="flex items-center space-x-3 mt-1">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Rencana Produksi: <span class="font-mono text-emerald-700">{{ $plan->plan_number }}</span>
                    </h2>
                    @php
                        $statusEnum = $plan->status instanceof \App\Enums\ProductionPlanStatus 
                            ? $plan->status 
                            : \App\Enums\ProductionPlanStatus::tryFrom($plan->status);
                    @endphp
                    @if ($statusEnum)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $statusEnum->badgeClass() }}">
                            {{ $statusEnum->label() }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center space-x-2">
                @if (in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                    <a href="{{ route('production-plans.edit', $plan) }}"
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition shadow-sm">
                        <svg class="w-4 h-4 me-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Rencana
                    </a>

                    <form method="POST"
                          action="{{ route('production-plans.destroy', $plan) }}"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus rencana produksi {{ $plan->plan_number }}?');"
                          class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-50 border border-red-200 rounded-lg text-xs font-semibold text-red-700 uppercase tracking-widest hover:bg-red-100 transition shadow-sm">
                            <svg class="w-4 h-4 me-1.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alert -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Warning jika ada menu yang belum memiliki resep (UC-14 Alt Flow 6a) -->
            @php
                $missingRecipes = $plan->details->filter(fn ($detail) => $detail->product && $detail->product->recipeItems->isEmpty());
            @endphp
            @if ($missingRecipes->isNotEmpty())
                <div class="p-4 bg-amber-50 border-l-4 border-amber-500 rounded-r-lg shadow-sm">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-amber-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <div class="text-xs text-amber-900 space-y-1">
                            <div class="font-bold text-sm">Peringatan: Komposisi Resep Belum Lengkap (UC-14 Alt 6a)</div>
                            <p>
                                Terdapat <strong>{{ $missingRecipes->count() }} menu</strong> dalam rencana produksi ini yang belum memiliki racikan resep bahan baku mentah. Kebutuhan bahan baku dapur belum dapat dihitung otomatis untuk menu tersebut.
                            </p>
                            <div class="pt-1 flex flex-wrap gap-2">
                                @foreach ($missingRecipes as $missing)
                                    @if ($missing->product)
                                        <a href="{{ route('products.recipe.edit', $missing->product) }}"
                                           class="inline-flex items-center text-xs font-semibold px-2.5 py-1 bg-amber-100 text-amber-800 rounded border border-amber-300 hover:bg-amber-200 transition">
                                            ⚙️ Racik Resep: {{ $missing->product->name }} &rarr;
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Ringkasan Rencana Header Info -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Tanggal Perencanaan</span>
                    <span class="text-base font-bold text-gray-800 mt-1 block">
                        {{ $plan->plan_date ? $plan->plan_date->translatedFormat('d F Y') : '-' }}
                    </span>
                    <span class="text-[11px] text-gray-400">Dibuat: {{ $plan->created_at ? $plan->created_at->diffForHumans() : '-' }}</span>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Jadwal Masak / Dapur</span>
                    <span class="text-base font-bold text-emerald-700 mt-1 block">
                        {{ $plan->production_date ? $plan->production_date->translatedFormat('l, d F Y') : '-' }}
                    </span>
                    <span class="text-[11px] text-gray-400">Target penyelesaian</span>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Total Target Produksi</span>
                    <span class="text-2xl font-extrabold text-emerald-800 mt-1 block">
                        {{ $plan->totalPlannedQuantity() }}
                        <span class="text-sm font-normal text-gray-500">Porsi</span>
                    </span>
                    <span class="text-[11px] text-gray-400">{{ $plan->details->count() }} variasi menu masakan</span>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Penanggung Jawab</span>
                    <span class="text-base font-bold text-gray-800 mt-1 block">
                        {{ $plan->creator?->name ?? 'Sistem' }}
                    </span>
                    <span class="text-[11px] text-gray-400">Role: {{ $plan->creator?->role?->value ?? '-' }}</span>
                </div>
            </div>

            <!-- Catatan Operasional -->
            @if ($plan->notes)
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Catatan Operasional & Instruksi Khusus</h4>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $plan->notes }}</p>
                </div>
            @endif

            <!-- Tabel Detail Menu Rencana Produksi -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-900 text-base">Rincian Menu Hidangan & Target Porsi</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Daftar masakan yang harus diproduksi oleh tim dapur.</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded bg-gray-100 text-gray-700">
                        {{ $plan->details->count() }} Menu Terdaftar
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-600">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left">No.</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Menu Makanan</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Status Resep Bahan</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Target Porsi</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Catatan Khusus</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Aksi Menu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($plan->details as $index => $detail)
                                <tr class="hover:bg-gray-50/75 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400 font-mono">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($detail->product)
                                            <a href="{{ route('products.show', $detail->product) }}" class="font-bold text-gray-900 hover:text-emerald-700 hover:underline">
                                                {{ $detail->product->name }}
                                            </a>
                                            <div class="text-xs text-gray-400 mt-0.5">
                                                Rp {{ number_format($detail->product->selling_price, 0, ',', '.') }} / {{ $detail->product->unit }}
                                            </div>
                                        @else
                                            <span class="text-red-500 italic">Menu tidak ditemukan (ID: {{ $detail->product_id }})</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($detail->product && $detail->product->recipeItems->isNotEmpty())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                ✓ {{ $detail->product->recipeItems->count() }} Bahan Terhubung
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                                Belum Ada Resep
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-mono font-bold text-emerald-700">
                                        {{ number_format($detail->planned_quantity, 0, ',', '.') }}
                                        <span class="text-xs font-normal text-gray-500">porsi</span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-600">
                                        {{ $detail->notes ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                        @if ($detail->product && in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                                            <a href="{{ route('products.recipe.edit', $detail->product) }}"
                                               class="text-emerald-600 hover:text-emerald-900 font-medium hover:underline">
                                                Kelola Resep &rarr;
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50/75 border-t border-gray-200 font-semibold text-gray-800">
                            <tr>
                                <td colspan="3" class="px-6 py-3.5 text-right text-xs uppercase tracking-wider">
                                    Total Porsi Keseluruhan:
                                </td>
                                <td class="px-6 py-3.5 text-right text-base text-emerald-800 font-bold font-mono">
                                    {{ number_format($plan->totalPlannedQuantity(), 0, ',', '.') }} porsi
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
