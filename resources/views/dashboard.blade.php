@php
    $user = auth()->user();
    $targetRoute = $user ? $user->getDashboardRoute() : 'login';
@endphp

<x-app-layout>
    @section('title', 'Dashboard')

    <div class="flex min-h-[60vh] flex-col items-center justify-center p-6 text-center">
        <div class="mb-4 h-12 w-12 animate-spin rounded-full border-4 border-primary border-t-transparent"></div>
        <h3 class="font-display text-h3 text-ink">Mengalihkan ke Dashboard Kerja...</h3>
        <p class="mt-2 max-w-md text-body text-muted">
            Selamat datang, <span class="font-medium text-ink">{{ $user?->name ?? 'Pengguna' }}</span>. Sistem sedang mengarahkan Anda ke dashboard operasional sesuai hak akses peran Anda.
        </p>
        <div class="mt-6">
            <a href="{{ route($targetRoute) }}" class="inline-flex items-center gap-2 rounded-full bg-primary px-5 py-2.5 text-body font-medium text-white shadow-card transition hover:bg-primary/90">
                <span>Buka Dashboard Sekarang</span>
                <x-icon name="arrow" class="h-4 w-4" />
            </a>
        </div>
    </div>

    <script>
        window.location.href = "{{ route($targetRoute) }}";
    </script>
</x-app-layout>
