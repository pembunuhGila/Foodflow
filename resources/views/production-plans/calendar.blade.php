<x-app-layout>
    @php
    $canManage = in_array(Auth::user()->role?->value, ['admin', 'produksi'], true);

    $statusTone = [
        'scheduled'   => ['bg-blue-50 border-blue-200 text-blue-800', 'bg-blue-500', 'Dijadwalkan'],
        'in_progress' => ['bg-amber-50 border-amber-200 text-amber-800', 'bg-amber-500', 'Sedang Dimasak'],
        'completed'   => ['bg-emerald-50 border-emerald-200 text-emerald-800', 'bg-emerald-500', 'Selesai'],
        'draft'       => ['bg-slate-50 border-slate-200 text-slate-600', 'bg-slate-400', 'Draft'],
        'cancelled'   => ['bg-rose-50 border-rose-200 text-rose-700', 'bg-rose-500', 'Dibatalkan'],
    ];

    $monthName = $currentDate->translatedFormat('F Y');
    @endphp

    <x-page-header eyebrow="Operasional Dapur Katering" :title="__('Kalender Jadwal Masak Dapur')"
        subtitle="Pantau distribusi jadwal memasak harian, alokasi porsi pesanan katering, dan kesiapan operasional dapur.">
        <x-slot name="actions">
            <div class="flex items-center gap-2.5">
                <a href="{{ route('production-plans.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-line bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 hover:text-primary transition">
                    <x-icon name="table" class="h-4 w-4 text-muted" />
                    <span>Tampilan Tabel</span>
                </a>

                @if ($canManage)
                <x-button :href="route('production-plans.create')" id="btn-create-production-plan">
                    <x-icon name="plus" class="h-4 w-4" />
                    <span>Buat Rencana Baru</span>
                </x-button>
                @endif
            </div>
        </x-slot>
    </x-page-header>

    <div class="space-y-6">

        <x-flash-alert />

        {{-- Navigasi Bulan & KPI Bulanan --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            {{-- Kontrol Pemilih Bulan --}}
            <div class="inline-flex items-center gap-2 rounded-card border border-line bg-white p-1.5 shadow-card">
                <a href="{{ route('production-plans.calendar', ['month' => $prevMonth, 'year' => $prevYear]) }}"
                   class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-line text-slate-600 hover:bg-slate-50 hover:text-primary transition"
                   title="Bulan sebelumnya">
                    <x-icon name="chevron-left" class="h-4 w-4" />
                </a>

                <div class="min-w-[170px] text-center font-display text-base font-bold capitalize text-ink">
                    {{ $monthName }}
                </div>

                <a href="{{ route('production-plans.calendar', ['month' => $nextMonth, 'year' => $nextYear]) }}"
                   class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-line text-slate-600 hover:bg-slate-50 hover:text-primary transition"
                   title="Bulan berikutnya">
                    <x-icon name="chevron-right" class="h-4 w-4" />
                </a>

                <div class="h-5 w-px bg-line mx-1"></div>

                <a href="{{ route('production-plans.calendar', ['month' => now()->month, 'year' => now()->year]) }}"
                   class="rounded-lg px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-primary/10 hover:text-primary transition">
                    Bulan Ini
                </a>
            </div>

            {{-- 4 Metrik Ringkasan Kalender --}}
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:gap-4">
                <div class="rounded-lg border border-line bg-white px-3.5 py-2.5 shadow-xs">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-muted">Total Batch</div>
                    <div class="mt-0.5 text-lg font-bold font-display text-ink">{{ $totalPlans }} <span class="text-xs font-normal text-muted">Jadwal</span></div>
                </div>
                <div class="rounded-lg border border-line bg-white px-3.5 py-2.5 shadow-xs">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-muted">Target Masak</div>
                    <div class="mt-0.5 text-lg font-bold font-display text-primary">{{ number_format($totalPortions, 0, ',', '.') }} <span class="text-xs font-normal text-muted">Porsi</span></div>
                </div>
                <div class="rounded-lg border border-line bg-white px-3.5 py-2.5 shadow-xs">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Batch Aktif</div>
                    <div class="mt-0.5 text-lg font-bold font-display text-amber-600">{{ $activeBatches }} <span class="text-xs font-normal text-muted">Batch</span></div>
                </div>
                <div class="rounded-lg border border-line bg-white px-3.5 py-2.5 shadow-xs">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Selesai</div>
                    <div class="mt-0.5 text-lg font-bold font-display text-emerald-600">{{ $completedBatches }} <span class="text-xs font-normal text-muted">Batch</span></div>
                </div>
            </div>
        </div>

        {{-- Grid Kalender Bulanan --}}
        <div class="rounded-card border border-line bg-white shadow-card overflow-hidden">
            {{-- Header Nama Hari --}}
            <div class="grid grid-cols-7 border-b border-line bg-slate-50/80 text-center text-xs font-bold uppercase tracking-wider text-slate-600">
                <div class="py-3">Senin</div>
                <div class="py-3">Selasa</div>
                <div class="py-3">Rabu</div>
                <div class="py-3">Kamis</div>
                <div class="py-3">Jumat</div>
                <div class="py-3 text-blue-700">Sabtu</div>
                <div class="py-3 text-rose-700">Minggu</div>
            </div>

            {{-- Slot Hari Kalender --}}
            <div class="grid grid-cols-7 gap-px bg-slate-200">
                @foreach ($calendarDays as $day)
                    @php
                        $isCurrentMonth = $day['is_current_month'];
                        $isToday = $day['is_today'];
                        $plans = $day['plans'];
                    @endphp
                    <div class="min-h-[135px] flex flex-col justify-between p-2.5 transition {{ $isCurrentMonth ? 'bg-white' : 'bg-slate-50/70 text-slate-400' }} hover:bg-slate-50/50">
                        {{-- Bilah Tanggal Atas --}}
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[11px] font-bold text-muted">
                                @if ($loop->first || $day['day_number'] === 1)
                                    {{ $day['date']->translatedFormat('M') }}
                                @endif
                            </span>

                            @if ($isToday)
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-primary font-bold text-xs text-white shadow-xs">
                                    {{ $day['day_number'] }}
                                </span>
                            @else
                                <span class="text-xs font-semibold {{ $isCurrentMonth ? 'text-slate-800' : 'text-slate-400' }}">
                                    {{ $day['day_number'] }}
                                </span>
                            @endif
                        </div>

                        {{-- Kartu Batch Rencana Masak --}}
                        <div class="flex-1 space-y-1.5 overflow-y-auto max-h-[140px] pr-0.5">
                            @foreach ($plans as $plan)
                                @php
                                    $st = $statusTone[$plan->status->value] ?? ['bg-slate-50 border-slate-200 text-slate-700', 'bg-slate-400', $plan->status->value];
                                    $custName = $plan->cateringOrder?->customer?->name ?? 'Dapur Umum';
                                    $orderCode = $plan->cateringOrder?->order_number ?? '';
                                    $totalPorsi = $plan->totalPlannedQuantity();
                                    $menuList = $plan->details->map(fn($d) => $d->product?->name)->filter()->take(2)->implode(', ');
                                @endphp
                                <a href="{{ route('production-plans.show', $plan) }}"
                                   class="group block rounded-md border p-1.5 text-[11px] transition shadow-2xs hover:shadow-xs {{ $st[0] }}"
                                   title="Rencana #{{ $plan->plan_number }}&#10;Pesanan: {{ $orderCode }} ({{ $custName }})&#10;Target: {{ $totalPorsi }} Porsi&#10;Menu: {{ $menuList }}">
                                    <div class="flex items-center justify-between gap-1 leading-tight">
                                        <span class="font-bold truncate">{{ $plan->plan_number }}</span>
                                        <span class="inline-flex h-1.5 w-1.5 rounded-full {{ $st[1] }}"></span>
                                    </div>
                                    <div class="mt-0.5 truncate text-[10px] font-medium opacity-90">
                                        🍱 {{ $custName }}
                                    </div>
                                    <div class="mt-0.5 flex items-center justify-between text-[10px] opacity-75">
                                        <span>{{ number_format($totalPorsi, 0, ',', '.') }} porsi</span>
                                        <span class="font-semibold">{{ $st[2] }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        {{-- Quick Add jika slot kosong dan role admin/produksi --}}
                        @if ($plans->isEmpty() && $canManage && $isCurrentMonth)
                            <div class="mt-1 pt-1 opacity-0 hover:opacity-100 transition text-right">
                                <a href="{{ route('production-plans.create') }}?production_date={{ $day['date_string'] }}"
                                   class="inline-flex items-center gap-1 text-[10px] font-semibold text-primary hover:text-primary-dark"
                                   title="Buat rencana masak untuk tanggal ini">
                                    <x-icon name="plus" class="h-3 w-3" />
                                    <span>Jadwal</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Legenda Status --}}
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-card border border-line bg-white p-4 shadow-card text-xs">
            <span class="font-semibold text-slate-700">Legenda Status Dapur:</span>
            <div class="flex flex-wrap items-center gap-4">
                <span class="flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                    <span class="text-slate-600">Dijadwalkan</span>
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                    <span class="text-slate-600">Sedang Dimasak</span>
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    <span class="text-slate-600">Selesai</span>
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                    <span class="text-slate-600">Draft</span>
                </span>
            </div>
        </div>

    </div>
</x-app-layout>
