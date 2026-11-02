<x-app-layout>
    @php
        $inputClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';
        $labelClass = 'mb-1.5 block text-label uppercase text-muted';
    @endphp

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('productions.index') }}" class="text-muted transition hover:text-primary">Daftar Realisasi</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Catat Realisasi</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">Pencatatan Realisasi Masak Dapur</h1>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-small font-medium text-amber-800">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> {{ $plan->status->label() }}
                </span>
            </div>
            <p class="mt-1 text-small text-muted">
                Input jumlah porsi yang berhasil dimasak untuk setiap menu pada rencana
                <span class="font-medium text-ink">{{ $plan->plan_number }}</span>.
            </p>
        </div>

        <a href="{{ route('productions.index') }}"
            class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-6">

        @if ($errors->any())
        <div class="rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text" role="alert">
            <p class="mb-2 font-semibold">Terdapat kesalahan dalam pengisian formulir:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Informasi rencana -->
        <x-card class="w-full sm:p-8">
            <div class="border-b border-line pb-4">
                <h2 class="text-lg font-semibold text-ink">Informasi Rencana Produksi</h2>
            </div>

            <dl class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Nomor Rencana</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $plan->plan_number }}</dd>
                </div>
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Jadwal Tanggal Masak</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $plan->production_date->locale('id')->translatedFormat('d F Y') }}</dd>
                </div>
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Dibuat Oleh</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $plan->creator?->name ?? '-' }}</dd>
                </div>
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Total Menu / Target Porsi</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $plan->details->count() }} menu / {{ $plan->totalPlannedQuantity() }} porsi</dd>
                </div>

                @if ($plan->notes)
                <div class="rounded-input border border-line bg-primary/5 p-4 md:col-span-2 lg:col-span-4">
                    <dt class="text-label uppercase text-muted">Catatan Rencana</dt>
                    <dd class="mt-1.5 whitespace-pre-line text-body font-medium text-ink">{{ $plan->notes }}</dd>
                </div>
                @endif
            </dl>
        </x-card>

        <form method="POST" action="{{ route('production-plans.realization.store', $plan) }}" class="space-y-6">
            @csrf

            <!-- Parameter realisasi -->
            <x-card class="w-full sm:p-8">
                <div class="border-b border-line pb-4">
                    <h2 class="text-lg font-semibold text-ink">Parameter Realisasi Hari Ini</h2>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="production_date" class="{{ $labelClass }}">Tanggal Pelaksanaan Masak <span class="text-danger-text">*</span></label>
                        <input type="date" name="production_date" id="production_date"
                               value="{{ old('production_date', $today) }}" required class="{{ $inputClass }}">
                        <p class="mt-1 text-small text-muted">Tanggal fisik masakan diselesaikan oleh tim dapur.</p>
                    </div>

                    <div>
                        <label for="notes" class="{{ $labelClass }}">Catatan Umum Dapur (Opsional)</label>
                        <input type="text" name="notes" id="notes" value="{{ old('notes') }}"
                               placeholder="Contoh: Batch masak selesai tepat waktu, tidak ada kendala."
                               class="{{ $inputClass }}">
                    </div>
                </div>
            </x-card>

            <!-- Tabel perbandingan -->
            <x-card :padding="false" class="overflow-hidden">
                <div class="border-b border-line px-6 py-5">
                    <h2 class="text-lg font-semibold text-ink">Perbandingan Porsi Rencana vs Porsi Aktual Masak</h2>
                    <p class="mt-0.5 text-small text-muted">
                        Masukkan jumlah porsi matang riil untuk masing-masing hidangan. Status Selesai otomatis jika seluruh menu memenuhi target.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-body">
                        <thead class="bg-primary/5">
                            <tr>
                                <th scope="col" class="w-12 px-6 py-3.5 text-center text-label uppercase text-muted">No</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Menu Hidangan</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Porsi Rencana</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Status Riil</th>
                                <th scope="col" class="w-48 px-6 py-3.5 text-left text-label uppercase text-muted">Porsi Aktual Baru</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Catatan Menu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line bg-white">
                            @foreach ($plan->details as $index => $detail)
                            @php
                                $currentActual = $detail->production?->actual_quantity ?? 0;
                                $initialInput = old("items.{$index}.actual_quantity", $currentActual > 0 ? $currentActual : '');
                                $isMet = $currentActual >= $detail->planned_quantity;
                            @endphp
                            <tr class="transition hover:bg-primary/5">
                                <td class="px-6 py-4 text-center text-small text-muted">{{ $index + 1 }}</td>

                                <td class="px-6 py-4">
                                    <div class="font-medium text-ink">{{ $detail->product->name }}</div>
                                    <div class="text-small text-muted">
                                        Satuan: {{ $detail->product->unit ?? 'porsi' }}@if ($detail->notes) &middot; {{ $detail->notes }}@endif
                                    </div>
                                    <input type="hidden" name="items[{{ $index }}][detail_id]" value="{{ $detail->id }}">
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 font-medium text-ink">{{ $detail->planned_quantity }} porsi</td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($currentActual === 0)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-small font-medium text-gray-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> Belum dimasak
                                    </span>
                                    @elseif ($isMet)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2.5 py-1 text-small font-medium text-primary">
                                        <span class="h-1.5 w-1.5 rounded-full bg-primary"></span> Tercapai ({{ $currentActual }})
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-small font-medium text-amber-800">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Parsial ({{ $currentActual }}/{{ $detail->planned_quantity }})
                                    </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <input type="number" name="items[{{ $index }}][actual_quantity]"
                                           value="{{ $initialInput }}" min="0" step="1"
                                           placeholder="{{ $detail->planned_quantity }}" required
                                           class="{{ $inputClass }} text-right @error("items.{$index}.actual_quantity") border-danger-text @enderror">
                                    @error("items.{$index}.actual_quantity")
                                    <span class="mt-1 block text-small text-danger-text">{{ $message }}</span>
                                    @enderror
                                </td>

                                <td class="px-6 py-4">
                                    <input type="text" name="items[{{ $index }}][notes]"
                                           value="{{ old("items.{$index}.notes", $detail->production?->notes) }}"
                                           placeholder="Contoh: rasa dan kematangan sesuai SOP"
                                           class="{{ $inputClass }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col gap-4 border-t border-line bg-primary/5 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-small text-muted">
                        <span class="font-medium text-ink">Catatan validasi:</span> nilai aktual tidak boleh negatif. Nilai lebih tinggi dari target diperbolehkan jika dapur menghasilkan porsi cadangan.
                    </p>

                    <div class="flex items-center gap-3">
                        <x-button variant="outline" :href="route('productions.index')">Batal</x-button>
                        <x-button type="submit">Simpan Realisasi Produksi</x-button>
                    </div>
                </div>
            </x-card>
        </form>

    </div>
</x-app-layout>