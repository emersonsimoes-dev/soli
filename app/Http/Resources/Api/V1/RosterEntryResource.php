<?php

namespace App\Http\Resources\Api\V1;

use App\Models\RosterEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin RosterEntry
 */
class RosterEntryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_date' => $this->service_date?->toDateString(),
            'ministry' => $this->ministry,
            'role' => $this->role,
            'person_name' => $this->displayName(),
            'member_id' => $this->member_id,
        ];
    }
}
