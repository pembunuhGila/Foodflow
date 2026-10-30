<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                    </span>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Edit Pesanan Katering') }} &mdash; <span class="font-mono text-emerald-700">{{ $order->order_number }}</span>
                    </h2>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Perbarui data pelanggan, tanggal pengiriman, status pesanan, dan catatan khusus.
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('catering-orders.show', $order) }}"
                   id="btn-back-to-detail"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold uppercase tracking-widest rounded-lg transition shadow-sm">
                    &larr; Kembali ke Detail
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Validation Errors Banner -->
            @if ($errors->any())
                <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm" role="alert">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 me-2 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-semibold text-red-800">Terdapat kesalahan pada input formulir:</h3>
                            <ul class="mt-1 text-xs text-red-700 list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('catering-orders.update', $order) }}"
                  class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Card 1: Informasi Pesanan & Status -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-5">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h3 class="text-base font-semibold text-gray-900 flex items-center space-x-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Informasi & Status Pesanan</span>
                        </h3>
                        <span class="text-xs font-mono font-bold text-gray-500 bg-gray-100 px-2.5 py-1 rounded">
                            {{ $order->order_number }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Pelanggan -->
                        <div class="md:col-span-2">
                            <label for="customer_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Pelanggan / Institusi Pemesan <span class="text-red-500">*</span>
                            </label>
                            <select id="customer_id"
                                    name="customer_id"
                                    class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-2xs">
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', $order->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->phone ?? 'No HP: -' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tanggal Pengiriman -->
                        <div>
                            <label for="delivery_date" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Tanggal Pengiriman / Acara <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   id="delivery_date"
                                   name="delivery_date"
                                   value="{{ old('delivery_date', $order->delivery_date?->format('Y-m-d')) }}"
                                   required
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-2xs">
                        </div>

                        <!-- Status Pesanan -->
                        <div>
                            <label for="status" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Status Pesanan <span class="text-red-500">*</span>
                            </label>
                            <select id="status"
                                    name="status"
                                    class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-2xs">
                                <option value="pending" {{ old('status', $order->status) === 'pending' ? 'selected' : '' }}>Pending (Menunggu Konfirmasi)</option>
                                <option value="confirmed" {{ old('status', $order->status) === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                                <option value="processing" {{ old('status', $order->status) === 'processing' ? 'selected' : '' }}>Diproses (Produksi)</option>
                                <option value="delivered" {{ old('status', $order->status) === 'delivered' ? 'selected' : '' }}>Selesai (Terkirim)</option>
                                <option value="cancelled" {{ old('status', $order->status) === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>

                        <!-- Catatan Khusus -->
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Catatan Khusus / Instruksi Tambahan
                            </label>
                            <textarea id="notes"
                                      name="notes"
                                      rows="3"
                                      placeholder="Contoh: Pengiriman sebelum jam 11 siang, kemasan ramah lingkungan, sambal dipisah..."
                                      class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-2xs">{{ old('notes', $order->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Rincian Menu Hidangan (Read Only saat edit info umum) -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                    <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">
                        Rincian Menu Hidangan Saat Ini
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-2.5">Menu</th>
                                    <th class="px-4 py-2.5 text-center">Porsi</th>
                                    <th class="px-4 py-2.5 text-right">Harga</th>
                                    <th class="px-4 py-2.5 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($order->details as $item)
                                    <tr>
                                        <td class="px-4 py-2.5 font-medium text-gray-900">{{ $item->product?->name ?? 'Produk #' . $item->product_id }}</td>
                                        <td class="px-4 py-2.5 text-center font-bold text-gray-800">{{ $item->quantity }}</td>
                                        <td class="px-4 py-2.5 text-right">Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2.5 text-right font-bold text-gray-900">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-emerald-50/50 border-t border-emerald-200">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-700 uppercase text-xs">Total Pembayaran:</td>
                                    <td class="px-4 py-3 text-right font-extrabold text-emerald-700 text-base">{{ $order->formattedTotal() }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Submit Button Bar -->
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <a href="{{ route('catering-orders.show', $order) }}"
                       class="px-5 py-2.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                        Batal
                    </a>
                    <button type="submit"
                            id="btn-submit-order-edit"
                            class="px-6 py-2.5 text-xs font-semibold uppercase tracking-wider text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
