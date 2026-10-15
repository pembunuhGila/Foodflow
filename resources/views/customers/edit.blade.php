<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Edit Pelanggan') }}: {{ $customer->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Perbarui data kontak, alamat pengiriman, atau preferensi pesanan (UC-26).</p>
            </div>
            <a href="{{ route('customers.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-8">

                <!-- Alert Validasi -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                        <p class="font-semibold mb-2">Terdapat kesalahan pada formulir:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Pelanggan -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Pelanggan / Sekolah / Instansi <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}" required class="mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <!-- Telepon -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon / WhatsApp PIC</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}" class="mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}" class="mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>

                        <!-- Alamat Pengiriman -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700">Alamat Pengiriman Pesanan Katering</label>
                            <textarea name="address" id="address" rows="3" class="mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('address', $customer->address) }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-1" />
                        </div>

                        <!-- Catatan -->
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Catatan Khusus / Preferensi Menu / Pantangan Alergi</label>
                            <textarea name="notes" id="notes" rows="3" class="mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $customer->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                        </div>

                        <!-- Status Aktif -->
                        <div class="md:col-span-2 flex items-center pt-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" {{ old('is_active', $customer->is_active) ? 'checked' : '' }}>
                                <span class="ms-2 text-sm text-gray-700 font-medium">Pelanggan Aktif Memesan Katering</span>
                            </label>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-between pt-6 border-t">
                        <!-- Tombol Hapus Aman -->
                        <button type="button" onclick="if(confirm('Apakah Anda yakin ingin menghapus data pelanggan ini? (Jika pelanggan sudah memiliki riwayat pesanan katering, statusnya akan otomatis dinonaktifkan tanpa menghapus data riwayat transaksi)')) document.getElementById('delete-customer-form').submit();" class="text-sm font-medium text-red-600 hover:text-red-900 transition">
                            Hapus Pelanggan
                        </button>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('customers.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md transition">
                                Batal
                            </a>
                            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-md shadow-sm transition">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>

                </form>

                <!-- Hidden Delete Form -->
                <form id="delete-customer-form" method="POST" action="{{ route('customers.destroy', $customer) }}" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
