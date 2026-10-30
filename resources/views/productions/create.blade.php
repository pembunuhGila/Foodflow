<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('productions.index') }}" class="text-xs text-gray-500 hover:text-emerald-600 transition">
                        &larr; Kembali ke Daftar Realisasi
                    </a>
                </div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mt-1">
                    {{ __('Form Pencatatan Realisasi Masak Dapur') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Input kuantitas porsi masakan yang berhasil dimasak untuk setiap menu pada rencana: <strong class="text-gray-800">{{ $plan->plan_number }}</strong>
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $plan->status->badgeClass() }}">
                Status Rencana: {{ $plan->status->label() }}
            </span>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash & Error Alert -->
            @if ($errors->any())
                <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-r-lg shadow-sm">
                    <div class="flex items-center space-x-2 mb-1">
                        <svg class="w-5 h-5 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-bold">Terdapat kesalahan dalam pengisian formulir:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 ms-6 text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Ringkasan Rencana Produksi -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">
                    Informasi Rencana Produksi
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-xs text-gray-500 block">Nomor Rencana</span>
                        <span class="font-bold text-gray-800 font-mono text-base">{{ $plan->plan_number }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Jadwal Tanggal Masak</span>
                        <span class="font-semibold text-gray-800">{{ $plan->production_date->translatedFormat('d F Y') }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Dibuat Oleh</span>
                        <span class="font-semibold text-gray-800">{{ $plan->creator?->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Total Menu / Target Porsi</span>
                        <span class="font-semibold text-gray-800">
                            {{ $plan->details->count() }} Menu / <span class="text-emerald-700 font-bold">{{ $plan->totalPlannedQuantity() }} Porsi</span>
                        </span>
                    </div>
                </div>
                @if ($plan->notes)
                    <div class="mt-4 pt-3 border-t border-gray-50 text-xs text-gray-600">
                        <span class="font-bold text-gray-700">Catatan Rencana:</span> {{ $plan->notes }}
                    </div>
                @endif
            </div>

            <!-- Form Pencatatan Realisasi -->
            <form method="POST" action="{{ route('production-plans.realization.store', $plan) }}" class="space-y-6">
                @csrf

                <!-- Tanggal Realisasi & Catatan Operasional -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b border-gray-100 pb-2">
                        Parameter Realisasi Hari Ini
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="production_date" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                Tanggal Pelaksanaan Masak <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   name="production_date"
                                   id="production_date"
                                   value="{{ old('production_date', $today) }}"
                                   required
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                            <span class="text-[11px] text-gray-500 mt-1 block">Tanggal fisik masakan diselesaikan oleh tim dapur.</span>
                        </div>
                        <div>
                            <label for="notes" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                Catatan Umum Dapur (Opsional)
                            </label>
                            <input type="text"
                                   name="notes"
                                   id="notes"
                                   value="{{ old('notes') }}"
                                   placeholder="Contoh: Batch masak selesai tepat waktu, tidak ada kendala."
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                        </div>
                    </div>
                </div>

                <!-- Tabel Perbandingan Rencana vs Realisasi Porsi -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">
                                Perbandingan Porsi Rencana vs Porsi Aktual Masak
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Masukkan jumlah porsi matang riil (<span class="font-mono text-gray-700 font-semibold">actual_quantity</span>) untuk masing-masing hidangan.
                            </p>
                        </div>
                        <div class="text-xs bg-amber-50 text-amber-800 border border-amber-200 px-3 py-1.5 rounded-lg flex items-center space-x-1.5 shrink-0">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Status <strong>Selesai</strong> otomatis jika seluruh menu memenuhi target.</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left divide-y divide-gray-100">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50/80 font-bold tracking-wider">
                                <tr>
                                    <th scope="col" class="px-6 py-4 w-12 text-center">No</th>
                                    <th scope="col" class="px-6 py-4">Menu Hidangan</th>
                                    <th scope="col" class="px-6 py-4 text-center w-36">Porsi Rencana</th>
                                    <th scope="col" class="px-6 py-4 text-center w-36">Status Riil</th>
                                    <th scope="col" class="px-6 py-4 text-center w-48">Porsi Aktual Baru</th>
                                    <th scope="col" class="px-6 py-4">Catatan Menu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($plan->details as $index => $detail)
                                    @php
                                        $currentActual = $detail->production?->actual_quantity ?? 0;
                                        $initialInput = old("items.{$index}.actual_quantity", $currentActual > 0 ? $currentActual : '');
                                        $isMet = $currentActual >= $detail->planned_quantity;
                                        $isPart = $currentActual > 0 && $currentActual < $detail->planned_quantity;
                                    @endphp
                                    <tr class="hover:bg-gray-50/60 transition">
                                        <td class="px-6 py-4 text-center font-bold text-gray-400 text-xs">
                                            {{ $index + 1 }}
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900">{{ $detail->product->name }}</div>
                                            <div class="text-xs text-gray-400">
                                                Satuan: {{ $detail->product->unit ?? 'porsi' }}
                                                @if ($detail->notes)
                                                    &bull; <span class="italic text-gray-500">{{ $detail->notes }}</span>
                                                @endif
                                            </div>
                                            <input type="hidden" name="items[{{ $index }}][detail_id]" value="{{ $detail->id }}">
                                        </td>

                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-800 font-bold font-mono text-sm rounded-lg border border-gray-200">
                                                {{ $detail->planned_quantity }}
                                            </div>
                                            <span class="block text-[10px] text-gray-400 mt-0.5">Target</span>
                                        </td>

                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            @if ($currentActual === 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                                    Belum Dimasak
                                                </span>
                                            @elseif ($isMet)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    <svg class="w-3 h-3 me-1 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    Tercapai ({{ $currentActual }})
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                                    Parsial ({{ $currentActual }}/{{ $detail->planned_quantity }})
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="relative rounded-md shadow-sm max-w-[160px] mx-auto">
                                                <input type="number"
                                                       name="items[{{ $index }}][actual_quantity]"
                                                       value="{{ $initialInput }}"
                                                       min="0"
                                                       step="1"
                                                       placeholder="{{ $detail->planned_quantity }}"
                                                       required
                                                       class="w-full text-center font-mono font-bold text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 @error("items.{$index}.actual_quantity") border-red-500 ring-red-500 @enderror">
                                            </div>
                                            @error("items.{$index}.actual_quantity")
                                                <span class="text-[11px] text-red-600 block text-center mt-1">{{ $message }}</span>
                                            @enderror
                                        </td>

                                        <td class="px-6 py-4">
                                            <input type="text"
                                                   name="items[{{ $index }}][notes]"
                                                   value="{{ old("items.{$index}.notes", $detail->production?->notes) }}"
                                                   placeholder="Contoh: Rasa dan tingkat kematangan sesuai SOP"
                                                   class="w-full text-xs rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Form & Tombol Simpan -->
                    <div class="p-6 bg-gray-50/80 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-xs text-gray-500">
                            <span class="font-semibold text-gray-700">Catatan Validasi:</span> Nilai aktual tidak boleh negatif. Nilai porsi lebih tinggi diperbolehkan jika dapur menghasilkan porsi cadangan.
                        </div>

                        <div class="flex items-center space-x-3">
                            <a href="{{ route('productions.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-100 transition shadow-sm">
                                Batal
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-2.5 bg-emerald-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition shadow-md">
                                <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan Realisasi Produksi
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
