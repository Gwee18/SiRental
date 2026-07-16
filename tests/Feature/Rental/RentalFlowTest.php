<?php

namespace Tests\Feature\Rental;

use App\Models\Alat;
use App\Models\Customer;
use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class RentalFlowTest extends TestCase
{
    use RefreshDatabase;

    private array $publicFilesBefore = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->publicFilesBefore = $this->currentPublicFiles();
    }

    protected function tearDown(): void
    {
        $newFiles = array_values(array_diff($this->currentPublicFiles(), $this->publicFilesBefore));

        if ($newFiles !== []) {
            Storage::disk('public')->delete($newFiles);
        }

        parent::tearDown();
    }

    public function test_customer_can_submit_valid_rental_request(): void
    {
        $customer = $this->createCustomer();
        $alat = $this->createAlat();
        $data = $this->validRentalData($alat);
        $data['jumlah'] = [2];
        $data['lama_sewa'] = 3;
        $data['nama_lengkap'] = 'Customer Transaksi';
        $data['no_telp'] = '081234567890';
        $data['alamat'] = 'Jl. Alamat Transaksi No. 1';
        $data['catatan'] = 'Mohon dicek sebelum disewa.';

        $response = $this
            ->actingAs($customer, 'web')
            ->post(route('rental.store'), $data);

        $response->assertSessionHasNoErrors();

        $transaksi = Transaksi::first();
        $detail = DetailTransaksi::first();

        $this->assertNotNull($transaksi);
        $this->assertNotNull($detail);
        $response->assertRedirect(route('customer.transaksi.show', $transaksi->id));
        $this->assertSame($customer->id, $transaksi->customer_id);
        $this->assertSame($data['request_token'], $transaksi->request_token);
        $this->assertSame('Customer Transaksi', $transaksi->nama_peminjam);
        $this->assertSame('customer@example.com', $transaksi->email_peminjam);
        $this->assertSame('081234567890', $transaksi->no_telp_peminjam);
        $this->assertSame('Jl. Alamat Transaksi No. 1', $transaksi->alamat_peminjam);
        $this->assertSame('menunggu', $transaksi->status);
        $this->assertSame(Transaksi::PEMBAYARAN_BELUM_BAYAR, $transaksi->status_pembayaran);
        $this->assertSame(300000, (int) $transaksi->total_harga);
        $this->assertSame(0, (int) $transaksi->total_denda);
        $this->assertSame(0, (int) $transaksi->total_dibayar);
        $this->assertSame($transaksi->id, $detail->transaksi_id);
        $this->assertSame($alat->id, $detail->alat_id);
        $this->assertSame(2, $detail->jumlah);
        $this->assertSame(3, $detail->lama_sewa);
        $this->assertSame(50000, (int) $detail->harga_satuan);
        $this->assertSame(300000, (int) $detail->subtotal);
        $this->assertSame(5, $alat->fresh()->stok_tersedia);

        $customer->refresh();
        $this->assertSame('Customer Test', $customer->nama_lengkap);
        $this->assertSame('081111111111', $customer->no_telp);
        $this->assertSame('Jl. Profil Lama No. 1', $customer->alamat);

        $customer->update([
            'nama_lengkap' => 'Nama Profil Baru',
            'no_telp' => '089999999999',
            'alamat' => 'Jl. Profil Baru',
        ]);

        $transaksi->refresh();
        $this->assertSame('Customer Transaksi', $transaksi->nama_peminjam);
        $this->assertSame('081234567890', $transaksi->no_telp_peminjam);
        $this->assertSame('Jl. Alamat Transaksi No. 1', $transaksi->alamat_peminjam);
        Storage::disk('public')->assertExists($transaksi->foto_ktp);
        Storage::disk('public')->assertExists($detail->foto_barang);
    }

    public function test_same_request_token_only_creates_one_transaction(): void
    {
        $customer = $this->createCustomer();
        $alat = $this->createAlat();
        $data = $this->validRentalData($alat);

        $firstResponse = $this->actingAs($customer, 'web')->post(route('rental.store'), $data);
        $transaksi = Transaksi::firstOrFail();

        $secondResponse = $this->actingAs($customer, 'web')->post(route('rental.store'), [
            'request_token' => $data['request_token'],
        ]);

        $firstResponse->assertRedirect(route('customer.transaksi.show', $transaksi->id));
        $secondResponse->assertRedirect(route('customer.transaksi.show', $transaksi->id));
        $this->assertDatabaseCount('transaksi', 1);
        $this->assertDatabaseCount('detail_transaksi', 1);
    }

    public function test_inactive_tool_cannot_be_ordered(): void
    {
        $customer = $this->createCustomer();
        $alat = $this->createAlat(['is_active' => false]);

        $response = $this
            ->from(route('rental.index'))
            ->actingAs($customer, 'web')
            ->post(route('rental.store'), $this->validRentalData($alat));

        $response
            ->assertRedirect(route('rental.index'))
            ->assertSessionHasErrors(['alat_id.0' => 'Barang tidak tersedia atau sudah dinonaktifkan.']);
        $this->assertDatabaseCount('transaksi', 0);
        $this->assertDatabaseCount('detail_transaksi', 0);
        $this->assertNoNewPublicFiles();
    }

    public function test_request_exceeding_available_stock_is_rejected(): void
    {
        $customer = $this->createCustomer();
        $alat = $this->createAlat(['stok_total' => 1, 'stok_tersedia' => 1]);
        $data = $this->validRentalData($alat);
        $data['jumlah'] = [2];

        $response = $this
            ->from(route('rental.index'))
            ->actingAs($customer, 'web')
            ->post(route('rental.store'), $data);

        $response->assertRedirect(route('rental.index'))->assertSessionHasErrors(['stok']);
        $this->assertDatabaseCount('transaksi', 0);
        $this->assertDatabaseCount('detail_transaksi', 0);
        $this->assertSame(1, $alat->fresh()->stok_tersedia);
        $this->assertNoNewPublicFiles();
    }

    public function test_image_larger_than_two_megabytes_is_rejected(): void
    {
        $customer = $this->createCustomer();
        $alat = $this->createAlat();
        $data = $this->validRentalData($alat);
        $data['foto_barang'] = [UploadedFile::fake()->image('foto-besar.jpg')->size(2049)];

        $response = $this
            ->from(route('rental.index'))
            ->actingAs($customer, 'web')
            ->post(route('rental.store'), $data);

        $response->assertRedirect(route('rental.index'))->assertSessionHasErrors(['foto_barang.0']);
        $this->assertDatabaseCount('transaksi', 0);
        $this->assertNoNewPublicFiles();
    }

    public function test_unsupported_file_type_is_rejected(): void
    {
        $customer = $this->createCustomer();
        $alat = $this->createAlat();
        $data = $this->validRentalData($alat);
        $data['foto_ktp'] = UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf');

        $response = $this
            ->from(route('rental.index'))
            ->actingAs($customer, 'web')
            ->post(route('rental.store'), $data);

        $response->assertRedirect(route('rental.index'))->assertSessionHasErrors(['foto_ktp']);
        $this->assertDatabaseCount('transaksi', 0);
        $this->assertNoNewPublicFiles();
    }

    private function validRentalData(Alat $alat): array
    {
        return [
            'request_token' => (string) Str::uuid(),
            'alat_id' => [$alat->id],
            'jumlah' => [1],
            'lama_sewa' => 2,
            'foto_barang' => [$this->fakePng('foto-barang.png')],
            'nama_lengkap' => 'Customer Test',
            'no_telp' => '081234567890',
            'alamat' => 'Jl. Testing No. 1',
            'catatan' => null,
            'foto_ktp' => $this->fakePng('foto-ktp.png'),
        ];
    }

    private function fakePng(string $name): UploadedFile
    {
        $content = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9Zz9sAAAAASUVORK5CYII=',
            true
        );

        if ($content === false) {
            throw new \RuntimeException(
                'Gagal membuat file gambar pengujian.'
            );
        }

        return UploadedFile::fake()->createWithContent(
            $name,
            $content
        );
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

    private function createAlat(array $attributes = []): Alat
    {
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

    private function assertNoNewPublicFiles(): void
    {
        $this->assertSame($this->publicFilesBefore, $this->currentPublicFiles());
    }

    private function currentPublicFiles(): array
    {
        $files = Storage::disk('public')->allFiles();
        sort($files);

        return array_values($files);
    }
}
