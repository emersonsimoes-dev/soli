<?php

namespace App\Filament\Actions;

use App\Enums\AnnouncementStatus;
use App\Models\Announcement;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class PublishAnnouncementAction
{
    public static function make(): Action
    {
        return Action::make('publish')
            ->label('Publicar')
            ->icon(Heroicon::OutlinedCheckCircle)
            ->color('success')
            ->requiresConfirmation()
            ->visible(fn (?Announcement $record): bool => $record?->status === AnnouncementStatus::Draft)
            ->action(fn (Announcement $record) => $record->publish())
            ->successNotificationTitle('Aviso publicado');
    }
}
