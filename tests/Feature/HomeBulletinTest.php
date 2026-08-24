<?php

namespace Tests\Feature;

use App\Enums\BulletinStatus;
use App\Models\Bulletin;
use App\Models\Church;
use App\Models\ScheduleItem;
use Carbon\CarbonImmutable;
use Database\Seeders\August2026BulletinSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeBulletinTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_home_shows_published_bulletin_for_current_month(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-08-24 14:15:00', 'America/Fortaleza'));
        $this->seed(August2026BulletinSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Igreja em Ação')
            ->assertSee('Bazar da Ação Social')
            ->assertSee('Cecy')
            ->assertSee('>8</p>', false)
            ->assertSee('SOLI')
            ->assertSee('Conectando a igreja. Honrando a missão.');
    }

    public function test_home_does_not_show_draft_bulletin(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-08-24 10:00:00', 'America/Fortaleza'));
        $church = Church::factory()->create(['slug' => 'icvb']);
        $bulletin = Bulletin::factory()->for($church)->forMonth(2026, 8)->create([
            'theme' => 'Rascunho secreto',
            'status' => BulletinStatus::Draft,
        ]);
        ScheduleItem::query()->create([
            'bulletin_id' => $bulletin->id,
            'day_label' => 'DOM 01',
            'description' => 'Não deve aparecer',
            'sort_order' => 0,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Boletim de')
            ->assertSee('2026')
            ->assertDontSee('Rascunho secreto')
            ->assertDontSee('Não deve aparecer');
    }

    public function test_home_does_not_fall_back_to_previous_month(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-09-01 08:00:00', 'America/Fortaleza'));
        $this->seed(August2026BulletinSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Boletim de')
            ->assertSee('Setembro')
            ->assertDontSee('Bazar da Ação Social')
            ->assertDontSee('Igreja em Ação');
    }

    public function test_church_without_logo_uses_soli_placeholder(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-08-10 12:00:00', 'America/Fortaleza'));
        $church = Church::factory()->create(['logo_path' => null]);
        Bulletin::factory()->for($church)->published()->create();

        $this->assertTrue($church->usesDefaultLogo());

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(asset('images/soli/mark.png'), false);
    }

    public function test_footer_always_credits_soli(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('soli-footer', false)
            ->assertSee('SOLI')
            ->assertSee('Conectando a igreja. Honrando a missão.');
    }

    public function test_church_logo_replaces_soli_placeholder_but_footer_stays(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-08-10 12:00:00', 'America/Fortaleza'));
        $church = Church::factory()->withCustomLogo()->create();
        Bulletin::factory()->for($church)->published()->create();

        $this->assertFalse($church->usesDefaultLogo());

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('/storage/churches/custom-logo.png', false)
            ->assertSee(asset('images/soli/mark.png'), false)
            ->assertSee('soli-footer', false)
            ->assertSee('SOLI');
    }
}
