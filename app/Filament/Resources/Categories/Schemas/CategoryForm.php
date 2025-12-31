<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Kategori Adı')
                    ->required()
                    ->maxLength(255),
                Select::make('department_id')
                    ->label('Bağlı Olduğu Bölüm')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Textarea::make('description')
                    ->label('Açıklama')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }
}