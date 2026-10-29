<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-h2 text-ink leading-tight">
                {{ __('Dashboard Admin / Staff Operasional') }}
            </h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                Role: {{ Auth::user()->role->label() }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-card rounded-card p-6">
                <h3 class="text-h3 font-bold text-ink mb-2">Selamat datang, {{ Auth::user()->name }}!</h3>
                <p class="text-muted">Anda login sebagai <strong>Admin / Staff Operasional</strong>. Sesuai PRD FoodFlow, Anda memiliki hak akses untuk:</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    <div class="border border-blue-200 rounded-card p-4 bg-blue-50/50 flex flex-col justify-between">
                        <div>
                            <div class="font-semibold text-blue-900 mb-1">Pengelolaan Pengguna</div>
                            <p class="text-body text-muted">Kelola akun pengguna, status akun (Aktif/Tidak Aktif), dan hak akses.</p>
                        </div>
                        <div class="mt-4 pt-2 border-t border-blue-100">
                            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-xs font-semibold text-blue-700 hover:text-blue-900">
                                Buka Kelola Pengguna &rarr;
                            </a>
                        </div>
                    </div>
                    <div class="border border-blue-200 rounded-card p-4 bg-blue-50/50">
                        <div class="font-semibold text-blue-900 mb-1">Master Data & Pesanan</div>
                        <p class="text-body text-muted">Kelola supplier, pelanggan, menu, dan pencatatan pesanan catering.</p>
                    </div>
                    <div class="border border-blue-200 rounded-card p-4 bg-blue-50/50">
                        <div class="font-semibold text-blue-900 mb-1">Pengadaan Bahan</div>
                        <p class="text-sm text-gray-600">Membuat catatan pengadaan bahan baku ke supplier saat stok menipis.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
