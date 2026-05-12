<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditAsset extends EditRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('printQr')
                ->label('QR Yazdır')
                ->icon('heroicon-o-qr-code')
                ->color('gray')
                ->url(fn ($record) => route('admin.assets.print-qr', $record))
                ->openUrlInNewTab(),
            Action::make('back')
                ->label('Listeye Dön')
                ->url(AssetResource::getUrl('index'))
                ->color('gray'),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}