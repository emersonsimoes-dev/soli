<?php

namespace App\Models;

use App\Enums\MemberStatus;
use App\Models\Concerns\BelongsToChurch;
use App\Models\Concerns\LogsSoliActivity;
use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['church_id', 'name', 'email', 'phone', 'birth_day', 'birth_month', 'status', 'notes'])]
class Member extends Model
{
    /** @use HasFactory<MemberFactory> */
    use BelongsToChurch, HasFactory, LogsSoliActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'birth_day' => 'integer',
            'birth_month' => 'integer',
            'status' => MemberStatus::class,
        ];
    }

    public function rosterEntries(): HasMany
    {
        return $this->hasMany(RosterEntry::class);
    }
}
