@php
/**
 * Sidebar component for the Production module & general application navigation.
 *
 * Permanent / static vertical sidebar on the left.
 */
@endphp

<aside class="flex flex-col h-screen w-64 min-w-[260px] bg-[#f8f7ff] border-r border-purple-100/80 fixed top-0 left-0 z-30 select-none">
    {{-- Header / Branding --}}
    <div class="px-6 pt-6 pb-4">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-purple-600 flex items-center justify-center text-white shadow-md shadow-purple-200 group-hover:bg-purple-700 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-900 tracking-tight leading-none group-hover:text-purple-700 transition">FoodFlow</h1>
                <p class="text-xs text-purple-600/80 font-medium mt-1">Versi 0.1</p>
            </div>
        </a>
    </div>

    {{-- Navigation Menu List --}}
    <div class="flex-1 overflow-y-auto px-4 py-2 space-y-6">
        {{-- UTAMA --}}
        <div>
            <h3 class="text-xs font-semibold text-gray-400 tracking-wider uppercase mb-2 px-3">UTAMA</h3>
            <ul class="space-y-1">
                <li>
                    <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard') || request()->routeIs('*.dashboard')">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </x-sidebar-link>
                </li>
            </ul>
        </div>

        {{-- DAPUR / PRODUKSI --}}
        <div>
            <h3 class="text-xs font-semibold text-gray-400 tracking-wider uppercase mb-2 px-3">DAPUR / PRODUKSI</h3>
            <ul class="space-y-1">
                <li>
                    <x-sidebar-link href="{{ route('products.index') }}" :active="request()->routeIs('products.*')">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span>Menu Makanan</span>
                    </x-sidebar-link>
                </li>
                <li>
                    <x-sidebar-link href="{{ route('production-plans.index') }}" :active="request()->routeIs('production-plans.*')">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span>Rencana Produksi</span>
                    </x-sidebar-link>
                </li>
                <li>
                    <x-sidebar-link href="{{ route('productions.index') }}" :active="request()->routeIs('productions.*')">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343a8 8 0 010 11.314z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                        </svg>
                        <span>Realisasi Masak</span>
                    </x-sidebar-link>
                </li>
            </ul>
        </div>

        {{-- PERSEDIAAN & BAHAN --}}
        <div>
            <h3 class="text-xs font-semibold text-gray-400 tracking-wider uppercase mb-2 px-3">PERSEDIAAN & BAHAN</h3>
            <ul class="space-y-1">
                <li>
                    <x-sidebar-link href="{{ route('ingredients.index') }}" :active="request()->routeIs('ingredients.*')">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span>Bahan Baku</span>
                    </x-sidebar-link>
                </li>
                <li>
                    <x-sidebar-link href="{{ route('material-requests.index') }}" :active="request()->routeIs('material-requests.*')">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Permintaan Bahan</span>
                    </x-sidebar-link>
                </li>
            </ul>
        </div>

        {{-- PESANAN --}}
        <div>
            <h3 class="text-xs font-semibold text-gray-400 tracking-wider uppercase mb-2 px-3">PESANAN</h3>
            <ul class="space-y-1">
                <li>
                    <x-sidebar-link href="{{ route('catering-orders.index') }}" :active="request()->routeIs('catering-orders.*')">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>Pesanan Katering</span>
                    </x-sidebar-link>
                </li>
            </ul>
        </div>
    </div>

    {{-- Bottom Sticky Actions --}}
    <div class="px-4 py-4 mt-auto border-t border-purple-100 bg-[#f8f7ff]">
        <ul class="space-y-1">
            <li>
                <x-sidebar-link href="{{ route('profile.edit') }}" :active="request()->routeIs('profile.edit')">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span>Ganti Password</span>
                </x-sidebar-link>
            </li>
            <li>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:text-red-700 hover:bg-red-50/80 transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 shrink-0 text-gray-500 hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</aside>
