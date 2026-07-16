@extends('layouts.app')

@section('title', 'Masuk')

@section('content')

<section class="min-h-[calc(100svh-76px)] bg-gray-50 pt-24 md:pt-28 pb-12 px-4 sm:px-6 flex items-center justify-center">
    <div class="w-full max-w-[420px]">

        <div class="text-center mb-5">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center">
                <img
                    src="{{ asset('images/logo-sirental-auth.png') }}"
                    alt="SiRental"
                    class="w-[185px] md:w-[210px] h-auto object-contain -mb-5"
                >
            </a>

            <h1 class="text-2xl md:text-[26px] font-bold text-[#00372c] -mt-4 tracking-tight">
                Masuk ke SiRental
            </h1>

            <p class="text-gray-500 text-sm mt-1.5 leading-relaxed">
                Gunakan email atau akun Google untuk melanjutkan.
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6">

            @if(session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-5">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-5">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.send-otp') }}" class="space-y-4" autocomplete="off">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="contoh@email.com"
                        class="w-full h-12 px-4 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#085041]/25 focus:border-[#085041] transition-all"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full h-12 bg-[#085041] hover:bg-[#00372c] text-white font-semibold rounded-xl transition-all duration-200 text-sm"
                >
                    Lanjutkan dengan Email
                </button>
            </form>

            <div class="flex items-center gap-4 my-5">
                <div class="flex-1 h-px bg-gray-100"></div>

                <span class="text-xs text-gray-400 font-medium whitespace-nowrap">
                    atau
                </span>

                <div class="flex-1 h-px bg-gray-100"></div>
            </div>

            <a
                href="{{ route('google.redirect') }}"
                class="w-full h-12 flex items-center justify-center gap-3 border border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition-all duration-200 text-sm"
            >
                <svg width="18" height="18" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>

                Lanjutkan dengan Google
            </a>
        </div>

        <p class="text-center text-xs text-gray-400 mt-5 leading-relaxed">
            Dengan melanjutkan, Anda menyetujui
            <a href="{{ route('terms') }}" class="text-[#085041] hover:text-[#00372c] font-semibold">
                Syarat & Ketentuan
            </a>
            dan
            <a href="{{ route('privacy') }}" class="text-[#085041] hover:text-[#00372c] font-semibold">
                Kebijakan Privasi
            </a>
            SiRental.
        </p>

    </div>
</section>

@endsection
