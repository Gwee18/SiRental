<?php

namespace Tests\Feature\Auth;

use App\Models\Customer;
use App\Models\EmailOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
    }

    public function test_customer_can_login_using_valid_otp(): void
    {
        $email = 'customer@example.com';
        $code = '123456';

        $customer = Customer::create([
            'nama_lengkap' => 'Customer Test',
            'email' => $email,
            'email_verified_at' => now(),
            'password' => null,
        ]);

        EmailOtp::create([
            'email' => $email,
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this
            ->withSession([
                'otp_email' => $email,
            ])
            ->post(route('login.verify.post'), [
                'code' => $code,
            ]);

        $this->assertAuthenticatedAs($customer, 'web');

        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('email_otps', [
            'email' => $email,
            'attempts' => 0,
        ]);

        $this->assertNotNull(
            EmailOtp::where('email', $email)->value('used_at')
        );
    }

    public function test_valid_otp_creates_new_customer(): void
    {
        $email = 'customer.baru@example.com';
        $code = '654321';

        EmailOtp::create([
            'email' => $email,
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this
            ->withSession([
                'otp_email' => $email,
            ])
            ->post(route('login.verify.post'), [
                'code' => $code,
            ]);

        $customer = Customer::where('email', $email)->first();

        $this->assertNotNull($customer);
        $this->assertSame(
            'Customer Baru',
            $customer->nama_lengkap
        );
        $this->assertNotNull($customer->email_verified_at);
        $this->assertAuthenticatedAs($customer, 'web');

        $response->assertRedirect(route('home'));
    }

    public function test_customer_cannot_login_using_invalid_otp(): void
    {
        $email = 'customer@example.com';

        EmailOtp::create([
            'email' => $email,
            'code_hash' => Hash::make('123456'),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this
            ->from(route('login.verify'))
            ->withSession([
                'otp_email' => $email,
            ])
            ->post(route('login.verify.post'), [
                'code' => '000000',
            ]);

        $this->assertGuest('web');

        $response
            ->assertRedirect(route('login.verify'))
            ->assertSessionHasErrors([
                'code',
            ]);

        $this->assertDatabaseHas('email_otps', [
            'email' => $email,
            'attempts' => 1,
        ]);
    }

    public function test_expired_otp_cannot_be_used(): void
    {
        $email = 'customer@example.com';

        EmailOtp::create([
            'email' => $email,
            'code_hash' => Hash::make('123456'),
            'attempts' => 0,
            'expires_at' => now()->subMinute(),
        ]);

        $response = $this
            ->from(route('login.verify'))
            ->withSession([
                'otp_email' => $email,
            ])
            ->post(route('login.verify.post'), [
                'code' => '123456',
            ]);

        $this->assertGuest('web');

        $response
            ->assertRedirect(route('login.verify'))
            ->assertSessionHasErrors([
                'code',
            ]);
    }

    public function test_customer_can_logout(): void
    {
        $customer = Customer::create([
            'nama_lengkap' => 'Customer Test',
            'email' => 'customer@example.com',
            'email_verified_at' => now(),
            'password' => null,
        ]);

        $response = $this
            ->actingAs($customer, 'web')
            ->post(route('logout'));

        $this->assertGuest('web');

        $response->assertRedirect(route('home'));
    }
}