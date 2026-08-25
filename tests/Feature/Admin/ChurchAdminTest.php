<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Churches\Pages\EditChurch;
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
        $editor = $this->makeEditor();
        $church = Church::factory()->create([
            'name' => 'Templo antigo',
            'pix_key' => 'chave-antiga',
            'settings' => [],
        ]);

        Livewire::actingAs($editor)
            ->test(EditChurch::class, ['record' => $church->getKey()])
            ->fillForm([
                'name' => 'Igreja Congregacional Vale da Benção',
                'short_name' => 'ICVB',
                'slug' => $church->slug,
                'timezone' => 'America/Fortaleza',
                'pix_key' => '50.208.029/0001-31',
                'settings' => [
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
    }

    public function test_editor_cannot_create_a_church(): void
    {
        $editor = $this->makeEditor();

        $this->assertFalse($editor->can('create', Church::class));
    }
}
