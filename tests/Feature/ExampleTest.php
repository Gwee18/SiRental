<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_can_be_rendered(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewIs('customer.home');
    }
}