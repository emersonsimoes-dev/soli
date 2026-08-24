<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthTest extends TestCase
{
    public function test_home_returns_successful_response(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('ICVB no ar', false);
    }

    public function test_health_endpoint_is_available(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_application_uses_fortaleza_timezone(): void
    {
        $this->assertSame('America/Fortaleza', config('app.timezone'));
    }
}
