<x-app-layout>
    @php
        $mrNumber = '#MR-' . str_pad($materialRequest->id, 4, '0', STR_PAD_LEFT);
        $isPending = $materialRequest->isPending();
        $isApproved = $materialRequest->isApproved();
        $canVerify = $isPending && (Auth::user()->isGudang() || Auth::user()->isAdmin());

        // Status stok gudang terhadap jumlah diminta
        $stockState = fn ($stock, $requested) => $stock <= 0
            ? ['danger', 'Stok Kosong']
            : ($stock < $requested ? ['danger', 'Stok Kurang'] : ['success', 'Stok Aman']);

        $verifyConfirm = [
            'formId' => 'verify-form',
            'tone' => 'primary',
            'eyebrow' => 'Verifikasi gudang',
            'title' => 'Konfirmasi serah terima bahan?',
            'subjectLabel' => 'Permintaan',
            'subject' => $mrNumber,
            'message' => 'Apakah Anda yakin data jumlah serah terima sudah sesuai?',
            'detail' => 'Stok bahan baku akan otomatis berkurang dan dicatat sebagai transaksi stok keluar (Stok OUT).',
            'confirmLabel' => 'Ya, Konfirmasi & Catat Stok OUT',
        ];
    @endphp

    {{-- Breadcrumb --}}
    <nav class="mb-2 flex items-center gap-2 text-small text-muted" aria-label="Breadcrumb">
        <span>Persediaan &amp; Bahan</span>
        <span aria-hidden="true">&rsaquo;</span>
        <a href="{{ route('material-requests.index') }}" class="transition hover:text-primary">Permintaan Bahan</a>
        <span aria-hidden="true">&rsaquo;</span>
        <span class="font-medium text-ink">Detail Permintaan</span>
    </nav>

    <x-page-header :title="'Permintaan Bahan ' . $mrNumber"
                   subtitle="Verifikasi bahan baku yang diserahkan dan otomatisasi pencatatan kartu stok keluar.">
        <x-slot name="actions">
            @if ($isPending)
                <x-badge variant="warning"><span class="h-1.5 w-1.5 rounded-full bg-current"></span> Menunggu Verifikasi Gudang</x-badge>
            @elseif ($isApproved)
                <x-badge variant="success"><x-icon name="check" class="h-3.5 w-3.5" /> Telah Diverifikasi &amp; Stok Keluar Dicatat</x-badge>
            @else
                <x-badge variant="neutral">{{ ucfirst($materialRequest->status) }}</x-badge>
            @endif
        </x-slot>
    </x-page-header>

    <div class="space-y-6">

        <x-flash-alert />

        @if ($errors->any())
            <div class="rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text" role="alert">
                <h4 class="mb-2 font-semibold">Verifikasi Tidak Dapat Diproses:</h4>
                <ul class="list-inside list-disc space-y-1 text-small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- CARD 1: Informasi permintaan --}}
        <x-card>
            <div class="mb-5">
                <h3 class="font-display text-h3 text-ink">Informasi Permintaan Bahan</h3>
                <p class="text-small text-muted">Data ringkasan permintaan dan relasi rencana produksi.</p>
            </div>

            <div class="grid grid-cols-2 gap-5 md:grid-cols-4">
                <div>
                    <span class="mb-1 block text-label uppercase text-muted">Nomor Permintaan</span>
                    <span class="font-display text-h3 text-primary">{{ $mrNumber }}</span>
                </div>

                <div>
                    <span class="mb-1 block text-label uppercase text-muted">Tanggal Permintaan</span>
                    <span class="font-medium text-ink">
                        {{ $materialRequest->request_date ? \Carbon\Carbon::parse($materialRequest->request_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                    </span>
                </div>

                <div>
                    <span class="mb-1 block text-label uppercase text-muted">Rencana Produksi</span>
                    @if ($materialRequest->productionPlan)
                        <a href="{{ route('production-plans.show', $materialRequest->productionPlan) }}"
                           class="font-medium text-primary transition hover:text-primary-dark">
                            {{ $materialRequest->productionPlan->plan_number }} &rarr;
                        </a>
                    @else
                        <span class="text-small italic text-muted">Tanpa Relasi Rencana Produksi</span>
                    @endif
                </div>

                <div>
                    <span class="mb-1 block text-label uppercase text-muted">Diajukan Oleh</span>
                    <span class="font-medium text-ink">{{ $materialRequest->requestedBy?->name ?? 'Staff Dapur' }}</span>
                    <span class="block text-small text-muted">{{ $materialRequest->requestedBy?->role?->label() ?? 'Produksi' }}</span>
                </div>

                @if ($isApproved)
                    <div>
                        <span class="mb-1 block text-label uppercase text-muted">Diverifikasi Oleh</span>
                        <span class="font-medium text-ink">{{ $materialRequest->verifiedBy?->name ?? 'Staff Gudang' }}</span>
                        <span class="block text-small text-muted">{{ $materialRequest->verifiedBy?->role?->label() ?? 'Gudang' }}</span>
                    </div>
                @endif
            </div>

            @if ($materialRequest->notes)
                <div class="mt-5 border-t border-line pt-4">
                    <span class="mb-1.5 block text-label uppercase text-muted">Catatan Permintaan</span>
                    <p class="whitespace-pre-line rounded-input border border-line bg-primary/5 p-3.5 text-body leading-relaxed text-ink">{{ $materialRequest->notes }}</p>
                </div>
            @endif
        </x-card>

        {{-- CARD 2: Rincian bahan baku --}}
        <x-card :padding="false" class="overflow-hidden">
            <div class="flex flex-col gap-3 border-b border-line p-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-display text-h3 text-ink">Rincian Bahan Baku yang Diminta</h3>
                    <p class="mt-0.5 text-small text-muted">
                        @if ($isPending)
                            Periksa ketersediaan stok fisik gudang, sesuaikan jumlah bahan yang diserahkan, lalu konfirmasi.
                        @else
                            Daftar bahan yang telah diserahkan ke dapur dan dicatat sebagai transaksi stok keluar.
                        @endif
                    </p>
                </div>

                @if ($isPending)
                    @if ($canVerify)
                        <x-badge variant="warning" class="self-start sm:self-auto">Mode: Verifikasi Serah Terima Gudang</x-badge>
                    @else
                        <x-badge variant="info" class="self-start sm:self-auto">Menunggu Verifikasi Gudang</x-badge>
                    @endif
                @endif
            </div>

            @if ($canVerify)
                {{-- Banner info --}}
                <div class="mx-6 mt-5 flex items-start gap-3 rounded-input border border-primary/20 bg-primary/5 p-3.5 text-small text-ink">
                    <x-icon name="info" class="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                    <span>Anda masuk dalam mode <strong>Verifikasi Gudang</strong>. Sesuaikan jumlah bahan yang akan diserahkan ke dapur, lalu klik tombol konfirmasi. Stok gudang akan otomatis berkurang.</span>
                </div>

                {{-- Form verifikasi khusus Gudang & Admin --}}
                <form method="POST" action="{{ route('material-requests.verify', $materialRequest) }}" id="verify-form">
                    @csrf

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-body">
                            <thead class="bg-primary/5">
                                <tr>
                                    <th class="px-6 py-3.5 text-left text-label uppercase text-muted">Bahan Baku</th>
                                    <th class="px-4 py-3.5 text-right text-label uppercase text-muted">Stok Gudang</th>
                                    <th class="px-4 py-3.5 text-right text-label uppercase text-muted">Jumlah Diminta</th>
                                    <th class="w-52 px-4 py-3.5 text-right text-label uppercase text-muted">Jumlah Diserahkan</th>
                                    <th class="px-4 py-3.5 text-left text-label uppercase text-muted">Satuan</th>
                                    <th class="px-4 py-3.5 text-left text-label uppercase text-muted">Status Stok</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line bg-white">
                                @foreach ($materialRequest->details as $index => $detail)
                                    @php
                                        $currentStock = (float) ($detail->ingredient?->current_stock ?? 0);
                                        $requestedQty = (float) $detail->requested_quantity;
                                        $defaultIssued = old("items.{$index}.issued_quantity", min($requestedQty, max(0, $currentStock)));
                                        [$stockVariant, $stockLabel] = $stockState($currentStock, $requestedQty);
                                    @endphp
                                    <tr class="transition hover:bg-primary/5">
                                        <td class="px-6 py-3.5">
                                            <div class="font-medium text-ink">{{ $detail->ingredient?->name ?? 'Bahan #' . $detail->ingredient_id }}</div>
                                            <div class="text-small text-muted">Kategori: {{ $detail->ingredient?->category ?? 'Umum' }}</div>
                                            <input type="hidden" name="items[{{ $index }}][detail_id]" value="{{ $detail->id }}">
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3.5 text-right tabular-nums {{ $currentStock < $requestedQty ? 'font-medium text-danger-text' : 'text-ink' }}">
                                            {{ number_format($currentStock, 2) }} {{ $detail->unit }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3.5 text-right font-medium tabular-nums text-ink">
                                            {{ number_format($requestedQty, 2) }} {{ $detail->unit }}
                                        </td>
                                        <td class="px-4 py-3.5 text-right">
                                            <x-text-input type="number" step="0.001" min="0" max="{{ $requestedQty }}"
                                                          name="items[{{ $index }}][issued_quantity]"
                                                          :value="$defaultIssued" required
                                                          class="block w-full text-right font-medium tabular-nums" />
                                            @error("items.{$index}.issued_quantity")
                                                <span class="mt-1 block text-left text-small font-medium text-danger-text">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3.5 text-small font-medium text-muted">{{ $detail->unit }}</td>
                                        <td class="whitespace-nowrap px-4 py-3.5">
                                            <x-badge :variant="$stockVariant">{{ $stockLabel }}</x-badge>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Catatan & aksi konfirmasi --}}
                    <div class="mt-1 space-y-4 border-t border-line bg-primary/5 p-6">
                        <div>
                            <x-input-label for="notes" value="Catatan Verifikasi Gudang (Opsional)" />
                            <x-textarea name="notes" id="notes" rows="2" class="mt-1.5 block bg-white"
                                        placeholder="Tambahkan catatan mengenai serah terima bahan, kondisi fisik, atau penyesuaian jumlah...">{{ old('notes') }}</x-textarea>
                        </div>

                        <div class="flex justify-end">
                            {{-- Periksa isian dulu, baru tampilkan popup konfirmasi --}}
                            <button type="button" @click="$el.form.reportValidity() && $dispatch('open-confirm', @js($verifyConfirm))"
                                    class="inline-flex items-center justify-center gap-2 rounded-full bg-primary px-5 py-2.5 text-body font-medium text-white shadow-card transition hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/30 active:scale-[0.98]">
                                <x-icon name="check" class="h-4 w-4" />
                                Konfirmasi Verifikasi &amp; Catat Stok OUT
                            </button>
                        </div>
                    </div>
                </form>
            @else
                @if ($isPending)
                    {{-- Read-only: menunggu verifikasi gudang --}}
                    <div class="mx-6 mt-5 flex items-start gap-3 rounded-input border border-primary/20 bg-primary/5 p-3.5 text-small text-ink">
                        <x-icon name="info" class="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                        <span>Permintaan ini telah diajukan ke Gudang dan sedang <strong>menunggu verifikasi fisik</strong> oleh petugas Gudang. Anda tidak dapat mengubah data permintaan ini.</span>
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-body">
                            <thead class="bg-primary/5">
                                <tr>
                                    <th class="px-6 py-3.5 text-left text-label uppercase text-muted">Bahan Baku</th>
                                    <th class="px-4 py-3.5 text-right text-label uppercase text-muted">Jumlah Diminta</th>
                                    <th class="px-4 py-3.5 text-right text-label uppercase text-muted">Stok Gudang</th>
                                    <th class="px-4 py-3.5 text-left text-label uppercase text-muted">Satuan</th>
                                    <th class="px-4 py-3.5 text-left text-label uppercase text-muted">Status Stok</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line bg-white">
                                @foreach ($materialRequest->details as $detail)
                                    @php
                                        $requestedQty = (float) $detail->requested_quantity;
                                        $currentStock = (float) ($detail->ingredient?->current_stock ?? 0);
                                        [$stockVariant, $stockLabel] = $stockState($currentStock, $requestedQty);
                                    @endphp
                                    <tr class="transition hover:bg-primary/5">
                                        <td class="px-6 py-3.5">
                                            <div class="font-medium text-ink">{{ $detail->ingredient?->name ?? 'Bahan #' . $detail->ingredient_id }}</div>
                                            <div class="text-small text-muted">Kategori: {{ $detail->ingredient?->category ?? 'Umum' }}</div>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3.5 text-right font-medium tabular-nums text-ink">{{ number_format($requestedQty, 2) }} {{ $detail->unit }}</td>
                                        <td class="whitespace-nowrap px-4 py-3.5 text-right tabular-nums text-muted">{{ number_format($currentStock, 2) }} {{ $detail->unit }}</td>
                                        <td class="whitespace-nowrap px-4 py-3.5 text-small font-medium text-muted">{{ $detail->unit }}</td>
                                        <td class="whitespace-nowrap px-4 py-3.5"><x-badge :variant="$stockVariant">{{ $stockLabel }}</x-badge></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    {{-- Read-only: sudah diverifikasi --}}
                    <div class="mx-6 mt-5 flex items-start gap-3 rounded-input border border-primary/20 bg-primary/5 p-3.5 text-small text-ink">
                        <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                        <span>Permintaan ini telah <strong>diverifikasi dan diserahkan</strong> oleh petugas gudang. Transaksi stok keluar (Stok OUT - BR-05) sudah dicatat secara otomatis.</span>
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-body">
                            <thead class="bg-primary/5">
                                <tr>
                                    <th class="px-6 py-3.5 text-left text-label uppercase text-muted">Bahan Baku</th>
                                    <th class="px-4 py-3.5 text-right text-label uppercase text-muted">Jumlah Diminta</th>
                                    <th class="px-4 py-3.5 text-right text-label uppercase text-muted">Jumlah Diserahkan</th>
                                    <th class="px-4 py-3.5 text-left text-label uppercase text-muted">Satuan</th>
                                    <th class="px-4 py-3.5 text-left text-label uppercase text-muted">Status Pemenuhan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line bg-white">
                                @foreach ($materialRequest->details as $detail)
                                    @php
                                        $requestedQty = (float) $detail->requested_quantity;
                                        $issuedQty = (float) $detail->issued_quantity;
                                        $fulfilled = $issuedQty >= $requestedQty;
                                    @endphp
                                    <tr class="transition hover:bg-primary/5">
                                        <td class="px-6 py-3.5">
                                            <div class="font-medium text-ink">{{ $detail->ingredient?->name ?? 'Bahan #' . $detail->ingredient_id }}</div>
                                            <div class="text-small text-muted">Kategori: {{ $detail->ingredient?->category ?? 'Umum' }}</div>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3.5 text-right tabular-nums text-muted">{{ number_format($requestedQty, 2) }} {{ $detail->unit }}</td>
                                        <td class="whitespace-nowrap px-4 py-3.5 text-right font-medium tabular-nums text-ink">{{ number_format($issuedQty, 2) }} {{ $detail->unit }}</td>
                                        <td class="whitespace-nowrap px-4 py-3.5 text-small font-medium text-muted">{{ $detail->unit }}</td>
                                        <td class="whitespace-nowrap px-4 py-3.5">
                                            @if ($fulfilled)
                                                <x-badge variant="success">Terpenuhi Penuh (100%)</x-badge>
                                            @elseif ($issuedQty > 0)
                                                <x-badge variant="warning">Sebagian ({{ round(($issuedQty / max(1, $requestedQty)) * 100) }}%)</x-badge>
                                            @else
                                                <x-badge variant="neutral">Tidak Diserahkan (0%)</x-badge>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Riwayat transaksi kartu stok keluar --}}
                @if ($materialRequest->stockTransactions->isNotEmpty())
                    <div class="mt-1 border-t border-line bg-primary/5 p-6">
                        <h4 class="mb-3 text-label uppercase text-ink">Riwayat Transaksi Kartu Stok Keluar (Stok OUT - BR-05)</h4>
                        <div class="overflow-x-auto rounded-input border border-line bg-white">
                            <table class="min-w-full text-small">
                                <thead class="bg-primary/5">
                                    <tr>
                                        <th class="px-4 py-2.5 text-left text-label uppercase text-muted">Bahan Baku</th>
                                        <th class="px-3 py-2.5 text-left text-label uppercase text-muted">Tipe Transaksi</th>
                                        <th class="px-3 py-2.5 text-right text-label uppercase text-muted">Jumlah Keluar</th>
                                        <th class="px-3 py-2.5 text-left text-label uppercase text-muted">Tanggal</th>
                                        <th class="px-3 py-2.5 text-left text-label uppercase text-muted">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-line">
                                    @foreach ($materialRequest->stockTransactions as $tx)
                                        <tr class="transition hover:bg-primary/5">
                                            <td class="px-4 py-2.5 font-medium text-ink">{{ $tx->ingredient?->name }}</td>
                                            <td class="px-3 py-2.5"><x-badge variant="info">{{ $tx->transaction_type }}</x-badge></td>
                                            <td class="whitespace-nowrap px-3 py-2.5 text-right font-medium tabular-nums text-danger-text">
                                                -{{ number_format((float) $tx->quantity, 2) }} {{ $tx->ingredient?->unit }}
                                            </td>
                                            <td class="px-3 py-2.5 text-muted">
                                                {{ $tx->transaction_date ? \Carbon\Carbon::parse($tx->transaction_date)->format('d/m/Y') : '-' }}
                                            </td>
                                            <td class="max-w-xs truncate px-3 py-2.5 text-muted">{{ $tx->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif
        </x-card>

    </div>
</x-app-layout>