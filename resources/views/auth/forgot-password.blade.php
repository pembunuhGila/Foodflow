<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 leading-relaxed">
        {{ __('Lupa kata sandi akun Anda? Masukkan alamat email terdaftar Anda di bawah ini untuk mengajukan permohonan pemulihan kata sandi kepada Administrator sistem. Admin akan memeriksa permohonan Anda dan menyiapkan kata sandi baru.') }}
    </div>

    <!-- Session Status Alert -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Alamat Email Terdaftar')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@foodflow.test" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('login') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                &larr; {{ __('Kembali ke Login') }}
            </a>

            <x-primary-button>
                {{ __('Ajukan Pemulihan') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
