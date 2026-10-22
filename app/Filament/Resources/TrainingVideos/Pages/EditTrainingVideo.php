<?php

namespace App\Filament\Resources\TrainingVideos\Pages;

use App\Filament\Resources\TrainingVideos\TrainingVideoResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTrainingVideo extends EditRecord
{
    protected static string $resource = TrainingVideoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Destek Sayfasında Gör')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn (): string => route('training.show', $this->record))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
