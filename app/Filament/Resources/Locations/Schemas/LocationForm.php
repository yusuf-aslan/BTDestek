<?php

namespace App\Filament\Resources\Locations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('anabirim')
                    ->label('Ana Birim')
                    ->options(function () {
                        return \App\Models\Location::distinct()->pluck('anabirim', 'anabirim')->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionUsing(function (array $data) {
                        $newLocation = \App\Models\Location::create([
                            'anabirim' => $data['anabirim'],
                            'altbirim' => null, // Default altbirim to null when creating only anabirim
                        ]);
                        return $newLocation->anabirim; // Return the anabirim value to be selected
                    })
                    ->createOptionForm([
                        TextInput::make('anabirim')
                            ->label('Yeni Ana Birim Adı')
                            ->required()
                            ->unique(\App\Models\Location::class, 'anabirim')
                            ->maxLength(255),
                    ]),
                TextInput::make('altbirim')
                    ->label('Alt Birim')
                    ->maxLength(255),
            ]);
    }
}
