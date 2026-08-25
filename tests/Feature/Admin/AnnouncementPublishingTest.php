<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Announcements\Pages\ListAnnouncements;
use App\Models\Announcement;
use App\Models\Church;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\CreatesPanelUsers;
use Tests\TestCase;

class AnnouncementPublishingTest extends TestCase
{
    use CreatesPanelUsers;
    use RefreshDatabase;

    public function test_editor_can_publish_announcement_of_own_church(): void
    {
        $church = Church::factory()->create();
        $editor = $this->makeEditor(church: $church);
        $announcement = Announcement::factory()->for($church)->create([
            'title' => 'Ensaio de sábado',
        ]);

        Livewire::actingAs($editor)
            ->test(ListAnnouncements::class)
            ->callAction(TestAction::make('publish')->table($announcement));

        $announcement->refresh();

        $this->assertTrue($announcement->isPublished());
        $this->assertNotNull($announcement->published_at);
    }
}
