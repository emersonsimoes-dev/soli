<?php

namespace App\Filament\Resources\Bulletins\Pages;

use App\Enums\BulletinStatus;
use App\Filament\Resources\Bulletins\BulletinResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateBulletin extends CreateRecord
{
    protected static string $resource = BulletinResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = BulletinStatus::Draft;
        $data['published_at'] = null;
        $data['church_id'] = $data['church_id'] ?? Filament::getTenant()?->getKey();

        return $data;
    }
}
