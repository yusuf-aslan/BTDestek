<?php

namespace App\Filament\Resources\AnnouncementTemplates\Pages;

use App\Filament\Resources\AnnouncementTemplates\AnnouncementTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnnouncementTemplates extends ListRecords
{
    protected static string $resource = AnnouncementTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
