<?php

namespace App\Filament\Resources\TrainingVideoSuggestions\Pages;

use App\Filament\Resources\TrainingVideoSuggestions\TrainingVideoSuggestionResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListTrainingVideoSuggestions extends ListRecords
{
    protected static string $resource = TrainingVideoSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_on_portal')
                ->label('Öneri Formunu Gör')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn (): string => route('training.index').'#video-oneri')
                ->openUrlInNewTab(),
        ];
    }
}
