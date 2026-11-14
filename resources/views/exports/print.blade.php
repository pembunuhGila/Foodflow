<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} — FoodFlow</title>
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
        }
    </style>
</head>
<body class="bg-gray-100 font-sans text-ink antialiased">

    {{-- Toolbar Aksi Atas (Hanya tampil di layar browser, tersembunyi saat dicetak) --}}
    <div class="no-print sticky top-0 z-30 border-b border-line bg-white/95 px-6 py-4 shadow-sm backdrop-blur-md">
        <div class="mx-auto flex max-w-5xl items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="javascript:history.back()" class="inline-flex items-center gap-2 rounded-full border border-line bg-white px-4 py-2 text-small font-medium text-ink transition hover:bg-primary/5">
                    <x-icon name="arrow-left" class="h-4 w-4" />
                    <span>Kembali</span>
                </a>
                <span class="text-body font-semibold text-ink">{{ $title }}</span>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-full bg-primary px-5 py-2 text-small font-medium text-white shadow-card transition hover:bg-primary-dark">
                    <x-icon name="printer" class="h-4 w-4" />
                    <span>Cetak Dokumen / Simpan PDF</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Kertas Cetak A4 --}}
    <div class="print-container mx-auto my-6 max-w-5xl rounded-card border border-line bg-white p-8 shadow-card sm:p-12">
        {{-- KOP Surat --}}
        <div class="flex items-center justify-between border-b-2 border-primary/20 pb-6">
            <div class="flex items-center gap-4">
                <x-brand-logo class="h-12 w-16" />
                <div>
                    <h1 class="font-display text-h2 font-bold tracking-tight text-ink">FoodFlow Catering ERP</h1>
                    <p class="text-small text-muted">Sistem Terintegrasi Manajemen Dapur, Pengadaan, & Logistik Persediaan</p>
                </div>
            </div>
            <div class="text-right text-small text-muted">
                <div class="font-semibold text-ink">DOKUMEN RESMI</div>
                <div>Dicetak: {{ $printedAt->translatedFormat('d F Y, H:i') }} WIB</div>
                <div>Oleh: {{ $user?->name ?? 'Sistem' }}</div>
            </div>
        </div>

        {{-- Judul Laporan --}}
        <div class="my-6">
            <h2 class="font-display text-h3 font-semibold text-ink">{{ $title }}</h2>
            <p class="text-small text-muted">Total Data: {{ count($rows) }} baris terdaftar</p>
        </div>

        {{-- Tabel Data --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-small">
                <thead>
                    <tr class="border-b-2 border-line bg-primary/5 text-label uppercase text-ink">
                        @foreach ($headers as $header)
                            <th class="px-3 py-2.5 font-semibold">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($rows as $row)
                        <tr class="transition hover:bg-primary/5">
                            @foreach ($row as $col)
                                <td class="px-3 py-2 text-ink">{{ $col }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headers) }}" class="px-3 py-8 text-center text-muted">
                                Tidak ada data yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Pengesahan --}}
        <div class="mt-12 flex items-end justify-between border-t border-line pt-6 text-small text-muted">
            <div>
                <p>Dokumen ini dibuat otomatis oleh Sistem FoodFlow.</p>
                <p>Status: Sah & Terverifikasi.</p>
            </div>
            <div class="text-center">
                <p class="mb-12">Penanggung Jawab,</p>
                <p class="font-semibold text-ink underline">{{ $user?->name ?? 'Administrator' }}</p>
                <p class="text-tiny text-muted">{{ ucfirst($user?->role?->value ?? 'Staff') }}</p>
            </div>
        </div>
    </div>

</body>
</html>
