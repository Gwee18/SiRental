<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Contracts\User as GoogleUser;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Auth::guard('web')->check()
            ? redirect()->route('home')
            : Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('home');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
            $googleId = trim((string) $googleUser->getId());
            $email = Str::lower(trim((string) $googleUser->getEmail()));

            if ($googleId === '' || $email === '') {
                throw ValidationException::withMessages([
                    'email' => 'Akun Google tidak memiliki identitas atau email yang valid.',
                ]);
            }

            $customer = DB::transaction(
                fn (): Customer => $this->resolveCustomer($googleUser, $googleId, $email)
            );

            Auth::guard('web')->login($customer);
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        } catch (ValidationException $exception) {
            return redirect()->route('login')->withErrors($exception->errors());
        } catch (Throwable $exception) {
            Log::error('Google login gagal.', [
                'message' => $exception->getMessage(),
                'exception' => $exception,
            ]);

            return redirect()->route('login')->withErrors([
                'email' => 'Gagal login dengan Google. Silakan coba lagi.',
            ]);
        }
    }

    private function resolveCustomer(GoogleUser $googleUser, string $googleId, string $email): Customer
    {
        $customerByGoogle = Customer::where('google_id', $googleId)->lockForUpdate()->first();
        $customerByEmail = Customer::where('email', $email)->lockForUpdate()->first();

        if ($customerByGoogle && $customerByEmail && ! $customerByGoogle->is($customerByEmail)) {
            throw ValidationException::withMessages([
                'email' => 'Akun Google dan email tersebut terhubung ke dua akun customer yang berbeda. Hubungi admin.',
            ]);
        }

        $customer = $customerByGoogle ?? $customerByEmail;

        if (! $customer) {
            return Customer::create([
                'nama_lengkap' => $googleUser->getName() ?: $this->nameFromEmail($email),
                'email' => $email,
                'google_id' => $googleId,
                'foto_profil' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'password' => null,
            ]);
        }

        if ($customer->google_id && $customer->google_id !== $googleId) {
            throw ValidationException::withMessages([
                'email' => 'Email ini sudah terhubung ke akun Google lain.',
            ]);
        }

        $customer->update([
            'nama_lengkap' => $customer->nama_lengkap ?: ($googleUser->getName() ?: $this->nameFromEmail($email)),
            'email' => $email,
            'google_id' => $googleId,
            'foto_profil' => $googleUser->getAvatar() ?: $customer->foto_profil,
            'email_verified_at' => $customer->email_verified_at ?? now(),
        ]);

        return $customer->fresh();
    }

    private function nameFromEmail(string $email): string
    {
        return Str::title(str_replace(['.', '_', '-'], ' ', explode('@', $email)[0]));
    }
}
