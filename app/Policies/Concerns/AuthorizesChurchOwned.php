<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait AuthorizesChurchOwned
{
    public function viewAny(User $user): bool
    {
        return $user->isEditor() || $user->isAdmin();
    }

    public function view(User $user, object $record): bool
    {
        return $user->canAccessChurch($record->church_id);
    }

    public function create(User $user): bool
    {
        return $user->isEditor() || $user->isAdmin();
    }

    public function update(User $user, object $record): bool
    {
        return $user->canAccessChurch($record->church_id);
    }

    public function delete(User $user, object $record): bool
    {
        return $user->canAccessChurch($record->church_id);
    }

    public function restore(User $user, object $record): bool
    {
        return $user->isAdmin() && $user->canAccessChurch($record->church_id);
    }

    public function forceDelete(User $user, object $record): bool
    {
        return $user->isAdmin() && $user->canAccessChurch($record->church_id);
    }
}
