<x-app-layout>
    @php
    $reports = [
    [
    'icon' => 'bar',
    'title' => 'Laporan Rekapitulasi Produksi',
    'desc' => 'Merekap target rencana porsi masakan dapur vs realisasi matang aktual, persentase ketercapaian efisiensi, dan daftar batch produksi yang diselesaikan.',
    'route' => 'owner.reports.production',
    'button' => 'Buka & Cetak Laporan Produksi (PDF)',
    ],
    [
    'icon' => 'package',
    'title' => 'Laporan Mutasi Persediaan Bahan Gudang',
    'desc' => 'Merekapitulasi seluruh mutasi barang masuk (IN), mutasi pengeluaran ke dapur (OUT), koreksi fisik (Adjustment), serta saldo sisa per bahan baku.',
    'route' => 'owner.reports.inventory',
    'button' => 'Buka & Cetak Laporan Persediaan (PDF)',
    ],
    ];
    @endphp

    <x-page-header eyebrow="Laporan" :title="__('Pusat Cetak Laporan Resmi Eksekutif (Owner)')">
        <x-slot name="actions">
            <x-button variant="outline" :href="route('owner.nutrition.index')">&larr; Evaluasi Gizi Menu</x-button>
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        @foreach ($reports as $i => $report)
        <x-card class="flex flex-col justify-between gap-5 sm:p-8">
            <div>
                <span class="mb-4 flex h-12 w-12 items-center justify-center rounded-input bg-info-bg text-primary">
                    <x-icon :name="$report['icon']" class="h-6 w-6" />
                </span>
                <h3 class="font-display text-h3 text-ink">{{ $report['title'] }}</h3>
                <p class="mt-1.5 text-body leading-relaxed text-muted">{{ $report['desc'] }}</p>
            </div>

            <form method="GET" action="{{ route($report['route']) }}" target="_blank" class="space-y-4 border-t border-line pt-5">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="from_date_{{ $i }}" value="Dari Tanggal" />
                        <x-text-input type="date" name="from_date" id="from_date_{{ $i }}" :value="date('Y-m-01')" class="mt-1.5 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="to_date_{{ $i }}" value="Sampai Tanggal" />
                        <x-text-input type="date" name="to_date" id="to_date_{{ $i }}" :value="date('Y-m-t')" class="mt-1.5 block w-full" />
                    </div>
                </div>

                <x-button type="submit" class="w-full">
                    <x-icon name="printer" class="h-4 w-4" />
                    {{ $report['button'] }}
                </x-button>
            </form>
        </x-card>
        @endforeach
    </div>
</x-app-layout>