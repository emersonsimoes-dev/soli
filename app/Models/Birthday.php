<?php

namespace App\Models;

use App\Models\Concerns\LogsSoliActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['bulletin_id', 'day', 'name', 'sort_order'])]
class Birthday extends Model
{
    use LogsSoliActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'day' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function bulletin(): BelongsTo
    {
        return $this->belongsTo(Bulletin::class);
    }
}
