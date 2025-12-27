<?php

namespace App\Filament\Resources\CannedResponses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CannedResponseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Başlık')
                    ->required()
                    ->maxLength(255),
                Textarea::make('content')
                    ->label('Hazır Cevap İçeriği')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}