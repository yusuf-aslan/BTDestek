<?php

namespace App\Filament\Resources\DeviceTemplates\Pages;

use App\Filament\Resources\DeviceTemplates\DeviceTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeviceTemplates extends ListRecords
{
    protected static string $resource = DeviceTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
