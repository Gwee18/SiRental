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

    /**
     * Daftar file pada disk public sebelum test dimulai.
     *
     * @var array<int, string>
     */
    private array $publicFilesBefore = [];

    protected function setUp(): void
    {
        parent::setUp();

        /*
         * Jangan gunakan Storage::fake('public') pada Windows karena
         * direktori tetap storage/framework/testing/disks/public dapat
         * terkunci atau kehilangan izin tulis.
         *
         * Test memakai disk public normal, mencatat file awal, lalu
         * hanya menghapus file baru yang dibuat oleh test ini.
         */
        $this->publicFilesBefore =
            $this->currentPublicFiles();
    }

    protected function tearDown(): void
    {
        $newFiles = array_values(array_diff(
            $this->currentPublicFiles(),
            $this->publicFilesBefore
        ));

        if ($newFiles !== []) {
            Storage::disk('public')->delete($newFiles);
        }

        parent::tearDown();
    }

    public function test_customer_can_submit_valid_rental_request(): void
    {
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
                'nama_lengkap' => 'Customer Transaksi',
                'no_telp' => '081234567890',
                'alamat' => 'Jl. Alamat Transaksi No. 1',
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
        $this->assertSame(
            'Customer Transaksi',
            $transaksi->nama_peminjam
        );
        $this->assertSame(
            'customer@example.com',
            $transaksi->email_peminjam
        );
        $this->assertSame(
            '081234567890',
            $transaksi->no_telp_peminjam
        );
        $this->assertSame(
            'Jl. Alamat Transaksi No. 1',
            $transaksi->alamat_peminjam
        );
        $this->assertSame(
            'menunggu',
            $transaksi->status
        );
        $this->assertSame(
            Transaksi::PEMBAYARAN_BELUM_BAYAR,
            $transaksi->status_pembayaran
        );
        $this->assertSame(
            300000,
            (int) $transaksi->total_harga
        );
        $this->assertSame(
            0,
            (int) $transaksi->total_denda
        );
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
        $this->assertSame(
            $alat->id,
            $detail->alat_id
        );
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
         * Stok belum berkurang ketika customer mengajukan.
         * Stok baru berkurang setelah admin menyetujui.
         */
        $this->assertSame(
            5,
            $alat->fresh()->stok_tersedia
        );

        /*
         * Checkbox tidak dicentang, sehingga perubahan data pada
         * form hanya berlaku untuk transaksi dan profil tetap.
         */
        $customer->refresh();

        $this->assertSame(
            'Customer Test',
            $customer->nama_lengkap
        );
        $this->assertSame(
            '081111111111',
            $customer->no_telp
        );
        $this->assertSame(
            'Jl. Profil Lama No. 1',
            $customer->alamat
        );

        /*
         * Perubahan profil setelah transaksi dibuat tidak boleh
         * mengubah snapshot data transaksi lama.
         */
        $customer->update([
            'nama_lengkap' => 'Nama Profil Baru',
            'no_telp' => '089999999999',
            'alamat' => 'Jl. Profil Baru',
        ]);

        $transaksi->refresh();

        $this->assertSame(
            'Customer Transaksi',
            $transaksi->nama_peminjam
        );
        $this->assertSame(
            '081234567890',
            $transaksi->no_telp_peminjam
        );
        $this->assertSame(
            'Jl. Alamat Transaksi No. 1',
            $transaksi->alamat_peminjam
        );

        Storage::disk('public')->assertExists(
            $transaksi->foto_ktp
        );
        Storage::disk('public')->assertExists(
            $detail->foto_barang
        );
    }

    public function test_customer_can_save_rental_data_as_latest_profile(): void
    {
        $customer = $this->createCustomer();
        $alat = $this->createAlat();

        $data = $this->validRentalData($alat);

        $data['nama_lengkap'] =
            'Customer Profil Terbaru';
        $data['no_telp'] =
            '082222222222';
        $data['alamat'] =
            'Jl. Profil Terbaru No. 2';
        $data['simpan_ke_profil'] = 1;

        $response = $this
            ->actingAs($customer, 'web')
            ->post(route('rental.store'), $data);

        $transaksi = Transaksi::query()->first();

        $response->assertRedirect(
            route(
                'customer.transaksi.show',
                $transaksi->id
            )
        );

        $customer->refresh();

        $this->assertSame(
            'Customer Profil Terbaru',
            $customer->nama_lengkap
        );
        $this->assertSame(
            '082222222222',
            $customer->no_telp
        );
        $this->assertSame(
            'Jl. Profil Terbaru No. 2',
            $customer->alamat
        );

        $this->assertSame(
            'Customer Profil Terbaru',
            $transaksi->nama_peminjam
        );
        $this->assertSame(
            'customer@example.com',
            $transaksi->email_peminjam
        );
        $this->assertSame(
            '082222222222',
            $transaksi->no_telp_peminjam
        );
        $this->assertSame(
            'Jl. Profil Terbaru No. 2',
            $transaksi->alamat_peminjam
        );
    }

    public function test_inactive_tool_cannot_be_ordered(): void
    {
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
                'alat_id.0' => 'Barang tidak tersedia atau sudah dinonaktifkan.',
            ]);

        $this->assertDatabaseCount('transaksi', 0);
        $this->assertDatabaseCount(
            'detail_transaksi',
            0
        );
        $this->assertNoNewPublicFiles();
    }

    public function test_request_exceeding_available_stock_is_rejected(): void
    {
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
        $this->assertNoNewPublicFiles();
    }

    public function test_image_larger_than_two_megabytes_is_rejected(): void
    {
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
        $this->assertNoNewPublicFiles();
    }

    public function test_unsupported_file_type_is_rejected(): void
    {
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
        $this->assertNoNewPublicFiles();
    }

    private function assertNoNewPublicFiles(): void
    {
        $this->assertSame(
            $this->publicFilesBefore,
            $this->currentPublicFiles()
        );
    }

    /**
     * Ambil daftar file public dengan urutan konsisten.
     *
     * @return array<int, string>
     */
    private function currentPublicFiles(): array
    {
        $files = Storage::disk('public')->allFiles();

        sort($files);

        return array_values($files);
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
            'no_telp' => '081111111111',
            'alamat' => 'Jl. Profil Lama No. 1',
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
