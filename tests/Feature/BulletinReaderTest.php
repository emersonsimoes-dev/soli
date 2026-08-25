<?php

namespace Tests\Feature;

use App\Enums\BulletinStatus;
use App\Models\Bulletin;
use App\Models\Church;
use App\Services\BulletinReader;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulletinReaderTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_returns_published_bulletin_of_the_current_month_only(): void
    {
        $church = Church::factory()->create();
        Bulletin::factory()->for($church)->published()->forMonth(2026, 8)->create(['theme' => 'Agosto']);
        Bulletin::factory()->for($church)->published()->forMonth(2026, 9)->create(['theme' => 'Setembro']);

        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-09-01 00:00:00', 'America/Fortaleza'));

        $found = app(BulletinReader::class)->publishedFor($church);

        $this->assertNotNull($found);
        $this->assertSame('Setembro', $found->theme);
        $this->assertSame(BulletinStatus::Published, $found->status);
    }

    public function test_returns_published_bulletin_for_a_specific_month(): void
    {
        $church = Church::factory()->create();
        Bulletin::factory()->for($church)->published()->forMonth(2026, 8)->create(['theme' => 'Agosto']);
        Bulletin::factory()->for($church)->forMonth(2026, 9)->create([
            'theme' => 'Rascunho',
            'status' => BulletinStatus::Draft,
        ]);

        $reader = app(BulletinReader::class);

        $august = $reader->publishedForPeriod($church, 2026, 8);
        $september = $reader->publishedForPeriod($church, 2026, 9);

        $this->assertNotNull($august);
        $this->assertSame('Agosto', $august->theme);
        $this->assertNull($september);
    }
}
