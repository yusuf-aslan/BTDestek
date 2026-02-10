<?php

namespace App\Filament\Resources\DeviceTemplates\Pages;

use App\Filament\Resources\DeviceTemplates\DeviceTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeviceTemplate extends EditRecord
{
    protected static string $resource = DeviceTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
