<?php

namespace App\Models;

use App\Enums\AnnouncementStatus;
use App\Models\Concerns\BelongsToChurch;
use App\Models\Concerns\LogsSoliActivity;
use Database\Factories\AnnouncementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['church_id', 'title', 'body', 'status', 'published_at'])]
class Announcement extends Model
{
    /** @use HasFactory<AnnouncementFactory> */
    use BelongsToChurch, HasFactory, LogsSoliActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'status' => AnnouncementStatus::class,
            'published_at' => 'datetime',
        ];
    }

    public function isPublished(): bool
    {
        return $this->status === AnnouncementStatus::Published;
    }

    public function publish(): void
    {
        $this->update([
            'status' => AnnouncementStatus::Published,
            'published_at' => $this->published_at ?? now('America/Fortaleza'),
        ]);
    }

    public function unpublish(): void
    {
        $this->update([
            'status' => AnnouncementStatus::Draft,
            'published_at' => null,
        ]);
    }
}
