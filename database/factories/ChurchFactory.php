<?php

namespace Database\Factories;

use App\Models\Church;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Church>
 */
class ChurchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Igreja Congregacional Vale da Benção',
            'short_name' => 'ICVB',
            'slug' => fake()->unique()->slug(2),
            'timezone' => 'America/Fortaleza',
            'pix_key' => '50.208.029/0001-31',
            'logo_path' => null,
            'settings' => [],
        ];
    }

    public function withCustomLogo(): static
    {
        return $this->state(fn () => [
            'logo_path' => 'churches/custom-logo.png',
        ]);
    }
}
