@props(['product'])

<div x-data="{open: false}" class="relative inline-block text-left">
    <button @click="open = !open" type="button" class="inline-flex items-center rounded-md text-sm font-medium text-gray-600 hover:text-gray-900 focus:outline-none">
        <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 4h.01M6 12h12" />
        </svg>
    </button>
    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
        <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
            <a href="{{ route('products.show', $product) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Detail</a>
            @if (in_array(auth()->user()->role?->value, ['admin', 'produksi'], true))
                <a href="{{ route('products.recipe.edit', $product) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Resep</a>
                <a href="{{ route('products.edit', $product) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Edit</a>
                <form action="{{ route('products.toggle-status', $product) }}" method="POST" class="block px-4 py-2">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full text-left text-sm text-gray-700 hover:bg-gray-100" onclick="return confirm('Apakah Anda yakin ingin mengubah status menu ini?')">
                        {{ $product->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="block px-4 py-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full text-left text-sm text-red-600 hover:bg-gray-100" onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini? Jika menu pernah dipesan, sistem akan menonaktifkannya.')">
                        Hapus
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>