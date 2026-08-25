<?php

namespace App\Filament\Resources\Announcements\Tables;

use App\Enums\AnnouncementStatus;
use App\Filament\Actions\PublishAnnouncementAction;
use App\Filament\Actions\UnpublishAnnouncementAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AnnouncementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (AnnouncementStatus $state): string => $state->label())
                    ->color(fn (AnnouncementStatus $state): string => $state->color()),
                TextColumn::make('published_at')
                    ->label('Publicado em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(AnnouncementStatus::cases())->mapWithKeys(
                        fn (AnnouncementStatus $status) => [$status->value => $status->label()],
                    )),
                TrashedFilter::make(),
            ])
            ->recordActions([
                PublishAnnouncementAction::make(),
                UnpublishAnnouncementAction::make(),
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
