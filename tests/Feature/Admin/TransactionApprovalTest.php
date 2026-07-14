<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Alat;
use App\Models\Customer;
use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_admin_can_approve_pending_transaction(): void
    {
        Carbon::setTestNow('2026-07-13 10:00:00');

        $admin = $this->createAdmin();
        $alat = $this->createAlat();
        $transaksi = $this->createTransaction(
            $alat,
            jumlah: 2,
            lamaSewa: 2
        );

        $response = $this
            ->actingAs($admin, 'admin')
            ->post(
                route(
                    'admin.transaksi.approve',
                    $transaksi->id
                )
            );

        $response
            ->assertRedirect(
                route(
                    'admin.transaksi.show',
                    $transaksi->id
                )
            )
            ->assertSessionHas('success');

        $transaksi->refresh();

        $this->assertSame(
            'aktif',
            $transaksi->status
        );
        $this->assertSame(
            Transaksi::PEMBAYARAN_SEWA_LUNAS,
            $transaksi->status_pembayaran
        );
        $this->assertSame(
            200000,
            $transaksi->total_dibayar
        );
        $this->assertNotNull(
            $transaksi->dibayar_pada
        );
        $this->assertNotNull(
            $transaksi->tanggal_mulai
        );
        $this->assertNotNull(
            $transaksi->tanggal_selesai
        );

        $this->assertTrue(
            $transaksi->tanggal_mulai->equalTo(
                Carbon::parse('2026-07-13 10:00:00')
            )
        );

        $this->assertTrue(
            $transaksi->tanggal_selesai->equalTo(
                Carbon::parse('2026-07-15 10:00:00')
            )
        );

        $this->assertSame(
            3,
            $alat->fresh()->stok_tersedia
        );
    }

    public function test_same_transaction_cannot_be_approved_twice(): void
    {
        $admin = $this->createAdmin();
        $alat = $this->createAlat();
        $transaksi = $this->createTransaction(
            $alat,
            jumlah: 2
        );

        $this
            ->actingAs($admin, 'admin')
            ->post(
                route(
                    'admin.transaksi.approve',
                    $transaksi->id
                )
            )
            ->assertSessionHas('success');

        $this->assertSame(
            3,
            $alat->fresh()->stok_tersedia
        );

        $response = $this
            ->actingAs($admin, 'admin')
            ->post(
                route(
                    'admin.transaksi.approve',
                    $transaksi->id
                )
            );

        $response
            ->assertRedirect(
                route(
                    'admin.transaksi.show',
                    $transaksi->id
                )
            )
            ->assertSessionHas('error');

        $this->assertSame(
            'aktif',
            $transaksi->fresh()->status
        );
        $this->assertSame(
            3,
            $alat->fresh()->stok_tersedia
        );
    }

    public function test_inactive_tool_prevents_transaction_approval(): void
    {
        $admin = $this->createAdmin();
        $alat = $this->createAlat([
            'is_active' => false,
        ]);
        $transaksi = $this->createTransaction($alat);

        $response = $this
            ->actingAs($admin, 'admin')
            ->post(
                route(
                    'admin.transaksi.approve',
                    $transaksi->id
                )
            );

        $response
            ->assertRedirect(
                route(
                    'admin.transaksi.show',
                    $transaksi->id
                )
            )
            ->assertSessionHas('error');

        $transaksi->refresh();

        $this->assertSame(
            'menunggu',
            $transaksi->status
        );
        $this->assertSame(
            Transaksi::PEMBAYARAN_BELUM_BAYAR,
            $transaksi->status_pembayaran
        );
        $this->assertSame(
            5,
            $alat->fresh()->stok_tersedia
        );
    }

    public function test_insufficient_stock_prevents_transaction_approval(): void
    {
        $admin = $this->createAdmin();
        $alat = $this->createAlat([
            'stok_total' => 1,
            'stok_tersedia' => 1,
        ]);
        $transaksi = $this->createTransaction(
            $alat,
            jumlah: 2
        );

        $response = $this
            ->actingAs($admin, 'admin')
            ->post(
                route(
                    'admin.transaksi.approve',
                    $transaksi->id
                )
            );

        $response
            ->assertRedirect(
                route(
                    'admin.transaksi.show',
                    $transaksi->id
                )
            )
            ->assertSessionHas('error');

        $this->assertSame(
            'menunggu',
            $transaksi->fresh()->status
        );
        $this->assertSame(
            1,
            $alat->fresh()->stok_tersedia
        );
    }

    public function test_admin_can_reject_pending_transaction_without_changing_stock(): void
    {
        $admin = $this->createAdmin();
        $alat = $this->createAlat();
        $transaksi = $this->createTransaction(
            $alat,
            jumlah: 2
        );

        $response = $this
            ->actingAs($admin, 'admin')
            ->post(
                route(
                    'admin.transaksi.tolak',
                    $transaksi->id
                )
            );

        $response
            ->assertRedirect(
                route(
                    'admin.transaksi.show',
                    $transaksi->id
                )
            )
            ->assertSessionHas('success');

        $transaksi->refresh();

        $this->assertSame(
            'ditolak',
            $transaksi->status
        );
        $this->assertSame(
            Transaksi::PEMBAYARAN_BELUM_BAYAR,
            $transaksi->status_pembayaran
        );
        $this->assertSame(
            0,
            $transaksi->total_dibayar
        );
        $this->assertNull(
            $transaksi->dibayar_pada
        );
        $this->assertSame(
            5,
            $alat->fresh()->stok_tersedia
        );
    }

    public function test_active_transaction_cannot_be_rejected(): void
    {
        $admin = $this->createAdmin();
        $alat = $this->createAlat();
        $transaksi = $this->createTransaction(
            $alat,
            status: 'aktif'
        );

        $response = $this
            ->actingAs($admin, 'admin')
            ->post(
                route(
                    'admin.transaksi.tolak',
                    $transaksi->id
                )
            );

        $response
            ->assertRedirect(
                route(
                    'admin.transaksi.show',
                    $transaksi->id
                )
            )
            ->assertSessionHas('error');

        $this->assertSame(
            'aktif',
            $transaksi->fresh()->status
        );
        $this->assertSame(
            5,
            $alat->fresh()->stok_tersedia
        );
    }

    private function createAdmin(): Admin
    {
        return Admin::create([
            'nama' => 'Admin Test',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);
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
            'deskripsi' => 'Alat pengujian.',
            'foto_alat' => null,
            'is_active' => true,
        ], $attributes));
    }

    private function createTransaction(
        Alat $alat,
        int $jumlah = 1,
        int $lamaSewa = 2,
        string $status = 'menunggu'
    ): Transaksi {
        $customer = $this->createCustomer();

        $totalHarga =
            (int) $alat->harga_per_hari *
            $jumlah *
            $lamaSewa;

        $transaksi = Transaksi::create([
            'customer_id' => $customer->id,
            'kode_transaksi' => 'SR-' . strtoupper(
                fake()->unique()->bothify('????????')
            ),
            'status' => $status,
            'status_pembayaran' =>
                $status === 'aktif'
                    ? Transaksi::PEMBAYARAN_SEWA_LUNAS
                    : Transaksi::PEMBAYARAN_BELUM_BAYAR,
            'total_harga' => $totalHarga,
            'total_denda' => 0,
            'total_dibayar' =>
                $status === 'aktif'
                    ? $totalHarga
                    : 0,
            'tanggal_pesan' => now()->toDateString(),
            'tanggal_mulai' =>
                $status === 'aktif'
                    ? now()
                    : null,
            'tanggal_selesai' =>
                $status === 'aktif'
                    ? now()->addDays($lamaSewa)
                    : null,
            'dibayar_pada' =>
                $status === 'aktif'
                    ? now()
                    : null,
            'denda_dibayar_pada' => null,
            'catatan' => null,
            'foto_ktp' => 'foto-ktp/testing.jpg',
        ]);

        DetailTransaksi::create([
            'transaksi_id' => $transaksi->id,
            'alat_id' => $alat->id,
            'foto_barang' =>
                'foto-barang/testing.jpg',
            'jumlah' => $jumlah,
            'lama_sewa' => $lamaSewa,
            'harga_satuan' =>
                (int) $alat->harga_per_hari,
            'subtotal' => $totalHarga,
        ]);

        return $transaksi;
    }
}