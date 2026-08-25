<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Registro')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Data')
                            ->dateTime('d/m/Y H:i:s'),
                        TextEntry::make('event')
                            ->label('Ação')
                            ->badge()
                            ->placeholder('—'),
                        TextEntry::make('causer.name')
                            ->label('Usuário')
                            ->placeholder('Sistema'),
                        TextEntry::make('subject_type')
                            ->label('Modelo')
                            ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '—'),
                        TextEntry::make('subject_id')
                            ->label('ID do registro'),
                        TextEntry::make('description')
                            ->label('Descrição'),
                        TextEntry::make('ip')
                            ->label('IP')
                            ->placeholder('—'),
                        TextEntry::make('user_agent')
                            ->label('User-agent')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ]),
                Section::make('Alterações')
                    ->schema([
                        TextEntry::make('properties.old')
                            ->label('Antes')
                            ->formatStateUsing(fn (mixed $state): string => self::pretty($state))
                            ->placeholder('—'),
                        TextEntry::make('properties.attributes')
                            ->label('Depois')
                            ->formatStateUsing(fn (mixed $state): string => self::pretty($state))
                            ->placeholder('—'),
                    ]),
            ]);
    }

    private static function pretty(mixed $state): string
    {
        if (blank($state)) {
            return '—';
        }

        if (is_string($state)) {
            return $state;
        }

        return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '—';
    }
}
