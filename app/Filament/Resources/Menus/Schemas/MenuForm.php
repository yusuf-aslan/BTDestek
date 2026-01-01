<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Menü Detayları')
                    ->schema([
                        TextInput::make('title')
                            ->label('Menü Başlığı')
                            ->required()
                            ->maxLength(255),
                        
                        TextInput::make('url')
                            ->label('Yönlendirilecek URL')
                            ->placeholder('Örn: /duyurular veya https://google.com')
                            ->maxLength(255),

                        Select::make('parent_id')
                            ->label('Üst Menü')
                            ->relationship('parent', 'title')
                            ->placeholder('Ana Menü Öğesi (Üst menü yok)')
                            ->searchable()
                            ->preload(),

                        Select::make('target')
                            ->label('Açılış Hedefi')
                            ->options([
                                '_self' => 'Aynı Sekmede Aç',
                                '_blank' => 'Yeni Sekmede Aç',
                            ])
                            ->default('_self')
                            ->required()
                            ->native(false),

                        TextInput::make('sort_order')
                            ->label('Sıralama')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])->columns(2)
            ]);
    }
}