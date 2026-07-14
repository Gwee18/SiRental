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

class ReturnFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_return_at_end_of_tolerance_has_no_fine(): void
    {
        Carbon::setTestNow('2026-07-15 12:00:00');

        [$admin, $alat, $transaksi, $detail] =
            $this->createActiveTransaction(
                dueAt: '2026-07-15 10:00:00'
            );

        $response = $this
            ->actingAs($admin, 'admin')
            ->post(
                route(
                    'admin.transaksi.selesai',
                    $transaksi->id
                ),
                [
                    'source' => 'pengembalian',
                    'barang_dikembalikan' => [
                        $detail->id,
                    ],
                ]
            );

        $response
            ->assertRedirect(
                route('admin.pengembalian.index')
            )
            ->assertSessionHas('success');

        $transaksi->refresh();

        $this->assertSame(
            'selesai',
            $transaksi->status
        );
        $this->assertSame(
            Transaksi::PEMBAYARAN_LUNAS,
            $transaksi->status_pembayaran
        );
        $this->assertSame(
            0,
            $transaksi->total_denda
        );
        $this->assertSame(
            $transaksi->total_harga,
            $transaksi->total_dibayar
        );
        $this->assertNull(
            $transaksi->denda_dibayar_pada
        );
        $this->assertSame(
            5,
            $alat->fresh()->stok_tersedia
        );
        $this->assertDatabaseCount('denda', 0);
    }

    public function test_late_return_requires_fine_payment_confirmation(): void
    {
        Carbon::setTestNow('2026-07-15 12:01:00');

        [$admin, $alat, $transaksi, $detail] =
            $this->createActiveTransaction(
                dueAt: '2026-07-15 10:00:00'
            );

        $response = $this
            ->from(
                route(
                    'admin.pengembalian.detail',
                    $transaksi->kode_transaksi
                )
            )
            ->actingAs($admin, 'admin')
            ->post(
                route(
                    'admin.transaksi.selesai',
                    $transaksi->id
                ),
                [
                    'source' => 'pengembalian',
                    'barang_dikembalikan' => [
                        $detail->id,
                    ],
                ]
            );

        $response
            ->assertRedirect(
                route(
                    'admin.pengembalian.detail',
                    $transaksi->kode_transaksi
                )
            )
            ->assertSessionHas('error');

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
            0,
            $transaksi->total_denda
        );
        $this->assertSame(
            3,
            $alat->fresh()->stok_tersedia
        );
        $this->assertDatabaseCount('denda', 0);
    }

    public function test_one_minute_after_tolerance_charges_one_fine_period(): void
    {
        Carbon::setTestNow('2026-07-15 12:01:00');

        [$admin, $alat, $transaksi, $detail] =
            $this->createActiveTransaction(
                dueAt: '2026-07-15 10:00:00'
            );

        $response = $this
            ->actingAs($admin, 'admin')
            ->post(
                route(
                    'admin.transaksi.selesai',
                    $transaksi->id
                ),
                [
                    'source' => 'pengembalian',
                    'barang_dikembalikan' => [
                        $detail->id,
                    ],
                    'konfirmasi_pembayaran_denda' => 1,
                ]
            );

        $response
            ->assertRedirect(
                route('admin.pengembalian.index')
            )
            ->assertSessionHas('success');

        $transaksi->refresh();

        $this->assertSame(
            'selesai',
            $transaksi->status
        );
        $this->assertSame(
            Transaksi::PEMBAYARAN_LUNAS,
            $transaksi->status_pembayaran
        );
        $this->assertSame(
            100000,
            $transaksi->total_denda
        );
        $this->assertSame(
            300000,
            $transaksi->total_dibayar
        );
        $this->assertNotNull(
            $transaksi->denda_dibayar_pada
        );
        $this->assertSame(
            5,
            $alat->fresh()->stok_tersedia
        );

        $this->assertDatabaseHas('denda', [
            'transaksi_id' => $transaksi->id,
            'hari_terlambat' => 1,
            'denda_per_hari' => 100000,
            'total_denda' => 100000,
        ]);
    }

    public function test_more_than_one_late_day_charges_two_fine_periods(): void
    {
        Carbon::setTestNow('2026-07-16 12:01:00');

        [$admin, $alat, $transaksi, $detail] =
            $this->createActiveTransaction(
                dueAt: '2026-07-15 10:00:00'
            );

        $response = $this
            ->actingAs($admin, 'admin')
            ->post(
                route(
                    'admin.transaksi.selesai',
                    $transaksi->id
                ),
                [
                    'source' => 'pengembalian',
                    'barang_dikembalikan' => [
                        $detail->id,
                    ],
                    'konfirmasi_pembayaran_denda' => 1,
                ]
            );

        $response->assertSessionHas('success');

        $transaksi->refresh();

        $this->assertSame(
            200000,
            $transaksi->total_denda
        );
        $this->assertSame(
            400000,
            $transaksi->total_dibayar
        );
        $this->assertSame(
            5,
            $alat->fresh()->stok_tersedia
        );

        $this->assertDatabaseHas('denda', [
            'transaksi_id' => $transaksi->id,
            'hari_terlambat' => 2,
            'denda_per_hari' => 100000,
            'total_denda' => 200000,
        ]);
    }

    public function test_all_items_must_be_checked_before_return_is_completed(): void
    {
        Carbon::setTestNow('2026-07-14 10:00:00');

        [$admin, $alat, $transaksi] =
            $this->createActiveTransaction(
                dueAt: '2026-07-15 10:00:00'
            );

        $response = $this
            ->from(
                route(
                    'admin.pengembalian.detail',
                    $transaksi->kode_transaksi
                )
            )
            ->actingAs($admin, 'admin')
            ->post(
                route(
                    'admin.transaksi.selesai',
                    $transaksi->id
                ),
                [
                    'source' => 'pengembalian',
                    'barang_dikembalikan' => [],
                ]
            );

        $response
            ->assertRedirect(
                route(
                    'admin.pengembalian.detail',
                    $transaksi->kode_transaksi
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

    public function test_completed_return_cannot_restore_stock_twice(): void
    {
        Carbon::setTestNow('2026-07-14 10:00:00');

        [$admin, $alat, $transaksi, $detail] =
            $this->createActiveTransaction(
                dueAt: '2026-07-15 10:00:00'
            );

        $payload = [
            'source' => 'pengembalian',
            'barang_dikembalikan' => [
                $detail->id,
            ],
        ];

        $this
            ->actingAs($admin, 'admin')
            ->post(
                route(
                    'admin.transaksi.selesai',
                    $transaksi->id
                ),
                $payload
            )
            ->assertSessionHas('success');

        $this->assertSame(
            5,
            $alat->fresh()->stok_tersedia
        );

        $response = $this
            ->from(
                route('admin.pengembalian.index')
            )
            ->actingAs($admin, 'admin')
            ->post(
                route(
                    'admin.transaksi.selesai',
                    $transaksi->id
                ),
                $payload
            );

        $response
            ->assertRedirect(
                route('admin.pengembalian.index')
            )
            ->assertSessionHas('error');

        $this->assertSame(
            'selesai',
            $transaksi->fresh()->status
        );
        $this->assertSame(
            5,
            $alat->fresh()->stok_tersedia
        );
    }

    public function test_unpaid_active_transaction_cannot_be_completed(): void
    {
        Carbon::setTestNow('2026-07-14 10:00:00');

        [$admin, $alat, $transaksi, $detail] =
            $this->createActiveTransaction(
                dueAt: '2026-07-15 10:00:00'
            );

        $transaksi->update([
            'status_pembayaran' =>
                Transaksi::PEMBAYARAN_BELUM_BAYAR,
            'total_dibayar' => 0,
            'dibayar_pada' => null,
        ]);

        $response = $this
            ->from(
                route(
                    'admin.pengembalian.detail',
                    $transaksi->kode_transaksi
                )
            )
            ->actingAs($admin, 'admin')
            ->post(
                route(
                    'admin.transaksi.selesai',
                    $transaksi->id
                ),
                [
                    'source' => 'pengembalian',
                    'barang_dikembalikan' => [
                        $detail->id,
                    ],
                ]
            );

        $response->assertSessionHas('error');

        $this->assertSame(
            'aktif',
            $transaksi->fresh()->status
        );
        $this->assertSame(
            3,
            $alat->fresh()->stok_tersedia
        );
    }

    private function createActiveTransaction(
        string $dueAt
    ): array {
        $admin = Admin::create([
            'nama' => 'Admin Test',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $customer = Customer::create([
            'nama_lengkap' => 'Customer Test',
            'email' => 'customer@example.com',
            'email_verified_at' => now(),
            'password' => null,
        ]);

        $alat = Alat::create([
            'nama_alat' => 'Tenda Camping',
            'kategori' => 'Tenda',
            'stok_total' => 5,
            'stok_tersedia' => 3,
            'harga_per_hari' => 50000,
            'kondisi' => 'baik',
            'deskripsi' => 'Alat pengujian.',
            'foto_alat' => null,
            'is_active' => true,
        ]);

        $tanggalSelesai = Carbon::parse($dueAt);
        $tanggalMulai = $tanggalSelesai
            ->copy()
            ->subHours(48);

        $transaksi = Transaksi::create([
            'customer_id' => $customer->id,
            'kode_transaksi' => 'SR-RETURN01',
            'status' => 'aktif',
            'status_pembayaran' =>
                Transaksi::PEMBAYARAN_SEWA_LUNAS,
            'total_harga' => 200000,
            'total_denda' => 0,
            'total_dibayar' => 200000,
            'tanggal_pesan' =>
                $tanggalMulai->toDateString(),
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'dibayar_pada' => $tanggalMulai,
            'denda_dibayar_pada' => null,
            'catatan' => null,
            'foto_ktp' => 'foto-ktp/testing.jpg',
        ]);

        $detail = DetailTransaksi::create([
            'transaksi_id' => $transaksi->id,
            'alat_id' => $alat->id,
            'foto_barang' =>
                'foto-barang/testing.jpg',
            'jumlah' => 2,
            'lama_sewa' => 2,
            'harga_satuan' => 50000,
            'subtotal' => 200000,
        ]);

        return [
            $admin,
            $alat,
            $transaksi,
            $detail,
        ];
    }
}