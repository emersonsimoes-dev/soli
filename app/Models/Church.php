<?php

namespace App\Models;

use App\Models\Concerns\LogsSoliActivity;
use Database\Factories\ChurchFactory;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'short_name', 'slug', 'timezone', 'pix_key', 'logo_path', 'settings'])]
class Church extends Model implements HasName
{
    /** @use HasFactory<ChurchFactory> */
    use HasFactory, LogsSoliActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }

    public function getFilamentName(): string
    {
        return $this->short_name ?: $this->name;
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function bulletins(): HasMany
    {
        return $this->hasMany(Bulletin::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function rosterEntries(): HasMany
    {
        return $this->hasMany(RosterEntry::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function usesDefaultLogo(): bool
    {
        return blank($this->logo_path);
    }

    public function logoUrl(): string
    {
        if ($this->logo_path) {
            return Storage::disk('public')->url($this->logo_path);
        }

        return asset((string) config('soli.default_logo'));
    }
}
