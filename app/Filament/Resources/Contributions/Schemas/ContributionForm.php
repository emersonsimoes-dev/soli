<?php

namespace App\Filament\Resources\Contributions\Schemas;

use App\Enums\ContributionType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContributionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('occurred_on')
                    ->label('Data')
                    ->default(now('America/Fortaleza'))
                    ->required(),
                Select::make('type')
                    ->label('Tipo')
                    ->options(collect(ContributionType::cases())->mapWithKeys(
                        fn (ContributionType $type) => [$type->value => $type->label()],
                    ))
                    ->required(),
                TextInput::make('amount')
                    ->label('Valor')
                    ->numeric()
                    ->prefix('R$')
                    ->minValue(0)
                    ->step(0.01)
                    ->required(),
                TextInput::make('description')
                    ->label('Descrição')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }
}
