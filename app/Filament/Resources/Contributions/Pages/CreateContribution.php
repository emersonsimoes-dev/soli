<?php

namespace App\Filament\Resources\Contributions\Pages;

use App\Filament\Resources\Contributions\ContributionResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateContribution extends CreateRecord
{
    protected static string $resource = ContributionResource::class;

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
