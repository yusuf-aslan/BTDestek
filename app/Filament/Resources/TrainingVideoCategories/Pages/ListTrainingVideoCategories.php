<?php

namespace App\Filament\Resources\TrainingVideoCategories\Pages;

use App\Filament\Resources\TrainingVideoCategories\TrainingVideoCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrainingVideoCategories extends ListRecords
{
    protected static string $resource = TrainingVideoCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
