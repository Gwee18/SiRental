<?php

namespace Tests\Feature\Auth;

use App\Models\Customer;
use App\Models\EmailOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OtpExpirationPersistenceTest extends TestCase
{
    use RefreshDatabase;

    private const EMAIL = 'retry@example.com';

    private const CORRECT_CODE = '123456';

    private const IP_ADDRESS = '127.0.0.77';

    protected function tearDown(): void
    {
        RateLimiter::clear(
            'otp-verify:'.sha1(
                self::EMAIL.'|'.self::IP_ADDRESS
            )
        );

        parent::tearDown();
    }

    public function test_otp_expiration_columns_use_datetime(): void
    {
        $this->assertSame(
            'datetime',
            Schema::getColumnType(
                'email_otps',
                'expires_at'
            )
        );

        $this->assertSame(
            'datetime',
            Schema::getColumnType(
                'email_otps',
                'used_at'
            )
        );
    }

    public function test_correct_otp_remains_valid_after_one_wrong_attempt(): void
    {
        $customer = Customer::create([
            'nama_lengkap' => 'Customer Retry',
            'email' => self::EMAIL,
            'email_verified_at' => now(),
            'password' => null,
        ]);

        $expiresAt = now()
            ->addMinutes(10)
            ->startOfSecond();

        $otp = EmailOtp::create([
            'email' => self::EMAIL,
            'code_hash' => Hash::make(
                self::CORRECT_CODE
            ),
            'attempts' => 0,
            'expires_at' => $expiresAt,
        ]);

        $wrongResponse = $this
            ->from(route('login.verify'))
            ->withServerVariables([
                'REMOTE_ADDR' => self::IP_ADDRESS,
            ])
            ->withSession([
                'otp_email' => self::EMAIL,
            ])
            ->post(
                route('login.verify.post'),
                ['code' => '000000']
            );

        $wrongResponse
            ->assertRedirect(route('login.verify'))
            ->assertSessionHasErrors(['code']);

        $otp->refresh();

        $this->assertSame(
            1,
            (int) $otp->attempts
        );

        $this->assertTrue(
            $otp->expires_at->equalTo($expiresAt)
        );

        $this->assertFalse(
            $otp->expires_at->isPast()
        );

        $correctResponse = $this
            ->withServerVariables([
                'REMOTE_ADDR' => self::IP_ADDRESS,
            ])
            ->withSession([
                'otp_email' => self::EMAIL,
            ])
            ->post(
                route('login.verify.post'),
                ['code' => self::CORRECT_CODE]
            );

        $correctResponse->assertRedirect(
            route('home')
        );

        $this->assertAuthenticatedAs(
            $customer,
            'web'
        );

        $this->assertNotNull(
            $otp->fresh()->used_at
        );
    }
}
