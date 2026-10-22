<?php

namespace App\Filament\Resources\TrainingVideos\Pages;

use App\Filament\Resources\TrainingVideos\TrainingVideoResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrainingVideos extends ListRecords
{
    protected static string $resource = TrainingVideoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open_portal')
                ->label('Destek Sayfasını Aç')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn (): string => route('training.index'))
                ->openUrlInNewTab(),
            CreateAction::make(),
        ];
    }
}
