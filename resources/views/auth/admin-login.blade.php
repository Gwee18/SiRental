<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-[#00372c]">Admin Panel</h1>
        <p class="text-gray-500 text-sm mt-1">Masuk untuk mengelola SiRental</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 font-medium text-sm text-red-600 bg-red-50 rounded-lg px-4 py-3">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Masuk sebagai Admin') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>