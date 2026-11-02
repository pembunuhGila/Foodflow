<x-app-layout>
    @php
    $hasFilter = $currentSearch || $currentStatus !== 'in_progress' || $currentDate;

    $inputClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';

    // Tab status membawa pencarian dan tanggal masak
    $tabLink = fn ($s) => route('productions.index', array_filter([
    'search' => $currentSearch,
    'production_date' => $currentDate,
    'status' => $s,
    ]));
    $tabs = [
    ['label' => 'Sedang Dimasak', 'value' => 'in_progress'],
    ['label' => 'Selesai', 'value' => 'completed'],
    ['label' => 'Semua Status', 'value' => 'all'],
    ];
    @endphp

    <x-page-header eyebrow="Operasional Dapur" :title="__('Pencatatan Realisasi Masak Dapur')"
        subtitle="Catat jumlah porsi hidangan yang berhasil dimasak per menu pada rencana produksi yang sedang berjalan.">
        <x-slot name="actions">
            <x-button variant="outline" :href="route('production-plans.index')">Semua Rencana Produksi</x-button>
        </x-slot>
    </x-page-header>

    <div class="space-y-6">

        <!-- Session Alert -->
        @if (session('success'))
        <div class="flex items-center gap-2 rounded-input border border-primary/20 bg-primary/10 p-4 text-body text-primary" role="status">
            <x-icon name="check" class="h-5 w-5 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if (session('warning'))
        <div class="rounded-input border border-amber-200 bg-amber-50 p-4 text-body text-amber-800" role="alert">{{ session('warning') }}</div>
        @endif

        @if (session('info'))
        <div class="rounded-input border border-line bg-primary/5 p-4 text-body text-ink" role="status">{{ session('info') }}</div>
        @endif

        <!-- Info singkat -->
        <x-card>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-label uppercase text-muted">Sedang Dimasak (Aktif)</p>
                    <p class="mt-1 text-2xl font-semibold text-primary">{{ $inProgressCount }}</p>
                    <p class="text-small text-muted">Rencana produksi yang siap dicatat realisasi porsinya.</p>
                </div>
                <p class="max-w-md text-small text-muted sm:text-right">
                    Rencana otomatis berubah ke <span class="font-medium text-ink">Selesai</span> hanya saat seluruh menu telah mencapai atau melampaui target porsi.
                </p>
            </div>
        </x-card>

        <!-- Filter: pencarian + tanggal + tab status -->
        <x-card>
            <form method="GET" action="{{ route('productions.index') }}"
                class="grid grid-cols-1 items-end gap-4 md:grid-cols-2 xl:grid-cols-[1fr_12rem_auto]">
                <input type="hidden" name="status" value="{{ $currentStatus }}">

                <div>
                    <label for="search" class="mb-1.5 block text-label uppercase text-muted">Cari No. Rencana</label>
                    <input id="search" type="text" name="search" value="{{ $currentSearch }}"
                        placeholder="Contoh: PLN-2026..." class="{{ $inputClass }}">
                </div>

                <div>
                    <label for="production_date" class="mb-1.5 block text-label uppercase text-muted">Tanggal Masak</label>
                    <input id="production_date" type="date" name="production_date" value="{{ $currentDate }}"
                        onchange="this.form.submit()" class="{{ $inputClass }}">
                </div>

                {{-- Tab status: sejajar dengan Tanggal Masak --}}
                <div>
                    <span class="mb-1.5 block text-label uppercase text-muted">Status</span>
                    <nav class="flex items-center gap-1 overflow-x-auto rounded-full bg-primary/5 p-1" aria-label="Filter status rencana">
                        @foreach ($tabs as $tab)
                        @php $isCurrent = (string) $currentStatus === $tab['value']; @endphp
                        <a href="{{ $tabLink($tab['value']) }}"
                            @if ($isCurrent) aria-current="page" @endif
                            class="whitespace-nowrap rounded-full px-4 py-2 text-center text-small font-semibold transition {{ $isCurrent ? 'bg-primary text-white shadow-sm' : 'text-ink hover:text-primary' }}">
                            {{ $tab['label'] }}
                        </a>
                        @endforeach
                    </nav>
                </div>

                @if ($hasFilter)
                <div class="md:col-span-2 xl:col-span-3">
                    <a href="{{ route('productions.index') }}"
                        class="text-small font-medium text-primary hover:text-primary-dark">Reset filter</a>
                </div>
                @endif

                {{-- Enter pada kolom pencarian tetap mengirim form --}}
                <button type="submit" class="sr-only">Cari</button>
            </form>
        </x-card>

        <!-- Tabel rencana -->
        <x-card :padding="false" class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">No. Rencana</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Tanggal Masak</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Rincian Menu &amp; Target Porsi</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Realisasi Total</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Status</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @forelse ($plans as $plan)
                        @php
                        $totalPlanned = $plan->totalPlannedQuantity();
                        $totalActual = $plan->totalActualQuantity();
                        $percent = $totalPlanned > 0 ? min(100, round(($totalActual / $totalPlanned) * 100)) : 0;
                        $st = $plan->status->value;
                        $badge = match ($st) {
                        'in_progress' => ['bg-amber-50 text-amber-800', 'bg-amber-500'],
                        'completed' => ['bg-primary/10 text-primary', 'bg-primary'],
                        'cancelled' => ['bg-danger-bg text-danger-text', 'bg-danger-text'],
                        default => ['bg-gray-100 text-gray-600', 'bg-gray-400'],
                        };
                        @endphp
                        <tr class="transition hover:bg-primary/5">
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-medium text-ink">{{ $plan->plan_number }}</div>
                                <div class="text-small text-muted">Dibuat: {{ $plan->created_at->format('d/m/Y') }}</div>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 font-medium text-ink">
                                {{ $plan->production_date->locale('id')->translatedFormat('d M Y') }}
                            </td>

                            <td class="px-6 py-4">
                                <div class="space-y-1 text-small">
                                    @foreach ($plan->details as $detail)
                                    @php $actual = $detail->production?->actual_quantity ?? 0; @endphp
                                    <div class="flex items-center justify-between gap-4">
                                        <span class="max-w-xs truncate text-ink" title="{{ $detail->product->name }}">{{ $detail->product->name }}</span>
                                        <span class="shrink-0 text-muted">
                                            <span class="font-medium {{ $actual >= $detail->planned_quantity ? 'text-primary' : ($actual > 0 ? 'text-amber-700' : 'text-muted') }}">{{ $actual }}</span>
                                            / {{ $detail->planned_quantity }} porsi
                                        </span>
                                    </div>
                                    @endforeach
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="w-28">
                                    <div class="mb-1 flex justify-between text-small">
                                        <span class="font-medium text-ink">{{ $totalActual }} / {{ $totalPlanned }}</span>
                                        <span class="text-muted">{{ $percent }}%</span>
                                    </div>
                                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-primary/10">
                                        <div class="h-1.5 rounded-full bg-primary" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-small font-medium {{ $badge[0] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $badge[1] }}"></span> {{ $plan->status->label() }}
                                </span>
                            </td>

                            <!-- Aksi (teks, bukan tombol) -->
                            <td class="whitespace-nowrap px-6 py-4 text-right text-small font-medium">
                                @if ($st === 'in_progress')
                                <a href="{{ route('production-plans.realization.create', $plan) }}" class="text-primary transition hover:text-primary-dark">
                                    {{ $totalActual > 0 ? 'Perbarui Realisasi' : 'Catat Realisasi' }}
                                </a>
                                @else
                                <span class="text-muted">{{ $st === 'completed' ? 'Telah selesai' : 'Tidak aktif' }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-muted">
                                <p class="font-medium text-ink">Tidak ada rencana produksi yang cocok dengan filter.</p>
                                <p class="mt-1 text-small text-muted">Pastikan ada rencana berstatus Sedang Dimasak untuk dicatat realisasinya.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($plans->hasPages())
            <div class="border-t border-line bg-primary/5 px-6 py-4">
                {{ $plans->links() }}
            </div>
            @endif
        </x-card>

    </div>
</x-app-layout>