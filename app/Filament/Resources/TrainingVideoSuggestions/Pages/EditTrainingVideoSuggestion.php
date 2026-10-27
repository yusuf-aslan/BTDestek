<?php

namespace App\Filament\Resources\TrainingVideoSuggestions\Pages;

use App\Filament\Resources\TrainingVideoSuggestions\TrainingVideoSuggestionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTrainingVideoSuggestion extends EditRecord
{
    protected static string $resource = TrainingVideoSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
