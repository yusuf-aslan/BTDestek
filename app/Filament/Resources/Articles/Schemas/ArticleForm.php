<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Makale Detayları')
                    ->description('Temel bilgiler ve yayın ayarları.')
                    ->schema([
                        TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        
                        DateTimePicker::make('published_at')
                            ->label('Yayınlanma Tarihi')
                            ->default(now()),

                        Toggle::make('is_published')
                            ->label('Yayında')
                            ->default(false)
                            ->inline(false),
                    ])->columns(2),

                Section::make('Makale İçeriği')
                    ->description('Detaylı makale içeriğini buradan düzenleyebilirsiniz.')
                    ->schema([
                        RichEditor::make('content')
                            ->hiddenLabel()
                            ->required()
                            ->columnSpanFull()
                            ->extraInputAttributes(['style' => 'min-height: 400px;']),
                    ])->collapsible(),
            ]);
    }
}
