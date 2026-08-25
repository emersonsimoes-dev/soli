<?php

namespace App\Models;

use App\Enums\ContributionType;
use App\Models\Concerns\BelongsToChurch;
use App\Models\Concerns\LogsSoliActivity;
use Database\Factories\ContributionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['church_id', 'occurred_on', 'type', 'amount', 'description'])]
class Contribution extends Model
{
    /** @use HasFactory<ContributionFactory> */
    use BelongsToChurch, HasFactory, LogsSoliActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'occurred_on' => 'date',
            'type' => ContributionType::class,
            'amount' => 'decimal:2',
        ];
    }
}
