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

        <x-card :padding="false" class="overflow-hidden">
            <div class="border-b border-line px-6 py-5">
                <h2 class="text-lg font-semibold text-ink">Perbandingan Kebutuhan Resep vs Pemakaian Riil</h2>
                <p class="mt-0.5 text-small text-muted">Selisih positif (+) berarti pemakaian lebih boros dari resep, selisih negatif (-) berarti hemat atau sisa.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="w-12 px-6 py-3.5 text-center text-label uppercase text-muted">#</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Bahan Baku</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Takaran Standar Resep</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Pemakaian Riil</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Selisih</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Catatan Dapur</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Dicatat Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @forelse ($usages as $index => $usage)
                        @php $diff = (float) $usage->difference_quantity; @endphp
                        <tr class="transition hover:bg-primary/5">
                            <td class="px-6 py-4 text-center text-small text-muted">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-ink">{{ $usage->ingredient?->name ?? '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-ink">{{ number_format((float) $usage->planned_quantity, 3, ',', '.') }} {{ $usage->unit }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right font-medium text-ink">{{ number_format((float) $usage->actual_quantity, 3, ',', '.') }} {{ $usage->unit }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($diff > 0)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-danger-bg px-2.5 py-1 text-small font-medium text-danger-text">
                                    <span class="h-1.5 w-1.5 rounded-full bg-danger-text"></span>
                                    +{{ number_format($diff, 3, ',', '.') }} {{ $usage->unit }} (boros/susut)
                                </span>
                                @elseif ($diff < 0)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2.5 py-1 text-small font-medium text-primary">
                                    <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
                                    {{ number_format($diff, 3, ',', '.') }} {{ $usage->unit }} (sisa/hemat)
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-small font-medium text-gray-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                        Sesuai resep
                                    </span>
                                    @endif
                            </td>
                            <td class="px-6 py-4 text-small text-muted">{{ $usage->notes ?: '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-small text-muted">{{ $usage->recorder?->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-muted">
                                <p class="font-medium text-ink">Belum ada pencatatan pemakaian bahan riil untuk jadwal masak ini.</p>
                                @if ($canManage)
                                <a href="{{ route('production-plans.material-usages.create', $plan) }}" class="mt-1 inline-block text-small font-medium text-primary hover:text-primary-dark">Input pemakaian bahan riil sekarang</a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

    </div>
</x-app-layout>