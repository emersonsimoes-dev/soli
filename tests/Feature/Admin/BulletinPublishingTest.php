<?php

namespace Tests\Feature\Admin;

use App\Enums\BulletinStatus;
use App\Filament\Resources\Bulletins\Pages\CreateBulletin;
use App\Filament\Resources\Bulletins\Pages\ListBulletins;
use App\Models\Bulletin;
use App\Models\Church;
use App\Models\ScheduleItem;
use Carbon\CarbonImmutable;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Repeater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\CreatesPanelUsers;
use Tests\TestCase;

class BulletinPublishingTest extends TestCase
{
    use CreatesPanelUsers;
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_editor_creates_bulletin_as_draft_with_children(): void
    {
        $editor = $this->makeEditor();
        $church = Church::factory()->create();
        Repeater::fake();

        Livewire::actingAs($editor)
            ->test(CreateBulletin::class)
            ->fillForm([
                'church_id' => $church->id,
                'year' => 2026,
                'month' => 9,
                'theme' => 'Setembro em fé',
                'scheduleItems' => [
                    [
                        'day_label' => 'DOM 06',
                        'description' => 'Culto das 19h',
                        'is_highlight' => false,
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $bulletin = Bulletin::query()->where('theme', 'Setembro em fé')->first();

        $this->assertNotNull($bulletin);
        $this->assertSame(BulletinStatus::Draft, $bulletin->status);
        $this->assertNull($bulletin->published_at);
        $this->assertTrue(
            ScheduleItem::query()->where('bulletin_id', $bulletin->id)->where('day_label', 'DOM 06')->exists(),
        );
    }

    public function test_editor_can_publish_and_unpublish_bulletin(): void
    {
        $editor = $this->makeEditor();
        $bulletin = Bulletin::factory()->create([
            'theme' => 'Rascunho de setembro',
            'status' => BulletinStatus::Draft,
            'year' => 2026,
            'month' => 9,
        ]);

        Livewire::actingAs($editor)
            ->test(ListBulletins::class)
            ->callAction(TestAction::make('publish')->table($bulletin));

        $bulletin->refresh();
        $this->assertTrue($bulletin->isPublished());
        $this->assertNotNull($bulletin->published_at);

        Livewire::actingAs($editor)
            ->test(ListBulletins::class)
            ->callAction(TestAction::make('unpublish')->table($bulletin));

        $bulletin->refresh();
        $this->assertSame(BulletinStatus::Draft, $bulletin->status);
        $this->assertNull($bulletin->published_at);
    }

    public function test_published_bulletin_appears_on_home_in_matching_month(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-09-01 08:00:00', 'America/Fortaleza'));

        $editor = $this->makeEditor();
        $church = Church::factory()->create(['slug' => 'icvb-setembro']);
        $bulletin = Bulletin::factory()->for($church)->forMonth(2026, 9)->create([
            'theme' => 'Setembro publicado',
            'status' => BulletinStatus::Draft,
        ]);

        Livewire::actingAs($editor)
            ->test(ListBulletins::class)
            ->callAction(TestAction::make('publish')->table($bulletin));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Setembro publicado');
    }

    public function test_rejects_second_bulletin_for_the_same_church_month(): void
    {
        $editor = $this->makeEditor();
        $church = Church::factory()->create();
        Bulletin::factory()->for($church)->forMonth(2026, 10)->create();
        Repeater::fake();

        Livewire::actingAs($editor)
            ->test(CreateBulletin::class)
            ->fillForm([
                'church_id' => $church->id,
                'year' => 2026,
                'month' => 10,
                'theme' => 'Duplicado',
            ])
            ->call('create')
            ->assertHasFormErrors(['month']);
    }
}
