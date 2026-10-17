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
                <a href="{{ route('production-plans.ingredients', $plan) }}"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-semibold text-emerald-700 uppercase tracking-widest hover:bg-emerald-100 transition shadow-sm">
                    <svg class="w-4 h-4 me-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Lembar Kerja Dapur
                </a>

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

            <!-- Kalkulator Otomatis Kebutuhan Bahan Baku Produksi (FR-19) -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

                <!-- ── Header Kalkulator ── -->
                <div class="p-6 sm:p-8 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-5">
                        <div class="min-w-0">
                            <!-- Badges status di atas judul -->
                            <div class="flex flex-wrap items-center gap-2.5 mb-3.5">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 tracking-wide">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a1 1 0 001-1V6a1 1 0 00-1-1H4a1 1 0 00-1 1v12a1 1 0 001 1z" />
                                    </svg>
                                    Kalkulator Logistik
                                </span>
                                @if ($calculation['summary']['shortage_count'] > 0)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold bg-red-50 text-red-700 border border-red-200 tracking-wide">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                        Perlu Pengadaan &mdash; {{ $calculation['summary']['shortage_count'] }} bahan kurang
                                    </span>
                                @elseif ($calculation['summary']['below_minimum_count'] > 0)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 tracking-wide">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Restock Diperlukan &mdash; {{ $calculation['summary']['below_minimum_count'] }} bahan di bawah minimum
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 tracking-wide">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Kebutuhan Terpenuhi &amp; Stok Aman
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 leading-snug">
                                Kalkulasi Kebutuhan Bahan Baku dan Ketersediaan Stok Gudang
                            </h3>
                            <p class="mt-2 text-xs sm:text-sm text-gray-500 leading-relaxed max-w-3xl">
                                Dihitung otomatis dari target porsi dikali takaran resep, diagregasikan antar-menu, dan dibandingkan dengan persediaan stok gudang.
                            </p>
                        </div>
                        <div class="shrink-0 pt-1 sm:pt-0">
                            <a href="{{ route('production-plans.ingredients', $plan) }}"
                               target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition shadow-xs whitespace-nowrap">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Cetak Lembar Kerja Dapur
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ── Kartu Ringkasan 6 Item (2 × 3 pada desktop) ── -->
                <div class="p-6 sm:p-8 bg-gray-50/70 border-b border-gray-200">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- 1. Total Jenis Bahan -->
                        <div class="bg-white rounded-lg border border-gray-200 px-4 py-3.5">
                            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Total Jenis Bahan</p>
                            <p class="mt-1 text-2xl font-black font-mono text-gray-900 tabular-nums">
                                {{ $calculation['summary']['total_ingredients'] }}
                                <span class="text-sm font-normal text-gray-400 ml-0.5">item</span>
                            </p>
                        </div>
                        <!-- 2. Kebutuhan Terpenuhi -->
                        <div class="bg-white rounded-lg border border-gray-200 px-4 py-3.5">
                            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Kebutuhan Terpenuhi</p>
                            <p class="mt-1 text-2xl font-black font-mono tabular-nums {{ $calculation['summary']['fulfilled_count'] > 0 ? 'text-emerald-700' : 'text-gray-900' }}">
                                {{ $calculation['summary']['fulfilled_count'] }}
                                <span class="text-sm font-normal {{ $calculation['summary']['fulfilled_count'] > 0 ? 'text-emerald-500' : 'text-gray-400' }} ml-0.5">item</span>
                            </p>
                        </div>
                        <!-- 3. Kebutuhan Kurang -->
                        <div class="bg-white rounded-lg border {{ $calculation['summary']['shortage_count'] > 0 ? 'border-red-200' : 'border-gray-200' }} px-4 py-3.5">
                            <p class="text-[11px] font-semibold {{ $calculation['summary']['shortage_count'] > 0 ? 'text-red-600' : 'text-gray-500' }} uppercase tracking-wider">Kebutuhan Kurang</p>
                            <p class="mt-1 text-2xl font-black font-mono tabular-nums {{ $calculation['summary']['shortage_count'] > 0 ? 'text-red-700' : 'text-gray-900' }}">
                                {{ $calculation['summary']['shortage_count'] }}
                                <span class="text-sm font-normal {{ $calculation['summary']['shortage_count'] > 0 ? 'text-red-400' : 'text-gray-400' }} ml-0.5">item</span>
                            </p>
                        </div>
                        <!-- 4. Bawah atau Sama Dengan Batas Minimum -->
                        <div class="bg-white rounded-lg border {{ $calculation['summary']['below_minimum_count'] > 0 ? 'border-amber-200' : 'border-gray-200' }} px-4 py-3.5">
                            <p class="text-[11px] font-semibold {{ $calculation['summary']['below_minimum_count'] > 0 ? 'text-amber-700' : 'text-gray-500' }} uppercase tracking-wider">&le; Batas Minimum</p>
                            <p class="mt-1 text-2xl font-black font-mono tabular-nums {{ $calculation['summary']['below_minimum_count'] > 0 ? 'text-amber-700' : 'text-gray-900' }}">
                                {{ $calculation['summary']['below_minimum_count'] }}
                                <span class="text-sm font-normal {{ $calculation['summary']['below_minimum_count'] > 0 ? 'text-amber-400' : 'text-gray-400' }} ml-0.5">item</span>
                            </p>
                        </div>
                        <!-- 5. Kesiapan Produksi -->
                        <div class="bg-white rounded-lg border border-gray-200 px-4 py-3.5">
                            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2.5">Kesiapan Produksi</p>
                            @if ($calculation['summary']['production_readiness']['status'] === 'ready')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                    Siap Produksi
                                </span>
                            @elseif ($calculation['summary']['production_readiness']['status'] === 'procurement_needed')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-red-50 text-red-800 border border-red-200">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                                    Perlu Pengadaan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                    Perlu Verifikasi
                                </span>
                            @endif
                        </div>
                        <!-- 6. Kondisi Stok Akhir -->
                        <div class="bg-white rounded-lg border border-gray-200 px-4 py-3.5">
                            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2.5">Kondisi Stok Akhir</p>
                            @if ($calculation['summary']['inventory_condition']['status'] === 'safe')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                    Persediaan Aman
                                </span>
                            @elseif ($calculation['summary']['inventory_condition']['status'] === 'restock_needed')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-900 border border-amber-200">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                    Restock Diperlukan
                                </span>
                            @elseif ($calculation['summary']['inventory_condition']['status'] === 'deficit')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-red-50 text-red-800 border border-red-200">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                                    Defisit Stok
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                    Perlu Verifikasi
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- ── Panel Peringatan Operasional ── -->
                @if ($calculation['summary']['has_shortage'])
                    <div class="px-6 sm:px-8 pt-6 pb-2">
                        <div class="p-4 sm:p-5 bg-red-50/90 border-l-4 border-red-500 rounded-r-xl border-y border-r border-red-100/80 shadow-xs">
                            <div class="flex items-start gap-3.5">
                                <svg class="w-5 h-5 text-red-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <p class="text-sm font-bold text-red-800">Peringatan Kekurangan Kebutuhan Produksi (Perlu Pengadaan)</p>
                                    <p class="mt-1 text-xs sm:text-sm text-red-700 leading-relaxed">
                                        Terdapat <strong>{{ $calculation['summary']['shortage_count'] }} jenis bahan baku</strong> dengan stok gudang yang tidak mencukupi kebutuhan produksi. Koordinasikan pengadaan dengan tim gudang sebelum jadwal produksi dimulai.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($calculation['summary']['has_restock_warning'] && ! $calculation['summary']['has_shortage'])
                    <div class="px-6 sm:px-8 pt-6 pb-2">
                        <div class="p-4 sm:p-5 bg-amber-50/90 border-l-4 border-amber-500 rounded-r-xl border-y border-r border-amber-100/80 shadow-xs">
                            <div class="flex items-start gap-3.5">
                                <svg class="w-5 h-5 text-amber-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <p class="text-sm font-bold text-amber-800">Peringatan Restock Persediaan Gudang (Batas Minimum Tercapai)</p>
                                    <p class="mt-1 text-xs sm:text-sm text-amber-700 leading-relaxed">
                                        Kebutuhan produksi terpenuhi. Namun proyeksi stok akhir pada <strong>{{ $calculation['summary']['below_minimum_count'] }} jenis bahan baku</strong> berada pada atau di bawah batas minimum stok. Segera jadwalkan pembelian ulang.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ── Tabel Bahan Baku (10 Kolom) ── -->
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm border-t border-gray-100">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                <th scope="col" class="w-10 px-3 py-3 text-center">No.</th>
                                <th scope="col" class="min-w-[180px] px-4 py-3 text-left">Bahan Baku &amp; Kategori</th>
                                <th scope="col" class="min-w-[110px] px-3 py-3 text-right">Kebutuhan Total</th>
                                <th scope="col" class="min-w-[100px] px-3 py-3 text-right">Stok Aktual</th>
                                <th scope="col" class="min-w-[120px] px-3 py-3 text-right">Proyeksi Stok Akhir</th>
                                <th scope="col" class="min-w-[120px] px-3 py-3 text-right">Kekurangan Produksi</th>
                                <th scope="col" class="min-w-[100px] px-3 py-3 text-right">Minimum Stok</th>
                                <th scope="col" class="min-w-[110px] px-3 py-3 text-center">Status Kebutuhan</th>
                                <th scope="col" class="min-w-[140px] px-3 py-3 text-center">Status Stok Akhir</th>
                                <th scope="col" class="w-20 px-3 py-3 text-center">Cek Fisik</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($calculation['items'] as $index => $item)
                                <tr class="hover:bg-gray-50/70 transition-colors {{ $item['availability_status'] === 'shortage' ? 'bg-red-50/30' : ($item['ending_stock_status'] === 'below_minimum' || $item['ending_stock_status'] === 'at_minimum' ? 'bg-amber-50/20' : '') }}">
                                    <!-- No -->
                                    <td class="px-3 py-4 text-center text-xs text-gray-400 font-mono whitespace-nowrap">
                                        {{ $index + 1 }}
                                    </td>
                                    <!-- Bahan & Kategori -->
                                    <td class="px-4 py-4">
                                        <p class="font-semibold text-gray-900 text-sm leading-snug">{{ $item['ingredient_name'] }}</p>
                                        <p class="mt-0.5 text-xs text-gray-500">
                                            {{ $item['ingredient_category'] }}
                                            <span class="text-gray-300 mx-1">&bull;</span>
                                            <span class="font-mono text-gray-600">{{ $item['unit'] }}</span>
                                        </p>
                                        @if ($item['has_unit_mismatch'])
                                            <p class="mt-1 text-[11px] text-amber-700 font-medium">Satuan resep dikonversi ke satuan stok</p>
                                        @endif
                                        <div class="mt-1 text-[11px] text-gray-400 space-y-0.5">
                                            @foreach ($item['menu_breakdown'] as $menu)
                                                <div>&bull; {{ $menu['product_name'] }} &mdash; {{ $menu['planned_quantity'] }} porsi &rarr; {{ $menu['display_subtotal'] }}</div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <!-- Kebutuhan Total -->
                                    <td class="px-3 py-4 text-right whitespace-nowrap font-mono font-bold text-gray-900 tabular-nums">
                                        {{ $item['formatted_required'] }}
                                    </td>
                                    <!-- Stok Aktual -->
                                    <td class="px-3 py-4 text-right whitespace-nowrap font-mono text-gray-700 tabular-nums">
                                        {{ $item['formatted_current_stock'] }}
                                    </td>
                                    <!-- Proyeksi Stok Akhir -->
                                    <td class="px-3 py-4 text-right whitespace-nowrap font-mono font-semibold tabular-nums">
                                        @if ($item['projected_ending_stock'] !== null)
                                            @if ($item['projected_ending_stock'] > $item['minimum_stock'])
                                                <span class="text-emerald-700">{{ $item['formatted_projected_ending_stock'] }}</span>
                                            @elseif ($item['projected_ending_stock'] >= 0)
                                                <span class="text-amber-600">{{ $item['formatted_projected_ending_stock'] }}</span>
                                            @else
                                                <span class="text-red-700">{{ $item['formatted_projected_ending_stock'] }}</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400 font-normal">&mdash;</span>
                                        @endif
                                    </td>
                                    <!-- Kekurangan Produksi -->
                                    <td class="px-3 py-4 text-right whitespace-nowrap font-mono font-semibold tabular-nums">
                                        @if ($item['production_shortage'] !== null && $item['production_shortage'] > 0)
                                            <span class="text-red-700">{{ $item['formatted_production_shortage'] }}</span>
                                        @elseif ($item['production_shortage'] !== null)
                                            <span class="text-gray-400 font-normal">0 {{ $item['unit'] }}</span>
                                        @else
                                            <span class="text-gray-400 font-normal">&mdash;</span>
                                        @endif
                                    </td>
                                    <!-- Minimum Stok -->
                                    <td class="px-3 py-4 text-right whitespace-nowrap font-mono text-gray-500 tabular-nums">
                                        {{ $item['formatted_minimum_stock'] }}
                                    </td>
                                    <!-- Status Kebutuhan -->
                                    <td class="px-3 py-4 text-center whitespace-nowrap">
                                        @if ($item['availability_status'] === 'fulfilled')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">Terpenuhi</span>
                                        @elseif ($item['availability_status'] === 'shortage')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-red-50 text-red-800 border border-red-200">Kurang</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">Perlu Verifikasi</span>
                                        @endif
                                    </td>
                                    <!-- Status Stok Akhir -->
                                    <td class="px-3 py-4 text-center whitespace-nowrap">
                                        @if ($item['ending_stock_status'] === 'safe')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">Aman</span>
                                        @elseif ($item['ending_stock_status'] === 'below_minimum')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">Di Bawah Minimum</span>
                                        @elseif ($item['ending_stock_status'] === 'at_minimum')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-100 text-amber-900 border border-amber-300">Tepat di Minimum</span>
                                        @elseif ($item['ending_stock_status'] === 'deficit')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-red-50 text-red-800 border border-red-200">Defisit</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">Perlu Verifikasi</span>
                                        @endif
                                    </td>
                                    <!-- Cek Fisik -->
                                    <td class="px-3 py-4 text-center whitespace-nowrap">
                                        <input type="checkbox"
                                               class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer"
                                               aria-label="Cek fisik {{ $item['ingredient_name'] }}"
                                               title="Centang setelah verifikasi fisik bahan">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-10 text-center text-sm text-gray-500 italic">
                                        Belum ada kebutuhan bahan baku yang dapat dihitung. Pastikan setiap menu dalam rencana produksi sudah memiliki komposisi resep bahan baku.
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
