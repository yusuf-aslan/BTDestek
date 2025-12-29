<?php

namespace App\Filament\Resources\AnnouncementTemplates\Pages;

use App\Filament\Resources\AnnouncementTemplates\AnnouncementTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAnnouncementTemplate extends EditRecord
{
    protected static string $resource = AnnouncementTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
