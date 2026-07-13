<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class AlatController extends Controller
{
    public function index()
    {
        $alat = Alat::withCount('detailTransaksi')
            ->orderByDesc('is_active')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.alat.index', compact('alat'));
    }

    public function create()
    {
        return view('admin.alat.form');
    }

    public function store(Request $request)
    {
        $validated = $this->validateAlat($request);
        $fotoAlat = null;

        try {
            if ($request->hasFile('foto_alat')) {
                $fotoAlat = $request
                    ->file('foto_alat')
                    ->store('foto-alat', 'public');
            }

            DB::transaction(function () use (
                $validated,
                $fotoAlat
            ) {
                Alat::create([
                    'nama_alat' => $validated['nama_alat'],
                    'kategori' => $validated['kategori'],
                    'stok_total' => (int) $validated['stok_total'],
                    'stok_tersedia' => (int) $validated['stok_total'],
                    'harga_per_hari' => $validated['harga_per_hari'],
                    'kondisi' => $validated['kondisi'],
                    'deskripsi' => $validated['deskripsi'] ?? null,
                    'foto_alat' => $fotoAlat,
                    'is_active' => true,
                ]);
            });
        } catch (Throwable $exception) {
            if ($fotoAlat) {
                Storage::disk('public')->delete($fotoAlat);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.alat.index')
            ->with('success', 'Alat berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $alat = Alat::findOrFail($id);

        return view('admin.alat.form', compact('alat'));
    }

    public function update(Request $request, $id)
    {
        $validated = $this->validateAlat($request);

        $fotoBaru = $request->hasFile('foto_alat')
            ? $request->file('foto_alat')->store('foto-alat', 'public')
            : null;

        $fotoLama = null;

        try {
            DB::transaction(function () use (
                $id,
                $validated,
                $fotoBaru,
                &$fotoLama
            ) {
                $alat = Alat::lockForUpdate()->findOrFail($id);

                $jumlahSedangDisewa = max(
                    0,
                    (int) $alat->stok_total - (int) $alat->stok_tersedia
                );

                $stokTotalBaru = (int) $validated['stok_total'];

                if ($stokTotalBaru < $jumlahSedangDisewa) {
                    throw ValidationException::withMessages([
                        'stok_total' =>
                            "Stok total tidak boleh kurang dari {$jumlahSedangDisewa} unit karena jumlah tersebut sedang disewa.",
                    ]);
                }

                $data = [
                    'nama_alat' => $validated['nama_alat'],
                    'kategori' => $validated['kategori'],
                    'stok_total' => $stokTotalBaru,
                    'stok_tersedia' =>
                        $stokTotalBaru - $jumlahSedangDisewa,
                    'harga_per_hari' => $validated['harga_per_hari'],
                    'kondisi' => $validated['kondisi'],
                    'deskripsi' => $validated['deskripsi'] ?? null,
                ];

                if ($fotoBaru) {
                    $fotoLama = $alat->foto_alat;
                    $data['foto_alat'] = $fotoBaru;
                }

                $alat->update($data);
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
            ->route('admin.alat.index')
            ->with('success', 'Data alat berhasil diperbarui.');
    }

    public function toggleStatus($id)
    {
        $alat = DB::transaction(function () use ($id) {
            $alat = Alat::lockForUpdate()->findOrFail($id);

            $alat->update([
                'is_active' => !$alat->is_active,
            ]);

            return $alat;
        });

        $pesan = $alat->is_active
            ? 'Alat berhasil diaktifkan dan dapat disewa kembali.'
            : 'Alat berhasil dinonaktifkan dan tidak tampil di katalog customer.';

        return redirect()
            ->route('admin.alat.index')
            ->with('success', $pesan);
    }

    public function destroy($id)
    {
        $fotoAlat = null;
        $dinonaktifkan = false;

        DB::transaction(function () use (
            $id,
            &$fotoAlat,
            &$dinonaktifkan
        ) {
            $alat = Alat::lockForUpdate()->findOrFail($id);

            if ($alat->detailTransaksi()->exists()) {
                $alat->update([
                    'is_active' => false,
                ]);

                $dinonaktifkan = true;

                return;
            }

            $fotoAlat = $alat->foto_alat;
            $alat->delete();
        });

        if ($fotoAlat) {
            Storage::disk('public')->delete($fotoAlat);
        }

        if ($dinonaktifkan) {
            return redirect()
                ->route('admin.alat.index')
                ->with(
                    'success',
                    'Alat memiliki riwayat transaksi sehingga tidak dihapus permanen. Alat sudah dinonaktifkan.'
                );
        }

        return redirect()
            ->route('admin.alat.index')
            ->with('success', 'Alat berhasil dihapus permanen.');
    }

    private function validateAlat(Request $request): array
    {
        return $request->validate([
            'nama_alat' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:255'],
            'stok_total' => ['required', 'integer', 'min:1'],
            'harga_per_hari' => ['required', 'numeric', 'min:0'],
            'kondisi' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'foto_alat' => [
                'bail',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:2048',
                'dimensions:max_width=6000,max_height=6000',
            ],
        ]);
    }
}