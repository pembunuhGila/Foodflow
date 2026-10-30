<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <!-- Breadcrumb Interaktif -->
                <nav class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-1.5">
                    <a href="{{ route('production-plans.index') }}" class="hover:text-purple-600 transition-colors">
                        Rencana Produksi
                    </a>
                    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-gray-900 font-semibold">Detail Rencana</span>
                </nav>

                <!-- Judul Halaman & Status -->
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                        Rencana Produksi: <span class="font-mono text-purple-700">{{ $plan->plan_number }}</span>
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

            <!-- Tombol Aksi Kanan Atas -->
            <div class="flex items-center gap-2">
                @if (in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                    <!-- Tombol Edit Rencana: Style button gray minimalis -->
                    <a href="{{ route('production-plans.edit', $plan) }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl px-4 py-2.5 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Edit Rencana</span>
                    </a>

                    <!-- Tombol Hapus (Native JS Trigger) -->
                    <button type="button" 
                            onclick="openDeleteModal()" 
                            class="p-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl transition-all border border-rose-100 flex items-center justify-center cursor-pointer" 
                            title="Hapus Rencana Produksi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

        <div class="py-8">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Flash Alert Sukses -->
                @if (session('success'))
                    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl shadow-xs flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Warning jika ada menu yang belum memiliki resep (UC-14 Alt Flow 6a) -->
                @php
                    $missingRecipes = $plan->details->filter(fn ($detail) => $detail->product && $detail->product->recipeItems->isEmpty());
                @endphp
                @if ($missingRecipes->isNotEmpty())
                    <div class="p-5 bg-amber-50/80 border border-amber-200 rounded-2xl shadow-xs">
                        <div class="flex items-start space-x-3.5">
                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="text-xs text-amber-900 space-y-2">
                                <div>
                                    <div class="font-bold text-sm text-amber-950">Peringatan: Komposisi Resep Belum Lengkap (UC-14 Alt 6a)</div>
                                    <p class="text-amber-800 text-xs mt-0.5">
                                        Terdapat <strong>{{ $missingRecipes->count() }} menu</strong> dalam rencana produksi ini yang belum memiliki racikan resep bahan baku mentah. Kebutuhan bahan baku dapur belum dapat dihitung otomatis untuk menu tersebut.
                                    </p>
                                </div>
                                <div class="pt-1 flex flex-wrap gap-2">
                                    @foreach ($missingRecipes as $missing)
                                        @if ($missing->product)
                                            <a href="{{ route('products.recipe.edit', $missing->product) }}"
                                               class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 bg-amber-100 text-amber-900 rounded-xl border border-amber-300/80 hover:bg-amber-200 transition-colors shadow-xs">
                                                <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                                </svg>
                                                <span>Racik Resep: {{ $missing->product->name }} &rarr;</span>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Kartu Status Alur Permintaan Bahan ke Gudang (URUTAN 13) -->
                @php
                    $planPendingMR = $plan->materialRequests->where('status', 'pending')->first()
                        ?? $plan->materialRequests->where('status', 'Pending')->first();
                    $planApprovedMR = $plan->materialRequests->where('status', 'approved')->first()
                        ?? $plan->materialRequests->where('status', 'Approved')->first();
                @endphp
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6 transition-all">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="text-sm font-bold text-gray-900">Alur Permintaan Bahan Baku Gudang</h4>
                                    @if ($planPendingMR)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Menunggu Verifikasi Gudang (#MR-{{ str_pad($planPendingMR->id, 4, '0', STR_PAD_LEFT) }})
                                        </span>
                                    @elseif ($planApprovedMR)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Sudah Diserahkan &amp; Stok Keluar Dicatat (#MR-{{ str_pad($planApprovedMR->id, 4, '0', STR_PAD_LEFT) }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                            Belum Diajukan ke Gudang
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                    @if ($planPendingMR)
                                        Permintaan bahan telah terkirim ke modul Gudang. Petugas gudang sedang memeriksa ketersediaan stok fisik untuk diserahkan ke dapur.
                                    @elseif ($planApprovedMR)
                                        Bahan baku telah disetujui dan diserahkan oleh petugas gudang. Transaksi stok keluar (Stok OUT - BR-05) telah selesai dicatat.
                                    @else
                                        Dapur belum mengirimkan permintaan bahan baku resmi untuk rencana masak ini. Klik tombol di samping untuk mengajukan ke gudang secara otomatis berdasarkan racikan resep menu.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0">
                            @if ($planPendingMR)
                                <a href="{{ route('material-requests.show', $planPendingMR) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-xl transition shadow-xs">
                                    <span>Pantau Verifikasi Gudang</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </a>
                            @elseif ($planApprovedMR)
                                <a href="{{ route('material-requests.show', $planApprovedMR) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl transition shadow-xs">
                                    <span>Lihat Bukti Serah Terima</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </a>
                            @elseif (in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                                <form method="POST" action="{{ route('production-plans.material-requests.store', $plan) }}">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('Kirimkan daftar kebutuhan bahan rencana produksi ini ke bagian gudang?');"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-xl transition shadow-xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                        <span>Ajukan ke Gudang Sekarang</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Kartu Status Alur Realisasi Masak Dapur (URUTAN 14) -->
                @php
                    $planStatusVal = $plan->status instanceof \App\Enums\ProductionPlanStatus ? $plan->status->value : $plan->status;
                    $totPlanned = $plan->totalPlannedQuantity();
                    $totActual = $plan->totalActualQuantity();
                    $isPlanDone = $planStatusVal === 'completed';
                    $isPlanCooking = $planStatusVal === 'in_progress';
                    $realizePercent = $totPlanned > 0 ? min(100, round(($totActual / $totPlanned) * 100)) : 0;
                @endphp
                @if ($isPlanCooking || $isPlanDone)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6 transition-all">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-xl {{ $isPlanDone ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-purple-50 text-purple-600 border border-purple-100' }} flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="text-sm font-bold text-gray-900">Realisasi Masak Dapur</h4>
                                        @if ($isPlanDone)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Seluruh Menu Telah Selesai Dimasak ({{ $totActual }}/{{ $totPlanned }} Porsi)
                                            </span>
                                        @elseif ($totActual > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                Sedang Dimasak Parsial ({{ $totActual }}/{{ $totPlanned }} Porsi &bull; {{ $realizePercent }}%)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                                Belum Dicatat Realisasinya
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                        @if ($isPlanDone)
                                            Seluruh menu hidangan pada rencana masak ini telah selesai dimasak memenuhi target. Rencana produksi telah berstatus selesai.
                                        @elseif ($totActual > 0)
                                            Dapur telah mencatat sebagian hasil masakan matang. Masih ada menu yang belum memenuhi target penyelesaian.
                                        @else
                                            Rencana produksi berstatus Sedang Dimasak. Petugas dapur dapat menginput porsi matang aktual per menu.
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if ($isPlanCooking && in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                                <div class="shrink-0">
                                    <a href="{{ route('production-plans.realization.create', $plan) }}"
                                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl transition shadow-xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span>{{ $totActual > 0 ? 'Perbarui Realisasi Dapur' : 'Input Realisasi Masak' }} &rarr;</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- 4 Kartu Statistik Informasi Utama -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- 1. Tanggal Perencanaan -->
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between text-gray-400 mb-2">
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Tanggal Perencanaan</span>
                            <div class="w-7 h-7 rounded-lg bg-gray-50 text-gray-400 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <span class="text-base font-bold text-gray-900 block">
                                {{ $plan->plan_date ? $plan->plan_date->translatedFormat('d F Y') : '-' }}
                            </span>
                            <span class="text-[11px] text-gray-400 mt-0.5 block">Dibuat {{ $plan->created_at ? $plan->created_at->diffForHumans() : '-' }}</span>
                        </div>
                    </div>

                    <!-- 2. Jadwal Masak / Dapur -->
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between text-purple-600 mb-2">
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jadwal Masak / Dapur</span>
                            <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <span class="text-base font-bold text-purple-700 block">
                                {{ $plan->production_date ? $plan->production_date->translatedFormat('l, d F Y') : '-' }}
                            </span>
                            <span class="text-[11px] text-gray-400 mt-0.5 block">Target penyelesaian masak</span>
                        </div>
                    </div>

                    <!-- 3. Total Target Produksi -->
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between text-amber-600 mb-2">
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Total Target Produksi</span>
                            <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-2xl font-black text-gray-900 font-mono">{{ number_format($plan->totalPlannedQuantity(), 0, ',', '.') }}</span>
                                <span class="text-xs font-semibold text-gray-500">Porsi</span>
                            </div>
                            <span class="text-[11px] text-gray-400 mt-0.5 block">{{ $plan->details->count() }} menu hidangan</span>
                        </div>
                    </div>

                    <!-- 4. Penanggung Jawab -->
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between text-emerald-600 mb-2">
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Penanggung Jawab</span>
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <span class="text-base font-bold text-gray-900 block truncate">
                                {{ $plan->creator?->name ?? 'Sistem' }}
                            </span>
                            <span class="text-[11px] text-gray-400 mt-0.5 block">Role: {{ $plan->creator?->role?->value ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Catatan Operasional -->
                @if ($plan->notes)
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Catatan Operasional &amp; Instruksi Khusus</h4>
                        <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $plan->notes }}</p>
                    </div>
                @endif

                <!-- Tabel Detail Menu Rencana Produksi -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-5 sm:p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-gray-900 text-base">Rincian Menu Hidangan &amp; Target Porsi</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Daftar hidangan masakan yang harus diproduksi oleh tim dapur.</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200 self-start sm:self-auto">
                            {{ $plan->details->count() }} Menu Terdaftar
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50/75 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                <tr>
                                    <th scope="col" class="px-5 py-3.5 text-center w-12">No.</th>
                                    <th scope="col" class="px-5 py-3.5 text-left">Menu Makanan</th>
                                    <th scope="col" class="px-5 py-3.5 text-center">Status Resep Bahan</th>
                                    <th scope="col" class="px-5 py-3.5 text-right">Target Porsi</th>
                                    <th scope="col" class="px-5 py-3.5 text-left">Catatan Khusus</th>
                                    <th scope="col" class="px-5 py-3.5 text-right">Aksi Menu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 bg-white">
                                @foreach ($plan->details as $index => $detail)
                                    <tr class="hover:bg-gray-50/75 transition-colors">
                                        <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-400 font-mono text-center">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-5 py-4">
                                            @if ($detail->product)
                                                <a href="{{ route('products.show', $detail->product) }}" class="font-bold text-gray-900 hover:text-purple-600 transition-colors">
                                                    {{ $detail->product->name }}
                                                </a>
                                                <div class="text-xs text-gray-400 mt-0.5">
                                                    Rp {{ number_format($detail->product->selling_price, 0, ',', '.') }} / {{ $detail->product->unit }}
                                                </div>
                                            @else
                                                <span class="text-rose-500 italic text-xs">Menu tidak ditemukan (ID: {{ $detail->product_id }})</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 whitespace-nowrap text-center">
                                            @if ($detail->product && $detail->product->recipeItems->isNotEmpty())
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    ✓ {{ $detail->product->recipeItems->count() }} Bahan Terhubung
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                    Belum Ada Resep
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 whitespace-nowrap text-right font-mono font-bold text-purple-700">
                                            {{ number_format($detail->planned_quantity, 0, ',', '.') }}
                                            <span class="text-xs font-normal text-gray-500">porsi</span>
                                        </td>
                                        <td class="px-5 py-4 text-xs text-gray-600 max-w-xs truncate">
                                            {{ $detail->notes ?: '-' }}
                                        </td>
                                        <td class="px-5 py-4 whitespace-nowrap text-right text-xs">
                                            @if ($detail->product && in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                                                <a href="{{ route('products.recipe.edit', $detail->product) }}"
                                                   class="font-semibold text-purple-600 hover:text-purple-800 transition-colors">
                                                    Kelola Resep &rarr;
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50/75 border-t border-gray-200 font-semibold text-gray-800">
                                <tr>
                                    <td colspan="3" class="px-5 py-3.5 text-right text-xs uppercase tracking-wider text-gray-500">
                                        Total Porsi Keseluruhan:
                                    </td>
                                    <td class="px-5 py-3.5 text-right text-base text-purple-800 font-bold font-mono">
                                        {{ number_format($plan->totalPlannedQuantity(), 0, ',', '.') }} porsi
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- CARD KALKULASI LOGISTIK & STOK (FR-19) -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

                    <!-- Header Card Kalkulator Logistik -->
                    <div class="p-6 sm:p-8 border-b border-gray-100">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-5">
                            <div class="min-w-0">
                                <!-- Status Badges -->
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a1 1 0 001-1V6a1 1 0 00-1-1H4a1 1 0 00-1 1v12a1 1 0 001 1z" />
                                        </svg>
                                        <span>Kalkulator Logistik MRP</span>
                                    </span>

                                    @if ($calculation['summary']['shortage_count'] > 0)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Perlu Pengadaan &bull; {{ $calculation['summary']['shortage_count'] }} bahan kurang</span>
                                        </span>
                                    @elseif ($calculation['summary']['below_minimum_count'] > 0)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Restock Diperlukan &bull; {{ $calculation['summary']['below_minimum_count'] }} bahan di bawah minimum</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Kebutuhan Terpenuhi &amp; Stok Aman</span>
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-lg font-bold text-gray-900 leading-snug">
                                    Kalkulasi Kebutuhan Bahan Baku dan Ketersediaan Stok Gudang
                                </h3>
                                <p class="mt-1 text-xs text-gray-500 leading-relaxed max-w-3xl">
                                    Dihitung otomatis dari target porsi dikali takaran resep, diagregasikan antar-menu, dan dibandingkan dengan persediaan stok gudang.
                                </p>
                            </div>

                            <!-- Tombol Aksi Utama: Lihat & Cetak Lembar Kerja Bahan -->
                            <div class="shrink-0 pt-1 sm:pt-0">
                                <a href="{{ route('production-plans.ingredients', $plan) }}"
                                   class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs rounded-xl px-4 py-2.5 shadow-md shadow-purple-200 transition-all whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    <span>Lihat &amp; Cetak Lembar Kerja Bahan</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 6 Item Kartu Ringkasan Statistik -->
                    <div class="p-6 bg-gray-50/60 border-b border-gray-100">
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                            <!-- 1. Total Jenis Bahan -->
                            <div class="bg-white rounded-xl border border-gray-200/80 p-3.5 shadow-xs flex flex-col justify-between">
                                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Total Bahan</p>
                                <p class="mt-1 text-xl font-black font-mono text-gray-900 tabular-nums">
                                    {{ $calculation['summary']['total_ingredients'] }}
                                    <span class="text-xs font-normal text-gray-400 ml-0.5">item</span>
                                </p>
                            </div>

                            <!-- 2. Kebutuhan Terpenuhi -->
                            <div class="bg-white rounded-xl border border-emerald-100 p-3.5 shadow-xs flex flex-col justify-between bg-emerald-50/20">
                                <p class="text-[10px] font-semibold text-emerald-700 uppercase tracking-wider">Terpenuhi</p>
                                <p class="mt-1 text-xl font-black font-mono tabular-nums text-emerald-700">
                                    {{ $calculation['summary']['fulfilled_count'] }}
                                    <span class="text-xs font-normal text-emerald-500 ml-0.5">item</span>
                                </p>
                            </div>

                            <!-- 3. Kebutuhan Kurang -->
                            <div class="bg-white rounded-xl border {{ $calculation['summary']['shortage_count'] > 0 ? 'border-rose-200 bg-rose-50/30' : 'border-gray-200/80' }} p-3.5 shadow-xs flex flex-col justify-between">
                                <p class="text-[10px] font-semibold {{ $calculation['summary']['shortage_count'] > 0 ? 'text-rose-600' : 'text-gray-400' }} uppercase tracking-wider">Kurang</p>
                                <p class="mt-1 text-xl font-black font-mono tabular-nums {{ $calculation['summary']['shortage_count'] > 0 ? 'text-rose-700' : 'text-gray-900' }}">
                                    {{ $calculation['summary']['shortage_count'] }}
                                    <span class="text-xs font-normal {{ $calculation['summary']['shortage_count'] > 0 ? 'text-rose-400' : 'text-gray-400' }} ml-0.5">item</span>
                                </p>
                            </div>

                            <!-- 4. <= Batas Minimum -->
                            <div class="bg-white rounded-xl border {{ $calculation['summary']['below_minimum_count'] > 0 ? 'border-amber-200 bg-amber-50/30' : 'border-gray-200/80' }} p-3.5 shadow-xs flex flex-col justify-between">
                                <p class="text-[10px] font-semibold {{ $calculation['summary']['below_minimum_count'] > 0 ? 'text-amber-700' : 'text-gray-400' }} uppercase tracking-wider">&le; Min Stok</p>
                                <p class="mt-1 text-xl font-black font-mono tabular-nums {{ $calculation['summary']['below_minimum_count'] > 0 ? 'text-amber-700' : 'text-gray-900' }}">
                                    {{ $calculation['summary']['below_minimum_count'] }}
                                    <span class="text-xs font-normal {{ $calculation['summary']['below_minimum_count'] > 0 ? 'text-amber-400' : 'text-gray-400' }} ml-0.5">item</span>
                                </p>
                            </div>

                            <!-- 5. Kesiapan Produksi -->
                            <div class="bg-white rounded-xl border border-gray-200/80 p-3.5 shadow-xs flex flex-col justify-between">
                                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Kesiapan</p>
                                <div>
                                    @if ($calculation['summary']['production_readiness']['status'] === 'ready')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Siap Produksi
                                        </span>
                                    @elseif ($calculation['summary']['production_readiness']['status'] === 'procurement_needed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                            Perlu Pengadaan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                            Perlu Verifikasi
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- 6. Kondisi Stok Akhir -->
                            <div class="bg-white rounded-xl border border-gray-200/80 p-3.5 shadow-xs flex flex-col justify-between">
                                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Stok Akhir</p>
                                <div>
                                    @if ($calculation['summary']['inventory_condition']['status'] === 'safe')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Aman
                                        </span>
                                    @elseif ($calculation['summary']['inventory_condition']['status'] === 'restock_needed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-50 text-amber-900 border border-amber-200">
                                            Restock
                                        </span>
                                    @elseif ($calculation['summary']['inventory_condition']['status'] === 'deficit')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                            Defisit
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                            Perlu Verifikasi
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel Peringatan Operasional jika ada shortage / restock -->
                    @if ($calculation['summary']['has_shortage'])
                        <div class="px-6 sm:px-8 pt-5">
                            <div class="p-4 bg-rose-50/90 border border-rose-200 rounded-2xl flex items-start gap-3">
                                <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="text-xs text-rose-800">
                                    <p class="font-bold text-rose-950">Peringatan Kekurangan Kebutuhan Produksi (Perlu Pengadaan)</p>
                                    <p class="mt-0.5 leading-relaxed text-rose-700">
                                        Terdapat <strong>{{ $calculation['summary']['shortage_count'] }} jenis bahan baku</strong> dengan stok gudang yang tidak mencukupi kebutuhan produksi. Koordinasikan pengadaan dengan tim gudang sebelum jadwal produksi dimulai.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($calculation['summary']['has_restock_warning'] && ! $calculation['summary']['has_shortage'])
                        <div class="px-6 sm:px-8 pt-5">
                            <div class="p-4 bg-amber-50/90 border border-amber-200 rounded-2xl flex items-start gap-3">
                                <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="text-xs text-amber-900">
                                    <p class="font-bold text-amber-950">Peringatan Restock Persediaan Gudang (Batas Minimum Tercapai)</p>
                                    <p class="mt-0.5 leading-relaxed text-amber-700">
                                        Kebutuhan produksi terpenuhi. Namun proyeksi stok akhir pada <strong>{{ $calculation['summary']['below_minimum_count'] }} jenis bahan baku</strong> berada pada atau di bawah batas minimum stok. Segera jadwalkan pembelian ulang.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Tabel 10 Kolom Kebutuhan Bahan Baku -->
                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full text-sm divide-y divide-gray-100">
                            <thead class="bg-gray-50/75 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                <tr>
                                    <th scope="col" class="w-12 px-4 py-3.5 text-center">No.</th>
                                    <th scope="col" class="min-w-[200px] px-4 py-3.5 text-left">Bahan Baku &amp; Kategori</th>
                                    <th scope="col" class="min-w-[120px] px-3 py-3.5 text-right">Kebutuhan Total</th>
                                    <th scope="col" class="min-w-[110px] px-3 py-3.5 text-right">Stok Aktual</th>
                                    <th scope="col" class="min-w-[120px] px-3 py-3.5 text-right">Proyeksi Stok Akhir</th>
                                    <th scope="col" class="min-w-[120px] px-3 py-3.5 text-right">Kekurangan</th>
                                    <th scope="col" class="min-w-[110px] px-3 py-3.5 text-right">Minimum Stok</th>
                                    <th scope="col" class="min-w-[110px] px-3 py-3.5 text-center">Status Kebutuhan</th>
                                    <th scope="col" class="min-w-[130px] px-3 py-3.5 text-center">Status Stok Akhir</th>
                                    <th scope="col" class="w-20 px-3 py-3.5 text-center">Cek Fisik</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 bg-white">
                                @forelse ($calculation['items'] as $index => $item)
                                    <tr class="hover:bg-gray-50/60 transition-colors {{ $item['availability_status'] === 'shortage' ? 'bg-rose-50/20' : ($item['ending_stock_status'] === 'below_minimum' || $item['ending_stock_status'] === 'at_minimum' ? 'bg-amber-50/15' : '') }}">
                                        <!-- No -->
                                        <td class="px-4 py-3.5 text-center text-xs text-gray-400 font-mono whitespace-nowrap">
                                            {{ $index + 1 }}
                                        </td>
                                        <!-- Bahan & Kategori -->
                                        <td class="px-4 py-3.5">
                                            <p class="font-bold text-gray-900 text-sm leading-snug">{{ $item['ingredient_name'] }}</p>
                                            <p class="mt-0.5 text-xs text-gray-500">
                                                {{ $item['ingredient_category'] }}
                                                <span class="text-gray-300 mx-1">&bull;</span>
                                                <span class="font-mono text-gray-600 font-semibold">{{ $item['unit'] }}</span>
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
                                        <td class="px-3 py-3.5 text-right whitespace-nowrap font-mono font-bold text-gray-900 tabular-nums">
                                            {{ $item['formatted_required'] }}
                                        </td>
                                        <!-- Stok Aktual -->
                                        <td class="px-3 py-3.5 text-right whitespace-nowrap font-mono font-semibold text-gray-700 tabular-nums">
                                            {{ $item['formatted_current_stock'] }}
                                        </td>
                                        <!-- Proyeksi Stok Akhir -->
                                        <td class="px-3 py-3.5 text-right whitespace-nowrap font-mono font-bold tabular-nums">
                                            @if ($item['projected_ending_stock'] !== null)
                                                @if ($item['projected_ending_stock'] > $item['minimum_stock'])
                                                    <span class="text-emerald-700">{{ $item['formatted_projected_ending_stock'] }}</span>
                                                @elseif ($item['projected_ending_stock'] >= 0)
                                                    <span class="text-amber-700">{{ $item['formatted_projected_ending_stock'] }}</span>
                                                @else
                                                    <span class="text-rose-700">{{ $item['formatted_projected_ending_stock'] }}</span>
                                                @endif
                                            @else
                                                <span class="text-gray-400 font-normal">&mdash;</span>
                                            @endif
                                        </td>
                                        <!-- Kekurangan Produksi -->
                                        <td class="px-3 py-3.5 text-right whitespace-nowrap font-mono font-bold tabular-nums">
                                            @if ($item['production_shortage'] !== null && $item['production_shortage'] > 0)
                                                <span class="text-rose-700">{{ $item['formatted_production_shortage'] }}</span>
                                            @elseif ($item['production_shortage'] !== null)
                                                <span class="text-gray-400 font-normal">0 {{ $item['unit'] }}</span>
                                            @else
                                                <span class="text-gray-400 font-normal">&mdash;</span>
                                            @endif
                                        </td>
                                        <!-- Minimum Stok -->
                                        <td class="px-3 py-3.5 text-right whitespace-nowrap font-mono text-gray-500 tabular-nums">
                                            {{ $item['formatted_minimum_stock'] }}
                                        </td>
                                        <!-- Status Kebutuhan -->
                                        <td class="px-3 py-3.5 text-center whitespace-nowrap">
                                            @if ($item['availability_status'] === 'fulfilled')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">Terpenuhi</span>
                                            @elseif ($item['availability_status'] === 'shortage')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-800 border border-rose-200">Kurang</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">Perlu Verifikasi</span>
                                            @endif
                                        </td>
                                        <!-- Status Stok Akhir -->
                                        <td class="px-3 py-3.5 text-center whitespace-nowrap">
                                            @if ($item['ending_stock_status'] === 'safe')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">Aman</span>
                                            @elseif ($item['ending_stock_status'] === 'below_minimum')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">Di Bawah Min</span>
                                            @elseif ($item['ending_stock_status'] === 'at_minimum')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-100 text-amber-900 border border-amber-300">Tepat Min</span>
                                            @elseif ($item['ending_stock_status'] === 'deficit')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-800 border border-rose-200">Defisit</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">Perlu Verifikasi</span>
                                            @endif
                                        </td>
                                        <!-- Cek Fisik -->
                                        <td class="px-3 py-3.5 text-center whitespace-nowrap">
                                            <input type="checkbox"
                                                   class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500 cursor-pointer"
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

    <!-- Hidden Form Delete Laravel -->
    <form id="delete-production-plan-form" action="{{ route('production-plans.destroy', $plan) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <!-- Modal Delete Container (Native JS + Tailwind) -->
    <div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" onclick="if(event.target === this) closeDeleteModal()">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 text-center shadow-2xl border border-gray-100 transform transition-all">
            
            <!-- Icon Tong Sampah Melayang -->
            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center mx-auto mb-4 shadow-sm">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>

            <h3 class="text-lg font-bold text-gray-900 mb-1">Hapus Rencana Produksi</h3>
            <p class="text-xs text-rose-600 font-medium mb-3">Aksi ini tidak dapat dibatalkan</p>
            <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin menghapus rencana produksi <strong class="text-gray-900 font-semibold">{{ $plan->plan_number }}</strong>?</p>

            <div class="grid grid-cols-2 gap-3">
                <button type="button" onclick="closeDeleteModal()" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl transition-all cursor-pointer">
                    BATAL
                </button>
                <button type="button" onclick="submitDeleteForm()" class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-xl shadow-md shadow-rose-200 transition-all cursor-pointer">
                    YA, HAPUS RENCANA
                </button>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal() {
            var modal = document.getElementById('deleteModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
        }
        function closeDeleteModal() {
            var modal = document.getElementById('deleteModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }
        function submitDeleteForm() {
            var form = document.getElementById('delete-production-plan-form');
            if (form) {
                form.submit();
            }
        }
    </script>
</x-app-layout>