<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\EditChurchProfile;
use App\Models\Church;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\CreatesPanelUsers;
use Tests\TestCase;

class ChurchAdminTest extends TestCase
{
    use CreatesPanelUsers;
    use RefreshDatabase;

    public function test_editor_can_update_church_registration(): void
    {
        $church = Church::factory()->create([
            'name' => 'Templo antigo',
            'pix_key' => 'chave-antiga',
            'settings' => [],
        ]);
        $editor = $this->makeEditor(church: $church);

        Livewire::actingAs($editor)
            ->test(EditChurchProfile::class)
            ->fillForm([
                'name' => 'Igreja Congregacional Vale da Benção',
                'short_name' => 'ICVB',
                'slug' => $church->slug,
                'timezone' => 'America/Fortaleza',
                'pix_key' => '50.208.029/0001-31',
                'settings' => [
                    'contact' => [
                        'phone' => '(85) 99999-0000',
                        'email' => 'contato@icvb.test',
                        'address' => 'Fortaleza, CE',
                    ],
                    'ministries' => [
                        ['name' => 'Ação Social', 'description' => 'Bazar e visitas'],
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $church->refresh();

        $this->assertSame('Igreja Congregacional Vale da Benção', $church->name);
        $this->assertSame('50.208.029/0001-31', $church->pix_key);
        $this->assertSame('Ação Social', data_get($church->settings, 'ministries.0.name'));
        $this->assertSame('contato@icvb.test', data_get($church->settings, 'contact.email'));
    }

    public function test_editor_cannot_create_a_church(): void
    {
        $editor = $this->makeEditor();

        $this->assertFalse($editor->can('create', Church::class));
        $this->assertFalse($editor->can('viewAny', Church::class));
    }
}
