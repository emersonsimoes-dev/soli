<?php

namespace Database\Factories;

use App\Models\Church;
use App\Models\Member;
use App\Models\RosterEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RosterEntry>
 */
class RosterEntryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'church_id' => Church::factory(),
            'member_id' => null,
            'ministry' => 'Louvor',
            'role' => 'Vocal',
            'service_date' => '2026-08-30',
            'person_name' => fake()->name(),
            'notes' => null,
        ];
    }

    public function forMember(Member $member): static
    {
        return $this->state(fn () => [
            'church_id' => $member->church_id,
            'member_id' => $member->id,
            'person_name' => null,
        ]);
    }
}
