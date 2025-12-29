<?php

namespace App\Filament\Resources\AnnouncementTemplates\Pages;

use App\Filament\Resources\AnnouncementTemplates\AnnouncementTemplateResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAnnouncementTemplate extends ViewRecord
{
    protected static string $resource = AnnouncementTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
