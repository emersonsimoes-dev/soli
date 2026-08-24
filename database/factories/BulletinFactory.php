<?php

namespace Database\Factories;

use App\Enums\BulletinStatus;
use App\Models\Bulletin;
use App\Models\Church;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bulletin>
 */
class BulletinFactory extends Factory
{
    public function definition(): array
    {
        return [
            'church_id' => Church::factory(),
            'year' => 2026,
            'month' => 8,
            'theme' => 'Igreja em Ação',
            'status' => BulletinStatus::Draft,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => BulletinStatus::Published,
            'published_at' => now('America/Fortaleza'),
        ]);
    }

    public function forMonth(int $year, int $month): static
    {
        return $this->state(fn () => [
            'year' => $year,
            'month' => $month,
        ]);
    }
}
