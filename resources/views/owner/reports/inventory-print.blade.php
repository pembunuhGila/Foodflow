<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Mutasi Persediaan Bahan Gudang - FoodFlow</title>
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
            <button onclick="window.print()" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-sm transition flex items-center space-x-2">
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
            <h2 class="text-lg font-bold text-gray-900 uppercase tracking-wide">LAPORAN MUTASI & PERSEDIAAN BAHAN BAKU GUDANG</h2>
            <p class="text-xs text-gray-600 mt-1 font-medium">
                Periode: {{ \Carbon\Carbon::parse($fromDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($toDate)->translatedFormat('d F Y') }}
            </p>
        </div>

        {{-- TABEL MUTASI BAHAN BAKU --}}
        <div class="overflow-x-auto mb-8">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 text-xs">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-3 py-2 text-left font-bold uppercase">No.</th>
                        <th class="px-3 py-2 text-left font-bold uppercase">Bahan Baku</th>
                        <th class="px-3 py-2 text-left font-bold uppercase">Satuan</th>
                        <th class="px-3 py-2 text-right font-bold uppercase text-emerald-800">Total Masuk (IN)</th>
                        <th class="px-3 py-2 text-right font-bold uppercase text-rose-800">Total Keluar (OUT)</th>
                        <th class="px-3 py-2 text-right font-bold uppercase text-blue-800">Penyesuaian (Adj)</th>
                        <th class="px-3 py-2 text-right font-bold uppercase text-gray-900">Saldo Akhir Saat Ini</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($reportData as $index => $row)
                        @php
                            $ing = $row['ingredient'];
                        @endphp
                        <tr>
                            <td class="px-3 py-2 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 font-semibold text-gray-900">{{ $ing->name }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ $ing->unit }}</td>
                            <td class="px-3 py-2 text-right font-mono font-semibold text-emerald-700">
                                {{ $row['total_in'] > 0 ? '+' . number_format($row['total_in'], 2, ',', '.') : '—' }}
                            </td>
                            <td class="px-3 py-2 text-right font-mono font-semibold text-rose-700">
                                {{ $row['total_out'] > 0 ? '-' . number_format($row['total_out'], 2, ',', '.') : '—' }}
                            </td>
                            <td class="px-3 py-2 text-right font-mono font-semibold text-blue-700">
                                {{ $row['total_adj'] != 0 ? ($row['total_adj'] > 0 ? '+' : '') . number_format($row['total_adj'], 2, ',', '.') : '—' }}
                            </td>
                            <td class="px-3 py-2 text-right font-mono font-bold text-gray-900">
                                {{ number_format($row['current_stock'], 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-center text-gray-400">
                                Tidak ada data bahan baku yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- KOLOM TANDA TANGAN --}}
        <div class="grid grid-cols-2 gap-8 pt-6 mt-8 border-t border-gray-200 text-center text-xs">
            <div>
                <p class="text-gray-600">Penanggung Jawab Gudang & Logistik,</p>
                <div class="h-20"></div>
                <p class="font-bold text-gray-900 border-b border-gray-400 inline-block px-8 pb-1">Yola Nur Aida</p>
                <p class="text-[11px] text-gray-500 mt-1">Divisi Persediaan & Gudang</p>
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
