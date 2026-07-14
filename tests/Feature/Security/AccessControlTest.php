<?php

namespace Tests\Feature\Security;

use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_customer_rental_page(): void
    {
        $response = $this->get(route('rental.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_customer_can_access_customer_rental_page(): void
    {
        $customer = $this->createCustomer();

        $response = $this
            ->actingAs($customer, 'web')
            ->get(route('rental.index'));

        $response->assertOk();
        $response->assertViewIs('customer.rental.form');
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = $this->createCustomer();

        $response = $this
            ->actingAs($customer, 'web')
            ->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = $this->createAdmin();

        $response = $this
            ->actingAs($admin, 'admin')
            ->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewIs('admin.dashboard');
    }

    public function test_admin_cannot_access_customer_rental_page(): void
    {
        $admin = $this->createAdmin();

        $response = $this
            ->actingAs($admin, 'admin')
            ->get(route('rental.index'));

        $response->assertRedirect(route('login'));
    }

    private function createCustomer(): Customer
    {
        $customer = new Customer;

        $customer->forceFill([
            'nama_lengkap' => 'Customer Test',
            'email' => 'customer@example.com',
            'email_verified_at' => now(),
            'password' => null,
        ]);

        $customer->save();

        return $customer;
    }

    private function createAdmin(): Admin
    {
        $admin = new Admin;

        $admin->forceFill([
            'nama' => 'Admin Test',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $admin->save();

        return $admin;
    }
}
