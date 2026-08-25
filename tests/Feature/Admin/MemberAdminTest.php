<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Members\Pages\CreateMember;
use App\Models\Church;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\CreatesPanelUsers;
use Tests\TestCase;

class MemberAdminTest extends TestCase
{
    use CreatesPanelUsers;
    use RefreshDatabase;

    public function test_editor_creates_member_for_current_church(): void
    {
        $church = Church::factory()->create();
        $editor = $this->makeEditor(church: $church);

        Livewire::actingAs($editor)
            ->test(CreateMember::class)
            ->fillForm([
                'name' => 'Ana Clara',
                'email' => 'ana@icvb.test',
                'phone' => '(85) 99999-0001',
                'status' => 'active',
                'birth_day' => 10,
                'birth_month' => 8,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $member = Member::query()->where('email', 'ana@icvb.test')->first();

        $this->assertNotNull($member);
        $this->assertSame($church->id, $member->church_id);
        $this->assertSame('Ana Clara', $member->name);
    }

    public function test_editor_does_not_see_members_from_another_church(): void
    {
        $churchA = Church::factory()->create(['slug' => 'templo-a']);
        $churchB = Church::factory()->create(['slug' => 'templo-b']);
        $editor = $this->makeEditor(church: $churchA);

        Member::factory()->for($churchA)->create(['name' => 'Membro A']);
        Member::factory()->for($churchB)->create(['name' => 'Membro B']);

        $this->actingAs($editor)
            ->get('/admin/'.$churchA->slug.'/members')
            ->assertOk()
            ->assertSee('Membro A')
            ->assertDontSee('Membro B');
    }
}
