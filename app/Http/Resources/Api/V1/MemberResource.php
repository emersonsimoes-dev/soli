<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Member
 */
class MemberResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'birth_day' => $this->birth_day,
            'birth_month' => $this->birth_month,
            'status' => $this->status->value,
            'notes' => $this->notes,
        ];
    }
}
