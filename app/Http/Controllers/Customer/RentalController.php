<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $request->validate([
            'alat_id'          => 'required|array|min:1',
            'alat_id.*'        => 'required|exists:alat,id',
            'jumlah.*'         => 'required|integer|min:1',
            'lama_sewa'        => 'required|integer|min:1',
            'foto_barang.*'    => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'nama_lengkap'     => 'required|string|max:255',
            'no_telp'          => 'required|string|max:20',
            'alamat'           => 'required|string',
            'foto_ktp'         => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Update data diri customer
        $customer = Auth::guard('web')->user();
        $fotoKtp  = $request->file('foto_ktp')->store('foto-ktp', 'public');

        $customer->update([
            'nama_lengkap' => $request->nama_lengkap,
            'no_telp'      => $request->no_telp,
            'alamat'       => $request->alamat,
            'foto_ktp'     => $fotoKtp,
        ]);

        // Hitung total harga
        $totalHarga = 0;
        $alatList   = [];

        foreach ($request->alat_id as $index => $alatId) {
            $alat       = Alat::findOrFail($alatId);
            $jumlah     = $request->jumlah[$index];
            $lamaSewa   = $request->lama_sewa;
            $subtotal   = $alat->harga_per_hari * $jumlah * $lamaSewa;
            $totalHarga += $subtotal;

            $alatList[] = [
                'alat'      => $alat,
                'jumlah'    => $jumlah,
                'lama_sewa' => $lamaSewa,
                'subtotal'  => $subtotal,
                'foto'      => $index,
            ];
        }

        // Buat transaksi
        $transaksi = Transaksi::create([
            'customer_id'     => $customer->id,
            'kode_transaksi'  => 'SR-' . strtoupper(Str::random(8)),
            'status'          => 'menunggu',
            'total_harga'     => $totalHarga,
            'tanggal_pesan'   => now()->toDateString(),
        ]);

        // Simpan detail transaksi
        foreach ($request->alat_id as $index => $alatId) {
            $alat       = Alat::findOrFail($alatId);
            $jumlah     = $request->jumlah[$index];
            $lamaSewa   = $request->lama_sewa;
            $subtotal   = $alat->harga_per_hari * $jumlah * $lamaSewa;
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

            // Kurangi stok
            $alat->decrement('stok_tersedia', $jumlah);
        }

        return redirect()->route('customer.transaksi.show', $transaksi->id);
    }
}