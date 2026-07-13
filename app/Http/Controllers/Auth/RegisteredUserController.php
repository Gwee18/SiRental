<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'no_telp'      => ['required', 'string', 'max:20'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:customers,email'],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        Customer::create([
            'nama_lengkap' => $request->nama_lengkap,
            'no_telp'      => $request->no_telp,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with(
            'status',
            'Pendaftaran berhasil. Silakan login menggunakan email dan password yang sudah dibuat.'
        );
    }
}