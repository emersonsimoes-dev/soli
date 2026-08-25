<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Church;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Church
 */
class ChurchResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'short_name' => $this->short_name,
            'slug' => $this->slug,
            'timezone' => $this->timezone,
            'pix_key' => $this->pix_key,
            'logo_url' => $this->logoUrl(),
            'uses_default_logo' => $this->usesDefaultLogo(),
            'settings' => [
                'contact' => data_get($this->settings, 'contact', []),
                'ministries' => data_get($this->settings, 'ministries', []),
            ],
        ];
    }
}
