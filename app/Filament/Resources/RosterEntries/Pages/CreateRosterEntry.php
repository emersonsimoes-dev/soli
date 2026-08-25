<?php

namespace App\Filament\Resources\RosterEntries\Pages;

use App\Filament\Resources\RosterEntries\RosterEntryResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateRosterEntry extends CreateRecord
{
    protected static string $resource = RosterEntryResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['church_id'] = Filament::getTenant()?->getKey();

        return $data;
    }
}
