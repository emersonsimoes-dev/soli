<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final readonly class CurrentMonth
{
    public function __construct(
        public int $year,
        public int $month,
        public CarbonImmutable $now,
    ) {}

    public static function in(?string $timezone = null): self
    {
        $timezone ??= (string) config('app.timezone');

        return self::at(CarbonImmutable::now($timezone), $timezone);
    }

    public static function at(CarbonInterface $moment, ?string $timezone = null): self
    {
        $timezone ??= (string) config('app.timezone');
        $now = CarbonImmutable::parse($moment)->timezone($timezone);

        return new self(
            year: (int) $now->year,
            month: (int) $now->month,
            now: $now,
        );
    }

    public function label(): string
    {
        $label = $this->now->locale('pt_BR')->translatedFormat('F Y');

        return mb_strtoupper(mb_substr($label, 0, 1)).mb_substr($label, 1);
    }

    public function equals(int $year, int $month): bool
    {
        return $this->year === $year && $this->month === $month;
    }
}
