<?php

namespace App\Filament\Actions;

use App\Enums\BulletinStatus;
use App\Models\Bulletin;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class UnpublishBulletinAction
{
    public static function make(): Action
    {
        return Action::make('unpublish')
            ->label('Despublicar')
            ->icon(Heroicon::OutlinedArrowUturnLeft)
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Despublicar boletim')
            ->modalDescription('O boletim volta a rascunho e deixa de aparecer na página pública.')
            ->visible(fn (?Bulletin $record): bool => $record?->status === BulletinStatus::Published)
            ->action(fn (Bulletin $record) => $record->unpublish())
            ->successNotificationTitle('Boletim despublicado');
    }
}
