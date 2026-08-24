<?php

namespace Tests\Feature\Admin;

use App\Enums\BulletinStatus;
use App\Filament\Resources\Activities\Pages\ListActivities;
use App\Filament\Resources\Bulletins\Pages\ListBulletins;
use App\Models\Activity;
use App\Models\Bulletin;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Livewire\Livewire;
use Tests\Concerns\CreatesPanelUsers;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use CreatesPanelUsers;
    use RefreshDatabase;

    public function test_publishing_a_bulletin_records_causer_changes_and_ip(): void
    {
        $editor = $this->makeEditor(['name' => 'Editora Ana']);
        $bulletin = Bulletin::factory()->create([
            'status' => BulletinStatus::Draft,
        ]);

        $this->actingAs($editor);
        $this->app->instance('request', Request::create('/', 'POST', server: [
            'REMOTE_ADDR' => '203.0.113.10',
            'HTTP_USER_AGENT' => 'SoliTest/1.0',
        ]));

        $bulletin->publish();

        $log = Activity::query()
            ->where('subject_type', Bulletin::class)
            ->where('subject_id', $bulletin->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($log);
        $this->assertSame($editor->id, $log->causer_id);
        $this->assertSame('updated', $log->event);
        $this->assertSame('203.0.113.10', $log->ip);
        $this->assertSame('SoliTest/1.0', $log->user_agent);
        $this->assertSame('draft', data_get($log->properties, 'old.status'));
        $this->assertSame('published', data_get($log->properties, 'attributes.status'));
    }

    public function test_panel_publish_action_writes_an_audit_row(): void
    {
        $editor = $this->makeEditor();
        $bulletin = Bulletin::factory()->create([
            'status' => BulletinStatus::Draft,
        ]);

        Livewire::actingAs($editor)
            ->test(ListBulletins::class)
            ->callAction(TestAction::make('publish')->table($bulletin));

        $this->assertTrue(
            Activity::query()
                ->where('subject_type', Bulletin::class)
                ->where('subject_id', $bulletin->id)
                ->where('causer_id', $editor->id)
                ->where('event', 'updated')
                ->exists(),
        );
    }

    public function test_editor_can_read_audit_but_cannot_mutate_logs(): void
    {
        $editor = $this->makeEditor();
        $bulletin = Bulletin::factory()->create();
        $bulletin->update(['theme' => 'Tema auditado']);

        Livewire::actingAs($editor)
            ->test(ListActivities::class)
            ->assertSuccessful();

        $log = Activity::query()->first();

        $this->assertNotNull($log);
        $this->assertTrue($editor->can('view', $log));
        $this->assertFalse($editor->can('update', $log));
        $this->assertFalse($editor->can('delete', $log));
        $this->assertFalse($editor->can('create', Activity::class));
    }
}
