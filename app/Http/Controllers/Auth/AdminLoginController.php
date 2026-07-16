<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;

    private const LOGIN_DECAY_SECONDS = 60;

    public function showLoginForm(): View|RedirectResponse
    {
        return Auth::guard('admin')->check()
            ? redirect()->route('admin.dashboard')
            : view('auth.admin-login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $email = Str::lower(trim($validated['email']));
        $throttleKey = $this->throttleKey($email, $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_LOGIN_ATTEMPTS)) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Terlalu banyak percobaan login. Coba lagi dalam '
                    .RateLimiter::availableIn($throttleKey).' detik.',
            ]);
        }

        $authenticated = Auth::guard('admin')->attempt([
            'email' => $email,
            'password' => $validated['password'],
        ], $request->boolean('remember'));

        if (! $authenticated) {
            RateLimiter::hit($throttleKey, self::LOGIN_DECAY_SECONDS);

            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Email atau password salah.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0, private',
        ]);
    }

    private function throttleKey(string $email, ?string $ip): string
    {
        return 'admin-login:'.sha1($email.'|'.($ip ?? 'unknown'));
    }
}
