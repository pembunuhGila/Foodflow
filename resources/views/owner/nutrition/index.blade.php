<x-app-layout>
    @php
    $stats = [
    ['Rata-rata Energi', $avgCalories, 'kkal', 'Target MBG: 500-750 kkal'],
    ['Rata-rata Protein', $avgProtein, 'g', 'Target: 20-30 gram'],
    ['Rata-rata Lemak', $avgFat, 'g', 'Target: 15-25 gram'],
    ['Rata-rata Karbohidrat', $avgCarbs, 'g', 'Target: 60-90 gram'],
    ['Rata-rata Serat', $avgFiber, 'g', 'Target: min 5 gram'],
    ];
    @endphp

    <x-page-header eyebrow="PEMANTAUAN GIZI" :title="__('Monitoring & Evaluasi Kepatuhan Gizi Menu')"
        subtitle="Pemantauan kandungan nilai nutrisi hidangan katering MBG (Kalori, Makronutrisi & Serat Pangan).">
        <x-slot name="actions">
            <x-button :href="route('owner.reports.index')">
                <x-icon name="printer" class="h-4 w-4" />
                Pusat Cetak Laporan Eksekutif &rarr;
            </x-button>
        </x-slot>
    </x-page-header>

    <div class="space-y-6">

        <!-- Rata-rata nutrisi menu -->
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
            @foreach ($stats as [$label, $value, $unit, $target])
            <x-card class="!p-4">
                <div class="text-label uppercase text-muted">{{ $label }}</div>
                <p class="mt-1.5 font-display text-h2 text-ink">{{ $value }} <span class="text-small font-normal text-muted">{{ $unit }}</span></p>
                <span class="text-small text-muted">{{ $target }}</span>
            </x-card>
            @endforeach
        </div>

        <!-- Tabel menu & profil gizi -->
        <x-card :padding="false" class="overflow-hidden">
            <div class="border-b border-line px-6 py-4">
                <h3 class="font-display text-h3 text-ink">Daftar Menu Hidangan Aktif & Evaluasi Keseimbangan Gizi</h3>
                <p class="mt-0.5 text-small text-muted">Memastikan hidangan katering sehat memenuhi proporsi gizi seimbang harian.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th class="px-4 py-3.5 text-left text-label uppercase text-muted">#</th>
                            <th class="px-4 py-3.5 text-left text-label uppercase text-muted">Nama Menu Hidangan</th>
                            <th class="px-4 py-3.5 text-right text-label uppercase text-muted">Energi (kkal)</th>
                            <th class="px-4 py-3.5 text-right text-label uppercase text-muted">Protein (g)</th>
                            <th class="px-4 py-3.5 text-right text-label uppercase text-muted">Lemak (g)</th>
                            <th class="px-4 py-3.5 text-right text-label uppercase text-muted">Karbo (g)</th>
                            <th class="px-4 py-3.5 text-right text-label uppercase text-muted">Serat (g)</th>
                            <th class="px-4 py-3.5 text-left text-label uppercase text-muted">Kelengkapan Resep</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @forelse ($products as $index => $product)
                        <tr class="transition hover:bg-primary/5">
                            <td class="px-4 py-3 text-muted">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-ink">{{ $product->name }}</div>
                                <div class="text-small text-muted">{{ $product->description ?: 'Menu hidangan reguler' }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-medium tabular-nums text-ink">{{ number_format((float) ($product->calories ?? 0), 1, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-ink">{{ number_format((float) ($product->protein ?? 0), 1, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-ink">{{ number_format((float) ($product->fat ?? 0), 1, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-ink">{{ number_format((float) ($product->carbohydrates ?? 0), 1, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-ink">{{ number_format((float) ($product->dietary_fiber ?? 0), 1, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-4 py-3">
                                @if ($product->recipe_items_count > 0)
                                <x-badge variant="success">Lengkap ({{ $product->recipe_items_count }} Bahan)</x-badge>
                                @else
                                <x-badge variant="neutral">Belum Ada Resep</x-badge>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-muted">
                                <div class="flex flex-col items-center justify-center">
                                    <x-icon name="book" class="mb-3 h-10 w-10 text-muted/50" />
                                    <p class="font-medium text-ink">Belum ada menu makanan yang terdaftar di sistem.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-app-layout>