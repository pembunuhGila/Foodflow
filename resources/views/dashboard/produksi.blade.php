<x-app-layout>
    @php
    // Peta status -> token warna (sama dengan halaman Daftar Rencana Produksi)
    $statusTone = [
    'scheduled' => ['bg-info-bg text-info-text', 'bg-info-text', 'Dijadwalkan'],
    'in_progress' => ['bg-warning-bg text-warning-text', 'bg-warning-text', 'Sedang Dimasak'],
    'completed' => ['bg-success-bg text-success-text', 'bg-success-text', 'Selesai'],
    'draft' => ['bg-canvas text-muted', 'bg-muted', 'Draft'],
    'cancelled' => ['bg-danger-bg text-danger-text', 'bg-danger-text', 'Dibatalkan'],
    ];

    $metrics = [
    [
    'label' => 'Rencana Aktif',
    'value' => number_format($activePlanCount, 0, ',', '.'),
    'unit' => null,
    'caption' => 'Dijadwalkan atau sedang dimasak',
    'href' => route('production-plans.index'),
    'icon' => 'clipboard',
    'valueClass' => 'text-ink',
    ],
    [
    'label' => 'Target Hari Ini',
    'value' => number_format($todayTarget, 0, ',', '.'),
    'unit' => 'porsi',
    'caption' => 'Total target semua rencana hari ini',
    'href' => route('production-plans.index'),
    'icon' => 'clock',
    'valueClass' => 'text-ink',
    ],
    [
    'label' => 'Realisasi Hari Ini',
    'value' => number_format($todayActual, 0, ',', '.'),
    'unit' => 'porsi',
    'caption' => 'Porsi matang yang sudah dicatat',
    'href' => route('productions.index'),
    'icon' => 'check',
    'valueClass' => 'text-primary',
    ],
    [
    'label' => 'Permintaan Bahan',
    'value' => number_format($pendingMaterials, 0, ',', '.'),
    'unit' => null,
    'caption' => 'Menunggu verifikasi serah terima gudang',
    'href' => route('material-requests.index'),
    'icon' => 'calendar',
    'valueClass' => $pendingMaterials > 0 ? 'text-warning-text' : 'text-ink',
    ],
    ];

    $shortcuts = [
    ['title' => 'Rencana Produksi', 'text' => 'Atur jadwal, menu, target porsi, dan kebutuhan bahan.', 'href' => route('production-plans.index'), 'icon' => 'clipboard'],
    ['title' => 'Permintaan Bahan', 'text' => 'Ajukan bahan ke gudang dan pantau status serah terima.', 'href' => route('material-requests.index'), 'icon' => 'calendar'],
    ['title' => 'Realisasi Masak', 'text' => 'Catat porsi matang aktual dan evaluasi hasil masakan.', 'href' => route('productions.index'), 'icon' => 'check'],
    ];
    @endphp

    <x-page-header eyebrow="Dashboard Produksi"
        :title="'Selamat bekerja, ' . Auth::user()->name"
        subtitle="Pantau rencana masak, capaian porsi, dan kesiapan bahan hari ini.">
        <x-slot name="actions">
            <x-button :href="route('production-plans.create')">
                <x-icon name="plus" class="h-4 w-4" />
                Buat Rencana
            </x-button>
        </x-slot>
    </x-page-header>

    <div class="space-y-6">

        {{-- Kartu metrik --}}
        <section class="grid grid-cols-2 gap-4 xl:grid-cols-4" aria-label="Ringkasan produksi">
            @foreach ($metrics as $m)
            <a href="{{ $m['href'] }}"
                class="rounded-card border border-line bg-white p-5 shadow-card transition hover:border-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/30">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-label uppercase text-muted">{{ $m['label'] }}</div>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="font-display text-h1 {{ $m['valueClass'] }}">{{ $m['value'] }}</span>
                            @if ($m['unit'])
                            <span class="text-small text-muted">{{ $m['unit'] }}</span>
                            @endif
                        </div>
                    </div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-input bg-info-bg text-primary">
                        <x-icon :name="$m['icon']" />
                    </span>
                </div>
                <div class="mt-1 text-small text-muted">{{ $m['caption'] }}</div>
            </a>
            @endforeach
        </section>

        {{-- Grafik mingguan + progres hari ini --}}
        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">

            <x-card class="xl:col-span-2">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="font-display text-h3 text-ink">Target vs realisasi</h3>
                        <p class="text-small text-muted">Porsi target dan porsi matang selama 7 hari terakhir.</p>
                    </div>
                    <div class="flex items-center gap-4 text-small text-muted">
                        <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-primary"></span>Target</span>
                        <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-success-text"></span>Realisasi</span>
                    </div>
                </div>

                <div class="relative mt-5 h-72">
                    <canvas id="productionWeeklyChart" role="img" aria-label="Grafik batang target dan realisasi porsi 7 hari terakhir"></canvas>
                </div>
            </x-card>

            <x-card>
                <h3 class="font-display text-h3 text-ink">Progres hari ini</h3>
                <p class="text-small text-muted">Capaian terhadap target porsi harian.</p>

                <div class="relative mx-auto my-6 h-48">
                    <canvas id="todayProgressChart" role="img" aria-label="Grafik donat capaian target hari ini"></canvas>
                    <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                        <span class="font-display text-h2 text-primary">{{ number_format($completionRate, 1, ',', '.') }}%</span>
                        <span class="text-small text-muted">Tercapai</span>
                    </div>
                </div>

                <dl class="space-y-2">
                    <div class="flex items-center justify-between rounded-input bg-canvas px-4 py-3">
                        <dt class="flex items-center gap-2 text-small text-muted">
                            <span class="h-2.5 w-2.5 rounded-full bg-primary"></span>Target
                        </dt>
                        <dd class="font-display text-body font-semibold text-ink">{{ number_format($todayTarget, 0, ',', '.') }} <span class="font-sans text-small font-normal text-muted">porsi</span></dd>
                    </div>
                    <div class="flex items-center justify-between rounded-input bg-canvas px-4 py-3">
                        <dt class="flex items-center gap-2 text-small text-muted">
                            <span class="h-2.5 w-2.5 rounded-full bg-success-text"></span>Terealisasi
                        </dt>
                        <dd class="font-display text-body font-semibold text-success-text">{{ number_format($todayActual, 0, ',', '.') }} <span class="font-sans text-small font-normal text-muted">porsi</span></dd>
                    </div>
                </dl>

                <div class="mt-4">
                    @if ($todayTarget === 0)
                    <div class="rounded-input bg-canvas p-3 text-center text-small text-muted">Belum ada target produksi hari ini.</div>
                    @elseif ($todayActual >= $todayTarget)
                    <div class="flex items-center justify-center gap-2 rounded-input bg-success-bg p-3 text-small font-medium text-success-text" role="status">
                        <x-icon name="check" class="h-4 w-4" />
                        Target porsi hari ini terpenuhi.
                    </div>
                    @else
                    <div class="rounded-input bg-warning-bg p-3 text-center text-small text-warning-text" role="status">
                        Kurang <strong>{{ number_format($todayTarget - $todayActual, 0, ',', '.') }} porsi</strong> lagi untuk mencapai target.
                    </div>
                    @endif
                </div>
            </x-card>

        </section>

        {{-- Tabel rencana aktif --}}
        <x-card :padding="false" class="overflow-hidden">
            <div class="flex flex-col gap-2 border-b border-line px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-display text-h3 text-ink">Rencana produksi aktif</h3>
                    <p class="text-small text-muted">Rencana yang dijadwalkan atau sedang dimasak.</p>
                </div>
                <a href="{{ route('production-plans.index') }}" class="text-small font-medium text-primary transition hover:text-primary-dark">Lihat semua rencana</a>
            </div>

            @if ($activePlans->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="font-medium text-ink">Belum ada rencana produksi aktif.</p>
                <p class="mx-auto mt-1 max-w-sm text-small text-muted">Rencana yang dijadwalkan atau sedang dimasak akan muncul di sini.</p>
                <x-button :href="route('production-plans.create')" class="mt-4">
                    <x-icon name="plus" class="h-4 w-4" />
                    Buat Rencana Baru
                </x-button>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">No. Rencana</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Tanggal Masak</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Menu</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Target</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Realisasi</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Status</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @foreach ($activePlans as $plan)
                        @php
                        $st = $plan->status instanceof \BackedEnum ? $plan->status->value : (string) $plan->status;
                        $tone = $statusTone[$st] ?? ['bg-canvas text-muted', 'bg-muted', $st];
                        @endphp
                        <tr class="transition hover:bg-primary/5">
                            <td class="whitespace-nowrap px-6 py-4">
                                <a href="{{ route('production-plans.show', $plan) }}" class="font-medium text-primary transition hover:text-primary-dark">{{ $plan->plan_number }}</a>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-ink">
                                {{ $plan->production_date?->translatedFormat('d M Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-ink">{{ $plan->details->count() }} menu</div>
                                <div class="max-w-xs truncate text-small text-muted">
                                    {{ $plan->details->pluck('product.name')->filter()->join(', ') ?: 'Belum ada menu' }}
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right font-medium text-ink">
                                {{ number_format($plan->totalPlannedQuantity(), 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right font-medium text-success-text">
                                {{ number_format($plan->totalActualQuantity(), 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-label uppercase {{ $tone[0] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $tone[1] }}"></span>{{ $tone[2] }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-small font-medium">
                                <a href="{{ route('production-plans.show', $plan) }}" class="text-primary transition hover:text-primary-dark">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </x-card>

        {{-- Akses cepat --}}
        <section aria-labelledby="akses-cepat">
            <h3 id="akses-cepat" class="font-display text-h3 text-ink">Akses cepat</h3>
            <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-3">
                @foreach ($shortcuts as $s)
                <a href="{{ $s['href'] }}"
                    class="group flex items-start gap-4 rounded-card border border-line bg-white p-5 shadow-card transition hover:border-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-input bg-info-bg text-primary transition group-hover:bg-primary group-hover:text-white">
                        <x-icon :name="$s['icon']" />
                    </span>
                    <span>
                        <span class="block font-display text-body font-semibold text-ink group-hover:text-primary">{{ $s['title'] }}</span>
                        <span class="mt-0.5 block text-small text-muted">{{ $s['text'] }}</span>
                    </span>
                </a>
                @endforeach
            </div>
        </section>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Warna chart mengikuti token di tailwind.config.js (Chart.js tidak bisa membaca class Tailwind)
            const C = {
                primary: '#7C5CE0', // primary
                success: '#15803D', // success-text
                ink: '#1E1B4B', // ink
                muted: '#6B7280', // muted
                line: '#ECE8F7', // line
                canvas: '#F7F5FD', // canvas
            };

            Chart.defaults.font.family = 'Inter, ui-sans-serif, system-ui, sans-serif';
            Chart.defaults.color = C.muted;

            const weeklyData = @json($weeklyChart);
            const labels = weeklyData.map(item => item.label + ' (' + item.date + ')');

            const weeklyCtx = document.getElementById('productionWeeklyChart');
            if (weeklyCtx) {
                new Chart(weeklyCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Target Porsi',
                                data: weeklyData.map(i => i.target),
                                backgroundColor: C.primary,
                                borderRadius: 6,
                                maxBarThickness: 20
                            },
                            {
                                label: 'Realisasi Porsi',
                                data: weeklyData.map(i => i.actual),
                                backgroundColor: C.success,
                                borderRadius: 6,
                                maxBarThickness: 20
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: C.ink,
                                padding: 10,
                                cornerRadius: 12,
                                callbacks: {
                                    label: ctx => ctx.dataset.label + ': ' + ctx.parsed.y.toLocaleString('id-ID') + ' porsi'
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            y: {
                                border: {
                                    display: false
                                },
                                grid: {
                                    color: C.line
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            const progressCtx = document.getElementById('todayProgressChart');
            if (progressCtx) {
                const rate = Math.min(100, @json((float) $completionRate));
                new Chart(progressCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Tercapai', 'Sisa'],
                        datasets: [{
                            data: [rate, Math.max(0, 100 - rate)],
                            backgroundColor: [C.primary, C.canvas],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: false
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>