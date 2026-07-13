@extends('layouts.app')

@section('title', 'Daftar')

@section('content')

<section class="min-h-screen bg-gray-50 pt-28 md:pt-32 pb-16 px-4 sm:px-6 flex items-center justify-center">
    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center">
                <img
                    src="{{ asset('images/logo-sirental-auth.png') }}"
                    alt="SiRental"
                    class="w-[220px] md:w-[260px] h-auto object-contain -mb-6 md:-mb-8"
                >
            </a>

            <h1 class="text-2xl md:text-[26px] font-bold text-[#00372c] -mt-5 md:-mt-8">
                Buat akun baru
            </h1>

            <p class="text-gray-500 text-sm mt-1">
                Daftar gratis dan mulai rental alat pendakian
            </p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-8">

            {{-- Session Status --}}
            @if(session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-5">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Error --}}
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-5">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('register') }}" class="space-y-5" autocomplete="off">
                @csrf

                {{-- Nama Lengkap --}}
                <div class="space-y-1.5">
                    <label for="nama_lengkap" class="text-sm font-medium text-gray-700">
                        Nama Lengkap
                    </label>

                    <input
                        id="nama_lengkap"
                        type="text"
                        name="nama_lengkap"
                        value="{{ old('nama_lengkap') }}"
                        required
                        autofocus
                        autocomplete="off"
                        placeholder="Masukkan nama lengkap"
                        class="w-full h-12 px-4 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#085041]/30 focus:border-[#085041] transition-all"
                    >
                </div>

                {{-- Nomor HP --}}
                <div class="space-y-1.5">
                    <label for="no_telp" class="text-sm font-medium text-gray-700">
                        Nomor HP
                    </label>

                    <input
                        id="no_telp"
                        type="text"
                        name="no_telp"
                        value="{{ old('no_telp') }}"
                        required
                        inputmode="numeric"
                        autocomplete="off"
                        placeholder="08xxxxxxxxxx"
                        class="w-full h-12 px-4 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#085041]/30 focus:border-[#085041] transition-all"
                    >
                </div>

                {{-- Email --}}
                <div class="space-y-1.5">
                    <label for="email" class="text-sm font-medium text-gray-700">
                        Email
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="off"
                        placeholder="contoh@email.com"
                        class="w-full h-12 px-4 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#085041]/30 focus:border-[#085041] transition-all"
                    >
                </div>

                {{-- Password --}}
                <div class="space-y-1.5">
                    <label for="password" class="text-sm font-medium text-gray-700">
                        Password
                    </label>

                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="Minimal 8 karakter"
                            class="w-full h-12 px-4 pr-12 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#085041]/30 focus:border-[#085041] transition-all"
                        >

                        <button
                            type="button"
                            onclick="togglePassword('password', this)"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                            aria-label="Tampilkan password"
                        >
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Konfirmasi Password --}}
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="text-sm font-medium text-gray-700">
                        Konfirmasi Password
                    </label>

                    <div class="relative">
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Ulangi password"
                            class="w-full h-12 px-4 pr-12 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#085041]/30 focus:border-[#085041] transition-all"
                        >

                        <button
                            type="button"
                            onclick="togglePassword('password_confirmation', this)"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                            aria-label="Tampilkan konfirmasi password"
                        >
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Terms --}}
                <div class="flex items-start gap-2">
                    <input
                        id="terms"
                        type="checkbox"
                        required
                        class="w-4 h-4 mt-0.5 rounded border-gray-300 text-[#085041] focus:ring-[#085041] shrink-0"
                    >

                    <label for="terms" class="text-sm text-gray-600 leading-relaxed">
                        Saya menyetujui
                        <a href="{{ route('terms') }}" class="text-[#085041] hover:text-[#00372c] font-medium transition-colors">
                            Syarat & Ketentuan
                        </a>
                        dan
                        <a href="{{ route('privacy') }}" class="text-[#085041] hover:text-[#00372c] font-medium transition-colors">
                            Kebijakan Privasi
                        </a>
                        SiRental
                    </label>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full h-12 bg-[#085041] hover:bg-[#00372c] text-white font-semibold rounded-xl transition-all duration-200 text-sm"
                >
                    Buat Akun
                </button>
            </form>
        </div>

        {{-- Login Link --}}
        <p class="text-center text-sm text-gray-500 mt-6">
            Sudah punya akun?

            <a href="{{ route('login') }}" class="text-[#085041] hover:text-[#00372c] font-semibold transition-colors">
                Masuk sekarang
            </a>
        </p>

    </div>
</section>

@endsection

@push('scripts')
<script>
    function togglePassword(fieldId, btn) {
        const input = document.getElementById(fieldId);
        const isPassword = input.type === 'password';

        input.type = isPassword ? 'text' : 'password';

        btn.innerHTML = isPassword
            ? `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`
            : `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
    }
</script>
@endpush