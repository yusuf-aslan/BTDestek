<?php

namespace App\Filament\Resources\TrainingVideoCategories\Pages;

use App\Filament\Resources\TrainingVideoCategories\TrainingVideoCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainingVideoCategory extends CreateRecord
{
    protected static string $resource = TrainingVideoCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
