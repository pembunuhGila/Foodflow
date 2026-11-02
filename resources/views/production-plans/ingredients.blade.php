<x-app-layout>
    @php
        $canManage = in_array(Auth::user()->role?->value, ['admin', 'produksi'], true);
        $summary = $calculation['summary'];

        $tones = [
            'primary' => ['bg-primary/10 text-primary', 'bg-primary'],
            'warn'    => ['bg-amber-50 text-amber-800', 'bg-amber-500'],
            'danger'  => ['bg-danger-bg text-danger-text', 'bg-danger-text'],
            'gray'    => ['bg-gray-100 text-gray-600', 'bg-gray-400'],
        ];
        $badge = fn (string $tone, string $text) => '<span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-small font-medium ' . $tones[$tone][0] . '"><span class="h-1.5 w-1.5 rounded-full ' . $tones[$tone][1] . '"></span> ' . e($text) . '</span>';

        $planStatus = $plan->status instanceof \BackedEnum ? $plan->status->value : (string) $plan->status;
        $statusTone = [
            'scheduled'   => ['primary', 'Dijadwalkan'],
            'in_progress' => ['warn', 'Sedang Dimasak'],
            'completed'   => ['gray', 'Selesai'],
            'draft'       => ['gray', 'Draft'],
            'cancelled'   => ['danger', 'Dibatalkan'],
        ];
        $pt = $statusTone[$planStatus] ?? ['gray', $planStatus];

        $availMap = ['fulfilled' => ['primary', 'Terpenuhi'], 'shortage' => ['danger', 'Kurang']];
        $endMap = [
            'safe' => ['primary', 'Aman'],
            'below_minimum' => ['warn', 'Di Bawah Min'],
            'at_minimum' => ['warn', 'Tepat Min'],
            'deficit' => ['danger', 'Defisit'],
        ];
        $readyMap = ['ready' => ['primary', 'Siap Produksi'], 'procurement_needed' => ['danger', 'Perlu Pengadaan']];
        $invMap = ['safe' => ['primary', 'Persediaan Aman'], 'restock_needed' => ['warn', 'Restock Diperlukan'], 'deficit' => ['danger', 'Defisit Stok']];

        $pendingMR = $plan->materialRequests->where('status', 'pending')->first()
            ?? $plan->materialRequests->where('status', 'Pending')->first();
        $approvedMR = $plan->materialRequests->where('status', 'approved')->first()
            ?? $plan->materialRequests->where('status', 'Approved')->first();

        $unverifiedItems = collect($calculation['items'])->filter(
            fn ($i) => $i['availability_status'] === 'unverified' || $i['current_stock'] === null
        );

        $mrConfirm = [
            'formId' => 'mr-store-form',
            'tone' => 'primary',
            'eyebrow' => 'Ajukan ke gudang',
            'title' => 'Ajukan permintaan bahan?',
            'subjectLabel' => 'Rencana',
            'subject' => $plan->plan_number,
            'message' => 'Kirimkan seluruh daftar kebutuhan bahan ini ke gudang untuk diverifikasi dan dikeluarkan?',
            'detail' => '',
            'confirmLabel' => 'Ya, Ajukan',
        ];
    @endphp

    {{-- Header (disembunyikan saat cetak) --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between print:hidden">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('production-plans.index') }}" class="text-muted transition hover:text-primary">Rencana Produksi</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('production-plans.show', $plan) }}" class="text-muted transition hover:text-primary">Detail Rencana</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Lembar Kerja Bahan</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">Lembar Kerja Bahan Baku</h1>
                {!! $badge($pt[0], $pt[1]) !!}
            </div>
            <p class="mt-1 text-small text-muted">No. rencana {{ $plan->plan_number }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('production-plans.show', $plan) }}"
                class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
                Kembali ke Detail
            </a>

            @if ($canManage)
            @if ($pendingMR)
            <a href="{{ route('material-requests.show', $pendingMR) }}"
               class="text-small font-medium text-amber-700 transition hover:underline">Menunggu Gudang (#MR-{{ str_pad($pendingMR->id, 4, '0', STR_PAD_LEFT) }})</a>
            @elseif ($approvedMR)
            <a href="{{ route('material-requests.show', $approvedMR) }}"
               class="text-small font-medium text-primary transition hover:text-primary-dark">Stok Dikeluarkan (#MR-{{ str_pad($approvedMR->id, 4, '0', STR_PAD_LEFT) }})</a>
            @else
            <form id="mr-store-form" method="POST" action="{{ route('production-plans.material-requests.store', $plan) }}" class="inline-block">
                @csrf
                <button type="button" @click="$dispatch('open-confirm', @js($mrConfirm))"
                        class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-primary transition hover:bg-primary/5">
                    Ajukan ke Gudang
                </button>
            </form>
            @endif
            @endif

            <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 rounded-input bg-primary px-4 py-2.5 text-label uppercase text-white transition hover:bg-primary-dark">
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <div class="space-y-6 print:space-y-3">

        <!-- Kop formal (hanya saat cetak) -->
        <div class="print-header-block mb-3 border-b-2 border-gray-900 pb-3">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-xl font-black uppercase leading-tight tracking-tight text-gray-900">FOODFLOW CATERING</h1>
                    <p class="mt-0.5 text-[11px] font-bold uppercase tracking-wide text-gray-700">LEMBAR KERJA BAHAN BAKU PRODUKSI &amp; LOGISTIK GUDANG (MRP)</p>
                </div>
                <div class="text-right">
                    <div class="text-sm font-bold text-gray-900">No. {{ $plan->plan_number }}</div>
                    <div class="text-[10px] text-gray-500">Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Informasi rencana -->
        <x-card class="plan-info-card w-full sm:p-8">
            <dl class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">No. Rencana</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $plan->plan_number }}</dd>
                </div>
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Jadwal Masak</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $plan->production_date ? $plan->production_date->locale('id')->translatedFormat('l, d F Y') : '-' }}</dd>
                </div>
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Total Target Porsi</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ number_format($plan->totalPlannedQuantity(), 0, ',', '.') }} porsi</dd>
                </div>
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Penanggung Jawab</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $plan->creator?->name ?? 'Sistem' }}</dd>
                </div>
                <div class="rounded-input border border-line bg-primary/5 p-4 md:col-span-2 lg:col-span-4">
                    <dt class="text-label uppercase text-muted">Catatan &amp; Instruksi Khusus</dt>
                    <dd class="mt-1.5 whitespace-pre-line text-body font-medium text-ink">{{ $plan->notes ?: 'Tidak ada catatan atau instruksi khusus.' }}</dd>
                </div>
            </dl>
        </x-card>

        <!-- 6 kartu ringkasan -->
        <div class="summary-cards-container grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            <div class="summary-card rounded-input border border-line bg-white p-4">
                <span class="card-label block text-label uppercase text-muted">Total Bahan</span>
                <div class="card-value mt-2 text-xl font-semibold text-ink">{{ $summary['total_ingredients'] }} <span class="text-small font-normal text-muted">item</span></div>
            </div>
            <div class="summary-card rounded-input border border-line bg-white p-4">
                <span class="card-label block text-label uppercase text-muted">Terpenuhi</span>
                <div class="card-value mt-2 text-xl font-semibold text-primary">{{ $summary['fulfilled_count'] }} <span class="text-small font-normal text-muted">item</span></div>
            </div>
            <div class="summary-card rounded-input border border-line bg-white p-4">
                <span class="card-label block text-label uppercase text-muted">Kurang</span>
                <div class="card-value mt-2 text-xl font-semibold {{ $summary['shortage_count'] > 0 ? 'text-danger-text' : 'text-ink' }}">{{ $summary['shortage_count'] }} <span class="text-small font-normal text-muted">item</span></div>
            </div>
            <div class="summary-card rounded-input border border-line bg-white p-4">
                <span class="card-label block text-label uppercase text-muted">&le; Min Stok</span>
                <div class="card-value mt-2 text-xl font-semibold {{ $summary['below_minimum_count'] > 0 ? 'text-amber-700' : 'text-ink' }}">{{ $summary['below_minimum_count'] }} <span class="text-small font-normal text-muted">item</span></div>
            </div>
            <div class="summary-card rounded-input border border-line bg-white p-4">
                @php $r = $readyMap[$summary['production_readiness']['status']] ?? ['gray', 'Perlu Verifikasi']; @endphp
                <span class="card-label block text-label uppercase text-muted">Kesiapan</span>
                <div class="mt-2">{!! $badge($r[0], $r[1]) !!}</div>
            </div>
            <div class="summary-card rounded-input border border-line bg-white p-4">
                @php $r = $invMap[$summary['inventory_condition']['status']] ?? ['gray', 'Perlu Verifikasi']; @endphp
                <span class="card-label block text-label uppercase text-muted">Stok Akhir</span>
                <div class="mt-2">{!! $badge($r[0], $r[1]) !!}</div>
            </div>
        </div>

        <!-- Peringatan -->
        @if ($summary['has_shortage'])
        <div class="print-alert rounded-input border border-danger-text/20 bg-danger-bg p-4 text-danger-text" role="alert">
            <p class="text-body font-semibold">Kekurangan kebutuhan produksi (perlu pengadaan)</p>
            <p class="mt-0.5 text-small">
                Terdapat {{ $summary['shortage_count'] }} jenis bahan baku yang stok aktualnya kurang dari kebutuhan. Tim gudang harus segera melakukan pengadaan sebelum jadwal masak dimulai.
            </p>
        </div>
        @endif

        @if ($summary['has_restock_warning'] && ! $summary['has_shortage'])
        <div class="print-alert rounded-input border border-amber-200 bg-amber-50 p-4 text-amber-800" role="alert">
            <p class="text-body font-semibold">Restock persediaan gudang (batas minimum tercapai)</p>
            <p class="mt-0.5 text-small">
                Kebutuhan produksi terpenuhi, namun proyeksi stok akhir pada {{ $summary['below_minimum_count'] }} jenis bahan akan mencapai atau berada di bawah batas minimum. Segera jadwalkan pembelian ulang.
            </p>
        </div>
        @endif

        @if ($unverifiedItems->isNotEmpty())
        <div class="print-alert rounded-input border border-line bg-primary/5 p-4 text-muted" role="alert">
            <p class="text-body font-semibold text-ink">Verifikasi data stok gudang diperlukan</p>
            <p class="mt-0.5 text-small">
                Terdapat {{ $unverifiedItems->count() }} jenis bahan baku dengan data persediaan yang belum tercatat atau perlu diverifikasi fisik di gudang sebelum serah terima.
            </p>
        </div>
        @endif

        <!-- Tabel kebutuhan bahan -->
        <x-card :padding="false" class="table-container overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-6 py-5">
                <div>
                    <h2 class="text-lg font-semibold text-ink">Daftar Kebutuhan Bahan Baku Produksi &amp; Status Stok</h2>
                    <p class="mt-0.5 text-small text-muted print:hidden">Dihitung otomatis berdasarkan resep dan porsi terencana, dibandingkan dengan persediaan gudang.</p>
                </div>
                <span class="text-small text-muted"><span class="font-medium text-ink">{{ $summary['total_ingredients'] }}</span> bahan terdaftar</span>
            </div>

            <div class="overflow-x-auto">
                <table class="ingredients-table min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="col-no px-3 py-3.5 text-center text-label uppercase text-muted">No.</th>
                            <th scope="col" class="col-name px-4 py-3.5 text-left text-label uppercase text-muted">Bahan Baku &amp; Kategori</th>
                            <th scope="col" class="col-req px-3 py-3.5 text-right text-label uppercase text-muted">Kebutuhan Total</th>
                            <th scope="col" class="col-stock px-3 py-3.5 text-right text-label uppercase text-muted">Stok Aktual</th>
                            <th scope="col" class="col-proj px-3 py-3.5 text-right text-label uppercase text-muted">Proyeksi Akhir</th>
                            <th scope="col" class="col-short px-3 py-3.5 text-right text-label uppercase text-muted">Kekurangan</th>
                            <th scope="col" class="col-min px-3 py-3.5 text-right text-label uppercase text-muted">Min Stok</th>
                            <th scope="col" class="col-status-req px-3 py-3.5 text-left text-label uppercase text-muted">Status Kebutuhan</th>
                            <th scope="col" class="col-status-end px-3 py-3.5 text-left text-label uppercase text-muted">Status Stok Akhir</th>
                            <th scope="col" class="col-check px-3 py-3.5 text-center text-label uppercase text-muted">Cek Fisik</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @forelse ($calculation['items'] as $index => $item)
                        @php
                            $av = $availMap[$item['availability_status']] ?? ['gray', 'Perlu Verifikasi'];
                            $en = $endMap[$item['ending_stock_status']] ?? ['gray', 'Perlu Verifikasi'];
                        @endphp
                        <tr class="align-top transition hover:bg-primary/5">
                            <td class="px-3 py-3 text-center text-small text-muted">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-ink">{{ $item['ingredient_name'] }}</div>
                                <div class="text-small text-muted">Kategori: {{ $item['ingredient_category'] }} &middot; Satuan: {{ $item['unit'] }}</div>
                                @if ($item['has_unit_mismatch'])
                                <div class="text-small text-amber-700">Satuan resep dikonversi ke satuan stok ({{ $item['unit'] }})</div>
                                @endif
                                <div class="mt-1 space-y-0.5 text-small text-muted print:hidden">
                                    @foreach ($item['menu_breakdown'] as $mb)
                                    <div class="truncate">{{ $mb['product_name'] }} ({{ $mb['planned_quantity'] }} porsi &rarr; {{ $mb['display_subtotal'] }})</div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-3 text-right font-medium text-ink">{{ $item['formatted_required'] }}</td>
                            <td class="whitespace-nowrap px-3 py-3 text-right text-ink">{{ $item['formatted_current_stock'] }}</td>
                            <td class="whitespace-nowrap px-3 py-3 text-right font-medium">
                                @if ($item['projected_ending_stock'] !== null)
                                @if ($item['projected_ending_stock'] > $item['minimum_stock'])
                                <span class="text-ink">{{ $item['formatted_projected_ending_stock'] }}</span>
                                @elseif ($item['projected_ending_stock'] >= 0)
                                <span class="text-amber-700">{{ $item['formatted_projected_ending_stock'] }}</span>
                                @else
                                <span class="text-danger-text">{{ $item['formatted_projected_ending_stock'] }}</span>
                                @endif
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-3 py-3 text-right font-medium">
                                @if ($item['production_shortage'] !== null && $item['production_shortage'] > 0)
                                <span class="text-danger-text">{{ $item['formatted_production_shortage'] }}</span>
                                @elseif ($item['production_shortage'] !== null)
                                <span class="text-muted">0 {{ $item['unit'] }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-3 py-3 text-right text-muted">{{ $item['formatted_minimum_stock'] }}</td>
                            <td class="whitespace-nowrap px-3 py-3">{!! $badge($av[0], $av[1]) !!}</td>
                            <td class="whitespace-nowrap px-3 py-3">{!! $badge($en[0], $en[1]) !!}</td>
                            <td class="px-3 py-3 text-center">
                                <input type="checkbox" class="h-4 w-4 rounded border-muted/50 text-primary focus:ring-primary/20"
                                       aria-label="Cek fisik {{ $item['ingredient_name'] }}">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-muted">Tidak ada bahan baku yang terdaftar pada menu rencana produksi ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- Tanda tangan serah terima -->
        <div class="signature-section mt-6 border-t border-line pt-5">
            <div class="signature-grid grid grid-cols-1 gap-6 text-center text-small text-ink sm:grid-cols-3">
                <div class="sig-box flex flex-col items-center">
                    <span class="sig-title mb-12 font-semibold sm:mb-14">Disiapkan Oleh (Staff Gudang):</span>
                    <div class="sig-line mb-1.5 w-48 max-w-full border-b border-gray-400"></div>
                    <span class="sig-sub text-muted">(Nama &amp; Tanda Tangan)</span>
                </div>
                <div class="sig-box flex flex-col items-center">
                    <span class="sig-title mb-12 font-semibold sm:mb-14">Diterima Oleh (Tim Dapur):</span>
                    <div class="sig-line mb-1.5 w-48 max-w-full border-b border-gray-400"></div>
                    <span class="sig-sub text-muted">(Nama &amp; Tanda Tangan)</span>
                </div>
                <div class="sig-box flex flex-col items-center">
                    <span class="sig-title mb-12 font-semibold sm:mb-14">Disetujui (Kepala Produksi / Ahli Gizi):</span>
                    <div class="sig-line mb-1.5 w-48 max-w-full border-b border-gray-400"></div>
                    <span class="sig-name font-semibold text-ink">{{ $plan->creator?->name ?? '(Nama & Tanda Tangan)' }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- CSS khusus cetak & PDF (tidak diubah) -->
    <style>
        .print-header-block { display: none; }

        @media print {
            nav, header, aside, .no-print, button,
            a[href^="http"]:after, a[href]:after {
                display: none !important;
                content: "" !important;
            }

            .print-header-block { display: block !important; }

            @page { size: A4 landscape; margin: 8mm 10mm 10mm 10mm; }

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

            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }

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
            .summary-card .card-label { font-size: 6.5pt !important; line-height: 1.1 !important; }
            .summary-card .card-value { font-size: 11pt !important; margin-top: 2px !important; }

            .table-container {
                overflow: visible !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 4px !important;
            }
            .overflow-x-auto { overflow: visible !important; }

            .ingredients-table {
                width: 100% !important;
                table-layout: fixed !important;
                border-collapse: collapse !important;
                font-size: 7.5pt !important;
            }
            .ingredients-table thead { display: table-header-group !important; }
            .ingredients-table tr { page-break-inside: avoid !important; break-inside: avoid !important; }
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
            .sig-box { flex: 1 1 0% !important; text-align: center !important; }
            .sig-title { font-size: 7.5pt !important; margin-bottom: 38px !important; display: block !important; }
            .sig-line { width: 140px !important; margin: 0 auto 3px auto !important; border-bottom: 1px solid #475569 !important; }
            .sig-sub, .sig-name { font-size: 7pt !important; display: block !important; }
        }
    </style>
</x-app-layout>