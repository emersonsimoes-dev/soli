<?php

namespace App\Filament\Resources\RosterEntries;

use App\Filament\Resources\RosterEntries\Pages\CreateRosterEntry;
use App\Filament\Resources\RosterEntries\Pages\EditRosterEntry;
use App\Filament\Resources\RosterEntries\Pages\ListRosterEntries;
use App\Filament\Resources\RosterEntries\Schemas\RosterEntryForm;
use App\Filament\Resources\RosterEntries\Tables\RosterEntriesTable;
use App\Models\RosterEntry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RosterEntryResource extends Resource
{
    protected static ?string $model = RosterEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $modelLabel = 'escala';

    protected static ?string $pluralModelLabel = 'escalas';

    protected static ?string $navigationLabel = 'Escalas';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestão';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return RosterEntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RosterEntriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRosterEntries::route('/'),
            'create' => CreateRosterEntry::route('/create'),
            'edit' => EditRosterEntry::route('/{record}/edit'),
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
