<?php

namespace Tests\Feature\Api;

use App\Enums\BulletinStatus;
use App\Models\Bulletin;
use App\Models\Church;
use App\Models\ScheduleItem;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\HasApiTokens;
use Tests\TestCase;

class BulletinApiTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_current_returns_published_bulletin_for_fortaleza_month(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-08-24 14:15:00', 'America/Fortaleza'));

        $church = Church::factory()->create([
            'name' => 'Igreja Congregacional Vale da Benção',
            'pix_key' => '50.208.029/0001-31',
        ]);
        $bulletin = Bulletin::factory()->for($church)->published()->forMonth(2026, 8)->create([
            'theme' => 'Igreja em Ação',
        ]);
        ScheduleItem::query()->create([
            'bulletin_id' => $bulletin->id,
            'day_label' => 'DOM 02',
            'description' => 'Culto e Jardim de Oração',
            'is_highlight' => false,
            'sort_order' => 0,
        ]);

        $this->getJson('/api/v1/bulletins/current')
            ->assertOk()
            ->assertJsonPath('data.year', 2026)
            ->assertJsonPath('data.month', 8)
            ->assertJsonPath('data.theme', 'Igreja em Ação')
            ->assertJsonPath('data.status', 'published')
            ->assertJsonPath('data.church.name', 'Igreja Congregacional Vale da Benção')
            ->assertJsonPath('data.church.pix_key', '50.208.029/0001-31')
            ->assertJsonPath('data.schedule.0.day_label', 'DOM 02')
            ->assertJsonPath('data.kpis.schedule', 1)
            ->assertJsonPath('data.kpis.events', 0)
            ->assertJsonPath('data.kpis.services', 0)
            ->assertJsonPath('data.kpis.birthdays', 0);
    }

    public function test_current_returns_404_when_current_month_is_not_published(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-09-01 08:00:00', 'America/Fortaleza'));

        $church = Church::factory()->create();
        Bulletin::factory()->for($church)->published()->forMonth(2026, 8)->create(['theme' => 'Agosto']);
        Bulletin::factory()->for($church)->forMonth(2026, 9)->create([
            'theme' => 'Rascunho de setembro',
            'status' => BulletinStatus::Draft,
        ]);

        $this->getJson('/api/v1/bulletins/current')
            ->assertNotFound()
            ->assertJsonMissingPath('data.theme');
    }

    public function test_show_returns_published_historical_month(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-09-15 10:00:00', 'America/Fortaleza'));

        $church = Church::factory()->create(['pix_key' => 'chave-pix']);
        Bulletin::factory()->for($church)->published()->forMonth(2026, 8)->create([
            'theme' => 'Igreja em Ação',
        ]);

        $this->getJson('/api/v1/bulletins/2026/8')
            ->assertOk()
            ->assertJsonPath('data.year', 2026)
            ->assertJsonPath('data.month', 8)
            ->assertJsonPath('data.theme', 'Igreja em Ação')
            ->assertJsonPath('data.church.pix_key', 'chave-pix');
    }

    public function test_show_returns_404_when_month_is_not_published(): void
    {
        $church = Church::factory()->create();
        Bulletin::factory()->for($church)->forMonth(2026, 10)->create([
            'theme' => 'Outubro secreto',
            'status' => BulletinStatus::Draft,
        ]);

        $this->getJson('/api/v1/bulletins/2026/10')
            ->assertNotFound()
            ->assertDontSee('Outubro secreto');
    }

    public function test_show_returns_404_when_month_does_not_exist(): void
    {
        Church::factory()->create();

        $this->getJson('/api/v1/bulletins/2026/11')->assertNotFound();
    }

    public function test_show_returns_404_for_invalid_month(): void
    {
        Church::factory()->create();

        $this->getJson('/api/v1/bulletins/2026/13')->assertNotFound();
    }

    public function test_sanctum_is_installed_for_future_authenticated_clients(): void
    {
        $this->assertContains(HasApiTokens::class, class_uses_recursive(User::class));
        $this->assertTrue(Schema::hasTable('personal_access_tokens'));
    }
}
