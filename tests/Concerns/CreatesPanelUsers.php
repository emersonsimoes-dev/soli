<?php

namespace Tests\Concerns;

use App\Models\Church;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;

trait CreatesPanelUsers
{
    protected function seedRoles(): void
    {
        $this->seed(RoleSeeder::class);
    }

    protected function makeAdmin(array $attributes = [], ?Church $church = null): User
    {
        $this->seedRoles();

        $user = User::factory()->admin()->create($attributes);
        $this->actingAsPanel($user, $church);

        return $user;
    }

    protected function makeEditor(array $attributes = [], ?Church $church = null): User
    {
        $this->seedRoles();

        $user = User::factory()->editor()->create($attributes);
        $this->actingAsPanel($user, $church);

        return $user;
    }

    protected function assignChurch(User $user, ?Church $church = null): Church
    {
        $church ??= Church::query()->orderBy('id')->first() ?? Church::factory()->create();

        $user->churches()->syncWithoutDetaching([$church->id]);

        return $church;
    }

    protected function actingAsPanel(User $user, ?Church $church = null): Church
    {
        $church = $this->assignChurch($user, $church ?? $user->churches()->first());

        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($church, isQuiet: true);

        return $church;
    }

    protected function tenantChurch(): Church
    {
        $tenant = Filament::getTenant();

        $this->assertInstanceOf(Church::class, $tenant);

        return $tenant;
    }
}
