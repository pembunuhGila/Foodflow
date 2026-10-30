<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Owner / Manager') }}
            </h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                Role: {{ Auth::user()->role->label() }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Selamat datang, {{ Auth::user()->name }}!</h3>
                <p class="text-gray-600">Anda login sebagai <strong>Owner / Manager</strong>. Sesuai PRD FoodFlow, Anda memiliki hak akses untuk:</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    <div class="border border-purple-200 rounded-lg p-4 bg-purple-50/50">
                        <div class="font-semibold text-purple-900 mb-1">Monitoring Operasional</div>
                        <p class="text-sm text-gray-600">Pantau proses produksi, realisasi hasil, serta status pesanan secara berkala.</p>
                    </div>
                    <div class="border border-purple-200 rounded-lg p-4 bg-purple-50/50">
                        <div class="font-semibold text-purple-900 mb-1">Monitoring Stok & Gizi</div>
                        <p class="text-sm text-gray-600">Pantau ketersediaan bahan, peringatan stok minimum, dan rekap gizi produk.</p>
                    </div>
                    <div class="border border-purple-200 rounded-lg p-4 bg-purple-50/50">
                        <div class="font-semibold text-purple-900 mb-1">Pencetakan Laporan</div>
                        <p class="text-sm text-gray-600">Hak eksklusif untuk mengekspor dan mencetak laporan ringkasan operasional.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
