<?php

namespace Tests\Feature\Rental;

use App\Models\Alat;
use App\Models\Customer;
use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RentalFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_submit_valid_rental_request(): void
    {
        Storage::fake('public');

        $customer = $this->createCustomer();
        $alat = $this->createAlat();

        $response = $this
            ->actingAs($customer, 'web')
            ->post(route('rental.store'), [
                'alat_id' => [$alat->id],
                'jumlah' => [2],
                'lama_sewa' => 3,
                'foto_barang' => [
                    UploadedFile::fake()->image(
                        'foto-barang.jpg'
                    ),
                ],
                'nama_lengkap' => 'Customer Diperbarui',
                'no_telp' => '081234567890',
                'alamat' => 'Jl. Testing No. 1',
                'catatan' => 'Mohon dicek sebelum disewa.',
                'foto_ktp' => UploadedFile::fake()->image(
                    'foto-ktp.jpg'
                ),
            ]);

        $transaksi = Transaksi::query()->first();

        $this->assertNotNull($transaksi);

        $response->assertRedirect(
            route(
                'customer.transaksi.show',
                $transaksi->id
            )
        );

        $this->assertSame(
            $customer->id,
            $transaksi->customer_id
        );
        $this->assertSame('menunggu', $transaksi->status);
        $this->assertSame(
            Transaksi::PEMBAYARAN_BELUM_BAYAR,
            $transaksi->status_pembayaran
        );
        $this->assertSame(
            300000,
            (int) $transaksi->total_harga
        );
        $this->assertSame(0, (int) $transaksi->total_denda);
        $this->assertSame(
            0,
            (int) $transaksi->total_dibayar
        );

        $detail = DetailTransaksi::query()->first();

        $this->assertNotNull($detail);
        $this->assertSame(
            $transaksi->id,
            $detail->transaksi_id
        );
        $this->assertSame($alat->id, $detail->alat_id);
        $this->assertSame(2, $detail->jumlah);
        $this->assertSame(3, $detail->lama_sewa);
        $this->assertSame(
            50000,
            (int) $detail->harga_satuan
        );
        $this->assertSame(
            300000,
            (int) $detail->subtotal
        );

        /*
         * Stok belum berkurang saat customer mengajukan.
         * Stok baru berkurang ketika admin menyetujui.
         */
        $this->assertSame(
            5,
            $alat->fresh()->stok_tersedia
        );

        $customer->refresh();

        $this->assertSame(
            'Customer Diperbarui',
            $customer->nama_lengkap
        );
        $this->assertSame(
            '081234567890',
            $customer->no_telp
        );
        $this->assertSame(
            'Jl. Testing No. 1',
            $customer->alamat
        );

        Storage::disk('public')->assertExists(
            $transaksi->foto_ktp
        );
        Storage::disk('public')->assertExists(
            $detail->foto_barang
        );
    }

    public function test_inactive_tool_cannot_be_ordered(): void
    {
        Storage::fake('public');

        $customer = $this->createCustomer();
        $alat = $this->createAlat([
            'is_active' => false,
        ]);

        $response = $this
            ->from(route('rental.index'))
            ->actingAs($customer, 'web')
            ->post(
                route('rental.store'),
                $this->validRentalData($alat)
            );

        $response
            ->assertRedirect(route('rental.index'))
            ->assertSessionHasErrors([
                'alat_id.0' =>
                    'Barang tidak tersedia atau sudah dinonaktifkan.',
            ]);

        $this->assertDatabaseCount('transaksi', 0);
        $this->assertDatabaseCount(
            'detail_transaksi',
            0
        );
        $this->assertSame(
            [],
            Storage::disk('public')->allFiles()
        );
    }

    public function test_request_exceeding_available_stock_is_rejected(): void
    {
        Storage::fake('public');

        $customer = $this->createCustomer();
        $alat = $this->createAlat([
            'stok_total' => 1,
            'stok_tersedia' => 1,
        ]);

        $data = $this->validRentalData($alat);
        $data['jumlah'] = [2];

        $response = $this
            ->from(route('rental.index'))
            ->actingAs($customer, 'web')
            ->post(route('rental.store'), $data);

        $response
            ->assertRedirect(route('rental.index'))
            ->assertSessionHasErrors([
                'stok',
            ]);

        $this->assertDatabaseCount('transaksi', 0);
        $this->assertDatabaseCount(
            'detail_transaksi',
            0
        );
        $this->assertSame(
            1,
            $alat->fresh()->stok_tersedia
        );
        $this->assertSame(
            [],
            Storage::disk('public')->allFiles()
        );
    }

    public function test_image_larger_than_two_megabytes_is_rejected(): void
    {
        Storage::fake('public');

        $customer = $this->createCustomer();
        $alat = $this->createAlat();

        $data = $this->validRentalData($alat);
        $data['foto_barang'] = [
            UploadedFile::fake()
                ->image('foto-besar.jpg')
                ->size(2049),
        ];

        $response = $this
            ->from(route('rental.index'))
            ->actingAs($customer, 'web')
            ->post(route('rental.store'), $data);

        $response
            ->assertRedirect(route('rental.index'))
            ->assertSessionHasErrors([
                'foto_barang.0',
            ]);

        $this->assertDatabaseCount('transaksi', 0);
        $this->assertSame(
            [],
            Storage::disk('public')->allFiles()
        );
    }

    public function test_unsupported_file_type_is_rejected(): void
    {
        Storage::fake('public');

        $customer = $this->createCustomer();
        $alat = $this->createAlat();

        $data = $this->validRentalData($alat);
        $data['foto_ktp'] = UploadedFile::fake()->create(
            'dokumen.pdf',
            100,
            'application/pdf'
        );

        $response = $this
            ->from(route('rental.index'))
            ->actingAs($customer, 'web')
            ->post(route('rental.store'), $data);

        $response
            ->assertRedirect(route('rental.index'))
            ->assertSessionHasErrors([
                'foto_ktp',
            ]);

        $this->assertDatabaseCount('transaksi', 0);
        $this->assertSame(
            [],
            Storage::disk('public')->allFiles()
        );
    }

    private function validRentalData(Alat $alat): array
    {
        return [
            'alat_id' => [$alat->id],
            'jumlah' => [1],
            'lama_sewa' => 2,
            'foto_barang' => [
                UploadedFile::fake()->image(
                    'foto-barang.jpg'
                ),
            ],
            'nama_lengkap' => 'Customer Test',
            'no_telp' => '081234567890',
            'alamat' => 'Jl. Testing No. 1',
            'catatan' => null,
            'foto_ktp' => UploadedFile::fake()->image(
                'foto-ktp.jpg'
            ),
        ];
    }

    private function createCustomer(): Customer
    {
        return Customer::create([
            'nama_lengkap' => 'Customer Test',
            'email' => 'customer@example.com',
            'email_verified_at' => now(),
            'password' => null,
        ]);
    }

    private function createAlat(
        array $attributes = []
    ): Alat {
        return Alat::create(array_merge([
            'nama_alat' => 'Tenda Camping',
            'kategori' => 'Tenda',
            'stok_total' => 5,
            'stok_tersedia' => 5,
            'harga_per_hari' => 50000,
            'kondisi' => 'baik',
            'deskripsi' => 'Alat untuk pengujian.',
            'foto_alat' => null,
            'is_active' => true,
        ], $attributes));
    }
}