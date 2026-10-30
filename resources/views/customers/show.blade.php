<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Detail Pelanggan') }}: {{ $customer->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Rincian profil pemesan katering, alamat pengantaran, dan status langganan.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('customers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-md uppercase tracking-wider transition">
                    &larr; Kembali
                </a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md uppercase tracking-wider shadow-sm transition">
                        Edit Pelanggan
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Card Utama Profil Pelanggan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-gray-100 gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h3 class="text-2xl font-bold text-gray-900">{{ $customer->name }}</h3>
                            @if ($customer->isActive())
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                    <span class="w-2 h-2 rounded-full bg-green-600"></span>
                                    Langganan Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                    <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                    Tidak Aktif
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            Terdaftar di sistem sejak: {{ $customer->created_at?->format('d F Y, H:i') ?? '-' }}
                        </p>
                    </div>
                </div>

                <!-- Informasi Kontak & Alamat Pengiriman -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6">
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor Telepon / WhatsApp PIC</span>
                        <div class="mt-2 text-base font-semibold text-gray-900">
                            {{ $customer->phone ?: 'Tidak dicantumkan' }}
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Alamat Email</span>
                        <div class="mt-2 text-base font-semibold text-gray-900">
                            {{ $customer->email ?: 'Tidak dicantumkan' }}
                        </div>
                    </div>

                    <div class="md:col-span-2 p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Alamat Pengiriman Pesanan Katering</span>
                        <p class="mt-2 text-sm text-gray-800 leading-relaxed">
                            {{ $customer->address ?: 'Alamat belum dilengkapi.' }}
                        </p>
                    </div>

                    <div class="md:col-span-2 p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Preferensi Pesanan / Catatan Khusus & Alergi</span>
                        <p class="mt-2 text-sm text-gray-800 leading-relaxed">
                            {{ $customer->notes ?: 'Tidak ada preferensi khusus untuk pelanggan ini.' }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
