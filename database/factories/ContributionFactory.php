<?php

namespace Database\Factories;

use App\Enums\ContributionType;
use App\Models\Church;
use App\Models\Contribution;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contribution>
 */
class ContributionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'church_id' => Church::factory(),
            'occurred_on' => '2026-08-10',
            'type' => ContributionType::Offering,
            'amount' => '150.00',
            'description' => 'Oferta do culto',
        ];
    }

    public function tithe(): static
    {
        return $this->state(fn () => [
            'type' => ContributionType::Tithe,
            'description' => 'Dízimo',
        ]);
    }
}
