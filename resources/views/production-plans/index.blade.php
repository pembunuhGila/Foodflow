<x-app-layout>
    @php
    $canManage = in_array(Auth::user()->role?->value, ['admin', 'produksi'], true);
    $hasFilter = $currentSearch || $currentStatus || $currentDate;

    $inputClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';

    $tabLink = fn ($s) => route('production-plans.index', array_filter([
    'search' => $currentSearch,
    'production_date' => $currentDate,
    'status' => $s,
    ]));

    $tabs = [
    ['label' => 'Semua', 'key' => 'all', 'value' => ''],
    ['label' => 'Dijadwalkan', 'key' => 'scheduled', 'value' => 'scheduled'],
    ['label' => 'Sedang Dimasak', 'key' => 'in_progress', 'value' => 'in_progress'],
    ['label' => 'Selesai', 'key' => 'completed', 'value' => 'completed'],
    ['label' => 'Draft', 'key' => 'draft', 'value' => 'draft'],
    ['label' => 'Dibatalkan', 'key' => 'cancelled', 'value' => 'cancelled'],
    ];
    foreach ($tabs as $i => $tab) {
    $tabs[$i]['active'] = (string) $currentStatus === $tab['value'];
    $tabs[$i]['text'] = $tab['label'] . (isset($statusCounts[$tab['key']]) ? ' (' . $statusCounts[$tab['key']] . ')' : '');
    }

    $tones = [
    'primary' => ['bg-primary/10 text-primary', 'bg-primary'],
    'warn' => ['bg-amber-50 text-amber-800', 'bg-amber-500'],
    'danger' => ['bg-danger-bg text-danger-text', 'bg-danger-text'],
    'gray' => ['bg-gray-100 text-gray-600', 'bg-gray-400'],
    ];
    $statusTone = [
    'scheduled' => ['primary', 'Dijadwalkan'],
    'in_progress' => ['warn', 'Sedang Dimasak'],
    'completed' => ['gray', 'Selesai'],
    'draft' => ['gray', 'Draft'],
    'cancelled' => ['danger', 'Dibatalkan'],
    ];
    @endphp

    <x-page-header eyebrow="Operasional Dapur" :title="__('Daftar Rencana Produksi Dapur')"
        subtitle="Pantau jadwal memasak, target porsi hidangan, dan status operasional dapur.">
        <x-slot name="actions">
            @if ($canManage)
            <x-button :href="route('production-plans.create')" id="btn-create-production-plan">
                <x-icon name="plus" class="h-4 w-4" />
                Buat Rencana Baru
            </x-button>
            @endif
        </x-slot>
    </x-page-header>

    <div class="space-y-6">

        @if (session('success'))
        <div class="flex items-center gap-2 rounded-input border border-primary/20 bg-primary/10 p-4 text-body text-primary" role="status">
            <x-icon name="check" class="h-5 w-5 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if (session('warning'))
        <div class="rounded-input border border-amber-200 bg-amber-50 p-4 text-body text-amber-800" role="alert">{{ session('warning') }}</div>
        @endif

        @if (!empty($filterWarning))
        <div class="rounded-input border border-amber-200 bg-amber-50 p-4 text-body text-amber-800" role="alert">{{ $filterWarning }}</div>
        @endif

        @if ($errors->any())
        <div class="rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text" role="alert">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Kartu ringkasan status -->
        @php
            $summaryCards = [
                ['label' => 'Semua Rencana',   'caption' => 'Total rencana kerja', 'key' => 'all',         'value' => '',            'icon' => 'clipboard'],
                ['label' => 'Dijadwalkan',     'caption' => 'Menunggu dimasak',    'key' => 'scheduled',   'value' => 'scheduled',   'icon' => 'calendar'],
                ['label' => 'Sedang Dimasak',  'caption' => 'Proses masak aktif',  'key' => 'in_progress', 'value' => 'in_progress', 'icon' => 'clock'],
                ['label' => 'Selesai',         'caption' => 'Produksi tuntas',     'key' => 'completed',   'value' => 'completed',   'icon' => 'check'],
            ];
        @endphp
        <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">
            @foreach ($summaryCards as $card)
                @php $isActive = (string) $currentStatus === $card['value']; @endphp
                <a href="{{ route('production-plans.index', $card['value'] === '' ? [] : ['status' => $card['value']]) }}"
                   @if ($isActive) aria-current="page" @endif
                   class="rounded-card border bg-white p-5 shadow-card transition {{ $isActive ? 'border-primary ring-2 ring-primary/20' : 'border-line hover:border-primary/40' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-label uppercase text-muted">{{ $card['label'] }}</div>
                            <div class="mt-2 font-display text-h1 {{ $isActive ? 'text-primary' : 'text-ink' }}">{{ $statusCounts[$card['key']] ?? 0 }}</div>
                        </div>
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-input {{ $isActive ? 'bg-primary text-white' : 'bg-info-bg text-primary' }}">
                            <x-icon :name="$card['icon']" />
                        </span>
                    </div>
                    <div class="mt-1 text-small text-muted">{{ $card['caption'] }}</div>
                </a>
            @endforeach
        </div>

        <!-- Filter: pencarian + tanggal + tab status -->
        <x-card>
            <div class="space-y-4">
                <form method="GET" action="{{ route('production-plans.index') }}"
                    class="grid grid-cols-1 items-end gap-4 md:grid-cols-2 xl:grid-cols-[1fr_12rem_auto]">
                    <input type="hidden" name="status" value="{{ $currentStatus }}">

                    <div>
                        <label for="search" class="mb-1.5 block text-label uppercase text-muted">Cari No. Rencana</label>
                        <input id="search" type="text" name="search" value="{{ $currentSearch }}"
                            placeholder="Contoh: PLN-2026..." class="{{ $inputClass }}">
                    </div>

                    <div>
                        <label for="production_date" class="mb-1.5 block text-label uppercase text-muted">Tanggal Produksi</label>
                        <input id="production_date" type="date" name="production_date" value="{{ $currentDate }}"
                            onchange="this.form.submit()" class="{{ $inputClass }}">
                    </div>

                    <div class="flex items-center gap-4 xl:h-[2.625rem]">
                        @if ($hasFilter)
                        <a href="{{ route('production-plans.index') }}" id="btn-reset-filter"
                            class="text-small font-medium text-primary hover:text-primary-dark">Reset</a>
                        @endif
                    </div>

                    <button type="submit" class="sr-only">Cari</button>
                </form>

                <nav class="flex w-full items-center gap-1 overflow-x-auto rounded-full bg-primary/5 p-1" aria-label="Filter status rencana">
                    @foreach ($tabs as $tab)
                    <a href="{{ $tabLink($tab['value']) }}"
                        class="flex-1 whitespace-nowrap rounded-full px-5 py-2 text-center text-small font-semibold transition {{ $tab['active'] ? 'bg-primary text-white shadow-sm' : 'text-ink hover:text-primary' }}">
                        {{ $tab['text'] }}
                    </a>
                    @endforeach
                </nav>
            </div>
        </x-card>

        <!-- Tabel rencana -->
        <x-card :padding="false" class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">No. Rencana</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Tanggal Produksi</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Target Menu &amp; Porsi</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Dibuat Oleh</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Status</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @forelse ($plans as $plan)
                        @php
                        $st = $plan->status instanceof \BackedEnum ? $plan->status->value : (string) $plan->status;
                        $tone = $statusTone[$st] ?? ['gray', $st];
                        $deleteConfirm = [
                        'formId' => 'delete-plan-' . $plan->id,
                        'tone' => 'danger',
                        'eyebrow' => 'Hapus rencana produksi',
                        'title' => 'Hapus rencana produksi ini?',
                        'subjectLabel' => 'Rencana',
                        'subject' => $plan->plan_number,
                        'message' => 'Apakah Anda yakin ingin menghapus rencana produksi ini?',
                        'detail' => 'Penghapusan akan membatalkan jadwal masak yang telah dialokasikan.',
                        'confirmLabel' => 'Ya, Hapus Rencana',
                        ];
                        @endphp
                        <tr class="transition hover:bg-primary/5">
                            <td class="whitespace-nowrap px-6 py-4">
                                <a href="{{ route('production-plans.show', $plan) }}" class="font-medium text-primary transition hover:text-primary-dark">{{ $plan->plan_number }}</a>
                                <div class="text-small text-muted">Dibuat: {{ $plan->plan_date ? $plan->plan_date->format('d/m/Y') : '-' }}</div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="whitespace-nowrap font-medium text-ink">
                                    {{ $plan->production_date ? $plan->production_date->locale('id')->translatedFormat('l, d M Y') : '-' }}
                                </div>
                                @if ($plan->notes)
                                <div class="max-w-xs truncate text-small text-muted" title="{{ $plan->notes }}">Catatan: {{ $plan->notes }}</div>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-medium text-ink">
                                    {{ number_format($plan->totalPlannedQuantity(), 0, ',', '.') }} porsi
                                    <span class="font-normal text-muted">({{ $plan->details_count }} menu)</span>
                                </div>
                                <div class="mt-1 space-y-0.5 text-small text-muted">
                                    @foreach ($plan->details->take(3) as $detail)
                                    <div class="max-w-xs truncate">{{ $detail->product?->name ?? 'Menu tidak ditemukan' }} &middot; {{ number_format($detail->planned_quantity, 0, ',', '.') }} porsi</div>
                                    @endforeach
                                    @if ($plan->details_count > 3)
                                    <div>+{{ $plan->details_count - 3 }} menu lainnya</div>
                                    @endif
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-medium text-ink">{{ $plan->creator?->name ?? 'Sistem' }}</div>
                                <div class="text-small capitalize text-muted">{{ $plan->creator?->role?->value ?? '-' }}</div>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-small font-medium {{ $tones[$tone[0]][0] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $tones[$tone[0]][1] }}"></span> {{ $tone[1] }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-right text-small font-medium">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('production-plans.show', $plan) }}" class="text-primary transition hover:text-primary-dark">Detail</a>
                                    <a href="{{ route('production-plans.ingredients', $plan) }}" class="text-primary transition hover:text-primary-dark">Lembar Bahan</a>
                                    @if ($canManage)
                                    <a href="{{ route('production-plans.edit', $plan) }}" class="text-primary transition hover:text-primary-dark">Edit</a>
                                    <form id="delete-plan-{{ $plan->id }}" method="POST" action="{{ route('production-plans.destroy', $plan) }}" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" @click="$dispatch('open-confirm', @js($deleteConfirm))"
                                            class="text-small font-medium text-danger-text transition hover:underline">Hapus</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-muted">
                                @if ($hasFilter)
                                <p class="font-medium text-ink">Tidak ada rencana produksi yang sesuai dengan filter.</p>
                                <a href="{{ route('production-plans.index') }}" class="mt-1 inline-block text-small font-medium text-primary hover:text-primary-dark">Reset filter</a>
                                @else
                                <p class="font-medium text-ink">Belum ada rencana produksi.</p>
                                @if ($canManage)
                                <a href="{{ route('production-plans.create') }}" class="mt-1 inline-block text-small font-medium text-primary hover:text-primary-dark">Buat rencana produksi baru</a>
                                @endif
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($plans->hasPages())
            <div class="border-t border-line bg-primary/5 px-6 py-4">
                {{ $plans->links() }}
            </div>
            @endif
        </x-card>

    </div>
</x-app-layout>