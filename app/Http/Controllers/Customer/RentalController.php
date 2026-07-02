<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RentalController extends Controller
{
    public function index()
    {
        $alat = Alat::where('stok_tersedia', '>', 0)->get();

        return view('customer.rental.form', compact('alat'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'alat_id'       => 'required|array|min:1',
            'alat_id.*'     => 'required|exists:alat,id',
            'jumlah'        => 'required|array|min:1',
            'jumlah.*'      => 'required|integer|min:1',
            'lama_sewa'     => 'required|integer|min:1|max:7',

            'foto_barang'   => 'required|array|min:1',
            'foto_barang.*' => 'required|file|mimes:jpg,jpeg,png,webp|max:2048',

            'nama_lengkap'  => 'required|string|max:255',
            'no_telp'       => 'required|string|max:20',
            'alamat'        => 'required|string',

            'foto_ktp'      => 'required|file|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'alat_id.required'       => 'Barang rental wajib dipilih.',
            'alat_id.array'          => 'Format pilihan barang tidak valid.',
            'alat_id.min'            => 'Minimal pilih satu barang rental.',
            'alat_id.*.required'     => 'Barang rental wajib dipilih.',
            'alat_id.*.exists'       => 'Barang yang dipilih tidak ditemukan.',

            'jumlah.required'        => 'Jumlah barang wajib diisi.',
            'jumlah.array'           => 'Format jumlah barang tidak valid.',
            'jumlah.min'             => 'Minimal ada satu jumlah barang.',
            'jumlah.*.required'      => 'Jumlah barang wajib diisi.',
            'jumlah.*.integer'       => 'Jumlah barang harus berupa angka.',
            'jumlah.*.min'           => 'Jumlah barang minimal 1.',

            'lama_sewa.required'     => 'Lama sewa wajib dipilih.',
            'lama_sewa.integer'      => 'Lama sewa tidak valid.',
            'lama_sewa.min'          => 'Lama sewa minimal 1 hari.',
            'lama_sewa.max'          => 'Lama sewa maksimal 7 hari.',

            'foto_barang.required'   => 'Foto barang wajib diupload.',
            'foto_barang.array'      => 'Format foto barang tidak valid.',
            'foto_barang.min'        => 'Minimal upload satu foto barang.',
            'foto_barang.*.required' => 'Foto barang wajib diupload.',
            'foto_barang.*.file'     => 'Foto barang harus berupa file gambar.',
            'foto_barang.*.mimes'    => 'Foto barang harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto_barang.*.max'      => 'Ukuran foto barang maksimal 2MB.',

            'nama_lengkap.required'  => 'Nama lengkap wajib diisi.',
            'nama_lengkap.string'    => 'Nama lengkap tidak valid.',
            'nama_lengkap.max'       => 'Nama lengkap maksimal 255 karakter.',

            'no_telp.required'       => 'Nomor telepon wajib diisi.',
            'no_telp.string'         => 'Nomor telepon tidak valid.',
            'no_telp.max'            => 'Nomor telepon maksimal 20 karakter.',

            'alamat.required'        => 'Alamat lengkap wajib diisi.',
            'alamat.string'          => 'Alamat lengkap tidak valid.',

            'foto_ktp.required'      => 'Foto KTP wajib diupload.',
            'foto_ktp.file'          => 'Foto KTP harus berupa file gambar.',
            'foto_ktp.mimes'         => 'Foto KTP harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto_ktp.max'           => 'Ukuran foto KTP maksimal 2MB.',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $jumlahPerAlat = [];

            foreach ($request->alat_id as $index => $alatId) {
                $jumlah = (int) ($request->jumlah[$index] ?? 0);

                if (!isset($jumlahPerAlat[$alatId])) {
                    $jumlahPerAlat[$alatId] = 0;
                }

                $jumlahPerAlat[$alatId] += $jumlah;
            }

            foreach ($jumlahPerAlat as $alatId => $jumlahDiminta) {
                $alat = Alat::find($alatId);

                if (!$alat) {
                    $validator->errors()->add('alat_id', 'Barang yang dipilih tidak ditemukan.');
                    continue;
                }

                if ($alat->stok_tersedia < $jumlahDiminta) {
                    $validator->errors()->add(
                        'stok',
                        'Stok ' . $alat->nama_alat . ' tidak mencukupi. Stok tersedia hanya ' . $alat->stok_tersedia . '.'
                    );
                }
            }
        });

        if ($validator->fails()) {
            $errorKeys = $validator->errors()->keys();
            $step = 1;

            foreach ($errorKeys as $key) {
                if (Str::startsWith($key, ['nama_lengkap', 'no_telp', 'alamat', 'foto_ktp'])) {
                    $step = 2;
                    break;
                }
            }

            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('form_step', $step);
        }

        $customer = Auth::guard('web')->user();
        $transaksi = null;

        DB::transaction(function () use ($request, $customer, &$transaksi) {
            $fotoKtp = $request->file('foto_ktp')->store('foto-ktp', 'public');

            $customer->update([
                'nama_lengkap' => $request->nama_lengkap,
                'no_telp'      => $request->no_telp,
                'alamat'       => $request->alamat,
                'foto_ktp'     => $fotoKtp,
            ]);

            $totalHarga = 0;

            foreach ($request->alat_id as $index => $alatId) {
                $alat = Alat::findOrFail($alatId);
                $jumlah = (int) $request->jumlah[$index];
                $lamaSewa = (int) $request->lama_sewa;
                $subtotal = $alat->harga_per_hari * $jumlah * $lamaSewa;

                $totalHarga += $subtotal;
            }

            $transaksi = Transaksi::create([
                'customer_id'    => $customer->id,
                'kode_transaksi' => 'SR-' . strtoupper(Str::random(8)),
                'status'         => 'menunggu',
                'total_harga'    => $totalHarga,
                'total_denda'    => 0,
                'tanggal_pesan'  => now()->toDateString(),
            ]);

            foreach ($request->alat_id as $index => $alatId) {
                $alat = Alat::findOrFail($alatId);
                $jumlah = (int) $request->jumlah[$index];
                $lamaSewa = (int) $request->lama_sewa;
                $subtotal = $alat->harga_per_hari * $jumlah * $lamaSewa;
                $fotoBarang = $request->file('foto_barang')[$index]->store('foto-barang', 'public');

                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'alat_id'      => $alatId,
                    'foto_barang'  => $fotoBarang,
                    'jumlah'       => $jumlah,
                    'lama_sewa'    => $lamaSewa,
                    'harga_satuan' => $alat->harga_per_hari,
                    'subtotal'     => $subtotal,
                ]);
            }
        });

        return redirect()->route('customer.transaksi.show', $transaksi->id);
    }
}