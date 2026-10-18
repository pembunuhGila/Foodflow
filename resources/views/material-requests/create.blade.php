<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('material-requests.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Pengajuan Permintaan Bahan Baku ke Gudang') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Kirim daftar kebutuhan bahan dapur dari rencana produksi ke bagian gudang untuk diverifikasi & dikeluarkan (Stok OUT).
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($errors->any())
                <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
                    <div class="font-bold text-xs text-red-800 mb-1">Gagal Mengajukan Permintaan:</div>
                    <ul class="list-disc list-inside text-xs text-red-700">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-2">
                    Metode 1: Ajukan Berdasarkan Rencana Produksi (Rekomendasi)
                </h3>
                <p class="text-xs text-gray-600 mb-4">
                    Pilih rencana produksi yang sudah terjadwal. Sistem akan otomatis menghitung seluruh bahan baku yang dibutuhkan dari kalkulator resep dapur dan mengirimkannya langsung ke gudang.
                </p>

                @if ($productionPlans->isEmpty())
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-xs text-gray-600">
                        Belum ada jadwal rencana produksi yang aktif.
                        <a href="{{ route('production-plans.create') }}" class="font-semibold text-emerald-700 hover:underline">
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
                            @endphp
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-4 rounded-lg border border-gray-200 hover:border-emerald-300 hover:bg-emerald-50/20 transition gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-sm text-gray-900">{{ $plan->plan_number }}</span>
                                        <span class="text-xs text-gray-500">
                                            (Masak: {{ \Carbon\Carbon::parse($plan->production_date)->translatedFormat('d M Y') }})
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-600 mt-1">
                                        Menu:
                                        @foreach ($plan->details as $d)
                                            <span class="inline-block bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-[11px] me-1">
                                                {{ $d->product?->name }} ({{ $d->planned_quantity }} porsi)
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="shrink-0 flex items-center gap-2">
                                    @if ($hasPending)
                                        <a href="{{ route('material-requests.show', $hasPending) }}" class="inline-flex items-center px-3 py-1.5 bg-amber-50 text-amber-800 border border-amber-300 text-xs font-semibold rounded-lg hover:bg-amber-100 transition">
                                            Sedang Menunggu Gudang (#MR-{{ $hasPending->id }})
                                        </a>
                                    @elseif ($hasApproved)
                                        <a href="{{ route('material-requests.show', $hasApproved) }}" class="inline-flex items-center px-3 py-1.5 bg-emerald-50 text-emerald-800 border border-emerald-300 text-xs font-semibold rounded-lg hover:bg-emerald-100 transition">
                                            Sudah Diserahkan Gudang (#MR-{{ $hasApproved->id }})
                                        </a>
                                    @else
                                        <form method="POST" action="{{ route('production-plans.material-requests.store', $plan) }}">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('Kirim seluruh daftar bahan rencana produksi {{ $plan->plan_number }} ke bagian gudang?');"
                                                    class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                                                <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                </svg>
                                                Ajukan Bahan ke Gudang &rarr;
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Form Pengajuan Manual Tambahan --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6" x-data="{
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
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-2">
                    Metode 2: Formulir Permintaan Bahan Tambahan
                </h3>
                <p class="text-xs text-gray-600 mb-4">
                    Gunakan jika ada kebutuhan bahan mentah tambahan di luar racikan resep standar.
                </p>

                <form method="POST" action="{{ route('material-requests.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Rencana Produksi Terkait (Opsional)</label>
                            <select name="production_plan_id" class="w-full text-xs rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                                <option value="">-- Tanpa Rencana Produksi --</option>
                                @foreach ($productionPlans as $p)
                                    <option value="{{ $p->id }}">{{ $p->plan_number }} ({{ $p->production_date }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Permintaan</label>
                            <input type="date" name="request_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan Tambahan untuk Petugas Gudang</label>
                        <textarea name="notes" rows="2" placeholder="Tuliskan catatan khusus atau alasan permintaan..." class="w-full text-xs rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200"></textarea>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-bold text-gray-800">Daftar Bahan yang Diminta</label>
                            <button type="button" @click="addItem()" class="text-xs text-emerald-700 font-bold hover:underline">
                                + Tambah Bahan Lain
                            </button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200">
                                    <div class="flex-1">
                                        <select :name="'items[' + index + '][ingredient_id]'"
                                                x-model="item.ingredient_id"
                                                @change="updateUnit(index)"
                                                required
                                                class="w-full text-xs rounded border-gray-300 focus:border-emerald-500">
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
                                               class="w-full text-xs text-right rounded border-gray-300 focus:border-emerald-500">
                                    </div>
                                    <div class="w-20">
                                        <input type="text"
                                               placeholder="Satuan"
                                               :name="'items[' + index + '][unit]'"
                                               x-model="item.unit"
                                               readonly
                                               class="w-full text-xs bg-gray-100 text-gray-600 rounded border-gray-300">
                                    </div>
                                    <button type="button"
                                            @click="removeItem(index)"
                                            x-show="items.length > 1"
                                            class="text-red-500 hover:text-red-700 text-xs px-2">
                                        &times;
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-sm transition">
                            Kirim Permintaan ke Gudang
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
