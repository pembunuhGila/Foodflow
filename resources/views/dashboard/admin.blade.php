<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Admin / Staff Operasional') }}
            </h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                Role: {{ Auth::user()->role->label() }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Selamat datang, {{ Auth::user()->name }}!</h3>
                <p class="text-gray-600">Anda login sebagai <strong>Admin / Staff Operasional</strong>. Sesuai PRD FoodFlow, Anda memiliki hak akses untuk:</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    <div class="border border-blue-200 rounded-lg p-4 bg-blue-50/50">
                        <div class="font-semibold text-blue-900 mb-1">Pengelolaan Pengguna</div>
                        <p class="text-sm text-gray-600">Kelola akun pengguna, status akun (Aktif/Tidak Aktif), dan hak akses.</p>
                    </div>
                    <div class="border border-blue-200 rounded-lg p-4 bg-blue-50/50">
                        <div class="font-semibold text-blue-900 mb-1">Master Data & Pesanan</div>
                        <p class="text-sm text-gray-600">Kelola supplier, pelanggan, menu, dan pencatatan pesanan catering.</p>
                    </div>
                    <div class="border border-blue-200 rounded-lg p-4 bg-blue-50/50">
                        <div class="font-semibold text-blue-900 mb-1">Pengadaan Bahan</div>
                        <p class="text-sm text-gray-600">Membuat catatan pengadaan bahan baku ke supplier saat stok menipis.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
