<x-app-layout>
    <x-page-header eyebrow="Persediaan & Bahan" :title="__('Pengajuan Permintaan Bahan Baku ke Gudang')"
                   subtitle="Kirim daftar kebutuhan bahan dapur dari rencana produksi ke bagian gudang untuk diverifikasi & dikeluarkan (Stok OUT).">
        <x-slot name="actions">
            <x-button variant="outline" :href="route('material-requests.index')">
                <x-icon name="arrow-left" class="h-4 w-4" />
                Kembali
            </x-button>
        </x-slot>
    </x-page-header>

    <div class="space-y-6" x-data="{ selectedMethod: 'plan' }" x-cloak>

        @if ($errors->any())
            <div class="rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text" role="alert">
                <div class="mb-1 font-semibold">Gagal Mengajukan Permintaan:</div>
                <ul class="list-inside list-disc text-small">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- METODE 1: berdasarkan rencana produksi --}}
        <x-card class="sm:p-8" x-show="selectedMethod === 'plan'" x-cloak>
            <h3 class="font-display text-h3 text-ink">Metode 1: Ajukan Berdasarkan Rencana Produksi (Rekomendasi)</h3>
            <p class="mb-5 mt-1 text-body text-muted">
                Pilih rencana produksi yang sudah terjadwal. Sistem akan otomatis menghitung seluruh bahan baku yang dibutuhkan dari kalkulator resep dapur dan mengirimkannya langsung ke gudang.
            </p>

            @if ($productionPlans->isEmpty())
                <div class="rounded-input border border-line bg-primary/5 p-4 text-small text-muted">
                    Belum ada jadwal rencana produksi yang aktif.
                    <a href="{{ route('production-plans.create') }}" class="font-medium text-primary hover:text-primary-dark">
                        Buat Rencana Produksi Baru Terlebih Dahulu &rarr;
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($productionPlans as $plan)
                        @php
                            $hasPending = $plan->materialRequests->where('status', 'pending')->first()
                                ?? $plan->materialRequests->where('status', 'Pending')->first();
                            $hasApproved = $plan->materialRequests->where('status', 'approved')->first()
                                ?? $plan->materialRequests->where('status', 'Approved')->first();
                            $sendConfirm = [
                                'formId' => 'request-plan-' . $plan->id,
                                'tone' => 'primary',
                                'eyebrow' => 'Ajukan bahan ke gudang',
                                'title' => 'Kirim daftar bahan ke gudang?',
                                'subjectLabel' => 'Rencana Produksi',
                                'subject' => $plan->plan_number,
                                'message' => 'Kirim seluruh daftar bahan rencana produksi ' . $plan->plan_number . ' ke bagian gudang?',
                                'detail' => '',
                                'confirmLabel' => 'Ya, Ajukan Bahan',
                            ];
                        @endphp
                        <div class="flex flex-col gap-3 rounded-input border border-line p-4 transition hover:border-primary/40 hover:bg-primary/5 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-medium text-ink">{{ $plan->plan_number }}</span>
                                    <span class="text-small text-muted">
                                        (Masak: {{ \Carbon\Carbon::parse($plan->production_date)->locale('id')->translatedFormat('d M Y') }})
                                    </span>
                                </div>
                                <div class="mt-1.5 flex flex-wrap items-center gap-1.5 text-small text-muted">
                                    <span>Menu:</span>
                                    @foreach ($plan->details as $d)
                                        <span class="rounded-full bg-primary/10 px-2.5 py-0.5 text-small text-primary">
                                            {{ $d->product?->name }} ({{ $d->planned_quantity }} porsi)
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center gap-2">
                                @if ($hasPending)
                                    <x-button variant="secondary" size="sm" :href="route('material-requests.show', $hasPending)">
                                        Sedang Menunggu Gudang (#MR-{{ $hasPending->id }})
                                    </x-button>
                                @elseif ($hasApproved)
                                    <x-button variant="outline" size="sm" :href="route('material-requests.show', $hasApproved)">
                                        Sudah Diserahkan Gudang (#MR-{{ $hasApproved->id }})
                                    </x-button>
                                @else
                                    <form id="request-plan-{{ $plan->id }}" method="POST" action="{{ route('production-plans.material-requests.store', $plan) }}">
                                        @csrf
                                        <button type="button" @click="$dispatch('open-confirm', @js($sendConfirm))"
                                                class="inline-flex items-center justify-center gap-2 rounded-full bg-primary px-5 py-2.5 text-body font-medium text-white shadow-card transition hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/30 active:scale-[0.98]">
                                            <x-icon name="send" class="h-4 w-4" />
                                            Ajukan Bahan ke Gudang &rarr;
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>

        {{-- METODE 2: formulir permintaan tambahan --}}
        <div class="rounded-card border border-line bg-white p-6 shadow-card sm:p-8" x-data="{
            items: [
                { ingredient_id: '', requested_quantity: '', unit: '' }
            ],
            ingredients: {{ Js::from($ingredients->map(fn($i) => ['id' => $i->id, 'name' => $i->name, 'unit' => $i->unit])) }},
            updateUnit(index) {
                const selectedId = this.items[index].ingredient_id;
                const found = this.ingredients.find(i => i.id == selectedId);
                if (found) {
                    this.items[index].unit = found.unit;
                }
            },
            addItem() {
                this.items.push({ ingredient_id: '', requested_quantity: '', unit: '' });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            }
        }">
            <h3 class="font-display text-h3 text-ink">Metode 2: Formulir Permintaan Bahan Tambahan</h3>
            <p class="mb-5 mt-1 text-body text-muted">
                Gunakan jika ada kebutuhan bahan mentah tambahan di luar racikan resep standar.
            </p>

            <form method="POST" action="{{ route('material-requests.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <x-input-label for="production_plan_id" value="Rencana Produksi Terkait (Opsional)" />
                        <x-select name="production_plan_id" id="production_plan_id" class="mt-1.5 block">
                            <option value="">-- Tanpa Rencana Produksi --</option>
                            @foreach ($productionPlans as $p)
                                <option value="{{ $p->id }}">{{ $p->plan_number }} ({{ $p->production_date }})</option>
                            @endforeach
                        </x-select>
                    </div>

                    <div>
                        <x-input-label for="request_date" value="Tanggal Permintaan" />
                        <x-text-input type="date" name="request_date" id="request_date" :value="date('Y-m-d')" required class="mt-1.5 block w-full" />
                    </div>
                </div>

                <div>
                    <x-input-label for="notes" value="Catatan Tambahan untuk Petugas Gudang" />
                    <x-textarea name="notes" id="notes" rows="2" placeholder="Tuliskan catatan khusus atau alasan permintaan..." class="mt-1.5 block"></x-textarea>
                </div>

                <div class="border-t border-line pt-5">
                    <div class="mb-3 flex items-center justify-between">
                        <span class="text-label uppercase text-muted">Daftar Bahan yang Diminta</span>
                        <button type="button" @click="addItem()" class="text-small font-medium text-primary transition hover:text-primary-dark">
                            + Tambah Bahan Lain
                        </button>
                    </div>

                    <div class="space-y-2">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex items-center gap-2 rounded-input border border-line bg-primary/5 p-2">
                                <div class="flex-1">
                                    <select :name="'items[' + index + '][ingredient_id]'"
                                            x-model="item.ingredient_id"
                                            @change="updateUnit(index)"
                                            required
                                            class="h-11 w-full rounded-input border-line bg-white text-body text-ink shadow-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                                        <option value="">-- Pilih Bahan Baku --</option>
                                        <template x-for="ing in ingredients" :key="ing.id">
                                            <option :value="ing.id" x-text="ing.name + ' (' + ing.unit + ')'"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="w-32">
                                    <input type="number"
                                           step="0.001"
                                           min="0.001"
                                           placeholder="Jumlah"
                                           :name="'items[' + index + '][requested_quantity]'"
                                           x-model="item.requested_quantity"
                                           required
                                           class="h-11 w-full rounded-input border-line bg-white text-right text-body text-ink shadow-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                                </div>
                                <div class="w-24">
                                    <input type="text"
                                           placeholder="Satuan"
                                           :name="'items[' + index + '][unit]'"
                                           x-model="item.unit"
                                           readonly
                                           class="h-11 w-full rounded-input border-line bg-white/60 text-body text-muted shadow-none">
                                </div>
                                <button type="button"
                                        @click="removeItem(index)"
                                        x-show="items.length > 1"
                                        class="flex h-9 w-9 items-center justify-center rounded-full text-danger-text transition hover:bg-danger-bg"
                                        aria-label="Hapus baris">
                                    <x-icon name="close" class="h-4 w-4" />
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end border-t border-line pt-5">
                    <x-button type="submit">
                        <x-icon name="send" class="h-4 w-4" />
                        Kirim Permintaan ke Gudang
                    </x-button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>