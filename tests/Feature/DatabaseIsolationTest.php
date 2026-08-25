<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_uses_dedicated_postgres_database(): void
    {
        $this->assertSame('testing', app()->environment());
        $this->assertSame('pgsql', config('database.default'));
        $this->assertSame('soli_test', config('database.connections.pgsql.database'));
        $this->assertSame('soli_test', DB::connection()->getDatabaseName());
        $this->assertNotSame('soli', DB::connection()->getDatabaseName());
    }
}
