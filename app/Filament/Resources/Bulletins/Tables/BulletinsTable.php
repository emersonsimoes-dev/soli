<?php

namespace App\Filament\Resources\Bulletins\Tables;

use App\Enums\BulletinStatus;
use App\Filament\Actions\PublishBulletinAction;
use App\Filament\Actions\UnpublishBulletinAction;
use App\Models\Bulletin;
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
use Illuminate\Database\Eloquent\Builder;

class BulletinsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->orderByDesc('year')->orderByDesc('month'))
            ->columns([
                TextColumn::make('year')
                    ->label('Ano')
                    ->sortable(),
                TextColumn::make('month')
                    ->label('Mês')
                    ->formatStateUsing(fn (int $state): string => Bulletin::monthLabel($state))
                    ->sortable(),
                TextColumn::make('theme')
                    ->label('Tema')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (BulletinStatus $state): string => $state->label())
                    ->color(fn (BulletinStatus $state): string => $state->color()),
                TextColumn::make('published_at')
                    ->label('Publicado em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('church.short_name')
                    ->label('Congregação')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(BulletinStatus::cases())->mapWithKeys(
                        fn (BulletinStatus $status) => [$status->value => $status->label()],
                    )),
                SelectFilter::make('year')
                    ->label('Ano')
                    ->options(fn () => Bulletin::query()
                        ->distinct()
                        ->orderByDesc('year')
                        ->pluck('year', 'year')
                        ->all()),
                TrashedFilter::make(),
            ])
            ->recordActions([
                PublishBulletinAction::make(),
                UnpublishBulletinAction::make(),
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
