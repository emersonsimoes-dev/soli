<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Activities\Pages\ListActivities;
use App\Filament\Resources\Bulletins\Pages\ListBulletins;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\CreatesPanelUsers;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use CreatesPanelUsers;
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();
    }

    public function test_user_without_role_cannot_access_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_editor_can_open_bulletins_and_audit_but_not_users(): void
    {
        $editor = $this->makeEditor();

        $this->actingAs($editor);

        $this->get('/admin')->assertOk();

        Livewire::actingAs($editor)
            ->test(ListBulletins::class)
            ->assertOk();

        Livewire::actingAs($editor)
            ->test(ListActivities::class)
            ->assertOk();

        Livewire::actingAs($editor)
            ->test(ListUsers::class)
            ->assertForbidden();
    }

    public function test_admin_can_manage_users(): void
    {
        $admin = $this->makeAdmin();

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->assertOk();
    }
}
