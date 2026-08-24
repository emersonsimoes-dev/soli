<?php

namespace App\Services;

use App\Enums\BulletinStatus;
use App\Models\Bulletin;
use App\Models\Church;
use App\Support\CurrentMonth;
use Carbon\CarbonInterface;

class BulletinReader
{
    public function publishedFor(Church $church, ?CarbonInterface $at = null): ?Bulletin
    {
        $current = $at
            ? CurrentMonth::at($at, $church->timezone)
            : CurrentMonth::in($church->timezone);

        return Bulletin::query()
            ->where('church_id', $church->id)
            ->where('year', $current->year)
            ->where('month', $current->month)
            ->where('status', BulletinStatus::Published)
            ->with([
                'scheduleItems',
                'specialEvents',
                'serviceRosters',
                'childrenMinistryRosters',
                'ebdClasses',
                'birthdays',
            ])
            ->first();
    }
}
