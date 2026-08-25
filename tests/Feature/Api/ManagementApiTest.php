<?php

namespace Tests\Feature\Api;

use App\Models\Announcement;
use App\Models\Bulletin;
use App\Models\Church;
use App\Models\Contribution;
use App\Models\Member;
use App\Models\RosterEntry;
use App\Models\ScheduleItem;
use App\Models\User;
use Carbon\CarbonImmutable;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ManagementApiTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_church_slug_returns_published_bulletin_and_settings(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-08-24 14:15:00', 'America/Fortaleza'));

        $church = Church::factory()->create([
            'slug' => 'icvb',
            'name' => 'Igreja Congregacional Vale da Benção',
            'settings' => [
                'contact' => ['email' => 'contato@icvb.test'],
                'ministries' => [['name' => 'Louvor']],
            ],
        ]);
        $other = Church::factory()->create(['slug' => 'outra']);
        Bulletin::factory()->for($church)->published()->forMonth(2026, 8)->create([
            'theme' => 'Igreja em Ação',
        ]);
        Bulletin::factory()->for($other)->published()->forMonth(2026, 8)->create([
            'theme' => 'Tema da outra',
        ]);

        $this->getJson('/api/v1/churches/icvb')
            ->assertOk()
            ->assertJsonPath('data.slug', 'icvb')
            ->assertJsonPath('data.settings.contact.email', 'contato@icvb.test')
            ->assertJsonPath('data.settings.ministries.0.name', 'Louvor');

        $this->getJson('/api/v1/churches/icvb/bulletins/current')
            ->assertOk()
            ->assertJsonPath('data.theme', 'Igreja em Ação')
            ->assertJsonMissing(['theme' => 'Tema da outra']);
    }

    public function test_public_announcements_hide_drafts_and_other_churches(): void
    {
        $church = Church::factory()->create(['slug' => 'icvb']);
        $other = Church::factory()->create(['slug' => 'outra']);

        Announcement::factory()->for($church)->published()->create(['title' => 'Aviso público']);
        Announcement::factory()->for($church)->create(['title' => 'Rascunho interno']);
        Announcement::factory()->for($other)->published()->create(['title' => 'Aviso da outra']);

        $this->getJson('/api/v1/churches/icvb/announcements')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Aviso público')
            ->assertJsonMissing(['title' => 'Rascunho interno'])
            ->assertJsonMissing(['title' => 'Aviso da outra']);
    }

    public function test_public_roster_returns_upcoming_entries_of_the_church(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-08-20 10:00:00', 'America/Fortaleza'));

        $church = Church::factory()->create(['slug' => 'icvb']);
        $member = Member::factory()->for($church)->create(['name' => 'Clara']);
        RosterEntry::factory()->for($church)->forMember($member)->create([
            'service_date' => '2026-08-30',
            'ministry' => 'Louvor',
            'role' => 'Vocal',
        ]);
        RosterEntry::factory()->for($church)->create([
            'service_date' => '2026-08-02',
            'ministry' => 'Passado',
            'person_name' => 'Antigo',
        ]);

        $this->getJson('/api/v1/churches/icvb/roster')
            ->assertOk()
            ->assertJsonPath('data.0.person_name', 'Clara')
            ->assertJsonPath('data.0.ministry', 'Louvor')
            ->assertJsonMissing(['ministry' => 'Passado']);
    }

    public function test_members_and_contributions_require_sanctum_and_church_access(): void
    {
        $church = Church::factory()->create(['slug' => 'icvb']);
        $other = Church::factory()->create(['slug' => 'outra']);
        Member::factory()->for($church)->create(['name' => 'Ana']);
        Contribution::factory()->for($church)->create(['amount' => '80.00']);

        $this->getJson('/api/v1/churches/icvb/members')->assertUnauthorized();
        $this->getJson('/api/v1/churches/icvb/contributions')->assertUnauthorized();

        $this->seed(RoleSeeder::class);

        $editor = User::factory()->editor()->create();
        $editor->churches()->attach($church);

        Sanctum::actingAs($editor);

        $this->getJson('/api/v1/churches/icvb/members')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Ana');

        $this->getJson('/api/v1/churches/icvb/contributions')
            ->assertOk()
            ->assertJsonPath('data.0.amount', '80.00');

        $this->getJson('/api/v1/churches/outra/members')
            ->assertForbidden();

        $outsider = User::factory()->editor()->create();

        Sanctum::actingAs($outsider);

        $this->getJson('/api/v1/churches/icvb/members')
            ->assertForbidden();
    }

    public function test_legacy_bulletin_routes_still_use_the_first_church(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-08-24 14:15:00', 'America/Fortaleza'));

        $first = Church::factory()->create(['slug' => 'primeira']);
        $second = Church::factory()->create(['slug' => 'segunda']);
        Bulletin::factory()->for($first)->published()->forMonth(2026, 8)->create(['theme' => 'Primeira']);
        Bulletin::factory()->for($second)->published()->forMonth(2026, 8)->create(['theme' => 'Segunda']);
        ScheduleItem::query()->create([
            'bulletin_id' => Bulletin::query()->where('theme', 'Primeira')->value('id'),
            'day_label' => 'DOM 02',
            'description' => 'Culto',
            'sort_order' => 0,
        ]);

        $this->getJson('/api/v1/bulletins/current')
            ->assertOk()
            ->assertJsonPath('data.theme', 'Primeira')
            ->assertJsonPath('data.church.slug', 'primeira');
    }
}
