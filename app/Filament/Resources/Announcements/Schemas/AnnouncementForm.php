<?php

namespace App\Filament\Resources\Announcements\Schemas;

use App\Enums\AnnouncementStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('body')
                    ->label('Texto')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Status')
                    ->options(collect(AnnouncementStatus::cases())->mapWithKeys(
                        fn (AnnouncementStatus $status) => [$status->value => $status->label()],
                    ))
                    ->default(AnnouncementStatus::Draft->value)
                    ->required(),
            ]);
    }
}
