<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\EmailOtp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class AuthenticatedSessionController extends Controller
{
    private const OTP_EXPIRE_MINUTES = 10;

    private const OTP_RESEND_SECONDS = 60;

    private const MAX_SEND_ATTEMPTS = 3;

    private const SEND_DECAY_SECONDS = 600;

    private const MAX_VERIFY_ATTEMPTS = 5;

    private const VERIFY_DECAY_SECONDS = 600;

    public function create(): View|RedirectResponse
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    public function sendOtp(
        Request $request
    ): RedirectResponse {
        if (Auth::guard('web')->check()) {
            return redirect()->route('home');
        }

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
            ],
        ]);

        $email = Str::lower(
            trim($validated['email'])
        );

        $sendKey = $this->sendThrottleKey(
            $email,
            $request->ip()
        );

        if (
            RateLimiter::tooManyAttempts(
                $sendKey,
                self::MAX_SEND_ATTEMPTS
            )
        ) {
            $seconds = RateLimiter::availableIn(
                $sendKey
            );

            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'Terlalu banyak permintaan kode. '.
                        "Coba lagi dalam {$seconds} detik.",
                ]);
        }

        $latestOtp = EmailOtp::where(
            'email',
            $email
        )
            ->latest('id')
            ->first();

        if (
            $latestOtp &&
            $latestOtp->created_at->gt(
                now()->subSeconds(
                    self::OTP_RESEND_SECONDS
                )
            )
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'Kode sudah dikirim. Tunggu 60 detik '.
                        'sebelum meminta kode baru.',
                ]);
        }

        $code = str_pad(
            (string) random_int(0, 999999),
            6,
            '0',
            STR_PAD_LEFT
        );

        $otp = DB::transaction(
            function () use ($email, $code) {
                EmailOtp::where('email', $email)
                    ->whereNull('used_at')
                    ->delete();

                return EmailOtp::create([
                    'email' => $email,
                    'code_hash' => Hash::make($code),
                    'expires_at' => now()->addMinutes(
                        self::OTP_EXPIRE_MINUTES
                    ),
                    'attempts' => 0,
                ]);
            }
        );

        try {
            Mail::raw(
                'Kode verifikasi SiRental Anda adalah: '.
                "{$code}\n\nKode ini berlaku selama ".
                self::OTP_EXPIRE_MINUTES.
                ' menit. Jangan bagikan kode ini '.
                'kepada siapa pun.',
                function ($message) use ($email): void {
                    $message
                        ->to($email)
                        ->subject(
                            'Kode Verifikasi SiRental'
                        );
                }
            );
        } catch (Throwable $exception) {
            $otp->delete();

            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'Gagal mengirim kode verifikasi. '.
                        'Periksa konfigurasi email aplikasi.',
                ]);
        }

        RateLimiter::hit(
            $sendKey,
            self::SEND_DECAY_SECONDS
        );

        /*
         * Kode baru harus dapat dicoba kembali meskipun kode lama
         * sebelumnya sudah mencapai batas percobaan verifikasi.
         */
        RateLimiter::clear(
            $this->verifyThrottleKey(
                $email,
                $request->ip()
            )
        );

        $request->session()->put(
            'otp_email',
            $email
        );

        return redirect()
            ->route('login.verify')
            ->with(
                'status',
                'Kode verifikasi sudah dikirim ke email Anda.'
            );
    }

    public function showVerifyForm(
        Request $request
    ): View|RedirectResponse {
        if (Auth::guard('web')->check()) {
            return redirect()->route('home');
        }

        if (
            ! $request->session()->has(
                'otp_email'
            )
        ) {
            return redirect()->route('login');
        }

        return view('auth.verify-otp', [
            'email' => $request->session()->get(
                'otp_email'
            ),
        ]);
    }

    public function verifyOtp(
        Request $request
    ): RedirectResponse {
        if (Auth::guard('web')->check()) {
            return redirect()->route('home');
        }

        $validated = $request->validate([
            'code' => [
                'required',
                'digits:6',
            ],
        ]);

        $email = $request->session()->get(
            'otp_email'
        );

        if (! $email) {
            return redirect()->route('login');
        }

        $verifyKey = $this->verifyThrottleKey(
            $email,
            $request->ip()
        );

        if (
            RateLimiter::tooManyAttempts(
                $verifyKey,
                self::MAX_VERIFY_ATTEMPTS
            )
        ) {
            $seconds = RateLimiter::availableIn(
                $verifyKey
            );

            return back()->withErrors([
                'code' => 'Terlalu banyak percobaan. '.
                    "Coba lagi dalam {$seconds} detik ".
                    'atau kirim kode baru.',
            ]);
        }

        $result = DB::transaction(
            function () use (
                $email,
                $validated
            ): array {
                $otp = EmailOtp::where(
                    'email',
                    $email
                )
                    ->whereNull('used_at')
                    ->latest('id')
                    ->lockForUpdate()
                    ->first();

                if (! $otp) {
                    return [
                        'error' => 'Kode verifikasi tidak ditemukan. '.
                            'Silakan kirim ulang kode.',
                    ];
                }

                if ($otp->expires_at->isPast()) {
                    return [
                        'error' => 'Kode verifikasi sudah kedaluwarsa. '.
                            'Silakan kirim ulang kode.',
                    ];
                }

                if (
                    (int) $otp->attempts >=
                    self::MAX_VERIFY_ATTEMPTS
                ) {
                    return [
                        'error' => 'Terlalu banyak percobaan. '.
                            'Silakan kirim ulang kode.',
                    ];
                }

                if (
                    ! Hash::check(
                        $validated['code'],
                        $otp->code_hash
                    )
                ) {
                    $otp->increment('attempts');

                    return [
                        'error' => 'Kode verifikasi tidak sesuai.',
                    ];
                }

                $otp->update([
                    'used_at' => now(),
                ]);

                $customer = Customer::where(
                    'email',
                    $email
                )
                    ->lockForUpdate()
                    ->first();

                if (! $customer) {
                    $customer = Customer::create([
                        'nama_lengkap' => $this->generateNameFromEmail(
                            $email
                        ),
                        'email' => $email,
                        'email_verified_at' => now(),
                        'password' => null,
                    ]);
                } elseif (
                    ! $customer->email_verified_at
                ) {
                    $customer->update([
                        'email_verified_at' => now(),
                    ]);
                }

                return [
                    'customer' => $customer,
                ];
            }
        );

        if (isset($result['error'])) {
            RateLimiter::hit(
                $verifyKey,
                self::VERIFY_DECAY_SECONDS
            );

            return back()->withErrors([
                'code' => $result['error'],
            ]);
        }

        RateLimiter::clear($verifyKey);

        Auth::guard('web')->login(
            $result['customer']
        );

        $request->session()->forget(
            'otp_email'
        );

        $request->session()->regenerate();

        return redirect()->intended(
            route('home')
        );
    }

    public function resendOtp(
        Request $request
    ): RedirectResponse {
        if (Auth::guard('web')->check()) {
            return redirect()->route('home');
        }

        $email = $request->session()->get(
            'otp_email'
        );

        if (! $email) {
            return redirect()->route('login');
        }

        $request->merge([
            'email' => $email,
        ]);

        return $this->sendOtp($request);
    }

    public function destroy(
        Request $request
    ): RedirectResponse {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function sendThrottleKey(
        string $email,
        ?string $ip
    ): string {
        return 'otp-send:'.sha1(
            $email.'|'.($ip ?? 'unknown')
        );
    }

    private function verifyThrottleKey(
        string $email,
        ?string $ip
    ): string {
        return 'otp-verify:'.sha1(
            $email.'|'.($ip ?? 'unknown')
        );
    }

    private function generateNameFromEmail(
        string $email
    ): string {
        $name = explode('@', $email)[0];

        $name = str_replace(
            ['.', '_', '-'],
            ' ',
            $name
        );

        return Str::title($name);
    }
}
