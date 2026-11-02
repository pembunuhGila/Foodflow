<x-app-layout>
    @php
    // ---------- Helper ----------
    $today = $today ?? now();
    $r = fn (string $name, $params = []) => \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : '#';
    $firstRoute = function (array $names, $params = []) {
    $found = collect($names)->first(fn ($n) => \Illuminate\Support\Facades\Route::has($n));
    return $found ? route($found, $params) : '#';
    };
    $hasCol = fn (string $table, string $col) => \Illuminate\Support\Facades\Schema::hasColumn($table, $col);
    $pickCol = fn (string $table, array $options) => collect($options)->first(fn ($c) => \Illuminate\Support\Facades\Schema::hasColumn($table, $c));
    $val = fn ($s) => $s instanceof \BackedEnum ? $s->value : (string) $s;
    $pct = fn ($a, $b) => $b > 0 ? (int) min(100, round(($a / $b) * 100)) : 0;
    $fmt = fn ($v) => number_format((float) $v, 0, ',', '.');
    $rupiah = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');
    $enumMatch = fn (string $enum, string $regex) => collect($enum::cases())
    ->filter(fn ($c) => preg_match($regex, ($c instanceof \BackedEnum ? $c->value : '') . $c->name))
    ->map(fn ($c) => $c instanceof \BackedEnum ? $c->value : $c->name)
    ->values()->all();

    // ---------- Periode ----------
    $periode = in_array(request('periode'), ['today', '7', 'month'], true) ? request('periode') : 'today';
    $from = match ($periode) {
    '7' => $today->copy()->subDays(6)->startOfDay(),
    'month' => $today->copy()->startOfMonth(),
    default => $today->copy()->startOfDay(),
    };
    $to = $today->copy()->endOfDay();
    $periodeLabel = ['today' => 'Hari ini', '7' => '7 hari terakhir', 'month' => 'Bulan ini'][$periode];
    $periodTabs = [['today', 'Hari Ini'], ['7', '7 Hari'], ['month', 'Bulan Ini']];
    $fromStr = $from->toDateString();
    $toStr = $to->toDateString();

    // ---------- Produksi ----------
    $validStatuses = ['scheduled', 'in_progress', 'completed'];
    $plans = \App\Models\ProductionPlan::with('details.product')
    ->whereDate('production_date', '>=', $fromStr)
    ->whereDate('production_date', '<=', $toStr)
        ->get();
        $validPlans = $plans->filter(fn ($p) => in_array($val($p->status), $validStatuses, true));
        $target = (int) $validPlans->sum(fn ($p) => $p->totalPlannedQuantity());
        $actual = (int) $validPlans->sum(fn ($p) => $p->totalActualQuantity());
        $achievement = $pct($actual, $target);
        $planDone = $validPlans->filter(fn ($p) => $val($p->status) === 'completed')->count();

        $statusDefs = [
        'scheduled' => ['Dijadwalkan', 'bg-info-text'],
        'in_progress' => ['Sedang Dimasak', 'bg-warning-text'],
        'completed' => ['Selesai', 'bg-success-text'],
        'draft' => ['Draft', 'bg-muted'],
        'cancelled' => ['Dibatalkan', 'bg-danger-text'],
        ];
        $statusCounts = $plans->groupBy(fn ($p) => $val($p->status))->map->count();
        $planTotal = $plans->count();

        // Tren harian (7 hari, atau awal bulan sampai hari ini)
        $chartStart = $periode === 'month' ? $today->copy()->startOfMonth() : $today->copy()->subDays(6)->startOfDay();
        $chartPlans = \App\Models\ProductionPlan::with('details')
        ->whereDate('production_date', '>=', $chartStart->toDateString())
        ->whereDate('production_date', '<=', $toStr)
            ->get()
            ->filter(fn ($p) => in_array($val($p->status), $validStatuses, true))
            ->groupBy(fn ($p) => $p->production_date?->toDateString());
            $trend = [];
            for ($d = $chartStart->copy(); $d->lte($today); $d->addDay()) {
            $dayPlans = $chartPlans->get($d->toDateString(), collect());
            $trend[] = [
            'label' => $d->copy()->locale('id')->translatedFormat('d M'),
            'target' => (int) $dayPlans->sum(fn ($p) => $p->totalPlannedQuantity()),
            'actual' => (int) $dayPlans->sum(fn ($p) => $p->totalActualQuantity()),
            ];
            }

            // Menu terbanyak
            $menuTotals = $validPlans
            ->flatMap(fn ($p) => $p->details)
            ->groupBy(fn ($d) => $d->product?->name ?? 'Menu tidak ditemukan')
            ->map(fn ($g) => (int) $g->sum('planned_quantity'))
            ->sortDesc();
            $topMenus = $menuTotals->take(4);
            $otherMenus = $menuTotals->slice(4)->sum();
            $menuRows = $topMenus->map(fn ($v, $k) => ['name' => $k, 'qty' => $v])->values();
            if ($otherMenus > 0) {
            $menuRows->push(['name' => 'Menu lainnya', 'qty' => (int) $otherMenus]);
            }
            $menuSum = (int) $menuRows->sum('qty');
            $menuSwatch = ['bg-primary', 'bg-secondary', 'bg-accent', 'bg-tertiary', 'bg-muted'];

            // ---------- Pesanan & pembelian ----------
            $orderTable = (new \App\Models\CateringOrder)->getTable();
            $orderDateCol = $pickCol($orderTable, ['delivery_date', 'event_date', 'order_date', 'created_at']) ?? 'created_at';
            $ordersInPeriod = \App\Models\CateringOrder::whereDate($orderDateCol, '>=', $fromStr)->whereDate($orderDateCol, '<=', $toStr)->count();
                $customerCount = \App\Models\Customer::count();

                $poTable = (new \App\Models\PurchaseOrder)->getTable();
                $poAmountCol = $pickCol($poTable, ['total_amount', 'grand_total', 'total_price', 'total']);
                $poDateCol = $pickCol($poTable, ['order_date', 'po_date', 'created_at']) ?? 'created_at';
                $poCancelled = $enumMatch(\App\Enums\PurchaseOrderStatus::class, '/cancel|batal/i');
                $poDone = $enumMatch(\App\Enums\PurchaseOrderStatus::class, '/draft|cancel|complete|received|selesai|batal/i');
                $poQuery = fn () => \App\Models\PurchaseOrder::query()
                ->whereDate($poDateCol, '>=', $fromStr)->whereDate($poDateCol, '<=', $toStr)
                    ->when($poCancelled, fn ($q) => $q->whereNotIn('status', $poCancelled));
                    $poValue = $poAmountCol ? (float) $poQuery()->sum($poAmountCol) : null;
                    $poInPeriod = $poQuery()->count();
                    $poActive = \App\Models\PurchaseOrder::query()->whereNotIn('status', $poDone)->count();

                    // ---------- Gudang ----------
                    $ingredients = fn () => \App\Models\Ingredient::query()->where('is_active', true);
                    $ingredientTotal = $ingredients()->count();
                    $ingredientSafe = $ingredients()->whereColumn('current_stock', '>', 'minimum_stock')->count();
                    $ingredientOut = $ingredients()->where('current_stock', '<=', 0)->count();
                        $attentionCount = $ingredients()->whereColumn('current_stock', '<=', 'minimum_stock' )->count();
                            $lowStockItems = $ingredients()->whereColumn('current_stock', '<=', 'minimum_stock' )->orderBy('current_stock')->limit(5)->get();
                                $stockHealth = $pct($ingredientSafe, $ingredientTotal);

                                $rcTable = (new \App\Models\MaterialReceipt)->getTable();
                                $rcDateCol = $pickCol($rcTable, ['receipt_date', 'received_at', 'received_date', 'created_at']) ?? 'created_at';
                                $receiptsInPeriod = \App\Models\MaterialReceipt::whereDate($rcDateCol, '>=', $fromStr)->whereDate($rcDateCol, '<=', $toStr)->count();

                                    $reqTable = (new \App\Models\MaterialRequest)->getTable();
                                    $pendingRequestCount = \App\Models\MaterialRequest::query()->when(
                                    $hasCol($reqTable, 'status'),
                                    fn ($q) => $q->whereIn('status', ['pending', 'submitted', 'requested', 'menunggu', 'diajukan', 'waiting'])
                                    )->count();

                                    // ---------- Tampilan ----------
                                    $metrics = [
                                    ['label' => 'Realisasi Produksi', 'value' => $fmt($actual), 'unit' => 'porsi', 'caption' => 'Pencapaian ' . $achievement . '% dari target ' . $fmt($target) . ' porsi', 'href' => $firstRoute(['owner.production-monitoring.index']), 'icon' => 'check', 'valueClass' => 'text-primary'],
                                    ['label' => 'Pesanan Katering', 'value' => $fmt($ordersInPeriod), 'unit' => 'pesanan', 'caption' => $fmt($customerCount) . ' pelanggan terdaftar', 'href' => $r('catering-orders.index'), 'icon' => 'clipboard', 'valueClass' => 'text-ink'],
                                    $poValue !== null
                                    ? ['label' => 'Pembelian Bahan', 'value' => $rupiah($poValue), 'unit' => '', 'caption' => $fmt($poInPeriod) . ' purchase order', 'href' => $r('purchase-orders.index'), 'icon' => 'calendar', 'valueClass' => 'text-ink']
                                    : ['label' => 'Penerimaan Bahan', 'value' => $fmt($receiptsInPeriod), 'unit' => 'penerimaan', 'caption' => $fmt($poInPeriod) . ' purchase order pada periode ini', 'href' => $r('material-receipts.index'), 'icon' => 'calendar', 'valueClass' => 'text-ink'],
                                    ['label' => 'Kesehatan Stok', 'value' => $stockHealth . '%', 'unit' => '', 'caption' => $fmt($attentionCount) . ' bahan menipis atau habis', 'href' => $firstRoute(['owner.stock-monitoring.index', 'owner.reports.index']), 'icon' => 'warning', 'valueClass' => $attentionCount > 0 ? 'text-warning-text' : 'text-success-text'],
                                    ];

                                    $divisions = [
                                    [
                                    'title' => 'Gudang', 'link' => 'Lihat detail gudang', 'href' => $firstRoute(['owner.stock-monitoring.index', 'owner.reports.index']),
                                    'tiles' => [
                                    ['Stok habis', $fmt($ingredientOut) . ' bahan', $ingredientOut > 0 ? 'text-danger-text' : 'text-ink'],
                                    ['Stok menipis', $fmt(max(0, $attentionCount - $ingredientOut)) . ' bahan', 'text-ink'],
                                    ['Permintaan menunggu', $fmt($pendingRequestCount), $pendingRequestCount > 0 ? 'text-warning-text' : 'text-ink'],
                                    ['Penerimaan', $fmt($receiptsInPeriod), 'text-ink'],
                                    ],
                                    ],
                                    [
                                    'title' => 'Produksi', 'link' => 'Lihat detail produksi', 'href' => $firstRoute(['owner.production-monitoring.index']),
                                    'tiles' => [
                                    ['Porsi terealisasi', $fmt($actual), 'text-primary'],
                                    ['Pencapaian target', $achievement . '%', 'text-ink'],
                                    ['Rencana selesai', $planDone . ' / ' . $validPlans->count(), 'text-ink'],
                                    ['Rencana dibatalkan', $fmt($statusCounts->get('cancelled', 0)), $statusCounts->get('cancelled', 0) > 0 ? 'text-danger-text' : 'text-ink'],
                                    ],
                                    ],
                                    [
                                    'title' => 'Pesanan dan Pengadaan', 'link' => 'Lihat pesanan', 'href' => $r('catering-orders.index'),
                                    'tiles' => [
                                    ['Pesanan katering', $fmt($ordersInPeriod), 'text-ink'],
                                    ['Pelanggan', $fmt($customerCount), 'text-ink'],
                                    ['PO pada periode', $fmt($poInPeriod), 'text-ink'],
                                    ['PO belum selesai', $fmt($poActive), $poActive > 0 ? 'text-warning-text' : 'text-ink'],
                                    ],
                                    ],
                                    ];

                                    $secondaryBtn = 'inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-5 py-2.5 text-body font-medium text-primary transition hover:bg-primary/10';
                                    @endphp

                                    <x-page-header eyebrow="Dashboard Owner"
                                        title="Ringkasan eksekutif"
                                        subtitle="Pantau capaian produksi, stok, dan pengadaan untuk mengambil keputusan.">
                                        <x-slot name="actions">
                                            {{-- Filter periode --}}
                                            <nav class="flex items-center gap-1 rounded-full bg-primary/5 p-1" aria-label="Filter periode">
                                                @foreach ($periodTabs as [$key, $label])
                                                <a href="{{ url()->current() }}?periode={{ $key }}"
                                                    @if ($periode===$key) aria-current="page" @endif
                                                    class="whitespace-nowrap rounded-full px-4 py-2 text-center text-small font-semibold transition {{ $periode === $key ? 'bg-primary text-white shadow-sm' : 'text-ink hover:text-primary' }}">
                                                    {{ $label }}
                                                </a>
                                                @endforeach
                                            </nav>
                                            <a href="{{ $firstRoute(['owner.reports.index']) }}" class="{{ $secondaryBtn }}">Laporan</a>
                                            <x-button :href="$firstRoute(['owner.production-monitoring.index'])">
                                                Monitoring Produksi
                                            </x-button>
                                        </x-slot>
                                    </x-page-header>

                                    <div class="space-y-6">

                                        {{-- Kartu metrik --}}
                                        <section class="grid grid-cols-2 gap-4 xl:grid-cols-4" aria-label="Ringkasan periode {{ $periodeLabel }}">
                                            @foreach ($metrics as $m)
                                            <a href="{{ $m['href'] }}"
                                                class="rounded-card border border-line bg-white p-5 shadow-card transition hover:border-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div class="min-w-0">
                                                        <div class="text-label uppercase text-muted">{{ $m['label'] }}</div>
                                                        <div class="mt-2 flex flex-wrap items-baseline gap-1.5">
                                                            <span class="font-display text-h1 {{ $m['valueClass'] }}">{{ $m['value'] }}</span>
                                                            @if ($m['unit'])
                                                            <span class="text-small text-muted">{{ $m['unit'] }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-input {{ $m['icon'] === 'warning' && $attentionCount > 0 ? 'bg-warning-bg text-warning-text' : 'bg-info-bg text-primary' }}">
                                                        @if ($m['icon'] === 'warning')
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.008v.008H12v-.008Z" />
                                                        </svg>
                                                        @else
                                                        <x-icon :name="$m['icon']" />
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="mt-1 text-small text-muted">{{ $m['caption'] }}</div>
                                            </a>
                                            @endforeach
                                        </section>

                                        {{-- Tren produksi + menu terbanyak --}}
                                        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">

                                            <x-card class="xl:col-span-2">
                                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                    <div>
                                                        <h3 class="font-display text-h3 text-ink">Tren target dan realisasi</h3>
                                                        <p class="text-small text-muted">Porsi per hari, {{ $periode === 'month' ? 'sejak awal bulan' : '7 hari terakhir' }}.</p>
                                                    </div>
                                                    <div class="flex items-center gap-4 text-small text-muted">
                                                        <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-primary"></span>Target</span>
                                                        <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-accent"></span>Realisasi</span>
                                                    </div>
                                                </div>

                                                <div class="relative mt-5 h-64">
                                                    <canvas id="ownerTrendChart" role="img" aria-label="Grafik garis target dan realisasi porsi per hari"></canvas>
                                                </div>

                                                <div class="mt-6 border-t border-line pt-5">
                                                    <div class="flex items-baseline justify-between gap-3">
                                                        <h4 class="font-display text-body font-semibold text-ink">Status rencana produksi</h4>
                                                        <span class="text-small text-muted">{{ $fmt($planTotal) }} rencana · {{ $periodeLabel }}</span>
                                                    </div>

                                                    @if ($planTotal === 0)
                                                    <p class="mt-3 rounded-input bg-canvas p-3 text-center text-small text-muted">Belum ada rencana produksi pada periode ini.</p>
                                                    @else
                                                    <div class="mt-3 flex h-3 overflow-hidden rounded-full bg-canvas" role="img" aria-label="Komposisi status rencana produksi">
                                                        @foreach ($statusDefs as $key => [$label, $swatch])
                                                        @if ($statusCounts->get($key, 0) > 0)
                                                        <div class="h-full {{ $swatch }}" style="width: {{ ($statusCounts->get($key) / $planTotal) * 100 }}%"></div>
                                                        @endif
                                                        @endforeach
                                                    </div>
                                                    <dl class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-5">
                                                        @foreach ($statusDefs as $key => [$label, $swatch])
                                                        <div class="rounded-input bg-canvas px-3 py-2">
                                                            <dt class="flex items-center gap-1.5 text-small text-muted"><span class="h-2 w-2 rounded-full {{ $swatch }}"></span>{{ $label }}</dt>
                                                            <dd class="font-display text-body font-semibold text-ink">{{ $fmt($statusCounts->get($key, 0)) }}</dd>
                                                        </div>
                                                        @endforeach
                                                    </dl>
                                                    @endif
                                                </div>
                                            </x-card>

                                            <x-card>
                                                <h3 class="font-display text-h3 text-ink">Menu terbanyak</h3>
                                                <p class="text-small text-muted">Porsi terencana per menu, {{ strtolower($periodeLabel) }}.</p>

                                                @if ($menuSum === 0)
                                                <p class="mt-6 rounded-input bg-canvas p-6 text-center text-small text-muted">Belum ada menu yang direncanakan pada periode ini.</p>
                                                @else
                                                <div class="relative mx-auto my-6 h-48">
                                                    <canvas id="ownerMenuChart" role="img" aria-label="Grafik donat porsi per menu"></canvas>
                                                    <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                                                        <span class="font-display text-h2 text-primary">{{ $fmt($menuSum) }}</span>
                                                        <span class="text-small text-muted">porsi</span>
                                                    </div>
                                                </div>

                                                <ul class="space-y-2">
                                                    @foreach ($menuRows as $i => $row)
                                                    <li class="flex items-center justify-between gap-3 rounded-input bg-canvas px-4 py-3">
                                                        <span class="flex min-w-0 items-center gap-2 text-small text-muted">
                                                            <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $menuSwatch[$i] ?? 'bg-muted' }}"></span>
                                                            <span class="truncate">{{ $row['name'] }}</span>
                                                        </span>
                                                        <span class="shrink-0 font-display text-body font-semibold text-ink">{{ $pct($row['qty'], $menuSum) }}%</span>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                                @endif
                                            </x-card>

                                        </section>

                                        {{-- Kesehatan operasional lintas divisi --}}
                                        <section aria-labelledby="kesehatan-ops">
                                            <h3 id="kesehatan-ops" class="font-display text-h3 text-ink">Kesehatan operasional</h3>
                                            <p class="text-small text-muted">Kondisi tiap divisi pada periode {{ strtolower($periodeLabel) }}.</p>

                                            <div class="mt-3 grid grid-cols-1 gap-6 lg:grid-cols-3">
                                                @foreach ($divisions as $d)
                                                <x-card>
                                                    <h4 class="font-display text-body font-semibold text-ink">{{ $d['title'] }}</h4>
                                                    <div class="mt-4 grid grid-cols-2 gap-3">
                                                        @foreach ($d['tiles'] as [$label, $value, $tone])
                                                        <div class="rounded-input bg-canvas px-4 py-3">
                                                            <div class="text-small text-muted">{{ $label }}</div>
                                                            <div class="mt-0.5 font-display text-h3 {{ $tone }}">{{ $value }}</div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                    <a href="{{ $d['href'] }}" class="mt-4 inline-block text-small font-medium text-primary transition hover:text-primary-dark">{{ $d['link'] }}</a>
                                                </x-card>
                                                @endforeach
                                            </div>
                                        </section>

                                        {{-- Stok yang perlu diperhatikan --}}
                                        <x-card :padding="false" class="overflow-hidden">
                                            <div class="flex flex-col gap-2 border-b border-line px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                                                <div>
                                                    <h3 class="font-display text-h3 text-ink">Stok yang perlu diperhatikan</h3>
                                                    <p class="text-small text-muted">Lima bahan dengan stok paling rendah di bawah batas minimum.</p>
                                                </div>
                                                <a href="{{ $firstRoute(['owner.stock-monitoring.index', 'owner.reports.index']) }}" class="text-small font-medium text-primary transition hover:text-primary-dark">Buka monitoring stok</a>
                                            </div>

                                            @if ($lowStockItems->isEmpty())
                                            <div class="px-6 py-12 text-center">
                                                <p class="font-medium text-ink">Semua stok bahan di atas batas minimum.</p>
                                            </div>
                                            @else
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full text-body">
                                                    <thead class="bg-primary/5">
                                                        <tr>
                                                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Bahan</th>
                                                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Level Stok</th>
                                                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Stok</th>
                                                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Minimum</th>
                                                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-line bg-white">
                                                        @foreach ($lowStockItems as $item)
                                                        @php
                                                        $isOut = (float) $item->current_stock <= 0;
                                                            $level=$pct((float) $item->current_stock, (float) $item->minimum_stock);
                                                            @endphp
                                                            <tr class="transition hover:bg-primary/5">
                                                                <td class="whitespace-nowrap px-6 py-4 font-medium text-ink">{{ $item->name }}</td>
                                                                <td class="px-6 py-4">
                                                                    <div class="h-2 w-40 overflow-hidden rounded-full bg-canvas" role="progressbar" aria-valuenow="{{ $level }}" aria-valuemin="0" aria-valuemax="100" aria-label="Stok {{ $item->name }} terhadap batas minimum">
                                                                        <div class="h-full rounded-full {{ $isOut ? 'bg-danger-text' : 'bg-warning-text' }}" style="width: {{ $level }}%"></div>
                                                                    </div>
                                                                </td>
                                                                <td class="whitespace-nowrap px-6 py-4 text-right font-medium {{ $isOut ? 'text-danger-text' : 'text-warning-text' }}">
                                                                    {{ rtrim(rtrim(number_format((float) $item->current_stock, 2, ',', '.'), '0'), ',') }} <span class="text-small font-normal text-muted">{{ $item->unit }}</span>
                                                                </td>
                                                                <td class="whitespace-nowrap px-6 py-4 text-right text-ink">
                                                                    {{ rtrim(rtrim(number_format((float) $item->minimum_stock, 2, ',', '.'), '0'), ',') }} <span class="text-small text-muted">{{ $item->unit }}</span>
                                                                </td>
                                                                <td class="whitespace-nowrap px-6 py-4">
                                                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-label uppercase {{ $isOut ? 'bg-danger-bg text-danger-text' : 'bg-warning-bg text-warning-text' }}">
                                                                        <span class="h-1.5 w-1.5 rounded-full {{ $isOut ? 'bg-danger-text' : 'bg-warning-text' }}"></span>{{ $isOut ? 'Stok habis' : 'Menipis' }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            @endif
                                        </x-card>

                                    </div>

                                    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            // Warna chart mengikuti token di tailwind.config.js (Chart.js tidak bisa membaca class Tailwind)
                                            const C = {
                                                primary: '#7C5CE0', // primary
                                                secondary: '#4C2FB5', // secondary
                                                tertiary: '#F5D0FE', // tertiary
                                                accent: '#06B6D4', // accent
                                                accentDark: '#0891B2', // accent-dark (garis tren)
                                                ink: '#1E1B4B', // ink
                                                muted: '#6B7280', // muted
                                                line: '#ECE8F7', // line
                                            };

                                            Chart.defaults.font.family = 'Inter, ui-sans-serif, system-ui, sans-serif';
                                            Chart.defaults.color = C.muted;

                                            const trend = @json($trend);
                                            const last = trend.length - 1;

                                            const trendCtx = document.getElementById('ownerTrendChart');
                                            if (trendCtx) {
                                                new Chart(trendCtx, {
                                                    type: 'line',
                                                    data: {
                                                        labels: trend.map(i => i.label),
                                                        datasets: [{
                                                                label: 'Target Porsi',
                                                                data: trend.map(i => i.target),
                                                                borderColor: C.primary,
                                                                backgroundColor: C.primary + '1F',
                                                                borderWidth: 2.5,
                                                                cubicInterpolationMode: 'monotone',
                                                                fill: true,
                                                                pointRadius: ctx => ctx.dataIndex === last ? 5 : 0,
                                                                pointBackgroundColor: C.primary,
                                                                pointHoverRadius: 5
                                                            },
                                                            {
                                                                label: 'Realisasi Porsi',
                                                                data: trend.map(i => i.actual),
                                                                borderColor: C.accentDark,
                                                                backgroundColor: C.accent + '1A',
                                                                borderWidth: 2.5,
                                                                cubicInterpolationMode: 'monotone',
                                                                fill: true,
                                                                pointRadius: ctx => ctx.dataIndex === last ? 5 : 0,
                                                                pointBackgroundColor: C.accentDark,
                                                                pointHoverRadius: 5
                                                            }
                                                        ]
                                                    },
                                                    options: {
                                                        responsive: true,
                                                        maintainAspectRatio: false,
                                                        interaction: {
                                                            mode: 'index',
                                                            intersect: false
                                                        },
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
                                                                border: {
                                                                    display: false
                                                                },
                                                                ticks: {
                                                                    font: {
                                                                        size: 11
                                                                    },
                                                                    maxTicksLimit: 8,
                                                                    color: ctx => ctx.index === last ? C.primary : C.muted
                                                                }
                                                            },
                                                            y: {
                                                                beginAtZero: true,
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

                                            const menuCtx = document.getElementById('ownerMenuChart');
                                            if (menuCtx) {
                                                const menus = @json($menuRows);
                                                new Chart(menuCtx, {
                                                    type: 'doughnut',
                                                    data: {
                                                        labels: menus.map(m => m.name),
                                                        datasets: [{
                                                            data: menus.map(m => m.qty),
                                                            backgroundColor: [C.primary, C.secondary, C.accent, C.tertiary, C.muted],
                                                            borderWidth: 0
                                                        }]
                                                    },
                                                    options: {
                                                        responsive: true,
                                                        maintainAspectRatio: false,
                                                        cutout: '72%',
                                                        plugins: {
                                                            legend: {
                                                                display: false
                                                            },
                                                            tooltip: {
                                                                backgroundColor: C.ink,
                                                                cornerRadius: 12,
                                                                callbacks: {
                                                                    label: ctx => ctx.label + ': ' + ctx.parsed.toLocaleString('id-ID') + ' porsi'
                                                                }
                                                            }
                                                        }
                                                    }
                                                });
                                            }
                                        });
                                    </script>
</x-app-layout>