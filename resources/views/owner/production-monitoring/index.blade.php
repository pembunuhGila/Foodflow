<x-app-layout>
    @php
    $periods = ['today' => 'Hari Ini', 'this_week' => 'Minggu Ini', 'this_month' => 'Bulan Ini', 'all' => 'Semua'];
    $statusTabs = [
    '' => 'Semua', 'scheduled' => 'Dijadwalkan', 'in_progress' => 'Sedang Memasak',
    'completed' => 'Selesai', 'cancelled' => 'Dibatalkan',
    ];
    $activeStatus = ($currentStatus && $currentStatus !== 'all') ? $currentStatus : '';

    $tabs = [];
    foreach ($statusTabs as $value => $label) {
    $tabs[] = [
    'label' => $label,
    'href' => route('owner.production-monitoring.index', array_filter(['period' => $period, 'search' => $currentSearch, 'status' => $value])),
    'active' => $activeStatus === $value,
    ];
    }
    foreach ($tabs as $i => $t) {
    if ($t['active']) {
    $tabs[$i]['count'] = $plans->total();
    }
    }
    $hasFilter = $currentSearch || $activeStatus !== '' || $period !== 'today';
    @endphp

    <x-page-header eyebrow="PEMANTAUAN PRODUKSI" :title="__('Monitoring Capaian Produksi Dapur (Live)')"
        subtitle="Pemantauan realtime porsi hidangan matang, efisiensi realisasi dapur, dan komparasi pemakaian bahan baku riil.">
        <x-slot name="actions">
            {{-- Pilih periode waktu --}}
            <nav class="flex items-center gap-1 rounded-full border border-line bg-white p-1" aria-label="Periode waktu">
                @foreach ($periods as $key => $label)
                <a href="{{ route('owner.production-monitoring.index', array_merge(request()->except('period', 'page'), ['period' => $key])) }}"
                    @if ($period===$key) aria-current="page" @endif
                    class="whitespace-nowrap rounded-full px-4 py-1.5 text-small font-medium transition {{ $period === $key ? 'bg-primary text-white shadow-card' : 'text-ink hover:bg-primary/10' }}">
                    {{ $label }}
                </a>
                @endforeach
            </nav>
        </x-slot>
    </x-page-header>

    <div class="space-y-6">

        <!-- Ringkasan eksekutif -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            <x-card class="!p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="text-label uppercase text-muted">Capaian Porsi Masak</div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-input bg-info-bg text-primary"><x-icon name="clipboard" /></span>
                </div>
                <div class="mt-2 flex flex-wrap items-baseline gap-x-2">
                    <span class="font-display text-h1 text-ink">{{ number_format($totalActualPortions) }}</span>
                    <span class="text-small text-muted">/ {{ number_format($totalPlannedPortions) }} Porsi Target</span>
                </div>
                <div class="mt-3"><x-progress-bar :value="min(100, $overallEfficiencyRate)" :max="100" /></div>
                <div class="mt-1.5 flex items-center justify-between text-small text-muted">
                    <span>Efisiensi Realisasi:</span>
                    <span class="font-medium text-primary">{{ $overallEfficiencyRate }}%</span>
                </div>
            </x-card>

            <x-card class="!p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="text-label uppercase text-muted">Batch Produksi Aktif</div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-input bg-info-bg text-primary"><x-icon name="clock" /></span>
                </div>
                <div class="mt-2 flex flex-wrap items-baseline gap-x-2">
                    <span class="font-display text-h1 text-ink">{{ $activeBatchCount }}</span>
                    <span class="text-small text-muted">Batch Sedang Berjalan</span>
                </div>
                <div class="mt-4 flex items-center justify-between text-small text-muted">
                    <span>Batch Selesai:</span>
                    <span class="font-medium text-primary">{{ $completedBatchCount }} Batch</span>
                </div>
            </x-card>

            <x-card class="!p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="text-label uppercase text-muted">Efisiensi Bahan Riil</div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-input {{ $materialVariance > 0 ? 'bg-danger-bg text-danger-text' : 'bg-info-bg text-primary' }}"><x-icon name="package" /></span>
                </div>
                <div class="mt-2">
                    @if ($materialVariance < 0)
                        <div class="flex flex-wrap items-baseline gap-x-2 text-primary">
                        <span class="font-display text-h1">{{ number_format(abs($materialVariance), 2) }}</span>
                        <span class="text-small font-medium">Unit Lebih Hemat</span>
                </div>
                @elseif ($materialVariance > 0)
                <div class="flex flex-wrap items-baseline gap-x-2 text-danger-text">
                    <span class="font-display text-h1">+{{ number_format($materialVariance, 2) }}</span>
                    <span class="text-small font-medium">Unit Selisih Boros</span>
                </div>
                @else
                <div class="flex flex-wrap items-baseline gap-x-2 text-ink">
                    <span class="font-display text-h1">0.00</span>
                    <span class="text-small text-muted">Sesuai Resep Standar</span>
                </div>
                @endif
        </div>
        <div class="mt-4 flex items-center justify-between text-small text-muted">
            <span>Target: {{ number_format($totalPlannedMaterial, 1) }}</span>
            <span>Riil: {{ number_format($totalActualMaterial, 1) }}</span>
        </div>
        </x-card>

        <x-card class="!p-5 flex flex-col justify-between">
            <div>
                <div class="text-label uppercase text-muted">Layanan Terkait</div>
                <p class="mt-1 text-small text-muted">Laporan resmi siap cetak & evaluasi standar gizi menu MBG.</p>
            </div>
            <div class="mt-3 flex flex-col gap-2">
                <x-button variant="secondary" size="sm" :href="route('owner.reports.production')" target="_blank">
                    <x-icon name="printer" class="h-4 w-4" /> Cetak Laporan Produksi
                </x-button>
                <x-button variant="outline" size="sm" :href="route('owner.nutrition.index')">
                    <x-icon name="heart" class="h-4 w-4" /> Evaluasi Kepatuhan Gizi
                </x-button>
            </div>
        </x-card>
    </div>

    <!-- Filter -->
    <x-filter-bar :action="route('owner.production-monitoring.index')" :search="$currentSearch ?? ''"
        placeholder="Cari nomor batch / pesanan, contoh: PP-20261015-0001"
        :tabs="$tabs" :hidden="['period' => $period, 'status' => $activeStatus]">
        @if ($hasFilter)
        <a href="{{ route('owner.production-monitoring.index') }}" class="text-small font-medium text-primary hover:text-primary-dark">Reset filter</a>
        @endif
    </x-filter-bar>

    <!-- Tabel pemantauan batch -->
    <x-card :padding="false" class="overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line px-6 py-4">
            <div>
                <h3 class="font-display text-h3 text-ink">Daftar Pemantauan Batch Produksi Dapur</h3>
                <p class="mt-0.5 text-small text-muted">Memantau kemajuan porsi masakan dan selisih bahan riil dapur secara transparan.</p>
            </div>
            <span class="text-small font-medium text-muted">
                Menampilkan {{ $plans->firstItem() ?? 0 }}-{{ $plans->lastItem() ?? 0 }} dari {{ $plans->total() }} Rencana
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-body">
                <thead class="bg-primary/5">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Nomor Batch & Referensi</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Jadwal Masak</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Hidangan & Capaian Porsi</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Status Dapur</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Efisiensi Bahan Riil</th>
                        <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line bg-white">
                    @forelse ($plans as $plan)
                    @php
                    $plannedPortions = $plan->totalPlannedQuantity();
                    $actualPortions = $plan->totalActualQuantity();
                    $progressPercent = $plannedPortions > 0 ? round(($actualPortions / $plannedPortions) * 100) : 0;
                    $usagesCount = $plan->materialUsages->count();
                    $netVariance = (float) $plan->materialUsages->sum('difference_quantity');
                    $statusVal = is_object($plan->status) ? $plan->status->value : (string) $plan->status;
                    $statusMap = [
                    'completed' => ['success', 'Selesai (Completed)'],
                    'in_progress' => ['warning', 'Sedang Memasak'],
                    'scheduled' => ['info', 'Dijadwalkan'],
                    'cancelled' => ['danger', 'Dibatalkan'],
                    ];
                    [$statusVariant, $statusLabel] = $statusMap[$statusVal] ?? ['neutral', 'Draft'];
                    @endphp
                    <tr class="align-top transition hover:bg-primary/5">
                        <!-- Nomor batch & order -->
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="font-medium text-ink">{{ $plan->plan_number }}</div>
                            @if ($plan->cateringOrder)
                            <div class="mt-0.5 text-small text-muted">
                                Order: <span class="font-medium text-ink">{{ $plan->cateringOrder->order_number }}</span>
                                @if ($plan->cateringOrder->customer)
                                <span class="block max-w-xs truncate">{{ $plan->cateringOrder->customer->name }}</span>
                                @endif
                            </div>
                            @else
                            <span class="text-small italic text-muted">Rencana Reguler Mandiri</span>
                            @endif
                        </td>

                        <!-- Jadwal -->
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="font-medium text-ink">{{ $plan->production_date ? $plan->production_date->translatedFormat('d F Y') : '-' }}</div>
                            <div class="mt-0.5 text-small text-muted">Dibuat: {{ $plan->plan_date ? $plan->plan_date->format('d/m/Y') : '-' }}</div>
                        </td>

                        <!-- Hidangan & capaian -->
                        <td class="px-6 py-4">
                            <div class="max-w-md space-y-2.5">
                                @foreach ($plan->details as $detail)
                                @php
                                $itemActual = (int) ($detail->production?->actual_quantity ?? 0);
                                $itemPlanned = (int) $detail->planned_quantity;
                                @endphp
                                <div>
                                    <div class="flex justify-between gap-3 text-small">
                                        <span class="font-medium text-ink">{{ $detail->product?->name ?? 'Menu' }}</span>
                                        <span class="shrink-0 text-muted">{{ number_format($itemActual) }} / {{ number_format($itemPlanned) }} porsi</span>
                                    </div>
                                    <div class="mt-1"><x-progress-bar :value="$itemActual" :max="$itemPlanned" /></div>
                                </div>
                                @endforeach
                                <div class="flex justify-between gap-3 border-t border-line pt-2 text-small font-medium text-primary">
                                    <span>Total Realisasi:</span>
                                    <span>{{ number_format($actualPortions) }} / {{ number_format($plannedPortions) }} Porsi ({{ $progressPercent }}%)</span>
                                </div>
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="whitespace-nowrap px-6 py-4">
                            <x-badge :variant="$statusVariant"><span class="h-1.5 w-1.5 rounded-full bg-current"></span> {{ $statusLabel }}</x-badge>
                        </td>

                        <!-- Efisiensi bahan -->
                        <td class="whitespace-nowrap px-6 py-4">
                            @if ($usagesCount > 0)
                            @if ($netVariance < 0)
                                <x-badge variant="success">Hemat {{ number_format(abs($netVariance), 2) }} Unit</x-badge>
                                @elseif ($netVariance > 0)
                                <x-badge variant="danger">Boros +{{ number_format($netVariance, 2) }} Unit</x-badge>
                                @else
                                <x-badge variant="neutral">Presisi Sesuai Resep</x-badge>
                                @endif
                                <div class="mt-1 text-small text-muted">{{ $usagesCount }} Jenis Bahan Dicatat</div>
                                @else
                                <span class="text-small italic text-muted">Belum Dicatat</span>
                                @endif
                        </td>

                        <!-- Aksi (teks) -->
                        <td class="whitespace-nowrap px-6 py-4 text-right text-small font-medium">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('production-plans.ingredients', $plan) }}" title="Lihat Lembar Kerja Resep Dapur" class="text-primary transition hover:text-primary-dark">Lembar Resep</a>
                                <a href="{{ route('production-plans.material-usages.show', $plan) }}" title="Lihat Rincian Pemakaian Bahan Riil" class="text-primary transition hover:text-primary-dark">Pemakaian Bahan</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-muted">
                            <div class="flex flex-col items-center justify-center">
                                <x-icon name="clipboard" class="mb-3 h-10 w-10 text-muted/50" />
                                <p class="font-medium text-ink">Tidak ada data produksi untuk kriteria ini</p>
                                <p class="mt-1 text-small text-muted">Coba ubah filter periode waktu atau status di atas.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($plans->hasPages())
        <div class="border-t border-line bg-primary/5 px-6 py-4">
            {{ $plans->links() }}
        </div>
        @endif
    </x-card>
    </div>
</x-app-layout>