<x-app-layout>
    @php
    // ---------- Helper ----------
    $today = $today ?? now();
    $r = fn (string $name, $params = []) => \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : '#';
    $firstRoute = fn (array $names, $params = []) => collect($names)->first(fn ($n) => \Illuminate\Support\Facades\Route::has($n)) ? route(collect($names)->first(fn ($n) => \Illuminate\Support\Facades\Route::has($n)), $params) : '#';
    $hasCol = fn (string $table, string $col) => \Illuminate\Support\Facades\Schema::hasColumn($table, $col);
    $pickCol = fn (string $table, array $options) => collect($options)->first(fn ($c) => \Illuminate\Support\Facades\Schema::hasColumn($table, $c));
    $val = fn ($s) => $s instanceof \BackedEnum ? $s->value : (string) $s;
    $pct = fn ($a, $b) => $b > 0 ? (int) min(100, round(($a / $b) * 100)) : 0;
    $enumMatch = fn (string $enum, string $regex) => collect($enum::cases())
        ->filter(fn ($c) => preg_match($regex, ($c instanceof \BackedEnum ? $c->value : '') . $c->name))
        ->map(fn ($c) => $c instanceof \BackedEnum ? $c->value : $c->name)
        ->values()->all();
    $number = fn ($m, array $fields, string $prefix) => collect($fields)->map(fn ($f) => $m->{$f} ?? null)->first(fn ($v) => filled($v)) ?? ($prefix . $m->id);
    $fmt = fn ($v) => number_format((float) $v, 0, ',', '.');
    $todayStr = $today->toDateString();

    // ---------- Pesanan katering ----------
    $orderTable = (new \App\Models\CateringOrder)->getTable();
    $orderDateCol = $pickCol($orderTable, ['delivery_date', 'event_date', 'order_date', 'created_at']) ?? 'created_at';
    $ordersToday = \App\Models\CateringOrder::whereDate($orderDateCol, $todayStr)->count();
    $customerCount = \App\Models\Customer::count();

    // ---------- Produksi ----------
    $todayPlans = \App\Models\ProductionPlan::with('details')
        ->whereDate('production_date', $todayStr)
        ->get()
        ->filter(fn ($p) => in_array($val($p->status), ['scheduled', 'in_progress', 'completed'], true));
    $todayTarget = (int) $todayPlans->sum(fn ($p) => $p->totalPlannedQuantity());
    $todayActual = (int) $todayPlans->sum(fn ($p) => $p->totalActualQuantity());
    $plansToday = $todayPlans->count();
    $plansDoneToday = $todayPlans->filter(fn ($p) => $val($p->status) === 'completed')->count();
    $activePlanCount = \App\Models\ProductionPlan::whereIn('status', ['scheduled', 'in_progress'])->count();
    $draftPlanCount = \App\Models\ProductionPlan::where('status', 'draft')->count();

    // ---------- Gudang ----------
    $ingredients = fn () => \App\Models\Ingredient::query()->where('is_active', true);
    $ingredientTotal = $ingredients()->count();
    $ingredientSafe = $ingredients()->whereColumn('current_stock', '>', 'minimum_stock')->count();
    $attentionCount = $ingredients()->whereColumn('current_stock', '<=', 'minimum_stock')->count();

    $reqTable = (new \App\Models\MaterialRequest)->getTable();
    $pendingReqStatuses = ['pending', 'submitted', 'requested', 'menunggu', 'diajukan', 'waiting'];
    $pendingReq = fn () => \App\Models\MaterialRequest::query()->when(
        $hasCol($reqTable, 'status'),
        fn ($q) => $q->whereIn('status', $pendingReqStatuses)
    );
    $pendingRequestCount = $pendingReq()->count();
    $requestsToday = \App\Models\MaterialRequest::whereDate('created_at', $todayStr)->count();
    $requestsTodayPending = $pendingReq()->whereDate('created_at', $todayStr)->count();

    $rcTable = (new \App\Models\MaterialReceipt)->getTable();
    $rcDateCol = $pickCol($rcTable, ['receipt_date', 'received_at', 'received_date', 'created_at']) ?? 'created_at';
    $receiptsToday = \App\Models\MaterialReceipt::whereDate($rcDateCol, $todayStr)->count();

    // ---------- Pengguna ----------
    $userTotal = \App\Models\User::count();
    $activeStatuses = $enumMatch(\App\Enums\UserStatus::class, '/active|aktif/i');
    $userActive = $activeStatuses ? \App\Models\User::whereIn('status', $activeStatuses)->count() : $userTotal;

    $resetTable = (new \App\Models\PasswordResetRequest)->getTable();
    $pendingResetStatuses = $enumMatch(\App\Enums\PasswordResetStatus::class, '/pending|menunggu|waiting/i');
    $pendingResetCount = $hasCol($resetTable, 'status') && $pendingResetStatuses
        ? \App\Models\PasswordResetRequest::whereIn('status', $pendingResetStatuses)->count()
        : 0;

    $actionTotal = $pendingRequestCount + $pendingResetCount + $attentionCount + $draftPlanCount;

    // ---------- Alur kerja hari ini ----------
    $flow = [
        [
            'role' => 'Admin', 'title' => 'Pesanan masuk',
            'value' => $fmt($ordersToday), 'unit' => 'pesanan',
            'caption' => 'Pesanan katering hari ini',
            'progress' => null, 'has' => $ordersToday > 0,
            'href' => $r('catering-orders.index'),
        ],
        [
            'role' => 'Produksi', 'title' => 'Rencana produksi',
            'value' => $fmt($plansToday), 'unit' => 'rencana',
            'caption' => $plansDoneToday . ' dari ' . $plansToday . ' rencana selesai',
            'progress' => $pct($plansDoneToday, $plansToday), 'has' => $plansToday > 0,
            'href' => $r('production-plans.index'),
        ],
        [
            'role' => 'Produksi → Gudang', 'title' => 'Permintaan bahan',
            'value' => $fmt($requestsToday), 'unit' => 'permintaan',
            'caption' => ($requestsToday - $requestsTodayPending) . ' dari ' . $requestsToday . ' sudah diverifikasi',
            'progress' => $pct($requestsToday - $requestsTodayPending, $requestsToday), 'has' => $requestsToday > 0,
            'href' => $r('material-requests.index'),
        ],
        [
            'role' => 'Gudang', 'title' => 'Penerimaan bahan',
            'value' => $fmt($receiptsToday), 'unit' => 'penerimaan',
            'caption' => 'Bahan masuk dari supplier',
            'progress' => null, 'has' => $receiptsToday > 0,
            'href' => $r('material-receipts.index'),
        ],
        [
            'role' => 'Dapur', 'title' => 'Realisasi masak',
            'value' => $fmt($todayActual), 'unit' => 'porsi',
            'caption' => 'Target ' . $fmt($todayTarget) . ' porsi',
            'progress' => $pct($todayActual, $todayTarget), 'has' => $todayTarget > 0,
            'href' => $r('productions.index'),
        ],
    ];

    // ---------- Perlu tindakan ----------
    $actions = [
        ['label' => 'Permintaan bahan menunggu',   'caption' => 'Perlu diverifikasi gudang',         'count' => $pendingRequestCount, 'tone' => 'warning', 'href' => $r('material-requests.index')],
        ['label' => 'Permintaan reset password',   'caption' => 'Menunggu diproses admin',            'count' => $pendingResetCount,   'tone' => 'warning', 'href' => $firstRoute(['admin.password-resets.index', 'admin.password-reset-requests.index'])],
        ['label' => 'Stok menipis atau habis',     'caption' => 'Di bawah batas minimum',             'count' => $attentionCount,      'tone' => 'danger',  'href' => $r('stock-cards.index')],
        ['label' => 'Rencana produksi draft',      'caption' => 'Belum dijadwalkan',                  'count' => $draftPlanCount,      'tone' => 'warning', 'href' => $r('production-plans.index', ['status' => 'draft'])],
    ];

    // ---------- Ringkasan divisi ----------
    $divisions = [
        [
            'title' => 'Produksi', 'href' => $r('production-plans.index'),
            'bar' => ['label' => 'Realisasi porsi hari ini', 'text' => $fmt($todayActual) . ' / ' . $fmt($todayTarget), 'pct' => $pct($todayActual, $todayTarget)],
            'rows' => [
                ['Rencana aktif', $fmt($activePlanCount)],
                ['Rencana draft', $fmt($draftPlanCount)],
                ['Rencana hari ini', $fmt($plansToday)],
            ],
        ],
        [
            'title' => 'Gudang', 'href' => $r('stock-cards.index'),
            'bar' => ['label' => 'Bahan dengan stok aman', 'text' => $fmt($ingredientSafe) . ' / ' . $fmt($ingredientTotal), 'pct' => $pct($ingredientSafe, $ingredientTotal)],
            'rows' => [
                ['Stok menipis atau habis', $fmt($attentionCount)],
                ['Permintaan menunggu', $fmt($pendingRequestCount)],
                ['Penerimaan hari ini', $fmt($receiptsToday)],
            ],
        ],
        [
            'title' => 'Admin', 'href' => $firstRoute(['admin.users.index']),
            'bar' => ['label' => 'Pengguna aktif', 'text' => $fmt($userActive) . ' / ' . $fmt($userTotal), 'pct' => $pct($userActive, $userTotal)],
            'rows' => [
                ['Pesanan hari ini', $fmt($ordersToday)],
                ['Pelanggan terdaftar', $fmt($customerCount)],
                ['Reset password menunggu', $fmt($pendingResetCount)],
            ],
        ],
    ];

    // ---------- Log aktivitas lintas divisi ----------
    $feed = collect();
    \App\Models\CateringOrder::latest()->limit(5)->get()->each(fn ($m) => $feed->push([
        'at' => $m->created_at, 'divisi' => 'Admin',
        'text' => 'Pesanan katering ' . $number($m, ['order_number', 'number'], '#'),
        'href' => $r('catering-orders.show', $m),
    ]));
    \App\Models\PurchaseOrder::latest()->limit(5)->get()->each(fn ($m) => $feed->push([
        'at' => $m->created_at, 'divisi' => 'Admin',
        'text' => 'Purchase order ' . $number($m, ['po_number', 'number'], '#'),
        'href' => $r('purchase-orders.show', $m),
    ]));
    \App\Models\ProductionPlan::latest()->limit(5)->get()->each(fn ($m) => $feed->push([
        'at' => $m->created_at, 'divisi' => 'Produksi',
        'text' => 'Rencana produksi ' . $number($m, ['plan_number'], '#'),
        'href' => $r('production-plans.show', $m),
    ]));
    \App\Models\MaterialRequest::latest()->limit(5)->get()->each(fn ($m) => $feed->push([
        'at' => $m->created_at, 'divisi' => 'Produksi',
        'text' => 'Permintaan bahan ' . $number($m, ['request_number', 'number'], '#'),
        'href' => $r('material-requests.show', $m),
    ]));
    \App\Models\MaterialReceipt::latest()->limit(5)->get()->each(fn ($m) => $feed->push([
        'at' => $m->created_at, 'divisi' => 'Gudang',
        'text' => 'Penerimaan bahan ' . $number($m, ['receipt_number', 'number'], '#'),
        'href' => $r('material-receipts.show', $m),
    ]));
    $feed = $feed->filter(fn ($i) => $i['at'])->sortByDesc('at')->take(8)->values();

    $divisiTone = [
        'Admin'    => 'bg-info-bg text-info-text',
        'Produksi' => 'bg-warning-bg text-warning-text',
        'Gudang'   => 'bg-success-bg text-success-text',
    ];

    $metrics = [
        ['label' => 'Pesanan Hari Ini',  'value' => $fmt($ordersToday),   'unit' => 'pesanan', 'caption' => 'Pesanan katering terjadwal hari ini', 'href' => $r('catering-orders.index'),  'icon' => 'clipboard', 'valueClass' => 'text-ink'],
        ['label' => 'Target Porsi',      'value' => $fmt($todayTarget),   'unit' => 'porsi',   'caption' => 'Terealisasi ' . $fmt($todayActual) . ' porsi (' . $pct($todayActual, $todayTarget) . '%)', 'href' => $r('production-plans.index'), 'icon' => 'calendar', 'valueClass' => 'text-ink'],
        ['label' => 'Penerimaan Bahan',  'value' => $fmt($receiptsToday), 'unit' => 'hari ini', 'caption' => 'Bahan masuk dari supplier',            'href' => $r('material-receipts.index'), 'icon' => 'check',     'valueClass' => 'text-primary'],
        ['label' => 'Perlu Tindakan',    'value' => $fmt($actionTotal),   'unit' => 'item',    'caption' => 'Permintaan, reset password, stok, draft', 'href' => '#perlu-tindakan',                'icon' => 'warning',   'valueClass' => $actionTotal > 0 ? 'text-danger-text' : 'text-ink'],
    ];

    $bar = fn (int $p) => new \Illuminate\Support\HtmlString(
        '<div class="h-2 overflow-hidden rounded-full bg-canvas" role="progressbar" aria-valuenow="' . $p . '" aria-valuemin="0" aria-valuemax="100">'
        . '<div class="h-full rounded-full bg-primary" style="width: ' . $p . '%"></div></div>'
    );

    $secondaryBtn = 'inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-5 py-2.5 text-body font-medium text-primary transition hover:bg-primary/10';
    @endphp

    <x-page-header eyebrow="Dashboard Admin"
        :title="'Halo, ' . Auth::user()->name"
        subtitle="Pantau alur kerja dari pesanan, produksi, sampai gudang dalam satu tampilan.">
        <x-slot name="actions">
            <a href="{{ $r('purchase-orders.create') }}" class="{{ $secondaryBtn }}">Buat PO</a>
            <x-button :href="$r('catering-orders.create')">
                <x-icon name="plus" class="h-4 w-4" />
                Buat Pesanan Baru
            </x-button>
        </x-slot>
    </x-page-header>

    <div class="space-y-6">

        {{-- Kartu metrik --}}
        <section class="grid grid-cols-2 gap-4 xl:grid-cols-4" aria-label="Ringkasan operasional">
            @foreach ($metrics as $m)
            <a href="{{ $m['href'] }}"
                class="rounded-card border border-line bg-white p-5 shadow-card transition hover:border-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/30">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-label uppercase text-muted">{{ $m['label'] }}</div>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="font-display text-h1 {{ $m['valueClass'] }}">{{ $m['value'] }}</span>
                            <span class="text-small text-muted">{{ $m['unit'] }}</span>
                        </div>
                    </div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-input {{ $m['icon'] === 'warning' && $actionTotal > 0 ? 'bg-danger-bg text-danger-text' : 'bg-info-bg text-primary' }}">
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

        {{-- Alur kerja antar divisi --}}
        <x-card>
            <h3 class="font-display text-h3 text-ink">Alur kerja hari ini</h3>
            <p class="text-small text-muted">Urutan kerja dari pesanan masuk sampai porsi matang.</p>

            <ol class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
                @foreach ($flow as $i => $step)
                @php
                $state = ! $step['has'] ? 'idle' : (($step['progress'] ?? null) === 100 ? 'done' : 'running');
                $stateTone = [
                    'idle'    => ['bg-canvas text-muted',              'Belum ada'],
                    'running' => ['bg-warning-bg text-warning-text',   'Berjalan'],
                    'done'    => ['bg-success-bg text-success-text',   'Selesai'],
                ][$state];
                @endphp
                <li>
                    <a href="{{ $step['href'] }}"
                        class="flex h-full flex-col rounded-input border border-line bg-canvas p-4 transition hover:border-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/30">
                        <div class="flex items-center justify-between gap-2">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-primary font-display text-small font-semibold text-white">{{ $i + 1 }}</span>
                            <span class="rounded-full px-2.5 py-1 text-label uppercase {{ $stateTone[0] }}">{{ $stateTone[1] }}</span>
                        </div>
                        <div class="mt-3 text-label uppercase text-muted">{{ $step['role'] }}</div>
                        <div class="font-display text-body font-semibold text-ink">{{ $step['title'] }}</div>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="font-display text-h2 text-ink">{{ $step['value'] }}</span>
                            <span class="text-small text-muted">{{ $step['unit'] }}</span>
                        </div>
                        <div class="mt-1 text-small text-muted">{{ $step['caption'] }}</div>
                        @if ($step['progress'] !== null && $step['has'])
                        <div class="mt-3">{{ $bar($step['progress']) }}</div>
                        @endif
                    </a>
                </li>
                @endforeach
            </ol>
        </x-card>

        {{-- Ringkasan per divisi --}}
        <section class="grid grid-cols-1 gap-6 lg:grid-cols-3" aria-label="Ringkasan per divisi">
            @foreach ($divisions as $d)
            <x-card>
                <div class="flex items-center justify-between gap-3">
                    <h3 class="font-display text-h3 text-ink">{{ $d['title'] }}</h3>
                    <a href="{{ $d['href'] }}" class="text-small font-medium text-primary transition hover:text-primary-dark">Buka</a>
                </div>

                <div class="mt-4">
                    <div class="flex items-baseline justify-between gap-3">
                        <span class="text-small text-muted">{{ $d['bar']['label'] }}</span>
                        <span class="font-display text-body font-semibold text-ink">{{ $d['bar']['text'] }}</span>
                    </div>
                    <div class="mt-2">{{ $bar($d['bar']['pct']) }}</div>
                </div>

                <dl class="mt-4 divide-y divide-line">
                    @foreach ($d['rows'] as [$label, $value])
                    <div class="flex items-center justify-between py-2.5">
                        <dt class="text-small text-muted">{{ $label }}</dt>
                        <dd class="font-display text-body font-semibold text-ink">{{ $value }}</dd>
                    </div>
                    @endforeach
                </dl>
            </x-card>
            @endforeach
        </section>

        {{-- Perlu tindakan + log aktivitas --}}
        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">

            <x-card :padding="false" class="overflow-hidden" id="perlu-tindakan">
                <div class="border-b border-line px-6 py-5">
                    <h3 class="font-display text-h3 text-ink">Perlu tindakan</h3>
                    <p class="text-small text-muted">Hal yang menunggu keputusan atau pengecekan.</p>
                </div>

                @foreach ($actions as $a)
                <a href="{{ $a['href'] }}"
                    class="flex items-center justify-between gap-4 border-b border-line px-6 py-4 transition last:border-b-0 hover:bg-primary/5">
                    <div class="min-w-0">
                        <div class="font-medium text-ink">{{ $a['label'] }}</div>
                        <div class="truncate text-small text-muted">{{ $a['caption'] }}</div>
                    </div>
                    <span class="inline-flex min-w-8 shrink-0 items-center justify-center rounded-full px-2.5 py-1 font-display text-small font-semibold
                        {{ $a['count'] === 0 ? 'bg-canvas text-muted' : ($a['tone'] === 'danger' ? 'bg-danger-bg text-danger-text' : 'bg-warning-bg text-warning-text') }}">
                        {{ $a['count'] }}
                    </span>
                </a>
                @endforeach
            </x-card>

            <x-card :padding="false" class="overflow-hidden xl:col-span-2">
                <div class="border-b border-line px-6 py-5">
                    <h3 class="font-display text-h3 text-ink">Aktivitas terbaru</h3>
                    <p class="text-small text-muted">Dokumen yang baru dibuat oleh semua divisi.</p>
                </div>

                @if ($feed->isEmpty())
                <div class="px-6 py-12 text-center">
                    <p class="font-medium text-ink">Belum ada aktivitas.</p>
                    <p class="mt-1 text-small text-muted">Pesanan, rencana, dan penerimaan baru akan muncul di sini.</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-body">
                        <thead class="bg-primary/5">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Waktu</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Divisi</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Aktivitas</th>
                                <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line bg-white">
                            @foreach ($feed as $item)
                            <tr class="transition hover:bg-primary/5">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="font-medium text-ink">{{ $item['at']->format('H:i') }}</div>
                                    <div class="text-small text-muted">{{ $item['at']->isSameDay($today) ? 'Hari ini' : $item['at']->locale('id')->translatedFormat('d M Y') }}</div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-label uppercase {{ $divisiTone[$item['divisi']] }}">{{ $item['divisi'] }}</span>
                                </td>
                                <td class="px-6 py-4 font-medium text-ink">{{ $item['text'] }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-small font-medium">
                                    <a href="{{ $item['href'] }}" class="text-primary transition hover:text-primary-dark">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </x-card>

        </section>

    </div>
</x-app-layout>