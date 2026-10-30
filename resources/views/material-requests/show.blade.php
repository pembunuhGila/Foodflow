<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-3">
                <a href="{{ url()->previous() ?: route('material-requests.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Detail Permintaan Bahan #MR-{{ str_pad($materialRequest->id, 4, '0', STR_PAD_LEFT) }}
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Verifikasi bahan baku yang diserahkan dan otomatisasi pencatatan kartu stok keluar.
                    </p>
                </div>
            </div>

            <div>
                @if ($materialRequest->isPending())
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                        <span class="w-2 h-2 rounded-full bg-amber-500 me-2 animate-pulse"></span>
                        Menunggu Verifikasi Gudang
                    </span>
                @elseif ($materialRequest->isApproved())
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        <svg class="w-4 h-4 me-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Telah Diverifikasi & Stok Keluar Dicatat
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                        {{ ucfirst($materialRequest->status) }}
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Alert Messages --}}
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg shadow-sm flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm text-emerald-800 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
                    <div class="flex items-center space-x-2 mb-1">
                        <svg class="w-5 h-5 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <h4 class="text-sm text-red-800 font-bold">Verifikasi Tidak Dapat Diproses:</h4>
                    </div>
                    <ul class="list-disc list-inside text-xs text-red-700 space-y-1 ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Info Ringkasan Permintaan --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">
                    Informasi Permintaan Bahan
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-sm">
                    <div>
                        <span class="block text-xs text-gray-500 font-medium">Nomor Permintaan</span>
                        <span class="font-mono font-bold text-gray-900">#MR-{{ str_pad($materialRequest->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <div>
                        <span class="block text-xs text-gray-500 font-medium">Tanggal Permintaan</span>
                        <span class="text-gray-900 font-semibold">
                            {{ $materialRequest->request_date ? \Carbon\Carbon::parse($materialRequest->request_date)->translatedFormat('d F Y') : '-' }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-xs text-gray-500 font-medium">Rencana Produksi</span>
                        @if ($materialRequest->productionPlan)
                            <a href="{{ route('production-plans.show', $materialRequest->productionPlan) }}" class="text-blue-600 font-semibold hover:underline inline-flex items-center gap-1">
                                {{ $materialRequest->productionPlan->plan_number }}
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        @else
                            <span class="text-gray-400 italic">Tanpa Relasi Rencana Produksi</span>
                        @endif
                    </div>

                    <div>
                        <span class="block text-xs text-gray-500 font-medium">Diajukan Oleh</span>
                        <span class="text-gray-900 font-semibold">{{ $materialRequest->requestedBy?->name ?? 'Staff Dapur' }}</span>
                        <span class="text-xs text-gray-500 block">({{ $materialRequest->requestedBy?->role?->label() ?? 'Produksi' }})</span>
                    </div>

                    @if ($materialRequest->isApproved())
                        <div>
                            <span class="block text-xs text-gray-500 font-medium">Diverifikasi Oleh</span>
                            <span class="text-emerald-900 font-semibold">{{ $materialRequest->verifiedBy?->name ?? 'Staff Gudang' }}</span>
                            <span class="text-xs text-emerald-600 block">({{ $materialRequest->verifiedBy?->role?->label() ?? 'Gudang' }})</span>
                        </div>
                    @endif

                    @if ($materialRequest->notes)
                        <div class="md:col-span-3">
                            <span class="block text-xs text-gray-500 font-medium">Catatan Permintaan</span>
                            <p class="text-xs text-gray-700 bg-gray-50 p-2.5 rounded-lg border border-gray-100 mt-1 whitespace-pre-line">
                                {{ $materialRequest->notes }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Formulir Verifikasi / Tabel Detail Bahan --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">
                            Rincian Bahan Baku yang Diminta
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            @if ($materialRequest->isPending())
                                Periksa ketersediaan stok fisik gudang, sesuaikan jumlah bahan yang diserahkan, lalu lakukan konfirmasi.
                            @else
                                Daftar bahan yang telah diserahkan ke dapur dan dicatat sebagai transaksi stok keluar.
                            @endif
                        </p>
                    </div>

                    @if ($materialRequest->isPending())
                        @if (Auth::user()->isGudang() || Auth::user()->isAdmin())
                            <span class="text-xs font-semibold px-2.5 py-1 bg-amber-100 text-amber-800 rounded-md border border-amber-300">
                                Mode: Verifikasi Serah Terima Gudang
                            </span>
                        @else
                            <span class="text-xs font-semibold px-2.5 py-1 bg-blue-50 text-blue-700 rounded-md border border-blue-200">
                                Status: Menunggu Verifikasi Gudang
                            </span>
                        @endif
                    @endif
                </div>

                @if ($materialRequest->isPending() && (Auth::user()->isGudang() || Auth::user()->isAdmin()))
                    {{-- Form Verifikasi Khusus Gudang & Admin --}}
                    <form method="POST" action="{{ route('material-requests.verify', $materialRequest) }}" id="verify-form">
                        @csrf

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead class="bg-gray-50/80 text-gray-600 text-xs uppercase tracking-wider font-semibold">
                                    <tr>
                                        <th class="py-3 px-4 text-left">Bahan Baku</th>
                                        <th class="py-3 px-4 text-right">Stok Gudang</th>
                                        <th class="py-3 px-4 text-right">Jumlah Diminta</th>
                                        <th class="py-3 px-4 text-right w-56">Jumlah Diserahkan</th>
                                        <th class="py-3 px-4 text-left">Satuan</th>
                                        <th class="py-3 px-4 text-left">Status Stok</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-gray-800">
                                    @foreach ($materialRequest->details as $index => $detail)
                                        @php
                                            $currentStock = (float) ($detail->ingredient?->current_stock ?? 0);
                                            $requestedQty = (float) $detail->requested_quantity;
                                            $defaultIssued = old("items.{$index}.issued_quantity", min($requestedQty, max(0, $currentStock)));
                                            $hasDeficit = $currentStock < $requestedQty;
                                        @endphp
                                        <tr class="hover:bg-gray-50/50 transition {{ $hasDeficit ? 'bg-amber-50/30' : '' }}">
                                            <td class="py-3.5 px-4 font-semibold text-gray-900">
                                                <div>{{ $detail->ingredient?->name ?? 'Bahan #' . $detail->ingredient_id }}</div>
                                                <div class="text-[11px] text-gray-400 font-normal">Kategori: {{ $detail->ingredient?->category ?? 'Umum' }}</div>
                                                <input type="hidden" name="items[{{ $index }}][detail_id]" value="{{ $detail->id }}">
                                            </td>
                                            <td class="py-3.5 px-4 text-right font-mono">
                                                @if ($currentStock <= 0)
                                                    <span class="text-red-600 font-bold">0.00 {{ $detail->unit }}</span>
                                                @elseif ($currentStock < $requestedQty)
                                                    <span class="text-amber-600 font-bold">{{ number_format($currentStock, 2) }} {{ $detail->unit }}</span>
                                                @else
                                                    <span class="text-emerald-700 font-semibold">{{ number_format($currentStock, 2) }} {{ $detail->unit }}</span>
                                                @endif
                                            </td>
                                            <td class="py-3.5 px-4 text-right font-mono font-semibold text-gray-900">
                                                {{ number_format($requestedQty, 2) }} {{ $detail->unit }}
                                            </td>
                                            <td class="py-3.5 px-4 text-right">
                                                <div class="relative">
                                                    <input type="number"
                                                           step="0.001"
                                                           min="0"
                                                           max="{{ $requestedQty }}"
                                                           name="items[{{ $index }}][issued_quantity]"
                                                           value="{{ $defaultIssued }}"
                                                           required
                                                           class="w-full text-right text-sm font-mono font-bold rounded-lg border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-200">
                                                </div>
                                                @error("items.{$index}.issued_quantity")
                                                    <span class="text-[11px] text-red-600 block mt-1 text-left font-medium">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td class="py-3.5 px-4 text-gray-600 text-xs font-semibold">
                                                {{ $detail->unit }}
                                            </td>
                                            <td class="py-3.5 px-4 text-xs">
                                                @if ($currentStock <= 0)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">
                                                        Stok Kosong
                                                    </span>
                                                @elseif ($currentStock < $requestedQty)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                                        Stok Kurang
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                        Stok Aman
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Catatan Verifikasi & Aksi --}}
                        <div class="p-6 bg-gray-50 border-t border-gray-100 space-y-4">
                            <div>
                                <label for="notes" class="block text-xs font-semibold text-gray-700 mb-1">
                                    Catatan Verifikasi Gudang (Opsional)
                                </label>
                                <textarea name="notes"
                                          id="notes"
                                          rows="2"
                                          placeholder="Tambahkan catatan khusus mengenai serah terima bahan, kondisi fisik, atau penyesuaian jumlah..."
                                          class="w-full text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-200">{{ old('notes') }}</textarea>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                                <a href="{{ url()->previous() ?: route('material-requests.index') }}"
                                   class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-50 transition">
                                    &larr; Kembali ke Daftar Permintaan
                                </a>

                                <button type="submit"
                                        onclick="return confirm('Apakah Anda yakin data jumlah serah terima sudah sesuai? Stok bahan baku akan otomatis berkurang dan dicatat sebagai transaksi stok keluar (Stok OUT).');"
                                        class="inline-flex items-center px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-sm transition">
                                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Konfirmasi Verifikasi & Catat Stok OUT
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    {{-- Read-only Table untuk Produksi / Owner (jika Pending) atau Semua Role (jika Approved) --}}
                    @if ($materialRequest->isPending())
                        <div class="p-4 bg-amber-50 border-b border-amber-200 flex items-center justify-between">
                            <div class="flex items-center gap-2 text-xs text-amber-900 font-semibold">
                                <svg class="w-4 h-4 text-amber-600 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                    <circle cx="10" cy="10" r="6" />
                                </svg>
                                <span>Permintaan ini telah diajukan ke Gudang dan sedang menunggu verifikasi fisik oleh petugas Gudang.</span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead class="bg-gray-50/80 text-gray-600 text-xs uppercase tracking-wider font-semibold">
                                    <tr>
                                        <th class="py-3 px-4 text-left">Bahan Baku</th>
                                        <th class="py-3 px-4 text-right">Jumlah Diminta</th>
                                        <th class="py-3 px-4 text-right">Stok Gudang</th>
                                        <th class="py-3 px-4 text-left">Satuan</th>
                                        <th class="py-3 px-4 text-center">Status Stok</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-gray-800">
                                    @foreach ($materialRequest->details as $detail)
                                        @php
                                            $requestedQty = (float) $detail->requested_quantity;
                                            $currentStock = (float) ($detail->ingredient?->current_stock ?? 0);
                                        @endphp
                                        <tr class="hover:bg-gray-50/50 transition">
                                            <td class="py-3.5 px-4 font-semibold text-gray-900">
                                                {{ $detail->ingredient?->name ?? 'Bahan #' . $detail->ingredient_id }}
                                                <div class="text-[11px] text-gray-400 font-normal">Kategori: {{ $detail->ingredient?->category ?? 'Umum' }}</div>
                                            </td>
                                            <td class="py-3.5 px-4 text-right font-mono font-bold text-gray-900">
                                                {{ number_format($requestedQty, 2) }} {{ $detail->unit }}
                                            </td>
                                            <td class="py-3.5 px-4 text-right font-mono text-gray-600">
                                                {{ number_format($currentStock, 2) }} {{ $detail->unit }}
                                            </td>
                                            <td class="py-3.5 px-4 text-gray-600 text-xs font-semibold">
                                                {{ $detail->unit }}
                                            </td>
                                            <td class="py-3.5 px-4 text-center">
                                                @if ($currentStock <= 0)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">
                                                        Stok Kosong
                                                    </span>
                                                @elseif ($currentStock < $requestedQty)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                                        Stok Kurang
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
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
                        {{-- Read-only Table untuk Permintaan yang sudah Diverifikasi (Approved) --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead class="bg-gray-50/80 text-gray-600 text-xs uppercase tracking-wider font-semibold">
                                    <tr>
                                        <th class="py-3 px-4 text-left">Bahan Baku</th>
                                        <th class="py-3 px-4 text-right">Jumlah Diminta</th>
                                        <th class="py-3 px-4 text-right">Jumlah Diserahkan (Issued)</th>
                                        <th class="py-3 px-4 text-left">Satuan</th>
                                        <th class="py-3 px-4 text-center">Status Pemenuhan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-gray-800">
                                    @foreach ($materialRequest->details as $detail)
                                        @php
                                            $requestedQty = (float) $detail->requested_quantity;
                                            $issuedQty = (float) $detail->issued_quantity;
                                            $fulfilled = $issuedQty >= $requestedQty;
                                        @endphp
                                        <tr class="hover:bg-gray-50/50 transition">
                                            <td class="py-3.5 px-4 font-semibold text-gray-900">
                                                {{ $detail->ingredient?->name ?? 'Bahan #' . $detail->ingredient_id }}
                                                <div class="text-[11px] text-gray-400 font-normal">Kategori: {{ $detail->ingredient?->category ?? 'Umum' }}</div>
                                            </td>
                                            <td class="py-3.5 px-4 text-right font-mono text-gray-600">
                                                {{ number_format($requestedQty, 2) }} {{ $detail->unit }}
                                            </td>
                                            <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-700">
                                                {{ number_format($issuedQty, 2) }} {{ $detail->unit }}
                                            </td>
                                            <td class="py-3.5 px-4 text-gray-600 text-xs font-semibold">
                                                {{ $detail->unit }}
                                            </td>
                                            <td class="py-3.5 px-4 text-center">
                                                @if ($fulfilled)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                        Terpenuhi Penuh (100%)
                                                    </span>
                                                @elseif ($issuedQty > 0)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                                        Terpenuhi Sebagian ({{ round(($issuedQty / max(1, $requestedQty)) * 100) }}%)
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">
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

                    {{-- Riwayat Transaksi Kartu Stok Keluar Terkait --}}
                    @if ($materialRequest->stockTransactions->isNotEmpty())
                        <div class="p-6 border-t border-gray-100 bg-gray-50/60">
                            <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Riwayat Transaksi Kartu Stok Keluar (Stok OUT - BR-05)
                            </h4>
                            <div class="overflow-x-auto bg-white rounded-lg border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-100 text-xs">
                                    <thead class="bg-gray-50 text-gray-600 font-semibold uppercase">
                                        <tr>
                                            <th class="py-2.5 px-3 text-left">Bahan Baku</th>
                                            <th class="py-2.5 px-3 text-center">Tipe Transaksi</th>
                                            <th class="py-2.5 px-3 text-right">Jumlah Keluar</th>
                                            <th class="py-2.5 px-3 text-left">Tanggal</th>
                                            <th class="py-2.5 px-3 text-left">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 font-medium">
                                        @foreach ($materialRequest->stockTransactions as $tx)
                                            <tr>
                                                <td class="py-2.5 px-3 font-semibold text-gray-900">
                                                    {{ $tx->ingredient?->name }}
                                                </td>
                                                <td class="py-2.5 px-3 text-center">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800 uppercase">
                                                        {{ $tx->transaction_type }}
                                                    </span>
                                                </td>
                                                <td class="py-2.5 px-3 text-right font-mono font-bold text-red-700">
                                                    -{{ number_format((float) $tx->quantity, 2) }} {{ $tx->ingredient?->unit }}
                                                </td>
                                                <td class="py-2.5 px-3 text-gray-600">
                                                    {{ $tx->transaction_date ? \Carbon\Carbon::parse($tx->transaction_date)->format('d/m/Y') : '-' }}
                                                </td>
                                                <td class="py-2.5 px-3 text-gray-600">
                                                    {{ $tx->notes ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="p-6 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                        <a href="{{ url()->previous() ?: route('material-requests.index') }}"
                           class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-50 transition">
                            &larr; Kembali ke Daftar Permintaan
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
