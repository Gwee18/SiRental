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
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.admin-login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $email = Str::lower(trim($validated['email']));
        $throttleKey = $this->throttleKey(
            $email,
            $request->ip()
        );

        if (
            RateLimiter::tooManyAttempts(
                $throttleKey,
                self::MAX_LOGIN_ATTEMPTS
            )
        ) {
            $seconds = RateLimiter::availableIn(
                $throttleKey
            );

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
                ]);
        }

        $credentials = [
            'email' => $email,
            'password' => $validated['password'],
        ];

        if (
            Auth::guard('admin')->attempt(
                $credentials,
                $request->boolean('remember')
            )
        ) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return redirect()->intended(
                route('admin.dashboard')
            );
        }

        RateLimiter::hit(
            $throttleKey,
            self::LOGIN_DECAY_SECONDS
        );

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'Email atau password salah.',
            ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    private function throttleKey(
        string $email,
        ?string $ip
    ): string {
        return 'admin-login:'.sha1(
            $email.'|'.($ip ?? 'unknown')
        );
    }
}
