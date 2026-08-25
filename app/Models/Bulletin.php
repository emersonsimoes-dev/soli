<?php

namespace App\Models;

use App\Enums\BulletinStatus;
use App\Models\Concerns\LogsSoliActivity;
use Carbon\CarbonImmutable;
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
    use HasFactory, LogsSoliActivity, SoftDeletes;

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

    public function publish(): void
    {
        $this->update([
            'status' => BulletinStatus::Published,
            'published_at' => $this->published_at ?? now('America/Fortaleza'),
        ]);
    }

    public function unpublish(): void
    {
        $this->update([
            'status' => BulletinStatus::Draft,
            'published_at' => null,
        ]);
    }

    public static function monthLabel(int $month): string
    {
        $label = CarbonImmutable::create(2000, $month, 1)->locale('pt_BR')->translatedFormat('F');

        return mb_strtoupper(mb_substr($label, 0, 1)).mb_substr($label, 1);
    }

    /**
     * @return array<int, string>
     */
    public static function monthOptions(): array
    {
        return collect(range(1, 12))
            ->mapWithKeys(fn (int $month) => [$month => self::monthLabel($month)])
            ->all();
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
