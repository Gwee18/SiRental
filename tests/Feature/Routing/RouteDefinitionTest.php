<?php

namespace Tests\Feature\Routing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteDefinitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_transaction_uses_one_canonical_index_route(): void
    {
        $this->assertTrue(
            Route::has('customer.transaksi.index')
        );

        $this->assertTrue(
            Route::has('customer.transaksi.show')
        );

        $this->assertFalse(
            Route::has('customer.transaksi')
        );

        $this->assertSame(
            url('/transaksi'),
            route('customer.transaksi.index')
        );

        $this->get('/transaksi-saya')
            ->assertNotFound();
    }

    public function test_admin_transaction_only_exposes_supported_routes(): void
    {
        foreach ([
            'admin.transaksi.index',
            'admin.transaksi.show',
            'admin.transaksi.approve',
            'admin.transaksi.tolak',
            'admin.transaksi.selesai',
        ] as $routeName) {
            $this->assertTrue(
                Route::has($routeName),
                "Route {$routeName} seharusnya tersedia."
            );
        }

        foreach ([
            'admin.transaksi.create',
            'admin.transaksi.store',
            'admin.transaksi.edit',
            'admin.transaksi.update',
            'admin.transaksi.destroy',
        ] as $routeName) {
            $this->assertFalse(
                Route::has($routeName),
                "Route {$routeName} seharusnya tidak tersedia."
            );
        }
    }

    public function test_admin_customer_only_exposes_supported_routes(): void
    {
        foreach ([
            'admin.pelanggan.index',
            'admin.pelanggan.show',
            'admin.pelanggan.destroy',
        ] as $routeName) {
            $this->assertTrue(
                Route::has($routeName),
                "Route {$routeName} seharusnya tersedia."
            );
        }

        foreach ([
            'admin.pelanggan.create',
            'admin.pelanggan.store',
            'admin.pelanggan.edit',
            'admin.pelanggan.update',
        ] as $routeName) {
            $this->assertFalse(
                Route::has($routeName),
                "Route {$routeName} seharusnya tidak tersedia."
            );
        }
    }

    public function test_register_get_redirects_to_otp_login_and_post_is_removed(): void
    {
        $this->get('/register')
            ->assertRedirect(route('login'));

        $this->post('/register')
            ->assertMethodNotAllowed();
    }
}
