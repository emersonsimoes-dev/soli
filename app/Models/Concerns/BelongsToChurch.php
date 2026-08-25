<?php

namespace App\Models\Concerns;

use App\Models\Church;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToChurch
{
    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }
}
