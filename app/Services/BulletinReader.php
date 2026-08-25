<?php

namespace App\Services;

use App\Enums\BulletinStatus;
use App\Models\Bulletin;
use App\Models\Church;
use App\Support\CurrentMonth;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

class BulletinReader
{
    public function publishedFor(Church $church, ?CarbonInterface $at = null): ?Bulletin
    {
        $current = $at
            ? CurrentMonth::at($at, $church->timezone)
            : CurrentMonth::in($church->timezone);

        return $this->publishedQuery($church)
            ->where('year', $current->year)
            ->where('month', $current->month)
            ->first();
    }

    public function publishedForPeriod(Church $church, int $year, int $month): ?Bulletin
    {
        return $this->publishedQuery($church)
            ->where('year', $year)
            ->where('month', $month)
            ->first();
    }

    /**
     * @return Builder<Bulletin>
     */
    private function publishedQuery(Church $church): Builder
    {
        return Bulletin::query()
            ->where('church_id', $church->id)
            ->where('status', BulletinStatus::Published)
            ->with([
                'church',
                'scheduleItems',
                'specialEvents',
                'serviceRosters',
                'childrenMinistryRosters',
                'ebdClasses',
                'birthdays',
            ]);
    }
}
