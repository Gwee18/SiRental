@extends('layouts.app')

@section('title', 'Verifikasi OTP')

@section('content')
    <section class="min-h-[calc(100svh-76px)] bg-gray-50 px-4 pb-12 pt-24 sm:px-6 md:pt-28">
        <div class="mx-auto flex min-h-[calc(100svh-160px)] w-full max-w-[420px] items-center justify-center">
            <div class="w-full">
                <div class="mb-5 text-center">
                    <a
                        href="{{ route('home') }}"
                        class="inline-flex items-center justify-center"
                    >
                        <img
                            src="{{ asset('images/logo-sirental-auth.png') }}"
                            alt="SiRental"
                            class="-mb-5 h-auto w-[185px] object-contain md:w-[210px]"
                        >
                    </a>

                    <h1 class="-mt-4 text-2xl font-bold tracking-tight text-[#00372c] md:text-[26px]">
                        Verifikasi Kode OTP
                    </h1>

                    <p class="mt-1.5 text-sm leading-relaxed text-gray-500">
                        Masukkan 6 digit kode yang telah dikirim ke
                    </p>

                    <p class="mt-1 break-all text-sm font-semibold text-[#085041]">
                        {{ $email }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                    @if (session('status'))
                        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('login.verify.post') }}"
                        class="space-y-5"
                        autocomplete="off"
                    >
                        @csrf

                        <div>
                            <label
                                for="code"
                                class="mb-2 block text-sm font-semibold text-gray-700"
                            >
                                Kode verifikasi
                            </label>

                            <input
                                id="code"
                                type="text"
                                name="code"
                                value="{{ old('code') }}"
                                required
                                autofocus
                                inputmode="numeric"
                                pattern="[0-9]{6}"
                                maxlength="6"
                                autocomplete="one-time-code"
                                placeholder="000000"
                                aria-describedby="code-help"
                                class="h-14 w-full rounded-xl border border-gray-200 px-4 text-center text-2xl font-bold tracking-[0.45em] text-gray-900 outline-none transition-all placeholder:text-gray-300 focus:border-[#085041] focus:ring-2 focus:ring-[#085041]/25"
                            >

                            <p
                                id="code-help"
                                class="mt-2 text-center text-xs leading-relaxed text-gray-400"
                            >
                                Kode berlaku selama 10 menit dan hanya dapat digunakan satu kali.
                            </p>
                        </div>

                        <button
                            type="submit"
                            class="h-12 w-full rounded-xl bg-[#085041] text-sm font-semibold text-white transition-colors hover:bg-[#00372c]"
                        >
                            Verifikasi & Masuk
                        </button>
                    </form>

                    <div class="my-5 flex items-center gap-4">
                        <div class="h-px flex-1 bg-gray-100"></div>
                        <span class="whitespace-nowrap text-xs font-medium text-gray-400">
                            Belum menerima kode?
                        </span>
                        <div class="h-px flex-1 bg-gray-100"></div>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('login.resend') }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="h-12 w-full rounded-xl border border-gray-200 text-sm font-semibold text-[#085041] transition-colors hover:border-[#085041]/30 hover:bg-[#085041]/5"
                        >
                            Kirim Ulang Kode
                        </button>
                    </form>

                    <a
                        href="{{ route('login') }}"
                        class="mt-4 flex items-center justify-center gap-2 text-sm font-semibold text-gray-500 transition-colors hover:text-[#085041]"
                    >
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 18l-6-6 6-6"
                            />
                        </svg>

                        Gunakan email lain
                    </a>
                </div>

                <div class="mt-5 flex items-start justify-center gap-2 px-3 text-center text-xs leading-relaxed text-gray-400">
                    <svg
                        class="mt-0.5 h-4 w-4 shrink-0"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 3l7 3v5c0 4.6-2.8 8.4-7 10-4.2-1.6-7-5.4-7-10V6l7-3z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9.5 12l1.7 1.7 3.5-3.7"
                        />
                    </svg>

                    Jangan bagikan kode OTP kepada siapa pun.
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const codeInput = document.getElementById('code');

            if (!codeInput) {
                return;
            }

            codeInput.addEventListener('input', () => {
                codeInput.value = codeInput.value
                    .replace(/\D/g, '')
                    .slice(0, 6);
            });

            codeInput.addEventListener('paste', (event) => {
                event.preventDefault();

                const pastedCode = event.clipboardData
                    .getData('text')
                    .replace(/\D/g, '')
                    .slice(0, 6);

                codeInput.value = pastedCode;
                codeInput.dispatchEvent(new Event('input'));
            });
        });
    </script>
@endpush