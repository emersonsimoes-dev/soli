<?php

namespace App\Filament\Resources\Bulletins\Pages;

use App\Filament\Actions\PublishBulletinAction;
use App\Filament\Actions\UnpublishBulletinAction;
use App\Filament\Resources\Bulletins\BulletinResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditBulletin extends EditRecord
{
    protected static string $resource = BulletinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PublishBulletinAction::make(),
            UnpublishBulletinAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
