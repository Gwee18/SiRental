<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Alat;
use App\Models\Customer;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardReportTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_dashboard_displays_correct_summary_totals(): void
    {
        $admin = $this->createAdmin();

        $this->createCustomer(
            'customer-one@example.com'
        );
        $this->createCustomer(
            'customer-two@example.com'
        );

        $this->createAlat('Tenda');
        $this->createAlat('Carrier', false);

        $this->createTransaksi([
            'status' => 'menunggu',
            'status_pembayaran' => Transaksi::PEMBAYARAN_BELUM_BAYAR,
            'total_harga' => 100000,
            'total_denda' => 0,
            'total_dibayar' => 0,
        ]);

        $this->createTransaksi([
            'status' => 'aktif',
            'status_pembayaran' => Transaksi::PEMBAYARAN_SEWA_LUNAS,
            'total_harga' => 100000,
            'total_denda' => 0,
            'total_dibayar' => 100000,
        ]);

        $this->createTransaksi([
            'status' => 'selesai',
            'status_pembayaran' => Transaksi::PEMBAYARAN_LUNAS,
            'total_harga' => 120000,
            'total_denda' => 30000,
            'total_dibayar' => 150000,
        ]);

        /*
         * Nilai ini tidak boleh dihitung karena pembayarannya
         * belum berstatus diterima.
         */
        $this->createTransaksi([
            'status' => 'selesai',
            'status_pembayaran' => Transaksi::PEMBAYARAN_BELUM_BAYAR,
            'total_harga' => 999000,
            'total_denda' => 999000,
            'total_dibayar' => 999000,
        ]);

        $response = $this
            ->actingAs($admin, 'admin')
            ->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertViewIs('admin.dashboard')
            ->assertViewHas('totalCustomer', 6)
            ->assertViewHas('totalAlat', 2)
            ->assertViewHas(
                'transaksiMenunggu',
                1
            )
            ->assertViewHas(
                'transaksiAktif',
                1
            )
            ->assertViewHas(
                'totalPendapatan',
                250000
            )
            ->assertViewHas(
                'totalDenda',
                30000
            );
    }

    public function test_dashboard_only_shows_five_latest_transactions(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer(
            'latest@example.com'
        );

        $createdIds = [];

        for ($index = 1; $index <= 6; $index++) {
            $transaksi = $this->createTransaksi(
                [
                    'customer_id' => $customer->id,
                    'created_at' => now()->subMinutes(
                        7 - $index
                    ),
                ]
            );

            $createdIds[] = $transaksi->id;
        }

        $expectedIds = array_reverse(
            array_slice($createdIds, 1)
        );

        $response = $this
            ->actingAs($admin, 'admin')
            ->get(route('admin.dashboard'));

        $response->assertViewHas(
            'transaksiTerbaru',
            function ($transaksi) use (
                $expectedIds
            ): bool {
                return $transaksi->count() === 5 &&
                    $transaksi
                        ->pluck('id')
                        ->all() === $expectedIds;
            }
        );
    }

    public function test_monthly_report_only_contains_completed_and_paid_transactions(): void
    {
        Carbon::setTestNow(
            Carbon::create(
                2026,
                7,
                14,
                12,
                0,
                0
            )
        );

        $admin = $this->createAdmin();

        $first = $this->createTransaksi([
            'status' => 'selesai',
            'status_pembayaran' => Transaksi::PEMBAYARAN_LUNAS,
            'total_harga' => 100000,
            'total_denda' => 0,
            'total_dibayar' => 100000,
            'dibayar_pada' => Carbon::create(2026, 7, 5, 10),
            'denda_dibayar_pada' => null,
        ]);

        $second = $this->createTransaksi([
            'status' => 'selesai',
            'status_pembayaran' => Transaksi::PEMBAYARAN_LUNAS,
            'total_harga' => 120000,
            'total_denda' => 30000,
            'total_dibayar' => 150000,
            'dibayar_pada' => Carbon::create(2026, 7, 2, 10),
            'denda_dibayar_pada' => Carbon::create(2026, 7, 10, 10),
        ]);

        /*
         * Data lama yang belum memiliki waktu pembayaran khusus
         * tetap dibaca dari updated_at.
         */
        $legacy = $this->createTransaksi([
            'status' => 'selesai',
            'status_pembayaran' => Transaksi::PEMBAYARAN_LUNAS,
            'total_harga' => 80000,
            'total_denda' => 10000,
            'total_dibayar' => 90000,
            'dibayar_pada' => null,
            'denda_dibayar_pada' => null,
            'updated_at' => Carbon::create(2026, 7, 8, 10),
        ]);

        $this->createTransaksi([
            'status' => 'selesai',
            'status_pembayaran' => Transaksi::PEMBAYARAN_LUNAS,
            'total_harga' => 500000,
            'total_denda' => 0,
            'total_dibayar' => 500000,
            'dibayar_pada' => Carbon::create(2026, 6, 30, 23),
        ]);

        $this->createTransaksi([
            'status' => 'aktif',
            'status_pembayaran' => Transaksi::PEMBAYARAN_SEWA_LUNAS,
            'total_harga' => 600000,
            'total_denda' => 0,
            'total_dibayar' => 600000,
            'dibayar_pada' => Carbon::create(2026, 7, 11, 10),
        ]);

        $this->createTransaksi([
            'status' => 'selesai',
            'status_pembayaran' => Transaksi::PEMBAYARAN_BELUM_BAYAR,
            'total_harga' => 700000,
            'total_denda' => 0,
            'total_dibayar' => 0,
            'updated_at' => Carbon::create(2026, 7, 12, 10),
        ]);

        $response = $this
            ->actingAs($admin, 'admin')
            ->get(
                route('admin.laporan.index', [
                    'bulan' => 7,
                    'tahun' => 2026,
                ])
            );

        $response
            ->assertOk()
            ->assertViewIs(
                'admin.laporan.index'
            )
            ->assertViewHas('bulan', 7)
            ->assertViewHas('tahun', 2026)
            ->assertViewHas(
                'totalSewa',
                300000
            )
            ->assertViewHas(
                'totalDenda',
                40000
            )
            ->assertViewHas(
                'totalPendapatan',
                340000
            )
            ->assertViewHas(
                'transaksi',
                function ($transaksi) use (
                    $first,
                    $second,
                    $legacy
                ): bool {
                    return $transaksi
                        ->pluck('id')
                        ->all() === [
                            $second->id,
                            $legacy->id,
                            $first->id,
                        ];
                }
            );
    }

    public function test_report_uses_current_month_and_year_by_default(): void
    {
        Carbon::setTestNow(
            Carbon::create(
                2026,
                9,
                20,
                12,
                0,
                0
            )
        );

        $admin = $this->createAdmin();

        $response = $this
            ->actingAs($admin, 'admin')
            ->get(route('admin.laporan.index'));

        $response
            ->assertOk()
            ->assertViewHas('bulan', 9)
            ->assertViewHas('tahun', 2026);
    }

    public function test_report_rejects_invalid_period_filter(): void
    {
        Carbon::setTestNow(
            Carbon::create(
                2026,
                7,
                14,
                12,
                0,
                0
            )
        );

        $admin = $this->createAdmin();

        $response = $this
            ->actingAs($admin, 'admin')
            ->from(route('admin.laporan.index'))
            ->get(
                route('admin.laporan.index', [
                    'bulan' => 13,
                    'tahun' => 2027,
                ])
            );

        $response
            ->assertRedirect(
                route('admin.laporan.index')
            )
            ->assertSessionHasErrors([
                'bulan',
                'tahun',
            ]);
    }

    public function test_admin_can_download_monthly_report_pdf(): void
    {
        Carbon::setTestNow(
            Carbon::create(
                2026,
                7,
                14,
                12,
                0,
                0
            )
        );

        $admin = $this->createAdmin();

        $response = $this
            ->actingAs($admin, 'admin')
            ->get(
                route('admin.laporan.pdf', [
                    'bulan' => 7,
                    'tahun' => 2026,
                ])
            );

        $response
            ->assertOk()
            ->assertDownload(
                'laporan-sirental-7-2026.pdf'
            );

        $this->assertSame(
            'application/pdf',
            $response->headers->get(
                'content-type'
            )
        );
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
        ?string $email = null
    ): Customer {
        static $number = 0;

        $number++;

        return Customer::create([
            'nama_lengkap' => "Customer {$number}",
            'email' => $email ??
                "customer{$number}@example.com",
            'email_verified_at' => now(),
            'password' => null,
            'no_telp' => '0812345678'.
                str_pad(
                    (string) $number,
                    2,
                    '0',
                    STR_PAD_LEFT
                ),
            'alamat' => "Jl. Customer No. {$number}",
        ]);
    }

    private function createAlat(
        string $name,
        bool $active = true
    ): Alat {
        return Alat::create([
            'nama_alat' => $name,
            'kategori' => 'Camping',
            'stok_total' => 10,
            'stok_tersedia' => 10,
            'harga_per_hari' => 50000,
            'kondisi' => 'baik',
            'deskripsi' => 'Alat untuk pengujian dashboard.',
            'foto_alat' => null,
            'is_active' => $active,
        ]);
    }

    private function createTransaksi(
        array $overrides = []
    ): Transaksi {
        static $number = 0;

        $number++;

        $customerId =
            $overrides['customer_id'] ??
            $this->createCustomer()->id;

        unset($overrides['customer_id']);

        $createdAt =
            $overrides['created_at'] ??
            now();

        $updatedAt =
            $overrides['updated_at'] ??
            $createdAt;

        unset(
            $overrides['created_at'],
            $overrides['updated_at']
        );

        $transaksi = Transaksi::create(
            array_merge([
                'customer_id' => $customerId,
                'nama_peminjam' => "Peminjam {$number}",
                'email_peminjam' => "peminjam{$number}@example.com",
                'no_telp_peminjam' => '0890000000'.
                    str_pad(
                        (string) $number,
                        2,
                        '0',
                        STR_PAD_LEFT
                    ),
                'alamat_peminjam' => "Jl. Rental No. {$number}",
                'kode_transaksi' => 'TRX-TEST-'.
                    str_pad(
                        (string) $number,
                        4,
                        '0',
                        STR_PAD_LEFT
                    ),
                'status' => 'menunggu',
                'status_pembayaran' => Transaksi::PEMBAYARAN_BELUM_BAYAR,
                'total_harga' => 100000,
                'total_denda' => 0,
                'total_dibayar' => 0,
                'tanggal_pesan' => now()->toDateString(),
                'tanggal_mulai' => null,
                'tanggal_selesai' => null,
                'dibayar_pada' => null,
                'denda_dibayar_pada' => null,
                'catatan' => null,
                'foto_ktp' => null,
            ], $overrides)
        );

        $transaksi->forceFill([
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ])->saveQuietly();

        return $transaksi->fresh();
    }
}
