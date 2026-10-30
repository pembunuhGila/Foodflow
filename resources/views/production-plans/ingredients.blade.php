<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
            <div>
                <!-- Breadcrumb Interaktif -->
                <nav class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-1.5">
                    <a href="{{ route('production-plans.show', $plan) }}" class="inline-flex items-center gap-1 hover:text-purple-600 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Kembali ke Detail Rencana</span>
                    </a>
                </nav>

                <!-- Judul & Status Plan -->
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
                        Lembar Kerja Bahan Baku
                    </h1>
                    <span class="font-mono text-purple-700 font-bold text-sm bg-purple-50 px-3 py-1 rounded-xl border border-purple-200">
                        #{{ $plan->plan_number }}
                    </span>
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

            <!-- Tombol Aksi Header -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                @if (in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                    @php
                        $pendingMR = $plan->materialRequests->where('status', 'pending')->first()
                            ?? $plan->materialRequests->where('status', 'Pending')->first();
                        $approvedMR = $plan->materialRequests->where('status', 'approved')->first()
                            ?? $plan->materialRequests->where('status', 'Approved')->first();
                    @endphp

                    @if ($pendingMR)
                        <a href="{{ route('material-requests.show', $pendingMR) }}"
                           class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-semibold transition-all shadow-sm whitespace-nowrap">
                            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                            <span>Menunggu Gudang (#MR-{{ str_pad($pendingMR->id, 4, '0', STR_PAD_LEFT) }})</span>
                        </a>
                    @elseif ($approvedMR)
                        <a href="{{ route('material-requests.show', $approvedMR) }}"
                           class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold transition-all shadow-sm whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Stok Dikeluarkan (#MR-{{ str_pad($approvedMR->id, 4, '0', STR_PAD_LEFT) }})</span>
                        </a>
                    @else
                        <form method="POST" action="{{ route('production-plans.material-requests.store', $plan) }}" class="inline-block">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Kirimkan seluruh daftar kebutuhan bahan lembar kerja ini ke bagian gudang untuk diverifikasi & dikeluarkan?');"
                                    class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold transition-all shadow-sm cursor-pointer whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                <span>Ajukan ke Gudang</span>
                            </button>
                        </form>
                    @endif
                @endif

                <!-- Tombol Cetak / PDF Tunggal -->
                <button type="button"
                        onclick="window.print()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl text-xs font-semibold transition-all shadow-sm cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak / Simpan PDF</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8 print:py-0 print:m-0">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 print:space-y-3 print:px-0 print:max-w-none">

            <!-- Kop Formal Khusus Dokumen Cetak / PDF -->
            <div class="print-header-block border-b-2 border-gray-900 pb-3 mb-3">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-xl font-black tracking-tight text-gray-900 uppercase leading-tight">FOODFLOW CATERING</h1>
                        <p class="text-[11px] font-bold text-gray-700 uppercase tracking-wide mt-0.5">LEMBAR KERJA BAHAN BAKU PRODUKSI &amp; LOGISTIK GUDANG (MRP)</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold font-mono text-gray-900">No. {{ $plan->plan_number }}</div>
                        <div class="text-[10px] text-gray-500">Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- Card Informasi Rencana Produksi (Pastel Soft) -->
            <div class="plan-info-card bg-white rounded-2xl border border-purple-100 shadow-sm p-5 sm:p-6 print:p-3 print:border print:border-gray-300 print:rounded-md print:shadow-none">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-8 text-sm">
                    <!-- Kolom Kiri -->
                    <div class="space-y-2.5 print:space-y-1">
                        <div class="flex flex-col sm:flex-row sm:items-baseline justify-between border-b border-gray-50 pb-2 print:pb-1">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider print:text-[10px]">No. Rencana</span>
                            <span class="font-mono font-bold text-gray-900 text-base sm:text-right print:text-xs">{{ $plan->plan_number }}</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-baseline justify-between border-b border-gray-50 pb-2 print:pb-1">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider print:text-[10px]">Jadwal Masak</span>
                            <span class="font-bold text-purple-700 text-base sm:text-right print:text-xs print:text-gray-900">
                                {{ $plan->production_date ? $plan->production_date->translatedFormat('l, d F Y') : '-' }}
                            </span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-baseline justify-between border-b border-gray-50 pb-2 md:border-b-0 md:pb-0 print:border-b-0 print:pb-0">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider print:text-[10px]">Total Target Porsi</span>
                            <span class="font-bold text-gray-900 text-base sm:text-right print:text-xs">
                                {{ number_format($plan->totalPlannedQuantity(), 0, ',', '.') }} Porsi
                            </span>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="space-y-2.5 print:space-y-1">
                        <div class="flex flex-col sm:flex-row sm:items-baseline justify-between border-b border-gray-50 pb-2 print:pb-1">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider print:text-[10px]">Penanggung Jawab</span>
                            <span class="font-bold text-gray-900 text-base sm:text-right print:text-xs">{{ $plan->creator?->name ?? 'Sistem' }}</span>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1 print:text-[10px] print:mb-0.5">Catatan &amp; Instruksi Khusus</span>
                            <div class="text-xs text-gray-700 bg-gray-50/75 border border-gray-100 rounded-xl p-3 min-h-[42px] print:p-1.5 print:text-[10px] print:min-h-0 print:bg-white print:border-gray-200">
                                @if ($plan->notes)
                                    {{ $plan->notes }}
                                @else
                                    <span class="text-gray-400 italic">Tidak ada catatan atau instruksi khusus.</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6 Kartu Ringkasan Stok & Kesiapan -->
            <div class="summary-cards-container grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5 print:gap-1.5">
                <!-- 1. Total Jenis Bahan -->
                <div class="summary-card bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between print:rounded-md print:shadow-none print:p-2">
                    <span class="card-label text-[10px] sm:text-[11px] font-semibold text-gray-400 uppercase tracking-wider block leading-tight">Total Bahan</span>
                    <div class="mt-2 flex items-baseline gap-1">
                        <span class="card-value text-xl sm:text-2xl font-black font-mono text-gray-900">{{ $calculation['summary']['total_ingredients'] }}</span>
                        <span class="text-xs font-medium text-gray-400 print:text-[9px]">item</span>
                    </div>
                </div>

                <!-- 2. Kebutuhan Terpenuhi -->
                <div class="summary-card bg-white p-4 rounded-2xl border border-emerald-100 bg-emerald-50/20 shadow-sm flex flex-col justify-between print:rounded-md print:shadow-none print:p-2">
                    <span class="card-label text-[10px] sm:text-[11px] font-semibold text-emerald-700 uppercase tracking-wider block leading-tight">Terpenuhi</span>
                    <div class="mt-2 flex items-baseline gap-1">
                        <span class="card-value text-xl sm:text-2xl font-black font-mono text-emerald-700 print:text-gray-900">{{ $calculation['summary']['fulfilled_count'] }}</span>
                        <span class="text-xs font-medium text-emerald-600 print:text-[9px] print:text-gray-500">item</span>
                    </div>
                </div>

                <!-- 3. Kebutuhan Kurang -->
                <div class="summary-card bg-white p-4 rounded-2xl border {{ $calculation['summary']['shortage_count'] > 0 ? 'border-rose-200 bg-rose-50/30' : 'border-gray-100' }} shadow-sm flex flex-col justify-between print:rounded-md print:shadow-none print:p-2">
                    <span class="card-label text-[10px] sm:text-[11px] font-semibold {{ $calculation['summary']['shortage_count'] > 0 ? 'text-rose-700' : 'text-gray-400' }} uppercase tracking-wider block leading-tight">Kurang</span>
                    <div class="mt-2 flex items-baseline gap-1">
                        <span class="card-value text-xl sm:text-2xl font-black font-mono {{ $calculation['summary']['shortage_count'] > 0 ? 'text-rose-700 print:text-red-700' : 'text-gray-900' }}">
                            {{ $calculation['summary']['shortage_count'] }}
                        </span>
                        <span class="text-xs font-medium {{ $calculation['summary']['shortage_count'] > 0 ? 'text-rose-600' : 'text-gray-400' }} print:text-[9px]">item</span>
                    </div>
                </div>

                <!-- 4. <= Batas Minimum -->
                <div class="summary-card bg-white p-4 rounded-2xl border {{ $calculation['summary']['below_minimum_count'] > 0 ? 'border-amber-200 bg-amber-50/30' : 'border-gray-100' }} shadow-sm flex flex-col justify-between print:rounded-md print:shadow-none print:p-2">
                    <span class="card-label text-[10px] sm:text-[11px] font-semibold {{ $calculation['summary']['below_minimum_count'] > 0 ? 'text-amber-800' : 'text-gray-400' }} uppercase tracking-wider block leading-tight">&le; Min Stok</span>
                    <div class="mt-2 flex items-baseline gap-1">
                        <span class="card-value text-xl sm:text-2xl font-black font-mono {{ $calculation['summary']['below_minimum_count'] > 0 ? 'text-amber-800' : 'text-gray-900' }}">
                            {{ $calculation['summary']['below_minimum_count'] }}
                        </span>
                        <span class="text-xs font-medium {{ $calculation['summary']['below_minimum_count'] > 0 ? 'text-amber-700' : 'text-gray-400' }} print:text-[9px]">item</span>
                    </div>
                </div>

                <!-- 5. Kesiapan Produksi -->
                <div class="summary-card bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between print:rounded-md print:shadow-none print:p-2">
                    <span class="card-label text-[10px] sm:text-[11px] font-semibold text-gray-400 uppercase tracking-wider block leading-tight">Kesiapan</span>
                    <div class="mt-2">
                        @if ($calculation['summary']['production_readiness']['status'] === 'ready')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 whitespace-nowrap print:text-[9px] print:px-1.5">
                                Siap Produksi
                            </span>
                        @elseif ($calculation['summary']['production_readiness']['status'] === 'procurement_needed')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200 whitespace-nowrap print:text-[9px] print:px-1.5">
                                Perlu Pengadaan
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200 whitespace-nowrap print:text-[9px] print:px-1.5">
                                Perlu Verifikasi
                            </span>
                        @endif
                    </div>
                </div>

                <!-- 6. Kondisi Persediaan Akhir -->
                <div class="summary-card bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between print:rounded-md print:shadow-none print:p-2">
                    <span class="card-label text-[10px] sm:text-[11px] font-semibold text-gray-400 uppercase tracking-wider block leading-tight">Stok Akhir</span>
                    <div class="mt-2">
                        @if ($calculation['summary']['inventory_condition']['status'] === 'safe')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 whitespace-nowrap print:text-[9px] print:px-1.5">
                                Persediaan Aman
                            </span>
                        @elseif ($calculation['summary']['inventory_condition']['status'] === 'restock_needed')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-50 text-amber-900 border border-amber-200 whitespace-nowrap print:text-[9px] print:px-1.5">
                                Restock Diperlukan
                            </span>
                        @elseif ($calculation['summary']['inventory_condition']['status'] === 'deficit')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200 whitespace-nowrap print:text-[9px] print:px-1.5">
                                Defisit Stok
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200 whitespace-nowrap print:text-[9px] print:px-1.5">
                                Perlu Verifikasi
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Peringatan Operasional (Jika Ada Kebutuhan Tindakan) -->
            @php
                $unverifiedItems = collect($calculation['items'])->filter(function ($i) {
                    return $i['availability_status'] === 'unverified' || $i['current_stock'] === null;
                });
            @endphp

            @if ($calculation['summary']['has_shortage'])
                <div class="print-alert p-4 bg-rose-50/90 border border-rose-200 rounded-2xl text-rose-900 text-xs print:p-2 print:border print:border-rose-300 print:rounded-md">
                    <div class="font-bold text-xs sm:text-sm text-rose-950 flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0 print:hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span>Peringatan Kekurangan Kebutuhan Produksi (Perlu Pengadaan)</span>
                    </div>
                    <p class="mt-1 text-rose-800 leading-relaxed print:text-[10px]">
                        Terdapat <strong>{{ $calculation['summary']['shortage_count'] }} jenis bahan baku</strong> yang stok aktualnya kurang dari kebutuhan porsi masak. Purchasing &amp; Tim Gudang harus segera melakukan pengadaan bahan sebelum jadwal masak dimulai.
                    </p>
                </div>
            @endif

            @if ($calculation['summary']['has_restock_warning'] && ! $calculation['summary']['has_shortage'])
                <div class="print-alert p-4 bg-amber-50/90 border border-amber-200 rounded-2xl text-amber-900 text-xs print:p-2 print:border print:border-amber-300 print:rounded-md">
                    <div class="font-bold text-xs sm:text-sm text-amber-950 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600 shrink-0 print:hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span>Peringatan Restock Persediaan Gudang (Batas Minimum Tercapai)</span>
                    </div>
                    <p class="mt-1 text-amber-800 leading-relaxed print:text-[10px]">
                        Kebutuhan produksi terpenuhi untuk jadwal masak ini, namun proyeksi stok akhir pada <strong>{{ $calculation['summary']['below_minimum_count'] }} jenis bahan baku</strong> akan mencapai atau berada di bawah batas minimum (safety stock). Segera jadwalkan pembelian ulang.
                    </p>
                </div>
            @endif

            @if ($unverifiedItems->isNotEmpty())
                <div class="print-alert p-4 bg-gray-50 border border-gray-200 rounded-2xl text-gray-800 text-xs print:p-2 print:border print:border-gray-300 print:rounded-md">
                    <div class="font-bold text-xs sm:text-sm text-gray-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500 shrink-0 print:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Verifikasi Data Stok Gudang Diperlukan</span>
                    </div>
                    <p class="mt-1 text-gray-600 leading-relaxed print:text-[10px]">
                        Terdapat <strong>{{ $unverifiedItems->count() }} jenis bahan baku</strong> dengan data persediaan yang belum tercatat atau memerlukan verifikasi fisik langsung di gudang sebelum proses serah terima.
                    </p>
                </div>
            @endif

            <!-- Tabel Kebutuhan Bahan Baku (10 Kolom) -->
            <div class="table-container bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden print:border print:border-gray-300 print:rounded-md print:shadow-none">
                <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between print:p-2 print:border-b print:border-gray-300">
                    <div>
                        <h3 class="font-bold text-gray-900 text-xs sm:text-sm print:text-[11px]">Daftar Kebutuhan Bahan Baku Produksi &amp; Status Stok</h3>
                        <p class="text-[11px] text-gray-400 print:hidden mt-0.5">Dihitung otomatis berdasarkan resep dan porsi terencana, dibandingkan langsung dengan persediaan gudang.</p>
                    </div>
                    <span class="text-xs font-semibold px-3 py-1 bg-gray-50 text-gray-700 rounded-full border border-gray-200 print:text-[9px] print:px-1.5">
                        {{ $calculation['summary']['total_ingredients'] }} Bahan Terdaftar
                    </span>
                </div>

                <div class="overflow-x-auto print:overflow-visible">
                    <table class="ingredients-table w-full border-collapse text-left text-xs">
                        <thead class="bg-gray-50/75 font-semibold uppercase tracking-wider text-gray-500 text-[11px] border-b border-gray-100 print:bg-gray-100">
                            <tr>
                                <th scope="col" class="col-no px-3 py-3 text-center">No.</th>
                                <th scope="col" class="col-name px-4 py-3 text-left">Bahan Baku &amp; Kategori</th>
                                <th scope="col" class="col-req px-3 py-3 text-right">Kebutuhan Total</th>
                                <th scope="col" class="col-stock px-3 py-3 text-right">Stok Aktual</th>
                                <th scope="col" class="col-proj px-3 py-3 text-right">Proyeksi Stok Akhir</th>
                                <th scope="col" class="col-short px-3 py-3 text-right">Kekurangan</th>
                                <th scope="col" class="col-min px-3 py-3 text-right">Minimum Stok</th>
                                <th scope="col" class="col-status-req px-3 py-3 text-center">Status Kebutuhan</th>
                                <th scope="col" class="col-status-end px-3 py-3 text-center">Status Stok Akhir</th>
                                <th scope="col" class="col-check px-3 py-3 text-center">Cek Fisik</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @forelse ($calculation['items'] as $index => $item)
                                <tr class="hover:bg-gray-50/60 transition-colors {{ $item['availability_status'] === 'shortage' ? 'bg-rose-50/20' : ($item['ending_stock_status'] === 'below_minimum' || $item['ending_stock_status'] === 'at_minimum' ? 'bg-amber-50/15' : '') }}">
                                    <!-- No. -->
                                    <td class="px-3 py-3 text-xs text-gray-400 font-mono text-center align-top">
                                        {{ $index + 1 }}
                                    </td>
                                    <!-- Bahan Baku & Kategori -->
                                    <td class="px-4 py-3 align-top">
                                        <div class="font-bold text-gray-900 text-xs sm:text-sm leading-snug print:text-[10px]">{{ $item['ingredient_name'] }}</div>
                                        <div class="text-[11px] text-gray-500 font-normal mt-0.5 print:text-[9px]">
                                            Kategori: <span class="font-medium text-gray-700">{{ $item['ingredient_category'] }}</span>
                                            &bull; Satuan: <span class="font-mono text-gray-700 font-semibold">{{ $item['unit'] }}</span>
                                        </div>
                                        @if ($item['has_unit_mismatch'])
                                            <div class="mt-0.5 inline-flex items-center gap-1 text-[10px] text-amber-700 font-medium print:text-[8px]">
                                                <svg class="w-3 h-3 text-amber-600 shrink-0 print:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>Satuan resep dikonversi ke satuan stok ({{ $item['unit'] }})</span>
                                            </div>
                                        @endif
                                        <div class="text-[10px] text-gray-400 mt-1 print:hidden space-y-0.5">
                                            @foreach ($item['menu_breakdown'] as $mb)
                                                <div class="truncate">&bull; {{ $mb['product_name'] }} ({{ $mb['planned_quantity'] }} porsi &rarr; {{ $mb['display_subtotal'] }})</div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <!-- Kebutuhan Total -->
                                    <td class="px-3 py-3 text-right font-mono tabular-nums font-bold text-gray-900 align-top whitespace-nowrap print:text-[10px]">
                                        {{ $item['formatted_required'] }}
                                    </td>
                                    <!-- Stok Aktual -->
                                    <td class="px-3 py-3 text-right font-mono tabular-nums font-semibold text-gray-700 align-top whitespace-nowrap print:text-[10px]">
                                        {{ $item['formatted_current_stock'] }}
                                    </td>
                                    <!-- Proyeksi Stok Akhir -->
                                    <td class="px-3 py-3 text-right font-mono tabular-nums font-bold align-top whitespace-nowrap print:text-[10px]">
                                        @if ($item['projected_ending_stock'] !== null)
                                            @if ($item['projected_ending_stock'] > $item['minimum_stock'])
                                                <span class="text-emerald-700 print:text-gray-900">{{ $item['formatted_projected_ending_stock'] }}</span>
                                            @elseif ($item['projected_ending_stock'] >= 0)
                                                <span class="text-amber-700 print:text-gray-900">{{ $item['formatted_projected_ending_stock'] }}</span>
                                            @else
                                                <span class="text-rose-700 print:text-red-700">{{ $item['formatted_projected_ending_stock'] }}</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400 font-normal">-</span>
                                        @endif
                                    </td>
                                    <!-- Kekurangan Produksi -->
                                    <td class="px-3 py-3 text-right font-mono tabular-nums font-bold align-top whitespace-nowrap print:text-[10px]">
                                        @if ($item['production_shortage'] !== null && $item['production_shortage'] > 0)
                                            <span class="text-rose-700 print:text-red-700">{{ $item['formatted_production_shortage'] }}</span>
                                        @elseif ($item['production_shortage'] !== null)
                                            <span class="text-emerald-700 font-medium print:text-gray-500">0 {{ $item['unit'] }}</span>
                                        @else
                                            <span class="text-gray-400 font-normal">-</span>
                                        @endif
                                    </td>
                                    <!-- Minimum Stok -->
                                    <td class="px-3 py-3 text-right font-mono tabular-nums text-gray-500 font-medium align-top whitespace-nowrap print:text-[10px]">
                                        {{ $item['formatted_minimum_stock'] }}
                                    </td>
                                    <!-- Status Kebutuhan -->
                                    <td class="px-3 py-3 text-center align-top whitespace-nowrap">
                                        @if ($item['availability_status'] === 'fulfilled')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                Terpenuhi
                                            </span>
                                        @elseif ($item['availability_status'] === 'shortage')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200 print:text-red-700">
                                                Kurang
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                                Perlu Verifikasi
                                            </span>
                                        @endif
                                    </td>
                                    <!-- Status Stok Akhir -->
                                    <td class="px-3 py-3 text-center align-top whitespace-nowrap">
                                        @if ($item['ending_stock_status'] === 'safe')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                Aman
                                            </span>
                                        @elseif ($item['ending_stock_status'] === 'below_minimum')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                                Di Bawah Min
                                            </span>
                                        @elseif ($item['ending_stock_status'] === 'at_minimum')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-900 border border-amber-300">
                                                Tepat Min
                                            </span>
                                        @elseif ($item['ending_stock_status'] === 'deficit')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200 print:text-red-700">
                                                Defisit
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                                Perlu Verifikasi
                                            </span>
                                        @endif
                                    </td>
                                    <!-- Cek Fisik -->
                                    <td class="px-3 py-3 text-center align-top">
                                        <div class="flex items-center justify-center">
                                            <input type="checkbox"
                                                   class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500 cursor-pointer print:w-3.5 print:h-3.5 print:border-gray-500"
                                                   aria-label="Cek fisik {{ $item['ingredient_name'] }}">
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-8 text-center text-sm text-gray-400 italic">
                                        Tidak ada bahan baku yang terdaftar pada menu rencana produksi ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bagian Tanda Tangan Serah Terima (3 Kolom Sejajar & Rapi) -->
            <div class="signature-section mt-6 pt-5 border-t border-gray-200 print:mt-4 print:pt-2">
                <div class="signature-grid grid grid-cols-1 sm:grid-cols-3 gap-6 text-center text-xs text-gray-700">
                    <div class="sig-box flex flex-col items-center">
                        <span class="sig-title font-semibold text-gray-800 mb-12 sm:mb-14">Disiapkan Oleh (Staff Gudang):</span>
                        <div class="sig-line border-b border-gray-400 w-48 max-w-full mb-1.5"></div>
                        <span class="sig-sub text-gray-400 text-[11px]">(Nama &amp; Tanda Tangan)</span>
                    </div>
                    <div class="sig-box flex flex-col items-center">
                        <span class="sig-title font-semibold text-gray-800 mb-12 sm:mb-14">Diterima Oleh (Tim Dapur):</span>
                        <div class="sig-line border-b border-gray-400 w-48 max-w-full mb-1.5"></div>
                        <span class="sig-sub text-gray-400 text-[11px]">(Nama &amp; Tanda Tangan)</span>
                    </div>
                    <div class="sig-box flex flex-col items-center">
                        <span class="sig-title font-semibold text-gray-800 mb-12 sm:mb-14">Disetujui (Kepala Produksi / Ahli Gizi):</span>
                        <div class="sig-line border-b border-gray-400 w-48 max-w-full mb-1.5"></div>
                        <span class="sig-name text-gray-900 font-semibold text-[11px]">{{ $plan->creator?->name ?? '(Nama & Tanda Tangan)' }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- CSS Khusus Cetak & Dokumen PDF -->
    <style>
        .print-header-block {
            display: none;
        }

        @media print {
            /* Sembunyikan navigasi web, sidebar, breadcrumb, dan tombol */
            nav,
            header,
            aside,
            .no-print,
            button,
            a[href^="http"]:after,
            a[href]:after {
                display: none !important;
                content: "" !important;
            }

            /* Tampilkan header formal kop surat */
            .print-header-block {
                display: block !important;
            }

            /* Halaman Landscape A4 dengan margin yang proporsional */
            @page {
                size: A4 landscape;
                margin: 8mm 10mm 10mm 10mm;
            }

            /* Pengaturan dasar cetak */
            html, body, .min-h-screen, main {
                background: #ffffff !important;
                color: #0f172a !important;
                font-size: 8pt !important;
                line-height: 1.25 !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                width: 100% !important;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* ── Tata Letak 6 Kartu Ringkasan Cetak ── */
            .summary-cards-container {
                display: grid !important;
                grid-template-columns: repeat(6, 1fr) !important;
                gap: 6px !important;
                margin-bottom: 6px !important;
            }

            .summary-card {
                padding: 4px 6px !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 4px !important;
                background-color: #ffffff !important;
            }

            .summary-card .card-label {
                font-size: 6.5pt !important;
                line-height: 1.1 !important;
            }

            .summary-card .card-value {
                font-size: 11pt !important;
                margin-top: 2px !important;
            }

            /* ── Tata Letak Tabel Cetak (Mencegah Scrollbar & Potong Kolom) ── */
            .table-container {
                overflow: visible !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 4px !important;
            }

            .overflow-x-auto {
                overflow: visible !important;
            }

            .ingredients-table {
                width: 100% !important;
                table-layout: fixed !important;
                border-collapse: collapse !important;
                font-size: 7.5pt !important;
            }

            .ingredients-table thead {
                display: table-header-group !important;
            }

            .ingredients-table tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .ingredients-table th,
            .ingredients-table td {
                border: 1px solid #cbd5e1 !important;
                padding: 3px 4px !important;
                word-wrap: break-word !important;
                overflow-wrap: break-word !important;
                white-space: normal !important;
            }

            .ingredients-table th {
                background-color: #f1f5f9 !important;
                color: #0f172a !important;
                font-weight: 700 !important;
                font-size: 7pt !important;
                line-height: 1.15 !important;
            }

            /* Proporsi Lebar Kolom 100% */
            .col-no { width: 4% !important; }
            .col-name { width: 25% !important; }
            .col-req { width: 9.5% !important; }
            .col-stock { width: 9% !important; }
            .col-proj { width: 9.5% !important; }
            .col-short { width: 9.5% !important; }
            .col-min { width: 8.5% !important; }
            .col-status-req { width: 9.5% !important; }
            .col-status-end { width: 10.5% !important; }
            .col-check { width: 5% !important; }

            /* ── Tata Letak Tanda Tangan ── */
            .signature-section {
                margin-top: 10px !important;
                padding-top: 6px !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .signature-grid {
                display: flex !important;
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: flex-start !important;
                gap: 16px !important;
            }

            .sig-box {
                flex: 1 1 0% !important;
                text-align: center !important;
            }

            .sig-title {
                font-size: 7.5pt !important;
                margin-bottom: 38px !important;
                display: block !important;
            }

            .sig-line {
                width: 140px !important;
                margin: 0 auto 3px auto !important;
                border-bottom: 1px solid #475569 !important;
            }

            .sig-sub, .sig-name {
                font-size: 7pt !important;
                display: block !important;
            }
        }
    </style>
</x-app-layout>