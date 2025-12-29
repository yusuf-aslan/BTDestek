<?php

namespace App\Filament\Resources\AnnouncementTemplates\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AnnouncementTemplateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('type'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
