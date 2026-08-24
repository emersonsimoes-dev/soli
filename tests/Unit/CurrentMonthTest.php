<?php

namespace Tests\Unit;

use App\Support\CurrentMonth;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CurrentMonthTest extends TestCase
{
    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_uses_fortaleza_calendar_month(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-08-24 14:15:00', 'America/Fortaleza'));

        $current = CurrentMonth::in('America/Fortaleza');

        $this->assertSame(2026, $current->year);
        $this->assertSame(8, $current->month);
        $this->assertStringContainsString('agosto', mb_strtolower($current->label()));
        $this->assertStringContainsString('2026', $current->label());
    }

    public function test_august_31_in_fortaleza_stays_in_august(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-08-31 23:59:59', 'America/Fortaleza'));

        $current = CurrentMonth::in('America/Fortaleza');

        $this->assertSame(8, $current->month);
        $this->assertTrue($current->equals(2026, 8));
    }

    public function test_september_first_in_fortaleza_switches_month(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-09-01 00:00:00', 'America/Fortaleza'));

        $current = CurrentMonth::in('America/Fortaleza');

        $this->assertSame(2026, $current->year);
        $this->assertSame(9, $current->month);
    }

    #[DataProvider('utcBoundaryProvider')]
    public function test_converts_utc_instant_into_fortaleza_month(string $utc, int $month): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse($utc, 'UTC'));

        $current = CurrentMonth::in('America/Fortaleza');

        $this->assertSame($month, $current->month);
    }

    /**
     * @return array<string, array{string, int}>
     */
    public static function utcBoundaryProvider(): array
    {
        return [
            'ainda 31/08 em Fortaleza' => ['2026-09-01 02:59:59', 8],
            'já 01/09 em Fortaleza' => ['2026-09-01 03:00:00', 9],
        ];
    }
}
