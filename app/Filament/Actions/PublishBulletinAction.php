<?php

namespace App\Filament\Actions;

use App\Enums\BulletinStatus;
use App\Models\Bulletin;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class PublishBulletinAction
{
    public static function make(): Action
    {
        return Action::make('publish')
            ->label('Publicar')
            ->icon(Heroicon::OutlinedCheckCircle)
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Publicar boletim')
            ->modalDescription('O boletim passará a aparecer na home no mês vigente, se o ano e o mês coincidirem com a data em Fortaleza.')
            ->visible(fn (?Bulletin $record): bool => $record?->status === BulletinStatus::Draft)
            ->action(fn (Bulletin $record) => $record->publish())
            ->successNotificationTitle('Boletim publicado');
    }
}
