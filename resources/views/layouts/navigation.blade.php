<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('*.dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('ingredients.index')" :active="request()->routeIs('ingredients.*')">
                        {{ __('Bahan Baku') }}
                    </x-nav-link>

                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                        {{ __('Menu Makanan') }}
                    </x-nav-link>

                    <x-nav-link :href="route('catering-orders.index')" :active="request()->routeIs('catering-orders.*')">
                        {{ __('Pesanan Katering') }}
                    </x-nav-link>

                    @if (in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                        <x-nav-link :href="route('production-plans.index')" :active="request()->routeIs('production-plans.*')">
                            {{ __('Rencana Produksi') }}
                        </x-nav-link>
                    @endif

                    @if (in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                        <x-nav-link :href="route('productions.index')" :active="request()->routeIs('productions.*') || request()->routeIs('production-plans.realization.*')">
                            {{ __('Realisasi Masak') }}
                        </x-nav-link>
                    @endif

                    @if (in_array(Auth::user()->role?->value, ['admin', 'gudang', 'owner'], true))
                        <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                            {{ __('Supplier') }}
                        </x-nav-link>
                    @endif

                    @if (in_array(Auth::user()->role?->value, ['admin', 'gudang', 'produksi'], true))
                        <x-nav-link :href="route('material-requests.index')" :active="request()->routeIs('material-requests.*')">
                            {{ __('Permintaan Bahan') }}
                        </x-nav-link>
                    @endif

                    @if (Auth::user()->isAdmin())
                        <x-nav-link :href="route('purchase-orders.index')" :active="request()->routeIs('purchase-orders.*')">
                            {{ __('Purchase Order') }}
                        </x-nav-link>
                    @endif

                    @if (in_array(Auth::user()->role?->value, ['admin', 'owner'], true))
                        <x-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                            {{ __('Pelanggan') }}
                        </x-nav-link>
                    @endif

                    @if (Auth::user()->isAdmin())
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Kelola Pengguna') }}
                        </x-nav-link>

                        <x-nav-link :href="route('admin.password-resets.index')" :active="request()->routeIs('admin.password-resets.*')">
                            {{ __('Permintaan Reset PW') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center space-x-2">
                                <span>{{ Auth::user()->name }}</span>
                                <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-semibold uppercase">
                                    {{ Auth::user()->role?->value ?? 'User' }}
                                </span>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
                                                    this.closest('form').submit();
                                                }">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('*.dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('ingredients.index')" :active="request()->routeIs('ingredients.*')">
                {{ __('Bahan Baku') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                {{ __('Menu Makanan') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('catering-orders.index')" :active="request()->routeIs('catering-orders.*')">
                {{ __('Pesanan Katering') }}
            </x-responsive-nav-link>

            @if (in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                <x-responsive-nav-link :href="route('production-plans.index')" :active="request()->routeIs('production-plans.*')">
                    {{ __('Rencana Produksi') }}
                </x-responsive-nav-link>
            @endif

            @if (in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                <x-responsive-nav-link :href="route('productions.index')" :active="request()->routeIs('productions.*') || request()->routeIs('production-plans.realization.*')">
                    {{ __('Realisasi Masak') }}
                </x-responsive-nav-link>
            @endif

            @if (in_array(Auth::user()->role?->value, ['admin', 'gudang', 'owner'], true))
                <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                    {{ __('Supplier') }}
                </x-responsive-nav-link>
            @endif

            @if (in_array(Auth::user()->role?->value, ['admin', 'gudang', 'produksi'], true))
                <x-responsive-nav-link :href="route('material-requests.index')" :active="request()->routeIs('material-requests.*')">
                    {{ __('Permintaan Bahan') }}
                </x-responsive-nav-link>
            @endif

            @if (Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('purchase-orders.index')" :active="request()->routeIs('purchase-orders.*')">
                    {{ __('Purchase Order') }}
                </x-responsive-nav-link>
            @endif

            @if (in_array(Auth::user()->role?->value, ['admin', 'owner'], true))
                <x-responsive-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                    {{ __('Pelanggan') }}
                </x-responsive-nav-link>
            @endif

            @if (Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    {{ __('Kelola Pengguna') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.password-resets.index')" :active="request()->routeIs('admin.password-resets.*')">
                    {{ __('Permintaan Reset PW') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
                                            this.closest('form').submit();
                                        }">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
