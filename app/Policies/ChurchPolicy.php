<?php

namespace App\Policies;

use App\Models\Church;
use App\Models\User;

class ChurchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isEditor() || $user->isAdmin();
    }

    public function view(User $user, Church $church): bool
    {
        return $user->isEditor() || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Church $church): bool
    {
        return $user->isEditor() || $user->isAdmin();
    }

    public function delete(User $user, Church $church): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Church $church): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Church $church): bool
    {
        return $user->isAdmin();
    }
}
