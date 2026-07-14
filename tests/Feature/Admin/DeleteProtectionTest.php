<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Alat;
use App\Models\Customer;
use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeleteProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_without_transaction_history_can_be_deleted(): void
    {

        $admin = $this->createAdmin();
        $customer = $this->createCustomer(
            'tanpa.riwayat@example.com',
            'foto-profil/customer-tanpa-riwayat.jpg'
        );

        Storage::shouldReceive('disk')
            ->once()
            ->with('public')
            ->andReturnSelf();

        Storage::shouldReceive('delete')
            ->once()
            ->with(
                'foto-profil/customer-tanpa-riwayat.jpg'
            )
            ->andReturnTrue();

        $response = $this
            ->actingAs($admin, 'admin')
            ->delete(
                route(
                    'admin.pelanggan.destroy',
                    $customer->id
                )
            );

        $response
            ->assertRedirect(
                route('admin.pelanggan.index')
            )
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id,
        ]);

    }

    public function test_customer_with_transaction_history_cannot_be_deleted(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer(
            'punya.riwayat@example.com'
        );

        $transaksi = $this->createTransaction($customer);

        $response = $this
            ->actingAs($admin, 'admin')
            ->delete(
                route(
                    'admin.pelanggan.destroy',
                    $customer->id
                )
            );

        $response
            ->assertRedirect(
                route('admin.pelanggan.index')
            )
            ->assertSessionHas(
                'error',
                'Pelanggan tidak dapat dihapus karena sudah memiliki riwayat transaksi.'
            );

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
        ]);

        $this->assertDatabaseHas('transaksi', [
            'id' => $transaksi->id,
            'customer_id' => $customer->id,
        ]);
    }

    public function test_tool_without_transaction_history_can_be_deleted_permanently(): void
    {

        $admin = $this->createAdmin();
        $alat = $this->createAlat(
            'foto-alat/alat-tanpa-riwayat.jpg'
        );

        Storage::shouldReceive('disk')
            ->once()
            ->with('public')
            ->andReturnSelf();

        Storage::shouldReceive('delete')
            ->once()
            ->with(
                'foto-alat/alat-tanpa-riwayat.jpg'
            )
            ->andReturnTrue();

        $response = $this
            ->actingAs($admin, 'admin')
            ->delete(
                route(
                    'admin.alat.destroy',
                    $alat->id
                )
            );

        $response
            ->assertRedirect(
                route('admin.alat.index')
            )
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('alat', [
            'id' => $alat->id,
        ]);

    }

    public function test_tool_with_transaction_history_is_deactivated_instead_of_deleted(): void
    {

        $admin = $this->createAdmin();
        $customer = $this->createCustomer(
            'customer.alat@example.com'
        );
        $alat = $this->createAlat(
            'foto-alat/alat-punya-riwayat.jpg'
        );

        $transaksi = $this->createTransaction($customer);
        $detail = $this->createTransactionDetail(
            $transaksi,
            $alat
        );

        Storage::shouldReceive('disk')->never();

        $response = $this
            ->actingAs($admin, 'admin')
            ->delete(
                route(
                    'admin.alat.destroy',
                    $alat->id
                )
            );

        $response
            ->assertRedirect(
                route('admin.alat.index')
            )
            ->assertSessionHas(
                'success',
                'Alat memiliki riwayat transaksi sehingga tidak dihapus permanen. Alat sudah dinonaktifkan.'
            );

        $this->assertDatabaseHas('alat', [
            'id' => $alat->id,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas(
            'detail_transaksi',
            [
                'id' => $detail->id,
                'alat_id' => $alat->id,
                'transaksi_id' => $transaksi->id,
            ]
        );

    }

    public function test_remote_google_profile_photo_does_not_block_customer_deletion(): void
    {

        $admin = $this->createAdmin();
        $customer = $this->createCustomer(
            'google.avatar@example.com',
            'https://example.com/avatar.jpg'
        );

        Storage::shouldReceive('disk')->never();

        $response = $this
            ->actingAs($admin, 'admin')
            ->delete(
                route(
                    'admin.pelanggan.destroy',
                    $customer->id
                )
            );

        $response
            ->assertRedirect(
                route('admin.pelanggan.index')
            )
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id,
        ]);

    }

    private function createAdmin(): Admin
    {
        $admin = new Admin;

        $admin->forceFill([
            'nama' => 'Admin Test',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $admin->save();

        return $admin;
    }

    private function createCustomer(
        string $email,
        ?string $fotoProfil = null
    ): Customer {
        $customer = new Customer;

        $customer->forceFill([
            'nama_lengkap' => 'Customer Test',
            'email' => $email,
            'email_verified_at' => now(),
            'password' => null,
            'foto_profil' => $fotoProfil,
        ]);

        $customer->save();

        return $customer;
    }

    private function createAlat(
        ?string $fotoAlat = null
    ): Alat {
        return Alat::create([
            'nama_alat' => 'Tenda Camping',
            'kategori' => 'Tenda',
            'stok_total' => 5,
            'stok_tersedia' => 5,
            'harga_per_hari' => 50000,
            'kondisi' => 'baik',
            'deskripsi' => 'Alat pengujian.',
            'foto_alat' => $fotoAlat,
            'is_active' => true,
        ]);
    }

    private function createTransaction(
        Customer $customer
    ): Transaksi {
        return Transaksi::create([
            'customer_id' => $customer->id,
            'kode_transaksi' => 'SR-'.strtoupper(
                fake()->unique()->bothify('????????')
            ),
            'status' => 'menunggu',
            'status_pembayaran' => Transaksi::PEMBAYARAN_BELUM_BAYAR,
            'total_harga' => 100000,
            'total_denda' => 0,
            'total_dibayar' => 0,
            'tanggal_pesan' => now()->toDateString(),
            'catatan' => null,
            'foto_ktp' => 'foto-ktp/testing.jpg',
        ]);
    }

    private function createTransactionDetail(
        Transaksi $transaksi,
        Alat $alat
    ): DetailTransaksi {
        return DetailTransaksi::create([
            'transaksi_id' => $transaksi->id,
            'alat_id' => $alat->id,
            'foto_barang' => 'foto-barang/testing.jpg',
            'jumlah' => 1,
            'lama_sewa' => 2,
            'harga_satuan' => 50000,
            'subtotal' => 100000,
        ]);
    }
}
