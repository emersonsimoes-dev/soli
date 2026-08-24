<?php

namespace Tests\Concerns;

use App\Models\User;
use Database\Seeders\RoleSeeder;

trait CreatesPanelUsers
{
    protected function seedRoles(): void
    {
        $this->seed(RoleSeeder::class);
    }

    protected function makeAdmin(array $attributes = []): User
    {
        $this->seedRoles();

        return User::factory()->admin()->create($attributes);
    }

    protected function makeEditor(array $attributes = []): User
    {
        $this->seedRoles();

        return User::factory()->editor()->create($attributes);
    }
}
