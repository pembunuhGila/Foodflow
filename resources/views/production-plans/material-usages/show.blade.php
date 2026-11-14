<x-app-layout>
    @php
    $canManage = in_array(Auth::user()->role?->value, ['admin', 'produksi'], true);
    $planCode = $plan->plan_number ?? $plan->plan_code;
    @endphp

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('production-plans.show', $plan) }}" class="text-muted transition hover:text-primary">Detail Rencana</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Pemakaian Bahan Riil</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">Evaluasi Penggunaan Bahan Baku Riil Dapur</h1>
            </div>
            <p class="mt-1 text-small text-muted">
                Rencana produksi {{ $planCode }} &middot; Tanggal masak {{ $plan->production_date?->format('d/m/Y') ?? $plan->plan_date?->format('d/m/Y') }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('production-plans.show', $plan) }}"
                class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Rencana
            </a>

            @if ($canManage)
            <a href="{{ route('production-plans.material-usages.create', $plan) }}"
                class="inline-flex items-center gap-2 rounded-input bg-primary px-4 py-2.5 text-label uppercase text-white transition hover:bg-primary-dark">
                Ubah / Input Pemakaian
            </a>
            @endif
        </div>
    </div>

    <div class="space-y-6">

        <x-flash-alert />

        {{-- Ringkasan Finansial & Varians Biaya Masak Dapur --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            {{-- Kartu 1: Estimasi Biaya Standar Resep --}}
            <div class="rounded-card border border-line bg-white p-5 shadow-card">
                <div class="flex items-center justify-between">
                    <span class="text-label uppercase text-muted">Estimasi Biaya Resep</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary">
                        <x-icon name="document-text" class="h-4 w-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold font-display text-ink">
                    Rp {{ number_format($totalPlannedCost ?? 0, 0, ',', '.') }}
                </div>
                <p class="mt-1 text-tiny text-muted">Berdasarkan formula standar resep</p>
            </div>

            {{-- Kartu 2: Realisasi Biaya Aktual Dapur --}}
            <div class="rounded-card border border-line bg-white p-5 shadow-card">
                <div class="flex items-center justify-between">
                    <span class="text-label uppercase text-muted">Realisasi Biaya Aktual</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/10 text-blue-600">
                        <x-icon name="cart" class="h-4 w-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold font-display text-ink">
                    Rp {{ number_format($totalActualCost ?? 0, 0, ',', '.') }}
                </div>
                <p class="mt-1 text-tiny text-muted">Total modal bahan terpakai di dapur</p>
            </div>

            {{-- Kartu 3: Varians & Efisiensi Biaya --}}
            @php
                $isSaving = ($costVariance ?? 0) <= 0;
            @endphp
            <div class="rounded-card border border-line bg-white p-5 shadow-card">
                <div class="flex items-center justify-between">
                    <span class="text-label uppercase text-muted">Varians Efisiensi Biaya</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg {{ $isSaving ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }}">
                        <x-icon name="chart-bar" class="h-4 w-4" />
                    </span>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-bold font-display {{ $isSaving ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ ($costVariance ?? 0) > 0 ? '+' : '' }}Rp {{ number_format($costVariance ?? 0, 0, ',', '.') }}
                    </span>
                    <span class="rounded-full px-2 py-0.5 text-tiny font-bold uppercase {{ $isSaving ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ $isSaving ? 'Hemat' : 'Boros' }}
                    </span>
                </div>
                <p class="mt-1 text-tiny text-muted">
                    {{ $isSaving ? 'Efisiensi laba katering optimal' : 'Terjadi pembengkakan pemakaian bahan baku' }}
                </p>
            </div>
        </div>

        <x-card :padding="false" class="overflow-hidden">
            <div class="border-b border-line px-6 py-5">
                <h2 class="text-lg font-semibold text-ink">Perbandingan Takaran Fisik &amp; Analisis Biaya Bahan Baku</h2>
                <p class="mt-0.5 text-small text-muted">Harga satuan bersumber dari harga beli Purchase Order (PO) terbaru atau standar estimasi pasar katering.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="w-10 px-4 py-3.5 text-center text-label uppercase text-muted">#</th>
                            <th scope="col" class="px-4 py-3.5 text-left text-label uppercase text-muted">Bahan Baku</th>
                            <th scope="col" class="px-4 py-3.5 text-right text-label uppercase text-muted">Takaran Resep</th>
                            <th scope="col" class="px-4 py-3.5 text-right text-label uppercase text-muted">Pemakaian Riil</th>
                            <th scope="col" class="px-4 py-3.5 text-left text-label uppercase text-muted">Selisih Fisik</th>
                            <th scope="col" class="px-4 py-3.5 text-right text-label uppercase text-muted">Harga Satuan</th>
                            <th scope="col" class="px-4 py-3.5 text-right text-label uppercase text-muted">Est. Biaya</th>
                            <th scope="col" class="px-4 py-3.5 text-right text-label uppercase text-muted">Real. Biaya</th>
                            <th scope="col" class="px-4 py-3.5 text-right text-label uppercase text-muted">Varians (Rp)</th>
                            <th scope="col" class="px-4 py-3.5 text-left text-label uppercase text-muted">Dicatat Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @forelse ($usages as $index => $usage)
                        @php 
                            $diff = (float) $usage->difference_quantity;
                            $costDiff = (float) ($usage->cost_difference ?? 0);
                        @endphp
                        <tr class="transition hover:bg-primary/5">
                            <td class="px-4 py-3.5 text-center text-small text-muted">{{ $index + 1 }}</td>
                            <td class="px-4 py-3.5 font-medium text-ink">
                                <div>{{ $usage->ingredient?->name ?? '-' }}</div>
                                @if($usage->notes)
                                    <div class="text-[11px] text-muted italic">{{ $usage->notes }}</div>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-right text-ink font-mono text-small">{{ number_format((float) $usage->planned_quantity, 2, ',', '.') }} {{ $usage->unit }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-right font-semibold text-ink font-mono text-small">{{ number_format((float) $usage->actual_quantity, 2, ',', '.') }} {{ $usage->unit }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5">
                                @if ($diff > 0)
                                <span class="inline-flex items-center gap-1 rounded-full bg-danger-bg px-2 py-0.5 text-tiny font-medium text-danger-text">
                                    +{{ number_format($diff, 2, ',', '.') }} (boros)
                                </span>
                                @elseif ($diff < 0)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-tiny font-medium text-emerald-700">
                                    {{ number_format($diff, 2, ',', '.') }} (hemat)
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-0.5 text-tiny font-medium text-gray-600">
                                    Sesuai
                                </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-right text-muted font-mono text-small">
                                Rp {{ number_format($usage->unit_cost ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-right text-ink font-mono text-small">
                                Rp {{ number_format($usage->planned_cost ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-right font-semibold text-ink font-mono text-small">
                                Rp {{ number_format($usage->actual_cost ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-right font-mono text-small font-semibold {{ $costDiff > 0 ? 'text-rose-600' : ($costDiff < 0 ? 'text-emerald-600' : 'text-muted') }}">
                                {{ $costDiff > 0 ? '+' : '' }}Rp {{ number_format($costDiff, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-small text-muted">{{ $usage->recorder?->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-muted">
                                <p class="font-medium text-ink">Belum ada pencatatan pemakaian bahan riil untuk jadwal masak ini.</p>
                                @if ($canManage)
                                <a href="{{ route('production-plans.material-usages.create', $plan) }}" class="mt-1 inline-block text-small font-medium text-primary hover:text-primary-dark">Input pemakaian bahan riil sekarang</a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($usages) > 0)
                    <tfoot class="bg-slate-50 border-t-2 border-line font-semibold text-ink">
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-right uppercase text-xs tracking-wider text-muted">Total Biaya Bahan:</td>
                            <td class="px-4 py-3 text-right font-mono text-small text-ink">Rp {{ number_format($totalPlannedCost ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-small text-ink">Rp {{ number_format($totalActualCost ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-small {{ ($costVariance ?? 0) > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ ($costVariance ?? 0) > 0 ? '+' : '' }}Rp {{ number_format($costVariance ?? 0, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </x-card>

    </div>
</x-app-layout>