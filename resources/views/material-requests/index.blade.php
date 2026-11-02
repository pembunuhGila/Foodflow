<x-app-layout>
    @php
        $canCreate = in_array(Auth::user()->role?->value, ['admin', 'produksi'], true);

        $tabLink = fn ($s) => route('material-requests.index', ['status' => $s, 'search' => $currentSearch]);
        $tabs = [
            ['label' => 'Menunggu Verifikasi', 'href' => $tabLink('pending'),  'active' => $currentStatus === 'pending'],
            ['label' => 'Sudah Diverifikasi',  'href' => $tabLink('approved'), 'active' => $currentStatus === 'approved'],
            ['label' => 'Semua Status',        'href' => $tabLink('all'),      'active' => $currentStatus === 'all'],
        ];
        if ($pendingCount > 0) {
            $tabs[0]['count'] = $pendingCount;
        }
    @endphp

    <x-page-header eyebrow="Persediaan & Bahan" :title="__('Permintaan Bahan Baku & Verifikasi Stok Keluar')"
                   subtitle="Verifikasi permintaan bahan baku dari dapur dan catat transaksi stok keluar (Stok OUT) ke kartu stok.">
        <x-slot name="actions">
            @if ($pendingCount > 0)
                <x-badge variant="warning">{{ $pendingCount }} Menunggu Verifikasi</x-badge>
            @endif

            @if ($canCreate)
                <x-button :href="route('material-requests.create')">
                    <x-icon name="plus" class="h-4 w-4" />
                    Ajukan Permintaan Bahan
                </x-button>
            @endif
        </x-slot>
    </x-page-header>

    <div class="space-y-6">

        {{-- Flash Alert --}}
        @if (session('success'))
            <div class="flex items-center gap-2 rounded-input border border-primary/20 bg-primary/10 p-4 text-body text-primary" role="status">
                <x-icon name="check" class="h-5 w-5 shrink-0" />
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error') || $errors->has('status') || $errors->has('general'))
            <div class="flex items-center gap-2 rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text" role="alert">
                <x-icon name="alert" class="h-5 w-5 shrink-0" />
                <span>{{ session('error') ?? $errors->first('status') ?? $errors->first('general') }}</span>
            </div>
        @endif

        {{-- Filter: pencarian + tab status --}}
        <x-filter-bar :action="route('material-requests.index')" :search="$currentSearch ?? ''"
                      placeholder="Cari ID, PLN-XXXX, atau pemohon..."
                      :tabs="$tabs" :hidden="['status' => $currentStatus]">
            @if ($currentSearch)
                <a href="{{ route('material-requests.index', ['status' => $currentStatus]) }}" class="text-small font-medium text-primary hover:text-primary-dark">Reset filter</a>
            @endif
        </x-filter-bar>

        {{-- Tabel --}}
        <x-card :padding="false" class="overflow-hidden">
            @if ($materialRequests->isEmpty())
                <div class="px-6 py-12 text-center text-muted">
                    <div class="flex flex-col items-center justify-center">
                        <x-icon name="clipboard" class="mb-3 h-10 w-10 text-muted/50" />
                        <p class="font-medium text-ink">Tidak ada permintaan bahan</p>
                        <p class="mt-1 text-small text-muted">
                            @if ($currentStatus === 'pending')
                                Saat ini tidak ada permintaan bahan dari dapur yang membutuhkan verifikasi stok keluar.
                            @else
                                Tidak ada data permintaan bahan yang sesuai dengan kriteria filter.
                            @endif
                        </p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-body">
                        <thead class="bg-primary/5">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-label uppercase text-muted">ID Permintaan</th>
                                <th class="px-6 py-3.5 text-left text-label uppercase text-muted">Tanggal</th>
                                <th class="px-6 py-3.5 text-left text-label uppercase text-muted">Rencana Produksi</th>
                                <th class="px-6 py-3.5 text-left text-label uppercase text-muted">Pemohon (Dapur)</th>
                                <th class="px-6 py-3.5 text-left text-label uppercase text-muted">Jumlah Item</th>
                                <th class="px-6 py-3.5 text-left text-label uppercase text-muted">Status</th>
                                <th class="px-6 py-3.5 text-right text-label uppercase text-muted">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line bg-white">
                            @foreach ($materialRequests as $req)
                                <tr class="transition hover:bg-primary/5">
                                    <td class="whitespace-nowrap px-6 py-4 font-medium text-ink">
                                        #MR-{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-small text-muted">
                                        {{ $req->request_date ? \Carbon\Carbon::parse($req->request_date)->locale('id')->translatedFormat('d M Y') : '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if ($req->productionPlan)
                                            <a href="{{ route('production-plans.show', $req->productionPlan) }}" class="text-small font-medium text-primary transition hover:text-primary-dark">{{ $req->productionPlan->plan_number }}</a>
                                        @else
                                            <span class="text-small italic text-muted">Tanpa Rencana</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="font-medium text-ink">{{ $req->requestedBy?->name ?? 'Dapur' }}</div>
                                        <div class="text-small text-muted">{{ $req->requestedBy?->role?->label() ?? 'Staff' }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-ink">
                                        {{ $req->details_count ?? $req->details->count() }} Bahan
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if ($req->isPending())
                                            <x-badge variant="warning">Menunggu Verifikasi Gudang</x-badge>
                                        @elseif ($req->isApproved())
                                            <x-badge variant="success">Selesai Diverifikasi</x-badge>
                                        @else
                                            <x-badge variant="neutral">{{ ucfirst($req->status) }}</x-badge>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-small font-medium">
                                        @if ($req->isPending())
                                            <a href="{{ route('material-requests.show', $req) }}" class="text-primary transition hover:text-primary-dark">Periksa & Verifikasi &rarr;</a>
                                        @else
                                            <a href="{{ route('material-requests.show', $req) }}" class="text-primary transition hover:text-primary-dark">Lihat Detail</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($materialRequests->hasPages())
                    <div class="border-t border-line bg-primary/5 px-6 py-4">
                        {{ $materialRequests->links() }}
                    </div>
                @endif
            @endif
        </x-card>

    </div>
</x-app-layout>