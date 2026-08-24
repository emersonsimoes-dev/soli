<?php

namespace App\Filament\Resources\Churches\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ChurchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Cadastro')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome do templo')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get, mixed $state): void {
                                if (filled($get('slug'))) {
                                    return;
                                }

                                $set('slug', Str::slug((string) $state));
                            }),
                        TextInput::make('short_name')
                            ->label('Sigla')
                            ->required()
                            ->maxLength(32),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Select::make('timezone')
                            ->label('Fuso horário')
                            ->options([
                                'America/Fortaleza' => 'America/Fortaleza',
                                'America/Recife' => 'America/Recife',
                                'America/Bahia' => 'America/Bahia',
                                'America/Sao_Paulo' => 'America/Sao_Paulo',
                                'America/Manaus' => 'America/Manaus',
                                'America/Belem' => 'America/Belem',
                            ])
                            ->default('America/Fortaleza')
                            ->required()
                            ->searchable(),
                        TextInput::make('pix_key')
                            ->label('Chave PIX')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
                Section::make('Identidade visual')
                    ->schema([
                        FileUpload::make('logo_path')
                            ->label('Logo da congregação')
                            ->image()
                            ->disk('public')
                            ->directory('churches')
                            ->visibility('public')
                            ->imageEditor()
                            ->helperText('Sem logo, o site público continua usando a marca Soli como exemplo.'),
                    ]),
                Section::make('Ministérios')
                    ->schema([
                        Repeater::make('settings.ministries')
                            ->label('Ministérios')
                            ->default([])
                            ->collapsible()
                            ->reorderable()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nome')
                                    ->required(),
                                Textarea::make('description')
                                    ->label('Descrição')
                                    ->rows(2),
                            ])
                            ->defaultItems(0),
                    ]),
            ]);
    }
}
