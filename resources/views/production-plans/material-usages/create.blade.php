<x-app-layout>
    @php
    $inputClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';
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
                <span class="text-primary">Catat Pemakaian Bahan</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">Catat Pemakaian Bahan Riil Dapur</h1>
            </div>
            <p class="mt-1 text-small text-muted">
                Rencana produksi {{ $planCode }} &middot; Tanggal masak {{ $plan->production_date?->format('d/m/Y') ?? $plan->plan_date?->format('d/m/Y') }}
            </p>
        </div>

        <a href="{{ route('production-plans.show', $plan) }}"
            class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Rencana
        </a>
    </div>

    <div class="space-y-6">

        @if ($errors->any())
        <div class="rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text" role="alert">
            <p class="mb-2 font-semibold">Terdapat kesalahan pada input:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <x-card :padding="false" class="overflow-hidden">
            <div class="border-b border-line px-6 py-5">
                <h2 class="text-lg font-semibold text-ink">Pemakaian Bahan Baku Riil</h2>
                <p class="mt-0.5 text-small text-muted">
                    Bandingkan takaran standar resep dengan jumlah bahan yang benar-benar habis dipakai saat memasak. Isi catatan jika ada bahan tumpah, susut, atau sisa layak simpan.
                </p>
            </div>

            <form method="POST" action="{{ route('production-plans.material-usages.store', $plan) }}">
                @csrf

                <div class="overflow-x-auto">
                    <table class="min-w-full text-body">
                        <thead class="bg-primary/5">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Bahan Baku</th>
                                <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Kebutuhan Rencana</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Pemakaian Riil <span class="text-danger-text">*</span></th>
                                <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Satuan</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Catatan Dapur</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line bg-white">
                            @forelse ($items as $index => $item)
                            <tr class="transition hover:bg-primary/5">
                                <td class="px-6 py-4 font-medium text-ink">
                                    {{ $item['ingredient_name'] }}
                                    <input type="hidden" name="items[{{ $index }}][ingredient_id]" value="{{ $item['ingredient_id'] }}">
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-ink">
                                    {{ number_format((float) $item['planned_quantity'], 3, ',', '.') }}
                                    <input type="hidden" name="items[{{ $index }}][planned_quantity]" value="{{ $item['planned_quantity'] }}">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="number" step="0.001" min="0" required
                                        name="items[{{ $index }}][actual_quantity]"
                                        value="{{ old("items.{$index}.actual_quantity", $item['actual_quantity']) }}"
                                        class="{{ $inputClass }} w-36 text-right">
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-muted">
                                    {{ $item['unit'] }}
                                    <input type="hidden" name="items[{{ $index }}][unit]" value="{{ $item['unit'] }}">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="text" name="items[{{ $index }}][notes]"
                                        value="{{ old("items.{$index}.notes", $item['notes']) }}"
                                        placeholder="Misal: tumpah 100 gr saat menimbang..."
                                        class="{{ $inputClass }}">
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-muted">
                                    Tidak ada komposisi resep bahan baku yang terdaftar pada menu rencana produksi ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (!empty($items))
                <div class="flex items-center justify-end gap-3 border-t border-line px-6 py-4">
                    <x-button variant="outline" :href="route('production-plans.show', $plan)">Batal</x-button>
                    <x-button type="submit">Simpan Realisasi Pemakaian</x-button>
                </div>
                @endif
            </form>
        </x-card>

    </div>
</x-app-layout>