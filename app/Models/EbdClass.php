<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['bulletin_id', 'class_name', 'teachers_text', 'sort_order'])]
class EbdClass extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function bulletin(): BelongsTo
    {
        return $this->belongsTo(Bulletin::class);
    }
}
