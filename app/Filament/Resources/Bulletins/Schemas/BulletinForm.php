<?php

namespace App\Filament\Resources\Bulletins\Schemas;

use App\Models\Bulletin;
use App\Models\Church;
use App\Rules\UniqueBulletinPeriod;
use App\Support\CurrentMonth;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class BulletinForm
{
    public static function configure(Schema $schema): Schema
    {
        $current = CurrentMonth::in();

        return $schema
            ->components([
                Tabs::make('boletim')
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Geral')
                            ->schema([
                                Select::make('church_id')
                                    ->label('Congregação')
                                    ->relationship('church', 'name')
                                    ->default(fn () => Church::query()->value('id'))
                                    ->required()
                                    ->preload(),
                                TextInput::make('year')
                                    ->label('Ano')
                                    ->numeric()
                                    ->minValue(2020)
                                    ->maxValue(2100)
                                    ->default($current->year)
                                    ->required(),
                                Select::make('month')
                                    ->label('Mês')
                                    ->options(Bulletin::monthOptions())
                                    ->default($current->month)
                                    ->required()
                                    ->rule(fn (Get $get, ?Bulletin $record) => new UniqueBulletinPeriod(
                                        churchId: $get('church_id') ? (int) $get('church_id') : null,
                                        year: $get('year') ? (int) $get('year') : null,
                                        ignoreId: $record?->getKey(),
                                    )),
                                TextInput::make('theme')
                                    ->label('Tema')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ])
                            ->columns(3),
                        Tab::make('Programação')
                            ->schema([
                                Repeater::make('scheduleItems')
                                    ->relationship()
                                    ->orderColumn('sort_order')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['day_label'] ?? null)
                                    ->defaultItems(0)
                                    ->schema([
                                        TextInput::make('day_label')
                                            ->label('Dia')
                                            ->required()
                                            ->maxLength(32),
                                        TextInput::make('description')
                                            ->label('Descrição')
                                            ->required()
                                            ->columnSpan(2),
                                        Checkbox::make('is_highlight')
                                            ->label('Destaque'),
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Eventos')
                            ->schema([
                                Repeater::make('specialEvents')
                                    ->relationship()
                                    ->orderColumn('sort_order')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                    ->defaultItems(0)
                                    ->schema([
                                        DatePicker::make('event_date')
                                            ->label('Data')
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, mixed $state): void {
                                                if (blank($state)) {
                                                    return;
                                                }

                                                $set(
                                                    'weekday_label',
                                                    mb_strtoupper(Carbon::parse($state)->locale('pt_BR')->isoFormat('ddd')),
                                                );
                                            }),
                                        TextInput::make('weekday_label')
                                            ->label('Dia da semana')
                                            ->required()
                                            ->maxLength(16),
                                        TextInput::make('title')
                                            ->label('Título')
                                            ->required()
                                            ->columnSpan(2),
                                        TextInput::make('subtitle')
                                            ->label('Subtítulo')
                                            ->columnSpan(2),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Escala de servir')
                            ->schema([
                                Repeater::make('serviceRosters')
                                    ->relationship()
                                    ->orderColumn('sort_order')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => isset($state['service_date']) ? (string) $state['service_date'] : null)
                                    ->defaultItems(0)
                                    ->schema([
                                        DatePicker::make('service_date')
                                            ->label('Data do culto')
                                            ->required(),
                                        TextInput::make('introducers')->label('Apresentação'),
                                        TextInput::make('offertory')->label('Ofertório'),
                                        TextInput::make('leaders')->label('Dirigentes'),
                                        TextInput::make('preachers')->label('Pregadores'),
                                        TextInput::make('support')->label('Apoio'),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Culto infantil')
                            ->schema([
                                Repeater::make('childrenMinistryRosters')
                                    ->relationship()
                                    ->orderColumn('sort_order')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => isset($state['service_date']) ? (string) $state['service_date'] : null)
                                    ->defaultItems(0)
                                    ->schema([
                                        DatePicker::make('service_date')
                                            ->label('Data')
                                            ->required(),
                                        TextInput::make('nursery')->label('Berçário'),
                                        TextInput::make('primary_class')->label('Primários'),
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('EBD')
                            ->schema([
                                Repeater::make('ebdClasses')
                                    ->relationship()
                                    ->orderColumn('sort_order')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['class_name'] ?? null)
                                    ->defaultItems(0)
                                    ->schema([
                                        TextInput::make('class_name')
                                            ->label('Classe')
                                            ->required(),
                                        Textarea::make('teachers_text')
                                            ->label('Professores')
                                            ->required()
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Aniversariantes')
                            ->schema([
                                Repeater::make('birthdays')
                                    ->relationship()
                                    ->orderColumn('sort_order')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                    ->defaultItems(0)
                                    ->schema([
                                        TextInput::make('day')
                                            ->label('Dia')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(31)
                                            ->required(),
                                        TextInput::make('name')
                                            ->label('Nome')
                                            ->required()
                                            ->columnSpan(2),
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
