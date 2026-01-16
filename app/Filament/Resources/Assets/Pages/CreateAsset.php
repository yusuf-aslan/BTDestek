<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateAsset extends CreateRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Listeye Dön')
                ->url(AssetResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}