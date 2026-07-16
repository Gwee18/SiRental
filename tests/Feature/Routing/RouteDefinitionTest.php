<?php

namespace Tests\Feature\Routing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteDefinitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_routes_use_canonical_names(): void
    {
        $this->assertTrue(Route::has('customer.transaksi.index'));
        $this->assertTrue(Route::has('customer.transaksi.show'));
        $this->assertTrue(Route::has('session.csrf'));
        $this->assertFalse(Route::has('customer.transaksi'));
        $this->assertSame(url('/transaksi'), route('customer.transaksi.index'));
        $this->get('/transaksi-saya')->assertNotFound();
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
            $this->assertTrue(Route::has($routeName));
        }

        foreach ([
            'admin.transaksi.create',
            'admin.transaksi.store',
            'admin.transaksi.edit',
            'admin.transaksi.update',
            'admin.transaksi.destroy',
        ] as $routeName) {
            $this->assertFalse(Route::has($routeName));
        }
    }

    public function test_admin_customer_only_exposes_supported_routes(): void
    {
        foreach ([
            'admin.pelanggan.index',
            'admin.pelanggan.show',
            'admin.pelanggan.destroy',
        ] as $routeName) {
            $this->assertTrue(Route::has($routeName));
        }

        foreach ([
            'admin.pelanggan.create',
            'admin.pelanggan.store',
            'admin.pelanggan.edit',
            'admin.pelanggan.update',
        ] as $routeName) {
            $this->assertFalse(Route::has($routeName));
        }
    }

    public function test_unused_registration_routes_are_removed(): void
    {
        $this->assertFalse(Route::has('register'));
        $this->get('/register')->assertNotFound();
        $this->post('/register')->assertNotFound();
    }
}
