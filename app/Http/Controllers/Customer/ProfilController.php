<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ProfilController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('web')->user();

        return view('customer.profil.index', [
            'customer' => $customer,
            'user' => $customer,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => [
                'required',
                'string',
                'max:255',
            ],
            'no_telp' => [
                'nullable',
                'string',
                'max:20',
            ],
            'alamat' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'foto_profil' => [
                'bail',
                'nullable',
                'image',
                'mimes:jpg,jpeg,jfif,png,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:2048',
                'dimensions:max_width=6000,max_height=6000',
            ],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter.',
            'no_telp.max' => 'Nomor telepon maksimal 20 karakter.',
            'alamat.max' => 'Alamat maksimal 1000 karakter.',
            'foto_profil.image' => 'Foto profil harus berupa gambar yang valid.',
            'foto_profil.mimes' => 'Foto profil harus berformat JPG, JPEG, JFIF, PNG, atau WEBP.',
            'foto_profil.mimetypes' => 'Tipe file foto profil tidak valid.',
            'foto_profil.max' => 'Ukuran foto profil maksimal 2MB.',
            'foto_profil.dimensions' => 'Dimensi foto profil maksimal 6000 × 6000 piksel.',
        ]);

        $customer = Auth::guard('web')->user();
        $fotoBaru = null;
        $fotoLama = null;

        try {
            if ($request->hasFile('foto_profil')) {
                $fotoBaru = $request
                    ->file('foto_profil')
                    ->store('foto-profil', 'public');
            }

            DB::transaction(function () use (
                $customer,
                $validated,
                $fotoBaru,
                &$fotoLama
            ) {
                $customer->refresh();

                if ($fotoBaru) {
                    $fotoLama = $this->localStoragePath(
                        $customer->foto_profil
                    );
                }

                $customer->update([
                    'nama_lengkap' => $validated['nama_lengkap'],
                    'no_telp' => $validated['no_telp'] ?? null,
                    'alamat' => $validated['alamat'] ?? null,
                    'foto_profil' => $fotoBaru
                        ?: $customer->foto_profil,
                ]);
            });
        } catch (Throwable $exception) {
            if ($fotoBaru) {
                Storage::disk('public')->delete($fotoBaru);
            }

            throw $exception;
        }

        if ($fotoBaru && $fotoLama) {
            Storage::disk('public')->delete($fotoLama);
        }

        return redirect()
            ->route('customer.profil')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    private function localStoragePath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (
            Str::startsWith(
                $path,
                ['http://', 'https://']
            )
        ) {
            return null;
        }

        return ltrim($path, '/');
    }
}
