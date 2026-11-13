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
        $invMap = ['safe' => ['primary', 'Aman'], 'restock_needed' => ['warn', 'Restock'], 'deficit' => ['danger', 'Defisit']];

        $missingRecipes = $plan->details->filter(fn ($d) => $d->product && $d->product->recipeItems->isEmpty());

        $planPendingMR = $plan->materialRequests->where('status', 'pending')->first()
            ?? $plan->materialRequests->where('status', 'Pending')->first();
        $planApprovedMR = $plan->materialRequests->where('status', 'approved')->first()
            ?? $plan->materialRequests->where('status', 'Approved')->first();

        $totPlanned = $plan->totalPlannedQuantity();
        $totActual = $plan->totalActualQuantity();
        $isPlanDone = $planStatus === 'completed';
        $isPlanCooking = $planStatus === 'in_progress';
        $realizePercent = $totPlanned > 0 ? min(100, round(($totActual / $totPlanned) * 100)) : 0;

        $deleteConfirm = [
            'formId' => 'delete-production-plan-form',
            'tone' => 'danger',
            'eyebrow' => 'Hapus rencana produksi',
            'title' => 'Hapus rencana produksi ini?',
            'subjectLabel' => 'Rencana',
            'subject' => $plan->plan_number,
            'message' => 'Apakah Anda yakin ingin menghapus rencana produksi ini?',
            'detail' => 'Aksi ini tidak dapat dibatalkan.',
            'confirmLabel' => 'Ya, Hapus Rencana',
        ];
        $mrConfirm = [
            'formId' => 'mr-store-form',
            'tone' => 'primary',
            'eyebrow' => 'Ajukan ke gudang',
            'title' => 'Ajukan permintaan bahan?',
            'subjectLabel' => 'Rencana',
            'subject' => $plan->plan_number,
            'message' => 'Kirimkan daftar kebutuhan bahan rencana produksi ini ke bagian gudang?',
            'detail' => '',
            'confirmLabel' => 'Ya, Ajukan',
        ];
    @endphp

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('production-plans.index') }}" class="text-muted transition hover:text-primary">Rencana Produksi</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Detail Rencana</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">{{ $plan->plan_number }}</h1>
                {!! $badge($pt[0], $pt[1]) !!}
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('production-plans.index') }}"
                class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>

            @if ($canManage)
            <a href="{{ route('production-plans.edit', $plan) }}"
                class="inline-flex items-center gap-2 rounded-input bg-primary px-4 py-2.5 text-label uppercase text-white transition hover:bg-primary-dark">
                Edit Rencana
            </a>
            @endif
        </div>
    </div>

    <div class="space-y-6">

        <x-flash-alert />

        {{-- Peringatan resep belum lengkap --}}
        @if ($missingRecipes->isNotEmpty())
        <div class="rounded-input border border-amber-200 bg-amber-50 p-4" role="alert">
            <p class="text-body font-semibold text-amber-900">Komposisi resep belum lengkap</p>
            <p class="mt-0.5 text-small text-amber-800">
                Terdapat {{ $missingRecipes->count() }} menu yang belum memiliki resep bahan baku, sehingga kebutuhan bahan belum dapat dihitung otomatis.
            </p>
            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-small font-medium">
                @foreach ($missingRecipes as $missing)
                @if ($missing->product)
                <a href="{{ route('products.recipe.edit', $missing->product) }}" class="text-amber-900 underline">Racik resep: {{ $missing->product->name }}</a>
                @endif
                @endforeach
            </div>
        </div>
        @endif

        {{-- Alur permintaan bahan --}}
        <x-card class="w-full">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-lg font-semibold text-ink">Alur Permintaan Bahan Baku Gudang</h2>
                        @if ($planPendingMR)
                        {!! $badge('warn', 'Menunggu verifikasi gudang (#MR-' . str_pad($planPendingMR->id, 4, '0', STR_PAD_LEFT) . ')') !!}
                        @elseif ($planApprovedMR)
                        {!! $badge('primary', 'Sudah diserahkan (#MR-' . str_pad($planApprovedMR->id, 4, '0', STR_PAD_LEFT) . ')') !!}
                        @else
                        {!! $badge('gray', 'Belum diajukan ke gudang') !!}
                        @endif
                    </div>
                    <p class="mt-1 text-small text-muted">
                        @if ($planPendingMR)
                        Permintaan bahan telah terkirim. Petugas gudang sedang memeriksa ketersediaan stok fisik.
                        @elseif ($planApprovedMR)
                        Bahan baku telah diserahkan oleh gudang dan transaksi stok keluar telah dicatat.
                        @else
                        Dapur belum mengirim permintaan bahan resmi. Ajukan ke gudang berdasarkan racikan resep menu.
                        @endif
                    </p>
                </div>

                <div class="shrink-0 text-small font-medium">
                    @if ($planPendingMR)
                    <a href="{{ route('material-requests.show', $planPendingMR) }}" class="text-primary transition hover:text-primary-dark">Pantau verifikasi gudang</a>
                    @elseif ($planApprovedMR)
                    <a href="{{ route('material-requests.show', $planApprovedMR) }}" class="text-primary transition hover:text-primary-dark">Lihat bukti serah terima</a>
                    @elseif ($canManage)
                    <form id="mr-store-form" method="POST" action="{{ route('production-plans.material-requests.store', $plan) }}">
                        @csrf
                        <x-button type="button" @click="$dispatch('open-confirm', @js($mrConfirm))">Ajukan ke Gudang</x-button>
                    </form>
                    @endif
                </div>
            </div>
        </x-card>

        {{-- Realisasi masak --}}
        @if ($isPlanCooking || $isPlanDone)
        <x-card class="w-full">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-lg font-semibold text-ink">Realisasi Masak Dapur</h2>
                        @if ($isPlanDone)
                        {!! $badge('primary', 'Selesai (' . $totActual . '/' . $totPlanned . ' porsi)') !!}
                        @elseif ($totActual > 0)
                        {!! $badge('warn', 'Parsial (' . $totActual . '/' . $totPlanned . ' porsi, ' . $realizePercent . '%)') !!}
                        @else
                        {!! $badge('gray', 'Belum dicatat') !!}
                        @endif
                    </div>
                    <p class="mt-1 text-small text-muted">
                        @if ($isPlanDone)
                        Seluruh menu telah selesai dimasak memenuhi target.
                        @elseif ($totActual > 0)
                        Dapur telah mencatat sebagian hasil masakan. Masih ada menu yang belum memenuhi target.
                        @else
                        Petugas dapur dapat menginput porsi matang aktual per menu.
                        @endif
                    </p>
                </div>

                @if ($isPlanCooking && $canManage)
                <a href="{{ route('production-plans.realization.create', $plan) }}"
                   class="shrink-0 text-small font-medium text-primary transition hover:text-primary-dark">
                    {{ $totActual > 0 ? 'Perbarui realisasi dapur' : 'Input realisasi masak' }}
                </a>
                @endif
            </div>
        </x-card>
        @endif

        {{-- Informasi utama --}}
        <x-card class="w-full sm:p-8">
            <div class="flex flex-col gap-3 border-b border-line pb-4 md:flex-row md:items-center md:justify-between">
                <p class="text-small text-muted">
                    Dibuat
                    <span class="font-medium text-ink">{{ $plan->created_at ? $plan->created_at->locale('id')->diffForHumans() : '-' }}</span>
                </p>

                @if ($canManage)
                <form id="delete-production-plan-form" method="POST" action="{{ route('production-plans.destroy', $plan) }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="$dispatch('open-confirm', @js($deleteConfirm))"
                            class="text-small font-medium text-danger-text transition hover:underline">Hapus Rencana</button>
                </form>
                @endif
            </div>

            <dl class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Tanggal Perencanaan</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $plan->plan_date ? $plan->plan_date->locale('id')->translatedFormat('d F Y') : '-' }}</dd>
                </div>
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Jadwal Masak</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $plan->production_date ? $plan->production_date->locale('id')->translatedFormat('l, d F Y') : '-' }}</dd>
                </div>
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Total Target Produksi</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ number_format($totPlanned, 0, ',', '.') }} porsi <span class="font-normal text-muted">({{ $plan->details->count() }} menu)</span></dd>
                </div>
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Penanggung Jawab</dt>
                    <dd class="mt-1.5 truncate text-body font-medium text-ink">{{ $plan->creator?->name ?? 'Sistem' }}</dd>
                </div>

                @if ($plan->notes)
                <div class="rounded-input border border-line bg-primary/5 p-4 md:col-span-2 lg:col-span-4">
                    <dt class="text-label uppercase text-muted">Catatan Operasional &amp; Instruksi Khusus</dt>
                    <dd class="mt-1.5 whitespace-pre-line text-body font-medium text-ink">{{ $plan->notes }}</dd>
                </div>
                @endif
            </dl>
        </x-card>

        {{-- Rincian menu --}}
        <x-card :padding="false" class="overflow-hidden">
            <div class="flex flex-col gap-2 border-b border-line px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-ink">Rincian Menu Hidangan &amp; Target Porsi</h2>
                    <p class="mt-0.5 text-small text-muted">Daftar hidangan yang harus diproduksi oleh tim dapur.</p>
                </div>
                <span class="text-small text-muted"><span class="font-medium text-ink">{{ $plan->details->count() }}</span> menu terdaftar</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="w-12 px-6 py-3.5 text-center text-label uppercase text-muted">No.</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Menu Makanan</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Status Resep</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Target Porsi</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Catatan Khusus</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @foreach ($plan->details as $index => $detail)
                        <tr class="transition hover:bg-primary/5">
                            <td class="px-6 py-4 text-center text-small text-muted">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                @if ($detail->product)
                                <a href="{{ route('products.show', $detail->product) }}" class="font-medium text-ink transition hover:text-primary">{{ $detail->product->name }}</a>
                                <div class="text-small text-muted">Rp {{ number_format($detail->product->selling_price, 0, ',', '.') }} / {{ $detail->product->unit }}</div>
                                @else
                                <span class="text-small text-danger-text">Menu tidak ditemukan (ID: {{ $detail->product_id }})</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($detail->product && $detail->product->recipeItems->isNotEmpty())
                                {!! $badge('primary', $detail->product->recipeItems->count() . ' bahan terhubung') !!}
                                @else
                                {!! $badge('warn', 'Belum ada resep') !!}
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right font-medium text-ink">{{ number_format($detail->planned_quantity, 0, ',', '.') }} porsi</td>
                            <td class="max-w-xs truncate px-6 py-4 text-small text-muted">{{ $detail->notes ?: '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-small font-medium">
                                @if ($detail->product && $canManage)
                                <a href="{{ route('products.recipe.edit', $detail->product) }}" class="text-primary transition hover:text-primary-dark">Kelola Resep</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t border-line bg-primary/5">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-label uppercase text-muted">Total Porsi Keseluruhan</td>
                            <td class="px-6 py-4 text-right text-lg font-semibold text-primary">{{ number_format($totPlanned, 0, ',', '.') }} porsi</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-card>

        {{-- Kalkulasi logistik --}}
        <x-card :padding="false" class="overflow-hidden">
            <div class="flex flex-col gap-4 border-b border-line px-6 py-5 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-lg font-semibold text-ink">Kalkulasi Kebutuhan Bahan Baku &amp; Stok Gudang</h2>
                        @if ($summary['shortage_count'] > 0)
                        {!! $badge('danger', 'Perlu pengadaan: ' . $summary['shortage_count'] . ' bahan kurang') !!}
                        @elseif ($summary['below_minimum_count'] > 0)
                        {!! $badge('warn', 'Restock: ' . $summary['below_minimum_count'] . ' bahan di bawah minimum') !!}
                        @else
                        {!! $badge('primary', 'Kebutuhan terpenuhi & stok aman') !!}
                        @endif
                    </div>
                    <p class="mt-1 max-w-3xl text-small text-muted">
                        Dihitung otomatis dari target porsi dikali takaran resep, diagregasikan antar-menu, lalu dibandingkan dengan stok gudang.
                    </p>
                </div>

                <a href="{{ route('production-plans.ingredients', $plan) }}"
                   class="shrink-0 whitespace-nowrap text-small font-medium text-primary transition hover:text-primary-dark">Lihat &amp; cetak lembar kerja bahan</a>
            </div>

            <div class="border-b border-line bg-primary/5 p-6">
                <dl class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                    <div class="rounded-input border border-line bg-white p-3.5">
                        <dt class="text-label uppercase text-muted">Total Bahan</dt>
                        <dd class="mt-1 text-body font-semibold text-ink">{{ $summary['total_ingredients'] }} item</dd>
                    </div>
                    <div class="rounded-input border border-line bg-white p-3.5">
                        <dt class="text-label uppercase text-muted">Terpenuhi</dt>
                        <dd class="mt-1 text-body font-semibold text-primary">{{ $summary['fulfilled_count'] }} item</dd>
                    </div>
                    <div class="rounded-input border border-line bg-white p-3.5">
                        <dt class="text-label uppercase text-muted">Kurang</dt>
                        <dd class="mt-1 text-body font-semibold {{ $summary['shortage_count'] > 0 ? 'text-danger-text' : 'text-ink' }}">{{ $summary['shortage_count'] }} item</dd>
                    </div>
                    <div class="rounded-input border border-line bg-white p-3.5">
                        <dt class="text-label uppercase text-muted">&le; Min Stok</dt>
                        <dd class="mt-1 text-body font-semibold {{ $summary['below_minimum_count'] > 0 ? 'text-amber-700' : 'text-ink' }}">{{ $summary['below_minimum_count'] }} item</dd>
                    </div>
                    <div class="rounded-input border border-line bg-white p-3.5">
                        @php $r = $readyMap[$summary['production_readiness']['status']] ?? ['gray', 'Perlu Verifikasi']; @endphp
                        <dt class="mb-1 text-label uppercase text-muted">Kesiapan</dt>
                        <dd>{!! $badge($r[0], $r[1]) !!}</dd>
                    </div>
                    <div class="rounded-input border border-line bg-white p-3.5">
                        @php $r = $invMap[$summary['inventory_condition']['status']] ?? ['gray', 'Perlu Verifikasi']; @endphp
                        <dt class="mb-1 text-label uppercase text-muted">Stok Akhir</dt>
                        <dd>{!! $badge($r[0], $r[1]) !!}</dd>
                    </div>
                </dl>
            </div>

            @if ($summary['has_shortage'])
            <div class="mx-6 mt-5 rounded-input border border-danger-text/20 bg-danger-bg p-4 text-danger-text" role="alert">
                <p class="text-body font-semibold">Kekurangan kebutuhan produksi (perlu pengadaan)</p>
                <p class="mt-0.5 text-small">
                    Terdapat {{ $summary['shortage_count'] }} jenis bahan baku dengan stok gudang yang tidak mencukupi. Koordinasikan pengadaan dengan tim gudang sebelum jadwal produksi dimulai.
                </p>
            </div>
            @endif

            @if ($summary['has_restock_warning'] && ! $summary['has_shortage'])
            <div class="mx-6 mt-5 rounded-input border border-amber-200 bg-amber-50 p-4 text-amber-800" role="alert">
                <p class="text-body font-semibold">Restock persediaan gudang (batas minimum tercapai)</p>
                <p class="mt-0.5 text-small">
                    Kebutuhan produksi terpenuhi, namun proyeksi stok akhir pada {{ $summary['below_minimum_count'] }} jenis bahan berada pada atau di bawah batas minimum. Segera jadwalkan pembelian ulang.
                </p>
            </div>
            @endif

            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="w-12 px-4 py-3.5 text-center text-label uppercase text-muted">No.</th>
                            <th scope="col" class="min-w-[200px] px-4 py-3.5 text-left text-label uppercase text-muted">Bahan Baku &amp; Kategori</th>
                            <th scope="col" class="px-3 py-3.5 text-right text-label uppercase text-muted">Kebutuhan</th>
                            <th scope="col" class="px-3 py-3.5 text-right text-label uppercase text-muted">Stok Aktual</th>
                            <th scope="col" class="px-3 py-3.5 text-right text-label uppercase text-muted">Proyeksi Akhir</th>
                            <th scope="col" class="px-3 py-3.5 text-right text-label uppercase text-muted">Kekurangan</th>
                            <th scope="col" class="px-3 py-3.5 text-right text-label uppercase text-muted">Min Stok</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-label uppercase text-muted">Kebutuhan</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-label uppercase text-muted">Stok Akhir</th>
                            <th scope="col" class="w-20 px-3 py-3.5 text-center text-label uppercase text-muted">Cek Fisik</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @forelse ($calculation['items'] as $index => $item)
                        @php
                            $av = $availMap[$item['availability_status']] ?? ['gray', 'Perlu Verifikasi'];
                            $en = $endMap[$item['ending_stock_status']] ?? ['gray', 'Perlu Verifikasi'];
                        @endphp
                        <tr class="transition hover:bg-primary/5">
                            <td class="px-4 py-4 text-center text-small text-muted">{{ $index + 1 }}</td>
                            <td class="px-4 py-4">
                                <p class="font-medium text-ink">{{ $item['ingredient_name'] }}</p>
                                <p class="text-small text-muted">{{ $item['ingredient_category'] }} &middot; {{ $item['unit'] }}</p>
                                @if ($item['has_unit_mismatch'])
                                <p class="text-small text-amber-700">Satuan resep dikonversi ke satuan stok</p>
                                @endif
                                <div class="mt-1 space-y-0.5 text-small text-muted">
                                    @foreach ($item['menu_breakdown'] as $menu)
                                    <div>{{ $menu['product_name'] }} &mdash; {{ $menu['planned_quantity'] }} porsi &rarr; {{ $menu['display_subtotal'] }}</div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-right font-medium text-ink">{{ $item['formatted_required'] }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-right text-ink">{{ $item['formatted_current_stock'] }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-right font-medium">
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
                            <td class="whitespace-nowrap px-3 py-4 text-right font-medium">
                                @if ($item['production_shortage'] !== null && $item['production_shortage'] > 0)
                                <span class="text-danger-text">{{ $item['formatted_production_shortage'] }}</span>
                                @elseif ($item['production_shortage'] !== null)
                                <span class="text-muted">0 {{ $item['unit'] }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-right text-muted">{{ $item['formatted_minimum_stock'] }}</td>
                            <td class="whitespace-nowrap px-3 py-4">{!! $badge($av[0], $av[1]) !!}</td>
                            <td class="whitespace-nowrap px-3 py-4">{!! $badge($en[0], $en[1]) !!}</td>
                            <td class="px-3 py-4 text-center">
                                <input type="checkbox" class="h-4 w-4 rounded border-muted/50 text-primary focus:ring-primary/20"
                                       aria-label="Cek fisik {{ $item['ingredient_name'] }}">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-6 py-10 text-center text-muted">
                                Belum ada kebutuhan bahan baku yang dapat dihitung. Pastikan setiap menu sudah memiliki komposisi resep bahan baku.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

    </div>
</x-app-layout>