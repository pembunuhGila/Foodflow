<x-app-layout>
    @php
        $canManage = in_array(Auth::user()->role?->value, ['admin', 'gudang'], true);
        $hasFilter = $selectedIngredientId || $selectedType || $fromDate || $toDate;

        $selectClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';
        $inputClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';

        // Tab jenis mutasi membawa bahan baku dan rentang tanggal
        $tabLink = fn ($t) => route('stock-cards.index', array_filter([
            'ingredient_id' => $selectedIngredientId,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'transaction_type' => $t,
        ]));
        $tabs = [
            ['label' => 'Semua Jenis',  'value' => ''],
            ['label' => 'Stok Masuk',   'value' => 'in'],
            ['label' => 'Stok Keluar',  'value' => 'out'],
            ['label' => 'Penyesuaian',  'value' => 'adjustment'],
        ];
    @endphp

    <x-page-header eyebrow="Pengadaan" :title="__('Buku Kartu Mutasi Stok Bahan Baku')"
                   subtitle="Histori lengkap seluruh mutasi stok bahan (masuk penerimaan, keluar dapur, dan penyesuaian fisik).">
        <x-slot name="actions">
            @if ($canManage)
            <x-button :href="route('stock-adjustments.create')">
                <x-icon name="plus" class="h-4 w-4" />
                Penyesuaian Fisik
            </x-button>
            @endif
        </x-slot>
    </x-page-header>

    <div class="space-y-6">

        <x-flash-alert />

        <!-- Filter: bahan baku + tanggal + tab jenis mutasi dalam satu card -->
        <x-card>
            <div class="space-y-4">
                <form method="GET" action="{{ route('stock-cards.index') }}"
                      class="grid grid-cols-1 items-end gap-4 md:grid-cols-2 xl:grid-cols-[1fr_11rem_11rem_auto]">
                    <input type="hidden" name="transaction_type" value="{{ $selectedType }}">

                    <div>
                        <label for="ingredient_id" class="mb-1.5 block text-label uppercase text-muted">Bahan Baku</label>
                        <select name="ingredient_id" id="ingredient_id" onchange="this.form.submit()" class="{{ $selectClass }}">
                            <option value="">Semua Bahan Baku</option>
                            @foreach ($ingredients as $ing)
                            <option value="{{ $ing->id }}" {{ $selectedIngredientId == $ing->id ? 'selected' : '' }}>
                                {{ $ing->name }} (Saldo: {{ (float) ($ing->current_stock ?? 0) }} {{ $ing->unit }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="from_date" class="mb-1.5 block text-label uppercase text-muted">Dari Tanggal</label>
                        <input type="date" name="from_date" id="from_date" value="{{ $fromDate }}"
                               onchange="this.form.submit()" class="{{ $inputClass }}">
                    </div>

                    <div>
                        <label for="to_date" class="mb-1.5 block text-label uppercase text-muted">Sampai Tanggal</label>
                        <input type="date" name="to_date" id="to_date" value="{{ $toDate }}"
                               onchange="this.form.submit()" class="{{ $inputClass }}">
                    </div>

                    <div class="flex items-center gap-4 xl:h-[2.625rem]">
                        @if ($hasFilter)
                        <a href="{{ route('stock-cards.index') }}"
                           class="text-small font-medium text-primary hover:text-primary-dark">Reset</a>
                        @endif
                    </div>
                </form>

                {{-- Tab jenis mutasi: selebar card --}}
                <nav class="flex w-full items-center gap-1 overflow-x-auto rounded-full bg-primary/5 p-1" aria-label="Filter jenis mutasi">
                    @foreach ($tabs as $tab)
                    @php $isCurrent = (string) $selectedType === $tab['value']; @endphp
                    <a href="{{ $tabLink($tab['value']) }}"
                       @if ($isCurrent) aria-current="page" @endif
                       class="flex-1 whitespace-nowrap rounded-full px-5 py-2 text-center text-small font-semibold transition {{ $isCurrent ? 'bg-primary text-white shadow-sm' : 'text-ink hover:text-primary' }}">
                        {{ $tab['label'] }}
                    </a>
                    @endforeach
                </nav>
            </div>
        </x-card>

        <!-- Ringkasan saldo jika bahan dipilih -->
        @if ($selectedIngredient)
        <x-card>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-label uppercase text-muted">Kartu Stok Terpilih</p>
                    <h2 class="mt-1 text-lg font-semibold text-ink">{{ $selectedIngredient->name }}</h2>
                </div>
                <div class="sm:text-right">
                    <p class="text-label uppercase text-muted">Saldo Fisik Saat Ini</p>
                    <p class="mt-1 text-2xl font-semibold text-primary">
                        {{ number_format((float) $selectedIngredient->current_stock, 2, ',', '.') }}
                        <span class="text-body font-medium text-muted">{{ $selectedIngredient->unit }}</span>
                    </p>
                </div>
            </div>
        </x-card>
        @endif

        <!-- Tabel kartu stok -->
        <x-card :padding="false" class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Tanggal</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Bahan Baku</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Jenis Mutasi</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Masuk</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Keluar</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Penyesuaian Fisik</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Catatan &amp; Referensi</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Operator</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @forelse ($transactions as $tx)
                        @php
                            $type = strtolower($tx->transaction_type);
                            $qty = (float) $tx->quantity;
                            $unit = $tx->ingredient?->unit ?? '';
                        @endphp
                        <tr class="transition hover:bg-primary/5">
                            <td class="whitespace-nowrap px-6 py-4 text-ink">{{ $tx->transaction_date?->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-medium text-ink">{{ $tx->ingredient?->name ?? '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($type === 'in')
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-success-text/20 bg-success-bg px-2.5 py-1 text-small font-medium text-success-text">
                                    <span class="h-1.5 w-1.5 rounded-full bg-success-text"></span> Masuk
                                </span>
                                @elseif ($type === 'out')
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-danger-text/20 bg-danger-bg px-2.5 py-1 text-small font-medium text-danger-text">
                                    <span class="h-1.5 w-1.5 rounded-full bg-danger-text"></span> Keluar
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-info-text/20 bg-info-bg px-2.5 py-1 text-small font-medium text-info-text">
                                    <span class="h-1.5 w-1.5 rounded-full bg-info-text"></span> Penyesuaian Fisik
                                </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right font-medium text-success-text">
                                {{ $type === 'in' ? '+' . number_format($qty, 2, ',', '.') . ' ' . $unit : '-' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right font-medium text-danger-text">
                                {{ $type === 'out' ? '-' . number_format($qty, 2, ',', '.') . ' ' . $unit : '-' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right font-medium {{ $qty >= 0 ? 'text-ink' : 'text-danger-text' }}">
                                @if ($type === 'adjustment')
                                {{ ($qty >= 0 ? '+' : '') . number_format($qty, 2, ',', '.') . ' ' . $unit }}
                                @else
                                -
                                @endif
                            </td>
                            <td class="max-w-xs px-6 py-4 text-small text-muted">{{ $tx->notes ?: '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-small text-muted">{{ $tx->creator?->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-muted">
                                <p class="font-medium text-ink">Belum ada riwayat mutasi stok yang sesuai dengan filter.</p>
                                @if ($hasFilter)
                                <a href="{{ route('stock-cards.index') }}" class="mt-1 inline-block text-small font-medium text-primary hover:text-primary-dark">Reset filter</a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($transactions->hasPages())
            <div class="border-t border-line bg-primary/5 px-6 py-4">
                {{ $transactions->links() }}
            </div>
            @endif
        </x-card>

    </div>
</x-app-layout>