<x-app-layout>
    @php
    // ---------- Data ----------
    $today = $today ?? now();
    $r = fn (string $name, $params = []) => \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : '#';
    $hasCol = fn (string $table, string $col) => \Illuminate\Support\Facades\Schema::hasColumn($table, $col);
    $pickCol = fn (string $table, array $options) => collect($options)->first(fn ($c) => \Illuminate\Support\Facades\Schema::hasColumn($table, $c));

    // Bahan baku (kolom is_active, current_stock, minimum_stock sudah terkonfirmasi dari query di route)
    $activeIngredients = fn () => \App\Models\Ingredient::query()->where('is_active', true);
    $totalIngredients = $activeIngredients()->count();
    $stockSummary = [
        'safe' => $activeIngredients()->whereColumn('current_stock', '>', 'minimum_stock')->count(),
        'low'  => $activeIngredients()->where('current_stock', '>', 0)->whereColumn('current_stock', '<=', 'minimum_stock')->count(),
        'out'  => $activeIngredients()->where('current_stock', '<=', 0)->count(),
    ];
    $attentionCount = $stockSummary['low'] + $stockSummary['out'];
    $lowStockItems = $activeIngredients()->whereColumn('current_stock', '<=', 'minimum_stock')->orderBy('current_stock')->limit(8)->get();

    // Mutasi stok
    $txModel = \App\Models\IngredientStockTransaction::class;
    $txTable = (new $txModel)->getTable();
    $typeCol = $pickCol($txTable, ['type', 'transaction_type', 'movement_type']);
    $qtyCol  = $pickCol($txTable, ['quantity', 'qty', 'amount']);
    $classify = function ($tx) use ($typeCol, $qtyCol) {
        $raw = $typeCol ? $tx->{$typeCol} : null;
        $raw = $raw instanceof \BackedEnum ? $raw->value : $raw;
        $v = strtolower((string) $raw);
        if ($v === '') return ((float) ($qtyCol ? $tx->{$qtyCol} : 0)) < 0 ? 'out' : 'in';
        if (preg_match('/^(in|masuk|receipt|purchase|stock_in|penerimaan)/', $v)) return 'in';
        if (preg_match('/^(out|keluar|usage|consum|production|stock_out|pengeluaran|issue)/', $v)) return 'out';
        return 'adjustment';
    };
    $qtyOf = fn ($tx) => abs((float) ($qtyCol ? $tx->{$qtyCol} : 0));

    $weekTx = $txModel::where('created_at', '>=', $today->copy()->subDays(6)->startOfDay())->get();
    $flowChart = collect(range(6, 0))->map(function ($ago) use ($today, $weekTx, $classify, $qtyOf) {
        $d = $today->copy()->subDays($ago)->locale('id');
        $dayTx = $weekTx->filter(fn ($t) => $t->created_at?->isSameDay($d));
        return [
            'label' => $d->translatedFormat('D'),
            'date'  => $d->translatedFormat('d M'),
            'in'    => round($dayTx->filter(fn ($t) => $classify($t) === 'in')->sum($qtyOf), 2),
            'out'   => round($dayTx->filter(fn ($t) => $classify($t) === 'out')->sum($qtyOf), 2),
        ];
    })->values()->all();

    $txQuery = $txModel::query()->latest();
    if (method_exists($txModel, 'ingredient')) {
        $txQuery->with('ingredient');
    }
    $recentTransactions = $txQuery->limit(8)->get();

    // Permintaan bahan dari dapur
    $reqTable = (new \App\Models\MaterialRequest)->getTable();
    $reqBase = fn () => \App\Models\MaterialRequest::query()->when(
        $hasCol($reqTable, 'status'),
        fn ($q) => $q->whereIn('status', ['pending', 'submitted', 'requested', 'menunggu', 'diajukan', 'waiting'])
    );
    $pendingRequestCount = $reqBase()->count();
    $pendingRequests = $reqBase()->latest()->limit(5)->get();

    // Purchase order yang belum selesai
    $poDone = collect(\App\Enums\PurchaseOrderStatus::cases())
        ->filter(fn ($c) => preg_match('/draft|cancel|complete|received|selesai|batal/i', ($c instanceof \BackedEnum ? $c->value : '') . $c->name))
        ->map(fn ($c) => $c instanceof \BackedEnum ? $c->value : $c->name)
        ->values()->all();
    $incomingOrders = \App\Models\PurchaseOrder::query()->whereNotIn('status', $poDone)->latest()->limit(5)->get();

    // Penerimaan hari ini
    $rcTable = (new \App\Models\MaterialReceipt)->getTable();
    $rcDateCol = $pickCol($rcTable, ['receipt_date', 'received_at', 'received_date', 'created_at']) ?? 'created_at';
    $todayReceiptCount = \App\Models\MaterialReceipt::whereDate($rcDateCol, $today->toDateString())->count();
    // ---------- Akhir data ----------

    $fmt = fn ($v) => rtrim(rtrim(number_format((float) $v, 2, ',', '.'), '0'), ',');

    $stockTotal = max(1, array_sum($stockSummary));
    $safeRate = round(($stockSummary['safe'] / $stockTotal) * 100, 1);

    $metrics = [
        ['label' => 'Jenis Bahan Baku',   'value' => number_format($totalIngredients, 0, ',', '.'),    'caption' => 'Terdaftar di gudang',               'href' => $r('ingredients.index'),       'icon' => 'clipboard', 'valueClass' => 'text-ink'],
        ['label' => 'Permintaan Menunggu', 'value' => number_format($pendingRequestCount, 0, ',', '.'), 'caption' => 'Perlu diverifikasi gudang',        'href' => $r('material-requests.index'), 'icon' => 'clock',     'valueClass' => $pendingRequestCount > 0 ? 'text-warning-text' : 'text-ink'],
        ['label' => 'Penerimaan Hari Ini', 'value' => number_format($todayReceiptCount, 0, ',', '.'),  'caption' => 'Bahan masuk dari supplier',        'href' => $r('material-receipts.index'), 'icon' => 'check',     'valueClass' => 'text-primary'],
        ['label' => 'Stok Perlu Perhatian', 'value' => number_format($attentionCount, 0, ',', '.'),    'caption' => 'Bahan menipis atau habis',          'href' => $r('stock-cards.index'),       'icon' => 'warning',   'valueClass' => $attentionCount > 0 ? 'text-danger-text' : 'text-ink'],
    ];

    $txTone = [
        'in'         => ['bg-success-bg text-success-text', 'Masuk'],
        'out'        => ['bg-info-bg text-info-text',       'Keluar'],
        'adjustment' => ['bg-warning-bg text-warning-text', 'Penyesuaian'],
    ];

    $secondaryBtn = 'inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-5 py-2.5 text-body font-medium text-primary transition hover:bg-primary/10';
    @endphp

    <x-page-header eyebrow="Dashboard Gudang"
        :title="'Halo, ' . Auth::user()->name"
        subtitle="Pantau stok bahan baku, permintaan dari dapur, dan penerimaan dari supplier.">
        <x-slot name="actions">
            <div class="hidden items-center gap-2.5 rounded-full border border-line bg-white px-5 py-2.5 sm:inline-flex">
                <x-icon name="calendar" class="h-4 w-4 text-primary" />
                <span class="text-label uppercase text-muted">Tanggal operasional</span>
                <span class="font-display text-body font-semibold text-ink">{{ $today->translatedFormat('d F Y') }}</span>
            </div>
            <a href="{{ $r('stock-cards.index') }}" class="{{ $secondaryBtn }}">Kartu Stok</a>
            <x-button :href="$r('material-receipts.create')">
                <x-icon name="plus" class="h-4 w-4" />
                Penerimaan Baru
            </x-button>
        </x-slot>
    </x-page-header>

    <div class="space-y-6">

        {{-- Kartu metrik --}}
        <section class="grid grid-cols-2 gap-4 xl:grid-cols-4" aria-label="Ringkasan gudang">
            @foreach ($metrics as $m)
            <a href="{{ $m['href'] }}"
                class="rounded-card border border-line bg-white p-5 shadow-card transition hover:border-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/30">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-label uppercase text-muted">{{ $m['label'] }}</div>
                        <div class="mt-2 font-display text-h1 {{ $m['valueClass'] }}">{{ $m['value'] }}</div>
                    </div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-input {{ $m['icon'] === 'warning' && $attentionCount > 0 ? 'bg-danger-bg text-danger-text' : 'bg-info-bg text-primary' }}">
                        @if ($m['icon'] === 'warning')
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.008v.008H12v-.008Z" /></svg>
                        @else
                        <x-icon :name="$m['icon']" />
                        @endif
                    </span>
                </div>
                <div class="mt-1 text-small text-muted">{{ $m['caption'] }}</div>
            </a>
            @endforeach
        </section>

        {{-- Grafik arus bahan + kondisi stok --}}
        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">

            <x-card class="xl:col-span-2">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="font-display text-h3 text-ink">Arus bahan masuk dan keluar</h3>
                        <p class="text-small text-muted">Total volume penerimaan dan pengeluaran selama 7 hari terakhir.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4 text-small text-muted">
                        <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-primary"></span>Masuk</span>
                        <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-accent"></span>Keluar</span>
                        <span class="inline-flex items-center gap-1.5"><span class="w-4 border-t-2 border-dashed border-muted"></span>Rata-rata keluar</span>
                    </div>
                </div>

                <div class="relative mt-5 h-72">
                    <canvas id="warehouseFlowChart" role="img" aria-label="Grafik bahan masuk dan keluar 7 hari terakhir"></canvas>
                </div>
            </x-card>

            <x-card>
                <h3 class="font-display text-h3 text-ink">Kondisi stok</h3>
                <p class="text-small text-muted">Perbandingan bahan aman, menipis, dan habis.</p>

                <div class="relative mx-auto my-6 h-48">
                    <canvas id="stockConditionChart" role="img" aria-label="Grafik donat kondisi stok bahan baku"></canvas>
                    <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                        <span class="font-display text-h2 text-primary">{{ number_format($safeRate, 1, ',', '.') }}%</span>
                        <span class="text-small text-muted">Stok aman</span>
                    </div>
                </div>

                <dl class="space-y-2">
                    <div class="flex items-center justify-between rounded-input bg-canvas px-4 py-3">
                        <dt class="flex items-center gap-2 text-small text-muted"><span class="h-2.5 w-2.5 rounded-full bg-primary"></span>Aman</dt>
                        <dd class="font-display text-body font-semibold text-ink">{{ $stockSummary['safe'] }} <span class="font-sans text-small font-normal text-muted">bahan</span></dd>
                    </div>
                    <div class="flex items-center justify-between rounded-input bg-canvas px-4 py-3">
                        <dt class="flex items-center gap-2 text-small text-muted"><span class="h-2.5 w-2.5 rounded-full bg-warning-text"></span>Menipis</dt>
                        <dd class="font-display text-body font-semibold text-warning-text">{{ $stockSummary['low'] }} <span class="font-sans text-small font-normal text-muted">bahan</span></dd>
                    </div>
                    <div class="flex items-center justify-between rounded-input bg-canvas px-4 py-3">
                        <dt class="flex items-center gap-2 text-small text-muted"><span class="h-2.5 w-2.5 rounded-full bg-danger-text"></span>Habis</dt>
                        <dd class="font-display text-body font-semibold text-danger-text">{{ $stockSummary['out'] }} <span class="font-sans text-small font-normal text-muted">bahan</span></dd>
                    </div>
                </dl>
            </x-card>

        </section>

        {{-- Antrean kerja gudang --}}
        <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">

            <x-card :padding="false" class="overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-line px-6 py-5">
                    <div>
                        <h3 class="font-display text-h3 text-ink">Permintaan bahan dari dapur</h3>
                        <p class="text-small text-muted">Menunggu verifikasi dan serah terima.</p>
                    </div>
                    <a href="{{ $r('material-requests.index') }}" class="shrink-0 text-small font-medium text-primary transition hover:text-primary-dark">Lihat semua</a>
                </div>

                @forelse ($pendingRequests as $req)
                <a href="{{ $r('material-requests.show', $req) }}"
                    class="flex items-center justify-between gap-4 border-b border-line px-6 py-4 transition last:border-b-0 hover:bg-primary/5">
                    <div class="min-w-0">
                        <div class="font-medium text-ink">{{ $req->request_number ?? 'Permintaan #' . $req->id }}</div>
                        <div class="truncate text-small text-muted">
                            Diajukan {{ $req->created_at?->translatedFormat('d M Y') }}
                            @if ($req->requester?->name) &middot; {{ $req->requester->name }} @endif
                        </div>
                    </div>
                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-warning-bg px-2.5 py-1 text-label uppercase text-warning-text">
                        <span class="h-1.5 w-1.5 rounded-full bg-warning-text"></span>Menunggu
                    </span>
                </a>
                @empty
                <div class="px-6 py-12 text-center">
                    <p class="font-medium text-ink">Tidak ada permintaan yang menunggu.</p>
                    <p class="mt-1 text-small text-muted">Permintaan baru dari dapur akan muncul di sini.</p>
                </div>
                @endforelse
            </x-card>

            <x-card :padding="false" class="overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-line px-6 py-5">
                    <div>
                        <h3 class="font-display text-h3 text-ink">Pesanan menunggu diterima</h3>
                        <p class="text-small text-muted">Purchase order yang belum selesai diterima.</p>
                    </div>
                    <a href="{{ $r('purchase-orders.index') }}" class="shrink-0 text-small font-medium text-primary transition hover:text-primary-dark">Lihat semua</a>
                </div>

                @forelse ($incomingOrders as $po)
                @php
                $poStatus = $po->status;
                $poLabel = $poStatus instanceof \BackedEnum
                    ? (method_exists($poStatus, 'label') ? $poStatus->label() : $poStatus->value)
                    : (string) $poStatus;
                @endphp
                <a href="{{ $r('purchase-orders.show', $po) }}"
                    class="flex items-center justify-between gap-4 border-b border-line px-6 py-4 transition last:border-b-0 hover:bg-primary/5">
                    <div class="min-w-0">
                        <div class="font-medium text-ink">{{ $po->po_number ?? 'PO #' . $po->id }}</div>
                        <div class="truncate text-small text-muted">
                            {{ $po->supplier?->name ?? 'Supplier tidak ditemukan' }} &middot; {{ $po->created_at?->translatedFormat('d M Y') }}
                        </div>
                    </div>
                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-info-bg px-2.5 py-1 text-label uppercase text-info-text">
                        <span class="h-1.5 w-1.5 rounded-full bg-info-text"></span>{{ $poLabel }}
                    </span>
                </a>
                @empty
                <div class="px-6 py-12 text-center">
                    <p class="font-medium text-ink">Tidak ada pesanan yang menunggu.</p>
                    <p class="mt-1 text-small text-muted">Pesanan ke supplier yang belum diterima akan muncul di sini.</p>
                </div>
                @endforelse
            </x-card>

        </section>

        {{-- Peringatan stok --}}
        <x-card :padding="false" class="overflow-hidden">
            <div class="flex flex-col gap-2 border-b border-line px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-display text-h3 text-ink">Peringatan stok</h3>
                    <p class="text-small text-muted">Bahan yang stoknya di bawah batas minimum.</p>
                </div>
                @if ($attentionCount > 0)
                <span class="inline-flex w-fit items-center rounded-full bg-danger-bg px-3 py-1 text-label uppercase text-danger-text">{{ $attentionCount }} bahan perlu tindakan</span>
                @endif
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
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @foreach ($lowStockItems as $item)
                        @php
                        $isOut = (float) $item->current_stock <= 0;
                        $pct = (float) $item->minimum_stock > 0
                            ? min(100, round(((float) $item->current_stock / (float) $item->minimum_stock) * 100))
                            : 0;
                        @endphp
                        <tr class="transition hover:bg-primary/5">
                            <td class="whitespace-nowrap px-6 py-4">
                                <a href="{{ $r('ingredients.show', $item) }}" class="font-medium text-primary transition hover:text-primary-dark">{{ $item->name }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="h-2 w-40 overflow-hidden rounded-full bg-canvas" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100" aria-label="Stok {{ $item->name }} terhadap batas minimum">
                                    <div class="h-full rounded-full {{ $isOut ? 'bg-danger-text' : 'bg-warning-text' }}" style="width: {{ $pct }}%"></div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right font-medium {{ $isOut ? 'text-danger-text' : 'text-warning-text' }}">
                                {{ $fmt($item->current_stock) }} <span class="text-small font-normal text-muted">{{ $item->unit }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-ink">
                                {{ $fmt($item->minimum_stock) }} <span class="text-small text-muted">{{ $item->unit }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-label uppercase {{ $isOut ? 'bg-danger-bg text-danger-text' : 'bg-warning-bg text-warning-text' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $isOut ? 'bg-danger-text' : 'bg-warning-text' }}"></span>{{ $isOut ? 'Stok habis' : 'Menipis' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-small font-medium">
                                <a href="{{ $r('purchase-orders.create') }}" class="text-primary transition hover:text-primary-dark">Buat PO</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </x-card>

        {{-- Aktivitas stok terbaru --}}
        <x-card :padding="false" class="overflow-hidden">
            <div class="flex flex-col gap-2 border-b border-line px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-display text-h3 text-ink">Aktivitas stok terbaru</h3>
                    <p class="text-small text-muted">Mutasi bahan masuk, keluar, dan penyesuaian.</p>
                </div>
                <a href="{{ $r('stock-cards.index') }}" class="text-small font-medium text-primary transition hover:text-primary-dark">Buka kartu stok</a>
            </div>

            @if ($recentTransactions->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="font-medium text-ink">Belum ada mutasi stok.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Waktu</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Bahan</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Tipe</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @foreach ($recentTransactions as $tx)
                        @php
                        $type = $classify($tx);
                        $tone = $txTone[$type] ?? ['bg-canvas text-muted', $type];
                        @endphp
                        <tr class="transition hover:bg-primary/5">
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-medium text-ink">{{ $tx->created_at?->format('H:i') }}</div>
                                <div class="text-small text-muted">{{ $tx->created_at?->translatedFormat('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-ink">{{ $tx->ingredient?->name ?? 'Bahan tidak ditemukan' }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-label uppercase {{ $tone[0] }}">{{ $tone[1] }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right font-medium text-ink">
                                {{ $fmt($qtyOf($tx)) }} <span class="text-small font-normal text-muted">{{ $tx->ingredient?->unit }}</span>
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
        document.addEventListener('DOMContentLoaded', function () {
            // Warna chart mengikuti token di tailwind.config.js (Chart.js tidak bisa membaca class Tailwind)
            const C = {
                primary: '#7C5CE0', // primary
                accent:     '#06B6D4', // accent (batang)
                accentDark: '#0891B2', // accent-dark (garis tren)
                warning: '#B45309', // warning-text
                danger:  '#B91C1C', // danger-text
                ink:     '#1E1B4B', // ink
                muted:   '#6B7280', // muted
                line:    '#ECE8F7', // line
            };

            Chart.defaults.font.family = 'Inter, ui-sans-serif, system-ui, sans-serif';
            Chart.defaults.color = C.muted;

            const flowData = @json($flowChart);
            const lastIndex = flowData.length - 1; // data terakhir = hari ini
            const avgOut = flowData.length
                ? flowData.reduce((sum, i) => sum + Number(i.out), 0) / flowData.length
                : 0;

            const barGradient = (hex) => (ctx) => {
                const area = ctx.chart.chartArea;
                if (!area) return hex;
                const g = ctx.chart.ctx.createLinearGradient(0, area.top, 0, area.bottom);
                g.addColorStop(0, hex);
                g.addColorStop(1, hex + '73');
                return g;
            };

            const formatK = (v) => v >= 1000
                ? (v / 1000).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + 'k'
                : v.toLocaleString('id-ID');

            const dayLabelsPlugin = {
                id: 'dayLabels',
                afterDraw(chart) {
                    const { ctx, chartArea, scales } = chart;
                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'alphabetic';
                    flowData.forEach((item, i) => {
                        const x = scales.x.getPixelForValue(i);
                        const isToday = i === lastIndex;
                        ctx.fillStyle = isToday ? C.primary : C.ink;
                        ctx.font = "700 13px 'Plus Jakarta Sans', Inter, sans-serif";
                        ctx.fillText(item.label, x, chartArea.bottom + 22);
                        ctx.fillStyle = isToday ? C.primary : C.muted;
                        ctx.font = (isToday ? '600 ' : '400 ') + "11px Inter, sans-serif";
                        ctx.fillText(isToday ? 'Hari Ini' : item.date, x, chartArea.bottom + 39);
                    });
                    ctx.restore();
                }
            };

            const flowCtx = document.getElementById('warehouseFlowChart');
            if (flowCtx) {
                new Chart(flowCtx, {
                    type: 'bar',
                    plugins: [dayLabelsPlugin],
                    data: {
                        labels: flowData.map(i => i.label),
                        datasets: [
                            {
                                type: 'line',
                                label: 'Tren Keluar',
                                data: flowData.map(i => i.out),
                                borderColor: C.accentDark,
                                borderWidth: 2.5,
                                cubicInterpolationMode: 'monotone',
                                pointRadius: 0,
                                pointHoverRadius: 0,
                                fill: true,
                                backgroundColor: C.primary + '14',
                                order: 0
                            },
                            {
                                type: 'line',
                                label: 'Rata-rata Keluar',
                                data: flowData.map(() => avgOut),
                                borderColor: C.muted,
                                borderWidth: 1,
                                borderDash: [6, 4],
                                pointRadius: 0,
                                pointHoverRadius: 0,
                                fill: false,
                                order: 0
                            },
                            {
                                label: 'Bahan Masuk',
                                data: flowData.map(i => i.in),
                                backgroundColor: barGradient(C.primary),
                                borderRadius: { topLeft: 6, topRight: 6 },
                                borderSkipped: false,
                                barPercentage: 0.9,
                                categoryPercentage: 0.6,
                                maxBarThickness: 16,
                                order: 1
                            },
                            {
                                label: 'Bahan Keluar',
                                data: flowData.map(i => i.out),
                                backgroundColor: barGradient(C.accent),
                                borderRadius: { topLeft: 6, topRight: 6 },
                                borderSkipped: false,
                                barPercentage: 0.9,
                                categoryPercentage: 0.6,
                                maxBarThickness: 16,
                                order: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: { padding: { bottom: 48 } },
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                filter: item => item.dataset.type !== 'line',
                                backgroundColor: C.ink,
                                padding: 10,
                                cornerRadius: 12,
                                callbacks: {
                                    label: ctx => ctx.dataset.label + ': ' + ctx.parsed.y.toLocaleString('id-ID')
                                }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, border: { display: false }, ticks: { display: false } },
                            y: {
                                beginAtZero: true,
                                border: { display: false },
                                grid: { color: C.line },
                                ticks: { font: { size: 11 }, precision: 0, callback: v => formatK(v) }
                            }
                        }
                    }
                });
            }

            const stockCtx = document.getElementById('stockConditionChart');
            if (stockCtx) {
                const s = @json($stockSummary);
                new Chart(stockCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Aman', 'Menipis', 'Habis'],
                        datasets: [{
                            data: [s.safe, s.low, s.out],
                            backgroundColor: [C.primary, C.warning, C.danger],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: C.ink,
                                cornerRadius: 12,
                                callbacks: { label: ctx => ctx.label + ': ' + ctx.parsed + ' bahan' }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>