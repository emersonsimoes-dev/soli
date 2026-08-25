<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Models\Concerns\LogsSoliActivity;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasDefaultTenant, HasTenants
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, LogsSoliActivity, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function churches(): BelongsToMany
    {
        return $this->belongsToMany(Church::class)->withTimestamps();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole([UserRole::Admin->value, UserRole::Editor->value]);
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $tenant instanceof Church && $this->canAccessChurch($tenant);
    }

    /**
     * @return Collection<int, Church>
     */
    public function getTenants(Panel $panel): Collection
    {
        if ($this->isAdmin()) {
            return Church::query()->orderBy('name')->get();
        }

        return $this->churches()->orderBy('name')->get();
    }

    public function getDefaultTenant(Panel $panel): ?Model
    {
        return $this->getTenants($panel)->first();
    }

    public function canAccessChurch(Church|int $church): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $id = $church instanceof Church ? $church->id : $church;

        return $this->churches()->where('churches.id', $id)->exists();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::Admin->value);
    }

    public function isEditor(): bool
    {
        return $this->hasRole(UserRole::Editor->value);
    }
}
