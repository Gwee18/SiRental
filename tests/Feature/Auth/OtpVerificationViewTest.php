<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OtpVerificationViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_otp_verification_page_displays_complete_form(): void
    {
        $response = $this
            ->withSession([
                'otp_email' => 'customer@example.com',
            ])
            ->get(route('login.verify'));

        $response
            ->assertOk()
            ->assertViewIs('auth.verify-otp')
            ->assertViewHas(
                'email',
                'customer@example.com'
            )
            ->assertSeeText('Verifikasi Kode OTP')
            ->assertSeeText('customer@example.com')
            ->assertSeeText('Verifikasi & Masuk', false)
            ->assertSeeText('Kirim Ulang Kode')
            ->assertSee('name="code"', false);
    }

    public function test_otp_verification_page_requires_email_session(): void
    {
        $this->get(route('login.verify'))
            ->assertRedirect(route('login'));
    }
}
