<?php

namespace App\Models;

use App\Models\Concerns\LogsSoliActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['bulletin_id', 'event_date', 'weekday_label', 'title', 'subtitle', 'sort_order'])]
class SpecialEvent extends Model
{
    use LogsSoliActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'sort_order' => 'integer',
        ];
    }

    public function bulletin(): BelongsTo
    {
        return $this->belongsTo(Bulletin::class);
    }
}
