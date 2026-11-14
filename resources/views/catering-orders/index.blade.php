<x-app-layout>
    @php
        $isAdmin = Auth::user()?->isAdmin();
        $hasFilter = $currentSearch || $currentStatus || $startDate || $endDate || $currentDate;

        // Tab status membawa pencarian dan rentang tanggal
        $tabLink = fn ($s) => route('catering-orders.index', array_filter([
            'search' => $currentSearch,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $s,
        ]));
        $tabs = [
            ['label' => 'Semua',              'key' => 'all',        'value' => ''],
            ['label' => 'Pending (Menunggu)', 'key' => 'pending',    'value' => 'pending'],
            ['label' => 'Dikonfirmasi',       'key' => 'confirmed',  'value' => 'confirmed'],
            ['label' => 'Diproses',           'key' => 'processing', 'value' => 'processing'],
            ['label' => 'Selesai',            'key' => 'delivered',  'value' => 'delivered'],
            ['label' => 'Dibatalkan',         'key' => 'cancelled',  'value' => 'cancelled'],
        ];

        $inputClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';

        // Langkah status berikutnya (aksi teks)
        $nextStep = [
            'pending'    => ['status' => 'confirmed',  'label' => 'Konfirmasi'],
            'confirmed'  => ['status' => 'processing', 'label' => 'Proses'],
            'processing' => ['status' => 'delivered',  'label' => 'Selesai'],
        ];

        $statusBadge = [
            'pending'    => ['bg-amber-50 text-amber-800 border border-amber-200', 'bg-amber-500'],
            'confirmed'  => ['bg-info-bg text-info-text border border-info-text/20', 'bg-info-text'],
            'processing' => ['bg-primary text-white', 'bg-white'],
            'delivered'  => ['bg-success-bg text-success-text border border-success-text/20', 'bg-success-text'],
            'cancelled'  => ['bg-danger-bg text-danger-text border border-danger-text/20', 'bg-danger-text'],
        ];
    @endphp

    <x-page-header eyebrow="Penjualan" :title="__('Daftar Pesanan Katering')"
                   subtitle="Kelola pesanan pelanggan, pantau jadwal kirim, total porsi, nominal transaksi, dan status pesanan.">
        <x-slot name="actions">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('export.data', ['module' => 'catering-orders', 'format' => 'excel', 'search' => request('search')]) }}"
                    class="inline-flex items-center gap-1.5 rounded-full border border-line bg-white px-3.5 py-2 text-small font-medium text-ink shadow-sm transition hover:bg-primary/5"
                    title="Ekspor format Excel / Spreadsheet">
                    <x-icon name="file" class="h-4 w-4 text-success-text" />
                    <span>Excel</span>
                </a>
                <a href="{{ route('export.data', ['module' => 'catering-orders', 'format' => 'pdf', 'search' => request('search')]) }}"
                    class="inline-flex items-center gap-1.5 rounded-full border border-line bg-white px-3.5 py-2 text-small font-medium text-ink shadow-sm transition hover:bg-primary/5"
                    title="Cetak Laporan / Simpan PDF">
                    <x-icon name="printer" class="h-4 w-4 text-primary" />
                    <span>PDF</span>
                </a>
                @if ($isAdmin)
                <x-button :href="route('catering-orders.create')" id="btn-create-catering-order">
                    <x-icon name="plus" class="h-4 w-4" />
                    Buat Pesanan Baru
                </x-button>
                @endif
            </div>
        </x-slot>
    </x-page-header>

    <div class="space-y-6">

        <x-flash-alert />

        @if ($errors->any())
        <div class="rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text" role="alert">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Filter: pencarian + tanggal + tab status dalam satu card -->
        <x-card>
            <div class="space-y-4">
                <form method="GET" action="{{ route('catering-orders.index') }}"
                      class="grid grid-cols-1 items-end gap-4 md:grid-cols-2 xl:grid-cols-[1fr_11rem_11rem_auto]">
                    <input type="hidden" name="status" value="{{ $currentStatus }}">

                    <div>
                        <label for="search" class="mb-1.5 block text-label uppercase text-muted">Cari Pesanan / Pelanggan</label>
                        <input id="search" type="text" name="search" value="{{ $currentSearch }}"
                               placeholder="No. ORD atau nama pelanggan..." class="{{ $inputClass }}">
                    </div>

                    <div>
                        <label for="start_date" class="mb-1.5 block text-label uppercase text-muted">Kirim Dari</label>
                        <input id="start_date" type="date" name="start_date" value="{{ $startDate }}"
                               onchange="this.form.submit()" class="{{ $inputClass }}">
                    </div>

                    <div>
                        <label for="end_date" class="mb-1.5 block text-label uppercase text-muted">Kirim Sampai</label>
                        <input id="end_date" type="date" name="end_date" value="{{ $endDate }}"
                               onchange="this.form.submit()" class="{{ $inputClass }}">
                    </div>

                    <div class="flex items-center gap-4 xl:h-[2.625rem]">
                        @if ($hasFilter)
                        <a href="{{ route('catering-orders.index') }}" id="btn-reset-filter"
                           class="text-small font-medium text-primary hover:text-primary-dark">Reset</a>
                        @endif
                    </div>

                    {{-- Enter pada kolom pencarian tetap mengirim form --}}
                    <button type="submit" class="sr-only">Cari</button>
                </form>

                {{-- Tab status: selebar card --}}
                <nav class="flex w-full items-center gap-1 overflow-x-auto rounded-full bg-primary/5 p-1" aria-label="Filter status pesanan">
                    @foreach ($tabs as $tab)
                    @php $isCurrent = (string) $currentStatus === $tab['value']; @endphp
                    <a href="{{ $tabLink($tab['value']) }}"
                       @if ($isCurrent) aria-current="page" @endif
                       class="flex-1 whitespace-nowrap rounded-full px-5 py-2 text-center text-small font-semibold transition {{ $isCurrent ? 'bg-primary text-white shadow-sm' : 'text-ink hover:text-primary' }}">
                        {{ $tab['label'] }} ({{ $statusCounts[$tab['key']] ?? 0 }})
                    </a>
                    @endforeach
                </nav>
            </div>
        </x-card>

        <!-- Tabel Pesanan -->
        <x-card :padding="false" class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-body" id="table-catering-orders">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Nomor Pesanan</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Pelanggan</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Tanggal Kirim</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Total Porsi</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Total Nominal</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Status</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @forelse ($orders as $order)
                        @php
                            $st = $order->status instanceof \BackedEnum ? $order->status->value : (string) $order->status;
                            $badge = $statusBadge[$st] ?? ['bg-gray-100 text-gray-600', 'bg-gray-400'];
                            $next = $nextStep[$st] ?? null;
                            $canCancel = in_array($st, ['pending', 'confirmed', 'processing'], true);
                        @endphp
                        <tr class="transition hover:bg-primary/5" id="order-row-{{ $order->id }}">
                            <!-- Nomor -->
                            <td class="whitespace-nowrap px-6 py-4">
                                <a href="{{ route('catering-orders.show', $order) }}" class="font-medium text-primary transition hover:text-primary-dark">
                                    {{ $order->order_number }}
                                </a>
                            </td>

                            <!-- Pelanggan -->
                            <td class="px-6 py-4">
                                <div class="font-medium text-ink">{{ $order->customer?->name ?? '-' }}</div>
                                <div class="text-small text-muted">{{ $order->customer?->phone ?? '-' }}</div>
                            </td>

                            <!-- Tanggal -->
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-medium text-ink">{{ $order->delivery_date?->format('d/m/Y') ?? '-' }}</div>
                                <div class="text-small text-muted">Pesan: {{ $order->order_date?->format('d/m/Y') ?? '-' }}</div>
                            </td>

                            <!-- Porsi -->
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-medium text-ink">{{ $order->total_portions }} porsi</div>
                                <div class="text-small text-muted">{{ $order->details_count }} menu</div>
                            </td>

                            <!-- Nominal -->
                            <td class="whitespace-nowrap px-6 py-4 text-right font-medium text-ink">
                                {{ $order->formattedTotal() }}
                            </td>

                            <!-- Status -->
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-small font-medium {{ $badge[0] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $badge[1] }}"></span> {{ $order->status_label }}
                                </span>
                            </td>

                            <!-- Aksi (tombol pill nyaman sentuh) -->
                            <td class="whitespace-nowrap px-6 py-4 text-right text-small font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('catering-orders.show', $order) }}"
                                       class="inline-flex items-center rounded-full border border-line bg-white px-3 py-1.5 text-small font-medium text-ink shadow-sm transition hover:border-primary hover:text-primary">
                                        Detail
                                    </a>

                                    @if ($isAdmin && $next)
                                    @php
                                        $nextConfirm = [
                                            'formId' => 'next-order-' . $order->id,
                                            'tone' => 'primary',
                                            'eyebrow' => 'Ubah status pesanan',
                                            'title' => $next['label'] . ' pesanan ini?',
                                            'subjectLabel' => 'Pesanan',
                                            'subject' => $order->order_number,
                                            'message' => 'Apakah Anda yakin ingin mengubah status pesanan ini?',
                                            'detail' => '',
                                            'confirmLabel' => 'Ya, ' . $next['label'],
                                        ];
                                    @endphp
                                    <form id="next-order-{{ $order->id }}" method="POST" action="{{ route('catering-orders.update-status', $order) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $next['status'] }}">
                                        <button type="button" @click="$dispatch('open-confirm', @js($nextConfirm))"
                                                class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1.5 text-small font-medium text-primary shadow-sm transition hover:bg-primary/20">
                                            {{ $next['label'] }}
                                        </button>
                                    </form>
                                    @endif

                                    @if ($isAdmin && $canCancel)
                                    @php
                                        $cancelConfirm = [
                                            'formId' => 'cancel-order-' . $order->id,
                                            'tone' => 'danger',
                                            'eyebrow' => 'Batalkan pesanan',
                                            'title' => 'Batalkan pesanan ini?',
                                            'subjectLabel' => 'Pesanan',
                                            'subject' => $order->order_number,
                                            'message' => 'Apakah Anda yakin ingin membatalkan pesanan ini?',
                                            'detail' => '',
                                            'confirmLabel' => 'Ya, Batalkan Pesanan',
                                        ];
                                    @endphp
                                    <form id="cancel-order-{{ $order->id }}" method="POST" action="{{ route('catering-orders.update-status', $order) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="button" @click="$dispatch('open-confirm', @js($cancelConfirm))"
                                                class="inline-flex items-center rounded-full border border-danger-text/20 bg-danger-bg/50 px-3 py-1.5 text-small font-medium text-danger-text shadow-sm transition hover:bg-danger-bg">
                                            Batalkan
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-muted">
                                <p class="font-medium text-ink">Tidak ada pesanan katering yang sesuai kriteria.</p>
                                @if ($hasFilter)
                                <a href="{{ route('catering-orders.index') }}" class="mt-1 inline-block text-small font-medium text-primary hover:text-primary-dark">Reset filter</a>
                                @elseif ($isAdmin)
                                <a href="{{ route('catering-orders.create') }}" class="mt-1 inline-block text-small font-medium text-primary hover:text-primary-dark">Buat pesanan katering baru</a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($orders->hasPages())
            <div class="border-t border-line bg-primary/5 px-6 py-4">
                {{ $orders->links() }}
            </div>
            @endif
        </x-card>

    </div>
</x-app-layout>