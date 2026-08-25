<?php

namespace App\Policies;

use App\Models\Bulletin;
use App\Models\User;

class BulletinPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isEditor() || $user->isAdmin();
    }

    public function view(User $user, Bulletin $bulletin): bool
    {
        return $user->canAccessChurch($bulletin->church_id);
    }

    public function create(User $user): bool
    {
        return $user->isEditor() || $user->isAdmin();
    }

    public function update(User $user, Bulletin $bulletin): bool
    {
        return $user->canAccessChurch($bulletin->church_id);
    }

    public function delete(User $user, Bulletin $bulletin): bool
    {
        return $user->canAccessChurch($bulletin->church_id);
    }

    public function restore(User $user, Bulletin $bulletin): bool
    {
        return $user->isAdmin() && $user->canAccessChurch($bulletin->church_id);
    }

    public function forceDelete(User $user, Bulletin $bulletin): bool
    {
        return $user->isAdmin() && $user->canAccessChurch($bulletin->church_id);
    }
}
