<?php

namespace App\Filament\Resources\TrainingVideoCategories\Pages;

use App\Filament\Resources\TrainingVideoCategories\TrainingVideoCategoryResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTrainingVideoCategory extends EditRecord
{
    protected static string $resource = TrainingVideoCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_on_portal')
                ->label('Destek Sayfasında Gör')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn (): string => route('training.index', ['modul' => $this->record->slug]))
                ->openUrlInNewTab(),
            DeleteAction::make()
                ->modalDescription('Modül silinir; içindeki videolar silinmez, modülsüz kalır.'),
        ];
    }
}
