<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_can_be_rendered(): void
    {
        $response = $this->get(
            route('admin.login')
        );

        $response->assertOk();
        $response->assertViewIs(
            'auth.admin-login'
        );
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $admin = $this->createAdmin();

        $response = $this->post(
            route('admin.login.post'),
            [
                'email' => 'ADMIN@EXAMPLE.COM ',
                'password' => 'password',
            ]
        );

        $this->assertAuthenticatedAs(
            $admin,
            'admin'
        );

        /*
         * Login admin tidak boleh membuat guard customer ikut login.
         */
        $this->assertGuest('web');

        $response->assertRedirect(
            route('admin.dashboard')
        );
    }

    public function test_admin_cannot_login_with_invalid_password(): void
    {
        $this->createAdmin();

        $response = $this
            ->from(route('admin.login'))
            ->post(
                route('admin.login.post'),
                [
                    'email' => 'admin@example.com',
                    'password' => 'password-salah',
                ]
            );

        $this->assertGuest('admin');
        $this->assertGuest('web');

        $response
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors([
                'email' => 'Email atau password salah.',
            ]);
    }

    public function test_authenticated_admin_is_redirected_from_login_page(): void
    {
        $admin = $this->createAdmin();

        $response = $this
            ->actingAs($admin, 'admin')
            ->get(route('admin.login'));

        $response->assertRedirect(
            route('admin.dashboard')
        );
    }

    public function test_admin_can_logout_without_authenticating_customer_guard(): void
    {
        $admin = $this->createAdmin();

        $response = $this
            ->actingAs($admin, 'admin')
            ->post(route('admin.logout'));

        $this->assertGuest('admin');
        $this->assertGuest('web');

        $response->assertRedirect(
            route('admin.login')
        );
    }

    public function test_customer_account_cannot_be_used_for_admin_login(): void
    {
        Customer::create([
            'nama_lengkap' => 'Customer Test',
            'email' => 'customer@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);

        $response = $this
            ->from(route('admin.login'))
            ->post(
                route('admin.login.post'),
                [
                    'email' => 'customer@example.com',
                    'password' => 'password',
                ]
            );

        $this->assertGuest('admin');

        $response
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors([
                'email' => 'Email atau password salah.',
            ]);
    }

    private function createAdmin(): Admin
    {
        $admin = new Admin();

        /*
         * Model Admin memiliki cast password "hashed", sehingga
         * password plain ini otomatis disimpan dalam bentuk hash.
         */
        $admin->forceFill([
            'nama' => 'Admin Test',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $admin->save();

        return $admin;
    }
}