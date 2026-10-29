<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                        FR-37 & UC-10
                    </span>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        {{ __('Monitoring Capaian Produksi Dapur (Live)') }}
                    </h2>
                </div>
                <p class="text-sm text-gray-600 mt-1">
                    Pemantauan realtime porsi hidangan matang, efisiensi realisasi dapur, dan komparasi pemakaian bahan baku riil.
                </p>
            </div>

            <!-- Periode Waktu Selector -->
            <div class="inline-flex rounded-lg bg-gray-100 p-1 shadow-inner text-sm font-medium">
                <a href="{{ route('owner.production-monitoring.index', array_merge(request()->except('period', 'page'), ['period' => 'today'])) }}"
                   class="px-3 py-1.5 rounded-md transition-all {{ $period === 'today' ? 'bg-white text-purple-700 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900' }}">
                    Hari Ini
                </a>
                <a href="{{ route('owner.production-monitoring.index', array_merge(request()->except('period', 'page'), ['period' => 'this_week'])) }}"
                   class="px-3 py-1.5 rounded-md transition-all {{ $period === 'this_week' ? 'bg-white text-purple-700 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900' }}">
                    Minggu Ini
                </a>
                <a href="{{ route('owner.production-monitoring.index', array_merge(request()->except('period', 'page'), ['period' => 'this_month'])) }}"
                   class="px-3 py-1.5 rounded-md transition-all {{ $period === 'this_month' ? 'bg-white text-purple-700 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900' }}">
                    Bulan Ini
                </a>
                <a href="{{ route('owner.production-monitoring.index', array_merge(request()->except('period', 'page'), ['period' => 'all'])) }}"
                   class="px-3 py-1.5 rounded-md transition-all {{ $period === 'all' ? 'bg-white text-purple-700 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900' }}">
                    Semua
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Ringkasan Eksekutif (Executive Summary Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Card 1: Total Realisasi Porsi vs Target -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl p-5 border-l-4 border-purple-600">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Capaian Porsi Masak</span>
                        <span class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-2xl font-extrabold text-gray-900">{{ number_format($totalActualPortions) }}</span>
                        <span class="text-sm font-medium text-gray-500">/ {{ number_format($totalPlannedPortions) }} Porsi Target</span>
                    </div>
                    <div class="mt-3">
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, $overallEfficiencyRate) }}%"></div>
                        </div>
                        <div class="flex justify-between items-center text-xs text-gray-500 mt-1.5 font-medium">
                            <span>Efisiensi Realisasi:</span>
                            <span class="font-bold text-purple-700">{{ $overallEfficiencyRate }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Batch Dapur Berjalan -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl p-5 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Batch Produksi Aktif</span>
                        <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    </div>
                    <div class="mt-3">
                        <span class="text-2xl font-extrabold text-gray-900">{{ $activeBatchCount }}</span>
                        <span class="text-sm font-medium text-gray-500"> Batch Sedang Berjalan</span>
                    </div>
                    <div class="mt-4 text-xs text-gray-500 flex items-center justify-between">
                        <span>Batch Selesai:</span>
                        <span class="font-bold text-emerald-600">{{ $completedBatchCount }} Batch</span>
                    </div>
                </div>

                <!-- Card 3: Variansi Bahan Baku Riil (FR-35) -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl p-5 border-l-4 {{ $materialVariance > 0 ? 'border-amber-500' : 'border-emerald-500' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Efisiensi Bahan Riil</span>
                        <span class="p-2 {{ $materialVariance > 0 ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600' }} rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 18h12l3-18H3z" />
                            </svg>
                        </span>
                    </div>
                    <div class="mt-3">
                        @if ($materialVariance < 0)
                            <div class="flex items-baseline gap-1 text-emerald-600">
                                <span class="text-2xl font-extrabold">{{ number_format(abs($materialVariance), 2) }}</span>
                                <span class="text-sm font-semibold">Unit Lebih Hemat</span>
                            </div>
                        @elseif ($materialVariance > 0)
                            <div class="flex items-baseline gap-1 text-amber-600">
                                <span class="text-2xl font-extrabold">+{{ number_format($materialVariance, 2) }}</span>
                                <span class="text-sm font-semibold">Unit Selisih Boros</span>
                            </div>
                        @else
                            <div class="flex items-baseline gap-1 text-gray-700">
                                <span class="text-2xl font-extrabold">0.00</span>
                                <span class="text-sm font-medium">Sesuai Resep Standar</span>
                            </div>
                        @endif
                    </div>
                    <div class="mt-4 text-xs text-gray-500 flex items-center justify-between">
                        <span>Target: {{ number_format($totalPlannedMaterial, 1) }}</span>
                        <span>Riil: {{ number_format($totalActualMaterial, 1) }}</span>
                    </div>
                </div>

                <!-- Card 4: Pintasan Cepat Eksekutif -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl p-5 border-l-4 border-indigo-500 flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Layanan Terkait</span>
                        <p class="text-xs text-gray-600 mt-1">Laporan resmi siap cetak & evaluasi standar gizi menu MBG.</p>
                    </div>
                    <div class="mt-3 flex flex-col gap-2">
                        <a href="{{ route('owner.reports.production') }}" target="_blank"
                           class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Laporan Produksi
                        </a>
                        <a href="{{ route('owner.nutrition.index') }}"
                           class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg bg-purple-50 text-purple-700 hover:bg-purple-100 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Evaluasi Kepatuhan Gizi
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="bg-white shadow-sm rounded-xl p-5">
                <form method="GET" action="{{ route('owner.production-monitoring.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <input type="hidden" name="period" value="{{ $period }}">

                    <div class="md:col-span-5">
                        <label for="search" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Pencarian Nomor Batch / Pesanan
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" id="search" name="search" value="{{ $currentSearch }}"
                                   placeholder="Contoh: PP-20261015-0001, ORD-..."
                                   class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-purple-500 focus:border-purple-500">
                        </div>
                    </div>

                    <div class="md:col-span-4">
                        <label for="status" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Status Produksi Dapur
                        </label>
                        <select id="status" name="status" class="block w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-purple-500 focus:border-purple-500">
                            <option value="all">Semua Status Operasional</option>
                            <option value="scheduled" {{ $currentStatus === 'scheduled' ? 'selected' : '' }}>Scheduled (Dijadwalkan)</option>
                            <option value="in_progress" {{ $currentStatus === 'in_progress' ? 'selected' : '' }}>In Progress (Sedang Memasak)</option>
                            <option value="completed" {{ $currentStatus === 'completed' ? 'selected' : '' }}>Completed (Selesai Penuh)</option>
                            <option value="cancelled" {{ $currentStatus === 'cancelled' ? 'selected' : '' }}>Cancelled (Dibatalkan)</option>
                        </select>
                    </div>

                    <div class="md:col-span-3 flex gap-2">
                        <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-purple-700 hover:bg-purple-800 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                            Terapkan Filter
                        </button>
                        @if ($currentSearch || ($currentStatus && $currentStatus !== 'all') || $period !== 'today')
                            <a href="{{ route('owner.production-monitoring.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabel Pemantauan Realtime Batch Produksi Dapur -->
            <div class="bg-white shadow-sm rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Daftar Pemantauan Batch Produksi Dapur</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Memantau kemajuan porsi masakan dan selisih bahan riil dapur secara transparan.</p>
                    </div>
                    <span class="text-xs font-semibold text-gray-500">
                        Menampilkan {{ $plans->firstItem() ?? 0 }}-{{ $plans->lastItem() ?? 0 }} dari {{ $plans->total() }} Rencana
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left">Nomor Batch & Referensi</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Jadwal Masak</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Hidangan & Capaian Porsi</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Status Dapur</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Efisiensi Bahan Riil</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($plans as $plan)
                                @php
                                    $plannedPortions = $plan->totalPlannedQuantity();
                                    $actualPortions = $plan->totalActualQuantity();
                                    $progressPercent = $plannedPortions > 0 ? round(($actualPortions / $plannedPortions) * 100) : 0;
                                    $usagesCount = $plan->materialUsages->count();
                                    $netVariance = (float) $plan->materialUsages->sum('difference_quantity');
                                @endphp
                                <tr class="hover:bg-gray-50/75 transition-colors">
                                    <!-- Nomor Batch & Order -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-purple-900">{{ $plan->plan_number }}</div>
                                        @if ($plan->cateringOrder)
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                Order: <span class="font-medium text-gray-700">{{ $plan->cateringOrder->order_number }}</span>
                                                @if ($plan->cateringOrder->customer)
                                                    <span class="block text-gray-400 truncate max-w-xs">{{ $plan->cateringOrder->customer->name }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Rencana Reguler Mandiri</span>
                                        @endif
                                    </td>

                                    <!-- Jadwal Masak -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">
                                            {{ $plan->production_date ? $plan->production_date->translatedFormat('d F Y') : '-' }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            Dibuat: {{ $plan->plan_date ? $plan->plan_date->format('d/m/Y') : '-' }}
                                        </div>
                                    </td>

                                    <!-- Hidangan & Capaian Porsi -->
                                    <td class="px-6 py-4">
                                        <div class="space-y-2 max-w-md">
                                            @foreach ($plan->details as $detail)
                                                @php
                                                    $itemActual = (int) ($detail->production?->actual_quantity ?? 0);
                                                    $itemPlanned = (int) $detail->planned_quantity;
                                                    $itemPercent = $itemPlanned > 0 ? round(($itemActual / $itemPlanned) * 100) : 0;
                                                @endphp
                                                <div>
                                                    <div class="flex justify-between text-xs font-medium text-gray-800">
                                                        <span>{{ $detail->product?->name ?? 'Menu' }}</span>
                                                        <span class="text-gray-600">{{ number_format($itemActual) }} / {{ number_format($itemPlanned) }} porsi</span>
                                                    </div>
                                                    <div class="w-full bg-gray-100 rounded-full h-1.5 mt-1">
                                                        <div class="bg-purple-600 h-1.5 rounded-full" style="width: {{ min(100, $itemPercent) }}%"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div class="text-xs font-semibold text-purple-700 pt-1 border-t border-gray-100 flex justify-between">
                                                <span>Total Realisasi:</span>
                                                <span>{{ number_format($actualPortions) }} / {{ number_format($plannedPortions) }} Porsi ({{ $progressPercent }}%)</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Status Dapur -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $statusVal = is_object($plan->status) ? $plan->status->value : (string) $plan->status;
                                        @endphp
                                        @if ($statusVal === 'completed')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                ● Selesai (Completed)
                                            </span>
                                        @elseif ($statusVal === 'in_progress')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                                ● Sedang Memasak
                                            </span>
                                        @elseif ($statusVal === 'scheduled')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                ● Dijadwalkan
                                            </span>
                                        @elseif ($statusVal === 'cancelled')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                ● Dibatalkan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                                ● Draft
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Efisiensi Bahan Riil (FR-35) -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($usagesCount > 0)
                                            <div>
                                                @if ($netVariance < 0)
                                                    <span class="inline-flex items-center text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">
                                                        Hemat {{ number_format(abs($netVariance), 2) }} Unit
                                                    </span>
                                                @elseif ($netVariance > 0)
                                                    <span class="inline-flex items-center text-xs font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded">
                                                        Boros +{{ number_format($netVariance, 2) }} Unit
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center text-xs font-semibold text-gray-600 bg-gray-100 px-2 py-0.5 rounded">
                                                        Presisi Sesuai Resep
                                                    </span>
                                                @endif
                                                <div class="text-xs text-gray-400 mt-1">
                                                    {{ $usagesCount }} Jenis Bahan Dicatat
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum Dicatat</span>
                                        @endif
                                    </td>

                                    <!-- Aksi -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-xs font-medium">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Lembar Kerja Resep -->
                                            <a href="{{ route('production-plans.ingredients', $plan) }}"
                                               title="Lihat Lembar Kerja Resep Dapur"
                                               class="p-1.5 text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </a>
                                            <!-- Pemakaian Bahan Riil -->
                                            <a href="{{ route('production-plans.material-usages.show', $plan) }}"
                                               title="Lihat Rincian Pemakaian Bahan Riil"
                                               class="p-1.5 text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <p class="font-bold text-gray-700 text-base">Tidak ada data produksi untuk kriteria ini</p>
                                            <p class="text-xs text-gray-400 mt-1">Coba ubah filter periode waktu atau status di atas.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($plans->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $plans->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
