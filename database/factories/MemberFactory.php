<?php

namespace Database\Factories;

use App\Enums\MemberStatus;
use App\Models\Church;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'church_id' => Church::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('(85) 9####-####'),
            'birth_day' => fake()->numberBetween(1, 28),
            'birth_month' => fake()->numberBetween(1, 12),
            'status' => MemberStatus::Active,
            'notes' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'status' => MemberStatus::Inactive,
        ]);
    }
}
