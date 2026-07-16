<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | SiRental</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-gray-100 px-4 py-10 font-sans text-gray-900 antialiased">
    <main class="mx-auto flex min-h-[calc(100vh-5rem)] w-full max-w-md items-center justify-center">
        <div class="w-full rounded-2xl bg-white p-6 shadow-lg sm:p-8">
            <a href="{{ route('home') }}" class="mx-auto mb-6 flex w-fit items-center justify-center">
                <img src="{{ asset('images/logo-sirental-auth.png') }}" alt="SiRental" class="h-auto w-44 object-contain">
            </a>

            <div class="mb-6 text-center">
                <h1 class="text-2xl font-bold text-[#00372c]">Admin Panel</h1>
                <p class="mt-1 text-sm text-gray-500">Masuk untuk mengelola SiRental</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        class="h-12 w-full rounded-xl border border-gray-300 px-4 text-sm outline-none transition focus:border-[#085041] focus:ring-2 focus:ring-[#085041]/20"
                    >
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-gray-700">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="h-12 w-full rounded-xl border border-gray-300 px-4 text-sm outline-none transition focus:border-[#085041] focus:ring-2 focus:ring-[#085041]/20"
                    >
                </div>

                <button type="submit" class="h-12 w-full rounded-xl bg-[#085041] text-sm font-semibold text-white transition hover:bg-[#00372c]">
                    Masuk sebagai Admin
                </button>
            </form>
        </div>
    </main>
</body>
</html>
