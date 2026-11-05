<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Mutasi Persediaan Bahan Gudang - FoodFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                font-size: 11px;
                color: #000;
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .print-container {
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            thead {
                display: table-header-group;
            }

            tr {
                page-break-inside: avoid;
            }

            .avoid-break {
                page-break-inside: avoid;
            }

            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-canvas p-6 font-sans text-ink">

    <div class="print-container mx-auto max-w-4xl rounded-card bg-white p-8 shadow-card print:rounded-none print:p-0 print:shadow-none">

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
            <h2 class="font-display text-h3 uppercase tracking-wide text-ink">LAPORAN MUTASI &amp; PERSEDIAAN BAHAN BAKU GUDANG</h2>
            <p class="mt-1 text-small font-medium text-muted">
                Periode: {{ \Carbon\Carbon::parse($fromDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($toDate)->translatedFormat('d F Y') }}
            </p>
        </div>

        {{-- TABEL MUTASI BAHAN BAKU --}}
        <div class="mb-8 overflow-x-auto">
            <table class="min-w-full border border-line text-small">
                <thead class="bg-primary/5 text-ink">
                    <tr>
                        <th class="px-3 py-2 text-left text-label uppercase">No.</th>
                        <th class="px-3 py-2 text-left text-label uppercase">Bahan Baku</th>
                        <th class="px-3 py-2 text-left text-label uppercase">Satuan</th>
                        <th class="px-3 py-2 text-right text-label uppercase">Total Masuk (IN)</th>
                        <th class="px-3 py-2 text-right text-label uppercase">Total Keluar (OUT)</th>
                        <th class="px-3 py-2 text-right text-label uppercase">Penyesuaian (Adj)</th>
                        <th class="px-3 py-2 text-right text-label uppercase">Saldo Akhir Saat Ini</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($reportData as $index => $row)
                        @php
                            $ing = $row['ingredient'];
                        @endphp
                        <tr>
                            <td class="px-3 py-2 text-muted">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 font-medium text-ink">{{ $ing->name }}</td>
                            <td class="px-3 py-2 text-muted">{{ $ing->unit }}</td>
                            <td class="px-3 py-2 text-right tabular-nums">
                                {{ $row['total_in'] > 0 ? '+' . number_format($row['total_in'], 2, ',', '.') : '—' }}
                            </td>
                            <td class="px-3 py-2 text-right tabular-nums">
                                {{ $row['total_out'] > 0 ? '-' . number_format($row['total_out'], 2, ',', '.') : '—' }}
                            </td>
                            <td class="px-3 py-2 text-right tabular-nums">
                                {{ $row['total_adj'] != 0 ? ($row['total_adj'] > 0 ? '+' : '') . number_format($row['total_adj'], 2, ',', '.') : '—' }}
                            </td>
                            <td class="px-3 py-2 text-right font-semibold tabular-nums text-ink">
                                {{ number_format($row['current_stock'], 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-center text-muted">
                                Tidak ada data bahan baku yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- KOLOM TANDA TANGAN --}}
        <div class="avoid-break mt-8 grid grid-cols-2 gap-8 border-t border-line pt-6 text-center text-small">
            <div>
                <p class="text-muted">Penanggung Jawab Gudang &amp; Logistik,</p>
                <div class="h-20"></div>
                <p class="inline-block border-b border-muted px-8 pb-1 font-semibold text-ink">Yola Nur Aida</p>
                <p class="mt-1 text-small text-muted">Divisi Persediaan &amp; Gudang</p>
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