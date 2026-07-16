<?php

namespace Tests\Feature\Rental;

use App\Models\Alat;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalFormViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_rental_form_contains_request_token_and_no_profile_sync_option(): void
    {
        $customer = Customer::create([
            'nama_lengkap' => 'Customer Test',
            'email' => 'customer@example.com',
            'email_verified_at' => now(),
            'password' => null,
        ]);

        Alat::create([
            'nama_alat' => 'Tenda Camping',
            'kategori' => 'Tenda',
            'stok_total' => 5,
            'stok_tersedia' => 5,
            'harga_per_hari' => 50000,
            'kondisi' => 'baik',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($customer, 'web')
            ->get(route('rental.index'));

        $response
            ->assertOk()
            ->assertViewIs('customer.rental.form')
            ->assertSee('name="request_token"', false)
            ->assertSee('id="duplicateToolNotice"', false)
            ->assertDontSee('simpan_ke_profil')
            ->assertDontSee('Data diisi otomatis dari profil');
    }
}
