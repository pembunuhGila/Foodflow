<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Staff Produksi / Ahli Gizi') }}
            </h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                Role: {{ Auth::user()->role->label() }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Selamat datang, {{ Auth::user()->name }}!</h3>
                <p class="text-gray-600">Anda login sebagai <strong>Staff Produksi / Ahli Gizi</strong>. Sesuai PRD FoodFlow, Anda memiliki hak akses untuk:</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    <div class="border border-emerald-200 rounded-lg p-4 bg-emerald-50/50">
                        <div class="font-semibold text-emerald-900 mb-1">Rencana Produksi</div>
                        <p class="text-sm text-gray-600">Susun rencana produksi berdasarkan pesanan, tentukan jumlah porsi, dan hitung kebutuhan bahan.</p>
                    </div>
                    <div class="border border-emerald-200 rounded-lg p-4 bg-emerald-50/50">
                        <div class="font-semibold text-emerald-900 mb-1">Permintaan Bahan</div>
                        <p class="text-sm text-gray-600">Ajukan permintaan bahan baku ke gudang sesuai kebutuhan rencana produksi.</p>
                    </div>
                    <div class="border border-emerald-200 rounded-lg p-4 bg-emerald-50/50">
                        <div class="font-semibold text-emerald-900 mb-1">Realisasi & Data Gizi</div>
                        <p class="text-sm text-gray-600">Catat produksi aktual, pemakaian bahan riil, dan kelola informasi kandungan gizi menu.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
