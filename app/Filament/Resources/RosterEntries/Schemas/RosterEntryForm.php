<?php

namespace App\Filament\Resources\RosterEntries\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RosterEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('service_date')
                    ->label('Data')
                    ->required(),
                TextInput::make('ministry')
                    ->label('Ministério')
                    ->required()
                    ->maxLength(255),
                TextInput::make('role')
                    ->label('Função')
                    ->required()
                    ->maxLength(255),
                Select::make('member_id')
                    ->label('Membro')
                    ->relationship('member', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('person_name')
                    ->label('Nome (se não for membro)')
                    ->maxLength(255)
                    ->helperText('Use quando a pessoa ainda não está no cadastro de membros.'),
                Textarea::make('notes')
                    ->label('Observações')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }
}
