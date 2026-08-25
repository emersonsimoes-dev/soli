<?php

namespace App\Filament\Resources\Announcements\Pages;

use App\Enums\AnnouncementStatus;
use App\Filament\Resources\Announcements\AnnouncementResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['church_id'] = Filament::getTenant()?->getKey();
        $data['status'] ??= AnnouncementStatus::Draft->value;

        if (($data['status'] ?? null) === AnnouncementStatus::Published->value) {
            $data['published_at'] = now('America/Fortaleza');
        }

        return $data;
    }
}
