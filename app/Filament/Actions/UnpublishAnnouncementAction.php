<?php

namespace App\Filament\Actions;

use App\Enums\AnnouncementStatus;
use App\Models\Announcement;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class UnpublishAnnouncementAction
{
    public static function make(): Action
    {
        return Action::make('unpublish')
            ->label('Despublicar')
            ->icon(Heroicon::OutlinedEyeSlash)
            ->color('gray')
            ->requiresConfirmation()
            ->visible(fn (?Announcement $record): bool => $record?->status === AnnouncementStatus::Published)
            ->action(fn (Announcement $record) => $record->unpublish())
            ->successNotificationTitle('Aviso despublicado');
    }
}
