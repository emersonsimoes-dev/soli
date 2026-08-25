<?php

namespace App\Filament\Resources\Contributions;

use App\Filament\Resources\Contributions\Pages\CreateContribution;
use App\Filament\Resources\Contributions\Pages\EditContribution;
use App\Filament\Resources\Contributions\Pages\ListContributions;
use App\Filament\Resources\Contributions\Schemas\ContributionForm;
use App\Filament\Resources\Contributions\Tables\ContributionsTable;
use App\Models\Contribution;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContributionResource extends Resource
{
    protected static ?string $model = Contribution::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $modelLabel = 'lançamento';

    protected static ?string $pluralModelLabel = 'finanças';

    protected static ?string $navigationLabel = 'Finanças';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestão';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return ContributionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContributionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContributions::route('/'),
            'create' => CreateContribution::route('/create'),
            'edit' => EditContribution::route('/{record}/edit'),
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
