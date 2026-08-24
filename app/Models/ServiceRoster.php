<?php

namespace App\Models;

use App\Models\Concerns\LogsSoliActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['bulletin_id', 'service_date', 'introducers', 'offertory', 'leaders', 'preachers', 'support', 'sort_order'])]
class ServiceRoster extends Model
{
    use LogsSoliActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'sort_order' => 'integer',
        ];
    }

    public function bulletin(): BelongsTo
    {
        return $this->belongsTo(Bulletin::class);
    }
}
