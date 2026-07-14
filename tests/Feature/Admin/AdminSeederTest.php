<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use RuntimeException;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_seeder_uses_configured_credentials(): void
    {
        config()->set(
            'sirental.admin',
            [
                'name' => 'Admin Pengujian',
                'email' => 'ADMIN.TEST@EXAMPLE.COM',
                'password' => 'password-aman-123',
            ]
        );

        $this->seed(AdminSeeder::class);

        $admin = Admin::query()->first();

        $this->assertNotNull($admin);

        $this->assertSame(
            'Admin Pengujian',
            $admin->nama
        );

        $this->assertSame(
            'admin.test@example.com',
            $admin->email
        );

        $this->assertTrue(
            Hash::check(
                'password-aman-123',
                $admin->password
            )
        );
    }

    public function test_admin_seeder_is_idempotent_and_updates_existing_admin(): void
    {
        config()->set(
            'sirental.admin',
            [
                'name' => 'Admin Lama',
                'email' => 'admin@example.com',
                'password' => 'password-lama',
            ]
        );

        $this->seed(AdminSeeder::class);

        config()->set(
            'sirental.admin',
            [
                'name' => 'Admin Baru',
                'email' => 'admin@example.com',
                'password' => 'password-baru',
            ]
        );

        $this->seed(AdminSeeder::class);

        $this->assertDatabaseCount(
            'admins',
            1
        );

        $admin = Admin::query()->first();

        $this->assertSame(
            'Admin Baru',
            $admin->nama
        );

        $this->assertTrue(
            Hash::check(
                'password-baru',
                $admin->password
            )
        );
    }

    public function test_admin_seeder_rejects_missing_password(): void
    {
        config()->set(
            'sirental.admin',
            [
                'name' => 'Admin Test',
                'email' => 'admin@example.com',
                'password' => null,
            ]
        );

        $this->expectException(
            RuntimeException::class
        );

        $this->expectExceptionMessage(
            'ADMIN_PASSWORD belum diatur'
        );

        $this->seed(AdminSeeder::class);
    }

    public function test_admin_seeder_rejects_weak_password(): void
    {
        config()->set(
            'sirental.admin',
            [
                'name' => 'Admin Test',
                'email' => 'admin@example.com',
                'password' => '1234567',
            ]
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'ADMIN_PASSWORD minimal'
        );

        $this->seed(AdminSeeder::class);
    }

    public function test_default_password_is_rejected_on_production(): void
    {
        $this->app->detectEnvironment(
            fn (): string => 'production'
        );

        config()->set(
            'sirental.admin',
            [
                'name' => 'Admin Test',
                'email' => 'admin@example.com',
                'password' => 'admin123',
            ]
        );

        $this->expectException(
            RuntimeException::class
        );

        $this->expectExceptionMessage(
            'tidak boleh digunakan pada production'
        );

        /*
         * Jangan memakai $this->seed() saat environment production.
         * Command db:seed Laravel akan meminta konfirmasi interaktif
         * sebelum menjalankan seeder pada production.
         *
         * Panggil seeder secara langsung agar test hanya menguji
         * validasi keamanan milik AdminSeeder.
         */
        $this->app
            ->make(AdminSeeder::class)
            ->run();
    }
}
