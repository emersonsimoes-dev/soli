<?php

namespace App\Filament\Resources\RosterEntries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class RosterEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('service_date')
            ->columns([
                TextColumn::make('service_date')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('ministry')
                    ->label('Ministério')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Função')
                    ->searchable(),
                TextColumn::make('person')
                    ->label('Pessoa')
                    ->state(fn ($record) => $record->displayName())
                    ->searchable(query: function ($query, string $search): void {
                        $query->where('person_name', 'ilike', "%{$search}%")
                            ->orWhereHas('member', fn ($memberQuery) => $memberQuery->where('name', 'ilike', "%{$search}%"));
                    }),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
