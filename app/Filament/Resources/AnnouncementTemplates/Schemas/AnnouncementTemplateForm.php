<?php

namespace App\Filament\Resources\AnnouncementTemplates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AnnouncementTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
                Select::make('type')
                    ->options(['info' => 'Info', 'warning' => 'Warning', 'danger' => 'Danger'])
                    ->default('info')
                    ->required(),
            ]);
    }
}
