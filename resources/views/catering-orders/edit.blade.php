<x-app-layout>
    @php
    $inputClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';
    $currentStatus = old('status', $order->status instanceof \BackedEnum ? $order->status->value : $order->status);
    @endphp

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('catering-orders.index') }}" class="text-muted transition hover:text-primary">Daftar Pesanan</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Edit Pesanan</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">Edit Pesanan: {{ $order->order_number }}</h1>
            </div>
            <p class="mt-1 text-small text-muted">Perbarui pelanggan, tanggal pengiriman, status pesanan, dan catatan khusus.</p>
        </div>

        <a href="{{ route('catering-orders.show', $order) }}" id="btn-back-to-detail"
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
            <p class="mb-2 font-semibold">Terdapat kesalahan pada input formulir:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('catering-orders.update', $order) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Informasi & Status -->
            <x-card class="w-full sm:p-8">
                <div class="border-b border-line pb-4">
                    <h2 class="text-lg font-semibold text-ink">Informasi &amp; Status Pesanan</h2>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <x-input-label for="customer_id">Pelanggan / Institusi Pemesan <span class="text-danger-text">*</span></x-input-label>
                        <select id="customer_id" name="customer_id" class="{{ $inputClass }} mt-1.5">
                            @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id', $order->customer_id) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->phone ?? 'No HP: -' }})
                            </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('customer_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="delivery_date">Tanggal Pengiriman / Acara <span class="text-danger-text">*</span></x-input-label>
                        <input type="date" id="delivery_date" name="delivery_date" required
                            value="{{ old('delivery_date', $order->delivery_date?->format('Y-m-d')) }}"
                            class="{{ $inputClass }} mt-1.5">
                        <x-input-error :messages="$errors->get('delivery_date')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="status">Status Pesanan <span class="text-danger-text">*</span></x-input-label>
                        <select id="status" name="status" class="{{ $inputClass }} mt-1.5">
                            <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>Pending (Menunggu Konfirmasi)</option>
                            <option value="confirmed" {{ $currentStatus === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                            <option value="processing" {{ $currentStatus === 'processing' ? 'selected' : '' }}>Diproses (Produksi)</option>
                            <option value="delivered" {{ $currentStatus === 'delivered' ? 'selected' : '' }}>Selesai (Terkirim)</option>
                            <option value="cancelled" {{ $currentStatus === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="notes" value="Catatan Khusus / Instruksi Tambahan" />
                        <x-textarea name="notes" id="notes" rows="3" placeholder="Contoh: Pengiriman sebelum jam 11 siang, kemasan ramah lingkungan, sambal dipisah..." class="mt-1.5 block w-full">{{ old('notes', $order->notes) }}</x-textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                    </div>
                </div>
            </x-card>

            <!-- Rincian menu (hanya baca) -->
            <x-card :padding="false" class="overflow-hidden">
                <div class="border-b border-line px-6 py-5">
                    <h2 class="text-lg font-semibold text-ink">Rincian Menu Hidangan Saat Ini</h2>
                    <p class="mt-0.5 text-small text-muted">Rincian menu tidak dapat diubah dari halaman ini.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-body">
                        <thead class="bg-primary/5">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Menu</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Porsi</th>
                                <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Harga</th>
                                <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line bg-white">
                            @foreach ($order->details as $item)
                            <tr>
                                <td class="px-6 py-4 font-medium text-ink">{{ $item->product?->name ?? 'Produk #' . $item->product_id }}</td>
                                <td class="px-6 py-4 text-ink">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-right text-ink">Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-medium text-ink">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t border-line bg-primary/5">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-label uppercase text-muted">Total Pembayaran</td>
                                <td class="px-6 py-4 text-right text-lg font-semibold text-primary">{{ $order->formattedTotal() }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-card>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3">
                <x-button variant="outline" :href="route('catering-orders.show', $order)">Batal</x-button>
                <x-button type="submit" id="btn-submit-order-edit">Simpan Perubahan</x-button>
            </div>
        </form>
    </div>
</x-app-layout>