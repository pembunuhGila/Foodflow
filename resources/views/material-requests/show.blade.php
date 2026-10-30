<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <!-- Breadcrumb Interaktif -->
                <nav class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-1.5">
                    <span class="text-gray-400">Persediaan &amp; Bahan</span>
                    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <a href="{{ route('material-requests.index') }}" class="hover:text-purple-600 transition-colors">
                        Permintaan Bahan
                    </a>
                    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-gray-900 font-semibold">Detail Permintaan</span>
                </nav>

                <!-- Judul & Status Badge -->
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                        Permintaan Bahan <span class="font-mono text-purple-700">#MR-{{ str_pad($materialRequest->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </h2>
                    @if ($materialRequest->isPending())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            Menunggu Verifikasi Gudang
                        </span>
                    @elseif ($materialRequest->isApproved())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            Telah Diverifikasi &amp; Stok Keluar Dicatat
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                            {{ ucfirst($materialRequest->status) }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-1">Verifikasi bahan baku yang diserahkan dan otomatisasi pencatatan kartu stok keluar.</p>
            </div>


        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Alert Messages --}}
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <p class="text-sm text-emerald-800 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <h4 class="text-sm text-rose-900 font-bold">Verifikasi Tidak Dapat Diproses:</h4>
                    </div>
                    <ul class="list-disc list-inside text-xs text-rose-700 space-y-1 ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- CARD 1: Informasi Permintaan Bahan --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Informasi Permintaan Bahan</h3>
                        <p class="text-xs text-gray-500">Data ringkasan permintaan dan relasi rencana produksi.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                    <!-- Nomor Permintaan -->
                    <div>
                        <span class="block text-[11px] text-gray-400 font-semibold uppercase tracking-wider mb-1">Nomor Permintaan</span>
                        <span class="font-mono font-bold text-purple-700 text-base">#MR-{{ str_pad($materialRequest->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <!-- Tanggal Permintaan -->
                    <div>
                        <span class="block text-[11px] text-gray-400 font-semibold uppercase tracking-wider mb-1">Tanggal Permintaan</span>
                        <span class="text-gray-900 font-semibold text-sm">
                            {{ $materialRequest->request_date ? \Carbon\Carbon::parse($materialRequest->request_date)->translatedFormat('d F Y') : '-' }}
                        </span>
                    </div>

                    <!-- Rencana Produksi (navigasi normal, tanpa target blank) -->
                    <div>
                        <span class="block text-[11px] text-gray-400 font-semibold uppercase tracking-wider mb-1">Rencana Produksi</span>
                        @if ($materialRequest->productionPlan)
                            <a href="{{ route('production-plans.show', $materialRequest->productionPlan) }}"
                               class="inline-flex items-center gap-1 font-mono font-bold text-purple-700 hover:text-purple-900 transition-colors text-sm hover:underline">
                                {{ $materialRequest->productionPlan->plan_number }}
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @else
                            <span class="text-gray-400 italic text-xs">Tanpa Relasi Rencana Produksi</span>
                        @endif
                    </div>

                    <!-- Diajukan Oleh -->
                    <div>
                        <span class="block text-[11px] text-gray-400 font-semibold uppercase tracking-wider mb-1">Diajukan Oleh</span>
                        <span class="text-gray-900 font-semibold text-sm">{{ $materialRequest->requestedBy?->name ?? 'Staff Dapur' }}</span>
                        <span class="text-xs text-gray-400 block mt-0.5">{{ $materialRequest->requestedBy?->role?->label() ?? 'Produksi' }}</span>
                    </div>

                    @if ($materialRequest->isApproved())
                        <!-- Diverifikasi Oleh -->
                        <div>
                            <span class="block text-[11px] text-gray-400 font-semibold uppercase tracking-wider mb-1">Diverifikasi Oleh</span>
                            <span class="text-emerald-800 font-semibold text-sm">{{ $materialRequest->verifiedBy?->name ?? 'Staff Gudang' }}</span>
                            <span class="text-xs text-emerald-600 block mt-0.5">{{ $materialRequest->verifiedBy?->role?->label() ?? 'Gudang' }}</span>
                        </div>
                    @endif
                </div>

                @if ($materialRequest->notes)
                    <div class="mt-5 pt-4 border-t border-gray-50">
                        <span class="block text-[11px] text-gray-400 font-semibold uppercase tracking-wider mb-1.5">Catatan Permintaan</span>
                        <p class="text-xs text-gray-700 bg-gray-50/60 border border-gray-200/60 rounded-xl p-3.5 whitespace-pre-line leading-relaxed">{{ $materialRequest->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- CARD 2: Rincian Bahan Baku --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Rincian Bahan Baku yang Diminta</h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                @if ($materialRequest->isPending())
                                    Periksa ketersediaan stok fisik gudang, sesuaikan jumlah bahan yang diserahkan, lalu konfirmasi.
                                @else
                                    Daftar bahan yang telah diserahkan ke dapur dan dicatat sebagai transaksi stok keluar.
                                @endif
                            </p>
                        </div>
                    </div>

                    @if ($materialRequest->isPending())
                        @if (Auth::user()->isGudang() || Auth::user()->isAdmin())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 self-start sm:self-auto">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Mode: Verifikasi Serah Terima Gudang
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 self-start sm:self-auto">
                                Menunggu Verifikasi Gudang
                            </span>
                        @endif
                    @endif
                </div>

                @if ($materialRequest->isPending() && (Auth::user()->isGudang() || Auth::user()->isAdmin()))
                    {{-- Alert Banner Info: Form Verifikasi Gudang --}}
                    <div class="mx-5 mt-5 bg-amber-50/60 border border-amber-100 rounded-xl p-3.5 flex items-center gap-3 text-xs text-amber-800">
                        <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <span>Anda masuk dalam mode <strong>Verifikasi Gudang</strong>. Sesuaikan jumlah bahan yang akan diserahkan ke dapur, lalu klik tombol konfirmasi. Stok gudang akan otomatis berkurang.</span>
                    </div>

                    {{-- Form Verifikasi Khusus Gudang & Admin --}}
                    <form method="POST" action="{{ route('material-requests.verify', $materialRequest) }}" id="verify-form">
                        @csrf

                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead class="bg-gray-50/70 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                    <tr>
                                        <th class="py-3 px-5 text-left">Bahan Baku</th>
                                        <th class="py-3 px-4 text-right">Stok Gudang</th>
                                        <th class="py-3 px-4 text-right">Jumlah Diminta</th>
                                        <th class="py-3 px-4 text-right w-52">Jumlah Diserahkan</th>
                                        <th class="py-3 px-4 text-left">Satuan</th>
                                        <th class="py-3 px-4 text-center">Status Stok</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 text-gray-800">
                                    @foreach ($materialRequest->details as $index => $detail)
                                        @php
                                            $currentStock = (float) ($detail->ingredient?->current_stock ?? 0);
                                            $requestedQty = (float) $detail->requested_quantity;
                                            $defaultIssued = old("items.{$index}.issued_quantity", min($requestedQty, max(0, $currentStock)));
                                            $hasDeficit = $currentStock < $requestedQty;
                                        @endphp
                                        <tr class="hover:bg-gray-50/50 transition {{ $hasDeficit ? 'bg-amber-50/20' : '' }}">
                                            <td class="py-3.5 px-5">
                                                <div class="font-bold text-gray-900 text-sm">{{ $detail->ingredient?->name ?? 'Bahan #' . $detail->ingredient_id }}</div>
                                                <div class="text-[11px] text-gray-400 font-normal mt-0.5">Kategori: {{ $detail->ingredient?->category ?? 'Umum' }}</div>
                                                <input type="hidden" name="items[{{ $index }}][detail_id]" value="{{ $detail->id }}">
                                            </td>
                                            <td class="py-3.5 px-4 text-right font-mono whitespace-nowrap">
                                                @if ($currentStock <= 0)
                                                    <span class="text-rose-600 font-bold">0.00 {{ $detail->unit }}</span>
                                                @elseif ($currentStock < $requestedQty)
                                                    <span class="text-amber-600 font-bold">{{ number_format($currentStock, 2) }} {{ $detail->unit }}</span>
                                                @else
                                                    <span class="text-emerald-700 font-semibold">{{ number_format($currentStock, 2) }} {{ $detail->unit }}</span>
                                                @endif
                                            </td>
                                            <td class="py-3.5 px-4 text-right font-mono font-bold text-gray-900 whitespace-nowrap">
                                                {{ number_format($requestedQty, 2) }} {{ $detail->unit }}
                                            </td>
                                            <td class="py-3.5 px-4 text-right">
                                                <input type="number"
                                                       step="0.001"
                                                       min="0"
                                                       max="{{ $requestedQty }}"
                                                       name="items[{{ $index }}][issued_quantity]"
                                                       value="{{ $defaultIssued }}"
                                                       required
                                                       class="w-full text-right text-sm font-mono font-bold rounded-xl border-gray-200 focus:border-purple-500 focus:ring focus:ring-purple-200 bg-gray-50/50">
                                                @error("items.{$index}.issued_quantity")
                                                    <span class="text-[11px] text-rose-600 block mt-1 text-left font-medium">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td class="py-3.5 px-4 text-gray-600 text-xs font-semibold whitespace-nowrap">
                                                {{ $detail->unit }}
                                            </td>
                                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                                @if ($currentStock <= 0)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-600 border border-rose-200">
                                                        Stok Kosong
                                                    </span>
                                                @elseif ($currentStock < $requestedQty)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                        Stok Kurang
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                                        Stok Aman
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Catatan Verifikasi & Aksi Konfirmasi --}}
                        <div class="p-5 sm:p-6 bg-gray-50/60 border-t border-gray-100 space-y-4 mt-1">
                            <div>
                                <label for="notes" class="block text-xs font-semibold text-gray-700 mb-1.5">
                                    Catatan Verifikasi Gudang (Opsional)
                                </label>
                                <textarea name="notes"
                                          id="notes"
                                          rows="2"
                                          placeholder="Tambahkan catatan mengenai serah terima bahan, kondisi fisik, atau penyesuaian jumlah..."
                                          class="w-full text-xs rounded-xl border-gray-200 focus:border-purple-500 focus:ring focus:ring-purple-200 bg-white">{{ old('notes') }}</textarea>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-1">
                                <button type="submit"
                                        onclick="return confirm('Apakah Anda yakin data jumlah serah terima sudah sesuai? Stok bahan baku akan otomatis berkurang dan dicatat sebagai transaksi stok keluar (Stok OUT).');"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-sm shadow-amber-100 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Konfirmasi Verifikasi &amp; Catat Stok OUT
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    {{-- Read-only view: Pending (non-Gudang) or Approved --}}
                    @if ($materialRequest->isPending())
                        {{-- Alert Banner: Menunggu Verifikasi Gudang (Read-only) --}}
                        <div class="mx-5 mt-5 bg-amber-50/60 border border-amber-100 rounded-xl p-3.5 flex items-center gap-3 text-xs text-amber-800">
                            <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <circle cx="10" cy="10" r="6" />
                                </svg>
                            </div>
                            <span>Permintaan ini telah diajukan ke Gudang dan sedang <strong>menunggu verifikasi fisik</strong> oleh petugas Gudang. Anda tidak dapat mengubah data permintaan ini.</span>
                        </div>

                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead class="bg-gray-50/70 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                    <tr>
                                        <th class="py-3 px-5 text-left">Bahan Baku</th>
                                        <th class="py-3 px-4 text-right">Jumlah Diminta</th>
                                        <th class="py-3 px-4 text-right">Stok Gudang</th>
                                        <th class="py-3 px-4 text-left">Satuan</th>
                                        <th class="py-3 px-4 text-center">Status Stok</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 text-gray-800">
                                    @foreach ($materialRequest->details as $detail)
                                        @php
                                            $requestedQty = (float) $detail->requested_quantity;
                                            $currentStock = (float) ($detail->ingredient?->current_stock ?? 0);
                                        @endphp
                                        <tr class="hover:bg-gray-50/50 transition">
                                            <td class="py-3.5 px-5">
                                                <div class="font-bold text-gray-900 text-sm">{{ $detail->ingredient?->name ?? 'Bahan #' . $detail->ingredient_id }}</div>
                                                <div class="text-[11px] text-gray-400 font-normal mt-0.5">Kategori: {{ $detail->ingredient?->category ?? 'Umum' }}</div>
                                            </td>
                                            <td class="py-3.5 px-4 text-right font-mono font-bold text-gray-900 whitespace-nowrap">
                                                {{ number_format($requestedQty, 2) }} {{ $detail->unit }}
                                            </td>
                                            <td class="py-3.5 px-4 text-right font-mono text-gray-600 whitespace-nowrap">
                                                {{ number_format($currentStock, 2) }} {{ $detail->unit }}
                                            </td>
                                            <td class="py-3.5 px-4 text-gray-500 text-xs font-semibold whitespace-nowrap">
                                                {{ $detail->unit }}
                                            </td>
                                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                                @if ($currentStock <= 0)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-600 border border-rose-200">
                                                        Stok Kosong
                                                    </span>
                                                @elseif ($currentStock < $requestedQty)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                        Stok Kurang
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                                        Stok Aman
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    @else
                        {{-- Approved: Read-only Table Jumlah Diserahkan & Pemenuhan --}}
                        <div class="mx-5 mt-5 bg-emerald-50/60 border border-emerald-100 rounded-xl p-3.5 flex items-center gap-3 text-xs text-emerald-800">
                            <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span>Permintaan ini telah <strong>diverifikasi dan diserahkan</strong> oleh petugas gudang. Transaksi stok keluar (Stok OUT - BR-05) sudah dicatat secara otomatis.</span>
                        </div>

                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead class="bg-gray-50/70 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                    <tr>
                                        <th class="py-3 px-5 text-left">Bahan Baku</th>
                                        <th class="py-3 px-4 text-right">Jumlah Diminta</th>
                                        <th class="py-3 px-4 text-right">Jumlah Diserahkan</th>
                                        <th class="py-3 px-4 text-left">Satuan</th>
                                        <th class="py-3 px-4 text-center">Status Pemenuhan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 text-gray-800">
                                    @foreach ($materialRequest->details as $detail)
                                        @php
                                            $requestedQty = (float) $detail->requested_quantity;
                                            $issuedQty = (float) $detail->issued_quantity;
                                            $fulfilled = $issuedQty >= $requestedQty;
                                        @endphp
                                        <tr class="hover:bg-gray-50/50 transition">
                                            <td class="py-3.5 px-5">
                                                <div class="font-bold text-gray-900 text-sm">{{ $detail->ingredient?->name ?? 'Bahan #' . $detail->ingredient_id }}</div>
                                                <div class="text-[11px] text-gray-400 font-normal mt-0.5">Kategori: {{ $detail->ingredient?->category ?? 'Umum' }}</div>
                                            </td>
                                            <td class="py-3.5 px-4 text-right font-mono text-gray-600 whitespace-nowrap">
                                                {{ number_format($requestedQty, 2) }} {{ $detail->unit }}
                                            </td>
                                            <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-700 whitespace-nowrap">
                                                {{ number_format($issuedQty, 2) }} {{ $detail->unit }}
                                            </td>
                                            <td class="py-3.5 px-4 text-gray-500 text-xs font-semibold whitespace-nowrap">
                                                {{ $detail->unit }}
                                            </td>
                                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                                @if ($fulfilled)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                                        Terpenuhi Penuh (100%)
                                                    </span>
                                                @elseif ($issuedQty > 0)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                        Sebagian ({{ round(($issuedQty / max(1, $requestedQty)) * 100) }}%)
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                                        Tidak Diserahkan (0%)
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- Riwayat Transaksi Kartu Stok Keluar --}}
                    @if ($materialRequest->stockTransactions->isNotEmpty())
                        <div class="p-5 sm:p-6 border-t border-gray-100 bg-gray-50/40 mt-1">
                            <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                                <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                Riwayat Transaksi Kartu Stok Keluar (Stok OUT - BR-05)
                            </h4>
                            <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-100 text-xs">
                                    <thead class="bg-gray-50/70 text-gray-500 font-semibold uppercase tracking-wider text-[11px]">
                                        <tr>
                                            <th class="py-2.5 px-4 text-left">Bahan Baku</th>
                                            <th class="py-2.5 px-3 text-center">Tipe Transaksi</th>
                                            <th class="py-2.5 px-3 text-right">Jumlah Keluar</th>
                                            <th class="py-2.5 px-3 text-left">Tanggal</th>
                                            <th class="py-2.5 px-3 text-left">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 font-medium">
                                        @foreach ($materialRequest->stockTransactions as $tx)
                                            <tr class="hover:bg-gray-50/50 transition">
                                                <td class="py-2.5 px-4 font-bold text-gray-900">
                                                    {{ $tx->ingredient?->name }}
                                                </td>
                                                <td class="py-2.5 px-3 text-center">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 uppercase">
                                                        {{ $tx->transaction_type }}
                                                    </span>
                                                </td>
                                                <td class="py-2.5 px-3 text-right font-mono font-bold text-rose-700">
                                                    -{{ number_format((float) $tx->quantity, 2) }} {{ $tx->ingredient?->unit }}
                                                </td>
                                                <td class="py-2.5 px-3 text-gray-600">
                                                    {{ $tx->transaction_date ? \Carbon\Carbon::parse($tx->transaction_date)->format('d/m/Y') : '-' }}
                                                </td>
                                                <td class="py-2.5 px-3 text-gray-500 truncate max-w-xs">
                                                    {{ $tx->notes ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
