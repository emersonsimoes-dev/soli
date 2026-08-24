<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        User::created(function (User $user): void {
            if (app()->runningUnitTests()) {
                return;
            }

            if ($user->roles()->exists()) {
                return;
            }

            if (! Role::query()->where('name', UserRole::Admin->value)->exists()) {
                return;
            }

            if (User::query()->count() === 1) {
                $user->assignRole(UserRole::Admin->value);
            }
        });
    }
}
