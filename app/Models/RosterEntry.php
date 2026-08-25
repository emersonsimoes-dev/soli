<?php

namespace App\Models;

use App\Models\Concerns\BelongsToChurch;
use App\Models\Concerns\LogsSoliActivity;
use Database\Factories\RosterEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['church_id', 'member_id', 'ministry', 'role', 'service_date', 'person_name', 'notes'])]
class RosterEntry extends Model
{
    /** @use HasFactory<RosterEntryFactory> */
    use BelongsToChurch, HasFactory, LogsSoliActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function displayName(): string
    {
        return $this->member?->name ?? $this->person_name ?? '—';
    }
}
