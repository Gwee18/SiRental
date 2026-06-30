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
                    'google_id' => $googleUser->getId(),
                ]);
            } else {
                $customer = Customer::create([
                    'nama_lengkap'      => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'email_verified_at' => now(),
                    'password'          => null,
                ]);
            }

            Auth::guard('web')->login($customer);

            return redirect()->route('home');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal login dengan Google, silakan coba lagi.',
            ]);
        }
    }
}