<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggan = Customer::withCount('transaksi')
            ->orderByDesc('created_at')
            ->get();

        return view(
            'admin.pelanggan.index',
            compact('pelanggan')
        );
    }

    public function show($id)
    {
        $pelanggan = Customer::with([
            'transaksi' => function ($query) {
                $query->orderByDesc('created_at');
            },
        ])->findOrFail($id);

        return view(
            'admin.pelanggan.show',
            compact('pelanggan')
        );
    }

    public function destroy($id)
    {
        $fotoProfil = null;
        $fotoKtp = null;

        try {
            DB::transaction(function () use (
                $id,
                &$fotoProfil,
                &$fotoKtp
            ) {
                $pelanggan = Customer::lockForUpdate()
                    ->findOrFail($id);

                if ($pelanggan->transaksi()->exists()) {
                    throw new \RuntimeException(
                        'Pelanggan tidak dapat dihapus karena sudah memiliki riwayat transaksi.'
                    );
                }

                $fotoProfil = $this->localStoragePath(
                    $pelanggan->foto_profil
                );

                $fotoKtp = $this->localStoragePath(
                    $pelanggan->foto_ktp
                );

                $pelanggan->delete();
            });
        } catch (\RuntimeException $exception) {
            return redirect()
                ->route('admin.pelanggan.index')
                ->with('error', $exception->getMessage());
        } catch (QueryException $exception) {
            report($exception);

            return redirect()
                ->route('admin.pelanggan.index')
                ->with(
                    'error',
                    'Pelanggan tidak dapat dihapus karena masih terhubung dengan data transaksi.'
                );
        }

        if ($fotoProfil) {
            Storage::disk('public')->delete($fotoProfil);
        }

        if ($fotoKtp) {
            Storage::disk('public')->delete($fotoKtp);
        }

        return redirect()
            ->route('admin.pelanggan.index')
            ->with(
                'success',
                'Pelanggan tanpa riwayat transaksi berhasil dihapus.'
            );
    }

    private function localStoragePath(
        ?string $path
    ): ?string {
        if (!$path) {
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