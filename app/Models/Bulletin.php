<?php

namespace App\Models;

use App\Enums\BulletinStatus;
use Database\Factories\BulletinFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['church_id', 'year', 'month', 'theme', 'status', 'published_at'])]
class Bulletin extends Model
{
    /** @use HasFactory<BulletinFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'month' => 'integer',
            'status' => BulletinStatus::class,
            'published_at' => 'datetime',
        ];
    }

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }

    public function scheduleItems(): HasMany
    {
        return $this->hasMany(ScheduleItem::class)->orderBy('sort_order');
    }

    public function specialEvents(): HasMany
    {
        return $this->hasMany(SpecialEvent::class)->orderBy('sort_order');
    }

    public function serviceRosters(): HasMany
    {
        return $this->hasMany(ServiceRoster::class)->orderBy('sort_order');
    }

    public function childrenMinistryRosters(): HasMany
    {
        return $this->hasMany(ChildrenMinistryRoster::class)->orderBy('sort_order');
    }

    public function ebdClasses(): HasMany
    {
        return $this->hasMany(EbdClass::class)->orderBy('sort_order');
    }

    public function birthdays(): HasMany
    {
        return $this->hasMany(Birthday::class)->orderBy('sort_order');
    }

    public function isPublished(): bool
    {
        return $this->status === BulletinStatus::Published;
    }

    /**
     * @return array{schedule: int, events: int, services: int, birthdays: int}
     */
    public function kpiCounts(): array
    {
        return [
            'schedule' => $this->scheduleItems->count(),
            'events' => $this->specialEvents->count(),
            'services' => $this->serviceRosters->count(),
            'birthdays' => $this->birthdays->count(),
        ];
    }
}
