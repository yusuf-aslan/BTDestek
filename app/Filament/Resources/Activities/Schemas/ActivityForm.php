<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Teknisyen')
                    ->relationship('user', 'name')
                    ->default(auth()->id())
                    ->required()
                    ->disabled(!auth()->user()->is_admin)
                    ->dehydrated(),
                Select::make('activity_type')
                    ->label('Faaliyet Türü')
                    ->options([
                        'Telefon Desteği' => 'Telefon Desteği',
                        'Saha Çalışması' => 'Saha Çalışması',
                        'Toplantı' => 'Toplantı',
                        'Rutin Bakım/Onarım' => 'Rutin Bakım/Onarım',
                        'Sistem/Altyapı Çalışması' => 'Sistem/Altyapı Çalışması',
                        'Diğer' => 'Diğer',
                    ])
                    ->required(),
                TextInput::make('duration')
                    ->label('Harcanan Süre (Dakika)')
                    ->required()
                    ->numeric()
                    ->default(15),
                TextInput::make('department')
                    ->label('Bölüm / Oda')
                    ->placeholder('Örn: Kardiyoloji Polikliniği 3. Oda')
                    ->required(),
                DatePicker::make('activity_date')
                    ->label('Faaliyet Tarihi')
                    ->default(now())
                    ->required(),
                Textarea::make('description')
                    ->label('Yapılan İşin Açıklaması')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
