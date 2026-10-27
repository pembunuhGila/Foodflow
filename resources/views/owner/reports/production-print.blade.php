<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Produksi - FoodFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; color: #000; background: #fff; }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 p-6 min-h-screen">

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-sm print:shadow-none print:p-0">

        {{-- Floating Top Bar for Print Action --}}
        <div class="no-print mb-6 pb-4 border-b border-gray-200 flex items-center justify-between">
            <a href="{{ route('owner.reports.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">
                &larr; Kembali ke Pusat Laporan
            </a>
            <button onclick="window.print()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-sm transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Dokumen / Simpan PDF</span>
            </button>
        </div>

        {{-- KOP SURAT RESMI --}}
        <div class="border-b-2 border-gray-900 pb-4 mb-6 flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-gray-900">FOODFLOW CATERING & MBG</h1>
                <p class="text-xs text-gray-600 mt-0.5">Sistem Informasi Manajemen Persediaan & Produksi Masakan Sehat</p>
                <p class="text-[11px] text-gray-500">Jl. Soekarno Hatta No. 9, Malang, Jawa Timur | Telp: (0341) 555-888</p>
            </div>
            <div class="text-right">
                <span class="inline-block px-2.5 py-1 bg-gray-100 font-bold text-xs uppercase tracking-widest text-gray-700 rounded">
                    DOKUMEN RESMI
                </span>
                <p class="text-[11px] text-gray-500 mt-1">Dicetak: {{ $printedAt->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        {{-- JUDUL LAPORAN --}}
        <div class="text-center mb-6">
            <h2 class="text-lg font-bold text-gray-900 uppercase tracking-wide">LAPORAN REKAPITULASI CAPAIAN PRODUKSI MASAK</h2>
            <p class="text-xs text-gray-600 mt-1 font-medium">
                Periode: {{ \Carbon\Carbon::parse($fromDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($toDate)->translatedFormat('d F Y') }}
            </p>
        </div>

        {{-- RINGKASAN KPI --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                <span class="text-xs text-gray-500 font-medium">Total Rencana Masak</span>
                <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($totalPlanned) }} <span class="text-xs font-normal text-gray-500">porsi</span></p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                <span class="text-xs text-gray-500 font-medium">Total Realisasi Matang</span>
                <p class="text-xl font-bold text-blue-700 mt-1">{{ number_format($totalActual) }} <span class="text-xs font-normal text-gray-500">porsi</span></p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                <span class="text-xs text-gray-500 font-medium">Tingkat Capaian (Efisiensi)</span>
                <p class="text-xl font-bold text-emerald-700 mt-1">{{ $achievementRate }}%</p>
            </div>
        </div>

        {{-- TABEL DATA BATCH PRODUKSI --}}
        <div class="overflow-x-auto mb-8">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 text-xs">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-3 py-2 text-left font-bold uppercase">No.</th>
                        <th class="px-3 py-2 text-left font-bold uppercase">No. Rencana</th>
                        <th class="px-3 py-2 text-left font-bold uppercase">Tgl Masak</th>
                        <th class="px-3 py-2 text-left font-bold uppercase">Menu Hidangan</th>
                        <th class="px-3 py-2 text-right font-bold uppercase">Rencana</th>
                        <th class="px-3 py-2 text-right font-bold uppercase">Realisasi</th>
                        <th class="px-3 py-2 text-center font-bold uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($plans as $index => $plan)
                        <tr>
                            <td class="px-3 py-2 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 font-mono font-semibold">{{ $plan->plan_number }}</td>
                            <td class="px-3 py-2">{{ $plan->plan_date?->format('d/m/Y') }}</td>
                            <td class="px-3 py-2">
                                <ul class="list-disc list-inside">
                                    @foreach ($plan->details as $d)
                                        <li>{{ $d->product?->name }} ({{ $d->planned_quantity }} porsi)</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-3 py-2 text-right font-semibold">{{ number_format($plan->totalPlannedQuantity()) }}</td>
                            <td class="px-3 py-2 text-right font-bold text-blue-700">{{ number_format($plan->totalActualQuantity()) }}</td>
                            <td class="px-3 py-2 text-center">
                                <span class="uppercase font-semibold text-[10px]">{{ $plan->status instanceof \App\Enums\ProductionPlanStatus ? $plan->status->label() : ucfirst($plan->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-center text-gray-400">
                                Tidak ada jadwal produksi pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- KOLOM TANDA TANGAN --}}
        <div class="grid grid-cols-2 gap-8 pt-6 mt-8 border-t border-gray-200 text-center text-xs">
            <div>
                <p class="text-gray-600">Penanggung Jawab Produksi & Dapur,</p>
                <div class="h-20"></div>
                <p class="font-bold text-gray-900 border-b border-gray-400 inline-block px-8 pb-1">Mutiara Inayah M.</p>
                <p class="text-[11px] text-gray-500 mt-1">Divisi Operasional Masak</p>
            </div>
            <div>
                <p class="text-gray-600">Mengetahui & Menyetujui,</p>
                <div class="h-20"></div>
                <p class="font-bold text-gray-900 border-b border-gray-400 inline-block px-8 pb-1">Rachmad Zaki Setyawan</p>
                <p class="text-[11px] text-gray-500 mt-1">Owner / Direktur Operasional</p>
            </div>
        </div>

    </div>

</body>
</html>
