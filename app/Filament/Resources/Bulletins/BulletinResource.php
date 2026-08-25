<?php

namespace App\Filament\Resources\Bulletins;

use App\Filament\Resources\Bulletins\Pages\CreateBulletin;
use App\Filament\Resources\Bulletins\Pages\EditBulletin;
use App\Filament\Resources\Bulletins\Pages\ListBulletins;
use App\Filament\Resources\Bulletins\Schemas\BulletinForm;
use App\Filament\Resources\Bulletins\Tables\BulletinsTable;
use App\Models\Bulletin;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BulletinResource extends Resource
{
    protected static ?string $model = Bulletin::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $recordTitleAttribute = 'theme';

    protected static ?string $modelLabel = 'boletim';

    protected static ?string $pluralModelLabel = 'boletins';

    protected static ?string $navigationLabel = 'Boletins';

    protected static string|\UnitEnum|null $navigationGroup = 'Conteúdo';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return BulletinForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BulletinsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBulletins::route('/'),
            'create' => CreateBulletin::route('/create'),
            'edit' => EditBulletin::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
