<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Produksi - FoodFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                font-size: 12px;
                color: #000;
                background: #fff !important;
            }

            .page-break {
                page-break-after: always;
            }

            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-canvas p-6 font-sans text-ink">

    <div class="mx-auto max-w-4xl rounded-card bg-white p-8 shadow-card print:rounded-none print:p-0 print:shadow-none">

        {{-- Bilah atas (tidak ikut tercetak) --}}
        <div class="no-print mb-6 flex items-center justify-between border-b border-line pb-4">
            <a href="{{ route('owner.reports.index') }}" class="text-body font-medium text-primary hover:text-primary-dark">
                &larr; Kembali ke Pusat Laporan
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-full bg-primary px-5 py-2.5 text-body font-medium text-white shadow-card transition hover:bg-primary-dark">
                <x-icon name="printer" class="h-4 w-4" />
                <span>Cetak Dokumen / Simpan PDF</span>
            </button>
        </div>

        {{-- KOP SURAT RESMI --}}
        <div class="mb-6 flex items-start justify-between border-b-2 border-ink pb-4">
            <div>
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink">FOODFLOW CATERING &amp; MBG</h1>
                <p class="mt-0.5 text-small text-muted">Sistem Informasi Manajemen Persediaan &amp; Produksi Masakan Sehat</p>
                <p class="text-small text-muted">Jl. Soekarno Hatta No. 9, Malang, Jawa Timur | Telp: (0341) 555-888</p>
            </div>
            <div class="text-right">
                <span class="inline-block rounded-full bg-primary/10 px-3 py-1 text-label uppercase text-primary">DOKUMEN RESMI</span>
                <p class="mt-1 text-small text-muted">Dicetak: {{ $printedAt->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        {{-- JUDUL LAPORAN --}}
        <div class="mb-6 text-center">
            <h2 class="font-display text-h3 uppercase tracking-wide text-ink">LAPORAN REKAPITULASI CAPAIAN PRODUKSI MASAK</h2>
            <p class="mt-1 text-small font-medium text-muted">
                Periode: {{ \Carbon\Carbon::parse($fromDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($toDate)->translatedFormat('d F Y') }}
            </p>
        </div>

        {{-- RINGKASAN KPI --}}
        <div class="mb-6 grid grid-cols-3 gap-4">
            <div class="rounded-input border border-line bg-primary/5 p-4 text-center">
                <span class="text-label uppercase text-muted">Total Rencana Masak</span>
                <p class="mt-1 font-display text-h2 text-ink">{{ number_format($totalPlanned) }} <span class="text-small font-normal text-muted">porsi</span></p>
            </div>
            <div class="rounded-input border border-line bg-primary/5 p-4 text-center">
                <span class="text-label uppercase text-muted">Total Realisasi Matang</span>
                <p class="mt-1 font-display text-h2 text-ink">{{ number_format($totalActual) }} <span class="text-small font-normal text-muted">porsi</span></p>
            </div>
            <div class="rounded-input border border-line bg-primary/5 p-4 text-center">
                <span class="text-label uppercase text-muted">Tingkat Capaian (Efisiensi)</span>
                <p class="mt-1 font-display text-h2 text-primary">{{ $achievementRate }}%</p>
            </div>
        </div>

        {{-- TABEL DATA BATCH PRODUKSI --}}
        <div class="mb-8 overflow-x-auto">
            <table class="min-w-full border border-line text-small">
                <thead class="bg-primary/5 text-ink">
                    <tr>
                        <th class="px-3 py-2 text-left text-label uppercase">No.</th>
                        <th class="px-3 py-2 text-left text-label uppercase">No. Rencana</th>
                        <th class="px-3 py-2 text-left text-label uppercase">Tgl Masak</th>
                        <th class="px-3 py-2 text-left text-label uppercase">Menu Hidangan</th>
                        <th class="px-3 py-2 text-right text-label uppercase">Rencana</th>
                        <th class="px-3 py-2 text-right text-label uppercase">Realisasi</th>
                        <th class="px-3 py-2 text-center text-label uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($plans as $index => $plan)
                    <tr>
                        <td class="px-3 py-2 text-muted">{{ $index + 1 }}</td>
                        <td class="px-3 py-2 font-medium text-ink">{{ $plan->plan_number }}</td>
                        <td class="px-3 py-2">{{ $plan->plan_date?->format('d/m/Y') }}</td>
                        <td class="px-3 py-2">
                            <ul class="list-inside list-disc">
                                @foreach ($plan->details as $d)
                                <li>{{ $d->product?->name }} ({{ $d->planned_quantity }} porsi)</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-3 py-2 text-right tabular-nums">{{ number_format($plan->totalPlannedQuantity()) }}</td>
                        <td class="px-3 py-2 text-right font-semibold tabular-nums text-ink">{{ number_format($plan->totalActualQuantity()) }}</td>
                        <td class="px-3 py-2 text-center">
                            <span class="text-label uppercase">{{ $plan->status instanceof \App\Enums\ProductionPlanStatus ? $plan->status->label() : ucfirst($plan->status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-3 py-6 text-center text-muted">
                            Tidak ada jadwal produksi pada rentang tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- KOLOM TANDA TANGAN --}}
        <div class="mt-8 grid grid-cols-2 gap-8 border-t border-line pt-6 text-center text-small">
            <div>
                <p class="text-muted">Penanggung Jawab Produksi &amp; Dapur,</p>
                <div class="h-20"></div>
                <p class="inline-block border-b border-muted px-8 pb-1 font-semibold text-ink">Mutiara Inayah M.</p>
                <p class="mt-1 text-small text-muted">Divisi Operasional Masak</p>
            </div>
            <div>
                <p class="text-muted">Mengetahui &amp; Menyetujui,</p>
                <div class="h-20"></div>
                <p class="inline-block border-b border-muted px-8 pb-1 font-semibold text-ink">Rachmad Zaki Setyawan</p>
                <p class="mt-1 text-small text-muted">Owner / Direktur Operasional</p>
            </div>
        </div>

    </div>

</body>

</html>