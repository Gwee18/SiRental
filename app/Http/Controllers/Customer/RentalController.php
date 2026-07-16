<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class RentalController extends Controller
{
    public function index(): View
    {
        return view('customer.rental.form', [
            'alat' => Alat::tersedia()->orderBy('nama_alat')->get(),
            'customer' => Auth::guard('web')->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $customer = Auth::guard('web')->user();
        $requestToken = (string) $request->input('request_token');

        if ($requestToken !== '') {
            $existing = $this->findExistingTransaction($customer->id, $requestToken);

            if ($existing) {
                return redirect()->route('customer.transaksi.show', $existing->id);
            }
        }

        $validator = Validator::make($request->all(), $this->rules(), $this->messages());

        $validator->after(function ($validator) use ($request): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $alatIds = $request->input('alat_id', []);
            $jumlah = $request->input('jumlah', []);
            $foto = $request->file('foto_barang', []);

            if (count($alatIds) !== count($jumlah) || count($alatIds) !== count($foto)) {
                $validator->errors()->add('alat_id', 'Data barang, jumlah, dan foto barang tidak lengkap.');

                return;
            }

            $alat = Alat::whereIn('id', $alatIds)->get()->keyBy('id');

            foreach ($alatIds as $index => $alatId) {
                $item = $alat->get((int) $alatId);
                $jumlahDiminta = (int) ($jumlah[$index] ?? 0);

                if (! $item || ! $item->is_active) {
                    $validator->errors()->add('alat_id', 'Salah satu barang sudah tidak tersedia.');

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
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('form_step', $this->errorStep($validator->errors()->keys()));
        }

        $validated = $validator->validated();
        $storedFiles = [];

        try {
            $transaksi = DB::transaction(function () use ($request, $validated, $customer, &$storedFiles) {
                $alatIds = collect($validated['alat_id'])->map(fn ($id) => (int) $id)->values();
                $jumlah = collect($validated['jumlah'])->map(fn ($value) => (int) $value)->values();
                $lamaSewa = (int) $validated['lama_sewa'];
                $alat = $this->lockTools($alatIds, $jumlah);
                $totalHarga = $this->calculateTotal($alatIds, $jumlah, $alat, $lamaSewa);

                $transaksi = Transaksi::create([
                    'customer_id' => $customer->id,
                    'request_token' => $validated['request_token'],
                    'nama_peminjam' => $validated['nama_lengkap'],
                    'email_peminjam' => $customer->email,
                    'no_telp_peminjam' => $validated['no_telp'],
                    'alamat_peminjam' => $validated['alamat'],
                    'kode_transaksi' => 'SR-'.strtoupper(Str::random(10)),
                    'status' => 'menunggu',
                    'status_pembayaran' => Transaksi::PEMBAYARAN_BELUM_BAYAR,
                    'total_harga' => $totalHarga,
                    'total_denda' => 0,
                    'total_dibayar' => 0,
                    'tanggal_pesan' => now()->toDateString(),
                    'catatan' => $validated['catatan'] ?? null,
                ]);

                $fotoKtp = $request->file('foto_ktp')->store('foto-ktp', 'public');
                $storedFiles[] = $fotoKtp;
                $transaksi->update(['foto_ktp' => $fotoKtp]);

                foreach ($alatIds as $index => $alatId) {
                    $item = $alat->get($alatId);
                    $fotoBarang = $request->file('foto_barang')[$index]->store('foto-barang', 'public');
                    $storedFiles[] = $fotoBarang;
                    $subtotal = (int) $item->harga_per_hari * $jumlah[$index] * $lamaSewa;

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'alat_id' => $alatId,
                        'foto_barang' => $fotoBarang,
                        'jumlah' => $jumlah[$index],
                        'lama_sewa' => $lamaSewa,
                        'harga_satuan' => $item->harga_per_hari,
                        'subtotal' => $subtotal,
                    ]);
                }

                return $transaksi;
            });
        } catch (QueryException $exception) {
            $this->deleteFiles($storedFiles);
            $existing = $this->findExistingTransaction($customer->id, $requestToken);

            if ($existing) {
                return redirect()->route('customer.transaksi.show', $existing->id);
            }

            throw $exception;
        } catch (Throwable $exception) {
            $this->deleteFiles($storedFiles);

            throw $exception;
        }

        return redirect()->route('customer.transaksi.show', $transaksi->id);
    }

    private function rules(): array
    {
        return [
            'request_token' => ['required', 'uuid'],
            'alat_id' => ['required', 'array', 'min:1'],
            'alat_id.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('alat', 'id')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'jumlah' => ['required', 'array', 'min:1'],
            'jumlah.*' => ['required', 'integer', 'min:1'],
            'lama_sewa' => ['required', 'integer', 'between:1,7'],
            'foto_barang' => ['required', 'array', 'min:1'],
            'foto_barang.*' => $this->imageRules(),
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'no_telp' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string', 'max:1000'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'foto_ktp' => $this->imageRules(),
        ];
    }

    private function imageRules(): array
    {
        return [
            'bail',
            'required',
            'image',
            'mimes:jpg,jpeg,jfif,png,webp',
            'mimetypes:image/jpeg,image/png,image/webp',
            'max:2048',
            'dimensions:max_width=6000,max_height=6000',
        ];
    }

    private function messages(): array
    {
        return [
            'request_token.required' => 'Sesi formulir tidak valid. Muat ulang halaman dan coba kembali.',
            'request_token.uuid' => 'Sesi formulir tidak valid. Muat ulang halaman dan coba kembali.',
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
            'lama_sewa.between' => 'Lama sewa harus antara 1 sampai 7 hari.',
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
            'alamat.max' => 'Alamat lengkap maksimal 1000 karakter.',
            'catatan.max' => 'Catatan maksimal 1000 karakter.',
            'foto_ktp.required' => 'Foto KTP wajib diupload.',
            'foto_ktp.image' => 'Foto KTP harus berupa gambar yang valid.',
            'foto_ktp.mimes' => 'Foto KTP harus berformat JPG, JPEG, JFIF, PNG, atau WEBP.',
            'foto_ktp.mimetypes' => 'Tipe file foto KTP tidak valid.',
            'foto_ktp.max' => 'Ukuran foto KTP maksimal 2MB.',
            'foto_ktp.dimensions' => 'Dimensi foto KTP maksimal 6000 × 6000 piksel.',
        ];
    }

    private function errorStep(array $keys): int
    {
        foreach ($keys as $key) {
            if (Str::startsWith($key, ['nama_lengkap', 'no_telp', 'alamat', 'catatan', 'foto_ktp'])) {
                return 2;
            }
        }

        return 1;
    }

    private function lockTools(Collection $alatIds, Collection $jumlah): Collection
    {
        $alat = Alat::whereIn('id', $alatIds)->lockForUpdate()->get()->keyBy('id');

        foreach ($alatIds as $index => $alatId) {
            $item = $alat->get($alatId);

            if (! $item || ! $item->is_active) {
                throw ValidationException::withMessages([
                    'alat_id' => 'Salah satu barang sudah dinonaktifkan. Silakan pilih ulang barang rental.',
                ]);
            }

            if ($item->stok_tersedia < $jumlah[$index]) {
                throw ValidationException::withMessages([
                    'stok' => "Stok {$item->nama_alat} tidak mencukupi. Stok tersedia hanya {$item->stok_tersedia}.",
                ]);
            }
        }

        return $alat;
    }

    private function calculateTotal(Collection $alatIds, Collection $jumlah, Collection $alat, int $lamaSewa): int
    {
        return $alatIds
            ->map(function ($alatId, $index) use ($jumlah, $alat, $lamaSewa): int {
                return (int) $alat->get($alatId)->harga_per_hari
                    * $jumlah[$index]
                    * $lamaSewa;
            })
            ->sum();
    }

    private function findExistingTransaction(int $customerId, string $requestToken): ?Transaksi
    {
        return Transaksi::where('customer_id', $customerId)
            ->where('request_token', $requestToken)
            ->first();
    }

    private function deleteFiles(array $paths): void
    {
        if ($paths !== []) {
            Storage::disk('public')->delete($paths);
        }
    }
}
