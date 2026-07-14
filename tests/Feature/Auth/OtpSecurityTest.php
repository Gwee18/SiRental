<?php

namespace Tests\Feature\Auth;

use App\Models\Customer;
use App\Models\EmailOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OtpSecurityTest extends TestCase
{
    use RefreshDatabase;

    private const IP_ADDRESS = '127.0.0.1';

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    public function test_email_is_normalized_when_otp_is_requested(): void
    {
        $response = $this
            ->from(route('login'))
            ->withServerVariables([
                'REMOTE_ADDR' => self::IP_ADDRESS,
            ])
            ->post(route('login.send-otp'), [
                'email' => '  USER.NORMALIZED@EXAMPLE.COM  ',
            ]);

        $response
            ->assertRedirect(
                route('login.verify')
            )
            ->assertSessionHas(
                'otp_email',
                'user.normalized@example.com'
            );

        $this->assertDatabaseHas(
            'email_otps',
            [
                'email' => 'user.normalized@example.com',
                'attempts' => 0,
                'used_at' => null,
            ]
        );

        $this->assertDatabaseCount(
            'email_otps',
            1
        );
    }

    public function test_resend_is_blocked_during_sixty_second_cooldown(): void
    {
        $email = 'cooldown@example.com';

        $otp = $this->createOtp(
            $email,
            '123456'
        );

        $response = $this
            ->from(route('login.verify'))
            ->withServerVariables([
                'REMOTE_ADDR' => self::IP_ADDRESS,
            ])
            ->withSession([
                'otp_email' => $email,
            ])
            ->post(route('login.resend'));

        $response
            ->assertRedirect(
                route('login.verify')
            )
            ->assertSessionHasErrors([
                'email',
            ]);

        $this->assertDatabaseCount(
            'email_otps',
            1
        );

        $this->assertDatabaseHas(
            'email_otps',
            [
                'id' => $otp->id,
                'email' => $email,
                'attempts' => 0,
                'used_at' => null,
            ]
        );
    }

    public function test_requesting_new_otp_removes_previous_unused_otp(): void
    {
        $email = 'replace@example.com';

        $oldOtp = $this->createOtp(
            $email,
            '123456',
            now()->subMinutes(2)
        );

        $response = $this
            ->from(route('login.verify'))
            ->withServerVariables([
                'REMOTE_ADDR' => self::IP_ADDRESS,
            ])
            ->withSession([
                'otp_email' => $email,
            ])
            ->post(route('login.resend'));

        $response->assertRedirect(
            route('login.verify')
        );

        $this->assertDatabaseMissing(
            'email_otps',
            [
                'id' => $oldOtp->id,
            ]
        );

        $this->assertDatabaseCount(
            'email_otps',
            1
        );

        $newOtp = EmailOtp::where(
            'email',
            $email
        )->first();

        $this->assertNotNull($newOtp);

        $this->assertNotSame(
            $oldOtp->id,
            $newOtp->id
        );

        $this->assertSame(
            0,
            (int) $newOtp->attempts
        );

        $this->assertNull(
            $newOtp->used_at
        );
    }

    public function test_otp_can_only_be_used_once(): void
    {
        $email = 'one-time@example.com';
        $code = '123456';

        $customer = Customer::create([
            'nama_lengkap' => 'One Time Customer',
            'email' => $email,
            'email_verified_at' => now(),
            'password' => null,
        ]);

        $otp = $this->createOtp(
            $email,
            $code
        );

        $firstResponse = $this
            ->withServerVariables([
                'REMOTE_ADDR' => self::IP_ADDRESS,
            ])
            ->withSession([
                'otp_email' => $email,
            ])
            ->post(
                route('login.verify.post'),
                ['code' => $code]
            );

        $firstResponse->assertRedirect(
            route('home')
        );

        $this->assertAuthenticatedAs(
            $customer,
            'web'
        );

        $this->assertNotNull(
            $otp->fresh()->used_at
        );

        $this->post(route('logout'));

        $secondResponse = $this
            ->from(route('login.verify'))
            ->withServerVariables([
                'REMOTE_ADDR' => self::IP_ADDRESS,
            ])
            ->withSession([
                'otp_email' => $email,
            ])
            ->post(
                route('login.verify.post'),
                ['code' => $code]
            );

        $this->assertGuest('web');

        $secondResponse
            ->assertRedirect(
                route('login.verify')
            )
            ->assertSessionHasErrors([
                'code',
            ]);

        $this->assertSame(
            1,
            EmailOtp::where(
                'email',
                $email
            )->count()
        );
    }

    public function test_correct_otp_is_blocked_after_five_wrong_attempts(): void
    {
        $email = 'verify-limit@example.com';
        $correctCode = '123456';

        $otp = $this->createOtp(
            $email,
            $correctCode
        );

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $response = $this
                ->from(route('login.verify'))
                ->withServerVariables([
                    'REMOTE_ADDR' => self::IP_ADDRESS,
                ])
                ->withSession([
                    'otp_email' => $email,
                ])
                ->post(
                    route('login.verify.post'),
                    ['code' => '000000']
                );

            $response->assertSessionHasErrors([
                'code',
            ]);
        }

        $this->assertSame(
            5,
            (int) $otp->fresh()->attempts
        );

        $blockedResponse = $this
            ->from(route('login.verify'))
            ->withServerVariables([
                'REMOTE_ADDR' => self::IP_ADDRESS,
            ])
            ->withSession([
                'otp_email' => $email,
            ])
            ->post(
                route('login.verify.post'),
                ['code' => $correctCode]
            );

        $this->assertGuest('web');

        $blockedResponse
            ->assertRedirect(
                route('login.verify')
            )
            ->assertSessionHasErrors([
                'code',
            ]);

        $otp->refresh();

        $this->assertSame(
            5,
            (int) $otp->attempts
        );

        $this->assertNull($otp->used_at);
    }

    public function test_new_otp_resets_verification_rate_limit(): void
    {
        $email = 'reset-limit@example.com';

        $oldOtp = $this->createOtp(
            $email,
            '123456',
            now()->subMinutes(2)
        );

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this
                ->withServerVariables([
                    'REMOTE_ADDR' => self::IP_ADDRESS,
                ])
                ->withSession([
                    'otp_email' => $email,
                ])
                ->post(
                    route('login.verify.post'),
                    ['code' => '000000']
                )
                ->assertSessionHasErrors([
                    'code',
                ]);
        }

        $this->assertSame(
            5,
            (int) $oldOtp->fresh()->attempts
        );

        $resendResponse = $this
            ->from(route('login.verify'))
            ->withServerVariables([
                'REMOTE_ADDR' => self::IP_ADDRESS,
            ])
            ->withSession([
                'otp_email' => $email,
            ])
            ->post(route('login.resend'));

        $resendResponse->assertRedirect(
            route('login.verify')
        );

        $newOtp = EmailOtp::where(
            'email',
            $email
        )->first();

        $this->assertNotNull($newOtp);

        $this->assertNotSame(
            $oldOtp->id,
            $newOtp->id
        );

        $this->assertSame(
            0,
            (int) $newOtp->attempts
        );

        /*
         * Satu kode salah pada OTP baru harus diproses sebagai
         * percobaan pertama, bukan langsung ditolak rate limiter lama.
         */
        $this
            ->from(route('login.verify'))
            ->withServerVariables([
                'REMOTE_ADDR' => self::IP_ADDRESS,
            ])
            ->withSession([
                'otp_email' => $email,
            ])
            ->post(
                route('login.verify.post'),
                ['code' => '000000']
            )
            ->assertSessionHasErrors([
                'code',
            ]);

        $this->assertSame(
            1,
            (int) $newOtp->fresh()->attempts
        );
    }

    public function test_otp_requests_are_limited_to_three_per_ten_minutes(): void
    {
        $email = 'send-limit@example.com';

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $response = $this
                ->from(route('login'))
                ->withServerVariables([
                    'REMOTE_ADDR' => self::IP_ADDRESS,
                ])
                ->post(
                    route('login.send-otp'),
                    ['email' => $email]
                );

            $response->assertRedirect(
                route('login.verify')
            );

            /*
             * Lewati cooldown 60 detik agar request berikutnya
             * menguji batas total pengiriman, bukan cooldown.
             */
            if ($attempt < 3) {
                $otp = EmailOtp::where(
                    'email',
                    $email
                )->first();

                $otp->forceFill([
                    'created_at' => now()->subSeconds(61),
                ])->saveQuietly();
            }
        }

        $blockedResponse = $this
            ->from(route('login'))
            ->withServerVariables([
                'REMOTE_ADDR' => self::IP_ADDRESS,
            ])
            ->post(
                route('login.send-otp'),
                ['email' => $email]
            );

        $blockedResponse
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email',
            ]);

        $this->assertDatabaseCount(
            'email_otps',
            1
        );
    }

    public function test_otp_for_one_email_cannot_be_used_by_another_email(): void
    {
        $ownerEmail = 'owner@example.com';
        $otherEmail = 'other@example.com';
        $code = '123456';

        $otp = $this->createOtp(
            $ownerEmail,
            $code
        );

        $response = $this
            ->from(route('login.verify'))
            ->withServerVariables([
                'REMOTE_ADDR' => self::IP_ADDRESS,
            ])
            ->withSession([
                'otp_email' => $otherEmail,
            ])
            ->post(
                route('login.verify.post'),
                ['code' => $code]
            );

        $this->assertGuest('web');

        $response
            ->assertRedirect(
                route('login.verify')
            )
            ->assertSessionHasErrors([
                'code',
            ]);

        $this->assertDatabaseMissing(
            'customers',
            [
                'email' => $otherEmail,
            ]
        );

        $otp->refresh();

        $this->assertSame(
            0,
            (int) $otp->attempts
        );

        $this->assertNull($otp->used_at);
    }

    private function createOtp(
        string $email,
        string $code,
        $createdAt = null
    ): EmailOtp {
        $otp = EmailOtp::create([
            'email' => $email,
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
        ]);

        if ($createdAt) {
            $otp->forceFill([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ])->saveQuietly();
        }

        return $otp->fresh();
    }
}
