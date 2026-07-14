<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class RentalController extends Controller
{
    public function index()
    {
        $alat = Alat::tersedia()
            ->orderBy('nama_alat')
            ->get();

        $customer = Auth::guard('web')->user();

        return view(
            'customer.rental.form',
            compact('alat', 'customer')
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'alat_id' => ['required', 'array', 'min:1'],
            'alat_id.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('alat', 'id')->where(
                    fn ($query) => $query->where('is_active', true)
                ),
            ],

            'jumlah' => ['required', 'array', 'min:1'],
            'jumlah.*' => ['required', 'integer', 'min:1'],

            'lama_sewa' => ['required', 'integer', 'min:1', 'max:7'],

            'foto_barang' => ['required', 'array', 'min:1'],
            'foto_barang.*' => [
                'bail',
                'required',
                'image',
                'mimes:jpg,jpeg,jfif,png,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:2048',
                'dimensions:max_width=6000,max_height=6000',
            ],

            'nama_lengkap' => ['required', 'string', 'max:255'],
            'no_telp' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string'],
            'catatan' => ['nullable', 'string', 'max:1000'],

            'simpan_ke_profil' => [
                'nullable',
                'boolean',
            ],

            'foto_ktp' => [
                'bail',
                'required',
                'image',
                'mimes:jpg,jpeg,jfif,png,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:2048',
                'dimensions:max_width=6000,max_height=6000',
            ],
        ], [
            'alat_id.required' => 'Barang rental wajib dipilih.',
            'alat_id.array' => 'Format pilihan barang tidak valid.',
            'alat_id.min' => 'Minimal pilih satu barang rental.',
            'alat_id.*.required' => 'Barang rental wajib dipilih.',
            'alat_id.*.integer' => 'Pilihan barang tidak valid.',
            'alat_id.*.distinct' => 'Barang yang sama tidak boleh dipilih lebih dari satu kali.',
            'alat_id.*.exists' => 'Barang tidak tersedia atau sudah dinonaktifkan.',

            'jumlah.required' => 'Jumlah barang wajib diisi.',
            'jumlah.array' => 'Format jumlah barang tidak valid.',
            'jumlah.min' => 'Minimal ada satu jumlah barang.',
            'jumlah.*.required' => 'Jumlah barang wajib diisi.',
            'jumlah.*.integer' => 'Jumlah barang harus berupa angka.',
            'jumlah.*.min' => 'Jumlah barang minimal 1.',

            'lama_sewa.required' => 'Lama sewa wajib dipilih.',
            'lama_sewa.integer' => 'Lama sewa tidak valid.',
            'lama_sewa.min' => 'Lama sewa minimal 1 hari.',
            'lama_sewa.max' => 'Lama sewa maksimal 7 hari.',

            'foto_barang.required' => 'Foto barang wajib diupload.',
            'foto_barang.array' => 'Format foto barang tidak valid.',
            'foto_barang.min' => 'Minimal upload satu foto barang.',
            'foto_barang.*.required' => 'Foto barang wajib diupload.',
            'foto_barang.*.image' => 'Foto barang harus berupa gambar yang valid.',
            'foto_barang.*.mimes' => 'Foto barang harus berformat JPG, JPEG, JFIF, PNG, atau WEBP.',
            'foto_barang.*.mimetypes' => 'Tipe file foto barang tidak valid.',
            'foto_barang.*.max' => 'Ukuran foto barang maksimal 2MB.',
            'foto_barang.*.dimensions' => 'Dimensi foto barang maksimal 6000 × 6000 piksel.',

            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.string' => 'Nama lengkap tidak valid.',
            'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter.',

            'no_telp.required' => 'Nomor telepon wajib diisi.',
            'no_telp.string' => 'Nomor telepon tidak valid.',
            'no_telp.max' => 'Nomor telepon maksimal 20 karakter.',

            'alamat.required' => 'Alamat lengkap wajib diisi.',
            'alamat.string' => 'Alamat lengkap tidak valid.',

            'catatan.max' => 'Catatan maksimal 1000 karakter.',

            'foto_ktp.required' => 'Foto KTP wajib diupload.',
            'foto_ktp.image' => 'Foto KTP harus berupa gambar yang valid.',
            'foto_ktp.mimes' => 'Foto KTP harus berformat JPG, JPEG, JFIF, PNG, atau WEBP.',
            'foto_ktp.mimetypes' => 'Tipe file foto KTP tidak valid.',
            'foto_ktp.max' => 'Ukuran foto KTP maksimal 2MB.',
            'foto_ktp.dimensions' => 'Dimensi foto KTP maksimal 6000 × 6000 piksel.',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $jumlahAlat = count($request->input('alat_id', []));
            $jumlahInput = count($request->input('jumlah', []));
            $jumlahFoto = count($request->file('foto_barang', []));

            if (
                $jumlahAlat !== $jumlahInput ||
                $jumlahAlat !== $jumlahFoto
            ) {
                $validator->errors()->add(
                    'alat_id',
                    'Data barang, jumlah, dan foto barang tidak lengkap.'
                );

                return;
            }

            $alat = Alat::whereIn(
                    'id',
                    $request->input('alat_id', [])
                )
                ->get()
                ->keyBy('id');

            foreach ($request->input('alat_id', []) as $index => $alatId) {
                $item = $alat->get((int) $alatId);
                $jumlahDiminta = (int) ($request->jumlah[$index] ?? 0);

                if (!$item || !$item->is_active) {
                    $validator->errors()->add(
                        'alat_id',
                        'Salah satu barang sudah tidak tersedia.'
                    );

                    continue;
                }

                if ($item->stok_tersedia < $jumlahDiminta) {
                    $validator->errors()->add(
                        'stok',
                        "Stok {$item->nama_alat} tidak mencukupi. Stok tersedia hanya {$item->stok_tersedia}."
                    );
                }
            }
        });

        if ($validator->fails()) {
            $step = 1;

            foreach ($validator->errors()->keys() as $key) {
                if (
                    Str::startsWith(
                        $key,
                        [
                            'nama_lengkap',
                            'no_telp',
                            'alamat',
                            'catatan',
                            'foto_ktp',
                        ]
                    )
                ) {
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
        $fileTersimpan = [];

        try {
            DB::transaction(function () use (
                $request,
                $customer,
                &$transaksi,
                &$fileTersimpan
            ) {
                $alatIds = collect($request->alat_id)
                    ->map(fn ($id) => (int) $id)
                    ->values();

                $alat = Alat::whereIn('id', $alatIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($alatIds as $index => $alatId) {
                    $item = $alat->get($alatId);
                    $jumlah = (int) $request->jumlah[$index];

                    if (!$item || !$item->is_active) {
                        throw ValidationException::withMessages([
                            'alat_id' =>
                                'Salah satu barang sudah dinonaktifkan. Silakan pilih ulang barang rental.',
                        ]);
                    }

                    if ($item->stok_tersedia < $jumlah) {
                        throw ValidationException::withMessages([
                            'stok' =>
                                "Stok {$item->nama_alat} tidak mencukupi. Stok tersedia hanya {$item->stok_tersedia}.",
                        ]);
                    }
                }

                $fotoKtp = $request
                    ->file('foto_ktp')
                    ->store('foto-ktp', 'public');

                $fileTersimpan[] = $fotoKtp;

                if ($request->boolean('simpan_ke_profil')) {
                    $customer->update([
                        'nama_lengkap' =>
                            $request->nama_lengkap,
                        'no_telp' =>
                            $request->no_telp,
                        'alamat' =>
                            $request->alamat,
                    ]);
                }

                $lamaSewa = (int) $request->lama_sewa;
                $totalHarga = 0;

                foreach ($alatIds as $index => $alatId) {
                    $item = $alat->get($alatId);
                    $jumlah = (int) $request->jumlah[$index];

                    $totalHarga +=
                        (float) $item->harga_per_hari *
                        $jumlah *
                        $lamaSewa;
                }

                $transaksi = Transaksi::create([
                    'customer_id' => $customer->id,
                    'nama_peminjam' =>
                        $request->nama_lengkap,
                    'email_peminjam' =>
                        $customer->email,
                    'no_telp_peminjam' =>
                        $request->no_telp,
                    'alamat_peminjam' =>
                        $request->alamat,
                    'kode_transaksi' =>
                        'SR-' . strtoupper(Str::random(8)),
                    'status' => 'menunggu',
                    'status_pembayaran' => 'belum_bayar',
                    'total_harga' => $totalHarga,
                    'total_denda' => 0,
                    'total_dibayar' => 0,
                    'tanggal_pesan' => now()->toDateString(),
                    'catatan' => $request->catatan,
                    'foto_ktp' => $fotoKtp,
                ]);

                foreach ($alatIds as $index => $alatId) {
                    $item = $alat->get($alatId);
                    $jumlah = (int) $request->jumlah[$index];

                    $subtotal =
                        (float) $item->harga_per_hari *
                        $jumlah *
                        $lamaSewa;

                    $fotoBarang = $request
                        ->file('foto_barang')[$index]
                        ->store('foto-barang', 'public');

                    $fileTersimpan[] = $fotoBarang;

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'alat_id' => $alatId,
                        'foto_barang' => $fotoBarang,
                        'jumlah' => $jumlah,
                        'lama_sewa' => $lamaSewa,
                        'harga_satuan' => $item->harga_per_hari,
                        'subtotal' => $subtotal,
                    ]);
                }
            });
        } catch (Throwable $exception) {
            foreach ($fileTersimpan as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }

        return redirect()
            ->route('customer.transaksi.show', $transaksi->id);
    }
}