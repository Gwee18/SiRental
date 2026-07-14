<?php

namespace Tests\Feature\Security;

use App\Models\Customer;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_own_transaction(): void
    {
        $customer = $this->createCustomer(
            'customer.satu@example.com',
            'Customer Satu'
        );

        $transaksi = $this->createTransaction(
            $customer,
            'SR-OWN00001'
        );

        $response = $this
            ->actingAs($customer, 'web')
            ->get(
                route(
                    'customer.transaksi.show',
                    $transaksi->id
                )
            );

        $response->assertOk();
        $response->assertViewIs(
            'customer.transaksi.show'
        );
        $response->assertViewHas(
            'transaksi',
            fn (Transaksi $item) => $item->is($transaksi)
        );
    }

    public function test_customer_cannot_view_another_customers_transaction(): void
    {
        $customerSatu = $this->createCustomer(
            'customer.satu@example.com',
            'Customer Satu'
        );

        $customerDua = $this->createCustomer(
            'customer.dua@example.com',
            'Customer Dua'
        );

        $transaksiMilikCustomerDua =
            $this->createTransaction(
                $customerDua,
                'SR-OTHER001'
            );

        $response = $this
            ->actingAs($customerSatu, 'web')
            ->get(
                route(
                    'customer.transaksi.show',
                    $transaksiMilikCustomerDua->id
                )
            );

        $response->assertNotFound();
    }

    public function test_transaction_list_only_contains_authenticated_customers_transactions(): void
    {
        $customerSatu = $this->createCustomer(
            'customer.satu@example.com',
            'Customer Satu'
        );

        $customerDua = $this->createCustomer(
            'customer.dua@example.com',
            'Customer Dua'
        );

        $transaksiSendiri = $this->createTransaction(
            $customerSatu,
            'SR-LIST0001'
        );

        $transaksiCustomerLain =
            $this->createTransaction(
                $customerDua,
                'SR-LIST0002'
            );

        $response = $this
            ->actingAs($customerSatu, 'web')
            ->get(
                route(
                    'customer.transaksi.index'
                )
            );

        $response->assertOk();
        $response->assertViewIs(
            'customer.transaksi.index'
        );

        $response->assertViewHas(
            'transaksi',
            function ($items) use (
                $transaksiSendiri,
                $transaksiCustomerLain
            ) {
                return $items->contains(
                    'id',
                    $transaksiSendiri->id
                ) &&
                ! $items->contains(
                    'id',
                    $transaksiCustomerLain->id
                );
            }
        );
    }

    private function createCustomer(
        string $email,
        string $nama
    ): Customer {
        return Customer::create([
            'nama_lengkap' => $nama,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => null,
        ]);
    }

    private function createTransaction(
        Customer $customer,
        string $kode
    ): Transaksi {
        return Transaksi::create([
            'customer_id' => $customer->id,
            'kode_transaksi' => $kode,
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
}
