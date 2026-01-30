<?php

namespace App\Filament\Resources\Locations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('anabirim')
                    ->label('Ana Birim')
                    ->required()
                    ->maxLength(255),
                TextInput::make('altbirim')
                    ->label('Alt Birim')
                    ->maxLength(255),
            ]);
    }
}
