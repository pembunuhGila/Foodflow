<x-app-layout>
    @php
        $inputClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';
        $labelClass = 'mb-1.5 block text-label uppercase text-muted';
        $currentVal = old('status', $plan->status instanceof \App\Enums\ProductionPlanStatus ? $plan->status->value : $plan->status);
    @endphp

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('production-plans.index') }}" class="text-muted transition hover:text-primary">Rencana Produksi</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('production-plans.show', $plan) }}" class="text-muted transition hover:text-primary">Detail Rencana</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Edit Rencana</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">Edit Rencana Produksi: {{ $plan->plan_number }}</h1>
            </div>
            <p class="mt-1 text-small text-muted">Perbarui tanggal pelaksanaan, status operasional, atau target porsi hidangan katering.</p>
        </div>

        <a href="{{ route('production-plans.show', $plan) }}"
            class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Detail
        </a>
    </div>

    <div class="space-y-6">

        @if ($errors->any())
        <div class="rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text" role="alert">
            <p class="mb-2 font-semibold">Mohon periksa kembali input Anda:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('production-plans.update', $plan) }}"
              x-data="productionPlanForm({
                  availableProducts: @js($products),
                  oldItems: @js(old('items', $plan->details->map(fn($d) => [
                      'product_id' => $d->product_id,
                      'planned_quantity' => $d->planned_quantity,
                      'notes' => $d->notes ?? '',
                  ])->toArray()))
              })"
              class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Bagian 1 -->
            <x-card class="w-full sm:p-8">
                <div class="border-b border-line pb-4">
                    <h2 class="text-lg font-semibold text-ink">Jadwal &amp; Status Rencana Produksi</h2>
                    <p class="mt-0.5 text-small text-muted">Atur jadwal pelaksanaan dan status operasional dapur.</p>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div>
                        <label for="plan_date" class="{{ $labelClass }}">Tanggal Perencanaan <span class="text-danger-text">*</span></label>
                        <input type="date" name="plan_date" id="plan_date" required class="{{ $inputClass }}"
                               value="{{ old('plan_date', $plan->plan_date ? $plan->plan_date->format('Y-m-d') : '') }}">
                    </div>

                    <div>
                        <label for="production_date" class="{{ $labelClass }}">Tanggal Jadwal Masak <span class="text-danger-text">*</span></label>
                        <input type="date" name="production_date" id="production_date" required class="{{ $inputClass }}"
                               value="{{ old('production_date', $plan->production_date ? $plan->production_date->format('Y-m-d') : '') }}">
                    </div>

                    <div>
                        <label for="status" class="{{ $labelClass }}">Status Rencana <span class="text-danger-text">*</span></label>
                        <select name="status" id="status" required class="{{ $inputClass }}">
                            @foreach ($statuses as $st)
                            <option value="{{ $st->value }}" {{ $currentVal === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label for="notes" class="{{ $labelClass }}">Catatan Operasional / Instruksi Khusus (Opsional)</label>
                        <textarea name="notes" id="notes" rows="2" class="{{ $inputClass }}">{{ old('notes', $plan->notes) }}</textarea>
                    </div>
                </div>
            </x-card>

            <!-- Bagian 2 -->
            <x-card class="w-full sm:p-8">
                <div class="flex flex-col gap-3 border-b border-line pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-ink">Daftar Menu Hidangan yang Dimasak</h2>
                        <p class="mt-0.5 text-small text-muted">Perbarui menu makanan atau sesuaikan target porsi.</p>
                    </div>
                    <button type="button" @click="addItem()"
                            class="inline-flex shrink-0 items-center gap-2 self-start rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-primary transition hover:bg-primary/5">
                        <x-icon name="plus" class="h-4 w-4" />
                        Tambah Menu Lain
                    </button>
                </div>

                <div class="mt-6 space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="rounded-input border border-line bg-primary/5 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <span class="text-label uppercase text-muted" x-text="'Menu #' + (index + 1)"></span>
                                <button type="button" x-show="items.length > 1" @click="removeItem(index)"
                                        class="text-small font-medium text-danger-text transition hover:underline">Hapus</button>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-12">
                                <div class="md:col-span-6">
                                    <label class="{{ $labelClass }}">Menu Makanan <span class="text-danger-text">*</span></label>
                                    <select :name="'items[' + index + '][product_id]'" x-model="item.product_id" required class="{{ $inputClass }}">
                                        <option value="">-- Pilih Menu Masakan --</option>
                                        <template x-for="p in availableProducts" :key="p.id">
                                            <option :value="p.id" :selected="item.product_id == p.id"
                                                    x-text="p.name + (p.recipe_items_count > 0 ? ' (Resep Siap)' : ' (Belum ada resep)')"></option>
                                        </template>
                                    </select>
                                </div>

                                <div class="md:col-span-3">
                                    <label class="{{ $labelClass }}">Target Porsi <span class="text-danger-text">*</span></label>
                                    <input type="number" :name="'items[' + index + '][planned_quantity]'"
                                           x-model.number="item.planned_quantity" min="1" step="1" required class="{{ $inputClass }}">
                                </div>

                                <div class="md:col-span-3">
                                    <label class="{{ $labelClass }}">Catatan Khusus (Opsional)</label>
                                    <input type="text" :name="'items[' + index + '][notes]'" x-model="item.notes" class="{{ $inputClass }}">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-x-8 gap-y-3 border-t border-line pt-6">
                    <div>
                        <div class="text-label uppercase text-muted">Total Ragam Menu</div>
                        <div class="mt-1 text-body font-medium text-ink" x-text="items.filter(i => i.product_id).length + ' menu'"></div>
                    </div>
                    <div>
                        <div class="text-label uppercase text-muted">Total Target Masak</div>
                        <div class="mt-1 text-2xl font-semibold text-primary" x-text="totalPlannedQuantity() + ' porsi'"></div>
                    </div>
                </div>
            </x-card>

            <div class="flex items-center justify-end gap-3">
                <x-button variant="outline" :href="route('production-plans.show', $plan)">Batal</x-button>
                <x-button type="submit">Simpan Perubahan Rencana</x-button>
            </div>
        </form>
    </div>

    <!-- Alpine.js Component Script (tidak diubah) -->
    <script>
        function productionPlanForm(config) {
            return {
                availableProducts: config.availableProducts || [],
                items: (config.oldItems && Array.isArray(config.oldItems) && config.oldItems.length > 0)
                    ? config.oldItems
                    : [{ product_id: '', planned_quantity: 50, notes: '' }],
                addItem() {
                    this.items.push({ product_id: '', planned_quantity: 50, notes: '' });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                totalPlannedQuantity() {
                    return this.items.reduce((total, item) => {
                        const qty = parseInt(item.planned_quantity, 10);
                        return total + (isNaN(qty) ? 0 : qty);
                    }, 0);
                }
            };
        }
    </script>
</x-app-layout>