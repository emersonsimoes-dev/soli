<?php

namespace App\Filament\Resources\RosterEntries\Pages;

use App\Filament\Resources\RosterEntries\RosterEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRosterEntries extends ListRecords
{
    protected static string $resource = RosterEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
