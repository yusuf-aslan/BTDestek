<?php

namespace App\Filament\Resources\TrainingVideos\Pages;

use App\Filament\Resources\TrainingVideos\TrainingVideoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainingVideo extends CreateRecord
{
    protected static string $resource = TrainingVideoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
