<?php

namespace Database\Factories;

use App\Enums\AnnouncementStatus;
use App\Models\Announcement;
use App\Models\Church;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'church_id' => Church::factory(),
            'title' => 'Aviso da semana',
            'body' => 'Culto especial no sábado às 19h.',
            'status' => AnnouncementStatus::Draft,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => AnnouncementStatus::Published,
            'published_at' => now('America/Fortaleza'),
        ]);
    }
}
