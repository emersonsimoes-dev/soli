<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Churches\Schemas\ChurchForm;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Schema;

class EditChurchProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Cadastro da congregação';
    }

    public function form(Schema $schema): Schema
    {
        return ChurchForm::configure($schema);
    }
}
