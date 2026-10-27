<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Pusat Cetak Laporan Resmi Eksekutif (Owner)') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Cetak dokumen laporan rekapitulasi operasional dapur dan mutasi persediaan bahan gudang (UC-12, FR-40).
                </p>
            </div>
            <div>
                <a href="{{ route('owner.nutrition.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold uppercase tracking-widest rounded-lg transition shadow-sm">
                    &larr; Evaluasi Gizi Menu
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Card Laporan Produksi --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Laporan Rekapitulasi Produksi</h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            Merekap target rencana porsi masakan dapur vs realisasi matang aktual, persentase ketercapaian efisiensi, dan daftar batch produksi yang diselesaikan.
                        </p>
                    </div>

                    <form method="GET" action="{{ route('owner.reports.production') }}" target="_blank" class="space-y-3 pt-3 border-t border-gray-100">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-medium text-gray-600 mb-1">Dari Tanggal</label>
                                <input type="date" name="from_date" value="{{ date('Y-m-01') }}" class="w-full text-xs rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                                <input type="date" name="to_date" value="{{ date('Y-m-t') }}" class="w-full text-xs rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition flex items-center justify-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Buka & Cetak Laporan Produksi (PDF)</span>
                        </button>
                    </form>
                </div>

                {{-- Card Laporan Gudang --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Laporan Mutasi Persediaan Bahan Gudang</h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            Merekapitulasi seluruh mutasi barang masuk (IN), mutasi pengeluaran ke dapur (OUT), koreksi fisik (Adjustment), serta saldo sisa per bahan baku.
                        </p>
                    </div>

                    <form method="GET" action="{{ route('owner.reports.inventory') }}" target="_blank" class="space-y-3 pt-3 border-t border-gray-100">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-medium text-gray-600 mb-1">Dari Tanggal</label>
                                <input type="date" name="from_date" value="{{ date('Y-m-01') }}" class="w-full text-xs rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                                <input type="date" name="to_date" value="{{ date('Y-m-t') }}" class="w-full text-xs rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition flex items-center justify-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Buka & Cetak Laporan Persediaan (PDF)</span>
                        </button>
                    </form>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
