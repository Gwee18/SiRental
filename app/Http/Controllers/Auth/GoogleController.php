<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $customer = Customer::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($customer) {
                $customer->update([
                    'google_id'         => $googleUser->getId(),
                    'foto_profil'       => $googleUser->getAvatar(),
                    // Login lewat Google membuktikan email valid,
                    // jadi langsung dianggap verified kalau belum.
                    'email_verified_at' => $customer->email_verified_at ?? now(),
                ]);
            } else {
                $customer = Customer::create([
                    'nama_lengkap'      => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'foto_profil'       => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'password'          => null,
                ]);
            }

            Auth::guard('web')->login($customer);

            return redirect()->route('home');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google login gagal: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return redirect()->route('login')->withErrors([
                'email' => 'Gagal login dengan Google, silakan coba lagi.',
            ]);
        }
    }
}