<?php

namespace App\Filament\Resources\Members\Schemas;

use App\Enums\MemberStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Telefone')
                    ->maxLength(255),
                Select::make('status')
                    ->label('Status')
                    ->options(collect(MemberStatus::cases())->mapWithKeys(
                        fn (MemberStatus $status) => [$status->value => $status->label()],
                    ))
                    ->default(MemberStatus::Active->value)
                    ->required(),
                TextInput::make('birth_day')
                    ->label('Dia de aniversário')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(31),
                TextInput::make('birth_month')
                    ->label('Mês de aniversário')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(12),
                Textarea::make('notes')
                    ->label('Observações')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
