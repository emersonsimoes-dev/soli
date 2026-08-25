<?php

namespace App\Models;

use App\Models\Concerns\LogsSoliActivity;
use Database\Factories\ChurchFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'short_name', 'slug', 'timezone', 'pix_key', 'logo_path', 'settings'])]
class Church extends Model
{
    /** @use HasFactory<ChurchFactory> */
    use HasFactory, LogsSoliActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }

    public function bulletins(): HasMany
    {
        return $this->hasMany(Bulletin::class);
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
